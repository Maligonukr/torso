<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

class WPvivid_tools
{
	public static function maintenance_on(){
		$myfile = fopen(ABSPATH.".maintenance", "w");
		$txt = '<?php $upgrading = time(); ?>';
		fwrite($myfile, $txt);
		fclose($myfile);
	}
	public static function maintenance_off(){
		@unlink(ABSPATH.".maintenance");
	}

	public static function recurse_copy($src,$dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		return array('result'=>WPVIVID_SUCCESS);
	}

    public static function clearcache($task_id)
    {
        $path = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false)
        {
            if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.$task_id.'#',$filename))
            {
                WPvivid_tools::deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
            }
            if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.$task_id.'#',$filename))
            {
                WPvivid_tools::deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
            }
            if(preg_match('#pclzip-.*\.tmp#', $filename)){
                @unlink($path.DIRECTORY_SEPARATOR.$filename);
            }
            if(preg_match('#pclzip-.*\.gz#', $filename)){
                @unlink($path.DIRECTORY_SEPARATOR.$filename);
            }
        }
        @closedir($handler);
    }

    public static function clean_junk_cache(){
        $home_url_prefix=get_home_url();
        $parse = parse_url($home_url_prefix);
        $tmppath=str_replace('/','_',$parse['path']);
        $home_url_prefix = $parse['host'].$tmppath;
        $path = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false)
        {
            if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.$home_url_prefix.'_'.'#',$filename))
            {
                WPvivid_tools::deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
            }
            if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.'#',$filename))
            {
                WPvivid_tools::deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
            }
            if(preg_match('#pclzip-.*\.tmp#', $filename)){
                @unlink($path.DIRECTORY_SEPARATOR.$filename);
            }
            if(preg_match('#pclzip-.*\.gz#', $filename)){
                @unlink($path.DIRECTORY_SEPARATOR.$filename);
            }
        }
        @closedir($handler);
    }

    public static function deldir($path,$exclude='',$flag = false)
    {
        if(!is_dir($path))
        {
            return ;
        }
        $handler=opendir($path);
        if(empty($handler))
            return ;
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename)){
                    if(empty($exclude)||WPvivid_tools::regex_match($exclude['directory'],$path.DIRECTORY_SEPARATOR.$filename ,0)){
                        self::deldir( $path.DIRECTORY_SEPARATOR.$filename ,$exclude, $flag);
                        @rmdir( $path.DIRECTORY_SEPARATOR.$filename );
                    }
                }else{
                    if(empty($exclude)||WPvivid_tools::regex_match($exclude['file'],$path.DIRECTORY_SEPARATOR.$filename ,0)){
                        @unlink($path.DIRECTORY_SEPARATOR.$filename);
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        if($flag)
            @rmdir($path);
    }

    public static function regex_match($regex_array,$string,$mode)
    {
        if(empty($regex_array))
        {
            return true;
        }

        if($mode==0)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return false;
                }
            }

            return true;
        }

        if($mode==1)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public static function delValueByKey($key , &$arr){
        $flag = true;
        if(!array_key_exists($key, $arr)){
            return false;
        }
        $keys = array_keys($arr);
        $index = array_search($key, $keys);
        if($index !== FALSE){
            array_splice($arr, $index, 1);
        }else{
            $flag = false;
        }
        return $flag;
    }

    public static function file_put_array($json,$file){
        file_put_contents($file,json_encode($json));
    }
    public static function  file_get_array($file){
        global $wpvivid_plugin;
        if(file_exists($file)){
            $get_file_ret = json_decode(file_get_contents($file),true);
            if(empty($get_file_ret)){
                $wpvivid_plugin->restore_data->write_log('Failed to decode restore data file.', 'notice');
            }
            return $get_file_ret;
        }else{
            $wpvivid_plugin->restore_data->write_log('Failed to open restore data file, the file may not exist.', 'notice');
            return array();
        }
    }

    public static function get_rollbackdata_file()
    {
        return WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_rollbackdata.txt';
    }
    public static function get_restoredata_file()
    {
        return WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_restoredata.txt';
    }
}