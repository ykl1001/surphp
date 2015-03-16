<?php

abstract class Action {
	protected $jslist;
	protected $csslist;
	protected $title;
	protected $keywords;
	protected $description;
	protected $user_id;
	protected $user_name;
	
	function __construct(){
		$this->user_id = 0;
		$this->user_name = 'guest';
	}

    function template($tpl_name,$app_name = '',$style = '') {
        if (!$app_name) $app_name = ROUTE_M;
        if (!$style) $style = surphp::load_config('config','TPL_NAME','default');
        $tempate = surphp::load_core_class('template',1);
    	$tpl_path = $tempate->tpl_compile($tpl_name,$app_name,$style);
    	
    	if ($tpl_path && file_exists($tpl_path)){
    	    return $tpl_path;
    	}
    	return FALSE;
    }
    
    public function addJs($js){
    	if(is_array($js)){
    	    $this->jslist[] = array_merge($this->jslist,$js);
    	} else {
    	    $this->jslist[] = $js;
    	}        
    }
    
    public function addCss($css){
    	if (is_array($css)){
    	    $this->csslist[] = array_merge($this->csslist,$css);
    	} else {
    	    $this->csslist[] = $css;
    	}        
    }
    
    public function setTitle($title){
        $this->title = $title;
    }
    
    public function setKeywords($keywords){
        $this->keywords = $keywords;
    } 
    
    public function setDescription($desc){
        $this->description = $desc;
    }
    
    public function error($message, $jumpurl = '',$waitSecond=1){
    	if (!$jumpurl){
    	    $jumpurl =  'javascript:history.back(-1);';
    	}  
        $this->_jumpto($message, 0, $jumpurl, $waitSecond);
    }
    
    public function success($message, $jumpurl = '',$waitSecond=1){
    	if (!$jumpurl && HTTP_REFERER){
    	    $jumpurl =  HTTP_REFERER;
    	}else if(!$jumpurl){
    	    $jumpurl = SITE_URL;
    	}
        $this->_jumpto($message, 1, $jumpurl, $waitSecond);
    }
    
    public function pageNotFound(){
        include CORE_PATH.'tpl'.DIRECTORY_SEPARATOR.'PageNotFound.tpl.php';
        exit;
    }
    
    public function ajaxReturn($status, $data = '', $info='' , $type='JSON'){
        $result  =  array();
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        }else{
            echo $status;exit;
        }
    }
    
    public function get($key,$default=FALSE){
    	if (isset($_REQUEST[$key]) && $_REQUEST[$key]) {
    		return $_REQUEST[$key];
    	}else {
    		return $default;
    	}
    }
    
    public function post($key,$default=FALSE){
    	if (isset($_POST[$key]) && $_POST[$key]) {
    		return $_POST[$key];
    	}else {
    		return $default;
    	}
    }
    
    public function requireLogin(){
    	$passport = surphp::load_core_class('Passport',1);
    	if($passport->checkLogin()){
    		$this->user_id = $_SESSION['userid'];
    		$this->user_name = $_SESSION['username'];
    	}else {
    	    $action = A('passport','user');
    	    $action->login();exit;
    	}
    }
    
    private function _jumpto($message, $status=1, $jumpUrl='', $waitSecond=1){
    	include CORE_PATH.'tpl'.DIRECTORY_SEPARATOR.'SurPHPMessage.tpl.php';
        exit;
    }    
}
?>