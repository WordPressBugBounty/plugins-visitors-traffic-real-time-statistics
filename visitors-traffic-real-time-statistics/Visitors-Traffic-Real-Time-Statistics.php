<?php
/*
Plugin Name: Visitor Traffic Real Time Statistics
Description: Hits counter that shows analytical numbers of your WordPress site visitors and hits.
Author: wp-buy
Author URI: https://www.wp-buy.com/
Version: 8.3
Text Domain: visitors-traffic-real-time-statistics
Domain Path: /languages
*/


define('AHCFREE_PLUGIN_MAIN_FILE', __FILE__);
define('AHCFREE_PLUGIN_ROOT_DIR', dirname(__FILE__));

require_once(AHCFREE_PLUGIN_ROOT_DIR . "/functions.php");
require_once(AHCFREE_PLUGIN_ROOT_DIR . "/init.php");
require_once(AHCFREE_PLUGIN_ROOT_DIR . "/includes/admin-columns.php");

if (!function_exists('get_plugin_data') or !function_exists('wp_get_current_user')) {
	include_once(ABSPATH . 'wp-includes/pluggable.php');
}


/**
 * Set default timezone on plugin activation.
 */
// Hook to set default timezone on plugin activation
// Hook the function to plugins_loaded

add_action('plugins_loaded', 'ahcfree_set_default_timezone_and_send_request');

function ahcfree_set_default_timezone_and_send_request()
{
	// Check if this is the first time the plugin is loaded
	$is_first_time = get_option('ahcfree_first_time_setup', '1'); // Default to true if not set

	if ($is_first_time === '1') {
		// Set the site's timezone
		$wp_timezone_string = get_option('timezone_string');
		$gmt_offset = get_option('gmt_offset');

		if (empty($wp_timezone_string)) {
			// If timezone_string is empty, fallback to GMT offset
			$wp_timezone_string = timezone_name_from_abbr('', $gmt_offset * 3600, 0);

			// If the GMT offset is invalid or cannot be converted, fallback to UTC
			if ($wp_timezone_string === false) {
				$wp_timezone_string = 'UTC';
			}
		}

		// Save the custom timezone to the database
		update_option('ahcfree_custom_timezone', $wp_timezone_string);

		// Trigger the first-time page visit tracking
		ahcfree_send_first_visit_request();

		// Mark the first-time setup as completed
		update_option('ahcfree_first_time_setup', '0');
	}
}

// Function to trigger first visit tracking
function ahcfree_send_first_visit_request()
{
	error_log('Sending first visit request...');
?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var referer = document.referrer;
			var useragent = window.navigator.userAgent;
			var servername = location.hostname;
			var hostname = location.host;
			var request_uri = location.pathname.substring(1);

			var xhttp = new XMLHttpRequest();
			xhttp.open("POST", "<?php echo admin_url('admin-ajax.php'); ?>", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("action=ahcfree_track_visitor&page_id=1&page_title=First Visit&post_type=page&referer=" + referer + "&useragent=" + useragent + "&servername=" + servername + "&hostname=" + hostname + "&request_uri=" + request_uri);
		});
	</script>
	<?php
}

add_action('plugins_loaded', 'ahcfree_init');
add_action('plugins_loaded', 'ahcfree_multisite_init', 99);
//if ( function_exists('get_plugin_data') ) {
//	$woodhl_detail = get_plugin_data( __FILE__ );
//	$installed_version = get_option( 'visitors-traffic-real-time-statistics-pro-version' );
//	if( $installed_version != $woodhl_detail['Version'] ){
//		add_action( 'plugins_loaded', 'ahcfree_init' );
//
//		update_option( 'visitors-traffic-real-time-statistics-pro-version', $woodhl_detail['Version'] );
//	}
//}
/*
$ahcfree_GetWPTimezoneString = isset(ahcfree_GetWPTimezoneString()) ? ahcfree_GetWPTimezoneString() : get_option( 'timezone_string' );

if(empty($ahcfree_GetWPTimezoneString))
{
	date_default_timezone_set(ahcfree_GetWPTimezoneString());
}
*/

function ahcfree_HideMessageAjaxFunction()
{
	add_option('ahcfree_upgrade_message', 'yes');
}


function ahcfree_after_plugin_row($plugin_file, $plugin_data, $status)
{

	if (get_option('ahcfree_upgrade_message') != 'yes') {
		$class_name = $plugin_data['slug'];
	?>
		<tr id="<?php echo $class_name ?>-plugin-update-tr" class="plugin-update-tr active">
			<td colspan="4" class="plugin-update">
				<div id="<?php echo $class_name; ?>-upgradeMsg" class="update-message" style="background:#FFF8E5; padding-left:10px; border-left:#FFB900 solid 4px">

					You are running visitor traffic free. To get more features (<b style="color:red">Online users, GEO locations and visitors on the map</b>), you can <a href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/#plugins-page" target="_blank"><strong>upgrade now</strong></a> or

					<span id="HideMe" style="cursor:pointer"><a href="javascript:void(0)"><strong>dismiss</strong></a> this message</span>
				</div>
			</td>
		</tr>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				var row = jQuery('#<?php echo esc_js($class_name); ?>-plugin-update-tr').closest('tr').prev();
				jQuery(row).addClass('update');


				jQuery("#HideMe").click(function() {

					jQuery("#<?php echo esc_js($class_name); ?>-upgradeMsg").empty();
					jQuery("#<?php echo esc_js($class_name); ?>-upgradeMsg").removeAttr("style");

					localStorage.setItem("vtrts_hide_upgrade_message", "hide_msg");


				});

				if (localStorage.getItem("vtrts_hide_upgrade_message") == "hide_msg") {

					jQuery("#<?php echo esc_js($class_name); ?>-upgradeMsg").empty();
					jQuery("#<?php echo esc_js($class_name); ?>-upgradeMsg").removeAttr("style");
				}

			});
		</script>


<?php

	}
}

function ahcfree_action_links($links)
{
	$links = array_merge(array(
		'<a href="' . esc_url(admin_url('/admin.php?page=ahc_hits_counter_menu_free')) . '">' . __('Dashboard', 'textdomain') . '</a>',
		'<a href="' . esc_url(admin_url('/admin.php?page=ahc_hits_counter_settings')) . '">' . __('Settings', 'textdomain') . '</a>'
	), $links);
	return $links;
}

function ahcfree_row_meta($meta_fields, $file)
{

	if (strpos($file, 'Visitors-Traffic-Real-Time-Statistics.php') == false) {

		return $meta_fields;
	}

	echo "<style>.pluginrows-rate-stars { display: inline-block; color: #ffb900; position: relative; top: 3px; }.pluginrows-rate-stars svg{ fill:#ffb900; } .pluginrows-rate-stars svg:hover{ fill:#ffb900 } .pluginrows-rate-stars svg:hover ~ svg{ fill:none; } </style>";

	$plugin_rate   = "https://wordpress.org/support/plugin/visitors-traffic-real-time-statistics/reviews/?rate=5#new-post";
	$plugin_filter = "https://wordpress.org/support/plugin/visitors-traffic-real-time-statistics/reviews/?filter=5";
	$svg_xmlns     = "https://www.w3.org/2000/svg";
	$svg_icon      = '';

	for ($i = 0; $i < 5; $i++) {
		$svg_icon .= "<svg xmlns='" . esc_url($svg_xmlns) . "' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>";
	}

	$meta_fields[] = '<a href="' . esc_url($plugin_filter) . '" target="_blank"><span class="dashicons dashicons-thumbs-up"></span>' . __('Vote!', 'pluginrows') . '</a>';
	$meta_fields[] = "<a href='" . esc_url($plugin_rate) . "' target='_blank' title='" . esc_html__('Rate', 'pluginrows') . "'><i class='pluginrows-rate-stars'>" . $svg_icon . "</i></a>";

	return $meta_fields;
}


$path = plugin_basename(__FILE__);
add_action("after_plugin_row_{$path}", 'ahcfree_after_plugin_row', 10, 3);
add_action('wp_ajax_ahcfree_HideMessageAjaxFunction', 'ahcfree_HideMessageAjaxFunction');
add_filter('plugin_row_meta', 'ahcfree_row_meta', 10, 4);
add_action('plugin_action_links_' . $path, 'ahcfree_action_links');
add_option('ahcfree_new_updates', date('Y-m-d', time()));



if (get_option('ahcfree_hide_top_bar_icon') != '1') {
	add_action('wp_footer', 'vtrts_free_adminbar_chart');
	add_action('admin_footer', 'vtrts_free_adminbar_chart');
	add_action('admin_bar_menu', 'vtrts_free_add_items', 100);
	add_action('wp_enqueue_scripts', 'vtrts_free_top_bar_enqueue_style');
	add_action('admin_enqueue_scripts', 'vtrts_free_top_bar_enqueue_style');
	add_action('wp_enqueue_scripts', 'enqueue_canvasjs_free');
	add_action('admin_enqueue_scripts', 'enqueue_canvasjs_free');
}
//--------------------------------------------

add_action('admin_menu', 'ahcfree_add_external_links_as_submenu');

function ahcfree_add_external_links_as_submenu()
{

	if (current_user_can('manage_options')) {
		global $submenu;
		$menu_slug = "ahc_hits_counter_menu_free"; // used as "key" in menus

		$search_url = "plugin-install.php?s=wp-buy&tab=search&type=author";
		if (is_multisite()) $search_url = "network/plugin-install.php?s=wp-buy&tab=search&type=author";

		$submenu[$menu_slug][] = array('<span style="color:#f18500">' . __('More Plugins', 'MorePlugins') . '</span>', 'manage_options', admin_url($search_url));
	}
}
?>