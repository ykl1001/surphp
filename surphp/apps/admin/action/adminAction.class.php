<?php
/**
 * 管理员类
 * @author kunlin.yang
 *
 */
//定义在后台
define('IN_ADMIN',true);
surphp::load_app_func('global','admin');
surphp::load_app_func('admin','admin');
class adminAction extends Action{
	var $username;
	var $userid;
	function __construct(){
		// start session
		surphp::load_core_class('Session',1);
		surphp::load_app_class('menu','admin',0);
		surphp::load_core_class('cookie',0);
		self::manage_log();
		self::checkLogin();
		self::check_hash();
		self::check_priv();
	}
	
	/**
	 * 登录检测
	 */
	final function checkLogin(){
		if ((ROUTE_M == 'admin' && ROUTE_C =='index' && ROUTE_A == 'login') || preg_match('/^public_/', ROUTE_A)) {
			return true;
		}elseif ( !isset($_SESSION['userid']) || !isset($_SESSION['roleid']) || !$_SESSION['userid'] || !$_SESSION['roleid']) {
			$this->goLogin();
			exit;
		}
	}
	
	/**
	 * 登录
	 */
	public function goLogin(){
		$this->error('请先登录',U('admin/index/login'));
	}
	
	/**
	 * 管理后台专用模板
	 * @param string $tpl_name
	 * @param string $app_name
	 * @return string|boolean
	 */
	public function admin_tpl($tpl_name,$app_name = ''){
		if (!$app_name) $app_name = ROUTE_M;
		$tpl_path = SUR_PATH.'apps'.DIRECTORY_SEPARATOR.$app_name.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$tpl_name.'.tpl.php';
		if ($tpl_path && file_exists($tpl_path)){
			return $tpl_path;
		}
		return FALSE;
	}
	
	/**
	 * 验证hash
	 * @return boolean
	 */
	public function check_hash(){
		if(preg_match('/^public_/', ROUTE_A) || (ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array('login'))) ) {
			return true;
		}
		if(isset($_GET['_hash']) && $_SESSION['_hash'] != '' && ($_SESSION['_hash'] == $_GET['_hash'])) {
			return true;
		} elseif(isset($_POST['_hash']) && $_SESSION['_hash'] != '' && ($_SESSION['_hash'] == $_POST['_hash'])) {
			return true;
		} else {
			$this->error('Hash验证失败');
		}
	}
	
	/**
	 * 权限判断
	 */
	final public function check_priv() {
		if(ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array('login', 'init', 'public_card'))) return true;
		if($_SESSION['roleid'] == 1) return true;
		$action = ROUTE_A;
		$privdb = surphp::load_model('admin_role_priv','admin',1);
		if(preg_match('/^public_/',ROUTE_A)) return true;
		if(preg_match('/^ajax_([a-z]+)_/',ROUTE_A,$_match)) {
			$action = $_match[1];
		}
		$r =$privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>$action,'roleid'=>$_SESSION['roleid']));
		if(!$r) $this->error('您没有操作权限');
	}
	
	/**
	 *
	 * 记录日志
	 */
	final private function manage_log() {
	    $admin_log = surphp::load_config('config','ADMIN_LOG');
	    if ($admin_log == 1){
	        $action = ROUTE_A;
	        if($action == '' || strchr($action,'public') || $action == 'init' || $action=='public_current_pos'){
	            return false;
	        }else{
	            $ip = ip();
	            $log = M('log');
	            $username = Cookie::getCookie('admin_username');
	            $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';
	            $time = date('Y-m-d H-i-s',SYS_TIME);
	            $url = '?m='.ROUTE_M.'&c='.ROUTE_C.'&a='.ROUTE_A;
	            $log->insert(array('module'=>ROUTE_M,'username'=>$username,'userid'=>$userid,'action'=>ROUTE_C, 'querystring'=>$url,'time'=>$time,'ip'=>$ip));
	        }
	    }
		
	}
}