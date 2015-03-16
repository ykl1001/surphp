<?php 
surphp::load_core_class('session',1);
surphp::load_core_class('cookie',0);
class indexAction extends Action{
	private $att_db;
	function __construct() {
		surphp::load_app_func('global','attachment');
		$this->upload_path = surphp::load_config('config','UPLOAD_PATH');
		$this->upload_url  = surphp::load_config('config','UPLOAD_URL') ? surphp::load_config('config','UPLOAD_URL') : SITE_URL.$this->upload_path;		
		$this->imgext      = array('jpg','gif','png','bmp','jpeg');
		$this->userid      = $_SESSION['userid'];
		$this->isadmin     = $this->admin_username = $_SESSION['roleid'] ? 1 : 0;
	}
	
	/**
	 * 常规上传
	 */
	public function upload() {
		surphp::load_core_class('attachment','',0);
		$module = trim($_GET['module']);
		$siteid = $this->get_siteid();
		$site_setting = get_site_setting($siteid);
		$site_allowext = $site_setting['upload_allowext'];		
		$attachment = new attachment($module);
		$attachment->set_userid($this->userid);
		$a = $attachment->upload('upload',$site_allowext);
		if($a){
			$filepath = $attachment->uploadedfiles[0]['filepath'];
			
			$fn = intval($_GET['CKEditorFuncNum']);
			$this->upload_json($a[0],$filepath,$attachment->uploadedfiles[0]['filename']);
			$attachment->mkhtml($fn,$this->upload_url.$filepath,'');
		}
	}
	/**
	 * swfupload上传附件
	 */
	public function swfupload(){
		if(isset($_POST['dosubmit'])){
			if( $_POST['swf_auth_key'] != md5(surphp::load_config('config','AUTH_KEY').$_POST['SWFUPLOADSESSID'])) exit();
			surphp::load_core_class('attachment',0);
			$attachment = new attachment($_POST['module']);
			$attachment->set_userid($_POST['userid']);
			$aids = $attachment->upload('Filedata',$_POST['filetype_post'],'','',array($_POST['thumb_width'],$_POST['thumb_height']),$_POST['watermark_enable']);
			if($aids[0]) {
				$filename= $attachment->uploadedfiles[0]['filename'];
				if($attachment->uploadedfiles[0]['isimage']) {
					echo $aids[0].','.$this->upload_url.$attachment->uploadedfiles[0]['filepath'].','.$attachment->uploadedfiles[0]['isimage'].','.$filename;
				} else {
					$fileext = $attachment->uploadedfiles[0]['fileext'];
					if($fileext == 'zip' || $fileext == 'rar') $fileext = 'rar';
					elseif($fileext == 'doc' || $fileext == 'docx') $fileext = 'doc';
					elseif($fileext == 'xls' || $fileext == 'xlsx') $fileext = 'xls';
					elseif($fileext == 'ppt' || $fileext == 'pptx') $fileext = 'ppt';
					elseif ($fileext == 'flv' || $fileext == 'swf' || $fileext == 'rm' || $fileext == 'rmvb') $fileext = 'flv';
					else $fileext = 'do';
					echo $aids[0].','.$this->upload_url.$attachment->uploadedfiles[0]['filepath'].','.$fileext.','.$filename;
				}			
				exit;
			} else {
				echo '0,'.$attachment->error();
				exit;
			}

		} else {
			$args = $_GET['args'];
			$authkey = $_GET['authkey'];
			if(upload_key($args) != $authkey) showmessage(L('attachment_parameter_error'));
			extract(getswfinit($_GET['args']));
			$siteid = $this->get_siteid();
			$site_setting = get_site_setting($siteid);
			$file_size_limit = sizecount($site_setting['upload_maxsize']*1024);					
			include $this->admin_tpl('swfupload');
		}
	}
	
	public function crop_upload() {	
		if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
			$pic = $GLOBALS["HTTP_RAW_POST_DATA"];
			if (isset($_GET['width']) && !empty($_GET['width'])) {
				$width = intval($_GET['width']);
			}
			if (isset($_GET['height']) && !empty($_GET['height'])) {
				$height = intval($_GET['height']);
			}
			if (isset($_GET['file']) && !empty($_GET['file'])) {
				$_GET['file'] = str_ireplace(';','','php',$_GET['file']);
				if(is_image($_GET['file'])== false || stripos($_GET['file'],'.php')!==false) exit();
				if (strpos($_GET['file'], pc_base::load_config('system', 'upload_url'))!==false) {
					$file = $_GET['file'];
					$basename = basename($file);
					if (strpos($basename, 'thumb_')!==false) {
						$file_arr = explode('_', $basename);
						$basename = array_pop($file_arr);
					}
					$new_file = 'thumb_'.$width.'_'.$height.'_'.$basename;
				} else {
					pc_base::load_sys_class('attachment','',0);
					$module = trim($_GET['module']);
					$catid = intval($_GET['catid']);
					$siteid = $this->get_siteid();
					$attachment = new attachment($module, $catid, $siteid);
					$uploadedfile['filename'] = basename($_GET['file']); 
					$uploadedfile['fileext'] = fileext($_GET['file']);
					if (in_array($uploadedfile['fileext'], array('jpg', 'gif', 'jpeg', 'png', 'bmp'))) {
						$uploadedfile['isimage'] = 1;
					}
					$file_path = $this->upload_path.date('Y/md/');
					pc_base::load_sys_func('dir');
					dir_create($file_path);
					$new_file = date('Ymdhis').rand(100, 999).'.'.$uploadedfile['fileext'];
					$uploadedfile['filepath'] = date('Y/md/').$new_file;
					$aid = $attachment->add($uploadedfile);
				}
				$filepath = date('Y/md/');
				file_put_contents($this->upload_path.$filepath.$new_file, $pic);
			} else {
				return false;
			}
			echo $this->upload_url.$filepath.$new_file;
			exit;
		}
	}
	
	/**
	 * 删除附件
	 */
	public function swfdelete() {
		$attachment = surphp::load_core_class('attachment');
		$att_del_arr = explode('|',$_GET['data']);
		foreach($att_del_arr as $n=>$att){
			if($att) $attachment->delete(array('aid'=>$att,'userid'=>$this->userid,'uploadip'=>ip()));
		}
	}
	

	/**
	 * 加载图片库
	 */
	public function album_load() {
		if(!$this->admin_username) return false;
		$where = $uploadtime= '';
		$this->att_db= M('attachment');
		if($_GET['args']) extract(getswfinit($_GET['args']));
		if($_GET['dosubmit']){
			extract($_GET['info']);
			$where = '';
			$filename = safe_replace($filename);
			if($filename) $where = "AND `filename` LIKE '%$filename%' ";
			if($uploadtime) {
				$start_uploadtime = strtotime($uploadtime.' 00:00:00');
				$stop_uploadtime = strtotime($uploadtime.' 23:59:59');
				$where .= "AND `uploadtime` >= '$start_uploadtime' AND  `uploadtime` <= '$stop_uploadtime'";				
			}
			if($where) $where = substr($where, 3);
		}
		surphp::load_core_class('form');
		$page = $_GET['page'] ? $_GET['page'] : '1';
		$infos = $this->att_db->listinfo($where, 'aid DESC', $page, 8,'','*');
		foreach($infos as $n=>$v){
			$ext = fileext($v['filepath']);
			if(in_array($ext,$this->imgext)) {
				$infos[$n]['src']=$this->upload_url.$v['filepath'];
				$infos[$n]['width']='80';
			} else {
				$infos[$n]['src']=file_icon($v['filepath']);
				$infos[$n]['width']='64';
			}
		}
		$pages = $this->att_db->pages;
		include $this->admin_tpl('album_list');
	}
	
	/**
	 * 目录浏览模式添加图片
	 */
	public function album_dir() {
		if(!$this->admin_username) return false;
		if($_GET['args']) extract(getswfinit($_GET['args']));
		$dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\','..'), '', trim($_GET['dir'])) : '';
		$filepath = $this->upload_path.$dir;
		$list = glob($filepath.'/'.'*');
		if(!empty($list)) rsort($list);
		$local = str_replace(array(WEBROOT, SUR_PATH ,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('','',DIRECTORY_SEPARATOR), $filepath);
		$url = ($dir == '.' || $dir=='') ? $this->upload_url : $this->upload_url.str_replace('.', '', $dir).'/';
		$show_header = true;
		include $this->admin_tpl('album_dir');
	}
	
	/**
	 * 设置upload上传的json格式cookie
	 */
	private function upload_json($aid,$src,$filename) {
		$arr['aid'] = intval($aid);
		$arr['src'] = trim($src);
		$arr['filename'] = urlencode($filename);
		$json_str = json_encode($arr);
		$att_arr_exist = Cookie::getCookie('att_json');
		$att_arr_exist_tmp = explode('||', $att_arr_exist);
		if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
			return true;
		} else {
			$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
			Cookie::setCookie('att_json',$json_str);
			return true;			
		}
	}
	
	/**
	 * 设置swfupload上传的json格式cookie
	 */
	public function swfupload_json() {
		$arr['aid'] = intval($_GET['aid']);
		$arr['src'] = trim($_GET['src']);
		$arr['filename'] = urlencode($_GET['filename']);
		$json_str = json_encode($arr);
		$att_arr_exist = Cookie::getCookie('att_json');
		$att_arr_exist_tmp = explode('||', $att_arr_exist);
		if(is_array($att_arr_exist_tmp) && in_array($json_str, $att_arr_exist_tmp)) {
			return true;
		} else {
			$json_str = $att_arr_exist ? $att_arr_exist.'||'.$json_str : $json_str;
			Cookie::setCookie('att_json',$json_str);
			return true;			
		}
	}
	
	/**
	 * 删除swfupload上传的json格式cookie
	 */	
	public function swfupload_json_del() {
		$arr['aid'] = intval($_GET['aid']);
		$arr['src'] = trim($_GET['src']);
		$arr['filename'] = urlencode($_GET['filename']);
		$json_str = json_encode($arr);
		$att_arr_exist = Cookie::getCookie('att_json');
		$att_arr_exist = str_replace(array($json_str,'||||'), array('','||'), $att_arr_exist);
		$att_arr_exist = preg_replace('/^\|\|||\|\|$/i', '', $att_arr_exist);
		Cookie::setCookie('att_json',$att_arr_exist);
	}		
	
	/**
	 * 
	 * 测试上传文件
	 * @param unknown_type $file
	 * @param unknown_type $m
	 */
	function test(){
	    surphp::load_core_class('form');
	    $this->requireLogin();
	    include $this->admin_tpl('test');
	}
	

	
	final public static function admin_tpl($file, $m = '') {
		$m = empty($m) ? ROUTE_M : $m;
		if(empty($m)) return false;
		return SUR_PATH.'apps'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
	}
	final public static function get_siteid() {
		return get_siteid();
	}	
}
?>