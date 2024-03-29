<?php

function wpvivid_schedule_settings()
{
    ?>
    <tr>
        <td class="row-title wpvivid-backup-settings-table tablelistcolumn"><label for="tablecell"><?php _e('Schedule Settings', 'wpvivid'); ?></label></td>
        <td class="tablelistcolumn">
            <div id="storage-brand-3">
                <div>
                    <div>
                        <div class="postbox schedule-tab-block">
                            <label for="wpvivid_schedule_enable">
                                <input option="schedule" name="enable" type="checkbox" id="wpvivid_schedule_enable" />
                                <span><?php _e( 'Enable backup schedule', 'wpvivid' ); ?></span>
                            </label><br>
                            <label>
                                <div style="float: left;">
                                    <input type="checkbox" disabled />
                                    <span style="color: #ddd;">Advanced Schedule</span>
                                </div>
                                <div style="float: left; height: 32px; line-height: 32px;">
                                    <span class="wpvivid-feature-pro">
                                        <a href="https://wpvivid.com/wpvivid-backup-pro-schedule-overview?utm_source=client_advanced_schedule&utm_medium=inner_link&utm_campaign=access">Pro feature: learn more</a>
                                    </span>
                                </div>
                            </label><br>
                            <div style="clear: both;"></div>
                            <div>
                                <?php
                                $notice='';
                                $notice= apply_filters('wpvivid_schedule_notice',$notice);
                                echo $notice;
                                ?>
                            </div>
                        </div>
                        <div class="postbox schedule-tab-block">
                            <fieldset>
                                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                                <?php
                                $time='';
                                $time= apply_filters('wpvivid_schedule_time',$time);
                                echo $time;
                                ?>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="postbox schedule-tab-block" id="wpvivid_schedule_backup_type">
                    <div>
                        <div>
                            <fieldset>
                                <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                                <?php
                                $backup_type='';
                                $backup_type= apply_filters('wpvivid_schedule_backup_type',$backup_type);
                                echo $backup_type;
                                ?>
                            </fieldset>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                </div>
                <div class="postbox schedule-tab-block" id="wpvivid_schedule_remote_storage">
                    <div id="wpvivid_schedule_backup_local_remote">
                        <?php
                        $html='';
                        $html= apply_filters('wpvivid_schedule_local_remote',$html);
                        echo $html;
                        ?>
                    </div>
                    <div id="schedule_upload_storage" style="cursor:pointer;" title="Highlighted icon illuminates that you have choosed a remote storage to store backups">
                        <?php
                        $pic='';
                        $pic= apply_filters('wpvivid_schedule_add_remote_pic',$pic);
                        echo $pic;
                        ?>
                    </div>
                </div>
                <div class="postbox schedule-tab-block">
                    <div style="float:left; color: #ddd; margin-right: 10px;">
                        + Add another schedule
                    </div>
                    <span class="wpvivid-feature-pro">
                        <a href="https://wpvivid.com/wpvivid-backup-pro-creating-schedules?utm_source=client_multi_schedule&utm_medium=inner_link&utm_campaign=access">Pro feature: learn more</a>
                    </span>
                </div>
            </div>
        </td>
    </tr>
    <script>
        <?php
        do_action('wpvivid_schedule_do_js');
        ?>
    </script>
    <?php
}

function wpvivid_schedule_notice($html)
{
    $html='<p>1) Scheduled job will start at web server time: </p>';
    $html.='<p>2) Being subjected to mechanisms of PHP, a scheduled backup task for your site will be triggered only when the site receives at least a visit at any page.</p>';
    return $html;
}

function wpvivid_schedule_backup_type($html)
{
    $html='<label>';
    $html.='<input type="radio" option="schedule" name="backup_type" value="files+db"/>';
    $html.='<span>Database + Files (WordPress Files)</span>';
    $html.='</label><br>';

    $html.='<label>';
    $html.='<input type="radio" option="schedule" name="backup_type" value="files"/>';
    $html.='<span>WordPress Files (Exclude Database)</span>';
    $html.='</label><br>';

    $html.='<label>';
    $html.='<input type="radio" option="schedule" name="backup_type" value="db"/>';
    $html.='<span>Only Database</span>';
    $html.='</label><br>';

    $html.='<label>';
    $html.='<div style="float: left;">';
    $html.='<input type="radio" disabled />';
    $html.='<span style="color: #ddd;">'.__('Custom').'</span>';
    $html.='</div>';
    $html.='<div style="float: left; height: 32px; line-height: 32px;">';
    $html.='<span class="wpvivid-feature-pro">';
    $html.='<a href="https://wpvivid.com/wpvivid-backup-pro-customize-what-to-backup-for-schedule?utm_source=client_schedule_custom_backup&utm_medium=inner_link&utm_campaign=access" style="text-decoration: none;">Pro feature: learn more</a>';
    $html.='</span>';
    $html.='</div>';
    $html.='</label><br>';
    return $html;
}

function wpvivid_schedule_do_js()
{
    $schedule=WPvivid_Schedule::get_schedule();
    if($schedule['enable'] == true)
    {
        ?>
        jQuery("#wpvivid_schedule_enable").prop('checked', true);
        <?php
        if($schedule['backup']['remote'] === 1)
        {
            $schedule_remote='remote';
        }
        else{
            $schedule_remote='local';
        }
    }
    else{
        $schedule['recurrence']='wpvivid_daily';
        $schedule['backup']['backup_files']='files+db';
        $schedule_remote='local';
    }
    ?>
    jQuery("input:radio[value='<?php echo $schedule['recurrence']?>']").prop('checked', true);
    jQuery("input:radio[value='<?php echo $schedule['backup']['backup_files']?>']").prop('checked', true);
    jQuery("input:radio[name='save_local_remote'][value='remote']").click(function()
    {
    <?php
    $remote_id_array = WPvivid_Setting::get_user_history('remote_selected');
    $remote_id = '';
    foreach ($remote_id_array as $value)
    {
        $remote_id = $value;
    }
    if(empty($remote_id))
    {
        ?>
        alert("There is no default remote storage configured. Please set it up first.");
        jQuery("input:radio[name='save_local_remote'][value='local']").prop('checked', true);
        <?php
    }
    ?>
    });
    <?php
}

add_action('wpvivid_schedule_add_cell','wpvivid_schedule_settings',11);
add_action('wpvivid_schedule_do_js','wpvivid_schedule_do_js',10);
add_filter('wpvivid_schedule_backup_type','wpvivid_schedule_backup_type');
add_filter('wpvivid_schedule_notice','wpvivid_schedule_notice',10);
?>

