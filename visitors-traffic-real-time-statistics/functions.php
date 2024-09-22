<?php
add_shortcode( 'ahc_stats_widget',  'ahc_stats_widget_func');
//[ahc_stats_widget title="" fontsize="16" fonttype="" display_today_visitors=true display_today_pageviwes=true display_total_visitors=true display_total_pageviwes=true ]

function ahc_stats_widget_func( $instance = [] ) 
	{
	
	 $args = shortcode_atts( array(
     
            'fontsize' => '14',
		 	'title' => '',
		    'fonttype' => '',
            'display_today_visitors' => "true",
            'display_today_pageviwes' => "true",
		    'display_total_visitors' => "true",
		    'display_total_pageviwes' => "true"
		 
 
        ), $instance );
	
		
		$ret  = '';
		

        $ahc_sum_stats = ahcfree_get_summary_statistics();
		$ret .= isset($args['title']) ? '<h3 class="ahc_stats_widget_title">'.esc_html( $args['title']).'</h3>' : '';
	
        
            // This is where you run the code and display the output
            $ret .= '<ul class="ahc_stats_widget" style="list-style:none; font-family: '.esc_attr($args['fonttype']).' !important; font-size:' . esc_attr($args['fontsize']) . 'px !important">';

            if ($args['display_today_visitors'] != 'false' && $args['display_today_visitors']  != false) {
                $ret .= '<li><b>'.__("Today's visitors:", 'visitors-traffic-real-time-statistics') .'</b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['today']['visitors'])) . '</span></li>';
            }
             if ($args['display_today_pageviwes']  != 'false' && $args['display_today_pageviwes']  != false) {
                $ret .= '<li><b>'.__("Today's page views", 'visitors-traffic-real-time-statistics') .' </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['today']['visits'])) . '</span></li>';
            }

             if ($args['display_total_visitors'] !='false' && $args['display_total_visitors'] !=false) {
                $ret .= '<li><b>'.__("Total visitors", 'visitors-traffic-real-time-statistics') .' </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['total']['visitors'])) . '</span></li>';
            }

              if ($args['display_total_pageviwes'] !='false' && $args['display_total_visitors'] !=false) {
                $ret .= '<li><b>'.__("Total page views", 'visitors-traffic-real-time-statistics') .' </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['total']['visits'])) . '</span></li>';
            }


            $ret .= '</ul>';
        

      return  $ret;
		
	}
	

add_shortcode( 'ahc_today_visitors',  'ahc_today_visitors_func');

	function ahc_today_visitors_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('today');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visitors']));
      
		
	}
	

add_shortcode( 'ahc_today_visits',  'ahc_today_visits_func');

	function ahc_today_visits_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('today');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visits']));
      
		
	}
	
add_shortcode( 'ahc_total_visitors',  'ahc_total_visitors_func');

	function ahc_total_visitors_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('total');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visitors']));
      
		
	}

add_shortcode( 'ahc_total_visits',  'ahc_total_visits_func');

	function ahc_total_visits_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('total');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visits']));
      
		
	}


add_shortcode( 'ahc_yesterday_total_visits',  'ahc_yesterday_total_visits_func');

	function ahc_yesterday_total_visits_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('yesterday');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visits']));
      
		
	}
add_shortcode( 'ahc_yesterday_total_visitors',  'ahc_yesterday_total_visitors_func');

	function ahc_yesterday_total_visitors_func() 
	{
	
        $ahc_sum_stats = ahcfree_get_visitors_visits_in_period('yesterday');
		return ahcfree_NumFormat(intval($ahc_sum_stats['visitors']));
      
		
	}	
	
function vtrts_free_top_bar_enqueue_style()
{

?>
    <style>
        #wpadminbar #wp-admin-bar-vtrts_free_top_button .ab-icon:before {
            content: "\f185";
            color: #1DAE22;
            top: 3px;
        }
    </style>
    <?php

}

/**
 * Recursive sanitation for text or array
 * 
 * @param $array_or_string (array|string)
 * @since  0.1
 * @return mixed
 */
function ahc_free_sanitize_text_or_array_field($array_or_string)
{
    if (is_string($array_or_string)) {
        $array_or_string = sanitize_text_field($array_or_string);
    } elseif (is_array($array_or_string)) {
        foreach ($array_or_string as $key => &$value) {
            if (is_array($value)) {
                $value = ahc_free_sanitize_text_or_array_field($value);
            } else {
                $value = sanitize_text_field($value);
            }
        }
    }

    return $array_or_string;
}

function vtrts_free_add_items($admin_bar)
{
    if (!current_user_can('manage_options')) {
        return;
    }
    global $pluginsurl;
    //The properties of the new item. Read More about the missing 'parent' parameter below
    $args = array(
        'id'    => 'vtrts_free_top_button',
        'parent' => null,
        'group'  => null,
        'title' => '<span class="ab-icon"></span>' . '' . __('Stats', 'visitors-traffic-real-time-statistics'),
        'href'  => admin_url('admin.php?page=ahc_hits_counter_menu_free'),
        'meta'  => array(
            'title' => __('visitor traffic real-time statistics', 'visitors-traffic-real-time-statistics'), //This title will show on hover
            'class' => ''
        )
    );

    //This is where the magic works.
    $admin_bar->add_menu($args);
}


/**
 * Called when plugin is activated or upgraded
 *
 * @uses add_option()
 * @uses get_option()
 *
 * @return void
 */


function ahcfree_localtime($dateformat)
{
    if (function_exists('date_i18n')) {

        return date_i18n($dateformat);
    } else {
        return gmdate($dateformat);
    }
}



function vtrts_free_remove_dokan_js_files() {
    if(isset($_GET['page']) && $_GET['page'] == 'ahc_hits_counter_menu_free'){
        wp_dequeue_script( 'dokan-tinymce-plugin' );
        wp_deregister_script( 'dokan-tinymce-plugin' );
        wp_dequeue_script( 'dokan-moment' );
        wp_deregister_script( 'dokan-moment' );
        wp_dequeue_script( 'dokan-chart' );
        wp_deregister_script( 'dokan-chart' );
        wp_dequeue_script( 'vue-vendor' );
        wp_deregister_script( 'vue-vendor' );
        wp_dequeue_script( 'dokan-promo-notice-js' );
        wp_deregister_script( 'dokan-promo-notice-js' );
        wp_dequeue_script( 'dps-custom-admin-js' );
        wp_deregister_script( 'dps-custom-admin-js' );
    }
}
add_action( 'admin_print_scripts', 'vtrts_free_remove_dokan_js_files', 1 );


function ahcfree_getVisitsTime($site_id='')
{
    if($site_id == ''){
        $site_id = get_current_blog_id();
    }
    global $wpdb;
    $table_exist = ahcfree_check_table_exists('ahc_visits_time');
    if ($table_exist) {
        if (!ahcfree_check_table_column_exists('ahc_visits_time', 'site_id')) {
            ahcfree_multisite_init();
        }
        $result = $wpdb->get_results("SELECT COUNT(  `vtm_id` ) cnt FROM ahc_visits_time where site_id =" . $site_id, OBJECT);
        if ($result !== false) {
            return $result[0]->cnt;
        }
    }
    return false;
}


/**
 * change plugin settings
 * @return void
 */

function ahcfree_savesettings()
{

    global $wpdb;

    $ahcproUserRoles_ = '';
    $ahcproExcludeRoles_  = '';
    $verify = isset($_POST['ahc_settings_send']) ? wp_verify_nonce(sanitize_text_field($_POST['ahc_settings_send']), 'ahc_settings_action') : false;

    if ($verify && current_user_can('manage_options')) {

        $set_hits_days = intval($_POST['set_hits_days']);
        $set_ajax_check = intval($_POST['set_ajax_check']);
        $posts_type = '';
        $set_ips = ahc_free_sanitize_text_or_array_field($_POST['set_ips']);

        $set_google_map = '';
        $ahcfree_hide_top_bar_icon = isset($_POST['ahcfree_hide_top_bar_icon']) ? intval($_POST['ahcfree_hide_top_bar_icon']) : '0';
        $ahcfree_ahcfree_haships = isset($_POST['ahcfree_ahcfree_haships']) ? intval($_POST['ahcfree_ahcfree_haships']) : '0';
        $delete_plugin_data = isset($_POST['delete_plugin_data']) ? intval($_POST['delete_plugin_data']) : '';

        $custom_timezone_offset = ahc_free_sanitize_text_or_array_field($_POST['set_custom_timezone']);
        if ($custom_timezone_offset && $custom_timezone_offset != '') {
            update_option('ahcfree_custom_timezone', $custom_timezone_offset);
        }

        $delete_plugin_data = (isset($delete_plugin_data)) ? intval($delete_plugin_data) : 0;
        update_option('ahcfree_ahcfree_haships', $ahcfree_ahcfree_haships);
        update_option('ahcfree_delete_plugin_data_on_uninstall', $delete_plugin_data);
        update_option('ahcfree_hide_top_bar_icon', $ahcfree_hide_top_bar_icon);

		$ahcproExcludeRoles = (isset($_POST['ahcproExcludeRoles']) && is_array($_POST['ahcproExcludeRoles'])) ? ahc_free_sanitize_text_or_array_field($_POST['ahcproExcludeRoles']) : ''; // sanitize inside the loop
        
        if (isset($ahcproExcludeRoles) && is_array($ahcproExcludeRoles)) {
            foreach ($ahcproExcludeRoles as $v) {

                $ahcproExcludeRoles_ .= $v . ",";
            }

            $ahcproExcludeRoles_ = substr($ahcproExcludeRoles_, 0, -1);
			
            update_option('ahcproExcludeRoles', $ahcproExcludeRoles_);
        }
	
	
        $ahcproUserRoles = (isset($_POST['ahcproUserRoles']) && is_array($_POST['ahcproUserRoles'])) ? ahc_free_sanitize_text_or_array_field($_POST['ahcproUserRoles']) : ''; // sanitize inside the loop
        if (isset($ahcproUserRoles)) {
            foreach ($ahcproUserRoles as $v) {

                $ahcproUserRoles_ .= $v . ",";
            }

            $ahcproUserRoles_ = substr($ahcproUserRoles_, 0, -1);
            update_option('ahcproUserRoles', $ahcproUserRoles_);
        }

        ahcfree_update_tables();
        $post_id = $wpdb->get_results("SELECT `set_id` FROM `ahc_settings` WHERE `site_id` =".get_current_blog_id());
        if(empty($post_id)){
            $sql = $wpdb->prepare("INSERT INTO `ahc_settings` (`set_id`, `set_hits_days`, `set_ajax_check`, `set_ips`, `set_google_map`, `site_id`) VALUES (NULL, %s, %s, %s, %s ,%s); ", $set_hits_days, $set_ajax_check, $set_ips, $set_google_map,get_current_blog_id());
        }else{
            $sql = $wpdb->prepare(
                "UPDATE `ahc_settings` set `set_hits_days` = %s, `set_ajax_check` = %s, `set_ips` = %s, `set_google_map` = %s  where `site_id`= %d",
                $set_hits_days,
                $set_ajax_check,
                $set_ips,
                $set_google_map,
                get_current_blog_id()
            );
        }

        if ($wpdb->query($sql) !== false) {

            return true;
        }
    }

    return false;
}

//--------------------------------------------
/*
function ahcfree_rate_us($plugin_url, $box_color = '#1D1F21') {

    $ret = '
	
	<script language="javascript">
	setTimeout(function() {
		$(\'#ratingdiv\').hide();
	}, 1000);
	</script>
    
	<style type="text/css">
	
	.rate_box{
		
		background-color:' . esc_js($box_color) . ';
		color:#ffffff;
		padding:3px;
	
		
	}
	.rating {
	  unicode-bidi: bidi-override;
	  direction: rtl;
  
	}
	.link_wp{
		
		color:#EDAE42 !important
	}
	
	.rating > span:hover:before,
	.rating > span:hover ~ span:before {
	   content: "\2605";
	   position: absolute;
	   color:yellow;
	}
	
	.rating > span {
	  display: inline-block;
	  position: relative;
	  width: 1.1em;
	  font-size:40px;
	 color:yellow;
	  content: "\2605";
	}
	</style>';

    $ret .= '<div class="row rate_box" id="ratingdiv">
<div class="col-md-6">
<br />
<p>
<strong>Do you like this plugin?</strong><br /> Please take a few seconds to <a class="link_wp" href="' . esc_url($plugin_url) . '" target="_blank">rate it on WordPress.org!</a></p>
</div>
<div class="col-md-6">
<div class="rating">';

    for ($r = 1; $r <= 5; $r++) {

        $ret .= '<span onclick="window.open(\'' . esc_js($plugin_url) . '\',\'_blank\')">☆</span>';
    }

    $ret .= '</div>
</div>
</div>';
    return $ret;
}*/

function ahcfree_get_save_settings()
{
    global $wpdb;
    $table_exist = ahcfree_check_table_exists('ahc_settings');
    if ($table_exist) {
        if(!ahcfree_check_table_column_exists('ahc_settings', 'site_id')){
            ahcfree_multisite_init();
        }
        $result = $wpdb->get_results("SELECT set_hits_days, set_ajax_check, set_ips, set_google_map FROM ahc_settings where site_id=".get_current_blog_id(), OBJECT);
        if ($result !== false) {
            return $result;
        }
    }

    return false;
}

function ahcfree_get_timezone_string()
{
    $custom_timezone = get_option('ahcfree_custom_timezone');
    if (!$custom_timezone) {
        $wsmTimeZone = get_option('timezone_string');
        if (is_null($wsmTimeZone) || $wsmTimeZone == '') {
            $wsmTimeZone = ahcfree_GetWPTimezoneString();
        }
        $custom_timezone = ahcfree_CleanupTimeZoneString($wsmTimeZone);

        /*
		$custom_timezone = get_option( 'timezone_string' );

        if ( ! empty( $custom_timezone ) ) {
            return $custom_timezone;
        }

        $offset  = get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - floor( $offset ) ) * 60;
        if( $hours < 10 ){
			$hours	= '0'+$hours;
		}
        echo $custom_timezone  = sprintf( '%s:%s', $hours, $minutes );*/
    }
    return $custom_timezone;
}


function ahcfree_CleanupTimeZoneString($tzString)
{
    $time = new DateTime('now', new DateTimeZone($tzString));
    return $time->format('P');

    /*$offset=$tzString;
     
     
     if (preg_match('/^UTC[+-]/', $tzString)) {
       $tzString= preg_replace('/UTC\+?/', '', $tzString);
    }
    if(is_numeric($tzString)){
        $offset=sprintf('%02d:%02d', (int) $tzString, fmod(abs($tzString), 1) * 60);
        if((int) $tzString>0){
            $offset='+'.$offset;
        }
    }
    return $offset;*/
}


function ahcfree_GetWPTimezoneString()
{
    // if site timezone string exists, return it
    if ($timezone = get_option('timezone_string'))
        return $timezone;

    // get UTC offset, if it isn't set then return UTC
    if (0 === ($utc_offset = get_option('gmt_offset', 0)))
        return 'UTC';

    // adjust UTC offset from hours to seconds
    $utc_offset *= 3600;

    // attempt to guess the timezone string from the UTC offset
    if ($timezone = timezone_name_from_abbr('', $utc_offset, 0)) {
        return $timezone;
    }

    // last try, guess timezone string manually
    $is_dst = date('I');

    foreach (timezone_abbreviations_list() as $abbr) {
        foreach ($abbr as $city) {
            if ($city['dst'] == $is_dst && $city['offset'] == $utc_offset)
                return $city['timezone_id'];
        }
    }

    // fallback to UTC
    return 'UTC';
}

function ahcfree_get_current_timezone_offset()
{
    $tz = ahcfree_get_timezone_string();
    try {
        $timeZone = new DateTimeZone($tz);
        $date = new DateTime('now', $timeZone);
        $date->setTimezone($timeZone);
    } catch (Exception $e) {
        $date = new DateTime('now');
    }
    return $date->format('P');
}

function ahcfree_last_hit_date()
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $sql = "SELECT max(CONVERT_TZ(vtr_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) as last_date FROM ahc_recent_visitors where site_id =".get_current_blog_id();
    //echo $sql = "SELECT max(vtr_date)) as last_date FROM ahc_recent_visitors";
    $result = $wpdb->get_results($sql, OBJECT);
    if ($result !== false) {
        return $result[0]->last_date;
    }
    return ahcfree_localtime('Y-m-d', time());
}

function ahcfree_getCountriesCount($site_id='')
{
    if($site_id == ''){
        $site_id = get_current_blog_id();
    }
    global $wpdb;
    $table_exist = ahcfree_check_table_exists('ahc_countries');
    if ($table_exist) {
        if (!ahcfree_check_table_column_exists('ahc_countries', 'site_id')) {
            ahcfree_multisite_init();
        }
        $result = $wpdb->get_results("SELECT COUNT(  `ctr_id` ) cnt FROM ahc_countries where site_id = " . $site_id, OBJECT);
        if ($result !== false) {
            return isset($result[0]->cnt)?$result[0]->cnt:0;
        }
    }
    return false;
}

function ahcfree_getBrowsersCount($site_id='')
{
    if($site_id == ''){
        $site_id = get_current_blog_id();
    }
    global $wpdb;
    $table_exist = ahcfree_check_table_exists('ahc_browsers');
    if ($table_exist) {
        if (!ahcfree_check_table_column_exists('ahc_browsers', 'site_id')) {
            ahcfree_multisite_init();
        }
        $result = $wpdb->get_results("SELECT COUNT(  `bsr_id` ) cnt FROM ahc_browsers  where site_id = " . $site_id, OBJECT);
        if ($result !== false) {
            return $result[0]->cnt;
        }
    }
    return false;
}

function ahcfree_getSearchEnginesCount()
{
    global $wpdb;
    $result = $wpdb->get_results("SELECT COUNT(  `srh_id` ) cnt FROM ahc_search_engines", OBJECT);
    if ($result !== false) {
        return $result[0]->cnt;
    }
    return false;
}

function ahcfree_set_default_options()
{


    //if (is_multisite())
     //   die('<b style="color:red">Sorry, This plugin can\'t be activated networkwide :(</b>');


    // plugin activation

    if (is_plugin_active('visitors-traffic-real-time-statistics-pro/visitors-traffic-real-time-statistics-pro.php')) {
        deactivate_plugins('visitors-traffic-real-time-statistics-pro/visitors-traffic-real-time-statistics-pro.php');
    }
    require_once("database_basics_data.php");
    if (get_option('ahcfree_wp_hits_counter_options') === false) {


        $plugin_options = array();
        $plugin_options['ahc_version'] = '1.0';
        $plugin_options['available_languages'] = array('ar' => 'عربي', 'en' => 'English');
        $plugin_options['ahc_lang'] = 'en';
        $plugin_options['user_roles_to_not_track'] = array('administrator' => true, 'editor' => true, 'author' => true, 'contributor' => true, 'subscriber' => false);
        add_option('ahcfree_wp_hits_counter_options', $plugin_options);
    }
    set_time_limit(300);
    if (ahcfree_create_database_tables()) {

        if(is_multisite()) {
            $get_site_ids = get_sites();
            foreach ($get_site_ids as $row) {
                if (ahcfree_getCountriesCount($row->blog_id) == 0) {
                    ahcfree_insert_countries_into_table($internetCountryCodes, $contriesLatLng,$row->blog_id);
                }
                if (ahcfree_getVisitsTime($row->blog_id) == 0) {
                    ahcfree_insert_visit_times_into_table($dayHours,$row->blog_id);
                }
                if (ahcfree_getBrowsersCount($row->blog_id) == 0) {
                    ahcfree_insert_browsers_into_table($browsers,$row->blog_id);
                }
            }

        }else{
            if (ahcfree_getCountriesCount(1) == 0) {
                ahcfree_insert_countries_into_table($internetCountryCodes, $contriesLatLng,1);
            }
            if (ahcfree_getVisitsTime(1) == 0) {
                ahcfree_insert_visit_times_into_table($dayHours,1);
            }
            if (ahcfree_getBrowsersCount(1) == 0) {
                ahcfree_insert_browsers_into_table($browsers,1);
            }
        }

        if (ahcfree_getSearchEnginesCount() == 0) {
            ahcfree_insert_search_engines_into_table($searchEngines);
        }


    }
}


if(is_multisite()){
    add_action( 'wp_initialize_site', 'ahcfree_action_wp_initialize_site', 900 );
    function ahcfree_action_wp_initialize_site( WP_Site $new_site ){
        $site_id = $new_site->blog_id;
        require_once("database_basics_data.php");
        if (ahcfree_getCountriesCount($site_id) == 0) {
            ahcfree_insert_countries_into_table($internetCountryCodes, $contriesLatLng,$site_id);
        }
        if (ahcfree_getVisitsTime($site_id) == 0) {
            ahcfree_insert_visit_times_into_table($dayHours,$site_id);
        }
        if (ahcfree_getBrowsersCount($site_id) == 0) {
            ahcfree_insert_browsers_into_table($browsers,$site_id);
        }
    }
}
//--------------------------------------------
/**
 * Creates plugin page link in the admin menu
 *
 * @uses add_menu_page()
 * @uses plugins_url()
 *
 * @return void
 */
function ahcfree_create_admin_menu_link()
{

    global $current_user;
    $ahcUserRoles = str_ireplace('Array', '', get_option('ahcproUserRoles'));

    $ahcproUserRole = explode(',', $ahcUserRoles);

    $roles_arr = array();

    foreach ($ahcproUserRole as $k => $v) {

        $roles_arr[] = strtolower($v);
    }

    $current_use_roles_ = $current_user->roles;
   // $current_use_roles_ = strtolower($current_use_roles_[0]);
	$current_use_roles_ = isset($current_use_roles_[1]) ? strtolower($current_use_roles_[1]) : strtolower($current_use_roles_[0]);

    if (!in_array( $current_use_roles_, $roles_arr ) && !current_user_can('manage_options')) {
			return;
			
		}



    add_menu_page('Visitor Traffic Real Time Statistics Free', 'Visitor Traffic', 'read', 'ahc_hits_counter_menu_free', 'ahcfree_create_plugin_overview_page', plugins_url('/images/vtrts.png', AHCFREE_PLUGIN_MAIN_FILE));
    add_submenu_page('ahc_hits_counter_menu_free', 'Settings', 'Settings', 'manage_options', 'ahc_hits_counter_settings', 'ahcfree_create_plugin_settings_page');
    add_submenu_page('ahc_hits_counter_menu_free', 'Contact Support', 'Help', 'manage_options', 'ahc_hits_counter_help', 'ahcfree_create_plugin_help_page');



    $ahcfree_custom_timezone = get_option('ahcfree_custom_timezone', false);
    if (!$ahcfree_custom_timezone) {

        update_option('ahcfree_custom_timezone', get_option('timezone_string'));
        add_action('admin_notices', 'ahcfree_admin_notice_to_set_timezone');
    }
    $page = isset($_GET['page']) ? ahc_free_sanitize_text_or_array_field($_GET['page']) : '';



    if ($page == 'ahc_hits_counter_settings') {
        remove_action('admin_notices', 'ahcfree_admin_notice_to_set_timezone');
    }
}

//--------------------------------------------
/**
 * Format numbers
 *
 * @return number
 */
function ahcfree_NumFormat($num)
{
    if ($num > 1000) {
        return number_format($num, 0, ',', ',');
    } else {
        return $num;
    }
}




function ahcfree_init()
{
    //add_action('wp_ajax_ahcfree_countOnlineusers','ahcfree_countOnlineusers');	
    add_action('wp_ajax_ahcfree_track_visitor', 'ahcfree_track_visitor');
    add_action('wp_ajax_nopriv_ahcfree_track_visitor', 'ahcfree_track_visitor');

    ahcfree_update_tables();
}
add_action('admin_init', 'ahcfree_init');

function ahcfree_enqueue_scripts()
{
    global $post, $wp_query;
    $post_id = "HOMEPAGE";
    $page_title = '';
    $post_type = '';
    if (is_singular() || is_page()) {
        $post_id = $post->ID;
        $page_title = get_the_title($post->ID);
        $post_type = get_post_type($post->ID);
    }
    if (is_home()) {
        $post_id = "BLOGPAGE";
    }
    if (is_archive()) {
        $post_id = get_the_archive_title();
    }
    wp_register_script('ahc_front_js', plugins_url('/js/front.js', AHCFREE_PLUGIN_MAIN_FILE), 'jquery', '', false);
    wp_enqueue_script('ahc_front_js');

    wp_localize_script('ahc_front_js', 'ahc_ajax_front', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'page_id' => $post_id,
        'page_title' => $page_title,
        'post_type' => $post_type
    ));
}
add_action('wp_enqueue_scripts', 'ahcfree_enqueue_scripts', 1);
//--------------------------------------------
/**
 * Creates the main overview page
 *
 * @return void
 */
function ahcfree_create_plugin_overview_page()
{
    require_once(AHCFREE_PLUGIN_ROOT_DIR . AHCFREE_DS . 'lang' . AHCFREE_DS . GlobalsAHC::$lang . '_lang.php');
    include("overview.php");
}

//--------------------------------------------
/**
 * Creates the plugin settings
 *
 * @return void
 */
function ahcfree_create_plugin_settings_page()
{

    require_once(AHCFREE_PLUGIN_ROOT_DIR . AHCFREE_DS . 'lang' . AHCFREE_DS . GlobalsAHC::$lang . '_lang.php');
    include("ahc_settings.php");
}

//--------------------------------------------
/**
 * Creates the plugin help page
 *
 * @return void
 */
function ahcfree_create_plugin_help_page()
{

    require_once(AHCFREE_PLUGIN_ROOT_DIR . AHCFREE_DS . 'lang' . AHCFREE_DS . GlobalsAHC::$lang . '_lang.php');
    include("ahc_help.php");
}

//--------------------------------------------
/**
 * Creates the plugin help page
 *
 * @return void
 */
function ahcfree_create_plugin_about_page()
{

    require_once(AHCFREE_PLUGIN_ROOT_DIR . AHCFREE_DS . 'lang' . AHCFREE_DS . GlobalsAHC::$lang . '_lang.php');
    include("ahc_about.php");
}

//--------------------------------------------
/**
 * Returns links array of available languages
 *
 * @uses get_option()
 * @uses add_query_arg()
 *
 * @return array
 */
function ahcfree_get_change_lang_links()
{
    $plugin_options = get_option('ahcfree_wp_hits_counter_options');
    $links = array();
    $i = 0;
    foreach ($plugin_options['available_languages'] as $key => $value) {
        if (GlobalsAHC::$lang != $key) {
            $links[$i]['name'] = $value;
            $links[$i]['href'] = add_query_arg('ahc_lang', $key);
            $i++;
        }
    }
    unset($plugin_options);
    unset($i);
    return $links;
}

//--------------------------------------------
/**
 * Decides whether or not should track the current visitor
 *
 * @uses is_user_logged_in()
 * @uses WP_User::$roles
 *
 * @return boolean
 */
function ahcfree_should_track_visitor()
{
   global $current_user;
    $allow = true;
	
			
    if (is_user_logged_in()) {
        $user = new WP_User($current_user->ID);
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role) {
				
				$ahcproExcludeRoles = get_option('ahcproExcludeRoles');
				if (isset($ahcproExcludeRoles))
				{
					
					$ahcproExcludeRoles = explode(',',$ahcproExcludeRoles);
					foreach($ahcproExcludeRoles as $k=>$v)
					{
						if (strtolower($v) == strtolower($role) )
						{
							return false;
						}
					}
				}
                /*$found = (isset(GlobalsPro::$plugin_options['user_roles_to_not_track'][$role])) ? GlobalsPro::$plugin_options['user_roles_to_not_track'][$role] : false;
                if ($found) {
                    $allow = false;
                    break;
                }*/
            }
        }
    }
    return true;
}

//--------------------------------------------
/**
 * Returns true if the current user has administrator role
 *
 * @uses is_user_logged_in()
 * @uses WP_User::$roles
 *
 * @return boolean
 */
function ahcfree_has_administrator_role()
{
    global $user_ID;
    $is_admin = false;
    if (is_user_logged_in()) {
        $user = new WP_User($user_ID);
        if (!empty($user->roles) && is_array($user->roles)) {
            foreach ($user->roles as $role) {
                if ($role == 'administrator') {
                    $is_admin = true;
                    break;
                }
            }
        }
    }
    return $is_admin;
}

//--------------------------------------------
/**
 * Check if column exist or not
 *
 * @uses wpdb::query()
 *
 * @return boolean
 */
function ahcfree_check_table_column_exists($table_name, $column_name)
{
    global $wpdb;
    $column = $wpdb->get_row($wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ", DB_NAME, $table_name, $column_name));

    if (!empty($column)) {
        return true;
    }
    return false;
}

//--------------------------------------------
/**
 * Check if index exist or not
 *
 * @uses wpdb::query()
 *
 * @return boolean
 */
function ahcfree_check_column_index_exists($table_name, $column_name)
{
    global $wpdb;

    $column = $wpdb->get_row("show index from " . $table_name . " where column_name='" . $column_name . "' ");

    if (!empty($column)) {

        return true;
    }

    return false;
}


//--------------------------------------------
/**
 * Check if Table exist or not
 *
 * @uses wpdb::query()
 *
 * @return boolean
 */
function ahcfree_check_table_exists($table_name)
{
    global $wpdb;
    $table_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s", DB_NAME, $table_name));

    if (!empty($table_data)) {

        return true;
    }

    return false;
}


//--------------------------------------------
/**
 * Creates database updates plugin tables
 *
 * @uses wpdb::query()
 *
 * @return boolean
 */
function ahcfree_update_tables()
{
    global $wpdb;
    $sqlQueries = array();



    if (!ahcfree_check_column_index_exists('ahc_visitors', 'vst_date')) {

        $sqlQueries[] = "alter table ahc_visitors add index idx_vst_date(vst_date)";
    }

    foreach ($sqlQueries as $sql) {
        $wpdb->query($sql);
    }
}

function ahcfree_add_settings()
{

    global $wpdb;

    $sql_ahc_settings = "CREATE TABLE  IF NOT EXISTS `ahc_settings` (
			  `set_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `set_hits_days` int(10) unsigned NOT NULL DEFAULT '14',
			  `set_ajax_check` int(10) unsigned NOT NULL DEFAULT '10',
			  `set_ips` text DEFAULT NULL,
			  `set_google_map` varchar(100) NOT NULL DEFAULT 'today_visitors',
			  `site_id` INT(11) NOT NULL DEFAULT '1',
			  
			  PRIMARY KEY (`set_id`)
			)  DEFAULT CHARSET=utf8";

    $wpdb->query($sql_ahc_settings);


  //  $sql1 = "truncate table `ahc_settings`";
  //  $wpdb->query($sql1);

    $sql = "insert ignore  into `ahc_settings` ( set_hits_days, set_ajax_check, set_ips, set_google_map,site_id) values (14, 15, null, 'today_visitors',".get_current_blog_id().")";


    if ($wpdb->query($sql) === false) {
        //return false;
    }
    return true;
}

//--------------------------------------------
/**
 * Creates database plugin tables
 *
 * @uses wpdb::query()
 *
 * @return boolean
 */
function ahcfree_create_database_tables()
{
    global $wpdb;
    $sqlQueries = array();

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_online_users`
			(
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(`id`),
			`hit_ip_address` VARCHAR(50) NOT NULL,
			`hit_page_id` VARCHAR(30) NOT NULL,
			`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_hits`
			(
			`hit_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(`hit_id`),
			`hit_ip_address` VARCHAR(50) NOT NULL,
			`hit_user_agent` VARCHAR(200) NOT NULL,
			`hit_request_uri` VARCHAR(200) NULL,
			`hit_page_id` VARCHAR(30) NOT NULL,
			`hit_page_title` VARCHAR(200) NULL,
			`ctr_id` INT(3) UNSIGNED NULL,
			`hit_referer` VARCHAR(300) NULL,
			`hit_referer_site` VARCHAR(100) NULL,
			`srh_id` INT(3) UNSIGNED NULL,
			`hit_search_words` VARCHAR(200) NULL,
			`bsr_id` INT(3) UNSIGNED NOT NULL,
			`hit_date` DATE NOT NULL,
			`hit_time` TIME NOT NULL
			)  DEFAULT CHARSET=utf8";


    $sqlQueries[] = "CREATE TABLE  IF NOT EXISTS `ahc_settings` (
			  `set_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `set_hits_days` int(10) unsigned NOT NULL DEFAULT '14',
			  `set_ajax_check` int(10) unsigned NOT NULL DEFAULT '10',
			  `set_ips` text DEFAULT NULL,
			  `set_google_map` varchar(100) NOT NULL DEFAULT 'today_visitors',
			  
			  PRIMARY KEY (`set_id`)
			)  DEFAULT CHARSET=utf8";





    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_browsers`
			(
			`bsr_id` INT(3) UNSIGNED NOT NULL,
			PRIMARY KEY(`bsr_id`),
			`bsr_name` VARCHAR(100) NOT NULL,
			`bsr_icon` VARCHAR(50),
			`bsr_visits` INT(11) NOT NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_search_engines`
			(
			`srh_id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(`srh_id`),
			`srh_name` VARCHAR(100) NOT NULL,
			`srh_query_parameter` VARCHAR(10) NOT NULL,
			`srh_icon` VARCHAR(50),
			`srh_identifier` VARCHAR(50)
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_search_engine_crawlers`
			(
			`bot_name` VARCHAR(50) NOT NULL,
			`srh_id` INT(3) UNSIGNED NOT NULL
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_countries`
			(
			`ctr_id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY(`ctr_id`),
			`ctr_name` VARCHAR(100) NOT NULL,
			`ctr_internet_code` VARCHAR(5) NOT NULL,
			`ctr_latitude` VARCHAR(30) NULL,
			`ctr_longitude` VARCHAR(30) NULL,
			`ctr_visitors` INT(11) NOT NULL DEFAULT 0,
			`ctr_visits` INT(11) NOT NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";





    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_visitors`
			(
			`vst_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`vst_id`),
			`vst_date` DATE NOT NULL,
			`vst_visitors` INT(11) UNSIGNED NULL DEFAULT 0,
			`vst_visits` INT(11) UNSIGNED NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "ALTER TABLE `ahc_visitors` CHANGE `vst_date` `vst_date` DATETIME NOT NULL";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_daily_visitors_stats`
			(
			`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`id`),
			`vst_date` DATETIME NOT NULL,
			`vst_visitors` INT(11) UNSIGNED NULL DEFAULT 0,
			`vst_visits` INT(11) UNSIGNED NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";


    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_searching_visits`
			(
			`vtsh_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`vtsh_id`),
			`srh_id` INT(3) UNSIGNED NOT NULL,
			`vtsh_date` DATE NOT NULL,
			`vtsh_visits` INT(11) UNSIGNED NOT NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "ALTER TABLE `ahc_searching_visits` CHANGE `vtsh_date` `vtsh_date` DATETIME NOT NULL";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_refering_sites`
			(
			`rfr_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`rfr_id`),
			`rfr_site_name` VARCHAR(100) NOT NULL,
			`rfr_visits` INT(11) UNSIGNED NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";




    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_recent_visitors`
			(
			`vtr_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`vtr_id`),
			`vtr_ip_address` VARCHAR(50) NOT NULL,
			`vtr_referer` VARCHAR(300) NULL,
			`srh_id` INT(3) UNSIGNED NULL,
			`bsr_id` INT(3) UNSIGNED NOT NULL,
			`ctr_id` INT(5) UNSIGNED NULL,
			`vtr_date` DATE NOT NULL,
			`vtr_time` TIME NOT NULL,
			`ahc_city` varchar(230) NULL,
			`ahc_region` varchar(230) NULL
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_keywords`
			(
			`kwd_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`kwd_id`),
			`kwd_ip_address` VARCHAR(50) NOT NULL,
			`kwd_keywords` VARCHAR(200) NOT NULL,
			`kwd_referer` VARCHAR(300) NOT NULL,
			`srh_id` INT(3) UNSIGNED NOT NULL,
			`ctr_id` INT(5) UNSIGNED NULL,
			`bsr_id` INT(3) UNSIGNED NOT NULL,
			`kwd_date` DATE NOT NULL,
			`kwd_time` TIME NOT NULL
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_title_traffic`
			(
			`til_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`til_id`),
			`til_page_id` VARCHAR(30) NOT NULL,
			`til_page_title` VARCHAR(100),
			`til_hits` INT(11) UNSIGNED NOT NULL
			)  DEFAULT CHARSET=utf8";

    $sqlQueries[] = "CREATE TABLE IF NOT EXISTS `ahc_visits_time`
			(
			`vtm_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			PRIMARY KEY (`vtm_id`),
			`vtm_time_from` TIME NOT NULL,
			`vtm_time_to` TIME NOT NULL,
			`vtm_visitors` INT(11) UNSIGNED NOT NULL DEFAULT 0,
			`vtm_visits` INT(11) UNSIGNED NOT NULL DEFAULT 0
			)  DEFAULT CHARSET=utf8";



    foreach ($sqlQueries as $sql) {
        if ($wpdb->query($sql) === false) {
            return false;
        }
    }
    return true;
}
function ahcfree_multisite_init(){

    global $wpdb;
    $sqlQueries = array();
    if (ahcfree_check_table_exists('ahc_browsers') === true && !ahcfree_check_table_column_exists('ahc_browsers', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_browsers` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_countries') === true && !ahcfree_check_table_column_exists('ahc_countries', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_countries` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_daily_visitors_stats') === true && !ahcfree_check_table_column_exists('ahc_daily_visitors_stats', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_daily_visitors_stats` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_hits') === true && !ahcfree_check_table_column_exists('ahc_hits', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_hits` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_keywords') === true && !ahcfree_check_table_column_exists('ahc_keywords', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_keywords` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_online_users') === true && !ahcfree_check_table_column_exists('ahc_online_users', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_online_users` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_recent_visitors') === true && !ahcfree_check_table_column_exists('ahc_recent_visitors', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_recent_visitors` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_refering_sites') === true && !ahcfree_check_table_column_exists('ahc_refering_sites', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_refering_sites` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_searching_visits') === true && !ahcfree_check_table_column_exists('ahc_searching_visits', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_searching_visits` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_search_engine_crawlers') === true && !ahcfree_check_table_column_exists('ahc_search_engine_crawlers', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_search_engine_crawlers` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_settings') === true && !ahcfree_check_table_column_exists('ahc_settings', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_settings` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_title_traffic') === true && !ahcfree_check_table_column_exists('ahc_title_traffic', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_title_traffic` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_visitors') === true && !ahcfree_check_table_column_exists('ahc_visitors', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_visitors` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    if (ahcfree_check_table_exists('ahc_visits_time') === true && !ahcfree_check_table_column_exists('ahc_visits_time', 'site_id')) {
        $sqlQueries[] = "ALTER TABLE `ahc_visits_time` ADD `site_id` INT(11) NOT NULL DEFAULT '1';";
    }
    $sqlQueries[] = "ALTER TABLE `ahc_settings` CHANGE `set_id` `set_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT";

    $sqlQueries[] = "ALTER TABLE `ahc_browsers` CHANGE `bsr_id` `bsr_id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT";
    if (count($sqlQueries)) {
        foreach ($sqlQueries as $sql) {
            $wpdb->query($sql);
        }
    }
}

//--------------------------------------------
/**
 * Inserts countries into ahc_countroes table
 *
 * @uses wpdb::insert()
 *
 * @param array $internetCountryCodes. internet codes and names of countries
 * @param array $contriesLatLng. LatLng of countries
 * @return boolean
 */
function ahcfree_insert_countries_into_table($internetCountryCodes, $contriesLatLng,$site_id=1)
{
    global $wpdb;
    $c = 1;
    $length = count($internetCountryCodes);
    foreach ($internetCountryCodes as $internetCode => $countryName) {
        $ctr_latitude = $ctr_longitude = NULL;
        if (isset($contriesLatLng[$internetCode])) {
            $ctr_latitude = $contriesLatLng[$internetCode][0];
            $ctr_longitude = $contriesLatLng[$internetCode][1];
        }
        $result = $wpdb->insert(
            'ahc_countries',
            array(
                'ctr_name' => $countryName,
                'ctr_internet_code' => $internetCode,
                'ctr_latitude' => $ctr_latitude,
                'ctr_longitude' => $ctr_longitude,
                'site_id' => $site_id
            ),
            array(
                '%s', '%s', '%s', '%s', '%d'
            )
        );
        if ($result === false) {
            return false;
        }
    }
    return true;
}

//--------------------------------------------
/**
 * Inserts search engines into ahc_search_engines table
 *
 * @uses wpdb::insert()
 * @uses wpdb::$insert_id
 *
 * @param array $searchEngines.
 * @return boolean
 */
function ahcfree_insert_search_engines_into_table($searchEngines)
{
    global $wpdb;
    foreach ($searchEngines as $se) {
        $result = $wpdb->insert(
            'ahc_search_engines',
            array(
                'srh_name' => $se['srh_name'],
                'srh_query_parameter' => $se['srh_query_parameter'],
                'srh_icon' => $se['srh_icon'],
                'srh_identifier' => $se['srh_identifier']
            ),
            array(
                '%s', '%s', '%s', '%s'
            )
        );
        if ($result !== false) {
            $srh_id = $wpdb->insert_id;
            if(is_multisite()) {
                $get_site_ids = get_sites();
                foreach ($get_site_ids as $row) {

                    foreach ($se['crawlers'] as $crawler) {
                        $result2 = $wpdb->insert(
                            'ahc_search_engine_crawlers',
                            array(
                                'bot_name' => $crawler,
                                'srh_id' => $srh_id,
                                'site_id' => $row->blog_id
                            ),
                            array(
                                '%s', '%d', '%d'
                            )
                        );
                        if ($result2 === false) {
                            return false;
                        }
                    }
                }
            }else{
                foreach ($se['crawlers'] as $crawler) {
                    $result2 = $wpdb->insert(
                        'ahc_search_engine_crawlers',
                        array(
                            'bot_name' => $crawler,
                            'srh_id' => $srh_id,
                            'site_id' => 1
                        ),
                        array(
                            '%s', '%d', '%d'
                        )
                    );
                    if ($result2 === false) {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }
    return true;
}

//--------------------------------------------
/**
 * Inserts browsers into ahc_browsers table
 *
 * @uses wpdb::insert()
 *
 * @param array $browsers
 * @return boolean
 */
function ahcfree_insert_browsers_into_table($browsers,$site_id=1)
{
    global $wpdb;
    foreach ($browsers as $browser) {
        $result = $wpdb->insert(
            'ahc_browsers',
            array(
                'bsr_id' => $browser['bsr_id'],
                'bsr_name' => $browser['bsr_name'],
                'bsr_icon' => $browser['bsr_icon'],
                'site_id' => $site_id
            ),
            array(
                '%d', '%s', '%s', '%d'
            )
        );
        if ($result === false) {
            return false;
        }
    }
    return true;
}

//--------------------------------------------
/**
 * Inserts periods into ahc_visits_time table
 *
 * @uses wpdb::insert()
 *
 * @param array $dayHours
 * @return boolean
 */
function ahcfree_insert_visit_times_into_table($dayHours,$site_id = 1)
{
    global $wpdb;
    foreach ($dayHours as $t) {
        $result = $wpdb->insert(
            'ahc_visits_time',
            array(
                'vtm_time_from' => $t['vtm_time_from'],
                'vtm_time_to' => $t['vtm_time_to'],
                'vtm_visitors' => 0,
                'site_id' => $site_id
            ),
            array(
                '%s', '%s', '%d', '%d'
            )
        );
        if ($result === false) {
            return false;
        }
    }
    return true;
}

//--------------------------------------------
/**
 * Returns the first and last days of the week of the date you pass
 *
 * @param string $date
 * @param string $format Optional
 * @return array
 */
function ahcfree_get_week_limits($date, $format = 'Y-m-d')
{
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $beginingDay = new DateTime($date);
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    $endingDay = new DateTime($date);
    $date = new DateTime($date);
    /*
    switch ($date->format('w')) {
        case 0: // sun
            //$beginingDay->modify('-1 day');
            $endingDay->modify('+6 day');
            break;

        case 1: // mon
            $beginingDay->modify('-1 day');
            $endingDay->modify('+5 day');
            break;

        case 2: // Tue
            $beginingDay->modify('-2 day');
            $endingDay->modify('+4 day');
            break;

        case 3: // Wed
            $beginingDay->modify('-3 day');
            $endingDay->modify('+3 day');
            break;

        case 4: // Thu
            $beginingDay->modify('-4 day');
            $endingDay->modify('+2 day');
            break;

        case 6: // Fri
            $beginingDay->modify('-5 day');
            $endingDay->modify('+1 day');
            break;
    }*/

    $beginingDay->modify('-6 day');
    //$endingDay->modify();

    $day = ahcfree_localtime('w');

    //$beginingDay->modify('-'.$day.' days');
    //$endingDay->modify('+'.(6-$day).' days');
    return array(0 => $beginingDay->format($format), 1 => $endingDay->format($format));
}

//--------------------------------------------
/**
 * Return summary statistics of visitors and visits
 *
 * @return array
 */
function ahcfree_get_summary_statistics()
{
    $arr = array();
    $arr['today'] = ahcfree_get_visitors_visits_in_period('today');
    $arr['yesterday'] = ahcfree_get_visitors_visits_in_period('yesterday');
    $arr['week'] = ahcfree_get_visitors_visits_in_period('week'); // last 7 days
    $arr['month'] = ahcfree_get_visitors_visits_in_period('month');
    $arr['year'] = ahcfree_get_visitors_visits_in_period('year');
    $arr['total'] = ahcfree_get_visitors_visits_in_period();
    return $arr;
}

//--------------------------------------------
/**
 * Return counts visitors and visits in certain day (today|yesterday), certain period(last week, last month, last year) or total
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @param string $period Optional
 * @return mixed
 */
function ahcfree_get_visitors_visits_in_period($period = 'total')
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    //	echo AHCFREE_SERVER_CURRENT_TIMEZONE;

    $current_date = new DateTime();
    $current_date->setTimezone($custom_timezone);


    $date = new DateTime();
    $date->setTimezone($custom_timezone);


    $sql = "SELECT SUM(vst_visitors) AS vst_visitors, SUM(vst_visits) AS  vst_visits 
			FROM `ahc_visitors` 
			WHERE site_id = ".get_current_blog_id();
    $results = false;
    switch ($period) {
        case 'today':


            $sql .= " AND DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) = '" . ahcfree_localtime('Y-m-d') . "'";

            $results = $wpdb->get_results($sql, OBJECT);
            break;

        case 'yesterday':
            $date->modify('-1 day');
            $sql .= " AND DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) = %s";
            $results = $wpdb->get_results($wpdb->prepare($sql, $date->format('Y-m-d')), OBJECT);
            break;

        case 'week': // last 7 days
            $limits = ahcfree_get_week_limits($date->format('Y-m-d'));

            $custom_timezone_offset = str_ireplace('+', '-', $custom_timezone_offset);

            $sql .= "  AND vst_date >= DATE_FORMAT(CONVERT_TZ('" . $limits[0] . " 23:59:59','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') - INTERVAL 1 DAY
and vst_date < DATE_FORMAT(CONVERT_TZ('" . $limits[1] . " 00:00:00','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') + INTERVAL 1 DAY";



            $results = $wpdb->get_results($sql);

            break;

        case 'month':

            $custom_timezone_offset = str_ireplace('+', '-', $custom_timezone_offset);

            $sql .= "  AND vst_date >= DATE_FORMAT(CONVERT_TZ('" . $date->modify('-30 day')->format('Y-m-d') . " 23:59:59','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') - INTERVAL 1 DAY
and vst_date < DATE_FORMAT(CONVERT_TZ('" . $current_date->format('Y-m-d') . " 00:00:00','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') + INTERVAL 1 DAY";



            $results = $wpdb->get_results($sql);




            break;

        case 'year':


            $custom_timezone_offset = str_ireplace('+', '-', $custom_timezone_offset);

            $sql .= "  AND vst_date >= DATE_FORMAT(CONVERT_TZ('" . $date->modify('-365 day')->format('Y-m-d') . " 23:59:59','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') - INTERVAL 1 DAY
and vst_date < DATE_FORMAT(CONVERT_TZ('" . $current_date->format('Y-m-d') . " 00:00:00','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m-%d %H:%i') + INTERVAL 1 DAY";



            $results = $wpdb->get_results($sql);



            break;

        default:
            $results = $wpdb->get_results($sql, OBJECT);
    }
    //echo $wpdb->last_query.'<br />';
    if ($results !== false) {
        return array(
            'visitors' => (empty($results[0]->vst_visitors) ? 0 : $results[0]->vst_visitors),
            'visits' => (empty($results[0]->vst_visits) ? 0 : $results[0]->vst_visits)
        );
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * Return visits in a period from today 
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return array
 */
function ahcfree_get_visitors_visits_by_date()
{
    global $wpdb;
    $lastDays = AHCFREE_VISITORS_VISITS_LIMIT - 1;
    $response = array();
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    $beginning = new DateTime();
    $beginning->setTimezone($custom_timezone);
    $beginning->modify('-' . $lastDays . ' day');

    $sql = "SELECT DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) as vst_date, vst_visitors, vst_visits 
            FROM ahc_visitors 
            WHERE site_id = ".get_current_blog_id()." and DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";

    $results = $wpdb->get_results($wpdb->prepare($sql, $beginning->format('Y-m-d')), OBJECT);
    if ($results !== false) {
        $response['success'] = true;
        $response['date'] = array();
        for ($i = count($results); $i < $lastDays; $i++) {
            $beginning->modify('+1 day');
            $response['data']['dates'][] = $beginning->format('d/m');
            $response['data']['visitors'][] = 0;
            $response['data']['visits'][] = 0;
        }

        foreach ($results as $r) {
            $hitDate = new DateTime($r->vst_date);
            //$hitDate->setTimezone($custom_timezone);
            $response['data']['dates'][] = $hitDate->format('d/m');
            $response['data']['visitors'][] = $r->vst_visitors;
            $response['data']['visits'][] = $r->vst_visits;
        }
    } else {
        $response['success'] = false;
    }
    return $response;
}

function ahcfree_get_visitors_by_date()
{
    global $wpdb;
    $lastDays = AHCFREE_VISITORS_VISITS_LIMIT;
    $response = array();
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    $beginning = new DateTime();
    $beginning->setTimezone($custom_timezone);
    $beginning->modify('-' . $lastDays . ' day');


    $sql = "SELECT DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) as vst_date, vst_visitors 
            FROM ahc_visitors 
            WHERE site_id = ".get_current_blog_id()." and DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";

    $results = $wpdb->get_results($wpdb->prepare($sql, $beginning->format('Y-m-d')), OBJECT);

    if ($results !== false) {
        for ($i = count($results); $i < $lastDays; $i++) {
            $beginning->modify('+1 day');
            $xx .= "['" . $beginning->format('Y-m-d') . "', 0], ";
        }
        foreach ($results as $r) {

            $hitDate = new DateTime($r->vst_date);
            //$hitDate->setTimezone($custom_timezone);
            $xx .= "['" . $hitDate->format('Y-m-d') . "', " . $r->vst_visitors . "], ";
        }
    }
    return '[' . $xx . ']';
}

function ahcfree_get_visits_by_date()
{
    global $wpdb;
    $lastDays = AHCFREE_VISITORS_VISITS_LIMIT;
    $response = array();
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    $beginning = new DateTime();
    $beginning->setTimezone($custom_timezone);
    $beginning->modify('-' . $lastDays . ' day');

    $sql = "SELECT DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) as vst_date, vst_visits 
            FROM ahc_visitors 
            WHERE site_id = ".get_current_blog_id()." and DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";


    $results = $wpdb->get_results($wpdb->prepare($sql, $beginning->format('Y-m-d')), OBJECT);
    if ($results !== false) {
        for ($i = count($results); $i < $lastDays; $i++) {
            $beginning->modify('+1 day');
            $x .= "['" . $beginning->format('Y-m-d') . "', 0], ";
        }
        foreach ($results as $r) {
            $hitDate = new DateTime($r->vst_date);
            //$hitDate->setTimezone($custom_timezone);
            $x .= "['" . $hitDate->format('Y-m-d') . "', " . $r->vst_visits . "], ";
        }
    }
    return '[' . $x . ']';
}

//--------------------------------------------
/**
 * Return visitors visits that came from search engine in a period from today 
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return array
 */




function ahcfree_get_serch_visits_by_date()
{
    global $wpdb;

    $response = array();
    $sql = "SELECT ase.srh_name, asv.vtsh_date, asv.srh_id, SUM(asv.vtsh_visits) as vtsh_visits FROM `ahc_searching_visits` asv, `ahc_search_engines` ase where asv.site_id = ".get_current_blog_id()." and asv.srh_id = ase.srh_id GROUP by asv.srh_id order by SUM(asv.vtsh_visits) DESC";


    $results = $wpdb->get_results($sql, OBJECT);




    if ($results !== false) {
        $response['success'] = true;
        $data = '';
        $c = 0;
        foreach ($results as $r) {
            $data .= "['" . $r->srh_name . "', " . $r->vtsh_visits . "],";
            $c++;
        }
    } else {
        $data = '';
    }
    $data = substr($data, 0, -1);
    return $data;
}


//--------------------------------------------
/**
 * Returns the total visits by search engines
 *
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_total_visits_by_search_engines()
{
    global $wpdb;
    $result = $wpdb->get_results("SELECT SUM(vtsh_visits) AS total FROM ahc_searching_visits where site_id = ".get_current_blog_id(), OBJECT);
    if ($result !== false) {
        return $result[0]->total;
    }
    return false;
}

//--------------------------------------------
/**
 * Return counts visits happened by search engine result in certain day (today|yesterday), certain period(last week, last month, last year) or total
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @param string $period Optional
 * @return mixed
 */
function ahcfree_get_hits_search_engines_referers($period = 'total')
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    $date = new DateTime();
    $date->setTimezone($custom_timezone);
    $sql = "SELECT ase.srh_name, asv.vtsh_date, asv.srh_id, SUM(asv.vtsh_visits) as vtsh_visits FROM `ahc_searching_visits` asv, `ahc_search_engines` ase where asv.site_id = ".get_current_blog_id()." and asv.srh_id = ase.srh_id ";
    $results = false;
    switch ($period) {
        case 'today':
            $sql .= " AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) = '" . ahcfree_localtime('Y-m-d') . "'";

            $results = $wpdb->get_results($sql, OBJECT);
            break;

        case 'yesterday':
            $date->modify('-1 day');
            $sql .= " AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) = DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";
            $results = $wpdb->get_results($wpdb->prepare($sql, $date->format('Y-m-d')), OBJECT);
            break;

        case 'week':
            $limits = ahcfree_get_week_limits($date->format('Y-m-d'));
            $sql .= " AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) <= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";
            $results = $wpdb->get_results($wpdb->prepare($sql, $limits[0], $limits[1]), OBJECT);
            break;

        case 'month':
            $sql .= " AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ('" . $date->format('Y-m-01') . "', '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) <= DATE(CONVERT_TZ('" . $date->format('Y-m-t') . "', '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";
            $results = $wpdb->get_results($sql, $limits[0], $limits[1], OBJECT);
            break;

        case 'year':
            $sql .= " AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) AND DATE(CONVERT_TZ(asv.vtsh_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) <= DATE(CONVERT_TZ(%s, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'))";
            $results = $wpdb->get_results($wpdb->prepare($sql, $date->format('Y-01-01'), $date->format('Y-12-31')), OBJECT);
            break;

        case 'alltime':

            $sql .= " GROUP by asv.srh_id order by SUM(asv.vtsh_visits) DESC";

            $results = $wpdb->get_results($sql, OBJECT);
            break;

        default:
    }

    $hitsReferers = array();
    if ($results !== false) {
        foreach ($results as $r) {

            $hitsReferers[$r->srh_name] = $r->vtsh_visits;
        }
        return $hitsReferers;
    }
    return false;
}

//--------------------------------------------
/**
 * Retrieves all search engines
 *
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_all_search_engines()
{
    global $wpdb;
    $sql = "SELECT `srh_id`, `srh_name`, `srh_icon` FROM `ahc_search_engines`";
    $searchEngines = array();
    $c = 0;
    $results = $wpdb->get_results($sql, OBJECT);
    if ($results !== false) {
        foreach ($results as $re) {
            $searchEngines[$c]['srh_id'] = $re->srh_id;
            $searchEngines[$c]['srh_name'] = $re->srh_name;
            $searchEngines[$c]['srh_icon'] = $re->srh_icon;
            $c++;
        }
        return $searchEngines;
    }
    return false;
}

//--------------------------------------------
/**
 * Retrieves count of visits order by browsers
 *
 * @uses wpdb::get_results()
 *
 * @return array
 */
function ahcfree_get_browsers_hits_counts()
{
    global $wpdb;
    $sql = "SELECT `bsr_id`, `bsr_name`, `bsr_visits` 
			FROM `ahc_browsers` 
			WHERE site_id = ".get_current_blog_id()." and `bsr_visits` > 0";
    $results = $wpdb->get_results($sql, OBJECT);
    $response = array();
    if ($results !== false) {
        $response['success'] = true;
        $data = '';
        $c = 0;
        foreach ($results as $bsr) {
            $data .= "['" . $bsr->bsr_name . "', " . $bsr->bsr_visits . "],";
            $c++;
        }
    } else {
        $data = '';
    }
    $data = substr($data, 0, -1);
    return $data;
}

//--------------------------------------------
/**
 * Retrieves top referring sites
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_top_refering_sites($start = '', $limit = '')
{
    global $wpdb;
    $limitCond = "";
    if ($start != '' && $limit != '') {
        $sql = "SELECT rfr_site_name, rfr_visits 
			FROM `ahc_refering_sites` 
            where site_id = ".get_current_blog_id()." 
			ORDER BY rfr_visits DESC LIMIT %d, %d ";

        $results = $wpdb->get_results($wpdb->prepare($sql, $start, $limit), OBJECT);
    } else {
        $sql = "SELECT rfr_site_name, rfr_visits 
			FROM `ahc_refering_sites` 
            where site_id = ".get_current_blog_id()." 
			ORDER BY rfr_visits DESC LIMIT 20 ";
        $results = $wpdb->get_results($sql, OBJECT);
    }




    if ($results !== false) {
        $arr = array();
        $c = 0;
        foreach ($results as $referer) {
            $arr[$c]['site_name'] = $referer->rfr_site_name;
            $arr[$c]['total_hits'] = $referer->rfr_visits;
            $c++;
        }
        return $arr;
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * Retrieves countries related to visits
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_top_countries($limit = 0, $start = '', $pagelimit = '', $all = '', $cnt = true)
{
    global $wpdb;
    if ($limit == 0) {
        $limit = AHCFREE_TOP_COUNTRIES_LIMIT;
    }

    if ($cnt == true) {
        $sql = "SELECT count(*) FROM `ahc_countries` WHERE site_id = ".get_current_blog_id()." and  ctr_visits > 0 ORDER BY ctr_visitors DESC";
        $count = $wpdb->get_var($sql);
        return $count;
    }

    $limitCond = "";
    if ($start != '' && $pagelimit != '') {

        $limitCond = " limit " . intval($start) . "," . intval($pagelimit);
    }


    if ($limit > 0 && $pagelimit == "") {
        $sql = "SELECT ctr_name, ctr_internet_code, ctr_visitors, ctr_visits 
		FROM `ahc_countries` WHERE site_id = ".get_current_blog_id()." and  ctr_visits > 0 
		ORDER BY ctr_visitors DESC 
		LIMIT %d OFFSET 0";

        $results = $wpdb->get_results($wpdb->prepare($sql, $limit), OBJECT);
    } else {
        $sql = "SELECT ctr_name, ctr_internet_code, ctr_visitors, ctr_visits 
				FROM `ahc_countries` WHERE site_id = ".get_current_blog_id()." and  ctr_visits > 0 
				ORDER BY ctr_visitors DESC $limitCond";
        $results = $wpdb->get_results($sql, OBJECT);
    }

    $response = array();
    if ($results !== false) {
        $new = array();
        $response['success'] = true;
        $response['data'] = array();
        $c = 0;
        if ($start == "")
            $start = 0;
        $rank = $start + 1;
        foreach ($results as $ctr) {
            $response['data'][$c]['rank'] = $rank;
            $furl = plugins_url('/images/flags/' . strtolower($ctr->ctr_internet_code) . '.png', AHCFREE_PLUGIN_MAIN_FILE);
            $flag = '<img src="' . esc_url($furl) . '" border="0" alt="' . esc_attr($ctr->ctr_name) . '" width="30" height="20" onerror="imgFlagError(this)" />';
            $response['data'][$c]['flag'] = $flag;
            $response['data'][$c]['ctr_name'] = $ctr->ctr_name;
            //$response['data'][$c]['ctr_internet_code'] = $ctr->ctr_internet_code;
            $response['data'][$c]['visitors'] = $ctr->ctr_visitors;
            $response['data'][$c]['visits'] = $ctr->ctr_visits;

            if ($all == 1) {
                $new[$c]['rank'] = $rank;
                $new[$c]['ctr_name'] = $ctr->ctr_name;
                $new[$c]['visitors'] = $ctr->ctr_visitors;
                $new[$c]['visits'] = $ctr->ctr_visits;
            }
            $c++;
            $rank++;
        }
    } else {
        $response['success'] = false;
    }
    if ($all == 1) {
        return $new;
    }
    return $response;
}

//--------------------------------------------
/**
 * Retrieves countries related to visits
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_vsitors_by_country($all, $cnt = true, $start = '', $limit = '', $fdt = '', $tdt = '')
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $vtr_date = "CONVERT_TZ(concat(vtr_date,' ',vtr_time), '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')";
    $cond = "";
    if (isset($_POST['t_from_dt']) && $_POST['t_from_dt'] != '' && isset($_POST['t_to_dt']) && $_POST['t_to_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "traffic_index_country") {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['t_from_dt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_POST['t_to_dt']);
    } else if (isset($_POST['t_from_dt']) && $_POST['t_from_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "traffic_index_country") {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['t_from_dt']);
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $cond = " and vtr_date ='$fromdt'";
    }

    if ($fdt != '' && $tdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $todt = ahcfree_getFormattedDate($tdt, 'yymmdd');
        $cond = "and (DATE($vtr_date) between '" . $fromdt . "' and '$todt')";
        //$cond =" and (vtr_date between '$fromdt' and '$todt')";		
    } else if ($fdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $cond = "and DATE($vtr_date) = '" . $fromdt . "'";
        //$cond =" and vtr_date ='$fromdt'";
    } else {
        $cond = "and DATE(CONVERT_TZ(concat(vtr_date,' ',vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) = '" . ahcfree_localtime('Y-m-d') . "'";
    }

    if ($cnt == true) {
        /*$sql = "select tot.ctr_name, tot.ctr_internet_code, tot.total from (SELECT c.ctr_name, c.ctr_internet_code, count(1) as total FROM ahc_recent_visitors v, ahc_countries c  where v.ctr_id = c.ctr_id  $cond group by ctr_name) as tot order by tot.total desc";	
		$results = $wpdb->get_results($sql, OBJECT);*/
        $sql = $wpdb->prepare("select count(*) as cnt from (SELECT c.ctr_name, c.ctr_internet_code, count(1) as total FROM ahc_recent_visitors v, ahc_countries c  where v.site_id = ".get_current_blog_id()." and  v.ctr_id = c.ctr_id %s group by ctr_name ) as tot order by tot.total desc",  $cond);

        return $wpdb->get_var($sql);
    }

    $limitCond = "";
    if ($start != '' && $limit != '') {
        $limitCond = " limit " . intval($start) . "," . intval($limit);
    }
    if ($all == 1) {
        $limitCond = "";
    }

    $sql = $wpdb->prepare("select tot.ctr_name, tot.ctr_internet_code, tot.total from (SELECT c.ctr_name, c.ctr_internet_code, count(1) as total FROM ahc_recent_visitors v, ahc_countries c  where v.site_id = ".get_current_blog_id()." and  v.ctr_id = c.ctr_id  %s group by ctr_name ) as tot order by tot.total desc %s", $cond, $limitCond);
    $results = $wpdb->get_results($sql, OBJECT);
    //echo $sql;
    if ($results !== false) {
        $arr = array();
        $new = array();
        $c = 0;
        if ($start == "")
            $start = 0;
        $no = $start + 1;
        $sum = 0;
        foreach ($results as $ctr) {

            /*if ($ctr->total > 1) {*/
            $imgurl = plugins_url('/images/flags/' . strtolower($ctr->ctr_internet_code) . '.png', AHCFREE_PLUGIN_MAIN_FILE);
            $arr[$c]['no'] = $no;
            $arr[$c]['country'] = '<img src="' . $imgurl . '" border="0" alt="' . $ctr->ctr_name . '" width="30" height="20" onerror="imgFlagError(this)" />';
            $arr[$c]['ctr_name'] = $ctr->ctr_name;
            $arr[$c]['ctr_internet_code'] = $ctr->ctr_internet_code;
            $arr[$c]['total'] = $ctr->total;

            if ($all == 1) {
                $new[$c]['no'] = $no;
                $new[$c]['ctr_name'] = $ctr->ctr_name;
                $new[$c]['total'] = $ctr->total;
            }

            $c++;
            $no++;


            /*} else {
				
                $sum += 1;
            }*/
        }

        if ($sum > 0) {
            $k = count($arr);
            $arr[$k]['no'] = $no;
            $imgurl = plugins_url('/images/flags/xx.png', AHCFREE_PLUGIN_MAIN_FILE);
            $arr[$k]['country'] = '<img src="' . esc_url($imgurl) . '" border="0" alt="' . esc_url($ctr->ctr_name) . '" width="30" height="20" onerror="imgFlagError(this)" />';
            $arr[$k]['ctr_name'] = 'others';
            $arr[$k]['ctr_internet_code'] = 'XX';
            $arr[$k]['total'] = $sum;

            if ($all == 1) {
                $new[$k]['no'] = $no;
                $new[$k]['ctr_name'] = 'others';
                $new[$k]['total'] = $sum;
            }
        }
        if ($all == 1) {
            return $new;
        }



        return $arr;
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * RHash IP
 *
 */

function ahcfree_haship($ip)
{
    if ($ip != '') {
        $ip = explode('.', $ip);
        return $ip[0] . "." . $ip[1] . "." . $ip[2] . ".***";
    } else {
        return '';
    }
}
//--------------------------------------------
/**
 * Retrieves recent visitors
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */

function ahcfree_get_recent_visitors($all, $cnt = true, $start = '', $limit = '', $fdt = '', $tdt = '', $ip = '')
{
    global $wpdb, $_SERVER;
    $cond = "";
    $cond1 = "";
    $ahcfree_save_ips = get_option('ahcfree_save_ips_opn');

    if (isset($_POST['r_from_dt']) && $_POST['r_from_dt'] != '' && isset($_POST['r_to_dt']) && $_POST['r_to_dt'] != '' && isset($_POST['section']) && $_POST['section'] == 'recent_visitor_by_ip') {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['r_from_dt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_POST['r_to_dt']);
    } else if (isset($_POST['r_from_dt']) && $_POST['r_from_dt'] != '') {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['r_from_dt']);
    }
    if (isset($_POST['ip_addr']) && $_POST['ip_addr'] != '' && isset($_POST['section']) && $_POST['section'] == 'recent_visitor_by_ip') {
        $ip = ahc_free_sanitize_text_or_array_field($_POST['ip_addr']);
    }

    if ($ip != '') {
        $cond .= " and vtr_ip_address='" . $ip . "'";
        $cond1 .= " and vtr_ip_address='" . $ip . "'";
    }

    if ($fdt != '' && $tdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $todt = ahcfree_getFormattedDate($tdt, 'yymmdd');
        $cond .= " having (dt between '$fromdt' and '$todt')";
        $cond1 .= " and (vtr_date between '$fromdt' and '$todt')";
    } else if ($fdt != '' && $tdt = '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $cond .= " and dt ='$fromdt'";
        $cond1 .= " and dt ='$fromdt'";
    }

    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    if ($cnt == true) {
        $sql_query = "SELECT count(*) from (Select DATE_FORMAT(CONVERT_TZ(CONCAT_WS(' ',v.vtr_date,v.vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'), '%Y-%m-%d') as dt FROM `ahc_recent_visitors` AS v LEFT JOIN `ahc_countries` AS c ON v.ctr_id = c.ctr_id LEFT JOIN `ahc_browsers` AS b ON v.bsr_id = b.bsr_id WHERE v.site_id = ".get_current_blog_id()."  and v.vtr_ip_address NOT LIKE 'UNKNOWN%%' $cond  ORDER BY v.vtr_id DESC) as res";
        $count = $wpdb->get_var($sql_query);
        return $count;
    }

    $limitCond = "";
    if ($start != '' && $limit != '') {
        $limitCond = " LIMIT " . intval($start) . ", " . intval($limit);
    }

    if ($all == 1) {
        $limitCond = "";
    }

    $sql_query = "SELECT v.vtr_id, v.vtr_ip_address, v.vtr_referer, DATE_FORMAT(CONVERT_TZ(CONCAT_WS(' ',v.vtr_date,v.vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'), '%Y-%m-%d') as dt ,DATE_FORMAT(CONVERT_TZ(CONCAT_WS(' ',v.vtr_date,v.vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'), '%Y-%m-%d') as vtr_date, DATE_FORMAT(CONVERT_TZ(CONCAT_WS(' ',v.vtr_date,v.vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'), '%H:%i:%s') as vtr_time, v.ahc_city, v.ahc_region,
		c.ctr_name, c.ctr_internet_code, b.bsr_name, b.bsr_icon 
		FROM `ahc_recent_visitors` AS v 
		LEFT JOIN `ahc_countries` AS c ON v.ctr_id = c.ctr_id 
		LEFT JOIN `ahc_browsers` AS b ON v.bsr_id = b.bsr_id 
		WHERE v.site_id = ".get_current_blog_id()." and v.vtr_ip_address NOT LIKE 'UNKNOWN%%' $cond  
		ORDER BY v.vtr_id DESC $limitCond";

    $results = $wpdb->get_results($sql_query);

    if ($results !== false) {
        $arr = array();
        $new = array();
        $c = 0;
        if (is_array($results)) {
            foreach ($results as $hit) {
                if (strlen($hit->vtr_ip_address) < 17) {
                    $visitDate = new DateTime($hit->vtr_date . ' ' . $hit->vtr_time);

                    $arr[$c]['hit_id'] = $hit->vtr_id;
                    $hit_referer = (parse_url($hit->vtr_referer, PHP_URL_HOST) == $_SERVER['SERVER_NAME']) ? '' : rawurldecode($hit->vtr_referer);
                    $hitip = (!empty($hit->hit_referer) ? '<a href="' . esc_url($hit_referer) . '" target="_blank"><img src="' . esc_url(plugins_url('/images/openW.jpg', AHCFREE_PLUGIN_MAIN_FILE)) . '" title="' . esc_attr(ahc_view_referer) . '"></a>' : '');
                    $arr[$c]['hit_ip_address'] = (get_option('ahcfree_ahcfree_haships') != '1') ? $hit->vtr_ip_address . "&nbsp;" . $hitip : ahcfree_haship($hit->vtr_ip_address) . "&nbsp;" . $hitip;
                    $img = "";
                    if ($hit->ctr_internet_code != '') {
                        $imgurl = plugins_url('/images/flags/' . strtolower($hit->ctr_internet_code) . '.png', AHCFREE_PLUGIN_MAIN_FILE);
                        $img = "<img src='" . esc_url($imgurl) . "' border='0' width='22' height='18' title='" . esc_attr($hit->ctr_name) . "' onerror='imgFlagError(this)' />&nbsp;";
                    }

                    // $bimgurl = plugins_url('/images/browsers/' . $hit->bsr_icon, AHCFREE_PLUGIN_MAIN_FILE);
                    // $bimg = '<img src="'.$bimgurl.'" border="0" width="20" height="20" title="'.$hit->bsr_name.'" />&nbsp;';
                    $arr[$c]['hit_date'] = $hit->vtr_date;
                    $arr[$c]['hit_time'] = $hit->vtr_time;

                    $arr[$c]['ctr_internet_code'] = $hit->ctr_internet_code;
                    $arr[$c]['bsr_name'] =  $hit->bsr_name;
                    $arr[$c]['bsr_icon'] = $hit->bsr_icon;

                    if (strpos($hit->ahc_region, '}')) {
                        $arr[$c]['ahc_region'] = "-";
                    } else {
                        $arr[$c]['ahc_region'] = $hit->ahc_region;
                    }

                    if (strpos($hit->ahc_city, 'charset')) {
                        $arr[$c]['ahc_city'] = '-';
                    } else {
                        $arr[$c]['ahc_city'] =  $hit->ahc_city;
                    }

                    $arr[$c]['ctr_name'] = $img . $hit->ctr_name;
                    $arr[$c]['ctr_name'] .= (!empty($hit->ahc_city)) ? ', ' . $hit->ahc_city : '';
                    $arr[$c]['ctr_name'] .= (!empty($hit->ahc_region)) ? ', ' . $hit->ahc_region : '';


                    $arr[$c]['time'] = $visitDate->format('d M Y @ h:i a');

                    if ($all == 1) {
                        $new[$c]['hit_ip_address'] = $hit->vtr_ip_address;
                        $new[$c]['ctr_name'] = $hit->ctr_name . ", " . $hit->ahc_city . ", " . $hit->ahc_region;
                        $new[$c]['time'] = $visitDate->format('d M Y @ h:i a');
                    }

                    $c++;
                }
            }
        }
        if ($all == 1)
            return $new;
        return $arr;
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * Retrieves latest of key words used in search
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_latest_search_key_words_used($all, $cnt = true, $start = '', $limit = '', $fdt = '', $tdt = '')
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $cond = "";
    $cond1 = "";

    if (isset($_POST['from_dt']) && $_POST['from_dt'] != '' && isset($_POST['to_dt']) && $_POST['to_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "lastest_search") {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['from_dt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_POST['to_dt']);
    }

    if ($fdt != '' && $tdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $todt = ahcfree_getFormattedDate($tdt, 'yymmdd');
        //$cond =" and (k.kwd_date between '$fromdt' and '$todt')";	
        $cond = " having (dt between '$fromdt' and '$todt')";
        $cond1 = " and (kwd_date between '$fromdt' and '$todt')";
    }

    if ($cnt == true) {
        $sql = "SELECT count(*) FROM `ahc_keywords` AS k LEFT JOIN `ahc_countries` AS c ON k.ctr_id = c.ctr_id JOIN `ahc_browsers` AS b ON k.bsr_id = b.bsr_id JOIN `ahc_search_engines` AS s on k.srh_id = s.srh_id WHERE k.site_id = ".get_current_blog_id()." and k.kwd_ip_address != 'UNKNOWN' and k.kwd_keywords !='amazon' and c.ctr_id IS NOT NULL $cond1 ORDER BY k.kwd_date DESC, k.kwd_time DESC ";
        $count = $wpdb->get_var($sql);
        return $count;
    }

    $limitCond = "";
    if ($start != '' && $limit != '') {
        $limitCond = " LIMIT " . intval($start) . ", " . intval($limit);
    }

    if ($all == 1) {
        $limitCond = "";
    }

    $sql = "SELECT  k.kwd_date as dt,k.kwd_ip_address, k.kwd_referer, k.kwd_keywords, CONVERT_TZ(CONCAT_WS(' ',k.kwd_date,k.kwd_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') as kwd_date, CONVERT_TZ(k.kwd_time,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') as kwd_time, k.ctr_id, 
		c.ctr_name, c.ctr_internet_code, b.bsr_name, b.bsr_icon, s.srh_name, s.srh_icon 
		FROM `ahc_keywords` AS k 
		LEFT JOIN `ahc_countries` AS c ON k.ctr_id = c.ctr_id 
		JOIN `ahc_browsers` AS b ON k.bsr_id = b.bsr_id 
		JOIN `ahc_search_engines` AS s on k.srh_id = s.srh_id 
		WHERE k.site_id = ".get_current_blog_id()." and k.kwd_ip_address != 'UNKNOWN' and k.kwd_keywords !='amazon' and c.ctr_id IS NOT NULL $cond
		ORDER BY k.kwd_date DESC, k.kwd_time DESC $limitCond";
    $results = $wpdb->get_results($sql, OBJECT);

    if ($results !== false) {
        $arr = array();
        $new = array();
        $c = 0;
        $custom_timezone_offset = ahcfree_get_current_timezone_offset();
        $custom_timezone_string = ahcfree_get_timezone_string();
        $ahcfree_save_ips = get_option('ahcfree_save_ips_opn');
        if ($custom_timezone_string) {
            $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
        }
        foreach ($results as $re) {

            $arr[$c]['hit_referer'] = rawurldecode($re->kwd_referer);
            $arr[$c]['hit_search_words'] = $re->kwd_keywords;
            $arr[$c]['hit_date'] = $re->kwd_date;
            $arr[$c]['hit_time'] = $re->kwd_time;
            $arr[$c]['hit_ip_address'] = $re->kwd_ip_address;
            $arr[$c]['ctr_name'] = $re->ctr_name;
            $arr[$c]['ctr_internet_code'] = $re->ctr_internet_code;
            $arr[$c]['bsr_name'] = $re->bsr_name;
            $arr[$c]['bsr_icon'] = $re->bsr_icon;
            $arr[$c]['srh_name'] = $re->srh_name;
            $arr[$c]['srh_icon'] = $re->srh_icon;

            $img = '<span>';
            if ($re->ctr_internet_code != '') {
                $imgurl = plugins_url('/images/flags/' . strtolower($re->ctr_internet_code) . '.png', AHCFREE_PLUGIN_MAIN_FILE);
                $img .= '<img src="' . esc_url($imgurl) . '" border="0" width="22" height="18" title="' . esc_attr($re->ctr_name) . '" onerror="imgFlagError(this)" />';
            }
            $img .= '&nbsp;' . esc_html($re->ctr_name) . '</span>';
            /*$eurl=plugins_url('/images/search_engines/' . $re->srh_icon, AHCFREE_PLUGIN_MAIN_FILE);
			$img.='<span><img src="'.$eurl.'" border="0" width="22" height="22" title="'.$re->srh_name.'" /></span>';
			
			$burl=plugins_url('/images/browsers/' . $re->bsr_icon, AHCFREE_PLUGIN_MAIN_FILE);
			$img.='<span><img src="'.$burl.'" border="0" width="20" height="20" title="'.$re->bsr_name.'" /></span>';
			*/
            $arr[$c]['img'] = $img;
            $arr[$c]['csb'] = $re->ctr_name . "/" . $re->srh_name . "/" . $re->bsr_name;

            $arr[$c]['keyword'] = '<span class="searchKeyWords"><a href="' . esc_url($re->kwd_referer) . '" target="_blank">' . esc_html($re->kwd_keywords) . '</a></span>';

            $visitDate = new DateTime($re->kwd_date);
            $visitDate->setTimezone($custom_timezone);
            //$arr[$c]['dt'] = '<span class="visitDateTime">'.$visitDate->format('d/m/Y').'</span>';
            $arr[$c]['dt'] = $visitDate->format('d/m/Y');

            if ($all == 1) {
                $new[$c]['csb'] = $re->ctr_name . "/" . $re->srh_name . "/" . $re->bsr_name;
                $new[$c]['keyword'] = $re->kwd_keywords;
                $new[$c]['dt'] = $visitDate->format('d/m/Y');
            }
            $c++;
        }
        if ($all == 1)
            return $new;
        return $arr;
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * Is in login page
 *
 * @return boolean
 */
function ahcfree_is_login_page()
{
    global $GLOBALS;
    // global $GlobalsAHC;

    // return in_array($GlobalsAHC['pagenow'], array('wp-login.php', 'wp-register.php'));

    if ($GLOBALS['pagenow'] === 'wp-login.php' || $GLOBALS['pagenow'] === 'wp-register.php') {
        return true;
    } else {
        return false;
    }
}

//--------------------------------------------
/**
 * Retrieves today visitors data, for google map
 *
 * @uses wpdb::get_results()
 *
 * @return array
 */
function ahcfree_get_today_visitors_for_map($map_status = '')
{
    global $wpdb;
    $whr = '';

    $custom_timezone_offset = ahcfree_get_current_timezone_offset();

    $current_month = ahcfree_localtime("m");
    $current_year = ahcfree_localtime("Y");
    $past_year = ahcfree_localtime("Y") - 1;
    $past_month = date('m', strtotime('last month'));


    if ($map_status == 'this_month') {
        $whr = " and month(CONVERT_TZ(concat(vtr_date, ' ', vtr_time),'+00:00','+02:00')) = " . $current_month . " and year(CONVERT_TZ(concat(vtr_date, ' ', vtr_time),'+00:00','+02:00')) =  " . $current_year;
    } else if ($map_status == 'past_month') {
        $whr = " and month(CONVERT_TZ(concat(vtr_date, ' ', vtr_time),'+00:00','+02:00')) = " . $past_month . " and year(CONVERT_TZ(concat(vtr_date, ' ', vtr_time),'+00:00','+02:00')) =  " . $past_year;
    } else {
        $whr = " and DATE(CONVERT_TZ(concat(vtr_date, ' ', vtr_time),'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) = '" . ahcfree_localtime("Y-m-d") . "'";
    }

    $sql = "select count(vtr_id) as visitors, c.* from `ahc_recent_visitors` recent, `ahc_countries` c where recent.site_id = ".get_current_blog_id()." and recent.ctr_id = c.ctr_id 
			and c.ctr_latitude IS NOT NULL AND c.ctr_latitude <> 0 AND c.ctr_longitude IS NOT NULL AND c.ctr_longitude <> 0 " . $whr . " GROUP by ctr_id";


    $results = $wpdb->get_results($sql, OBJECT);
    $response = array();
    if ($results !== false) {
        $response['success'] = true;
        $response['data'] = array();
        if (is_array($results) && isset($results[0]->visitors) && !empty($results[0]->visitors)) {
            foreach ($results as $r) {
                $response['data'][$r->ctr_id]['visitors'] = $r->visitors;
                $response['data'][$r->ctr_id]['ctr_name'] = $r->ctr_name;
                $response['data'][$r->ctr_id]['ctr_internet_code'] = $r->ctr_internet_code;
                $response['data'][$r->ctr_id]['ctr_latitude'] = $r->ctr_latitude;
                $response['data'][$r->ctr_id]['ctr_longitude'] = $r->ctr_longitude;
            }
        }
    } else {
        $response['success'] = false;
    }
    return $response;
}
function ahcfree_get_all_visitors_for_map()
{
    global $wpdb;
    $sql = "SELECT c.`ctr_visitors` as visitors, c.ctr_id, c.ctr_name, c.ctr_internet_code, c.ctr_latitude, c.ctr_longitude from `ahc_countries` c where c.site_id = ".get_current_blog_id()." and  c.ctr_latitude IS NOT NULL AND c.ctr_latitude <> 0 AND c.ctr_longitude IS NOT NULL AND c.ctr_longitude <> 0 group by `ctr_name` ORDER BY ctr_visitors desc LIMIT 10";

    $results = $wpdb->get_results($sql, OBJECT);
    $response = array();
    if ($results !== false) {
        $response['success'] = true;
        $response['data'] = array();
        if (is_array($results) && isset($results[0]->visitors) && !empty($results[0]->visitors)) {
            foreach ($results as $r) {
                $response['data'][$r->ctr_id]['visitors'] = $r->visitors;
                $response['data'][$r->ctr_id]['ctr_name'] = $r->ctr_name;
                $response['data'][$r->ctr_id]['ctr_internet_code'] = $r->ctr_internet_code;
                $response['data'][$r->ctr_id]['ctr_latitude'] = $r->ctr_latitude;
                $response['data'][$r->ctr_id]['ctr_longitude'] = $r->ctr_longitude;
            }
        }
    } else {
        $response['success'] = false;
    }
    return $response;
}

/**
 * Retrieves online visitors data, for google map
 *
 * @uses wpdb::get_results()
 *
 * @return array
 */
function ahcfree_get_online_visitors_for_map()
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $sql = "SELECT hits.visitors, hits.ctr_id, 
			c.ctr_name, c.ctr_internet_code, c.ctr_latitude, c.ctr_longitude FROM (
			SELECT COUNT(v.visitor) AS visitors, v.ctr_id FROM (
			SELECT ctr_id, 1 AS visitor FROM `ahc_hits`
			WHERE site_id = ".get_current_blog_id()." and ctr_id IS NOT NULL AND hit_ip_address NOT LIKE 'UNKNOWN%' and hit_date = DATE( CONVERT_TZ( '" . ahcfree_localtime("Y-m-d H:i:s") . "' ,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') ) and TIME( CONVERT_TZ(hit_time,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') ) between TIME(CONVERT_TZ('" . date("Y-m-d H:i:s") . "','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') - INTERVAL 60 SECOND) and TIME( CONVERT_TZ('" . date("Y-m-d H:i:s") . "','" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "') )
			GROUP BY hit_ip_address 
			) AS v 
			GROUP BY ctr_id) AS hits 
			JOIN `ahc_countries` AS c ON hits.ctr_id = c.ctr_id 
			WHERE c.site_id = ".get_current_blog_id()." and c.ctr_latitude IS NOT NULL AND c.ctr_latitude <> 0 AND c.ctr_longitude IS NOT NULL AND c.ctr_longitude <> 0 ";

    $results = $wpdb->get_results($sql, OBJECT);
    $response = array();
    if ($results !== false) {
        $response['success'] = true;
        $response['data'] = array();
        if (is_array($results) && isset($results[0]->visitors) && !empty($results[0]->visitors)) {
            foreach ($results as $r) {
                $response['data'][$r->ctr_id]['visitors'] = $r->visitors;
                $response['data'][$r->ctr_id]['ctr_name'] = $r->ctr_name;
                $response['data'][$r->ctr_id]['ctr_internet_code'] = $r->ctr_internet_code;
                $response['data'][$r->ctr_id]['ctr_latitude'] = $r->ctr_latitude;
                $response['data'][$r->ctr_id]['ctr_longitude'] = $r->ctr_longitude;
            }
        }
    } else {
        $response['success'] = false;
    }
    return $response;
}

//--------------------------------------------
/**
 * Detect if the visitor is search engine bot
 *
 * @uses wpdb::get_results()
 *
 * @return boolean
 */
function ahcfree_is_search_engine_bot()
{
    global $wpdb, $_SERVER;
    $results = $wpdb->get_results("SELECT `bot_name` FROM `ahc_search_engine_crawlers` where site_id = ".get_current_blog_id(), OBJECT);
    foreach ($results as $crawler) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $crawler->bot_name) !== false) {
            return true;
        }
    }

    if (stripos($_SERVER['REQUEST_URI'], 'robots.txt') !== false) {
        return true;
    }

    if (stripos($_SERVER['REQUEST_URI'], 'Bot') !== false) {
        return true;
    }

    if (stripos($_SERVER['REQUEST_URI'], 'bot') !== false) {
        return true;
    }
    return false;
}

//--------------------------------------------
/**
 * Detect if the visitor is WordPress bot
 *
 * @return boolean
 */
function ahcfree_is_wordpress_bot()
{
    global $_SERVER;
    if (stripos($_SERVER['HTTP_USER_AGENT'], 'WordPress') !== false) {
        return true;
    }
    return false;
}

//--------------------------------------------
/**
 * Detects post id, post title and post type of current page
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @param object $query. this object is passed to the callback function of "parse_query" hooked action
 * @return mixed
 */
function ahcfree_detect_requested_page($query)
{
    global $wpdb;
    $vars = $query->query_vars;
    if (isset($vars['p']) && !empty($vars['p'])) {
        $result = $wpdb->get_results($wpdb->prepare("SELECT post_title FROM " . $wpdb->prefix . "posts WHERE site_id = %d and ID = %d ", get_current_blog_id(),$vars['p']));
        if ($result !== false && $wpdb->num_rows > 0) {
            return array('page_id' => $vars['p'], 'page_title' => $result[0]->post_title, 'post_type' => 'post');
        }
    } else if (isset($vars['name']) && !empty($vars['name'])) {
        $result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE site_id = %d and post_name = %s ", get_current_blog_id(),$vars['name']));
        if ($result !== false && $wpdb->num_rows > 0) {
            return array('page_id' => $result[0]->ID, 'page_title' => $result[0]->post_title, 'post_type' => 'post');
        }
    } else if (isset($vars['pagename']) && !empty($vars['pagename'])) {
        $result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE site_id = %d and post_name = %s AND post_type = %s", get_current_blog_id(),ahcfree_get_subpage_name($vars['pagename']), 'page'));
        if ($result !== false && $wpdb->num_rows > 0) {
            return array('page_id' => $result[0]->ID, 'page_title' => $result[0]->post_title, 'post_type' => 'page');
        }
    } else if (isset($vars['page_id']) && !empty($vars['page_id'])) {
        $result = $wpdb->get_results($wpdb->prepare("SELECT post_title FROM " . $wpdb->prefix . "posts WHERE site_id = %d and ID = %s AND post_type = %s", get_current_blog_id(),$vars['page_id'], 'page'));
        if ($result !== false && $wpdb->num_rows > 0) {
            return array('page_id' => $page_id, 'page_title' => $result[0]->post_title, 'post_type' => 'page');
        }
    } else {
        return array('page_id' => 'HOMEPAGE', 'page_title' => NULL, 'post_type' => NULL);
    }
}

function ahcfree_get_subpage_name($page_name)
{
    $sub_name = strrchr($page_name, '/');
    if (!$sub_name) {
        return $page_name;
    }
    return substr($sub_name, 1);
}

//--------------------------------------------
/**
 * Initiates tracking process
 *
 * @param object $query. this object is passed to this callback function of "parse_request" hooked action
 * @return void
 */
/*function ahcfree_track_visitor($query) {

    $exclude_ips = AHCFREE_AHCFREE_EXCLUDE_IPS;
    if ($exclude_ips == '' or $exclude_ips == '') {
        $exclude_ips = array();
    }
    if (AHCFREE_AHCFREE_EXCLUDE_IPS != NULL && AHCFREE_AHCFREE_EXCLUDE_IPS != '') {
        $exclude_ips = explode("\n", $exclude_ips);
    }

    if (ahcfree_should_track_visitor() && !ahcfree_is_login_page() && !ahcfree_is_search_engine_bot() && !ahcfree_is_wordpress_bot()) {
        if (!in_array(ahcfree_get_client_ip_address(), $exclude_ips)) {

            $page = ahcfree_detect_requested_page($query);
            if (is_array($page)) {
                GlobalsAHC::$page_id = $page['page_id'];
                GlobalsAHC::$page_title = $page['page_title'];
                GlobalsAHC::$post_type = $page['post_type'];
            } else {
                return;
            }
            $hitsCounter = new WPHitsCounter(GlobalsAHC::$page_id, GlobalsAHC::$page_title, GlobalsAHC::$post_type);
            $hitsCounter->traceVisitorHit();
        }
    }
}*/


function ahcfree_track_visitor()
{
    $exclude_ips_arr = array();
    $exclude_ips = AHCFREE_EXCLUDE_IPS;
    if ($exclude_ips == '' or empty($exclude_ips)) {
        $exclude_ips = array();
    } else {

        $exclude_ips = str_ireplace("\n", " ", $exclude_ips);
        $exclude_ips = str_ireplace(",", " ", $exclude_ips);

        $exclude_ips = explode(" ", $exclude_ips);
    }


    $client_ip_address = trim(ahcfree_get_client_ip_address());
    foreach ($exclude_ips as $k => $v) {

        if ($v != '') {

            $exclude_ips_arr[] = trim($v);
        }
    }



    if (!ahcfree_is_login_page() && !ahcfree_is_search_engine_bot() && !ahcfree_is_wordpress_bot()) {

        if (!in_array($client_ip_address, $exclude_ips_arr)) {


            $page_id = intval($_POST['page_id']);
            $page_title = ahc_free_sanitize_text_or_array_field($_POST['page_title']);
            $post_type = ahc_free_sanitize_text_or_array_field($_POST['post_type']);
            $_SERVER['HTTP_REFERER'] = ahc_free_sanitize_text_or_array_field($_POST['referer']);
            $_SERVER['HTTP_USER_AGENT'] = ahc_free_sanitize_text_or_array_field($_POST['useragent']);
            $_SERVER['SERVER_NAME'] = ahc_free_sanitize_text_or_array_field($_POST['servername']);
            $_SERVER['HTTP_HOST'] = ahc_free_sanitize_text_or_array_field($_POST['hostname']);
            $_SERVER['REQUEST_URI'] = ahc_free_sanitize_text_or_array_field($_POST['request_uri']);

            $hitsCounter = new WPHitsCounter($page_id, $page_title, $post_type);
            $hitsCounter->traceVisitorHit();
        }
    }

    die;
}

//--------------------------------------------
/**
 * Ceil for decimal numbers with precision
 *
 * @param float $number
 * @param integer $precision
 * @param string $separator
 * @return float
 */
function ahcfree_ceil_dec($number, $precision, $separator)
{
    if (strpos($number, '.') !== false) {
        $numberpart = explode($separator, $number);
        $numberpart[1] = substr_replace($numberpart[1], $separator, $precision, 0);
        if ($numberpart[0] >= 0) {
            $numberpart[1] = ceil($numberpart[1]);
        } else {
            $numberpart[1] = floor($numberpart[1]);
        }

        $ceil_number = array($numberpart[0], $numberpart[1]);
        return implode($separator, $ceil_number);
    }
    return $number;
}

//--------------------------------------------
/**
 * Retrieve sum visits by post title
 *
 * @uses wpdb::prepare()
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_traffic_by_title($all, $cnt = false, $start = '0', $limit = '10', $search = '')
{

    global $wpdb;
    $sql2 = '';
    $sql1 = "SELECT SUM(hits) AS sm FROM (
			SELECT SUM(til_hits) AS hits 
			FROM ahc_title_traffic 
			where site_id =".get_current_blog_id()."
			GROUP BY til_page_id
			) myTable";


    $cond = "";
    if ($search != '') {
        $cond = " and til_page_title like '%" . $search . "%'";
    }

    if ($cnt == true) {
        $sql2 = "SELECT til_page_id, til_page_title, til_hits 
			FROM ahc_title_traffic where site_id =".get_current_blog_id()." $cond 
			GROUP BY til_page_id , til_page_title, til_hits
			ORDER BY til_hits DESC limit %d, %d";

        $count = $wpdb->get_results($wpdb->prepare($sql2, $start, $limit));
        return   $wpdb->num_rows;
    }

    $limitCond = "";
    if ($start != '' && $limit != '') {
        $sql2 = "SELECT til_page_id, til_page_title, til_hits 
			FROM ahc_title_traffic where site_id =".get_current_blog_id()." $cond 
			GROUP BY til_page_id , til_page_title, til_hits
			ORDER BY til_hits DESC limit %d, %d";
    }





    $result1 = $wpdb->get_results($sql1);
    if ($result1 !== false && $sql2 != '') {
        $total = $result1[0]->sm;

        $result2 = $wpdb->get_results($wpdb->prepare($sql2, $start, $limit));
        if ($result2 !== false) {
            $arr = array();
            if ($wpdb->num_rows > 0) {
                $c = 0;
                if ($start == "")
                    $start = 0;
                $no = $start;
                foreach ($result2 as $r) {
                    $ans = 0;
                    $arr[$c]['rank'] = $no + 1;
                    //$arr[$c]['til_page_id'] = $r->til_page_id;
                    if ($all == 1)
                        $arr[$c]['til_page_title'] = $r->til_page_title;
                    else
                        $arr[$c]['til_page_title'] = "<a href='" . get_permalink($r->til_page_id) . "' target='_blank'>" . $r->til_page_title . "</a>";
                    $arr[$c]['til_hits'] = $r->til_hits;
                    $ans = ($total > 0) ? ahcfree_ceil_dec((($r->til_hits / $total) * 100), 2, ".")  : 0;
                    $arr[$c]['percent'] = ahcfree_NumFormat($ans) . '%';
                    $c++;
                    $no++;
                }
            }
            return $arr;
        }
    }
    return false;
}

//--------------------------------------------
/**
 * Retrieves sum of visits order by time
 *
 * @uses wpdb::get_results()
 *
 * @return mixed
 */
function ahcfree_get_time_visits($all, $start = '', $limit = '', $fdt = '', $tdt = '')
{
    global $wpdb;
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $vst_date = "CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')";

    $cond = "";

    if (isset($_POST['vfrom_dt']) && $_POST['vfrom_dt'] != '' && isset($_POST['vto_dt']) && $_POST['vto_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "visit_time") {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['vfrom_dt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_POST['vto_dt']);
    } else if (isset($_POST['vfrom_dt']) && $_POST['vfrom_dt'] != '' && isset($_POST['section']) && $_POST['section'] == "visit_time") {
        $fdt = ahc_free_sanitize_text_or_array_field($_POST['vfrom_dt']);
    }
    if ($fdt != '' && $tdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $todt = ahcfree_getFormattedDate($tdt, 'yymmdd');
        $cond = "(DATE($vst_date) between '" . $fromdt . "' and '$todt')";
        $groupby = " hour";
    } else if ($fdt != '') {
        $fromdt = ahcfree_getFormattedDate($fdt, 'yymmdd');
        $cond = "DATE($vst_date) = '" . $fromdt . "'";
        $groupby = " hour($vst_date)";
    } else {
        $cond = "DATE($vst_date) = '" . ahcfree_localtime('Y-m-d') . "'";
        $groupby = " hour($vst_date)";
    }

    $sql1 = "SELECT SUM(vtm_visitors) AS sm FROM ahc_visits_time WHERE site_id = ".get_current_blog_id()." and DATE($vst_date) = '" . ahcfree_localtime('Y-m-d') . "'";


    $sql2 = "SELECT date(vst_date) as dt,hour($vst_date) AS hour, SUM(vst_visitors) AS vst_visitors, SUM(vst_visits) AS vst_visits FROM `ahc_visitors` 
WHERE site_id = ".get_current_blog_id()." and $cond GROUP BY $groupby";

    //echo $sql2;
    //$result1 = $wpdb->get_results($sql1);
    //if ($result1 !== false) {
    $total = 0;
    $result2 = $wpdb->get_results($sql2);
    //asort($result2);
    $utc_data = array();

    if ($result2 !== false) {
        $arr = array();
        $new = array();
        $hourDetails = array();
        foreach ($result2 as $r) {

            if (isset($hourDetails[$r->hour])) {
                $hourDetails[$r->hour]['visitor']    += $r->vst_visitors;
                $hourDetails[$r->hour]['visits']    += $r->vst_visits;
                $hourDetails[$r->hour]['counter'] += 1;
            } else {
                $hourDetails[$r->hour] = array(
                    'visitor'     => $r->vst_visitors,
                    'visits'    => $r->vst_visits,
                    'counter' => 1
                );
            }
            //$dtArr[]= $hourDetails;
            $total += $r->vst_visitors;
        }

        if ($start == '')
            $start = 0;
        if ($limit != '' && $start == 20)
            $end = 24;
        else if ($limit == "")
            $end = 24;
        else
            $end = $limit + $start;

        if ($all == 1) {
            $start = 0;
            $end = 24;
        }
        $k = 0;
        $avgtotal = 0;
        for ($i = $start; $i < $end; $i++) {

            $vtm_visitors = 0;
            $vtm_visits = 0;
            $totalDt =  count($hourDetails);

            if (isset($hourDetails[$i])) {
                $vtm_visitors = $hourDetails[$i]['visitor'] / $hourDetails[$i]['counter'];
                $avgtotal += $vtm_visitors;
                $vtm_visits = $hourDetails[$i]['visits'] / $hourDetails[$i]['counter'];
            }
            if ($i < 10) {
                $timeTo = $timeFrom = '0' . $i;
            } else {
                $timeTo = $timeFrom = $i;
            }
            $arr[$k]['vtm_time_from'] = $timeFrom . ':00';
            $arr[$k]['vtm_time_to'] = $timeTo . ':59';
            // $arr[$k]['percent'] = ($total > 0) ? ahcfree_ceil_dec((($vtm_visitors / $total) * 100), 2, ".") : 0;

            $arr[$k]['time'] = $timeFrom . ':00 - ' . $timeTo . ':59';

            $arr[$k]['vtm_visitors'] = ceil($vtm_visitors);
            $arr[$k]['vtm_visits'] = ceil($vtm_visits);

            if ($all == 1) {
                $new[$k]['time'] = $timeFrom . ':00 - ' . $timeTo . ':59';
                $new[$k]['vtm_visitors'] = ceil($vtm_visitors);
                $new[$k]['vtm_visits'] = ceil($vtm_visits);
            }
            $k++;
        }
        $avgtotal = $total;

        $j = 0;
        for ($i = $start; $i < $end; $i++) {
            if (isset($hourDetails[$i])) {
                $vtm_visitors = $hourDetails[$i]['visitor'] / $hourDetails[$i]['counter'];
            } else {
                $vtm_visitors = 0;
            }

            $arr[$j]['percent'] = ($avgtotal > 0) ? ahcfree_ceil_dec((($vtm_visitors / $total) * 100), 2, ".") : 0;
            $per = ($avgtotal > 0) ? ahcfree_ceil_dec((($vtm_visitors / $avgtotal) * 100), 2, ".") : 0;

            if ($all == 1)
                $new[$j]['percent'] = $per;

            if (ceil($per) > 25 && ceil($per) < 50) {
                $cls = 'visitorsGraph2';
            } else if (ceil($per) > 50) {
                $cls = 'visitorsGraph3';
            } else {
                $cls = 'visitorsGraph';
            }
            $css = (!empty($per)) ? 'style="width: ' . ceil($per) . '%;"' : '';
            $arr[$j]['graph'] = '<div class="visitorsGraphContainer"><div class="' . esc_attr($cls) . '" ' . $css . '>&nbsp;</div><div class="cleaner"></div></div><div class="visitorsPercent">(' . ceil($per) . ')%</div>';
            $j++;
            $cls = '';
            $per = 0;
        }
        if ($all == 1)
            return $new;
        return $arr;
    }
    //}
    return false;
}

function ahcfree_advanced_get_link($url, $followRedirects = true)
{
    $ahc_data = wp_remote_get($url);
    return json_decode(wp_remote_retrieve_body($ahc_data));
}

//--------------------------------------------
/**
 * Returns client IP address
 *
 * @return string
 */
 
 
function ahc_free_get_simple_ip($ip)
{
	$exploded_ip = explode(":", $ip);
	$size = sizeof($exploded_ip);

	if($size == 1) // ipv4 format
	return $ip;

	if($size == 4) // ipv4 in ipv6 format
	{
	if(empty($exploded_ip[0]) && empty($exploded_ip[1]) && strtoupper($exploded_ip[2]) == "FFFF")
	return $exploded_ip[3];
	}

	return $ip; // default ipv6
}

function ahcfree_get_client_ip_address()
{
    global $_SERVER;
    $ipAddress = '';
    if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_X_REAL_IP'];
    } else if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']) && $_SERVER['HTTP_X_FORWARDED'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR']) && $_SERVER['HTTP_FORWARDED_FOR'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED'])  && $_SERVER['HTTP_FORWARDED'] != '127.0.0.1') {
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])  && $_SERVER['REMOTE_ADDR'] != '127.0.0.1') {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipAddress = 'UNKNOWN';
    }

	$ipAddress = ahc_free_get_simple_ip($ipAddress);
    $ipAddress = ahc_free_sanitize_text_or_array_field($ipAddress);
	

	
    $ipAddress = explode(',', $ipAddress);

    return $ipAddress[0];
}

//--------------------------------------------
/**
 * To include scripts and styles tags into the head
 *
 * @uses wp_register_style()
 * @uses wp_enqueue_style()
 * @uses wp_register_script()
 * @uses wp_enqueue_script()
 *
 * @return void
 */
function ahcfree_include_scripts()
{
    wp_register_style('ahc_custom_css', plugins_url('/css/custom.css', AHCFREE_PLUGIN_MAIN_FILE), '', time());
    wp_enqueue_style('ahc_custom_css');

    wp_register_style('ahc_lang_css', plugins_url('/css/vtrts_css_stylesheet.css', AHCFREE_PLUGIN_MAIN_FILE), '', '1.4');
    wp_enqueue_style('ahc_lang_css');


    wp_register_script('slimselect_js', plugins_url('js/slimselect.min.js', AHCFREE_PLUGIN_MAIN_FILE), null, null, true);
    wp_enqueue_script('slimselect_js');


    wp_register_style('slimselect', plugins_url('css/slimselect.min.css', AHCFREE_PLUGIN_MAIN_FILE));
    wp_enqueue_style('slimselect');

    wp_register_style('ahc_bootstrap_css', plugins_url('/lib/bootstrap/css/bootstrap.min.css', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_style('ahc_bootstrap_css');

    wp_enqueue_script('jquery');

    wp_register_script('ahc_bootstrap_js', plugins_url('/lib/bootstrap/js/bootstrap.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_bootstrap_js');

    wp_register_script('ahc_lang_js', plugins_url('/lang/js/' . GlobalsAHC::$lang . '_lang.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_lang_js');

    /* Pagination and export */
    wp_register_script('ahc_datatable_js', plugins_url('/js/jquery.dataTables.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_datatable_js');
    wp_register_script('ahc_tableexport_js', plugins_url('/js/dataTables.buttons.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_tableexport_js');
    wp_register_script('ahc_jzip_js', plugins_url('/js/jszip.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_jzip_js');
    wp_register_script('ahc_tableexportbutton_js', plugins_url('/js/buttons.html5.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_tableexportbutton_js');

    wp_register_script('ahc_xlscore_js', plugins_url('/js/xlsx.core.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_xlscore_js');
    wp_register_script('ahc_filesave_js', plugins_url('/js/FileSaver.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_filesave_js');
    wp_register_script('ahc_xls_js', plugins_url('/js/jhxlsx.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_xls_js');

    wp_register_style('jquery_date_css', plugins_url('/css/datepicker.css', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_style('jquery_date_css');



    wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
    wp_register_script('ahc_main_js', plugins_url('/js/ahcfree_js_scripts.js', AHCFREE_PLUGIN_MAIN_FILE), '', '2.5');
    wp_enqueue_script('ahc_main_js');

    wp_localize_script('ahc_main_js', 'ahc_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
	
	wp_enqueue_script('sweetalert', plugins_url('/js/sweetalert.min.js', AHCFREE_PLUGIN_MAIN_FILE));
	wp_enqueue_style( 'sweetalert', plugins_url('/css/sweetalerts.css', AHCFREE_PLUGIN_MAIN_FILE));
}



function ahcfree_settings_scripts()
{


    wp_register_style('ahc_bootstrap_css', plugins_url('/lib/bootstrap/css/bootstrap.min.css', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_style('ahc_bootstrap_css');

    wp_enqueue_script('jquery');

    wp_register_script('ahc_bootstrap_js', plugins_url('/lib/bootstrap/js/bootstrap.min.js', AHCFREE_PLUGIN_MAIN_FILE), '', '1.21');
    wp_enqueue_script('ahc_bootstrap_js');

    wp_register_script('slimselect_js', plugins_url('js/slimselect.min.js', AHCFREE_PLUGIN_MAIN_FILE), null, null, true);
    wp_enqueue_script('slimselect_js');


    wp_register_style('slimselect', plugins_url('css/slimselect.min.css', AHCFREE_PLUGIN_MAIN_FILE));
    wp_enqueue_style('slimselect');
}



//--------------------------------------------
//---------------------------------------------Add button to the admin bar
function ahcfree_vtrts_add_items($admin_bar)
{
    global $pluginsurl;

    $wccpadminurl = get_admin_url();
    //The properties of the new item. Read More about the missing 'parent' parameter below
    $args = array(
        'id' => 'visitorstraffic',
        'title' => __('<img src="' . plugins_url('/images/vtrtspro.png', AHCFREE_PLUGIN_MAIN_FILE) . '" style="vertical-align:middle;margin-right:5px;" alt="visitor traffic" title="visitor traffic" />'),
        'href' => $wccpadminurl . 'admin.php?page=ahc_hits_counter_menu_free',
        'meta' => array('title' => __('Visitor Traffic Real Time Statistics'),)
    );

    //This is where the magic works.
    $admin_bar->add_menu($args);
}

//---------------------------------------- Add plugin settings link to Plugins page
function ahcfree_vtrtsp_plugin_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=ahc_hits_counter_menu_free">' . __('visitor traffic') . '</a>';
    array_push($links, $settings_link);
    return $links;
}





function ahcfree_get_hits_by_custom_duration_callback()
{
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	
    $hits_duration = ahc_free_sanitize_text_or_array_field($_POST['hits_duration']);
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());

    $myend_date = new DateTime();
    $myend_date->setTimezone($custom_timezone);

    $end_date = $myend_date->format('Y-m-d');
    $full_end_date = $myend_date->format('Y-m-d 23:59:59');

    $mystart_date = new DateTime();
    $mystart_date->setTimezone($custom_timezone);
    $stat = '';
    switch ($hits_duration) {

        case '7':
            $mystart_date->modify('-6 days');
            $start_date = $mystart_date->format('Y-m-d');
            $full_start_date = $mystart_date->format('Y-m-d 00:00:00');
            $interval = '1 day';

            break;

        case 'current_month':
            //$mystart_date->modify('0:00 first day of curent month');
            $start_date = $mystart_date->format('Y-m-01');
            $end_date = $mystart_date->format('Y-m-t');
            $full_start_date = $mystart_date->format('Y-m-01');
            $full_end_date = $mystart_date->format('Y-m-t');
            $interval = '1 day';
            $stat = 'current_month';
            break;

        case 'last_month':
            $mystart_date->modify('0:00 first day of previous month');
            $start_date = $mystart_date->format('Y-m-d');
            $end_date = $mystart_date->format('Y-m-t');
            $full_start_date = $mystart_date->format('Y-m-d');
            $full_end_date = $mystart_date->format('Y-m-t');
            $interval = '1 day';
            $stat = 'last_month';
            break;

        case '30':
            /*$mystart_date->modify('first day of previous month');
            $start_date = $mystart_date->format('Y-m-d');
            $full_start_date = $mystart_date->format('Y-m-d H:i:s');
            
            $myend_date->modify('last day of previous month');
            $end_date = $myend_date->format('Y-m-d');
            $full_end_date = $myend_date->format('Y-m-d H:i:s');*/

            $mystart_date->modify('-30 days');
            $start_date = $mystart_date->format('Y-m-d');
            $full_start_date = $mystart_date->format('Y-m-d 00:00:00');

            $interval = '1 week';
            break;



        case '0':
            $full_start_date = $full_end_date = '';
            $stat = 'all';
            break;

        case 'range':
            $full_start_date = $start_date = ahc_free_sanitize_text_or_array_field($_POST['hits_duration_from']);
            $full_end_date = ahc_free_sanitize_text_or_array_field($_POST['hits_duration_to']);
            $interval = '1 day';
            break;

        default:
            $mystart_date->modify(' - ' . (AHCFREE_VISITORS_VISITS_LIMIT - 1) . ' days');
            $start_date = $mystart_date->format('Y-m-d');
            $full_start_date = $mystart_date->format('Y-m-d 00:00:00');
            $interval = '1 day';
            break;
    }

    $visits_visitors_data = ahcfree_get_visits_by_custom_duration_callback($full_start_date, $full_end_date, $stat);
    //print_r($visits_visitors_data);

    $response = array(
        'mystart_date' => $start_date,
        'myend_date' => $end_date,
        'full_start_date' => $full_start_date,
        'full_end_date' => $full_end_date,
        'interval' => $interval,
        'visitors_data' => json_encode($visits_visitors_data['visitors']),
        'visits_data' => json_encode($visits_visitors_data['visits'])
    );

    echo json_encode($response);
    die;
}


function ahcfree_get_visits_by_custom_duration_callback($start_date, $end_date, $stat)
{
    global $wpdb;
    $visits_arr = array();
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());

    $results = false;

    $mystart_date = new DateTime($start_date);
    $myend_date = new DateTime($end_date);

    $total_days = date_diff($mystart_date, $myend_date);
    $total_days = $total_days->format("%a");

    $cond = "DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) >= DATE('" . $start_date . " 00:00:00') AND DATE(CONVERT_TZ(vst_date, '" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "')) <= DATE('" . $end_date . " 23:59:59')";

    if ($stat == 'year') {
        $sql = "SELECT DATE_FORMAT(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m') as group_date,DATE_FORMAT(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'),'%Y-%m-01') as vst_date,SUM(vst_visitors) as vst_visitors,SUM(vst_visits) as vst_visits FROM ahc_visitors WHERE ahc_visitors.site_id = ".get_current_blog_id()." and " . $cond . " GROUP BY group_date";
    }
    if ($stat == 'all') {
        $sql = "SELECT DATE_FORMAT(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "', '" . $custom_timezone_offset . "'),'%Y-%m') as group_date,DATE_FORMAT(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'),'%Y-%m-01') as vst_date,SUM(vst_visitors) as vst_visitors,SUM(vst_visits) as vst_visits FROM ahc_visitors where ahc_visitors.site_id = ".get_current_blog_id()."  GROUP BY group_date ORDER BY vst_date ASC";
    }
    if ($stat == '' || $stat == 'current_month' || $stat == 'last_month') {
        $sql = "SELECT DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "')) as vst_date, SUM(vst_visits) AS vst_visits,SUM(vst_visitors) as vst_visitors FROM ahc_visitors WHERE ahc_visitors.site_id = ".get_current_blog_id()." and " . $cond . " GROUP BY DATE(CONVERT_TZ(vst_date,'" . AHCFREE_SERVER_CURRENT_TIMEZONE . "','" . $custom_timezone_offset . "'))";
    }
    //echo $sql;
    $results = $wpdb->get_results($sql, OBJECT);
    if ($results !== false) {

        if ($stat == 'year') {
            for ($i = 1; $i <= ahcfree_localtime('n'); $i++) {
                $month = $mystart_date->format('m');
                $year  = $mystart_date->format('Y');
                $total_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                $visits_arr['visits'][] = array($mystart_date->format('Y-m-d'), 0);
                $visits_arr['visitors'][] = array($mystart_date->format('Y-m-d'), 0);
                $mystart_date->modify('+' . $total_days . ' days');
            }
        } elseif ($stat == 'all') {
            foreach ($results as $key => $element) {
                reset($results);
                if ($key === key($results)) {
                    $first_date = $element->vst_date;
                }

                end($results);
                if ($key === key($results)) {
                    $last_date = $element->vst_date;
                }
            }

            $d1 = new DateTime($first_date);
            $d2 = new DateTime($last_date);

            if (count($results) == 1) {
                $pre_d1 = new DateTime($first_date);
                $pre_d1->modify('first day of previous month');
                $visits_arr['visits'][] = array($pre_d1->format('Y-m-d'), 0);
                $visits_arr['visitors'][] = array($pre_d1->format('Y-m-d'), 0);
            }

            $diff = $d1->diff($d2)->m + 1;

            for ($i = 1; $i <= $diff; $i++) {
                $visits_arr['visits'][] = array($d1->format('Y-m-d'), 0);
                $visits_arr['visitors'][] = array($d1->format('Y-m-d'), 0);
                $d1->modify('+1 Month');
            }
        } else {
            if ($stat == 'current_month') {
                $total_days = ahcfree_localtime('t');
                $total_days--;
            }
            if ($stat == 'last_month') {
                $total_days = ahcfree_localtime('t', strtotime('first day of previous month'));
                $total_days--;
            }
            $visits_arr['visits'][] = array($mystart_date->format('Y-m-d'), 0);
            $visits_arr['visitors'][] = array($mystart_date->format('Y-m-d'), 0);
            for ($i = 1; $i <= $total_days; $i++) {
                $mystart_date->modify('+1 Day');
                $visits_arr['visits'][] = array($mystart_date->format('Y-m-d'), 0);
                $visits_arr['visitors'][] = array($mystart_date->format('Y-m-d'), 0);
            }
        }
        //print_r($visits_arr['visits']);
        foreach ($visits_arr['visits'] as $key => $visits) {
            foreach ($results as $r) {
                if ($visits[0] == $r->vst_date) {
                    $visits_arr['visits'][$key][1] = $r->vst_visits;
                }
            }
        }

        foreach ($visits_arr['visitors'] as $key => $visits) {
            foreach ($results as $r) {
                if ($visits[0] == $r->vst_date) {
                    $visits_arr['visitors'][$key][1] = $r->vst_visitors;
                }
            }
        }
    }
    //echo $wpdb->last_query;
    return $visits_arr;
}
function ahcfree_admin_notice_to_set_timezone()
{
    $class = 'notice notice-error';
    $name = 'Visitor Traffic Real Time Statistics Free - Invalid Timezone';
    $message = sprintf(__('Invalid timezone, Please visit the <a href="%s">settings</a> page and select your current timezone.'), site_url('wp-admin/admin.php?page=ahc_hits_counter_settings'));

    printf('<br><div class="%1$s" style="padding-top:5px;"><b>%2$s</b><p>%3$s</p></div>', esc_attr($class), $name, $message);
}
/*function ahcfree_get_visitors_by_custom_duration_callback( $start_date,$end_date ){
    
    global $wpdb;
    $visitors_arr = array();
    $custom_timezone_offset = ahcfree_get_current_timezone_offset();
    $custom_timezone = new DateTimeZone(ahcfree_get_timezone_string());
    
    $results = false;
    
    $mystart_date = new DateTime($start_date);
    $myend_date = new DateTime($end_date);
    
    $total_days = date_diff( $mystart_date, $myend_date );
    $total_days = $total_days->format("%a");
    
    
    if($start_date == 'all'){
        
        $cond = "DATE(CONVERT_TZ(vst_date, " . AHCFREE_SERVER_CURRENT_TIMEZONE . ", '" . $custom_timezone_offset . "')) <= DATE('". $end_date ."')";
    
    }
    else{
        
        $cond = "DATE(CONVERT_TZ(vst_date, " . AHCFREE_SERVER_CURRENT_TIMEZONE . ", '" . $custom_timezone_offset . "')) <= DATE('". $end_date ."') AND DATE(CONVERT_TZ(vst_date, " . AHCFREE_SERVER_CURRENT_TIMEZONE . ", '" . $custom_timezone_offset . "')) >= DATE('". $start_date ."')";
    
    }
    
    
    $sql = "SELECT DATE(CONVERT_TZ(vst_date," . AHCFREE_SERVER_CURRENT_TIMEZONE . ",'" . $custom_timezone_offset . "')) as vst_date, vst_visitors 
            FROM ahc_visitors 
            WHERE ". $cond;
    
    $results = $wpdb->get_results($sql, OBJECT);

    if ($results !== false) {
        $mystart_date->modify( '-1 Day' );
        for ($i = count($results); $i < $total_days; $i++) {
            $visitors_arr[] = array($mystart_date->format('Y-m-d') , 0 );
            $mystart_date->modify( '+1 Day' );
        }
        foreach ($results as $r) {

            $hitDate = new DateTime($r->vst_date);
            $visitors_arr[] = array($hitDate->format('Y-m-d'), $r->vst_visitors);
        }
    }
    return $visitors_arr;
    
}*/

function ahcfree_getFormattedDate($date, $format = "")
{
    if ($date != '') {
        if ($format == "yymmdd")
            return  DateTime::createFromFormat('m-d-Y', $date)->format('Y-m-d');
        else
            return  DateTime::createFromFormat('m-d-Y', $date)->format('m/d/Y');
    }
}
add_action("wp_ajax_traffic_by_title", "ahcfree_traffic_by_title_callback");
function ahcfree_traffic_by_title_callback()
{
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {

        $search_value = ahc_free_sanitize_text_or_array_field($_REQUEST['search']['value']);

        $search_page = ahc_free_sanitize_text_or_array_field($search_value);

        $res = ahcfree_get_traffic_by_title(1, false, "", "", $search_page);
        echo json_encode($res);
        exit;
    } else {
        $search_page = ahc_free_sanitize_text_or_array_field($_REQUEST['search']['value']);
        $search_start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $search_length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);

        $cnt = ahcfree_get_traffic_by_title("", true, "", "", $search_page);

        $tTitles = ahcfree_get_traffic_by_title("", false, $search_start, $search_length, $search_page);

        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $tTitles;
        echo json_encode($arr);
        exit;
    }
}
//add_action("wp_ajax_traffic_by_countries","ahcfree_traffic_by_countries_callback");
function ahcfree_traffic_by_countries_callback()
{
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	

    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {
        $res = ahcfree_get_top_countries(0, "", "", 1, false);
        echo json_encode($res);
        exit;
    } else {

        $search_start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $search_length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);


        $tTitles = ahcfree_get_top_countries(0, $search_start, $search_length, "", false);
        $cnt = ahcfree_get_top_countries(0, "", "", "", true);

        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $tTitles['data'];
        echo json_encode($arr);
        exit;
    }
}

add_action("wp_ajax_recent_visitor_by_ip", "ahcfree_recent_visitor_by_ip_callback");
function ahcfree_recent_visitor_by_ip_callback()
{
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {

        $ip = ahc_free_sanitize_text_or_array_field($_REQUEST['ip']);
        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);

        $res = ahcfree_get_recent_visitors(1, false, "", "", $fdt, $tdt, $ip);
        echo json_encode($res);
        exit;
    } else {
        $ip = ahc_free_sanitize_text_or_array_field($_REQUEST['ip']);
        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);
        $start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);

        $cnt = ahcfree_get_recent_visitors("", true, "", "", $fdt, $tdt, $ip);
        $recentVisitors = ahcfree_get_recent_visitors("", false, $start, $length, $fdt, $tdt, $ip);


        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $recentVisitors;
        echo json_encode($arr);
        exit;
    }
}
add_action("wp_ajax_latest_search_words", "ahcfree_latest_search_words_callback");
function ahcfree_latest_search_words_callback()
{
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {

        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);


        $res = ahcfree_get_latest_search_key_words_used(1, false, "", "", $fdt, $tdt);
        echo json_encode($res);
        exit;
    } else {

        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);
        $start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);


        $cnt = ahcfree_get_latest_search_key_words_used("", true, "", "", $fdt, $tdt);
        $recentVisitors = ahcfree_get_latest_search_key_words_used("", false, $start, $length, $fdt, $tdt);

        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $recentVisitors;
        echo json_encode($arr);
        exit;
    }
}
add_action("wp_ajax_today_traffic_index", "ahcfree_today_traffic_index_callback");
function ahcfree_today_traffic_index_callback()
{
	
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {
        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);


        $res = ahcfree_get_vsitors_by_country(1, false, "", "", $fdt, $tdt);
        echo json_encode($res);
        exit;
    } else {
        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);
        $start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);

        $cnt = ahcfree_get_vsitors_by_country("", true, "", "", $fdt, $tdt);
        $countries = ahcfree_get_vsitors_by_country("", false, $start, $length, $fdt, $tdt);

        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $countries;
        echo json_encode($arr);
        exit;
    }
}
add_action("wp_ajax_visits_time_graph", "ahcfree_visits_time_graph_callback");
function ahcfree_visits_time_graph_callback()
{
	
	if ( !is_user_logged_in() || !current_user_can( 'manage_options' ) )
	{
		die( 'you don\'t have permission to access this resource' );
	}
	
    if (isset($_REQUEST['page']) && $_REQUEST['page'] == "all") {
        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);


        $times = ahcfree_get_time_visits(1, "", "", $fdt, $tdt);
        echo json_encode($times);
        exit;
    } else {

        $fdt = ahc_free_sanitize_text_or_array_field($_REQUEST['fdt']);
        $tdt = ahc_free_sanitize_text_or_array_field($_REQUEST['tdt']);
        $start = ahc_free_sanitize_text_or_array_field($_REQUEST['start']);
        $length = ahc_free_sanitize_text_or_array_field($_REQUEST['length']);


        $times = ahcfree_get_time_visits("", $start, $length, $fdt, $tdt);
        //$res = ahcfree_get_time_visits("","","",$_REQUEST['fdt'],$_REQUEST['tdt']);
        $cnt = 24;
        $arr["draw"] = 0;
        $arr["recordsTotal"] = $cnt;
        $arr["recordsFiltered"] = $cnt;
        $arr['data'] = $times;
        echo json_encode($arr);
        exit;
    }
}

//------------------------------------------------------------------------
// --------------------------------------- Create front-end widget
// Creating the widget 
class ahcfree_vtrts_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            // Base ID of your widget
            'ahcfree_vtrts_widget',
            // Widget name will appear in UI
            __('Visitor Traffic', 'wpb_widget_domain'),
            // Widget description
            array('description' => __('Display your site statistics', 'wpb_widget_domain'),)
        );
    }

    // Creating widget front-end
    // This is where the action happens
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes

        if (!empty($title))
            echo esc_html($title);


        $ahc_sum_stats = ahcfree_get_summary_statistics();

        if (is_array($instance)) {
            // This is where you run the code and display the output
            echo '<ul style="list-style:none; ' . esc_attr($instance['fontTypeCombo']) . '; font-size:' . esc_attr($instance['fontSizeCombo']) . 'px">';

            if (isset($instance['display_visitorstoday']) && ($instance['display_visitorstoday'] == 1 or $instance['display_visitorstoday'] == '1')) {
                echo '<li><b>Today\'s visitors: </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['today']['visitors'])) . '</span></li>';
            }
            if (isset($instance['display_pageviewtoday']) && ($instance['display_pageviewtoday'] == 1 or $instance['display_pageviewtoday'] == '1')) {
                echo '<li><b>Today\'s page views: : </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['today']['visits'])) . '</span></li>';
            }

            if (isset($instance['display_totalvisitors']) && ($instance['display_totalvisitors'] == 1 or $instance['display_totalvisitors'] == '1')) {
                echo '<li><b>Total visitors : </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['total']['visitors'])) . '</span></li>';
            }

            if (isset($instance['display_totalpageview']) &&  ($instance['display_totalpageview'] == 1 or $instance['display_totalpageview'] == '1')) {
                echo '<li><b>Total page views: </b><span>' . ahcfree_NumFormat(intval($ahc_sum_stats['total']['visits'])) . '</span></li>';
            }


            echo '</ul>';
        }
    }

    // Widget Backend 
    public function form($instance)
    {
        extract($instance);

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Site Statistics', 'wpb_widget_domain');
        }
        // Widget admin form

        $fontTypeCombo = (isset($fontTypeCombo)) ? $fontTypeCombo : '';
        $display_valuescolor = (isset($display_valuescolor)) ? $display_valuescolor : '';
        $display_titlecolor = (isset($display_titlecolor)) ? $display_titlecolor : '';


    ?>


        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>





        <p>
            <label for="<?php echo esc_attr($this->get_field_id('fontTypeCombo')); ?>"><?php _e('Font Type:'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('fontTypeCombo')); ?>" name="<?php echo esc_attr($this->get_field_name('fontTypeCombo')); ?>">
                <optgroup class='verdana'>
                    <option <?php selected($fontTypeCombo, 'font-family:Verdana, Geneva, sans-serif'); ?> value="font-family:Verdana, Geneva, sans-serif">Verdana</option>
                </optgroup>

                <optgroup class='TimesNew'>
                    <option <?php selected($fontTypeCombo, "font-family:'Times New Roman', Times, serif"); ?> value="font-family:'Times New Roman', Times, serif">Times New Roman</option>
                </optgroup>

                <optgroup class='Arial'>
                    <option <?php selected($fontTypeCombo, "font-family:Arial, Helvetica, sans-serif"); ?> value="font-family:Arial, Helvetica, sans-serif">Arial</option>
                </optgroup>

                <optgroup class='Tahoma'>
                    <option <?php selected($fontTypeCombo, "font-family:Tahoma, Geneva, sans-serif"); ?> value="font-family:Tahoma, Geneva, sans-serif">Tahoma</option>
                </optgroup>

                <optgroup class='Courier'>
                    <option <?php selected($fontTypeCombo, "font-family:'Courier New', Courier, monospace"); ?> value="font-family:'Courier New', Courier, monospace">Courier</option>
                </optgroup>

                <optgroup class='TrebuchetMS'>
                    <option <?php selected($fontTypeCombo, "font-family:'Trebuchet MS', Arial, Helvetica, sans-serif"); ?> value="font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">Trebuchet MS</option>
                </optgroup>


            </select>

        </p>
        <label for="<?php echo esc_attr($this->get_field_id('fontSizeCombo')); ?>"><?php _e('Font Size:'); ?></label>
        <select class="widefat" id="<?php echo esc_attr($this->get_field_id('fontSizeCombo')); ?>" name="<?php echo esc_attr($this->get_field_name('fontSizeCombo')); ?>">
            <?php
            for ($fs = 8; $fs <= 22; $fs++) {
                $fontSizeCombo = (isset($fontSizeCombo)) ? $fontSizeCombo : '12';
            ?>
                <option value="<?php echo intval($fs) ?>" <?php selected($fontSizeCombo, $fs); ?>><?php echo intval($fs); ?>px</option>
            <?php } ?>
        </select>
        <p>

        </p>

        <p><em>Display :</em></p>
        <?php

        $display_visitorstoday = isset($display_visitorstoday) ? $display_visitorstoday : '0';
        $display_pageviewtoday = isset($display_pageviewtoday) ? $display_pageviewtoday : '0';
        $display_totalpageview = isset($display_totalpageview) ? $display_totalpageview : '0';
        $display_totalvisitors = isset($display_totalvisitors) ? $display_totalvisitors : '0';
        ?>


        <p>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('display_visitorstoday')); ?>" name="<?php echo esc_attr($this->get_field_name('display_visitorstoday')); ?>" type="checkbox" value="1" <?php checked($display_visitorstoday, '1'); ?> />&nbsp;<label for="<?php echo esc_attr($this->get_field_id('display_visitorstoday')); ?>">Today\'s visitors</label>
        </p>
        <p>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('display_pageviewtoday')); ?>" name="<?php echo esc_attr($this->get_field_name('display_pageviewtoday')); ?>" type="checkbox" value="1" <?php checked($display_pageviewtoday, '1'); ?> />&nbsp;<label for="<?php echo esc_attr($this->get_field_id('display_pageviewtoday')); ?>">Today\'s page views</label>
        </p>
        <p>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('display_totalpageview')); ?>" name="<?php echo esc_attr($this->get_field_name('display_totalpageview')); ?>" type="checkbox" value="1" <?php checked($display_totalpageview, '1'); ?> />&nbsp;<label for="<?php echo esc_attr($this->get_field_id('display_totalpageview')); ?>">Total page views</label>
        </p>
        <p>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('display_totalvisitors')); ?>" name="<?php echo esc_attr($this->get_field_name('display_totalvisitors')); ?>" type="checkbox" value="1" <?php checked($display_totalvisitors, '1'); ?> />&nbsp;<label for="<?php echo esc_attr($this->get_field_id('display_totalvisitors')); ?>">Total visitors</label>
        </p>

<?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {

        return $new_instance;
    }
}

// Class ahcfree_vtrts_widget ends here
// Register and load the widget
function ahcfree_wpb_load_widget()
{
    register_widget('ahcfree_vtrts_widget');
}

add_action('widgets_init', 'ahcfree_wpb_load_widget');

?>