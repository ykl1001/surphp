<?php
abstract class Api{
    protected $user_id;
	protected $user_name;
	protected $data;
	
	function __construct(){
	    $this->user_id   = 0;
		$this->user_name = 'guest';
		$this->data      = $_REQUEST;
	}
	
    //未登录提示信息
	protected  function verifyFailure(){
		$message['message'] = '用户未登录';
		$message['code']    = '00001';
		exit( json_encode( $message ) );
	}
	
    public function requireLogin(){
    	$passport = surphp::load_core_class('Passport',1);
    	if($passport->checkLogin()){
    		$this->user_id = $_SESSION['userid'];
    		$this->user_name = $_SESSION['username'];
    	}else {
    	    $this->verifyFailure();
    	}
    }
}