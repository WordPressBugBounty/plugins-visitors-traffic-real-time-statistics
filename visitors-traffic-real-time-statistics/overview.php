<?php


$custom_timezone_offset = ahcfree_get_current_timezone_offset();
$custom_timezone_string = ahcfree_get_timezone_string();

if (!function_exists('ahcfree_render_locked_country_panel')) {
    /**
     * Render a "Pro" panel that shows a representative SAMPLE country table
     * behind a soft blur, with an upgrade overlay on top.
     *
     * Note: this intentionally uses sample (not real) data. The blur is purely
     * cosmetic and can be removed client-side, so real visitor data must never
     * be placed behind it. The sample conveys what the Pro view looks like
     * without exposing the site's actual figures.
     *
     * @param string $variant 'today' | 'all' | 'referring'
     */
    function ahcfree_render_locked_country_panel($variant = 'all')
    {
        // Representative SAMPLE data only — never the site's real figures,
        // because the blur can be removed in the browser.
        $rows = array();
        $sample = array(
            array('us', 'United States', 1240),
            array('gb', 'United Kingdom', 880),
            array('de', 'Germany', 645),
            array('in', 'India', 590),
            array('fr', 'France', 410),
            array('ca', 'Canada', 305),
            array('au', 'Australia', 240),
            array('br', 'Brazil', 180),
        );
        foreach ($sample as $s) {
            $furl = plugins_url('images/flags/' . $s[0] . '.png', AHCFREE_PLUGIN_MAIN_FILE);
            $rows[] = array(
                'flag'     => '<img src="' . esc_url($furl) . '" width="26" height="17" alt="" onerror="imgFlagError(this)" />',
                'name'     => $s[1],
                'visitors' => $s[2],
            );
        }

        $headline = ($variant === 'referring')
            ? 'See which countries refer you the most'
            : 'Unlock full country-level insights';
        $subline = ($variant === 'referring')
            ? 'Pro reveals referring countries, ranked by traffic, with full history.'
            : 'Pro adds visits, visitors and trends per country — with unlimited history.';

        $upgrade_url = 'https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true';
        ?>
        <div class="vtrts-locked">
            <div class="vtrts-locked__data" aria-hidden="true">
                <table class="vtrts-locked__table">
                    <thead>
                        <tr><th>#</th><th>Country</th><th>Visitors</th></tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($rows as $r) : ?>
                        <tr>
                            <td><?php echo intval($i); ?></td>
                            <td><span class="vtrts-locked__flag"><?php echo $r['flag']; ?></span><?php echo esc_html($r['name']); ?></td>
                            <td><?php echo number_format_i18n(intval($r['visitors'])); ?></td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="vtrts-locked__overlay">
                <div class="vtrts-locked__lock">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="11" width="14" height="9" rx="2" stroke="#fff" stroke-width="2"/>
                        <path d="M8 11V8a4 4 0 018 0v3" stroke="#fff" stroke-width="2"/>
                    </svg>
                </div>
                <div class="vtrts-locked__headline"><?php echo esc_html($headline); ?></div>
                <div class="vtrts-locked__subline"><?php echo esc_html($subline); ?></div>
                <a class="vtrts-locked__btn" target="_blank" rel="noopener" href="<?php echo esc_url($upgrade_url); ?>">
                    Unlock with Pro
                </a>
            </div>
        </div>
        <?php
    }
}



$ahcfree_save_ips = get_option('ahcfree_save_ips_opn');
if ($custom_timezone_string) {
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
}

$myend_date = new DateTime();
$myend_date->setTimezone(new DateTimeZone('UTC'));
//$myend_date->setTimezone($custom_timezone);
$myend_date_full = ahcfree_localtime('Y-m-d H:i:s');
$myend_date = ahcfree_localtime('Y-m-d');

$mystart_date = new DateTime($myend_date);
$mystart_date->modify(' - ' . (AHCFREE_VISITORS_VISITS_LIMIT - 1) . ' days');
$mystart_date->setTimezone(new DateTimeZone('UTC'));
//$mystart_date->setTimezone($custom_timezone);
$mystart_date_full = $mystart_date->format('Y-m-d H:i:s');
$mystart_date = $mystart_date->format('Y-m-d');

//echo date('Y-m-d H:i:s',time());
?>
<style>
    body {
        background: #F1F1F1 !important
    }

    /* Friendly empty-state used across reports that have no data yet */
    .vtrts-empty {
        text-align: center;
        padding: 34px 20px;
        color: #50575e;
        min-height: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .vtrts-empty__icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: rgba(34, 113, 177, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .vtrts-empty__title {
        font-size: 15px;
        font-weight: 600;
        color: #1d2327;
        margin-bottom: 5px;
    }
    .vtrts-empty__text {
        font-size: 13px;
        line-height: 1.55;
        max-width: 360px;
        margin: 0 auto;
        color: #646970;
    }

    /* Locked Pro panel: real/sample data blurred with an upgrade overlay */
    .vtrts-locked {
        position: relative;
        overflow: hidden;
        border-radius: 0 0 7px 7px;
        min-height: 280px;
        flex: 1 1 auto;
    }
    .vtrts-locked__data {
        filter: blur(2px);
        opacity: 0.9;
        pointer-events: none;
        user-select: none;
        padding: 6px 12px 12px;
    }
    .vtrts-locked__table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .vtrts-locked__table th {
        text-align: left;
        font-weight: 600;
        color: #50575e;
        padding: 8px 10px;
        border-bottom: 1px solid #e2e4e7;
    }
    .vtrts-locked__table td {
        padding: 9px 10px;
        border-bottom: 1px solid #f0f0f1;
        color: #1d2327;
    }
    .vtrts-locked__flag {
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }
    .vtrts-locked__flag img { vertical-align: middle; border-radius: 2px; }
    .vtrts-locked__overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
        background: linear-gradient(180deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.55) 45%, rgba(255,255,255,0.75) 100%);
    }
    .vtrts-locked__lock {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #2271b1;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        box-shadow: 0 6px 16px rgba(34,113,177,0.32), 0 0 0 6px rgba(255,255,255,0.7);
    }
    .vtrts-locked__headline {
        font-size: 16px;
        font-weight: 700;
        color: #1d2327;
        margin-bottom: 6px;
        max-width: 340px;
        background: rgba(255,255,255,0.85);
        padding: 4px 12px;
        border-radius: 6px;
    }
    .vtrts-locked__subline {
        font-size: 13px;
        line-height: 1.55;
        color: #3c434a;
        max-width: 340px;
        margin-bottom: 16px;
        background: rgba(255,255,255,0.85);
        padding: 4px 12px;
        border-radius: 6px;
    }
    .vtrts-locked__btn {
        display: inline-block;
        background: #2271b1;
        color: #fff !important;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        padding: 10px 22px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(34,113,177,0.3);
        transition: transform .15s ease, background .15s ease, box-shadow .15s ease;
    }
    .vtrts-locked__btn:hover {
        background: #135e96;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(19,94,150,0.4);
    }

    .swal2-content {
        font-size: 18px;
        text-aight: center !important;
    }

    .swal-noscroll h2 {
        display: none
    }

    .swal2-modal {
        margin: auto !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        position: fixed !important;
        text-aight: center !important;

    }

    .swal2-modal {
        width: 800px !important;
        max-width: 95%;
        padding: 10px;
        margin: 0 auto;
        /* يحاول يوسّط بالعرض */

    }

    .swal2-container {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
</style>


<script language="javascript" type="text/javascript">
    function imgFlagError(image) {
        image.onerror = "";
        image.src = "<?php echo plugins_url('images/flags/noFlag.png', AHCFREE_PLUGIN_MAIN_FILE) ?>";
        return true;
    }

    setInterval(function() {

        var now = new Date();
        var year = now.getFullYear();
        var month = now.getMonth() + 1;
        var day = now.getDate();
        var hour = now.getHours();
        var minute = now.getMinutes();
        var second = now.getSeconds();
        if (month.toString().length == 1) {
            month = '0' + month;
        }
        if (day.toString().length == 1) {
            day = '0' + day;
        }
        if (hour.toString().length == 1) {
            hour = '0' + hour;
        }
        if (minute.toString().length == 1) {
            minute = '0' + minute;
        }
        if (second.toString().length == 1) {
            second = '0' + second;
        }



        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        const d = new Date();


        var dateTime = day + ' ' + monthNames[d.getMonth()] + ' ' + year + ', ' + hour + ':' + minute + ':' + second;
        document.getElementById('ahcfree_currenttime').innerHTML = dateTime;
    }, 500);
</script>



<div class="ahc_main_container">
    <!-- Top note in admin page -->

    <?php
    // Show the newsletter banner only if this user hasn't dismissed it.
    $ahcfree_sub_dismissed = get_user_meta(get_current_user_id(), 'ahcfree_subscribe_dismissed', true);

    // Pre-fill with the logged-in admin's email; fall back to the site admin email.
    $ahcfree_admin_email = '';
    if (function_exists('wp_get_current_user')) {
        $ahcfree_current_user = wp_get_current_user();
        if ($ahcfree_current_user && !empty($ahcfree_current_user->user_email)) {
            $ahcfree_admin_email = $ahcfree_current_user->user_email;
        }
    }
    if ($ahcfree_admin_email === '') {
        $ahcfree_admin_email = get_option('admin_email', '');
    }

    if ($ahcfree_sub_dismissed !== '1') :
    ?>
    <div class="row">
        <div class="col-lg-12">
            <br />
            <div id="vtrts_subscribe" role="region" aria-label="Newsletter">
                <div class="vtrts-sub__inner">
                    <div class="vtrts-sub__left">
                        <div class="vtrts-sub__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 7l9 6 9-6" stroke="#2271b1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <rect x="3" y="5" width="18" height="14" rx="2.5" stroke="#2271b1" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="vtrts-sub__copy">
                            <div class="vtrts-sub__title">Stay in the loop</div>
                            <div class="vtrts-sub__text">
                                Get new releases, helpful tips, and the occasional
                                <strong>discount coupon</strong> by email. That&rsquo;s it &mdash; no spam.
                            </div>
                        </div>
                    </div>

                    <div class="vtrts-sub__actions">
                        <input type="email" id="ahc_admin_email" placeholder="you@example.com"
                            value="<?php echo esc_attr($ahcfree_admin_email); ?>">
                        <button type="button" class="vtrts-sub__btn"
                            onclick="vtrts_open_subscribe_link('<?php echo esc_js($ahcfree_admin_email); ?>')">
                            Subscribe
                        </button>
                        <button type="button" class="vtrts-sub__dismiss" onclick="vtrts_dismiss_notice()">
                            No thanks
                        </button>
                    </div>
                </div>
            </div>

            <style>
                #vtrts_subscribe {
                    position: relative;
                    margin: 5px 0 15px;
                    padding: 16px 20px;
                    border: 1px solid #dcdcde;
                    border-left: 4px solid #2271b1;
                    border-radius: 8px;
                    background: #fff;
                    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                }
                .vtrts-sub__inner {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 20px;
                    flex-wrap: wrap;
                }
                .vtrts-sub__left {
                    display: flex;
                    align-items: flex-start;
                    gap: 13px;
                    flex: 1 1 360px;
                    min-width: 260px;
                }
                .vtrts-sub__icon {
                    flex: 0 0 auto;
                    width: 38px;
                    height: 38px;
                    border-radius: 8px;
                    background: rgba(34,113,177,0.08);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-top: 1px;
                }
                .vtrts-sub__title {
                    font-size: 15px;
                    font-weight: 600;
                    color: #1d2327;
                    line-height: 1.3;
                    margin-bottom: 3px;
                }
                .vtrts-sub__text {
                    font-size: 13px;
                    line-height: 1.5;
                    color: #50575e;
                }
                .vtrts-sub__text strong { color: #1d2327; }
                .vtrts-sub__actions {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    flex: 0 0 auto;
                    flex-wrap: wrap;
                }
                .vtrts-sub__actions input {
                    padding: 7px 11px;
                    border: 1px solid #8c8f94;
                    border-radius: 5px;
                    font-size: 13px;
                    width: 190px;
                    max-width: 100%;
                    background: #fff;
                    color: #2c3338;
                    box-sizing: border-box;
                    transition: border-color .15s ease, box-shadow .15s ease;
                }
                .vtrts-sub__actions input:focus {
                    outline: none;
                    border-color: #2271b1;
                    box-shadow: 0 0 0 1px #2271b1;
                }
                .vtrts-sub__btn {
                    padding: 7px 16px;
                    border: 1px solid #2271b1;
                    border-radius: 5px;
                    background: #2271b1;
                    color: #fff;
                    font-size: 13px;
                    font-weight: 600;
                    cursor: pointer;
                    white-space: nowrap;
                    transition: background .15s ease, border-color .15s ease;
                }
                .vtrts-sub__btn:hover { background: #135e96; border-color: #135e96; }
                .vtrts-sub__dismiss {
                    padding: 7px 10px;
                    border: none;
                    background: none;
                    color: #787c82;
                    font-size: 13px;
                    cursor: pointer;
                    text-decoration: underline;
                    transition: color .15s ease;
                }
                .vtrts-sub__dismiss:hover { color: #50575e; }

                @media (max-width: 600px) {
                    .vtrts-sub__actions { flex: 1 1 100%; }
                    .vtrts-sub__actions input { flex: 1 1 auto; width: auto; }
                }
            </style>

            <script>
                function vtrts_dismiss_notice() {
                    var notice = document.getElementById("vtrts_subscribe");
                    if (notice) {
                        notice.style.transition = "opacity .2s ease";
                        notice.style.opacity = "0";
                        setTimeout(function () { notice.style.display = "none"; }, 200);
                    }
                    try {
                        jQuery.post(ajaxurl, {
                            action: "ahcfree_dismiss_subscribe_notice",
                            nonce: "<?php echo esc_js(wp_create_nonce('ahcfree_subscribe_nonce')); ?>"
                        });
                    } catch (e) {}
                }

                function vtrts_open_subscribe_link(defaultEmail) {
                    var emailInput = document.getElementById("ahc_admin_email");
                    var adminEmail = emailInput ? emailInput.value : defaultEmail;

                    if (!adminEmail || !vtrts_isValidEmail(adminEmail)) {
                        alert('Please enter a valid email address.');
                        if (emailInput) { emailInput.focus(); }
                        return;
                    }

                    window.open('https://www.wp-buy.com/vtrts-subscribe/?email=' + encodeURIComponent(adminEmail), '_blank');
                }

                function vtrts_isValidEmail(email) {
                    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(email);
                }
            </script>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header Name and Timezone -->

    <div class="row">
        <div class="col-lg-8">
            <h1><img height="55px" src="<?php echo esc_url(plugins_url('images/logo.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>">&nbsp;Visitor Traffic Real Time Statistics free &nbsp;
                <?php if (current_user_can('manage_options')) { ?><a title="change settings" href="admin.php?page=ahc_hits_counter_settings"><img src="<?php echo esc_url(plugins_url('images/settings.jpg', AHCFREE_PLUGIN_MAIN_FILE)) ?>" /></a><?php } ?></h1>

        </div>
        <div class="col-lg-4">
            <h2 id="ahcfree_currenttime"></h2>
        </div>
    </div>

    <?php
    // ===== Geolocation setup notice =====
    // If the local GeoIP database has not been downloaded yet, invite the admin
    // to finish the one-click setup instead of silently relying on WP-Cron
    // (which may be disabled on some hosts).
    if (
        current_user_can('manage_options')
        && function_exists('ahcfree_geoip_db_exists')
        && get_option('ahcfree_geoip_enabled', '1') == '1'
        && !ahcfree_geoip_db_exists()
    ) {
        $ahcfree_setup_url = wp_nonce_url(
            admin_url('admin-post.php?action=ahcfree_geoip_download'),
            'ahcfree_geoip_download_action',
            'ahcfree_geoip_download_nonce'
        );
    ?>
    <div class="ahcfree-setup-notice">
        <div class="ahcfree-setup-notice__icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z" fill="#ffffff"/>
            </svg>
        </div>
        <div class="ahcfree-setup-notice__body">
            <div class="ahcfree-setup-notice__title">One quick step to finish setup</div>
            <div class="ahcfree-setup-notice__text">
                To show your visitors&rsquo; country, region and city, the local geolocation
                database needs to be downloaded once. It updates automatically every month afterwards.
            </div>
        </div>
        <a class="ahcfree-setup-notice__btn" href="<?php echo esc_url($ahcfree_setup_url); ?>">
            Click here to complete setup
        </a>
    </div>
    <style>
        .ahcfree-setup-notice {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 0 0 22px;
            padding: 18px 22px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2271b1 0%, #135e96 100%);
            box-shadow: 0 6px 18px rgba(19, 94, 150, 0.25);
            color: #fff;
            flex-wrap: wrap;
        }
        .ahcfree-setup-notice__icon {
            flex: 0 0 auto;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ahcfree-setup-notice__body {
            flex: 1 1 320px;
            min-width: 240px;
        }
        .ahcfree-setup-notice__title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .ahcfree-setup-notice__text {
            font-size: 13.5px;
            line-height: 1.6;
            opacity: 0.95;
        }
        .ahcfree-setup-notice__btn {
            flex: 0 0 auto;
            display: inline-block;
            background: #fff;
            color: #135e96 !important;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 11px 22px;
            border-radius: 8px;
            transition: transform .15s ease, box-shadow .15s ease;
            white-space: nowrap;
        }
        .ahcfree-setup-notice__btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }
    </style>
    <?php
    }
    ?>

    <!-- Top 4 boxes in admin page -->

    <?php
    // ===== Modern Dashboard Boxes Data =====
    // Independently fetch data directly (the $ahc_sum_stat var is defined later in this file)
    $ahcfree_box_today     = ahcfree_get_visitors_visits_in_period('today');
    $ahcfree_box_yesterday = ahcfree_get_visitors_visits_in_period('yesterday');

    $ahcfree_today_visitors     = isset($ahcfree_box_today['visitors'])     ? (int) $ahcfree_box_today['visitors']     : 0;
    $ahcfree_today_visits       = isset($ahcfree_box_today['visits'])       ? (int) $ahcfree_box_today['visits']       : 0;
    $ahcfree_yesterday_visitors = isset($ahcfree_box_yesterday['visitors']) ? (int) $ahcfree_box_yesterday['visitors'] : 0;
    $ahcfree_yesterday_visits   = isset($ahcfree_box_yesterday['visits'])   ? (int) $ahcfree_box_yesterday['visits']   : 0;

    // All-time search engine totals
    $ahcfree_alltime_se = ahcfree_get_hits_search_engines_referers('alltime');
    $ahcfree_total_search = 0;
    if (is_array($ahcfree_alltime_se)) {
        foreach ($ahcfree_alltime_se as $v) { $ahcfree_total_search += (int) $v; }
    }
    // Today's search engine count (for the "+N today" badge)
    $ahcfree_today_se = ahcfree_get_hits_search_engines_referers('today');
    $ahcfree_today_search = 0;
    if (is_array($ahcfree_today_se)) {
        foreach ($ahcfree_today_se as $v) { $ahcfree_today_search += (int) $v; }
    }

    // 7 days breakdown for sparklines
    $ahcfree_last_7_days = ahcfree_get_last_7_days_data();
    $ahcfree_visitors_sparkline = array_map(function ($d) { return $d['visitors']; }, $ahcfree_last_7_days);
    $ahcfree_visits_sparkline   = array_map(function ($d) { return $d['visits']; },   $ahcfree_last_7_days);

    // Real online users count for blurred display in upgrade box
    // (data exists in the table; the free version simply does not surface it)
    global $wpdb;
    $ahcfree_online_count = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(DISTINCT hit_ip_address)
         FROM ahc_online_users
         WHERE site_id = %d
           AND `date` >= DATE_SUB(%s, INTERVAL 5 MINUTE)",
        get_current_blog_id(),
        ahcfree_localtime('Y-m-d H:i:s')
    ));
    if ($ahcfree_online_count < 1) { $ahcfree_online_count = 1; } // never display zero behind blur
    ?>

    <div class="row ahcfree-modern-boxes">
        <!-- Box 1: Upgrade to Pro (Online Users is a Pro feature) -->
        <div class="col-lg-3 col-md-6">
            <a href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true" target="_blank" class="ahcfree-mbox-link">
                <div class="ahcfree-mbox" data-color="turquoise">
                    <div class="ahcfree-mbox-head">
                        <span class="ahcfree-mbox-title">Online Users</span>
                        <span class="ahcfree-mbox-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21v-1a8 8 0 0 1 16 0v1"/></svg>
                        </span>
                    </div>
                    <div class="ahcfree-mbox-value ahcfree-mbox-value-blur"><?php echo (int) $ahcfree_online_count; ?></div>
                    <div class="ahcfree-mbox-meta">
                        <span class="ahcfree-mbox-upgrade-link">&#9733; Upgrade to unlock</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Box 2: Today's Visitors -->
        <div class="col-lg-3 col-md-6">
            <div class="ahcfree-mbox" data-color="coral">
                <div class="ahcfree-mbox-head">
                    <span class="ahcfree-mbox-title">Today's Visitors</span>
                    <span class="ahcfree-mbox-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 17 9 11 13 15 21 7"/><polyline points="14 7 21 7 21 14"/></svg>
                    </span>
                </div>
                <div class="ahcfree-mbox-value" id="today_visitors_box"><?php echo ahcfree_NumFormat($ahcfree_today_visitors); ?></div>
                <div class="ahcfree-mbox-meta">
                    <?php
                    $vd = $ahcfree_yesterday_visitors > 0 ? round((($ahcfree_today_visitors - $ahcfree_yesterday_visitors) / $ahcfree_yesterday_visitors) * 100, 1) : 0;
                    $vd_class = $vd >= 0 ? 'ahcfree-mbox-badge-up' : 'ahcfree-mbox-badge-down';
                    $vd_arrow = $vd >= 0 ? '&#8593;' : '&#8595;';
                    ?>
                    <span class="ahcfree-mbox-badge <?php echo $vd_class; ?>"><?php echo $vd_arrow . ' ' . abs($vd) . '%'; ?></span>
                    <span class="ahcfree-mbox-sub">vs. yesterday (<?php echo ahcfree_NumFormat($ahcfree_yesterday_visitors); ?>)</span>
                </div>
            </div>
        </div>

        <!-- Box 3: Today's Page Views -->
        <div class="col-lg-3 col-md-6">
            <div class="ahcfree-mbox" data-color="blue">
                <div class="ahcfree-mbox-head">
                    <span class="ahcfree-mbox-title">Today's Page Views</span>
                    <span class="ahcfree-mbox-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                </div>
                <div class="ahcfree-mbox-value" id="today_visits_box"><?php echo ahcfree_NumFormat($ahcfree_today_visits); ?></div>
                <div class="ahcfree-mbox-meta">
                    <?php
                    $pd = $ahcfree_yesterday_visits > 0 ? round((($ahcfree_today_visits - $ahcfree_yesterday_visits) / $ahcfree_yesterday_visits) * 100, 1) : 0;
                    $pd_class = $pd >= 0 ? 'ahcfree-mbox-badge-up' : 'ahcfree-mbox-badge-down';
                    $pd_arrow = $pd >= 0 ? '&#8593;' : '&#8595;';
                    ?>
                    <span class="ahcfree-mbox-badge <?php echo $pd_class; ?>"><?php echo $pd_arrow . ' ' . abs($pd) . '%'; ?></span>
                    <span class="ahcfree-mbox-sub">vs. yesterday (<?php echo ahcfree_NumFormat($ahcfree_yesterday_visits); ?>)</span>
                </div>
            </div>
        </div>

        <!-- Box 4: All-Time Search Engines -->
        <div class="col-lg-3 col-md-6">
            <div class="ahcfree-mbox" data-color="purple">
                <div class="ahcfree-mbox-head">
                    <span class="ahcfree-mbox-title">All-Time Search Engines</span>
                    <span class="ahcfree-mbox-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                </div>
                <div class="ahcfree-mbox-value" id="today_search_box"><?php echo ahcfree_NumFormat($ahcfree_total_search); ?></div>
                <div class="ahcfree-mbox-meta">
                    <span class="ahcfree-mbox-badge ahcfree-mbox-badge-up">&#8593; +<?php echo ahcfree_NumFormat($ahcfree_today_search); ?> today</span>
                    <span class="ahcfree-mbox-sub">All time</span>
                </div>
            </div>
        </div>
    </div>

    <style>
    .ahcfree-modern-boxes { margin-bottom: 16px; }
    .ahcfree-modern-boxes [class*="col-"] { padding: 8px; }
    .ahcfree-mbox-link { text-decoration: none !important; color: inherit !important; display: block; height: 100%; }
    .ahcfree-mbox-link:hover { text-decoration: none !important; color: inherit !important; }
    .ahcfree-mbox {
        position: relative;
        border-radius: 12px;
        padding: 22px;
        min-height: 124px;
        height: 100%;
        background: #fff;
        border: 1px solid #e9ecef;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: box-shadow .15s ease;
    }
    .ahcfree-mbox:hover {
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }
    /* Per-card accent color (drives icon color via currentColor) */
    .ahcfree-mbox[data-color="turquoise"] { color: #3aafa9; }
    .ahcfree-mbox[data-color="coral"]     { color: #e57373; }
    .ahcfree-mbox[data-color="blue"]      { color: #5b8fbd; }
    .ahcfree-mbox[data-color="purple"]    { color: #8c7baf; }

    /* Header row: title left, icon right */
    .ahcfree-mbox-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .ahcfree-mbox-title {
        font-size: 12px;
        font-weight: 600;
        color: #8a929c;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .ahcfree-mbox-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        color: currentColor;
        flex-shrink: 0;
    }
    /* Soft tint backgrounds for the icon, per color */
    .ahcfree-mbox[data-color="turquoise"] .ahcfree-mbox-icon { background: #e8f5f4; }
    .ahcfree-mbox[data-color="coral"]     .ahcfree-mbox-icon { background: #fdeeee; }
    .ahcfree-mbox[data-color="blue"]      .ahcfree-mbox-icon { background: #eaf1f8; }
    .ahcfree-mbox[data-color="purple"]    .ahcfree-mbox-icon { background: #f1eef8; }

    /* Big value number */
    .ahcfree-mbox-value {
        font-size: 40px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 10px;
        color: #1f2937;
        letter-spacing: -.01em;
    }
    /* Meta row: badge + sub-text */
    .ahcfree-mbox-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
    }
    .ahcfree-mbox-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        white-space: nowrap;
    }
    /* Trend variants */
    .ahcfree-mbox-badge-up {
        background: #e8f6ee;
        color: #1a8245;
    }
    .ahcfree-mbox-badge-down {
        background: #fdecec;
        color: #c0392b;
    }
    .ahcfree-mbox-sub {
        color: #a0a6ad;
        white-space: nowrap;
        font-size: 11.5px;
    }
    /* Upgrade box specific — blurred number + small upgrade link */
    .ahcfree-mbox-value-blur {
        filter: blur(8px);
        width: 60px;
        user-select: none;
        pointer-events: none;
    }
    .ahcfree-mbox-upgrade-link {
        color: currentColor;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    </style>


    <!-- Traffic Chart in admin page -->

    <div class="row">
        <div class="col-lg-12">

            <div class="panel" style="background-color:white ;border-radius: 7px;">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    <?php echo "Traffic Report "; ?>
                </h2>
                <div class="hits_duration_select">
                    <select id="hits-duration" class="hits-duration" style="width: 150px; height: 35px; font-size: 15px;">
                        <option value="">Last <?php echo AHCFREE_VISITORS_VISITS_LIMIT; ?> days</option>
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="current_month">This month</option>
                        <option value="last_month">Last month</option>
                        <option value="0">Since installing the plugin</option>
                        <option value="range">Custom Period</option>
                    </select>

                    <span id="duration_area">
                        <?php
                        $summary_from_dt = isset($_POST['summary_from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['summary_from_dt']) : '';
                        $summary_to_dt = isset($_POST['summary_to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['summary_to_dt']) : '';
                        ?>
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="summary_from_dt" id="summary_from_dt" autocomplete="off" value="<?php echo esc_attr($summary_from_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="summary_to_dt" id="summary_to_dt" autocomplete="off" value="<?php echo esc_attr($summary_to_dt); ?>" />
                    </span>
                </div>
                <div class="panelcontent" id="visitors_graph_stats" style="width:100% !important; overflow:hidden">
                    <canvas id="visitscount" style="height:400px; width:99% !important;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!--  Summary Statistics / Map in admin page -->

    <div class="row ahcfree-map-row" style="display:flex; flex-wrap:wrap;">
        <div class="col-lg-8" style="display:flex;">
            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">

                <div class="panelcontent" style="width:100% !important; flex:1 1 auto; display:flex; flex-direction:column;">
                    <?php
                    $ahcfree_map_visitors = function_exists('ahcfree_get_today_visitors_for_map') ? ahcfree_get_today_visitors_for_map() : array();
                    $ahcfree_map_points = array();
                    if (isset($ahcfree_map_visitors['data']) && is_array($ahcfree_map_visitors['data'])) {
                        foreach ($ahcfree_map_visitors['data'] as $mp) {
                            if (!empty($mp['ctr_latitude']) && !empty($mp['ctr_longitude'])) {
                                $ahcfree_map_points[] = array(
                                    'lat'  => (float) $mp['ctr_latitude'],
                                    'lng'  => (float) $mp['ctr_longitude'],
                                    'name' => $mp['ctr_name'],
                                    'code' => strtolower($mp['ctr_internet_code']),
                                    'cnt'  => intval($mp['visitors']),
                                );
                            }
                        }
                    }
                    ?>
                    <div id="ahcfree_visitor_map" style="width:100%; flex:1 1 auto; min-height:430px; border-radius:0 0 7px 7px; z-index:0;"></div>
                    <script>
                        (function () {
                            var ahcfreeMapPoints = <?php echo wp_json_encode($ahcfree_map_points); ?>;
                            var ahcfreeFlagBase = "<?php echo esc_js(plugins_url('images/flags/', AHCFREE_PLUGIN_MAIN_FILE)); ?>";
                            var ahcfreeMarkerIcon = "<?php echo esc_js(plugins_url('images/marker-icon.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>";
                            var ahcfreeMarkerIcon2x = "<?php echo esc_js(plugins_url('images/marker-icon-2x.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>";
                            var ahcfreeMarkerShadow = "<?php echo esc_js(plugins_url('images/marker-shadow.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>";

                            function ahcfreeInitMap() {
                                if (typeof L === "undefined") { return; }
                                var el = document.getElementById("ahcfree_visitor_map");
                                if (!el || el._ahcfreeInit) { return; }
                                el._ahcfreeInit = true;

                                var map = L.map("ahcfree_visitor_map", {
                                    center: [20, 0],
                                    zoom: 2,
                                    scrollWheelZoom: false,
                                    worldCopyJump: true
                                });

                                L.tileLayer("https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png", {
                                    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                                    subdomains: 'abcd',
                                    maxZoom: 18
                                }).addTo(map);

                                var icon = L.icon({
                                    iconUrl: ahcfreeMarkerIcon,
                                    iconRetinaUrl: ahcfreeMarkerIcon2x,
                                    shadowUrl: ahcfreeMarkerShadow,
                                    iconSize: [25, 41],
                                    iconAnchor: [12, 41],
                                    popupAnchor: [1, -34],
                                    shadowSize: [41, 41]
                                });

                                var bounds = [];
                                ahcfreeMapPoints.forEach(function (p) {
                                    if (isNaN(p.lat) || isNaN(p.lng)) { return; }
                                    var flag = p.code ? '<img src="' + ahcfreeFlagBase + p.code + '.png" width="22" height="15" style="vertical-align:middle;margin-right:6px;border-radius:2px;" onerror="this.style.display=\'none\'" />' : '';
                                    var popup = '<div style="font-size:13px;">' + flag + '<strong>' + p.name + '</strong> &nbsp;(' + p.cnt + ')</div>';
                                    L.marker([p.lat, p.lng], { icon: icon }).addTo(map).bindPopup(popup);
                                    bounds.push([p.lat, p.lng]);
                                });

                                if (bounds.length > 0) {
                                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 5 });
                                }

                                // The map lives in a flex column, so its final
                                // height settles after layout — refresh tiles a
                                // few times and on resize to avoid grey gaps.
                                setTimeout(function () { map.invalidateSize(); }, 200);
                                setTimeout(function () { map.invalidateSize(); }, 600);
                                setTimeout(function () { map.invalidateSize(); }, 1200);
                                window.addEventListener("resize", function () { map.invalidateSize(); });
                            }

                            if (document.readyState === "loading") {
                                document.addEventListener("DOMContentLoaded", ahcfreeInitMap);
                            } else {
                                ahcfreeInitMap();
                            }
                        })();
                    </script>
                </div>
            </div>
        </div>
        <?php
        $ahc_sum_stat = ahcfree_get_summary_statistics();
        ?>
        <div class="col-lg-4">
            <div class="panel-group">
                <div class="panel" style="width:100% !important">
                    <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_summary_statistics ?></h2>
                    <div class="panelcontent" style="width:100% !important">
                        <table width="95%" tableborder="0" cellspacing="0" id="summary_statistics">
                            <thead>
                                <tr>
                                    <th width="40%"></th>
                                    <th width="30%"><b><?php echo ahc_visitors ?></b></th>
                                    <th width="30%"><?php echo ahc_visits ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b><?php echo ahc_today ?></b></td>
                                    <td class="values"><span id="today_visitors"><?php echo ahcfree_NumFormat($ahc_sum_stat['today']['visitors']); ?></span></td>
                                    <td class="values"><span id="today_visits"><?php echo ahcfree_NumFormat($ahc_sum_stat['today']['visits']); ?></span></td>
                                </tr>

                                <tr>
                                    <td><b><?php echo ahc_yesterday ?></b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['yesterday']['visitors']); ?></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['yesterday']['visits']); ?></td>
                                </tr>

                                <tr>
                                    <td><b><?php echo ahc_this_week ?></b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['week']['visitors']); ?></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['week']['visits']); ?></td>
                                </tr>

                                <tr>
                                    <td><b><?php echo ahc_this_month ?></b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['month']['visitors']); ?></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['month']['visits']); ?></td>
                                </tr>

                                <tr>
                                    <td><b><?php echo ahc_this_yesr ?></b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['year']['visitors']); ?></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['year']['visits']); ?></td>
                                </tr>

                                <tr>
                                    <td style="color:#090"><strong><b><?php echo ahc_total ?></b></strong></td>
                                    <td class="values" style="color:#090"><strong><?php echo ahcfree_NumFormat($ahc_sum_stat['total']['visitors']); ?></strong></td>
                                    <td class="values" style="color:#090"><strong><?php echo ahcfree_NumFormat($ahc_sum_stat['total']['visits']); ?></strong></td>
                                </tr>

                                <!-- New Enhanced Metrics Rows (Total Only) -->
                                <tr>
                                    <td><b>New Visitors</b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['total']['new_visitors']); ?></strong></td>
                                    <td class="values">-</td>
                                </tr>

                                <tr>
                                    <td><b>Returning Visitors</b></td>
                                    <td class="values"><?php echo ahcfree_NumFormat($ahc_sum_stat['total']['returning_visitors']); ?></>
                                    </td>
                                    <td class="values">-</td>
                                </tr>

                                <tr>
                                    <td><b>Bounce Rate</b></td>
                                    <td class="values"><?php echo $ahc_sum_stat['total']['bounce_rate']; ?>%</>
                                    </td>
                                    <td class="values">-</td>
                                </tr>

                                <tr>
                                    <td><b>Avg Session Duration</b></td>
                                    <td class="values"><?php echo ahcfree_format_duration($ahc_sum_stat['total']['avg_session_duration']); ?></>
                                    </td>
                                    <td class="values">-</td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- end visitors and visits section -->

                    </div>
                </div>

                <div class="panel" style="width:100% !important">
                    <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_search_engines_statistics ?></h2>
                    <div class="panelcontent" style="width:100% !important">
                        <table width="95%" tableborder="0" cellspacing="0" id="search_engine">
                            <thead>
                                <tr>
                                    <th width="40%">Engine</th>
                                    <th width="30%">Total</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $alltimeSER = ahcfree_get_hits_search_engines_referers('alltime');

                                $tot_srch = 0;
                                if (is_array($alltimeSER)) {
                                    foreach ($alltimeSER as $ser => $v) {
                                        $tot_srch += $v;
                                        $ser = (!empty($ser)) ? $ser : 'Other';
                                ?>
                                        <tr>
                                            <td>
                                                <div>
                                                    <span><b><?php echo esc_html($ser); ?></b></span>
                                                </div>
                                            </td>
                                            <td class="values"><?php echo ahcfree_NumFormat(intval($v)); ?></td>

                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <td><strong>Total </strong></td>
                                    <td class="values"><strong id="today_search"><?php echo ahcfree_NumFormat(intval($tot_srch)); ?></strong></td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row ahcfree-eqrow" style="display:flex; flex-wrap:wrap;">
        <div class="col-lg-8" style="display:flex;">
            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">
                <h2 class="box-heading"
                    style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    Recent visitors by IP<span class="search_data"><a href="#" class="dashicons dashicons-search"
                            title="Search"></a></span></h2>
                <div
                    class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "recent_visitor_by_ip") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">

                        <?php
                        $r_from_dt = isset($_POST['r_from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['r_from_dt']) : '';
                        $r_to_dt = isset($_POST['r_to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['r_to_dt']) : '';
                        $ip_addr = isset($_POST['ip_addr']) ? ahc_free_sanitize_text_or_array_field($_POST['ip_addr']) : '';
                        ?>

                        <label>Search: </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="recent_visitor_by_ip" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear"
                            name="r_from_dt" id="r_from_dt" autocomplete="off"
                            value="<?php echo esc_attr($r_from_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="r_to_dt"
                            id="r_to_dt" autocomplete="off" value="<?php echo esc_attr($r_to_dt); ?>" />
                        <input type="text" name="ip_addr" id="ip_addr" placeholder="IP address" class="ahc_clear"
                            value="<?php echo esc_attr($ip_addr); ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <div class="panelcontent" style="width:100% !important; min-height:280px; flex:1 1 auto;">

                    <!-- Modal -->
                    <div class="modal fade" id="DayHitsModal" role="dialog" tabindex="-1">
                        <br>
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">IP Tracking</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="ahc_loader" style="width:100px !important; height:50px !important;">
                                        &nbsp;</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table width="95%" tableborder="0" cellspacing="0" class="recentv" id="recent_visit_by_ip">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Location</th>
                                <th>Time</th>
                                <th>Duration</th>
                                <th>Hits</th>
                            </tr>
                        </thead>

                        <tbody>

                        </tbody>

                    </table>

                </div>
            </div>
        </div>
        <?php

        $countries  = array();
        ?>
        <div class="col-lg-4" style="display:flex;">
            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    <?php
                    if (isset($_POST['t_from_dt']) && $_POST['t_from_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "traffic_index_country") {
                        echo "Traffic Index by Country";
                    } else {
                        echo "Today Traffic by Country ";
                    }
                    ?>
                    <span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span>
                </h2>

                <div class="panelcontent" style="width:100% !important; flex:1 1 auto; display:flex; flex-direction:column;">
                    <?php ahcfree_render_locked_country_panel('today'); ?>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <!-- browsers chart panel -->
            <div class="panel" style="width:100% !important; overflow:hidden">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_browsers ?></h2>
                <div class="panelcontent" style="width:100% !important">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="brsPiechartContainer" style=" height: 400px;"></div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <!-- search engines chart panel -->


            <div class="panel" style="width:100% !important; overflow:hidden">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Search Engines</h2>
                <div class="panelcontent" style="width:100% !important">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="srhEngBieChartContainer" style=" height: 400px;"></div>
                        </div>



                    </div>
                </div>
            </div>




        </div>
    </div>


    <div class="row">
        <?php
        /*$countries_data = ahcfree_get_top_countries("","","","",true);*/
        $countries_data = array();
        if (isset($countries_data['data'])) {
            $countries = $countries_data['data'];
        } else {
            $countries = false;
        }
        ?>
        <div class="col-lg-6">
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Traffic by country</h2>
                <div class="panelcontent" style="width:100% !important">
                    <?php ahcfree_render_locked_country_panel('all'); ?>
                </div>
            </div>

        </div>

        <div class="col-lg-6">
            <!-- Countries chart panel -->
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Top Referring Countries</h2>

                <div class="panelcontent" style="width:100% !important">
                    <?php ahcfree_render_locked_country_panel('referring'); ?>
                </div>

            </div>

        </div>
    </div>
    <div class="row ahcfree-eqrow" style="display:flex; flex-wrap:wrap;">
        <div class="col-lg-6" style="display:flex;">

            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_refering_sites ?><span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span></h2>
                <div class="panelcontent" style="width:100% !important; flex:1 1 auto; min-height:300px;">
                    <table width="95%" tableborder="0" cellspacing="0" id="top_refering_sites">
                        <thead>
                            <tr>
                                <th width="70%"><?php echo ahc_site_name ?></th>
                                <th width="30%"><?php echo ahc_total_times ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $googlehits = 0;

                            $norecord = "";
                            $referingSites = ahcfree_get_top_refering_sites();
                            if (is_array($referingSites) && count($referingSites) > 0) {
                                foreach ($referingSites as $site) {
                                    /*if (strpos($site['site_name'], 'google')) {
										$googlehits += $site['total_hits'];
									} else {
*/

                                    str_replace('https://', '', $site['site_name']);
                            ?>
                                    <tr>
                                        <td class="values"><?php echo esc_html($site['site_name']); ?>&nbsp;<a href="https://<?php echo str_replace('http://', '', esc_url($site['site_name'])) ?>" target="_blank"><img src="<?php echo esc_url(plugins_url('images/openW.jpg', AHCFREE_PLUGIN_MAIN_FILE)) ?>" title="<?php echo esc_attr(ahc_view_referer) ?>"></a></td>
                                        <td class="values"><?php echo intval($site['total_hits']); ?></td>
                                    </tr>

                            <?php
                                    //}
                                }
                            } else {
                                $norecord = 1;
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    if ($norecord == "1") {
                    ?>
                        <div class="vtrts-empty">
                            <div class="vtrts-empty__icon">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 13a5 5 0 007.07 0l3-3a5 5 0 00-7.07-7.07L11.5 4.5" stroke="#2271b1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 11a5 5 0 00-7.07 0l-3 3a5 5 0 007.07 7.07L12.5 19.5" stroke="#2271b1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="vtrts-empty__title">No referring sites yet</div>
                            <div class="vtrts-empty__text">When visitors arrive from other websites, you&rsquo;ll see which sites send you the most traffic here.</div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>

        </div>


        <!-- time visits graph begin -->
        <div class="col-lg-6" style="display:flex;">
            <?php
            //$times = ahcfree_get_time_visits();
            $times = array();
            ?>
            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">
                <h2 class="box-heading"
                    style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    Visits Time Graph
                    <span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span>
                    <span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span>
                </h2>
                <div
                    class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "visit_time") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">
                        <?php
                        $vfrom_dt = isset($_POST['vfrom_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['vfrom_dt']) : '';
                        $vto_dt = isset($_POST['vto_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['vto_dt']) : '';
                        ?>

                        <label>Search : </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="visit_time" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="vfrom_dt"
                            id="vfrom_dt" autocomplete="off" value="<?php echo esc_attr($vfrom_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="vto_dt"
                            id="vto_dt" autocomplete="off" value="<?php echo esc_attr($vto_dt); ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <div class="panelcontent" style="padding-right: 50px;">
                    <table width="100%" tableborder="0" cellspacing="0" id="visit_time_graph_table">
                        <thead>
                            <tr>
                                <th width="25%">Time</th>
                                <th width="55%">Visitors Graph</th>
                                <th width="10%">Visitors</th>
                                <th width="10%">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (is_array($times)) {
                                foreach ($times as $t) {
                            ?>
                                    <tr>
                                        <td class="values">
                                            <?php echo esc_html($t['vtm_time_from']) . ' - ' . esc_html($t['vtm_time_to']) ?>
                                        </td>
                                        <td class="values">
                                            <div class="visitorsGraphContainer">
                                                <div class="<?php
                                                            if (ceil($t['percent']) > 25 && ceil($t['percent']) < 50) {
                                                                echo 'visitorsGraph2';
                                                            } else if (ceil($t['percent']) > 50) {
                                                                echo 'visitorsGraph3';
                                                            } else {
                                                                echo 'visitorsGraph';
                                                            }
                                                            ?>"
                                                    <?php echo (!empty($t['percent']) ? ' style="width: ' . ceil($t['percent']) . '%;"' : '') ?>>
                                                    &nbsp;</div>
                                                <div class="cleaner"></div>
                                            </div>
                                            <div class="visitorsPercent">(<?php echo ceil($t['percent']) ?>)%..</div>
                                        </td>
                                        <td class="values"><?php echo intval($t['vtm_visitors']); ?></td>
                                        <td class="values"><?php echo intval($t['vtm_visits']); ?></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row ahcfree-eqrow" style="display:flex; flex-wrap:wrap;">
        <div class="col-lg-6" style="display:flex;">
            <?php
            // $tTitles = ahcfree_get_traffic_by_title();
            $tTitles = array();
            ?>
            <div class="panel"
                style="width:100% !important; border-radius: 7px !important; border: 0 !important; box-shadow: 0 4px 25px 0 rgb(168 180 208 / 10%) !important; display:flex; flex-direction:column;">
                <h2 class="box-heading"
                    style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    Traffic by Title
                    <span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span>
                    <span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span>
                </h2>

                <div class="panelcontent" style="border-radius:0 0 7px 7px !important; padding-right: 50px; flex:1 1 auto; min-height:300px;">
                    <!-- Modal - matching Recent Visitors style -->
                    <div class="modal fade" id="TrafficStatsModal" role="dialog" tabindex="-1">
                        <br>
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Page Statistics</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="ahc_loader" style="width:100px !important; height:50px !important;">
                                        &nbsp;</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table width="100%" border="0" cellspacing="0" id="traffic_by_title">
                        <thead>
                            <tr>
                                <th width="5%">Rank</th>
                                <th width="60%">Title</th>
                                <th width="20%" id="hits">Hits</th>
                                <th width="15%">Visit %</th>
                            </tr>

                        </thead>
                        <tbody>
                            <?php
                            $norecord = "";
                            if (is_array($tTitles) && count($tTitles) > 0) {
                                foreach ($tTitles as $t) {
                            ?>
                                    <tr>
                                        <td class="values"><?php echo intval($t['rank']) ?></td>
                                        <td class="values">
                                            <a href="<?php echo esc_url(get_permalink($t['til_page_id'])); ?>" target="_blank">
                                                <?php echo esc_html($t['til_page_title']); ?>
                                            </a>
                                        </td>
                                        <td class="values"><?php echo ahcfree_NumFormat(intval($t['til_hits'])); ?></td>
                                        <td class="values"><?php echo esc_html($t['percent']) ?></td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6" style="display:flex;">
            <?php
            /*$lastSearchKeyWordsUsed = ahcfree_get_latest_search_key_words_used();*/
            $lastSearchKeyWordsUsed = array();
            /*if ($lastSearchKeyWordsUsed) 
            {*/
            ?>
            <!-- last search key words used -->
            <div class="panel" style="width:100% !important; display:flex; flex-direction:column;">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_latest_search_words; ?><span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span><span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span></h2>
                <div class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "lastest_search") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">

                        <?php
                        $from_dt = isset($_POST['from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['from_dt']) : '';
                        $to_dt = isset($_POST['to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['to_dt']) : '';

                        ?>
                        <label>Search in Time Frame: </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="lastest_search" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="from_dt" id="from_dt" autocomplete="off" value="<?php echo esc_attr($from_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="to_dt" id="to_dt" autocomplete="off" value="<?php echo esc_attr($to_dt); ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <div class="panelcontent" style="padding-right: 50px;">
                    <table width="100%" tableborder="0" cellspacing="0" id="lasest_search_words">
                        <thead>
                            <tr>
                                <th width="20%">Country</th>
                                <th width="30%">Info.</th>
                                <th width="40%">Keyword</th>
                                <th width="10%" class='text-center'>Date</th>
                            </tr>
                        </thead>


                        <?php
                        if (count($lastSearchKeyWordsUsed) > 0) {
                        ?>
                            <tbody>
                                <?php
                                foreach ($lastSearchKeyWordsUsed as $searchWord) {
                                    $visitDate = new DateTime($searchWord['hit_date']);
                                    $visitDate->setTimezone($custom_timezone);
                                ?>
                                    <tr>
                                        <td>
                                            <span><?php if ($searchWord['ctr_internet_code'] != '') { ?><img src="<?php echo plugins_url('images/flags/' . strtolower($searchWord['ctr_internet_code']) . '.png', AHCFREE_PLUGIN_MAIN_FILE); ?>" border="0" width="22" height="18" title="<?php echo esc_html($searchWord['ctr_name']) ?>" onerror="imgFlagError(this)" /><?php } ?></span>
                                        </td>
                                        <td class="hide"><?php echo esc_html($searchWord['csb']); ?></td>
                                        <td>
                                            <span class="searchKeyWords"><a href="<?php echo esc_url($searchWord['hit_referer']); ?>" target="_blank"><?php echo esc_html($searchWord['hit_search_words']) ?></a></span>
                                        </td>
                                        <td>
                                            <span class="visitDateTime">&nbsp;<?php echo esc_html($visitDate->format('d/m/Y')); ?></span>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        <?php
                        }
                        ?>
                    </table>
                    <?php if (count($lastSearchKeyWordsUsed) === 0) : ?>
                        <div class="vtrts-empty">
                            <div class="vtrts-empty__icon">
                                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="11" cy="11" r="7" stroke="#2271b1" stroke-width="1.8"/>
                                    <path d="M21 21l-4.3-4.3" stroke="#2271b1" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="vtrts-empty__title">No search keywords yet</div>
                            <div class="vtrts-empty__text">When visitors reach your site by searching on Google or Bing, the keywords they used will show up here.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php /*}*/ ?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">

            <div class="panel"
                style="width:100% !important; border-radius: 7px !important;  border: 0 !important; box-shadow: 0 4px 25px 0 rgb(168 180 208 / 10%) !important;">
                <h2 class="box-heading"
                    style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    Traffic Sources</h2>
                <div style="display:none;"
                    class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "traffic_sources") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">
                        <label>Search in Time Frame: </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="traffic_sources" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="from_dt"
                            id="traffic_from_dt" autocomplete="off"
                            value="<?php echo isset($_POST['from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['from_dt']) : ''; ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="to_dt"
                            id="traffic_to_dt" autocomplete="off"
                            value="<?php echo isset($_POST['to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['to_dt']) : ''; ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <?php
                // Traffic Sources is a Pro feature shown as a blurred sample;
                // no real data is fetched here.
                $trafficSources = array();
                ?>
                <div class="panelcontent" style="border-radius:0 0 7px 7px !important; padding-right: 50px;">

                    <div class="vtrts-locked">
                        <div class="vtrts-locked__data" aria-hidden="true">
                    <table width="100%" border="0" cellspacing="0" id="traffic_sources_locked">
                        <thead>
                            <tr>
                                <th width="5%">Rank</th>
                                <th width="20%">Referrer</th>
                                <th width="20%">Sessions</th>
                                <th width="30%">Hits %</th>
                                <th width="25%">Source Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Always use SAMPLE data here — never real figures —
                            // because the blur can be removed client-side.
                            if (false) {
                                foreach ($trafficSources as $source) {
                            ?>
                                    <tr>
                                        <td class="values"><?php echo intval($source['rank']); ?></td>
                                        <td class="values">
                                            <span class="traffic-source-item">
                                                <span class="source-icon"><?php echo esc_html($source['icon']); ?></span>
                                                <span class="source-name"><?php echo esc_html($source['name']); ?></span>
                                            </span>
                                        </td>
                                        <td class="values"><?php echo number_format(intval($source['sessions'])); ?></td>
                                        <td class="values">
                                            <div class="traffic-percentage-container">
                                                <div class="visitorsGraphContainer">
                                                    <div class="<?php
                                                                if (ceil($source['percent']) > 25 && ceil($source['percent']) < 50) {
                                                                    echo 'visitorsGraph2';
                                                                } else if (ceil($source['percent']) > 50) {
                                                                    echo 'visitorsGraph3';
                                                                } else {
                                                                    echo 'visitorsGraph';
                                                                }
                                                                ?>"
                                                        <?php echo (!empty($source['percent']) ? 'style="width: ' . ceil($source['percent']) . '%;"' : '') ?>>
                                                        &nbsp;(<?php echo ceil($source['percent']); ?>)%</div>
                                                    <div class="cleaner"></div>
                                                </div>

                                            </div>
                                        </td>
                                        <td class="values">
                                            <span
                                                class="source-type <?php echo strtolower(str_replace(' ', '-', $source['type'])); ?>">
                                                <?php echo esc_html($source['type']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                // Representative sample so the locked panel never looks empty.
                                $vtrts_src_sample = array(
                                    array('Direct Traffic', 87, 64, 'DIRECT'),
                                    array('google.com', 23, 17, 'REFERRAL'),
                                    array('facebook.com', 12, 9, 'REFERRAL'),
                                    array('t.co', 6, 4, 'REFERRAL'),
                                    array('bing.com', 4, 3, 'REFERRAL'),
                                    array('linkedin.com', 3, 2, 'REFERRAL'),
                                );
                                $vtrts_src_rank = 1;
                                foreach ($vtrts_src_sample as $s) {
                                ?>
                                <tr>
                                    <td class="values"><?php echo intval($vtrts_src_rank); ?></td>
                                    <td class="values">
                                        <span class="traffic-source-item">
                                            <span class="source-name"><?php echo esc_html($s[0]); ?></span>
                                        </span>
                                    </td>
                                    <td class="values"><?php echo number_format($s[1]); ?></td>
                                    <td class="values">
                                        <div class="traffic-percentage-container">
                                            <div class="visitorsGraphContainer">
                                                <div class="<?php echo ($s[2] > 50 ? 'visitorsGraph3' : ($s[2] > 25 ? 'visitorsGraph2' : 'visitorsGraph')); ?>" style="width: <?php echo intval($s[2]); ?>%;">
                                                    &nbsp;(<?php echo intval($s[2]); ?>)%</div>
                                                <div class="cleaner"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="values">
                                        <span class="source-type <?php echo strtolower($s[3]); ?>"><?php echo esc_html($s[3]); ?></span>
                                    </td>
                                </tr>
                                <?php
                                    $vtrts_src_rank++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                        </div>
                        <div class="vtrts-locked__overlay">
                            <div class="vtrts-locked__lock">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="5" y="11" width="14" height="9" rx="2" stroke="#fff" stroke-width="2"/>
                                    <path d="M8 11V8a4 4 0 018 0v3" stroke="#fff" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="vtrts-locked__headline">See where your traffic comes from</div>
                            <div class="vtrts-locked__subline">Pro reveals every traffic source &mdash; direct, search, social and referrals &mdash; with full history.</div>
                            <a class="vtrts-locked__btn" target="_blank" rel="noopener" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                                Unlock with Pro
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* General styling for export icons */
        .export_data a {
            transition: all 0.3s ease;
        }

        .export_data a:hover {
            opacity: 0.7;
        }

        /* If using dashicons-media-spreadsheet, you can color it green */
        .dashicons-media-spreadsheet {
            color: #217346 !important;
        }

        .dashicons-media-spreadsheet:hover {
            color: #1e6b3e !important;
        }

        /* Traffic Sources Styling */
        .traffic-source-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .source-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .source-name {
            font-weight: 500;
            color: #1d2327;
        }

        .traffic-percentage-container {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .source-type {
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .source-type.direct {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .source-type.organic-search {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .source-type.referral {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .source-type.social-media {
            background-color: #fce4ec;
            color: #c2185b;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="upgrade-footer-panel">
                <div class="upgrade-content">
                    <div class="upgrade-text">
                        <h3>Need More Statistics?</h3>
                        <p>Upgrade to the professional version now. The premium version of Visitor Traffic real-time
                            statistics is completely different from the free version with many more features included.
                        </p>
                    </div>
                    <div class="upgrade-action">
                        <a target="_blank"
                            href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&footer=true"
                            class="upgrade-btn">
                            <span class="btn-text">Upgrade Now</span>
                            <span class="btn-discount">50% OFF</span>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .upgrade-footer-panel {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .upgrade-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .upgrade-text {
            flex: 1;
            text-align: left;
        }

        .upgrade-text h3 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
            margin: 0 0 8px 0;
        }

        .upgrade-text p {
            color: #495057;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
        }

        .upgrade-action {
            text-align: center;
            flex-shrink: 0;
        }

        .upgrade-btn {
            display: inline-block;
            background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
            color: #2c3e50;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(255, 215, 0, 0.3);
            border: 2px solid #ffd700;
        }

        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
            background: linear-gradient(135deg, #ffb347 0%, #ffd700 100%);
            text-decoration: none;
            color: #2c3e50;
        }

        .btn-text {
            margin-right: 12px;
        }

        .btn-discount {
            background: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
            position: absolute;
            top: -6px;
            right: -6px;
            box-shadow: 0 2px 6px rgba(231, 76, 60, 0.3);
        }

        .payment-badges {
            margin-top: 10px;
            opacity: 0.8;
        }

        .payment-badges img {
            max-height: 25px;
            width: auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .upgrade-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .upgrade-text h3 {
                font-size: 18px;
            }

            .upgrade-text p {
                font-size: 13px;
            }

            .upgrade-btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .upgrade-footer-panel {
                padding: 15px;
                margin: 15px 0;
            }
        }
    </style>
    <?php
    $visits_visitors_data = ahcfree_get_visits_by_custom_duration_callback($mystart_date, $myend_date, $stat = '');
    ?>


    <?php
    wp_register_script('ahc_gstatic_js', 'https://www.gstatic.com/charts/loader.js', array(), '1.0.0', true);
    wp_enqueue_script('ahc_gstatic_js');
    ?>
    <script language="javascript" type="text/javascript">
        // Global chart instance for managing updates
        let visitsChart = null;
        let chartJsLoaded = false;

        function loadChartJS(callback) {
            if (chartJsLoaded) {
                callback();
                return;
            }

            // Check if Chart.js is already loaded
            if (typeof Chart !== 'undefined') {
                chartJsLoaded = true;
                callback();
                return;
            }

            // Load Chart.js dynamically
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
            script.onload = function() {
                chartJsLoaded = true;
                callback();
            };
            script.onerror = function() {
                console.error('Failed to load Chart.js');
            };
            document.head.appendChild(script);
        }

        function drawVisitsLineChart(start_date, end_date, interval, visitors, visits, duration) {
            loadChartJS(function() {
                drawChart();
            });

            function drawChart() {
                const ctx = document.getElementById('visitscount');

                // Destroy existing chart if it exists
                if (visitsChart) {
                    visitsChart.destroy();
                }

                // Prepare data
                const labels = [];
                const visitorsData = [];
                const visitsData = [];

                for (let i = 0; i < visitors.length; i++) {
                    labels.push(visitors[i][0]);
                    visitorsData.push(parseFloat(visitors[i][1]));
                    visitsData.push(parseFloat(visits[i][1]));
                }

                // Create chart
                visitsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visitors',
                            data: visitorsData,
                            borderColor: '#3366CC',
                            backgroundColor: 'rgba(51, 102, 204, 0.1)',
                            tension: 0, // Straight lines (equivalent to curveType: 'none')
                            fill: false,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }, {
                            label: 'Page Views',
                            data: visitsData,
                            borderColor: '#DC3912',
                            backgroundColor: 'rgba(220, 57, 18, 0.1)',
                            tension: 0,
                            fill: false,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {

                            legend: {
                                position: 'top',
                                labels: {
                                    color: 'blue',
                                    font: {
                                        size: 16
                                    },
                                    usePointStyle: true,
                                    padding: 20
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.2)',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: {
                                display: true,
                                title: {
                                    display: true,
                                    text: 'Date'
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            y: {
                                display: true,
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count'
                                },
                                grid: {
                                    display: true,
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        },
                        elements: {
                            point: {
                                hoverRadius: 8
                            }
                        }
                    }
                });

            }
        }

        function drawBrowsersPieChart() {


            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Browser');
                data.addColumn('number', 'Hits');
                data.addRows([

                    <?php echo ahcfree_get_browsers_hits_counts(); ?>
                ]);

                var brsContainer = document.getElementById('brsPiechartContainer');

                // With only a single browser (or none), a pie chart is not
                // meaningful yet, so show a friendly empty state instead.
                if (data.getNumberOfRows() <= 1) {
                    if (brsContainer) {
                        brsContainer.style.height = 'auto';
                        brsContainer.innerHTML =
                            '<div class="vtrts-empty">' +
                            '<div class="vtrts-empty__icon">' +
                            '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                            '<circle cx="12" cy="12" r="9" stroke="#2271b1" stroke-width="1.8"/>' +
                            '<path d="M3 12h18M12 3c2.5 2.5 2.5 15 0 18M12 3c-2.5 2.5-2.5 15 0 18" stroke="#2271b1" stroke-width="1.4"/>' +
                            '</svg></div>' +
                            '<div class="vtrts-empty__title">No browsers data yet</div>' +
                            '<div class="vtrts-empty__text">Once visitors arrive using different browsers, you&rsquo;ll see the breakdown by browser here.</div>' +
                            '</div>';
                    }
                    return;
                }

                var options = {
                    title: '',
                    slices: {
                        4: {
                            offset: 0.2
                        },
                        12: {
                            offset: 0.3
                        },
                        14: {
                            offset: 0.4
                        },
                        15: {
                            offset: 0.5
                        },
                    },
                };

                var chart = new google.visualization.PieChart(brsContainer);
                chart.draw(data, options);
            }
        }

        function drawSrhEngVstLineChart() {


            google.charts.load('current', {
                'packages': ['corechart']
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {

                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Browser');
                data.addColumn('number', 'Hits');
                data.addRows([

                    <?php echo ahcfree_get_serch_visits_by_date(); ?>
                ]);

                var srhContainer = document.getElementById('srhEngBieChartContainer');

                // No search-engine traffic yet: show a friendly message instead
                // of an empty chart so the panel doesn't look broken.
                if (data.getNumberOfRows() === 0) {
                    if (srhContainer) {
                        srhContainer.style.height = 'auto';
                        srhContainer.innerHTML =
                            '<div class="vtrts-empty">' +
                            '<div class="vtrts-empty__icon">' +
                            '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                            '<circle cx="11" cy="11" r="7" stroke="#2271b1" stroke-width="1.8"/>' +
                            '<path d="M21 21l-4.3-4.3" stroke="#2271b1" stroke-width="1.8" stroke-linecap="round"/>' +
                            '</svg></div>' +
                            '<div class="vtrts-empty__title">No search engine visits yet</div>' +
                            '<div class="vtrts-empty__text">Once visitors find your site through Google, Bing or other search engines, the breakdown will appear here.</div>' +
                            '</div>';
                    }
                    return;
                }

                var options = {
                    title: '',
                    slices: {
                        4: {
                            offset: 0.2
                        },
                        12: {
                            offset: 0.3
                        },
                        14: {
                            offset: 0.4
                        },
                        15: {
                            offset: 0.5
                        },
                    },
                };

                var chart = new google.visualization.PieChart(document.getElementById('srhEngBieChartContainer'));
                chart.draw(data, options);
            }



        }

        var mystart_date = "<?php echo esc_js($mystart_date); ?>";
        var myend_date = "<?php echo esc_js($myend_date); ?>";
        var mystart_date_full = "<?php echo esc_js($mystart_date_full); ?>";
        var myend_date_full = "<?php echo esc_js($myend_date_full); ?>";


        var countriesData = <?php echo json_encode(ahcfree_get_top_countries(10, "", "", "", false)); ?>;
        var visits_data = <?php echo json_encode($visits_visitors_data['visits']); ?>;
        var visitors_data = <?php echo json_encode($visits_visitors_data['visitors']); ?>;
        //console.log(visits_data);
        // console.log(visitors_data);
        jQuery(document).ready(function() {
            jQuery('#duration_area').hide();

            //------------------------------------------
            //if(visitsData.success && typeof visitsData.data != 'undefined'){
            var duration = jQuery('#hits-duration').val();
            drawVisitsLineChart(mystart_date, myend_date, '1 day', visitors_data, visits_data, duration);
            //}
            //------------------------------------------



            if (typeof drawBrowsersPieChart === "function") {

                drawBrowsersPieChart();
            }
            //------------------------------------------
            if (typeof drawSrhEngVstLineChart === "function") {
                drawSrhEngVstLineChart();
            }





            jQuery(document).on('click', '.SwalBtn1', function() {
                swal.clickConfirm();
            });
            jQuery(document).on('click', '.SwalBtn2', function() {
                window.open(
                    "https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?popup=1",
                    '_blank'
                );

                swal.clickConfirm();
            });
            jQuery(document).on('click', '.SwalBtn3', function() {
                localStorage.setItem("ahcfreemsg", "1");
                swal.clickConfirm();
            });

            var save_method; //for save method string
            var host = window.location.hostname;
            var fullpath = window.location.pathname;
            var fullparam = window.location.search.split('&');

            var firstparam = fullparam[0];

            if (localStorage && (firstparam == "?page=ahc_hits_counter_menu_free")) {

                var today_visitors_box = document.getElementById('today_visitors_box').innerHTML;
                if (!localStorage.getItem("ahcfreemsg") == true) {

                    if (today_visitors_box > 5) {
                        setTimeout(function() {

                            swal({
                                title: 'Take Your Website Analytics to The Next Level!',
                                text: 'Upgrade to Pro',
                                imageUrl: '<?php echo plugin_dir_url(__FILE__); ?>images/ezgif.com-animated-gif-maker.gif',
                                imageWidth: '95%',

                                animation: true,
                                customClass: 'swal-noscroll',
                                allowEscapeKey: true,
                                showCancelButton: false,
                                showConfirmButton: false,
                                html: 'Get real-time stats, visitor locations & live online counter, Unlock all PRO features now!?<br><br><center><button type="button" role="button" class="confirm btn btn-success SwalBtn2">' + 'Upgrade to PRO' + '</button>&nbsp;&nbsp;' +
                                    '<button type="button" role="button"  class="cancel btn btn-info SwalBtn1">' + 'Maybe Later' + '</button>&nbsp;&nbsp;' +
                                    '<button type="button" role="button" class="confirm btn btn-warning SwalBtn3">' + "Dismiss" + '</button></center>'
                            });
                        }, 5000);
                    }


                }




            }

        });

        jQuery(document).on('change', '#hits-duration', function() {


            var self = jQuery(this);
            var duration = self.val();
            if (duration == 'range') {
                jQuery('#duration_area').show();

            } else {
                jQuery('#duration_area').hide();

                jQuery('#visitors_graph_stats').addClass('loader');
                jQuery.ajax({
                    url: ahc_ajax.ajax_url,
                    data: {
                        action: 'ahcfree_get_hits_by_custom_duration',
                        'hits_duration': duration
                    },
                    method: 'post',
                    success: function(res) {
                        if (res) {
                            var data = jQuery.parseJSON(res);

                            var start_date = data.mystart_date;
                            var end_date = data.myend_date;
                            var full_start_date = data.full_start_date;
                            var full_end_date = data.full_end_date;
                            var interval = data.interval;
                            var visitors = JSON.parse(data.visitors_data);
                            var visits = JSON.parse(data.visits_data);

                            drawVisitsLineChart(start_date, end_date, interval, visitors, visits, duration);
                            jQuery('#visitors_graph_stats').removeClass('loader');
                            return false;
                        }
                    }
                });
            }
        });

        jQuery(document).on('change', '#summary_from_dt, #summary_to_dt', function() {
            var self = jQuery(this);
            var duration = jQuery('#summary_from_dt').val() + '#' + self.val();

            if (jQuery('#summary_to_dt').val() != '') {
                jQuery('#visitors_graph_stats').addClass('loader');

                jQuery.ajax({
                    url: ahc_ajax.ajax_url,
                    data: {
                        action: 'ahcfree_get_hits_by_custom_duration',
                        'hits_duration_from': jQuery('#summary_from_dt').val(),
                        'hits_duration_to': jQuery('#summary_to_dt').val(),
                        'hits_duration': 'range'
                    },
                    method: 'post',
                    success: function(res) {
                        if (res) {
                            var data = jQuery.parseJSON(res);
                            //console.log(data);
                            var start_date = data.full_start_date;
                            var end_date = data.full_end_date;
                            var full_start_date = data.full_start_date;
                            var full_end_date = data.full_end_date;
                            var interval = data.interval;
                            var visitors = JSON.parse(data.visitors_data);
                            var visits = JSON.parse(data.visits_data);
                            // console.log(visitors);
                            // console.log(visits);
                            drawVisitsLineChart(start_date, end_date, interval, visitors, visits, 'range');
                            jQuery('#visitors_graph_stats').removeClass('loader');
                            return false;
                        }
                    }
                });
            }
        });

        document.getElementById('today_visitors_box').innerHTML = (document.getElementById('today_visitors').innerHTML);
        //document.getElementById('today_visitors_detail_cnt').innerHTML = (document.getElementById('today_visitors').innerHTML);
        document.getElementById('today_visits_box').innerHTML = (document.getElementById('today_visits').innerHTML);
        document.getElementById('today_search_box').innerHTML = (document.getElementById('today_search').innerHTML);
    </script>