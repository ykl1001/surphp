<?php
surphp::load_app_class('admin', 'admin',0);
admin::loadAdmin();
surphp::load_core_class('cookie',0);
surphp::load_app_func('admin','admin');
class roleAction extends adminAction {
	private $db, $priv_db;
	function __construct() {
		parent::__construct();
		$this->db = surphp::load_model('admin_role','admin',1);
		$this->priv_db = surphp::load_model('admin_role_priv','admin',1);
		$this->op = surphp::load_app_class('role_op','admin',1);
	}
	
	/**
	 * 角色管理列表
	 */
	public function init() {
		$infos = $this->db->select($where = '', $data = '*', $limit = '', $order = 'listorder DESC, roleid DESC', $group = '');
		
		include $this->admin_tpl('role_list');
	}
	
	/**
	 * 添加角色
	 */
	public function add() {
		if(isset($_POST['dosubmit'])) {
			if(!is_array($_POST['info']) || empty($_POST['info']['rolename'])){
				$this->error(L('operation_failure'));
			}
			if($this->op->checkname($_POST['info']['rolename'])){
				$this->error(L('role_duplicate'));
			}
			$insert_id = $this->db->insert($_POST['info'],true);
			if($insert_id){
				$this->success(L('operation_success'),'?m=admin&c=role&a=init&_hash='.$_SESSION['_hash']);
			}
		} else {
			include $this->admin_tpl('role_add');
		}
		
	}
	
	/**
	 * 编辑角色
	 */
	public function edit() {
		if(isset($_POST['dosubmit'])) {
			$_POST['roleid'] = intval($_POST['roleid']);
			if(!is_array($_POST['info']) || empty($_POST['info']['rolename'])){
				$this->error(L('operation_failure'));
			}
			$this->db->update($_POST['info'],array('roleid'=>$_POST['roleid']));
			$this->success(L('operation_success'),'?m=admin&c=role&_hash='.$_SESSION['_hash']);
		} else {					
			$info = $this->db->get_one(array('roleid'=>$_GET['roleid']));
			extract($info);		
			include $this->admin_tpl('role_edit');		
		}
	}
	
	/**
	 * 删除角色
	 */
	public function delete() {
		$roleid = intval($_GET['roleid']);
		if($roleid == '1') $this->error(L('this_object_not_del'), HTTP_REFERER);
		$this->db->delete(array('roleid'=>$roleid));
		$this->priv_db->delete(array('roleid'=>$roleid));
		$this->success(L('role_del_success'));
	}
	/**
	 * 更新角色排序
	 */
	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			foreach($_POST['listorders'] as $roleid => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('roleid'=>$roleid));
			}
			$this->success(L('operation_success'));
		} else {
			$this->error(L('operation_failure'));
		}
	}
	
	/**
	 * 角色权限设置
	 */
	public function role_priv() {
		$this->menu_db = M('menu');
		$siteid = $siteid ? $siteid : get_siteid(); 
		if(isset($_POST['dosubmit'])){
			if (is_array($_POST['menuid']) && count($_POST['menuid']) > 0) {
			
				$this->priv_db->delete(array('roleid'=>$_POST['roleid'],'siteid'=>$_POST['siteid']));
				$menuinfo = $this->menu_db->select('','`id`,`m`,`c`,`a`,`data`');
				foreach ($menuinfo as $_v) $menu_info[$_v[id]] = $_v;
				foreach($_POST['menuid'] as $menuid){
					$info = array();
					$info = $this->op->get_menuinfo(intval($menuid),$menu_info);
					$info['roleid'] = $_POST['roleid'];
					$info['siteid'] = $_POST['siteid'];
					$this->priv_db->insert($info);
				}
			} else {
				$this->priv_db->delete(array('roleid'=>$_POST['roleid'],'siteid'=>$_POST['siteid']));
			}
			$this->success(L('operation_success'));

		} else {
			$siteid = intval($_GET['siteid']);
			$roleid = intval($_GET['roleid']);
			if ($siteid) {
				$menu = surphp::load_core_class('tree',1);
				$menu->icon = array('│ ','├─ ','└─ ');
				$menu->nbsp = '&nbsp;&nbsp;&nbsp;';
				$result = $this->menu_db->select();
				$priv_data = $this->priv_db->select(); //获取权限表数据
				$modules = 'admin,system';
				foreach ($result as $n=>$t) {
					$result[$n]['cname'] = L($t['name'],'',$modules);
					$result[$n]['checked'] = ($this->op->is_checked($t,$_GET['roleid'],$siteid, $priv_data))? ' checked' : '';
					$result[$n]['level'] = $this->op->get_level($t['id'],$result);
					$result[$n]['parentid_node'] = ($t['parentid'])? ' class="child-of-node-'.$t['parentid'].'"' : '';
				}
				$str  = "<tr id='node-\$id' \$parentid_node>
							<td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='javascript:checknode(this);'> \$cname</td>
						</tr>";
			
				$menu->init($result);
				$categorys = $menu->get_tree(0, $str);
			}
			$show_header = true;
			$show_scroll = true;
			include $this->admin_tpl('role_priv');
		}
	}
	
	public function priv_setting() {
		$sites = surphp::load_app_class('sites', 'admin', 1);
		$sites_list = $sites->get_list();
		$roleid = intval($_GET['roleid']);
		include $this->admin_tpl('role_priv_setting');
		
	}

	/**
	 * 更新角色状态
	 */
	public function change_status(){
		$roleid = intval($_GET['roleid']);
		$disabled = intval($_GET['disabled']);
		$this->db->update(array('disabled'=>$disabled),array('roleid'=>$roleid));
		$this->success(L('operation_success'),'?m=admin&c=role&_hash='.$_SESSION['_hash']);
	}
	/**
	 * 成员管理
	 */
	public function member_manage() {
		$this->admin_db = M('admin');
		$roleid = intval($_GET['roleid']);
		$roles = M('admin_role')->listinfo('','roleid',1,20,'roleid');
		$infos = $this->admin_db->select(array('roleid'=>$roleid));
		include $this->admin_tpl('admin_list');
	}
	
	/**
	 * 角色缓存
	 */
	private function _cache() {

		$infos = $this->db->select(array('disabled'=>'0'), $data = '`roleid`,`rolename`', '', 'roleid ASC');
		$role = array();
		foreach ($infos as $info){
			$role[$info['roleid']] = $info['rolename'];
		}
		$this->_cache_siteid($role);
		setcache('role', $role,'commons');
		return $infos;
	}
	
	/**
	 * 缓存站点数据
	 */
	private function _cache_siteid($role) {
		$sitelist = array();
		foreach($role as $n=>$r) {
			$sitelists = $this->priv_db->select(array('roleid'=>$n),'siteid', '', 'siteid');
			foreach($sitelists as $site) {
				foreach($site as $v){
					$sitelist[$n][] = intval($v);
				}
			}
		}
		if(is_array($sitelist)) {
			$sitelist = @array_map("array_unique", $sitelist);
			setcache('role_siteid', $sitelist,'commons');
		}								
		return $sitelist;
	}
	
}
?>