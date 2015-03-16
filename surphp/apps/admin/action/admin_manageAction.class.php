<?php
surphp::load_app_class('admin', 'admin',0);
admin::loadAdmin();
surphp::load_core_class('cookie',0);
surphp::load_app_func('admin','admin');
class admin_manageAction extends adminAction {
	private $db,$role_db;
	function __construct() {
		parent::__construct();
		$this->db = M('admin');
		$this->role_db = surphp::load_model('admin_role','admin',1);
		$this->op = surphp::load_app_class('admin_op','admin',1);
	}
	
	/**
	 * 管理员管理列表
	 */
	public function init() {
		$userid = $_SESSION['userid'];
		$admin_username = Cookie::getCookie('admin_username');
		$page = $_GET['page'] ? intval($_GET['page']) : '1';
		$infos = $this->db->listinfo('', '', $page, 20);
		$pages = $this->db->pages;
		$roles = M('admin_role')->listinfo('','roleid',1,20,'roleid','*');
		include $this->admin_tpl('admin_list');
	}
	
	/**
	 * 添加管理员
	 */
	public function add() {
		if(isset($_POST['dosubmit'])) {
			$info = array();
			if(!$this->op->checkname($_POST['info']['username'])){
				$this->error(L('admin_already_exists'));
			}
			$info = checkuserinfo($_POST['info']);		
			if(!checkpasswd($info['password'])){
				$this->error(L('pwd_incorrect'));
			}
			$passwordinfo = password($info['password']);
			$info['password'] = $passwordinfo['password'];
			$info['encrypt'] = $passwordinfo['encrypt'];
			
			$admin_fields = array('username', 'email', 'password', 'encrypt','roleid','realname');
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			$this->db->insert($info);
			if($this->db->insert_id()){
				$this->success(L('operation_success'),'?m=admin&c=admin_manage&_hash='.$_SESSION['_hash']);
			}
		} else {
			$roles = $this->role_db->select(array('disabled'=>'0'));
			include $this->admin_tpl('admin_add');
		}
		
	}
	
	/**
	 * 修改管理员
	 */
	public function edit() {
		if(isset($_POST['dosubmit'])) {
			$memberinfo = $info = array();			
			$info = checkuserinfo($_POST['info']);
			if(isset($info['password']) && !empty($info['password']))
			{
				$this->op->edit_password($info['userid'], $info['password']);
			}
			$userid = $info['userid'];
			$admin_fields = array('username', 'email', 'roleid','realname');
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			$this->db->update($info,array('userid'=>$userid));
			showmessage(L('operation_success'),'','','edit');
		} else {					
			$info = $this->db->get_one(array('userid'=>$_GET['userid']));
			extract($info);	
			$roles = $this->role_db->select(array('disabled'=>'0'));	
			$show_header = true;
			include $this->admin_tpl('admin_edit');		
		}
	}
	
	/**
	 * 删除管理员
	 */
	public function delete() {
		$userid = intval($_GET['userid']);
		if($userid == '1') $this->error(L('this_object_not_del'), HTTP_REFERER);
		$this->db->delete(array('userid'=>$userid));
		$this->success(L('admin_cancel_succ'));
	}
	
	/**
	 * 更新管理员状态
	 */
	public function lock(){
		$userid = intval($_GET['userid']);
		$disabled = intval($_GET['disabled']);
		$this->db->update(array('disabled'=>$disabled),array('userid'=>$userid));
		$this->success(L('operation_success'),'?m=admin&c=admin_manage&_hash='.$_SESSION['_hash']);
	}
	
	/**
	 * 管理员自助修改密码
	 */
	public function public_edit_pwd() {
		$userid = $_SESSION['userid'];
		if(isset($_POST['dosubmit'])) {
			$r = $this->db->get_one(array('userid'=>$userid),'password,encrypt');
			if ( password($_POST['old_password'],$r['encrypt']) !== $r['password'] ) $this->error(L('old_password_wrong'),HTTP_REFERER);
			if(isset($_POST['new_password']) && !empty($_POST['new_password'])) {
				$this->op->edit_password($userid, $_POST['new_password']);
			}
			$this->success(L('password_edit_succ_logout'),'?m=admin&c=index&a=public_logout');			
		} else {
			$info = $this->db->get_one(array('userid'=>$userid));
			extract($info);
			include $this->admin_tpl('admin_edit_pwd');			
		}

	}
	/*
	 * 编辑用户信息
	 */
	public function public_edit_info() {
		$userid = $_SESSION['userid'];
		if(isset($_POST['dosubmit'])) {
			$admin_fields = array('email','realname','lang');
			$info = array();
			$info = $_POST['info'];
			
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			$this->db->update($info,array('userid'=>$userid));
			$this->success(L('operation_success'));			
		} else {
			$info = $this->db->get_one(array('userid'=>$userid));
			extract($info);
			
			include $this->admin_tpl('admin_edit_info');			
		}	
	
	}
	/**
	 * 异步检测用户名
	 */
	function public_checkname_ajx() {
		$username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit(0);
		if ($this->db->get_one(array('username'=>$username),'userid')){
			exit('0');
		}
		exit('1');
	}
	/**
	 * 异步检测密码
	 */
	function public_password_ajx() {
		$userid = $_SESSION['userid'];
		$r = array();
		$r = $this->db->get_one(array('userid'=>$userid),'password,encrypt');
		if ( password($_GET['old_password'],$r['encrypt']) == $r['password'] ) {
			exit('1');
		}
		exit('0');
	}
	/**
	 * 异步检测emial合法性
	 */
	function public_email_ajx() {
		$email = $_GET['email'];
		if ($this->db->get_one(array('email'=>$email),'userid')){
			exit('0');
		}
		exit('1');
	}

}
?>