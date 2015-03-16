<?php 
	

	/**
	 * 设置config文件
	 * @param $config 配属信息
	 * @param $filename 要配置的文件名称
	 */
	function set_config($config, $filename='config') {
		$configfile = SUR_PATH.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$filename.'.inc.php';
		if(!is_writable($configfile)) showmessage('Please chmod '.$configfile.' to 0777 !');
		$pattern = $replacement = array();
		foreach($config as $k=>$v) {
			if(in_array($k,array('JS_PATH','CSS_PATH','IMG_PATH','UPLOAD_URL','ADMIN_LOG','ERRORLOG','ERRORLOG_SIZE','MAXLOGINFAILEDTIMES'))) {
				$v = trim($v);
				$configs[$k] = $v;
				$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
	        	$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";					
			}
		}
		$str = file_get_contents($configfile);
		$str = preg_replace($pattern, $replacement, $str);
		return file_put_contents($configfile, $str);		
	}
	
	/**
	 * 获取系统信息
	 */
	function get_sysinfo() {
		$sys_info['os']             = PHP_OS;
		$sys_info['zlib']           = function_exists('gzclose');//zlib
		$sys_info['safe_mode']      = (boolean) ini_get('safe_mode');//safe_mode = Off
		$sys_info['safe_mode_gid']  = (boolean) ini_get('safe_mode_gid');//safe_mode_gid = Off
		$sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no_setting');
		$sys_info['socket']         = function_exists('fsockopen') ;
		$sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
		$sys_info['phpv']           = phpversion();	
		$sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
		return $sys_info;
	}
	
	/**
	 * 检查目录可写性
	 * @param $dir 目录路径
	 */
	function dir_writeable($dir) {
		$writeable = 0;
		if(is_dir($dir)) {  
	        if($fp = @fopen("$dir/chkdir.test", 'w')) {
	            @fclose($fp);      
	            @unlink("$dir/chkdir.test"); 
	            $writeable = 1;
	        } else {
	            $writeable = 0; 
	        } 
		}
		return $writeable;
	}
	
	/**
	 * 返回错误日志大小，单位MB
	 */
	function errorlog_size() {
		$logfile = CACHE_PATH.'error_log.php';
		if(file_exists($logfile)) {
			return $logsize = 0;
		} 
		return 0;
	}

?>