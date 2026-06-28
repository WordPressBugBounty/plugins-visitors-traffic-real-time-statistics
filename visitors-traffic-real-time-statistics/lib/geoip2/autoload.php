<?php

/**
 * Minimal PSR-4 autoloader for the vendored MaxMind GeoIp2 + MaxMind\Db
 * libraries. No Composer is required.
 *
 * Only the namespaces GeoIp2\ and MaxMind\ (under this directory) are handled,
 * so this autoloader never interferes with anything else on the site.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('ahcfree_geoip2_autoloader_registered')) {

    function ahcfree_geoip2_autoloader_registered()
    {
        return true;
    }

    spl_autoload_register(function ($class) {
        // Namespace prefixes handled by this autoloader -> their base dir.
        static $prefixes = null;

        if ($prefixes === null) {
            $base = __DIR__;
            $prefixes = array(
                'GeoIp2\\'  => $base . '/GeoIp2/',
                'MaxMind\\' => $base . '/MaxMind/',
            );
        }

        foreach ($prefixes as $prefix => $base_dir) {
            $len = strlen($prefix);
            if (strncmp($class, $prefix, $len) !== 0) {
                continue;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
            return;
        }
    });
}
