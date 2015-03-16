<?php
surphp::load_app_class('admin', 'admin',0);
admin::loadAdmin();
surphp::load_core_class('cookie',0);
class settingAction extends adminAction {
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * 配置信息
	 */
	public function init() {
		$show_validator = true;
		$setconfig = surphp::load_config('config');	
		extract($setconfig);
		$show_header = true;
		$show_validator = 1;
		
		include $this->admin_tpl('setting');
	}
	
	/**
	 * 保存配置信息
	 */
	public function save() {
		set_config($_POST['setconfig']);	 //保存进config文件
		showmessage(L('setting_succ'), HTTP_REFERER);
	}
	
}
?>