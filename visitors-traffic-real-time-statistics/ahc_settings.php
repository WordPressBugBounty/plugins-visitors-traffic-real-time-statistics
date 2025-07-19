<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly			
?>
<script language="javascript" type="text/javascript">
    function imgFlagError(image) {
        image.onerror = "";
        image.src = "<?php echo plugins_url('images/flags/noFlag.png', AHCFREE_PLUGIN_MAIN_FILE) ?>";
        return true;
    }
</script>
<style type="text/css">
    .ahc_main_container {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        padding: 20px;
    }

    .ahc-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .ahc-header img {
        margin-right: 15px;
    }

    .ahc-header h1 {
        margin: 0;
        font-size: 24px;
        color: #23282d;
        font-weight: 600;
    }

    .ahc-settings-icon {
        margin-left: auto;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .ahc-settings-icon:hover {
        background-color: #f0f0f0;
    }

    .panel {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .panel-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .panelcontent {
        padding: 30px;
    }

    .settings-section {
        margin-bottom: 35px;
        padding-bottom: 25px;
        border-bottom: 1px solid #e5e5e5;
    }

    .settings-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #23282d;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin-right: 12px;
        border-radius: 2px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        align-items: flex-start;
    }

    .form-group {
        flex: 1;
    }

    .form-group.half-width {
        flex: 0 0 48%;
    }

    .form-group.quarter-width {
        flex: 0 0 23%;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-help {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
        font-style: italic;
    }

    .timezone-note {
        background: #e8f5e8;
        color: #2d5016;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 13px;
        border-left: 4px solid #4caf50;
    }

    .checkbox-container {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
    }

    .checkbox-container:hover {
        background: #f0f2f5;
    }

    .checkbox-container input[type="checkbox"] {
        margin: 0;
        transform: scale(1.2);
        accent-color: #667eea;
    }

    .checkbox-label {
        flex: 1;
        font-size: 14px;
        color: #333;
        line-height: 1.4;
    }

    .checkbox-label.warning {
        color: #d32f2f;
        font-weight: 500;
    }

    .select-container {
        position: relative;
    }

    .multi-select {
        min-height: 52px;
        padding: 10px;

    }

    .button-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 1px solid #e5e5e5;
    }

    .btn {
        padding: 15px 30px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        min-width: 150px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #333;
        border: 2px solid #e9ecef;
    }

    .btn-secondary:hover {
        background: #e9ecef;
        transform: translateY(-1px);
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-radius: 6px;
        border-left: 4px solid #28a745;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .success-message::before {
        content: '✓';
        font-weight: bold;
        font-size: 18px;
    }

    .success-links {
        margin-top: 10px;
    }

    .success-links a {
        color: #155724;
        text-decoration: none;
        font-weight: 500;
        margin-right: 15px;
    }

    .success-links a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .form-group.half-width,
        .form-group.quarter-width {
            flex: 1;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>

<?php
$msg = '';
$save_btn = (isset($_POST['save'])) ? sanitize_text_field($_POST['save']) : '';
$saved_suc = false;

if (!empty($save_btn)) {
    $verify = isset($_POST['ahc_settings_send']) ? wp_verify_nonce(sanitize_text_field($_POST['ahc_settings_send']), 'ahc_settings_action') : false;

    if ($verify && current_user_can('manage_options')) {
        if (ahcfree_savesettings()) {
            $saved_suc = true;
        }
    }
}

$ahcfree_get_save_settings = ahcfree_get_save_settings();
$hits_days = $ahcfree_get_save_settings[0]->set_hits_days;
$ajax_check = ($ahcfree_get_save_settings[0]->set_ajax_check * 1000);
$set_ips = $ahcfree_get_save_settings[0]->set_ips;
if ($set_ips != '') {
    $set_ips = str_ireplace(' ', '&#10;', $set_ips);
}

$delete_plugin_data = get_option('ahcfree_delete_plugin_data_on_uninstall');
$ahcfree_hide_top_bar_icon = get_option('ahcfree_hide_top_bar_icon');
$ahcproExcludeRoles = get_option('ahcproExcludeRoles');
$ahcfree_ahcfree_haships = get_option('ahcfree_ahcfree_haships');
$ahcfree_save_ips = get_option('ahcfree_save_ips_opn');
$ahcproUserRoles = get_option('ahcproUserRoles');
$ahcproRobots = get_option('ahcproRobots');
?>

<div class="ahc_main_container">
    <!-- Header -->
    <div class="ahc-header">
        <img width="40px" src="<?php echo esc_url(plugins_url('images/logo.png', AHCFREE_PLUGIN_MAIN_FILE)); ?>"
            alt="Plugin Logo">
        <h1>Visitor Traffic Real Time Statistics - Settings</h1>
        <a class="ahc-settings-icon" href="admin.php?page=ahc_hits_counter_menu_free" title="Back to Dashboard">
            <img src="<?php echo esc_url(plugins_url('images/settings.jpg', AHCFREE_PLUGIN_MAIN_FILE)); ?>"
                alt="Dashboard" />
        </a>
    </div>

    <!-- Success Message -->
    <?php if ($saved_suc) : ?>
        <div class="success-message">
            Settings saved successfully!
            <div class="success-links">
                <a href="admin.php?page=ahc_hits_counter_settings">Reload Settings</a>
                <a href="admin.php?page=ahc_hits_counter_menu_free">Back to Dashboard</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Settings Panel -->
    <div class="panel">
        <h2 class="panel-header">Plugin Configuration</h2>
        <div class="panelcontent">
            <form method="post" enctype="multipart/form-data" name="myform">
                <?php $nonce = wp_create_nonce('ahc_settings_action'); ?>
                <input type="hidden" name="ahc_settings_send" value="<?php echo esc_attr($nonce); ?>" />

                <!-- Statistics Settings -->
                <div class="settings-section">
                    <h3 class="section-title">Statistics Configuration</h3>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label class="form-label" for="set_hits_days">Statistics Time Range (Days)</label>
                            <input type="number" value="<?php echo esc_attr($hits_days); ?>" class="form-control"
                                id="set_hits_days" name="set_hits_days" min="1" max="365" placeholder="14">
                            <div class="form-help">Number of days to display in statistics charts (default: 14 days)
                            </div>
                        </div>

                        <div class="form-group quarter-width">
                            <label class="form-label" for="set_custom_timezone">Timezone</label>
                            <select class="form-control" id="set_custom_timezone" name="set_custom_timezone">
                                <?php
                                $wp_timezone_string = get_option('timezone_string');
                                $custom_timezone_offset = (get_option('ahcfree_custom_timezone') != '') ? get_option('ahcfree_custom_timezone') : $wp_timezone_string;
                                $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                foreach ($timezones as $key => $value) {
                                ?>
                                    <option value="<?php echo esc_attr($value); ?>"
                                        <?php echo ($value == $custom_timezone_offset) ? 'selected' : ''; ?>>
                                        <?php echo esc_html($value); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group quarter-width">
                            <div class="timezone-note">
                                <strong>Note:</strong> Timezone is automatically fetched from WordPress general
                                settings.
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label class="form-label" for="set_ajax_check">Online Users Check Interval (Seconds)</label>
                            <input type="number" value="<?php echo esc_attr(intval($ajax_check) / 1000); ?>"
                                class="form-control" id="set_ajax_check" name="set_ajax_check" min="5" max="300"
                                placeholder="10">
                            <div class="form-help">How often to check for online users (default: 10 seconds)</div>
                        </div>
                    </div>
                </div>

                <!-- Exclusion Settings -->
                <div class="settings-section">
                    <h3 class="section-title">Tracking Exclusions</h3>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label class="form-label" for="set_ips">IP Addresses to Exclude</label>
                            <textarea placeholder="192.168.1.1&#10;192.168.1.2&#10;One IP per line" name="set_ips"
                                id="set_ips" rows="4" class="form-control"><?php echo esc_html($set_ips); ?></textarea>
                            <div class="form-help">Excluded IP addresses will not be tracked. Enter one IP address per
                                line.</div>
                        </div>

                        <div class="form-group half-width">
                            <label class="form-label" for="ahcproExcludeRoles">User Roles to Exclude from
                                Statistics</label>
                            <div class="select-container">
                                <select id="ahcproExcludeRoles" name="ahcproExcludeRoles[]" multiple="true"
                                    class="multi-select">
                                    <?php
                                    global $wp_roles;
                                    if (!isset($wp_roles)) $wp_roles = new WP_Roles();
                                    $available_roles_names = $wp_roles->get_names();
                                    $available_roles_capable = array();

                                    foreach ($available_roles_names as $role_key => $role_name) {
                                        $available_roles_capable[$role_key] = $role_name;
                                    }

                                    $UserRoles = get_option('ahcproExcludeRoles');
                                    $UserRoles_arr = explode(',', $UserRoles);

                                    foreach ($available_roles_capable as $role) {
                                        $translated_role_name = $role;
                                        $selected_value = in_array($translated_role_name, $UserRoles_arr) ? 'selected=selected' : '';
                                    ?>
                                        <option <?php echo $selected_value; ?>
                                            value="<?php echo esc_attr($translated_role_name); ?>">
                                            <?php echo esc_html($translated_role_name); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-help">Selected user roles will be excluded from visitor tracking</div>
                        </div>
                    </div>
                </div>

                <!-- Access Control -->
                <div class="settings-section">
                    <h3 class="section-title">Access Control</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="ahcproUserRoles">Plugin Access Permissions</label>
                            <div class="select-container">
                                <select id="ahcproUserRoles" name="ahcproUserRoles[]" multiple="true"
                                    class="multi-select">
                                    <?php
                                    $UserRoles = get_option('ahcproUserRoles');
                                    $UserRoles_arr = explode(',', $UserRoles);

                                    foreach ($available_roles_capable as $role) {
                                        $translated_role_name = $role;
                                        $is_admin = ($translated_role_name == 'Administrator' || $translated_role_name == 'Super Admin');
                                        $selected_value = (in_array($translated_role_name, $UserRoles_arr) || $is_admin) ? 'selected=selected' : '';
                                    ?>
                                        <option <?php echo $selected_value; ?>
                                            value="<?php echo esc_attr($translated_role_name); ?>">
                                            <?php echo esc_html($translated_role_name); ?>
                                            <?php echo $is_admin ? ' (Always Enabled)' : ''; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-help">Select which user roles can access the plugin dashboard and
                                statistics</div>
                        </div>
                    </div>
                </div>

                <!-- Privacy & Display Options -->
                <div class="settings-section">
                    <h3 class="section-title">Privacy & Display Options</h3>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <div class="checkbox-container">
                                <input type="checkbox" id="ahcfree_hide_top_bar_icon" value="1"
                                    name="ahcfree_hide_top_bar_icon"
                                    <?php echo ($ahcfree_hide_top_bar_icon == 1) ? 'checked=checked' : ''; ?>>
                                <label class="checkbox-label" for="ahcfree_hide_top_bar_icon">
                                    <strong>Hide Top Bar Icon</strong><br>
                                    Remove the plugin icon from the WordPress admin top bar
                                </label>
                            </div>
                        </div>

                        <div class="form-group half-width">
                            <div class="checkbox-container">
                                <input type="checkbox" id="ahcfree_ahcfree_haships" value="1"
                                    name="ahcfree_ahcfree_haships"
                                    <?php echo ($ahcfree_ahcfree_haships == 1) ? 'checked=checked' : ''; ?>>
                                <label class="checkbox-label" for="ahcfree_ahcfree_haships">
                                    <strong>Hash IP Addresses</strong><br>
                                    Hide the last 3 digits of all IP addresses for privacy protection
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Management -->
                <div class="settings-section">
                    <h3 class="section-title">Data Management</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <div class="checkbox-container">
                                <input type="checkbox" id="delete_plugin_data" value="1" name="delete_plugin_data"
                                    <?php echo ($delete_plugin_data == 1) ? 'checked=checked' : ''; ?>>
                                <label class="checkbox-label warning" for="delete_plugin_data">
                                    <strong>⚠️ Delete All Data on Uninstall</strong><br>
                                    WARNING: All statistics data will be permanently deleted when the plugin is
                                    uninstalled. This action cannot be undone.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="button-group">
                    <button type="submit" name="save" value="save settings" class="btn btn-primary">
                        Save Settings
                    </button>
                    <a href="admin.php?page=ahc_hits_counter_menu_free" class="btn btn-secondary">
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script language="javascript" type="text/javascript">
    jQuery(document).ready(function() {
        // Initialize SlimSelect for multi-select dropdowns
        if (typeof SlimSelect !== 'undefined') {
            new SlimSelect({
                select: '#ahcproExcludeRoles',
                settings: {
                    placeholderText: 'Select roles to exclude...',
                    searchText: 'Search roles...',
                    searchPlaceholder: 'Search...'
                }
            });

            new SlimSelect({
                select: '#ahcproUserRoles',
                settings: {
                    placeholderText: 'Select roles with access...',
                    searchText: 'Search roles...',
                    searchPlaceholder: 'Search...'
                }
            });
        }

        // Add form validation
        jQuery('form[name="myform"]').on('submit', function(e) {
            var hits_days = parseInt(jQuery('#set_hits_days').val());
            var ajax_check = parseInt(jQuery('#set_ajax_check').val());

            if (hits_days < 1 || hits_days > 365) {
                alert('Statistics time range must be between 1 and 365 days.');
                e.preventDefault();
                return false;
            }

            if (ajax_check < 5 || ajax_check > 300) {
                alert('Online users check interval must be between 5 and 300 seconds.');
                e.preventDefault();
                return false;
            }
        });
    });
</script>