<?php

/**
 * AHCFREE Geolocation module
 * -------------------------------------------------------------------------
 * Resolves country / city / region for an IP address WITHOUT making a
 * synchronous external API call on every visit. This replaces the old code
 * that called up to 7 external services per new visitor (the cause of the
 * server overload / Cloudflare 522 timeouts).
 *
 * Resolution order (first hit wins):
 *   1. Cloudflare request header (CF-IPCountry) — free, zero latency.
 *   2. Permanent DB cache (table `ahc_ip_geo`) — one row per IP, kept forever.
 *   3. Local MaxMind GeoLite2-City.mmdb database (offline, microseconds).
 *   4. (Optional, last resort) ONE quick external API call — only when the
 *      .mmdb is missing AND the admin left the fallback enabled. The result
 *      is cached so the same IP is never looked up twice.
 *
 * The .mmdb database is downloaded once into wp-content/uploads (it is NOT
 * bundled in the plugin zip) and refreshed monthly via WP-Cron, exactly like
 * major statistics plugins do.
 *
 * Public API:
 *   ahcfree_geo_lookup($ip)            => ['country_code','city','region']
 *   ahcfree_geoip_db_path()            => absolute path to the .mmdb (may not exist yet)
 *   ahcfree_geoip_db_exists()          => bool
 *   ahcfree_geoip_download_database()  => bool (downloads / refreshes the .mmdb)
 *
 * @package visitors-traffic-real-time-statistics
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =========================================================================
 *  Option keys & defaults
 * ========================================================================= */

if (!defined('AHCFREE_GEOIP_OPT_ENABLED')) {
    // Master switch for local-DB geolocation (1 = on).
    define('AHCFREE_GEOIP_OPT_ENABLED', 'ahcfree_geoip_enabled');
}
if (!defined('AHCFREE_GEOIP_OPT_FALLBACK')) {
    // Allow a single external API call when the .mmdb is missing (1 = on).
    define('AHCFREE_GEOIP_OPT_FALLBACK', 'ahcfree_geoip_ext_fallback');
}
if (!defined('AHCFREE_GEOIP_OPT_ACCOUNT')) {
    define('AHCFREE_GEOIP_OPT_ACCOUNT', 'ahcfree_geoip_account_id');
}
if (!defined('AHCFREE_GEOIP_OPT_LICENSE')) {
    define('AHCFREE_GEOIP_OPT_LICENSE', 'ahcfree_geoip_license_key');
}
if (!defined('AHCFREE_GEOIP_OPT_LASTUPDATE')) {
    define('AHCFREE_GEOIP_OPT_LASTUPDATE', 'ahcfree_geoip_db_last_update');
}
if (!defined('AHCFREE_GEOIP_OPT_LASTERROR')) {
    define('AHCFREE_GEOIP_OPT_LASTERROR', 'ahcfree_geoip_db_last_error');
}
if (!defined('AHCFREE_GEOIP_CRON_HOOK')) {
    define('AHCFREE_GEOIP_CRON_HOOK', 'ahcfree_geoip_update_db_cron');
}

/* =========================================================================
 *  Filesystem helpers
 * ========================================================================= */

/**
 * Directory (inside uploads) where the .mmdb is stored.
 */
function ahcfree_geoip_dir()
{
    $uploads = wp_upload_dir();
    $dir = trailingslashit($uploads['basedir']) . 'vtrts-geoip';
    return $dir;
}

/**
 * Absolute path to the GeoLite2-City database (may not exist yet).
 */
function ahcfree_geoip_db_path()
{
    return trailingslashit(ahcfree_geoip_dir()) . 'GeoLite2-City.mmdb';
}

/**
 * Whether the local database file currently exists and is non-trivial.
 */
function ahcfree_geoip_db_exists()
{
    $path = ahcfree_geoip_db_path();
    return (is_file($path) && filesize($path) > 1000);
}

/**
 * Ensure the geoip directory exists and is protected from public listing.
 */
function ahcfree_geoip_prepare_dir()
{
    $dir = ahcfree_geoip_dir();

    if (!is_dir($dir)) {
        wp_mkdir_p($dir);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        return false;
    }

    // Block direct access / directory listing.
    $htaccess = trailingslashit($dir) . '.htaccess';
    if (!is_file($htaccess)) {
        @file_put_contents($htaccess, "Deny from all\n");
    }
    $index = trailingslashit($dir) . 'index.php';
    if (!is_file($index)) {
        @file_put_contents($index, "<?php // Silence is golden.\n");
    }

    return true;
}

/* =========================================================================
 *  Reader (singleton)
 * ========================================================================= */

/**
 * Lazily build and cache a GeoIp2\Database\Reader for the local .mmdb.
 *
 * @return \GeoIp2\Database\Reader|null
 */
function ahcfree_geoip_reader()
{
    static $reader = false; // false = not yet attempted, null = failed

    if ($reader !== false) {
        return $reader;
    }

    $reader = null;

    if (!ahcfree_geoip_db_exists()) {
        return null;
    }

    $autoload = AHCFREE_PLUGIN_ROOT_DIR . '/lib/geoip2/autoload.php';
    if (!is_file($autoload)) {
        return null;
    }
    require_once $autoload;

    if (!class_exists('GeoIp2\\Database\\Reader')) {
        return null;
    }

    try {
        $reader = new \GeoIp2\Database\Reader(ahcfree_geoip_db_path());
    } catch (\Throwable $e) {
        $reader = null;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[VTRTS] GeoIP reader init failed: ' . $e->getMessage());
        }
    }

    return $reader;
}

/* =========================================================================
 *  Permanent IP -> geo cache (DB)
 * ========================================================================= */

/**
 * Create the cache table once. On the hot path this is a no-op after the first
 * successful creation, because a persistent option flag lets us skip the
 * SHOW TABLES query entirely.
 */
function ahcfree_geoip_ensure_cache_table()
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    // Fast path: we already created the table in a previous request.
    if (get_option('ahcfree_geoip_cache_table_ready') === '1') {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'ahc_ip_geo';

    // Existence check (runs at most once per process until the flag is set).
    $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
    if ($exists === $table) {
        update_option('ahcfree_geoip_cache_table_ready', '1');
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$table} (
        ip_address VARCHAR(45) NOT NULL,
        country_code VARCHAR(2) NOT NULL DEFAULT 'XX',
        city VARCHAR(128) NOT NULL DEFAULT '',
        region VARCHAR(128) NOT NULL DEFAULT '',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (ip_address)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    @dbDelta($sql);

    // Confirm and remember, so subsequent requests skip SHOW TABLES.
    $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
    if ($exists === $table) {
        update_option('ahcfree_geoip_cache_table_ready', '1');
    }
}

/**
 * Read one cached row.
 *
 * @return array|null ['country_code','city','region'] or null on miss.
 */
function ahcfree_geoip_cache_get($ip)
{
    global $wpdb;
    $table = $wpdb->prefix . 'ahc_ip_geo';

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT country_code, city, region FROM {$table} WHERE ip_address = %s LIMIT 1",
            $ip
        ),
        ARRAY_A
    );

    if (is_array($row)) {
        return array(
            'country_code' => $row['country_code'] !== '' ? $row['country_code'] : 'XX',
            'city'         => (string) $row['city'],
            'region'       => (string) $row['region'],
        );
    }
    return null;
}

/**
 * Persist one resolved IP. Uses INSERT IGNORE so concurrent hits never error.
 */
function ahcfree_geoip_cache_put($ip, $data)
{
    global $wpdb;
    $table = $wpdb->prefix . 'ahc_ip_geo';

    $wpdb->query(
        $wpdb->prepare(
            "INSERT IGNORE INTO {$table} (ip_address, country_code, city, region) VALUES (%s, %s, %s, %s)",
            $ip,
            isset($data['country_code']) && $data['country_code'] !== '' ? $data['country_code'] : 'XX',
            isset($data['city']) ? $data['city'] : '',
            isset($data['region']) ? $data['region'] : ''
        )
    );
}

/* =========================================================================
 *  Individual resolvers
 * ========================================================================= */

/**
 * Cloudflare country header (only the 2-letter country, no city/region).
 */
function ahcfree_geoip_from_cloudflare()
{
    if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        $cc = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $_SERVER['HTTP_CF_IPCOUNTRY']), 0, 2));
        // CF uses "XX" (unknown) and "T1" (Tor) — treat those as unknown.
        if (strlen($cc) === 2 && $cc !== 'XX' && $cc !== 'T1') {
            return $cc;
        }
    }
    return '';
}

/**
 * Local .mmdb lookup. Returns full country/city/region or null.
 */
function ahcfree_geoip_from_local_db($ip)
{
    $reader = ahcfree_geoip_reader();
    if ($reader === null) {
        return null;
    }

    try {
        $record = $reader->city($ip);

        $country_code = '';
        if (isset($record->country->isoCode) && $record->country->isoCode) {
            $country_code = strtoupper($record->country->isoCode);
        }

        $city = '';
        if (isset($record->city->name) && $record->city->name) {
            $city = $record->city->name;
        }

        $region = '';
        if (!empty($record->subdivisions)) {
            $sub = $record->subdivisions[0];
            if (isset($sub->name) && $sub->name) {
                $region = $sub->name;
            }
        }

        return array(
            'country_code' => $country_code !== '' ? $country_code : 'XX',
            'city'         => $city,
            'region'       => $region,
        );
    } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
        // IP genuinely not in the database (e.g. private / reserved range).
        return array('country_code' => 'XX', 'city' => '', 'region' => '');
    } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[VTRTS] GeoIP local lookup failed: ' . $e->getMessage());
        }
        return null;
    }
}

/**
 * OPTIONAL single external API call — last resort, only if the admin enabled it
 * and the .mmdb is missing. Hard 2s timeout, one provider, no chaining.
 *
 * A short transient cooldown is set on failure so a dead/blocked network never
 * slows the site down on every visit.
 */
function ahcfree_geoip_from_external($ip)
{
    if (get_option(AHCFREE_GEOIP_OPT_FALLBACK, '1') != '1') {
        return null;
    }

    // Respect a cooldown after a previous failure / rate-limit.
    if (get_transient('ahcfree_geoip_ext_cooldown')) {
        return null;
    }

    $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,countryCode,regionName,city';

    $response = wp_remote_get($url, array(
        'timeout'     => 2,
        'redirection' => 1,
        'httpversion' => '1.1',
        'sslverify'   => true,
    ));

    if (is_wp_error($response)) {
        set_transient('ahcfree_geoip_ext_cooldown', 1, 15 * MINUTE_IN_SECONDS);
        return null;
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code === 429 || $code === 403) {
        set_transient('ahcfree_geoip_ext_cooldown', 1, 15 * MINUTE_IN_SECONDS);
        return null;
    }
    if ($code !== 200) {
        return null;
    }

    $body = wp_remote_retrieve_body($response);
    if (empty($body)) {
        return null;
    }

    $data = json_decode($body);
    if (!is_object($data) || !isset($data->status) || $data->status !== 'success') {
        return null;
    }

    $cc = isset($data->countryCode) ? strtoupper(trim($data->countryCode)) : '';

    return array(
        'country_code' => (strlen($cc) === 2) ? $cc : 'XX',
        'city'         => isset($data->city) ? trim($data->city) : '',
        'region'       => isset($data->regionName) ? trim($data->regionName) : '',
    );
}

/* =========================================================================
 *  Public resolver
 * ========================================================================= */

/**
 * Resolve an IP to ['country_code','city','region'].
 *
 * Always returns an array; country_code defaults to 'XX' and city/region to ''.
 * Never makes more than ONE external call, and only when explicitly allowed.
 *
 * @param string $ip
 * @return array
 */
function ahcfree_geo_lookup($ip)
{
    $unknown = array('country_code' => 'XX', 'city' => '', 'region' => '');

    $ip = trim((string) $ip);
    if ($ip === '' || strtoupper($ip) === 'UNKNOWN') {
        return $unknown;
    }

    // Skip private / reserved ranges entirely — no point caching or looking up.
    if (!filter_var(
        $ip,
        FILTER_VALIDATE_IP,
        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
    )) {
        // Still allow a plain valid IP through even if flags reject it as
        // private; we just return unknown for those.
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return $unknown;
        }
        return $unknown;
    }

    if (get_option(AHCFREE_GEOIP_OPT_ENABLED, '1') != '1') {
        // Geolocation disabled: still honour Cloudflare's free header if present.
        $cc = ahcfree_geoip_from_cloudflare();
        return array('country_code' => $cc !== '' ? $cc : 'XX', 'city' => '', 'region' => '');
    }

    ahcfree_geoip_ensure_cache_table();

    // 2) Permanent cache.
    $cached = ahcfree_geoip_cache_get($ip);
    if ($cached !== null) {
        return $cached;
    }

    $result = null;

    // 3) Local .mmdb (preferred — offline, fast).
    $local = ahcfree_geoip_from_local_db($ip);
    if ($local !== null) {
        $result = $local;
    }

    // 1b) Cloudflare can supply the country even if the DB lacks city detail.
    if ($result === null || $result['country_code'] === 'XX') {
        $cf = ahcfree_geoip_from_cloudflare();
        if ($cf !== '') {
            if ($result === null) {
                $result = array('country_code' => $cf, 'city' => '', 'region' => '');
            } else {
                $result['country_code'] = $cf;
            }
        }
    }

    // 4) Optional single external fallback ONLY if DB missing and nothing yet.
    if (($result === null || $result['country_code'] === 'XX') && !ahcfree_geoip_db_exists()) {
        $ext = ahcfree_geoip_from_external($ip);
        if ($ext !== null) {
            $result = $ext;
        }
    }

    if ($result === null) {
        $result = $unknown;
    }

    // Persist whatever we resolved (even 'XX') to avoid repeat lookups.
    ahcfree_geoip_cache_put($ip, $result);

    return $result;
}

/* =========================================================================
 *  Database download / monthly update (cron)
 * ========================================================================= */

/**
 * Download (or refresh) the GeoLite2-City database.
 *
 * Source order:
 *   1. MaxMind official permalink — requires a free Account ID + License Key
 *      saved in settings (recommended, always current).
 *   2. A public GitHub mirror of GeoLite2-City.mmdb — used only if no MaxMind
 *      credentials are configured (best-effort convenience).
 *
 * @return bool true on success.
 */
function ahcfree_geoip_download_database()
{
    if (!ahcfree_geoip_prepare_dir()) {
        update_option(AHCFREE_GEOIP_OPT_LASTERROR, 'GeoIP directory is not writable.');
        return false;
    }

    @set_time_limit(300);

    $account_id  = trim((string) get_option(AHCFREE_GEOIP_OPT_ACCOUNT, ''));
    $license_key = trim((string) get_option(AHCFREE_GEOIP_OPT_LICENSE, ''));

    $tmp = false;
    $args = array('timeout' => 180, 'stream' => true, 'redirection' => 5);

    if ($account_id !== '' && $license_key !== '') {
        // Official MaxMind permalink (tar.gz). Basic-auth with account/license.
        $url = 'https://download.maxmind.com/geoip/databases/GeoLite2-City/download?suffix=tar.gz';
        $args['headers'] = array(
            'Authorization' => 'Basic ' . base64_encode($account_id . ':' . $license_key),
        );
    } else {
        // Public mirror fallback (no credentials needed).
        $url = 'https://raw.githubusercontent.com/P3TERX/GeoLite.mmdb/download/GeoLite2-City.mmdb';
    }

    $dest_tmp = trailingslashit(ahcfree_geoip_dir()) . 'download.tmp';
    $args['filename'] = $dest_tmp;

    $response = wp_remote_get($url, $args);

    if (is_wp_error($response)) {
        @unlink($dest_tmp);
        update_option(AHCFREE_GEOIP_OPT_LASTERROR, 'Download failed: ' . $response->get_error_message());
        return false;
    }
    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200 || !is_file($dest_tmp)) {
        @unlink($dest_tmp);
        update_option(AHCFREE_GEOIP_OPT_LASTERROR, 'Download failed: HTTP ' . $code);
        return false;
    }

    $final = ahcfree_geoip_db_path();
    $ok = false;

    // Detect gzip/tar (MaxMind) vs raw mmdb (mirror) by magic bytes.
    $fh = @fopen($dest_tmp, 'rb');
    $magic = $fh ? fread($fh, 2) : '';
    if ($fh) {
        fclose($fh);
    }

    if ($magic === "\x1f\x8b") {
        // gzip tarball from MaxMind — extract the .mmdb inside.
        $ok = ahcfree_geoip_extract_mmdb_from_targz($dest_tmp, $final);
    } else {
        // Assume raw .mmdb; sanity-check minimum size, then move into place.
        if (filesize($dest_tmp) > 1000) {
            $ok = @rename($dest_tmp, $final);
            if (!$ok) {
                $ok = @copy($dest_tmp, $final);
            }
        }
    }

    @unlink($dest_tmp);

    if ($ok && ahcfree_geoip_db_exists()) {
        update_option(AHCFREE_GEOIP_OPT_LASTUPDATE, current_time('mysql'));
        update_option(AHCFREE_GEOIP_OPT_LASTERROR, '');
        return true;
    }

    update_option(AHCFREE_GEOIP_OPT_LASTERROR, 'Downloaded file was not a valid GeoIP database.');
    return false;
}

/**
 * Extract GeoLite2-City.mmdb from a MaxMind .tar.gz into $final.
 * The .mmdb lives inside a dated folder whose name is not known in advance.
 */
function ahcfree_geoip_extract_mmdb_from_targz($targz, $final)
{
    if (!class_exists('PharData')) {
        return false;
    }

    $work = trailingslashit(ahcfree_geoip_dir()) . 'extract_' . wp_generate_password(8, false);
    if (!wp_mkdir_p($work)) {
        return false;
    }

    $ok = false;
    try {
        $phar = new PharData($targz);
        // Decompress .tar.gz -> .tar, then extract.
        $tar = $phar->decompress(); // creates a .tar alongside
        $tarPath = $tar->getPath();

        $pharTar = new PharData($tarPath);
        $pharTar->extractTo($work, null, true);

        // Find the .mmdb anywhere under $work.
        $found = '';
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($work, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if (substr($file->getFilename(), -5) === '.mmdb') {
                $found = $file->getPathname();
                break;
            }
        }

        if ($found !== '') {
            $ok = @copy($found, $final);
        }

        @unlink($tarPath);
    } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[VTRTS] GeoIP extract failed: ' . $e->getMessage());
        }
        $ok = false;
    }

    // Clean the temporary extraction folder.
    ahcfree_geoip_rrmdir($work);

    return $ok;
}

/**
 * Recursively remove a directory (used for temp extraction cleanup).
 */
function ahcfree_geoip_rrmdir($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $items = @scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            ahcfree_geoip_rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

/* =========================================================================
 *  Cron scheduling
 * ========================================================================= */

add_action(AHCFREE_GEOIP_CRON_HOOK, 'ahcfree_geoip_cron_run');

/**
 * Dedicated one-off hook used right after activation to fetch the database
 * without blocking the activation request itself.
 */
add_action('ahcfree_geoip_first_download', 'ahcfree_geoip_cron_run');

/**
 * Cron callback: refresh the DB monthly, or download it the first time.
 */
function ahcfree_geoip_cron_run()
{
    if (get_option(AHCFREE_GEOIP_OPT_ENABLED, '1') != '1') {
        return;
    }
    ahcfree_geoip_download_database();
}

/**
 * Ensure the monthly cron event is scheduled (idempotent).
 */
function ahcfree_geoip_maybe_schedule_cron()
{
    if (!wp_next_scheduled(AHCFREE_GEOIP_CRON_HOOK)) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'ahcfree_monthly', AHCFREE_GEOIP_CRON_HOOK);
    }
}

/**
 * Register a "monthly" cron schedule (WordPress has no built-in monthly).
 */
add_filter('cron_schedules', 'ahcfree_geoip_cron_schedules');
function ahcfree_geoip_cron_schedules($schedules)
{
    if (!isset($schedules['ahcfree_monthly'])) {
        $schedules['ahcfree_monthly'] = array(
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => __('Once Monthly', 'visitors-traffic-real-time-statistics'),
        );
    }
    return $schedules;
}

/**
 * Schedule the cron on init (cheap; wp_next_scheduled is cached).
 */
add_action('init', 'ahcfree_geoip_maybe_schedule_cron');

/**
 * Plugin-activation routine for geolocation:
 *   - create the uploads/vtrts-geoip directory immediately,
 *   - schedule the monthly refresh,
 *   - schedule a one-off download in ~1 minute so the heavy ~60MB fetch never
 *     blocks (or fails) the activation request itself.
 *
 * Hooked from the main plugin file via register_activation_hook().
 */
function ahcfree_geoip_on_activation()
{
    // Make sure the folder exists right away (even before the DB arrives).
    ahcfree_geoip_prepare_dir();

    // Monthly refresh.
    ahcfree_geoip_maybe_schedule_cron();

    // First download shortly after activation, non-blocking.
    if (get_option(AHCFREE_GEOIP_OPT_ENABLED, '1') == '1' && !ahcfree_geoip_db_exists()) {
        if (!wp_next_scheduled('ahcfree_geoip_first_download')) {
            wp_schedule_single_event(time() + MINUTE_IN_SECONDS, 'ahcfree_geoip_first_download');
        }
    }
}

/**
 * Safety net: if the directory or database is missing during normal operation
 * (e.g. uploads was cleared, or the first-download cron never ran on a host
 * with WP-Cron disabled), make sure the folder exists and queue one download
 * attempt. Runs at most once per day to avoid any repeated work.
 */
add_action('init', 'ahcfree_geoip_self_heal', 20);
function ahcfree_geoip_self_heal()
{
    if (get_option(AHCFREE_GEOIP_OPT_ENABLED, '1') != '1') {
        return;
    }

    // Always make sure the folder exists.
    if (!is_dir(ahcfree_geoip_dir())) {
        ahcfree_geoip_prepare_dir();
    }

    if (ahcfree_geoip_db_exists()) {
        return;
    }

    // Throttle: only attempt to (re)queue a download once per day.
    if (get_transient('ahcfree_geoip_heal_lock')) {
        return;
    }
    set_transient('ahcfree_geoip_heal_lock', 1, DAY_IN_SECONDS);

    if (!wp_next_scheduled('ahcfree_geoip_first_download')) {
        wp_schedule_single_event(time() + MINUTE_IN_SECONDS, 'ahcfree_geoip_first_download');
    }
}

/**
 * Clear the cron when the plugin is deactivated.
 */
function ahcfree_geoip_unschedule_cron()
{
    $timestamp = wp_next_scheduled(AHCFREE_GEOIP_CRON_HOOK);
    if ($timestamp) {
        wp_unschedule_event($timestamp, AHCFREE_GEOIP_CRON_HOOK);
    }

    // Clear any pending one-off first download.
    $first = wp_next_scheduled('ahcfree_geoip_first_download');
    if ($first) {
        wp_unschedule_event($first, 'ahcfree_geoip_first_download');
    }
    wp_clear_scheduled_hook('ahcfree_geoip_first_download');
}

/* =========================================================================
 *  Manual "Update database now" button handler
 * ========================================================================= */

add_action('admin_post_ahcfree_geoip_download', 'ahcfree_geoip_handle_manual_download');

function ahcfree_geoip_handle_manual_download()
{
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions.');
    }
    check_admin_referer('ahcfree_geoip_download_action', 'ahcfree_geoip_download_nonce');

    $ok = ahcfree_geoip_download_database();

    $redirect = add_query_arg(
        array(
            'page'            => 'ahc_hits_counter_settings',
            'ahcfree_geoip_dl' => $ok ? 'ok' : 'fail',
        ),
        admin_url('admin.php')
    );
    wp_safe_redirect($redirect . '#geolocation-settings');
    exit;
}
