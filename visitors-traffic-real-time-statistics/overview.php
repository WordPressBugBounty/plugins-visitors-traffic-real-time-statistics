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

    <div class="row">
        <div class="col-lg-12">
            <br />
            <div id="vtrts_subscribe" class="notice notice-info is-dismissible" role="alert" style="
                    border: 1px solid #c3c4c7;
                    border-left: 4px solid #2271b1;
                    background: #ffffff;
                    padding: 10px 10px 0px 15px;
                    margin: 5px 0 15px;
                    box-shadow: 0 1px 1px rgba(0,0,0,0.04);
                    position: relative;
                ">
                <div class="notice-content" style="
                        display: flex; 
                        align-items: flex-start; 
                        gap: 15px; 
                        padding-right: 40px;
                        flex-wrap: wrap;
                    ">
                    <!-- Left section with icon and text -->
                    <div class="notice-text" style="
                            display: flex; 
                            align-items: flex-start; 
                            gap: 12px; 
                            flex: 1;
                            min-width: 280px;
                        ">
                        <div class="notice-icon" style="
                                width: 32px;
                                height: 32px;
                                background: #2271b1;
                                border-radius: 4px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                flex-shrink: 0;
                                margin-top: 2px;
                            ">
                            <svg width="18" height="14" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2 4L10 10L18 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <rect x="1" y="3" width="18" height="14" rx="2" stroke="white" stroke-width="2" />
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <p style="
                                    margin: 0 0 4px 0;
                                    font-size: 14px;
                                    font-weight: 600;
                                    color: #1d2327;
                                    line-height: 1.3;
                                ">
                                Stay Updated with Plugin News & Exclusive Offers
                            </p>
                            <p style="
                                    margin: 0;
                                    font-size: 13px;
                                    line-height: 1.4;
                                    color: #50575e;
                                ">
                                Get the latest updates, configuration help, and
                                <strong style="color: #d63638; background: rgba(214,54,56,0.1); padding: 1px 4px; border-radius: 2px;">exclusive discount codes</strong>
                                delivered to your inbox.
                            </p>
                        </div>
                    </div>

                    <!-- Right section with form -->
                    <div class="notice-form" style="
                            display: flex; 
                            align-items: center; 
                            gap: 8px; 
                            flex-shrink: 0;
                            min-width: 200px;
                        ">
                        <input type="email" name="ahc_admin_email" id="ahc_admin_email"
                            value="osama.esh@gmail.com" placeholder="your-email@domain.com"
                            style="
                                    padding: 4px 8px;
                                    border: 1px solid #8c8f94;
                                    border-radius: 3px;
                                    font-size: 13px;
                                    line-height: 2;
                                    width: 100%;
                                    max-width: 200px;
                                    min-width: 140px;
                                    background: #fff;
                                    color: #2c3338;
                                    box-sizing: border-box;
                                "
                            onfocus="this.style.borderColor='#2271b1'; this.style.boxShadow='0 0 0 1px #2271b1';"
                            onblur="this.style.borderColor='#8c8f94'; this.style.boxShadow='none';">
                        <button type="button" class="button button-primary"
                            onclick="vtrts_open_subscribe_link('osama.esh@gmail.com')" style="
                                    font-size: 13px;
                                    line-height: 2;
                                    min-height: 28px;
                                    padding: 0 12px;
                                    background: #2271b1;
                                    border-color: #2271b1;
                                    color: #fff;
                                    text-decoration: none;
                                    border-radius: 3px;
                                    border: 1px solid;
                                    cursor: pointer;
                                    white-space: nowrap;
                                    flex-shrink: 0;
                                "
                            onmouseover="this.style.background='#135e96'; this.style.borderColor='#135e96';"
                            onmouseout="this.style.background='#2271b1'; this.style.borderColor='#2271b1';">
                            Subscribe
                        </button>
                    </div>
                </div>

                <!-- WordPress standard dismiss button -->
                <button type="button" class="notice-dismiss" onclick="vtrts_dismiss_notice()" style="
                        position: absolute;
                        top: 0;
                        right: 1px;
                        padding: 9px;
                        background: none;
                        border: none;
                        color: #787c82;
                        cursor: pointer;
                        font-size: 13px;
                    " onmouseover="this.style.color='#135e96';" onmouseout="this.style.color='#787c82';">
                    <span class="screen-reader-text"></span>
                    <span style="
                            width: 20px;
                            height: 20px;
                            display: block;
                            position: relative;
                        ">


                    </span>
                </button>
            </div>

            <!-- Mobile responsive styles -->
            <style>
                @media (max-width: 768px) {
                    #vtrts_subscribe {
                        padding: 10px 40px 10px 10px !important;
                    }

                    .notice-content {
                        flex-direction: column !important;
                        align-items: stretch !important;
                        gap: 12px !important;
                    }

                    .notice-text {
                        min-width: unset !important;
                        margin-bottom: 8px;
                    }

                    .notice-form {
                        justify-content: stretch !important;
                        min-width: unset !important;
                    }

                    .notice-form input {
                        max-width: none !important;
                        min-width: unset !important;
                        flex: 1 !important;
                    }

                    .notice-form button {
                        min-width: 80px !important;
                    }
                }

                @media (max-width: 480px) {
                    .notice-form {
                        flex-direction: column !important;
                        gap: 8px !important;
                    }

                    .notice-form input,
                    .notice-form button {
                        width: 100% !important;
                        max-width: none !important;
                    }

                    .notice-text {
                        gap: 8px !important;
                    }

                    .notice-text p:first-child {
                        font-size: 13px !important;
                    }

                    .notice-text p:last-child {
                        font-size: 12px !important;
                    }
                }
            </style>

            <script>
                function vtrts_dismiss_notice() {
                    // Note: In production, you'd want to use AJAX to save this server-side
                    // localStorage won't persist across different devices/browsers
                    const notice = document.getElementById("vtrts_subscribe");
                    notice.style.display = "none";
                }

                function vtrts_open_subscribe_link(defaultEmail) {
                    const emailInput = document.getElementById("ahc_admin_email");
                    const adminEmail = emailInput ? emailInput.value : defaultEmail;

                    if (!adminEmail || !isValidEmail(adminEmail)) {
                        alert('Please enter a valid email address.');
                        return;
                    }

                    window.open('https://www.wp-buy.com/vtrts-subscribe/?email=' + encodeURIComponent(adminEmail), '_blank');
                }

                function isValidEmail(email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(email);
                }
            </script>
        </div>
    </div>

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

    <!-- Top 4 boxes in admin page -->

    <div class="row">
        <div class="col-lg-3">
            <div class="box_widget greenBox">
                <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true"><img src="<?php echo esc_url(plugins_url('images/upgrade_now.png', AHCFREE_PLUGIN_MAIN_FILE)) ?>"></a>
                <br /><span class="txt"><img src="<?php echo esc_url(plugins_url('images/live.gif', AHCFREE_PLUGIN_MAIN_FILE)) ?>">&nbsp; Online Users</span>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="panel" style="width:100% !important">

                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('images/geomap_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
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
    <div class="row">
        <div class="col-lg-8">
            <div class="panel" style="width:100% !important">
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
                <div class="panelcontent" style="width:100% !important">

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
                        <img width="99%" src="<?php echo plugins_url('images/today_traffic_by_country_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE) ?>">
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
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Traffic by country</h2>
                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('images/traffic_by_country_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
                    </a>
                </div>
            </div>

        </div>

        <div class="col-lg-6">
            <!-- Countries chart panel -->
            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">Top Referring Countries</h2>

                <div class="panelcontent" style="width:100% !important">
                    <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&box=true">
                        <img width="99%" src="<?php echo esc_url(plugins_url('images/top_refferring_countries_pro.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>">
                    </a>
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">

            <div class="panel" style="width:100% !important">
                <h2 class="box-heading" style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important"><?php echo ahc_refering_sites ?><span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span></h2>
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
                        <div class="no-record">No data available.</div>
                    <?php
                    }
                    ?>
                </div>
            </div>

        </div>


        <!-- time visits graph begin -->
        <div class="col-lg-6">
            <?php
            //$times = ahcfree_get_time_visits();
            $times = array();
            ?>
            <div class="panel" style="width:100% !important">
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
    <div class="row">
        <div class="col-lg-6">
            <?php
            // $tTitles = ahcfree_get_traffic_by_title();
            $tTitles = array();
            ?>
            <div class="panel"
                style="width:100% !important; border-radius: 7px !important; border: 0 !important; box-shadow: 0 4px 25px 0 rgb(168 180 208 / 10%) !important;">
                <h2 class="box-heading"
                    style="border-radius: 7px 7px 0 0 !important; padding:12px 15px !important ; border-bottom:0 !important">
                    Traffic by Title
                    <span class="search_data"><a href="#" class="dashicons dashicons-search" title="Search"></a></span>
                    <span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span>
                </h2>

                <div class="panelcontent" style="border-radius:0 0 7px 7px !important; padding-right: 50px;">
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

        <div class="col-lg-6">
            <?php
            /*$lastSearchKeyWordsUsed = ahcfree_get_latest_search_key_words_used();*/
            $lastSearchKeyWordsUsed = array();
            /*if ($lastSearchKeyWordsUsed) 
            {*/
            ?>
            <!-- last search key words used -->
            <div class="panel" style="width:100% !important">
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
                    Traffic Sources<span class="search_data"><a href="#" class="dashicons dashicons-search"
                            title="Search"></a></span><span class="export_data">
                        <a href="#" class="dashicons dashicons-media-spreadsheet" title="Export to Excel"></a>
                    </span></h2>
                <a href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029$&footer=true" target="_blank">
                    <img src="<?php echo plugin_dir_url(__FILE__); ?>images/sourcetype.jpg" style="width:100%">
                </a>
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
                // Get the traffic sources data for display
                $trafficSources = ahcfree_get_traffic_sources_for_display(
                    isset($_POST['from_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['from_dt']) : '',
                    isset($_POST['to_dt']) ? ahc_free_sanitize_text_or_array_field($_POST['to_dt']) : ''
                );
                ?>
                <div class="panelcontent" style="border-radius:0 0 7px 7px !important; padding-right: 50px; display:none;">

                    <table width="100%" border="0" cellspacing="0" id="traffic_sources_table">
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
                            if (is_array($trafficSources) && count($trafficSources) > 0) {
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
                                ?>
                                <tr>
                                    <td colspan="5" class="values" style="text-align:center; color:#666; padding:20px;">
                                        No traffic sources data available
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
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