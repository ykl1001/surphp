<?php
/*
 * Created on 2013-4-13
 *
 * @author yangkunlin
 * @copyright surphp@yangkunlin 2013
 */
 
define('CORE_PATH',dirname(__FILE__). DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR); 
define('SUR_PATH',substr(CORE_PATH,0,strlen(CORE_PATH)-5));
define('CACHE_PATH', SAE_TMP_PATH.'caches'.DIRECTORY_SEPARATOR);
define('ADDON_PATH', SUR_PATH.'addons'.DIRECTORY_SEPARATOR);

//设置本地时差
function_exists('date_default_timezone_set') && date_default_timezone_set(surphp::load_config('config','TIMEZONE'));
define('SYS_TIME', time());
//当前访问的主机名
define('SITE_URL', (isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'].'/' : surphp::load_config('config','SITE_URL')));
//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

header('Content-type: text/html; charset=UTF-8');

//js 路径
define('JS_PATH',surphp::load_config('config','JS_PATH'));
//css 路径
define('CSS_PATH',surphp::load_config('config','CSS_PATH'));
//img 路径
define('IMG_PATH',surphp::load_config('config','IMG_PATH'));

// load golb function
surphp::load_core_func('global');
surphp::auto_load_func(); 

class surphp
{
	/**
	 * init app
	 */
	public static function run(){
		self::load_core_class('Model');
		self::load_core_class('dispatch',1);
	} 
	
	/**
	 * load class
	 */
	public static function load_core_class($classname, $initialize = 0){
		return self::_load_class($classname,'',$initialize);
	}
	
	public static function load_app_class($classname, $appname, $initialize = 0){
		$appname = 'apps'.DIRECTORY_SEPARATOR.$appname;
		return self::_load_class($classname, $appname,$initialize);
	}
	
	public static function load_model($model, $appname='', $initialize = 0){
		if ($appname) {
			$appname = 'apps'.DIRECTORY_SEPARATOR.$appname;
		}else {
			$appname = 'addons'.DIRECTORY_SEPARATOR.$appname;
		}
	    
	    return self::_load_model($model.'Model', $appname, $initialize);
	}
	
	public static function load_core_func($func){
		self::_load_func($func,'');
	}
	
	public static function load_app_func($func, $appname){
		$appname = 'apps'.DIRECTORY_SEPARATOR.$appname;
		self::_load_func($func, $appname);
	}
	
	public static function auto_load_func(){
		static $funcs;
		$dir = CORE_PATH.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'autoload';
		$files = scandir($dir);
		if(!empty($files)){
			foreach($files as $file){
				$gkey = md5('core.functions.autoload'.$file);
				if(!isset($funcs[$gkey]) && is_file($dir.DIRECTORY_SEPARATOR.$file)){
					include $dir.DIRECTORY_SEPARATOR.$file;
					$funcs[$gkey] = TRUE;
				}
				
			}
		}
	}
	
	public static function load_config($name, $key='', $default= ''){
	    static $configs;
	    if (isset($configs[$name])){
	        if ($key){
	            return isset($configs[$name][$key]) ? $configs[$name][$key] : $default;
	        } else {
	          return $configs[$name];  
	        }
	    }
	    $path = WEBROOT.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.$name.'.inc.php';
	    if (file_exists($path)){
	        $configs[$name] = include $path;
	        if ($key){
	            return isset($configs[$name][$key]) ? $configs[$name][$key] : $default;
	        }else{
	            return $configs[$name];
	        }
	    }
	    return $default;
	}
	
	private static function _load_class($classname, $appname,$initialize = 0){
		static $gclasses;
		if(!$appname) {
			$appname = 'core';
		}
		$classname = ucfirst($classname);
		$gkey = md5($appname.'.'.$classname);
		
		if(isset($gclasses[$gkey])){
			return $initialize ? new $classname  : TRUE;			
		}
		
		$path = SUR_PATH.$appname.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$classname.'.class.php';
		
		if(file_exists($path)){
			include $path;
			if($initialize){
				$gclasses[$gkey] = new $classname;
				return $gclasses[$gkey];
			}else {
			    $gclasses[$gkey] = true;
			}
			
			return true;
		}
		return false;
	}
	
	private static function _load_model($classname, $appname, $initialize = 0){
		static $gclasses;
		if(!$appname) {
			$appname = 'core';
		}
		$classname = ucfirst($classname);
		$gkey = md5($appname.'.'.$classname);
	
		if(isset($gclasses[$gkey])){
			return $initialize ? new $classname  : TRUE;
		}
	
		$path = SUR_PATH.$appname.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$classname.'.class.php';
		if(file_exists($path)){
			include $path;
			if($initialize){
				$gclasses[$gkey] = new $classname;
				return $gclasses[$gkey];
			}else {
				$gclasses[$gkey] = true;
			}
				
			return true;
		}
		return false;
	}	
	
	private static function _load_func($functionname, $appname){
		static $funcs;
		if (!$appname){
			$appname = 'core';
		}
		$gkey = md5($appname.'.'.$functionname);
		if (!isset($funcs[$gkey])){
			$path = SUR_PATH.$appname.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.$functionname.'.func.php';
			if (file_exists($path)){
				include $path;
				$funcs[$gkey] = TRUE;
			}else{
			    return FALSE;
			}					
		}
		return TRUE;
	}
} 