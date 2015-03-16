<?php
/**
 * 用户登录类
 * @author yangkkunlin
 *
 */
class Passport{
	function __construct(){
	    surphp::load_core_class('Cookie');
		$this->_session_start();
	}
	
	public function checkLogin(){
		//1.检查Session 存活时间
		if (intval($_SESSION['userid']) > 0) {
			return true;
		}else if($uid = $this->loginCookie()){
			//2.检查cookie
			$this->_saveLogin($uid);
			return true;
		}else{
			//3.注销
			unset($_SESSION['userid']);
			unset($_SESSION['username']);
			return false;
		}
			
	}
	
	/**
	 * get user cookie
	 */
	public function loginCookie(){
		$auth = Cookie::getCookie('auth');
		if ($auth) {
			$auth_key = md5(surphp::load_config('config', 'AUTH_KEY').$_SERVER['HTTP_USER_AGENT']);
			list($userid,$password) = explode('\t', authcode($auth,'DECODE',$auth_key));
			if($userid > 0 && ($user = M('user')->get_one('userid='.$userid,'*'))){
				if ($user['password'] == $password) {
					return $userid;
				}
			}
		}
		return false;
	}
	
	public function _saveLogin($userid){
		$this->_session_start();
		$_SESSION['userid'] = $userid;
		$user = M('user')->get_one('userid='.$userid,'*');
		$_SESSION['username'] = $user['username'];
	}
	
	public function _session_start(){
		surphp::load_core_class('Session',1);
	}
}