<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-zipclass.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-backup.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-rollback.php';
class WPvivid_RestoreSite
{

    public function restore($option,$files)
    {
        global $wpvivid_plugin;
        if(isset($option['has_child']))
        {
            $root_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();

            if(!file_exists($root_path))
            {
                @mkdir($root_path);
            }
            $wpvivid_plugin->restore_data->write_log('extract root:'.$root_path,'notice');
            $zip = new WPvivid_ZipClass();
            $all_files = array();
            foreach ($files as $file)
            {
                $all_files[] =WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.$file;
            }

            if(isset($option['extract_child_files']))
            {
                return $zip -> extract_ex($all_files,$root_path,$option['extract_child_files']);
            }
            else
            {
                return $zip -> extract($all_files,$root_path);
            }
        }
        else
        {
            $is_type_db = false;
            $is_type_db = apply_filters('wpvivid_check_type_database', $is_type_db, $option);
            if($is_type_db){
                $path = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_DEFAULT_ROLLBACK_DIR.DIRECTORY_SEPARATOR.'wpvivid_old_database';
                if(file_exists($path))
                {
                    @mkdir($path);
                }

                $zip = new WPvivid_ZipClass();
                $all_files = array();
                foreach ($files as $file)
                {
                    $all_files[] = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.$file;
                }

                $ret= $zip -> extract($all_files,$path);

                unset($zip);
            }
            else {
                $root_path = '';
                if (isset($option['root'])) {
                    $root_path = $this->transfer_path(get_home_path() . $option['root']);
                } else if (isset($option['root_flag'])) {
                    if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_WP_CONTENT) {
                        $root_path = $this->transfer_path(WP_CONTENT_DIR);
                    } else if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_CUSTOM) {
                        $root_path = $this->transfer_path(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . WPvivid_Setting::get_backupdir());
                    } else if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_WP_ROOT) {
                        $root_path = $this->transfer_path(ABSPATH);
                    }
                }

                $root_path = rtrim($root_path, '/');
                $root_path = rtrim($root_path, DIRECTORY_SEPARATOR);

                $exclude_path[] = $this->transfer_path(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . WPvivid_Setting::get_backupdir());

                if (isset($option['include_path'])) {
                    $include_path = $option['include_path'];
                } else {
                    $include_path = array();
                }

                $zip = new WPvivid_ZipClass();
                $all_files = array();
                foreach ($files as $file) {
                    $all_files[] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . WPvivid_Setting::get_backupdir() . DIRECTORY_SEPARATOR . $file;
                }

                $wpvivid_plugin->restore_data->write_log('restore from files:' . json_encode($all_files), 'notice');

                $ret = $zip->extract($all_files, $root_path);

                if (isset($option['file_type'])) {
                    if ($option['file_type'] == 'themes') {
                        if (isset($option['remove_themes'])) {
                            foreach ($option['remove_themes'] as $slug => $themes) {
                                if (empty($slug))
                                    continue;
                                $wpvivid_plugin->restore_data->write_log('remove ' . get_theme_root() . DIRECTORY_SEPARATOR . $slug, 'notice');
                                $this->delTree(get_theme_root() . DIRECTORY_SEPARATOR . $slug);
                            }
                        }
                    } else if ($option['file_type'] == 'plugin') {
                        if (isset($option['remove_plugin'])) {
                            foreach ($option['remove_plugin'] as $slug => $plugin) {
                                if (empty($slug))
                                    continue;
                                $wpvivid_plugin->restore_data->write_log('remove ' . WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug, 'notice');
                                $this->delTree(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug);
                            }
                        }
                    }
                }


                unset($zip);

                if (isset($option['wp_core']) && isset($option['is_migrate'])) {
                    if ($option['is_migrate'] == 1) {
                        if (function_exists('save_mod_rewrite_rules')) {
                            if (file_exists(get_home_path() . '.htaccess')) {
                                $htaccess_data = file_get_contents(get_home_path() . '.htaccess');
                                $line = '';
                                if (preg_match('#AddHandler application/x-httpd-php.*#', $htaccess_data, $matcher)) {
                                    $line = PHP_EOL . $matcher[0];

                                    if (preg_match('#<IfModule mod_suphp.c>#', $htaccess_data, $matcher)) {
                                        $line .= PHP_EOL . '<IfModule mod_suphp.c>';
                                        if (preg_match('#suPHP_ConfigPath .*#', $htaccess_data, $matcher)) {
                                            $line .= PHP_EOL . $matcher[0];
                                        }
                                        $line .= PHP_EOL . '</IfModule>';
                                    }
                                    $wpvivid_plugin->restore_data->write_log('find php selector:' . $line, 'notice');
                                }
                                @rename(get_home_path() . '.htaccess', get_home_path() . '.htaccess_old');
                                save_mod_rewrite_rules();
                                if (!empty($line))
                                    file_put_contents(get_home_path() . '.htaccess', $line, FILE_APPEND);
                            }
                        }
                        WPvivid_Setting::update_option('wpvivid_migrate_status', 'completed');
                    }
                }
            }
            return $ret;
        }
    }

    public function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file)
        {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function pre_restore($path,$save_old_path,$include_path,$exclude_path)
    {
        if(!file_exists($save_old_path))
            @mkdir($save_old_path);
        return $this->copy_old_files($path,$save_old_path,$path,$include_path,$exclude_path);
    }

    private function copy_old_files($path,$save_old_path,$root_path,$include_path,$exclude_path)
    {
        $result = array('result'=>WPVIVID_SUCCESS);

        $handler=opendir($path);
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                $new_dir=$save_old_path.str_replace($root_path,'',$path.DIRECTORY_SEPARATOR.$filename);
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename))
                {
                    if(!empty($include_path))
                    {
                        if( $this->has_include_dir($path.DIRECTORY_SEPARATOR.$filename,$include_path,$root_path))
                        {
                            if(!file_exists($new_dir))
                                @mkdir($new_dir);
                            $this->copy_old_files($path.DIRECTORY_SEPARATOR.$filename,$save_old_path,$root_path,$include_path,$exclude_path);
                        }
                        else
                        {
                            continue;
                        }
                    }

                    if(!empty($exclude_path))
                    {
                        $check_path=$this->transfer_path($path.DIRECTORY_SEPARATOR.$filename);
                        if(in_array($check_path,$exclude_path))
                        {
                            continue;
                        }
                    }

                    if(!file_exists($new_dir))
                        @mkdir($new_dir);
                    $this->copy_old_files($path.DIRECTORY_SEPARATOR.$filename,$save_old_path,$root_path,$include_path,$exclude_path);
                }else {
                    if(!file_exists($new_dir))
                    {
                        @copy($path.DIRECTORY_SEPARATOR.$filename,$new_dir);
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        return $result;
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    private function has_include_dir($path,$include_path,$root)
    {
        $path=$this->transfer_path($path);
        foreach ($include_path as $needed_path)
        {
            $needed_path=$this->transfer_path($root.DIRECTORY_SEPARATOR.$needed_path);
            if(strpos($path,$needed_path)!==false)
            {
                return true;
            }
        }
        return false;
    }

    private function _restore($restorePath,$path , $files){
        $zip = new WPvivid_ZipClass();
        $allfiles = array();
        foreach ($files as $file){
            $allfiles[] = $path.DIRECTORY_SEPARATOR.$file['file_name'];
        }
        return $zip -> zipextract($restorePath , $allfiles);
    }

    public function restore_copy($data){
        $src_path = $data['data']['src'];
        $dst_path = $data['data']['dst'];
        $replace_path = $data['data']['replace'];
        return $this -> _restore_copy_loop($src_path,$dst_path,$replace_path);
    }
    private function _restore_copy_loop($path,$temp_path,$replace_path){
        $result = array('result'=>WPVIVID_SUCCESS);
        if(empty($path)) {
            return array('result'=>'failed','error'=>'The old folder not found. It may be deleted, renamed, or moved. Please verify the folder exists.');
        }
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename))
                {
                    @mkdir(str_replace($replace_path,$temp_path,$path.DIRECTORY_SEPARATOR.$filename));
                    $result = $this->_restore_copy_loop($path.DIRECTORY_SEPARATOR.$filename,$temp_path,$replace_path);
                    if($result['result'] != WPVIVID_SUCCESS)
                        break;
                }else{
                    if(file_exists($path.DIRECTORY_SEPARATOR.$filename))
                    {
                        if(!copy($path.DIRECTORY_SEPARATOR.$filename,str_replace($replace_path,$temp_path,$path.DIRECTORY_SEPARATOR.$filename))){
                            $result = array('result'=>'failed','error'=>'Copying '.$path.DIRECTORY_SEPARATOR.$filename.' into '.$temp_path.DIRECTORY_SEPARATOR.$filename.' failed. The file may be occupied, or the folder may not be granted a permission to write. Please try again.');
                            break;
                        }
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        return $result;
    }
}