<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpvivid.com
 * @since      0.9.1
 *
 * @package    WPvivid
 * @subpackage WPvivid/admin/partials
 */

include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-backup-restore-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-remote-storage-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-settings-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-schedule-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-website-info-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-logs-page-display.php';
include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-log-read-page-display.php';

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

global $wpvivid_plugin;
$schedule=WPvivid_Schedule::get_schedule();

do_action('show_notice');

?>

<?php

$page_array = array();
$page_array = apply_filters('wpvivid_add_tab_page', $page_array);
foreach ($page_array as $page_name){
    add_action('wpvivid_backuprestore_add_tab', $page_name['tab_func'], $page_name['index']);
    add_action('wpvivid_backuprestore_add_page', $page_name['page_func'], $page_name['index']);
}

function wpvivid_add_side_bar($html){
    $wpvivid_version = WPVIVID_PLUGIN_VERSION;
    $wpvivid_version = apply_filters('wpvivid_display_pro_version', $wpvivid_version);
    $join_pro_testing = '<div class="postbox">
                            <h2><a href="https://wpvivid.com/pro-version-beta-testing?utm_source=client_beta_testing&utm_medium=inner_link&utm_campaign=access" style="text-decoration: none;">WPvivid Backup Pro Beta Testing</a></h2>
                         </div>';
    $join_pro_testing = apply_filters('wpvivid_join_pro_testing', $join_pro_testing);

    $schedule_html = '';
    $schedule_html = apply_filters('wpvivid_schedule_module', $schedule_html);

    $html = '<div class="postbox">
                <h2><span>Current Version: '.$wpvivid_version.'</span></h2>
             </div>
             <div id="wpvivid_backup_schedule_part"></div>
             '.$join_pro_testing.$schedule_html.'
             <div class="postbox">
                <h2><span>Troubleshooting</span></h2>
                <div class="inside">
                    <table class="widefat" cellpadding="0">
                        <tbody>
                        <tr class="alternate">
                            <td class="row-title">Read <a href="https://wpvivid.com/troubleshooting-issues-wpvivid-backup-plugin" target="_blank">Troubleshooting page</a> for faster solutions.</td>
                        </tr>
                        <tr>
                            <td class="row-title">Adjust <a href="https://wpvivid.com/wpvivid-backup-plugin-advanced-settings.html" target="_blank">Advanced Settings</a> for higher task success rate.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
             </div>
             <div class="postbox">
                <h2><span>How-to</span></h2>
                <div class="inside">
                    <table class="widefat" cellpadding="0">
                        <tbody>
                            <tr class="alternate"><td class="row-title"><a href="https://wpvivid.com/get-started-settings.html" target="_blank">WPvivid Backup Settings</a></td></tr>
                            <tr><td class="row-title"><a href="https://wpvivid.com/get-started-create-a-manual-backup.html" target="_blank">Create a Manual Backup</a></td></tr>
                            <tr class="alternate"><td class="row-title"><a href="https://wpvivid.com/get-started-restore-site.html" target="_blank">Restore Your Site from a Backup</a></td></tr>
                            <tr><td class="row-title"><a href="https://wpvivid.com/get-started-transfer-site.html" target="_blank">Migrate WordPress</a></td></tr>
                        </tbody>
                    </table>
                </div>
             </div>';
    return $html;
}

add_filter('wpvivid_add_side_bar', 'wpvivid_add_side_bar', 10);

?>

<div class="wrap">
    <h1><?php
        $plugin_display_name = 'WPvivid Backup Plugin';
        $plugin_display_name = apply_filters('wpvivid_display_pro_name', $plugin_display_name);
        _e($plugin_display_name);
        ?></h1>
    <div id="wpvivid_backup_notice">
        <?php
        if($schedule['enable'] == true) {
            if($schedule['backup']['remote'] === 1)
            {
                $remoteslist=WPvivid_Setting::get_all_remote_options();
                $default_remote_storage='';
                foreach ($remoteslist['remote_selected'] as $value)
                {
                    $default_remote_storage=$value;
                }
                if($default_remote_storage == ''){
                    _e('<div class="notice notice-warning is-dismissible"><p>Warning: There is no default remote storage available for the scheduled backups, please set up it first.</p></div>');
                }
            }
        }
        ?>
    </div>
    <?php do_action('wpvivid_add_schedule_notice'); ?>
    <div id="wpvivid_remote_notice"></div>
</div>
<h2 class="nav-tab-wrapper">
    <?php
    do_action('wpvivid_backuprestore_add_tab');
    ?>
</h2>
<div class="wrap" style="max-width:1720px;">
    <div id="poststuff" style="padding-top: 0;">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="inside" style="margin-top:0;">
                    <?php
                    do_action('wpvivid_backuprestore_add_page');
                    ?>
                </div>
            </div>

            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <?php
                    $html = '';
                    echo apply_filters('wpvivid_add_side_bar' ,$html);
                    ?>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>

<script>
    function switchTabs(evt,contentName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("wrap-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="wrap-nav-tab" and remove the class "active"
        tablinks = document.getElementsByClassName("wrap-nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
        }

        // Show the current tab, and add an "nav-tab-active" class to the button that opened the tab
        document.getElementById(contentName).style.display = "block";
        evt.currentTarget.className += " nav-tab-active";
        jQuery( document ).trigger( 'wpvivid-switch-tabs', contentName );
    }
    function switchrestoreTabs(evt,contentName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="table-list-content" and hide them
        tabcontent = document.getElementsByClassName("backup-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="table-nav-tab" and remove the class "nav-tab-active"
        tablinks = document.getElementsByClassName("backup-nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
        }

        // Show the current tab, and add an "storage-menu-active" class to the button that opened the tab
        document.getElementById(contentName).style.display = "block";
        evt.currentTarget.className += " nav-tab-active";
    }
    function switchsettingTabs(evt,contentName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="table-list-content" and hide them
        tabcontent = document.getElementsByClassName("setting-tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="table-nav-tab" and remove the class "nav-tab-active"
        tablinks = document.getElementsByClassName("setting-nav-tab");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
        }

        // Show the current tab, and add an "storage-menu-active" class to the button that opened the tab
        document.getElementById(contentName).style.display = "block";
        evt.currentTarget.className += " nav-tab-active";
    }
    function wpvivid_getrequest()
    {
        wpvivid_click_switch_page('wrap', wpvivid_page_request, false);
    }

    function wpvivid_task_monitor()
    {
        setTimeout(function () {
            wpvivid_task_monitor();
        }, 120000);

        var ajax_data = {
            'action': 'wpvivid_task_monitor'
        };

        wpvivid_post_request(ajax_data, function (data)
        {
        },function (XMLHttpRequest, textStatus, errorThrown)
        {
        });
    }

    jQuery(document).ready(function ()
    {
        wpvivid_getrequest();
        wpvivid_task_monitor();
        <?php
        $default_task_type = array();
        $default_task_type = apply_filters('wpvivid_get_task_type', $default_task_type);
        if(empty($default_task_type)){
        ?>
        wpvivid_activate_cron();
        wpvivid_manage_task();
        <?php
        }
        ?>
    });

</script>