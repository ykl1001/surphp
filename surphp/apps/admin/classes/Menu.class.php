<?php
class Menu{
	final public static function admin_menu($parentid, $with_self = 0){
		$parentid = intval($parentid);
		$menudb = M('menu');
		$result =$menudb->select(array('parentid'=>$parentid,'display'=>1),'*',1000,'listorder ASC');
		if($with_self) {
			$result2[] = $menudb->get_one(array('id'=>$parentid));
			$result = array_merge($result2,$result);
		}
	
		//权限检查
		if($_SESSION['roleid'] == 1) return $result;
		$array = array();
		$privdb = M('admin_role_priv');
		foreach($result as $v) {
			$action = $v['a'];
			if(preg_match('/^public_/',$action)) {
				$array[] = $v;
			} else {
				if(preg_match('/^ajax_([a-z]+)_/',$action,$_match)) $action = $_match[1];
				$r = $privdb->get_one(array('m'=>$v['m'],'c'=>$v['c'],'a'=>$action,'roleid'=>$_SESSION['roleid']));
				if($r) $array[] = $v;
			}
		}
		return $array;
	}
	
	final static public function submenu($parentid = '', $big_menu = false){
		if(empty($parentid)) {
			$menudb = M('menu');
			$r = $menudb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>ROUTE_A));
			$parentid = $_GET['menuid'] = $r['id'];
		}
		$array = self::admin_menu($parentid,1);
	
		$numbers = count($array);
		if($numbers==1 && !$big_menu) return '';
		$string = '';
		$_hash = $_SESSION['_hash'];
		foreach($array as $_value) {
			if (!isset($_GET['s'])) {
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == $_value['a'] ? 'class="on"' : '';
			} else {
				$_s = !empty($_value['data']) ? str_replace('=', '', strstr($_value['data'], '=')) : '';
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == $_value['a'] && $_GET['s'] == $_s ? 'class="on"' : '';
			}
			if($_value['parentid'] == 0 || $_value['m']=='') continue;
			if($classname) {
				$string .= "<a href='javascript:;' $classname><em>".L($_value['name'])."</em></a><span>|</span>";
			} else {
				$string .= "<a href='?m=".$_value['m']."&c=".$_value['c']."&a=".$_value['a']."&menuid=$parentid&_hash=$_hash".'&'.$_value['data']."' $classname><em>".L($_value['name'])."</em></a><span>|</span>";
			}
		}
		$string = substr($string,0,-14);
		return $string;
	}
	
	final public static function current_pos($id) {
		$menudb = M('menu');
		$r =$menudb->get_one(array('id'=>$id),'id,name,parentid');
		$str = '';
		if($r['parentid']) {
			$str = self::current_pos($r['parentid']);
		}
		return $str.L($r['name']).' > ';
	}
}