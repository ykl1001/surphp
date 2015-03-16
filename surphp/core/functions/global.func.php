<?php
/*
 * Created on 2013-4-13
 * global.func.php 公共函数库 *
 * @author yangkunlin
 * @copyright surphp@yangkunlin 2013
 */

/**
 * 返回经addslashes处理过的字符串或数组 — 使用反斜线引用字符串
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($data){
	if(is_array($data)){
	    foreach ($data as $key=>&$val){
	        $data[$key] = new_addslashes($val);
	    }
	    return $data;
	}
	
	return addslashes($data);
}

/**
 * 返回经stripslashes处理过的字符串或数组 — 反引用一个引用字符串
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($data) {
	if (is_array($data)){
	    foreach ($data as $key=>$val){
	        $data[$key] = new_stripslashes($val);
	    }
	    return $data;
	}
	return stripslashes($data);
}

/**
 * 返回经htmlspecialchars处理过的字符串或数组
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($data) {
	if (is_array($data)){
	    foreach ($data as $key=>$val){
	        $data[$key] = new_html_special_chars($val);
	    }
	    return $data;
	}
	return htmlspecialchars($data);
}
/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
	$string = str_replace('%20','',$string);
	$string = str_replace('%27','',$string);
	$string = str_replace('%2527','',$string);
	$string = str_replace('*','',$string);
	$string = str_replace('"','&quot;',$string);
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('<','&lt;',$string);
	$string = str_replace('>','&gt;',$string);
	$string = str_replace("{",'',$string);
	$string = str_replace('}','',$string);
	$string = str_replace('\\','',$string);
	$string = remove_xss($string);
	return $string;
}

/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function remove_xss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

    $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, '', $string); 
	}
	return $string;
}

function template($tpl_name,$app_name='', $style=''){
    if (!$app_name) $app_name = ROUTE_M;
    if (!$style) $style = surphp::load_config('config','TPL_NAME','default');
    $tempate = surphp::load_core_class('template',1);
    $tpl_path = $tempate->tpl_compile($tpl_name,$app_name,$style);
     
    if ($tpl_path && file_exists($tpl_path)){
        return $tpl_path;
    }
}

/**
 * 
 * 定义模型类
 * @param string $name
 * @param string $appname
 */
function M($name, $appname = ''){
    static $_model = array();
    if (!$appname) $appname = ROUTE_M;
    $key = $appname.'.'.$name;
    if (isset($_model[$key])) return $_model[$key];
    // 优先载入addons
    $model = surphp::load_model($name);
    if (!$model){
    	$model = surphp::load_model($name, $appname);
    }
    
    $className =  $name.'Model';
    if (class_exists($className)) {
    	$_model[$key] = new $className($name);
    }else {
    	$_model[$key] = new Model($name);
    }

    return $_model[$key];
}

/**
 * 调用任意Action
 * @param string $name
 * @param string $app
 */
function A($name, $app = ''){
    static $_action = array();
    if (!$appname) $appname = ROUTE_M;
    $key = $appname.'.'.$name;
    if (isset($_action[$key])) return $_action[$key];
    
    // check class is exist
    $class_name = $name.'Action';
    if (!class_exists($class_name, FALSE)){
        $filepath = SUR_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'action'.DIRECTORY_SEPARATOR.$class_name.'.class.php';
        if(file_exists($filepath)){
            include $filepath;
            if(!class_exists($class_name,FALSE)){
                exit('Controller '.$app.'.'.$class_name.' does not exist.');
			}
        } else {
			exit('Controller '.$app.'.'.$class_name.' does not exist.');
		}
    }
    $_action[$key] = new $class_name; 
    return $_action[$key];
}

/**
 * 
 * 调试程序时用来打印变量的函数
 * @param mix $args
 */
function var_pre($args){
    $args = array($args);
    echo '<pre>';
    foreach ($args as $arg) {
        var_dump($arg);
    }
}

/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$ip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

/**
 * 好用的字符串加密、解密函数 @todo 修改加密算法
 *
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$key		密钥：数字、字母、下划线
 * @param	string	$expiry		过期时间
 * @return	string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $key_length = 4;
	$key = md5($key != '' ? $key : surphp::load_config('config', 'AUTH_KEY'));
	$fixedkey = md5($key);
	$egiskeys = md5(substr($fixedkey, 16, 16));
	$runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
	$keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
	$string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

	$i = 0; $result = '';
	$string_length = strlen($string);
	for ($i = 0; $i < $string_length; $i++){
		$result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
	}
	if($operation == 'ENCODE') {
		return $runtokey . str_replace('=', '', base64_encode($result));
	} else {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$egiskeys), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	}
}

/**
 * 友好的时间显示
 *
 * @param int    $sTime 待显示的时间
 * @param string $type  类型. normal | mohu | full | ymd | other
 * @param string $alt   已失效
 * @return string
 */
function friendlyDate($sTime,$type = 'normal',$alt = 'false') {
	if (!$sTime)
		return '';
	//sTime=源时间，cTime=当前时间，dTime=时间差
	$cTime      =   time();
	$dTime      =   $cTime - $sTime;
	$dDay       =   intval(date("z",$cTime)) - intval(date("z",$sTime));
	//$dDay     =   intval($dTime/3600/24);
	$dYear      =   intval(date("Y",$cTime)) - intval(date("Y",$sTime));
	//normal：n秒前，n分钟前，n小时前，日期
	if($type=='normal'){
		if( $dTime < 60 ){
			if($dTime < 10){
				return '刚刚';    //by yangjs
			}else{
				return intval(floor($dTime / 10) * 10)."秒前";
			}
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
			//今天的数据.年份相同.日期相同.
		}elseif( $dYear==0 && $dDay == 0  ){
			//return intval($dTime/3600)."小时前";
			return '今天'.date('H:i',$sTime);
		}elseif($dYear==0){
			return date("m月d日 H:i",$sTime);
		}else{
			return date("Y-m-d H:i",$sTime);
		}
	}elseif($type=='mohu'){
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif( $dDay > 0 && $dDay<=7 ){
			return intval($dDay)."天前";
		}elseif( $dDay > 7 &&  $dDay <= 30 ){
			return intval($dDay/7) . '周前';
		}elseif( $dDay > 30 ){
			return intval($dDay/30) . '个月前';
		}
		//full: Y-m-d , H:i:s
	}elseif($type=='full'){
		return date("Y-m-d , H:i:s",$sTime);
	}elseif($type=='ymd'){
		return date("Y-m-d",$sTime);
	}else{
		if( $dTime < 60 ){
			return $dTime."秒前";
		}elseif( $dTime < 3600 ){
			return intval($dTime/60)."分钟前";
		}elseif( $dTime >= 3600 && $dDay == 0  ){
			return intval($dTime/3600)."小时前";
		}elseif($dYear==0){
			return date("Y-m-d H:i:s",$sTime);
		}else{
			return date("Y-m-d H:i:s",$sTime);
		}
	}
}

/**
 * 用法：U('blog/index/lists','uid=1')
 * @param string $url
 * @param string $params
 */
function U($url,$params=''){
	if(defined('IN_ADMIN') && strpos($url, '/') !== FALSE){
		$urls   =   explode('/',$url);
		$mod    =   isset($urls[0]) && !empty($urls[0]) ? $urls[0] : ROUTE_M;
		$con    =   isset($urls[1]) && !empty($urls[1]) ? $urls[1] : 'index';
		$act    =   isset($urls[2]) && !empty($urls[2]) ? $urls[2] : 'init';
		$site_url   =   SITE_URL.'index.php?m='.$mod.'&c='.$con.'&a='.$act;
	}else{
	    $site_url   =   SITE_URL.$url;
	}
    
	//填充附加参数
	if($params){
		if(is_array($params)){
			$params =   http_build_query($params);
			$params =   urldecode($params);
		}
		$params     =   str_replace('&amp;','&',$params);
		$site_url   .=  strpos($site_url, '?') === FALSE ? '?'.$params : '&'.$params;
	}
	return $site_url;
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt='') {
	$pwd = array();
	$pwd['encrypt'] =  $encrypt ? $encrypt : create_randomstr();
	$pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
	return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
	return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其它编码 copy from thinksns
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function mStr($str, $length, $charset="utf-8", $suffix=true){
	return msubstr($str, 0, $length, $charset, $suffix);
}
/**
 +----------------------------------------------------------
 * 字符串截取，支持中文和其它编码 copy from thinksns
 +----------------------------------------------------------
 * @static
 * @access public
 +----------------------------------------------------------
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	if($suffix && $str != $slice) return $slice."...";
	return $slice;
}

/**
 * 语言文件处理
 *
 * @param	string		$language	标示符
 * @param	array		$pars	转义的数组,二维数组 ,'key1'=>'value1','key2'=>'value2',
 * @param	string		$modules 多个模块之间用半角逗号隔开，如：member,guestbook
 * @return	string		语言字符
 */
function L($language = 'no_language',$pars = array(), $modules = '') {
	static $LANG = array();
	static $LANG_MODULES = array();
	static $lang = '';
	if(!$LANG) {
		require_once SUR_PATH.'languages'.DIRECTORY_SEPARATOR.'system.lang.php';
		if(defined('IN_ADMIN')) require_once SUR_PATH.'languages'.DIRECTORY_SEPARATOR.'system_menu.lang.php';
		if(file_exists(SUR_PATH.'languages'.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php')) require SUR_PATH.'languages'.DIRECTORY_SEPARATOR.ROUTE_M.'.lang.php';
	}
	if(!empty($modules)) {
		$modules = explode(',',$modules);
		foreach($modules AS $m) {
			if(!isset($LANG_MODULES[$m])) require SUR_PATH.'languages'.DIRECTORY_SEPARATOR.$m.'.lang.php';
		}
	}
	if(!array_key_exists($language,$LANG)) {
		return $language;
	} else {
		$language = $LANG[$language];
		if($pars) {
			foreach($pars AS $_k=>$_v) {
				$language = str_replace('{'.$_k.'}',$_v,$language);
			}
		}
		return $language;
	}
}

/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
	$badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
	foreach($badwords as $value){
		if(strpos($string, $value) !== FALSE) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_username($username) {
	$strlen = strlen($username);
	if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
		return false;
	} elseif ( 20 < $strlen || $strlen < 2 ) {
		return false;
	}
	return true;
}

/**
 * 检查密码长度是否符合规定
 *
 * @param STRING $password
 * @return 	TRUE or FALSE
 */
function is_password($password) {
	$strlen = strlen($password);
	if($strlen >= 6 && $strlen <= 20) return true;
	return false;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.phpcms.cn'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '') {
	if(defined('IN_ADMIN')) {
	    $tpl_path = SUR_PATH.'apps'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'showmessage.tpl.php';
		if ($tpl_path && file_exists($tpl_path)){
			include $tpl_path;
		}
	} else {
		include(template('message', 'public'));
	}
	exit;
}

/**
 * 加载模板标签缓存
 * @param string $name 缓存名
 * @param integer $times 缓存时间
 */
function tpl_cache($name,$times = 0) {
	$filepath = 'tpl_data';
	$info = getcacheinfo($name, $filepath);
	if (SYS_TIME - $info['filemtime'] >= $times) {
		return false;
	} else {
		return getcache($name,$filepath);
	}
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 * @param $timeout 过期时间
 * @since changed add memcache 2013-06-12 by kunlin.yang
 */
function setcache($name, $data, $filepath='', $type='file', $config='', $timeout=0) {
	surphp::load_core_class('cache_factory');
	if($config) {
		// use memcache
		$cacheconfig = surphp::load_config('cache');
		$cache_memcache = Cache_factory::get_instance($cacheconfig)->get_cache($config);
		$cache_key = md5($name.DIRECTORY_SEPARATOR.$filepath);
		
		// delete file cache info in memcache
		if ($filepath == 'tpl_data'){
		    $info_key = md5($name.DIRECTORY_SEPARATOR.$filepath.DIRECTORY_SEPARATOR.'cache_info');
		    $cache_memcache->delete($info_key);
		}
		
		// update memcache data
		$cache_memcache->set($cache_key,$data,$timeout);
		// update file cache
		$cache = Cache_factory::get_instance()->get_cache($type);
	} else {
		$cache = Cache_factory::get_instance()->get_cache($type);
	}

	return $cache->set($name, $data, 0, '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 * @since changed add memcache 2013-06-12 by kunlin.yang
 */
function getcache($name, $filepath='', $type='file', $config='') {
	surphp::load_core_class('cache_factory');
	if($config) {
		$cacheconfig = surphp::load_config('cache');
		$cache_memcache = Cache_factory::get_instance($cacheconfig)->get_cache($config);
		$cache_key = md5($name.DIRECTORY_SEPARATOR.$filepath);
		$val = $cache_memcache->get($cache_key);
		if ($val === FALSE) {
			// load file cache data
			$cache_file = Cache_factory::get_instance()->get_cache('file');
			$val = $cache_file->get($name, '', '', $filepath);
			if ($val !== FALSE) {
				// save to memcache
				$cache_memcache->set($cache_key,$val);
			}	
		}
		return $val;
	} else {
		$cache = Cache_factory::get_instance()->get_cache($type);
	}
	return $cache->get($name, '', '', $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param $type 缓存类型[file,memcache,apc]
 * @param $config 配置名称
 * @since changed add memcache 2013-06-12 by kunlin.yang
 */
function delcache($name, $filepath='', $type='file', $config='') {
	surphp::load_core_class('cache_factory');
	if($config) {
		// delete memcache first
		$cacheconfig = surphp::load_config('cache');
		$cache = Cache_factory::get_instance($cacheconfig)->get_cache($config);
		$key = md5($name.DIRECTORY_SEPARATOR.$filepath);
		$cache->delete($key);
		// delete file data second
		$cache_file = Cache_factory::get_instance()->get_cache('file');
		return $cache_file->delete($name, '', '', $filepath);
	} else {
		$cache = Cache_factory::get_instance()->get_cache($type);
	}
	return $cache->delete($name, '', '', $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 * @since changed add memcache 2013-06-12 by kunlin.yang
 */
function getcacheinfo($name, $filepath='', $type='file', $config='') {
	surphp::load_core_class('cache_factory');
	$cacheconfig = surphp::load_config('cache');
	$cache_key = md5($name.DIRECTORY_SEPARATOR.$filepath.DIRECTORY_SEPARATOR.'cache_info');
	$cache_memcache = Cache_factory::get_instance($cacheconfig)->get_cache($config);
	
	$cache_info = $cache_memcache->get($cache_key);
	if ($cache_info === FALSE) {
		$cache = Cache_factory::get_instance()->get_cache($type);
		$cache_info = $cache->cacheinfo($name, '', '', $filepath);
		
		// 10分钟过期
		if ($cache_info !== FALSE) {
			$cache_memcache->set($cache_key,$cache_info,600);
		}
	}
	
	return $cache_info;
}

/**
* 将字符串转换为数组
*
* @param	string	$data	字符串
* @return	array	返回数组格式，如果，data为空，则返回空数组
*/
function string2array($data) {
	if($data == '') return array();
	@eval("\$array = $data;");
	return $array;
}
/**
* 将数组转换为字符串
*
* @param	array	$data		数组
* @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
* @return	string	返回字符串，如果，data为空，则返回空
*/
function array2string($data, $isformdata = 1) {
	if($data == '') return '';
	if($isformdata) $data = new_stripslashes($data);
	return addslashes(var_export($data, TRUE));
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
	$path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 生成上传附件验证
 * @param $args   参数
 * @param $operation   操作类型(加密解密)
 */

function upload_key($args) {
	$pc_auth_key = md5(surphp::load_config('config', 'AUTH_KEY').$_SERVER['HTTP_USER_AGENT']);
	$authkey = md5($args.$pc_auth_key);
	return $authkey;
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
* 转换字节数为其他单位
*
*
* @param	string	$filesize	字节大小
* @return	string	返回大小
*/
function sizecount($filesize) {
	if ($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
	} elseif ($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 .' MB';
	} elseif($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize.' Bytes';
	}
	return $filesize;
}

/**
 * 获取站点的信息
 * @param $siteid   站点ID
 */
function siteinfo($siteid=1) {
	static $sitelist;
	if (empty($sitelist)) $sitelist  = getcache('sitelist','commons');
	
	if ($sitelist !== false){
	    return $sitelist[$siteid];
	}else{
	    $db = M('site');
	    $sitelist = $db->select('1=1','*','10','','','siteid');
	    setcache('sitelist', $sitelist, 'commons');
	    return $sitelist[$siteid];
	}
	return isset($sitelist[$siteid]) ? $sitelist[$siteid] : '';
}

/**
 * 获取当前的站点ID
 */
function get_siteid() {
	static $siteid;
	if (!empty($siteid)) return $siteid;
	if (empty($siteid)) $siteid = 1;
	return $siteid;
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '') {
	if($url == '') $url = get_url();
	$pos = strpos($url, '?');
	if($pos === false) {
		$url .= '?'.$par;
	} else {
		$querystring = substr(strstr($url, '?'), 1);
		parse_str($querystring, $pars);
		$query_array = array();
		foreach($pars as $k=>$v) {
			if($k != 'page') $query_array[$k] = $v;
		}
		$querystring = http_build_query($query_array).'&'.$par;
		$url = substr($url, 0, $pos).'?'.$querystring;
	}
	return $url;
}

/**
 * IE浏览器判断
 */

function is_ie() {
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}