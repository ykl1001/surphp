<?php
surphp::load_app_class('admin', 'admin',0);
admin::loadAdmin();
surphp::load_core_class('cookie',0);
class indexAction extends adminAction{
	public function login(){
		if ($this->post('dosubmit')) {
			
			$username = isset($_POST['username']) ? trim($_POST['username']) : $this->error('用户名不正确');
			$code = isset($_POST['vcode']) && trim($_POST['vcode']) ? trim($_POST['vcode']) : $this->error('验证码不正确');
			if ($_SESSION['code'] != strtolower($code)) {
				$this->error('验证码不正确');
			}
			$times_db = M('times');
			$rtime = $times_db->get_one(array('username'=>$username,'isadmin'=>1));
			$maxloginfailedtimes = surphp::load_config('config','MAXLOGINFAILEDTIMES');
			if(!empty($rtime) && $rtime['times'] >= $maxloginfailedtimes) {
				$minute = 60-floor((SYS_TIME-$rtime['logintime'])/60);
				if ($minute >= 0) {
					$this->error('您密码错误次数太多，请'.$minute.'分钟后重试');
				}else {
				    $times_db->delete(array('username'=>$username));
				    unset($rtime);
				}
			}
			//查询帐号
			$admin_db = M('admin');
			$r = $admin_db->get_one(array('username'=>$username));
			if(!$r) $this->error('用户不存在');
			$password = md5(md5(trim($_POST['password'])).$r['encrypt']);
				
			if($r['password'] != $password) {
				$ip = ip();
				if(!empty($rtime)) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
				} else {
					$times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}
				$this->error('密码错误，您还剩下'.($times - 1).'次机会');
			}
			
			$times_db->delete(array('username'=>$username));
			$admin_db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$_SESSION['userid'] = $r['userid'];
			$_SESSION['roleid'] = $r['roleid'];
			$_SESSION['_hash'] = random(6,'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY0123456789');
			$cookie_time = SYS_TIME+86400*30;
			Cookie::setCookie('admin_username',$username,$cookie_time);
			Cookie::setCookie('userid', $r['userid'],$cookie_time);
			Cookie::setCookie('admin_email', $r['email'],$cookie_time);
			$this->success('登录成功',U('admin/index/init','_hash='.$_SESSION['_hash']));
			
		}else{
			include $this->admin_tpl('login','admin');
		}
		
	}
	
	public function init(){
		$userid = $_SESSION['userid'];
		$admin_username = Cookie::getCookie('admin_username');
		$roles = M('admin_role')->listinfo('','roleid',1,20,'roleid');
		$rolename = $roles[$_SESSION['roleid']]['rolename'];
		surphp::load_app_class('menu','admin',0);
		/*管理员收藏栏*/
		$adminpanel = M('admin_panel')->select(array('userid'=>$userid), "*",20 , 'datetime');
		include $this->admin_tpl('index');
	}
	
	//左侧菜单
	public function public_menu_left() {
		surphp::load_app_class('menu','admin',0);
		$menuid = intval($_GET['menuid']);
		$datas = Menu::admin_menu($menuid);
		if (isset($_GET['parentid']) && $parentid = intval($_GET['parentid']) ? intval($_GET['parentid']) : 10) {
			foreach($datas as $_value) {
				if($parentid==$_value['id']) {
					echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
					 
				} else {
					echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
				}
			}
		} else {
			include $this->admin_tpl('left');
		}
	
	}
	//当前位置
	public function public_current_pos() {
		surphp::load_app_class('menu','admin',0);
		echo Menu::current_pos($_GET['menuid']);
		exit;
	}
	
	public function public_main() {
		$admin_username = Cookie::getCookie('admin_username');
		$roles = M('admin_role')->listinfo('','roleid',1,20,'roleid');
		$userid = $_SESSION['userid'];
		$rolename = $roles[$_SESSION['roleid']]['rolename'];
		$r = M('admin')->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = mysql_get_server_info();
		$show_header = $show_pc_hash = 1;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(SUR_PATH.'surphp.php');
		include $this->admin_tpl('main');
	}
	
	public function public_logout() {
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		Cookie::setCookie('admin_username','');
		Cookie::setCookie('userid',0);
	
		$this->success(L('logout_success'),U('admin/index/login'));
	}
	
	//后台站点地图
	public function public_map() {
		surphp::load_app_class('menu','admin',0);
		$array = Menu::admin_menu(0);
		$menu = array();
		foreach ($array as $k=>$v) {
			$menu[$v['id']] = $v;
			$menu[$v['id']]['childmenus'] = Menu::admin_menu($v['id']);
		}
		$show_header = true;
		include $this->admin_tpl('map');
	}
	
	/**
	 * 维持 session 登陆状态
	 */
	public function public_session_life() {
		$userid = $_SESSION['userid'];
		return true;
	}
	
	public function public_ajax_add_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		$menuarr = M('menu')->get_one(array('id'=>$menuid));
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].'&'.$menuarr['data'];
		$data = array('menuid'=>$menuid, 'userid'=>$_SESSION['userid'], 'name'=>$menuarr['name'], 'url'=>$url, 'datetime'=>SYS_TIME);
		M('admin_panel')->insert($data, '', 1);
		$panelarr = M('admin_panel')->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url'].'&menuid='.$v['menuid']."&_hash=".$_SESSION['_hash']."'>".L($v['name'])."</a>  <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}
	
	public function public_ajax_delete_panel() {
		$menuid = isset($_POST['menuid']) ? $_POST['menuid'] : exit('0');
		M('admin_panel')->delete(array('menuid'=>$menuid, 'userid'=>$_SESSION['userid']));

		$panelarr = M('admin_panel')->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$v['url']."&_hash=".$_SESSION['_hash']."'>".L($v['name'])."</a> <a class='panel-delete' href='javascript:delete_panel(".$v['menuid'].");'></a></span>";
		}
		exit;
	}	
	
	/**
	 * 清除缓存
	 * @since 2013, Jun 21
	 */
	public function clear_cache(){
	    $dirs = array('caches_commons','caches_template');
	    foreach ($dirs as $dir) {
	        $this->rmdirr(CACHE_PATH.$dir);
	        echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'>\"".$dir."\" 目录清理完毕! </div> <br /><br />";
	    }
	}
	
	private function rmdirr($dirname){
		if (!file_exists($dirname)) {
		    return false;
		}

		if (is_file($dirname) || is_link($dirname)) {
		    return unlink($dirname);
		}
		
		$dir = dir($dirname);
		
		while (false !== $entry = $dir->read()) {

		    if ($entry == '.' || $entry == '..') {
		        continue;
		    }

		    $this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
		}
		
		$dir->close();
		return rmdir($dirname);
	}
}