<?php


$custom_timezone_offset = ahcfree_get_current_timezone_offset();
$custom_timezone_string = ahcfree_get_timezone_string();


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
</style>
<script language="javascript" type="text/javascript">
    function imgFlagError(image) {
        image.onerror = "";
        image.src = "<?php echo plugins_url('/images/flags/noFlag.png', AHCFREE_PLUGIN_MAIN_FILE) ?>";
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

    <div class="row">
        <div class="col-lg-12">
            <br />
            <div id="vtrts_subscribe" class="alert alert-success is-dismissible alert is-dismissible" role="alert" style="font-size:12px">

                <input type="text" width="400px" name="ahc_admin_email" id="ahc_admin_email" value="<?php echo get_bloginfo("admin_email"); ?>">
                <button type="button" class="btn btn-primary" onclick="vtrts_open_subscribe_link('<?php echo get_bloginfo("admin_email") ?>')">Subscribe</button><br /><br />Subscribe now to get latest news and updates, plugin recommendations and configuration help, promotional email with<b style="color:red"> discount codes :) </b><br />
                <div style="float:right; font-size:12px; display:inline"><a href="#" style="cursor: pointer; !important;" onclick="vtrts_dismiss_notice()">Dismiss this notice</a></div>
            </div>

            <script>
                function vtrts_dismiss_notice() {
                    localStorage.setItem('vtrts_subscribed', 'vtrts_subs_users');
                    document.getElementById("vtrts_subscribe").style.display = "none";
                }

                function vtrts_open_subscribe_link() {
                    if (localStorage.getItem('vtrts_subscribed') != 'vtrts_subs_users') 
                    {
                        var ahc_admin_email = document.getElementById("ahc_admin_email").value;
                        window.open('https://www.wp-buy.com/vtrts-subscribe/?email=' + ahc_admin_email, '_blank');

                    }
                }

                if (localStorage.getItem('vtrts_subscribed') == 'vtrts_subs_users') {

                    document.getElementById("vtrts_subscribe").style.display = "none";
                }
            </script>


        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <h1><img height="55px" src="<?php echo esc_url(plugins_url('/images/logo.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>">&nbsp;Visitor Traffic Real Time Statistics free &nbsp;
				<?php if(current_user_can('manage_options')){?><a title="change settings" href="admin.php?page=ahc_hits_counter_settings"><img src="<?php echo esc_url(plugins_url('/images/settings.jpg', AHCFREE_PLUGIN_MAIN_FILE)) ?>" /></a><?php }?></h1>

        </div>
        <div class="col-lg-4">
            <h2 id="ahcfree_currenttime"></h2>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-3">
            <div class="box_widget greenBox">
                <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true"><img src="<?php echo esc_url(plugins_url('/images/upgrade_now.png', AHCFREE_PLUGIN_MAIN_FILE)) ?>"></a>
                <br /><span class="txt"><img src="<?php echo esc_url(plugins_url('/images/live.gif', AHCFREE_PLUGIN_MAIN_FILE)) ?>">&nbsp; Online Users</span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="box_widget blueBox">
                <span id="today_visitors_box">0</span><br /><span class="txt">Today's Visitors</span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="box_widget redBox">
                <span id="today_visits_box">0</span><br /><span class="txt">Today's Visits</span>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="box_widget movBox">
                <span id="today_search_box">0</span><br /><span class="txt">Search Engines</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="panel" style="background-color:white ;border-radius: 7px;">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    <?php echo "Traffic Report "; ?></h2>
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
                    <div id="visitscount" style="height:400px; width:99% !important; "></div>
                </div>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="panel" style="width:100% !important">

                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('/images/geomap_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
                    </a>
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
    <div class="row">
        <div class="col-lg-8">
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Recent visitors by IP<span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span></h2>
                <div class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "recent_visitor_by_ip") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">


                        <?php
                        $r_from_dt = isset($_POST['r_from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['r_from_dt']) : '';
                        $r_to_dt = isset($_POST['r_to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['r_to_dt']) : '';
                        $ip_addr = isset($_POST['ip_addr']) ? ahc_free_sanitize_text_or_array_field($_POST['ip_addr']) : '';
                        ?>


                        <label>Search: </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="recent_visitor_by_ip" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="r_from_dt" id="r_from_dt" autocomplete="off" value="<?php echo esc_attr($r_from_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="r_to_dt" id="r_to_dt" autocomplete="off" value="<?php echo esc_attr($r_to_dt); ?>" />
                        <input type="text" name="ip_addr" id="ip_addr" placeholder="IP address" class="ahc_clear" value="<?php echo esc_attr($ip_addr); ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <div class="panelcontent" style="width:100% !important">


                    <table width="95%" tableborder="0" cellspacing="0" class="recentv" id="recent_visit_by_ip">
                        <thead>
                            <tr>

                                <th>IP Address</th>
                                <th>Location</th>


                                <th>Time</th>
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
        <div class="col-lg-4">
            <div class="panel" style="width:100% !important">
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

                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo plugins_url('/images/today_traffic_by_country_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE) ?>">
                    </a>
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
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Traffic by country (all time)</h2>
                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('/images/traffic_by_country_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
                    </a>
                </div>
            </div>

        </div>

        <div class="col-lg-6">
            <!-- Countries chart panel -->
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Top Referring Countries (all time)</h2>

                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('/images/top_refferring_countries_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
                    </a>
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">

            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_refering_sites ?> (Top 20)<span class="export_data"><a href="#" class="dashicons dashicons-external" title="Export Data"></a></span></h2>
                <div class="panelcontent" style="width:100% !important">
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
                                        <td class="values"><?php echo esc_html($site['site_name']); ?>&nbsp;<a href="https://<?php echo str_replace('http://', '', esc_url($site['site_name'])) ?>" target="_blank"><img src="<?php echo esc_url(plugins_url('/images/openW.jpg', AHCFREE_PLUGIN_MAIN_FILE)) ?>" title="<?php echo esc_attr(ahc_view_referer) ?>"></a></td>
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
                        <div class="no-record">No data available.</div>
                    <?php
                    }
                    ?>
                </div>
            </div>

        </div>



        <div class="col-lg-6">
            <!-- time visits graph begin -->
            <?php
            //$times = ahcfree_get_time_visits();
            $times = array();
            ?>
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Today's time graph<span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span><span class="export_data"><a href="#" class="dashicons dashicons-external" title="Export Data"></a></span></h2>
                <div class="search-panel <?php echo (isset($_POST['section']) && $_POST['section'] == "visit_time") ? "open" : ''; ?>">
                    <form method="post" class="search_frm">
                        <?php
                        $vfrom_dt = isset($_POST['vfrom_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['vfrom_dt']) : '';
                        $vto_dt = isset($_POST['vto_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['vto_dt']) : '';
                        ?>

                        <label>Search : </label>
                        <input type="hidden" name="page" value="ahc_hits_counter_menu_free" />
                        <input type="hidden" name="section" value="visit_time" />
                        <input type="text" readonly="readonly" placeholder="From Date" class="ahc_clear" name="vfrom_dt" id="vfrom_dt" autocomplete="off" value="<?php echo esc_attr($vfrom_dt); ?>" />
                        <input type="text" readonly="readonly" placeholder="To Date" class="ahc_clear" name="vto_dt" id="vto_dt" autocomplete="off" value="<?php echo esc_attr($vto_dt); ?>" />
                        <input type="submit" class="button button-primary" />
                        <input type="button" class="button button-primary clear_form" value="Clear" />
                    </form>
                </div>
                <div class="panelcontent" style="padding-right: 50px;">
                    <table width="100%" tableborder="0" cellspacing="0" id="visit_time_graph_table">
                        <thead>
                            <tr>
                                <th width="25%"><?php echo ahc_time ?></th>
                                <th width="55%"><?php echo ahc_visitors_graph ?></th>
                                <th width="10%"><?php echo ahc_visitors ?></th>
                                <th width="10%">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (is_array($times)) {
                                foreach ($times as $t) {
                            ?>
                                    <tr>
                                        <td class="values"><?php echo esc_html($t['vtm_time_from']) . ' - ' . esc_html($t['vtm_time_to']) ?></td>
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
                                                            ?>" <?php echo (!empty($t['percent']) ? ' ** style="width: ' . ceil($t['percent']) . '%;"' : '') ?>>&nbsp;</div>
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
    <div class="row">
        <!-- traffic by title -->
        <div class="col-lg-6">
            <?php
            /*$tTitles = ahcfree_get_traffic_by_title();*/
            $tTitles = array();
            ?>
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo traffic_by_title ?> (all time)<span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span><span class="export_data"><a href="#" class="dashicons dashicons-external" title="Export Data"></a></span></h2>

                <div class="panelcontent" style="padding-right: 50px;">
                    <table width="100%" tableborder="0" cellspacing="0" id="traffic_by_title">
                        <thead>
                            <tr>
                                <th width="5%"><?php echo ahc_rank ?></th>
                                <th width="65%"><?php echo ahc_title ?></th>
                                <th width="15%"><?php echo ahc_hits ?></th>
                                <th width="15%"><?php echo ahc_percent ?></th>
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
                                        <td class="values"><a href="<?php echo esc_url(get_permalink($t['til_page_id'])); ?>" target="_blank"><?php echo esc_html($t['til_page_title']); ?></a></td>
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

        <div class="col-lg-6">
            <?php
            /*$lastSearchKeyWordsUsed = ahcfree_get_latest_search_key_words_used();*/
            $lastSearchKeyWordsUsed = array();
            /*if ($lastSearchKeyWordsUsed) 
            {*/
            ?>
            <!-- last search key words used -->
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_latest_search_words; ?> (all time)<span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span><span class="export_data"><a href="#" class="dashicons dashicons-external" title="Export Data"></a></span></h2>
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
                                            <span><?php if ($searchWord['ctr_internet_code'] != '') { ?><img src="<?php echo plugins_url('/images/flags/' . strtolower($searchWord['ctr_internet_code']) . '.png', AHCFREE_PLUGIN_MAIN_FILE); ?>" border="0" width="22" height="18" title="<?php echo esc_html($searchWord['ctr_name']) ?>" onerror="imgFlagError(this)" /><?php } ?></span>
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
                </div>
            </div>

            <?php /*}*/ ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="panel" style="background-color: #fff; border-radius:7px ; padding:20px 10px 10px 10px">
                <center>
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&footer=true">
                        <p style="color:#00F; font-size:15px;">if you need more statistics you can upgrade to the professional version now, The premium version of Visitor Traffic real-time statistics is completely different from the free version as there are a lot more features included.</p>

                        <p><img  height="auto" src="<?php echo esc_url(plugins_url('/images/upgradenow-button.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>" /></p>
                    </a>
                </center>
            </div>


        </div>
    </div>
    <?php
    $visits_visitors_data = ahcfree_get_visits_by_custom_duration_callback($mystart_date, $myend_date, $stat = '');
    ?>


    <?php
    wp_register_script('ahc_gstatic_js', 'https://www.gstatic.com/charts/loader.js', array(), '1.0.0', true);
    wp_enqueue_script('ahc_gstatic_js');
    ?>
    <script language="javascript" type="text/javascript">
        function drawVisitsLineChart(start_date, end_date, interval, visitors, visits, duration) {



            google.charts.load('current', {
                'packages': ['line']
            });
            google.charts.setOnLoadCallback(drawChart);


            var dataRows = [
                ['Date', 'Visitors', 'Page Views']
            ];
            for (var i = 0; i < visitors.length; i++) {
                //alert(visitors[i][1]);
                dataRows.push([visitors[i][0], parseFloat(visitors[i][1]), parseFloat(visits[i][1])]);
            }


            function drawChart() {
                var data = google.visualization.arrayToDataTable(dataRows);

                var options = {
                    title: 'Traffic Report',

                    curveType: 'none',
                    legend: {
                        position: 'top',
                        textStyle: {
                            color: 'blue',
                            fontSize: 16
                        }
                    }
                };

                var chart = new google.charts.Line(document.getElementById('visitscount'));

                chart.draw(data, options);
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

                var chart = new google.visualization.PieChart(document.getElementById('brsPiechartContainer'));
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
var host=window.location.hostname;
var fullpath=window.location.pathname;
var fullparam=window.location.search.split('&');

var firstparam=fullparam[0];

if(localStorage && (firstparam=="?page=ahc_hits_counter_menu_free"))
{
	
var today_visitors_box = document.getElementById('today_visitors_box').innerHTML;
    if (!localStorage.getItem("ahcfreemsg")==true)
    {
	if(today_visitors_box > 5)
	{
		setTimeout(function(){
		
	  swal({
	  title: '',
	  text: '',
	  imageUrl: 'https://www.wp-buy.com/wp-content/uploads/2018/10/output_ZD6GUg-1-2.gif',
	  imageWidth: 'auto',
	  imageHeight: 'auto',
	  imageAlt: 'Need more statistics, GEO locations & online counter?',
	  animation: true,
	  customClass: 'swal-noscroll',
	  allowEscapeKey:true,
	  showCancelButton: false,
	  showConfirmButton: false,
	  html: 'Need more statistics, GEO locations & online counter?<br><br><center><button type="button" role="button" class="confirm btn btn-success SwalBtn2">' + 'Upgrade to pro' + '</button>&nbsp;&nbsp;' +
        '<button type="button" role="button"  class="cancel btn btn-info SwalBtn1">' + 'Close' + '</button>&nbsp;&nbsp;'+
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