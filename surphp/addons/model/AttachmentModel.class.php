<?php
class AttachmentModel extends Model{
	public function __construct(){
		parent::__construct('attachment');
	}
	
	public function api_add($uploadedfile) {
		$uploadfield = array();
		$uploadfield = $uploadedfile;
		unset($uploadfield['fn']);
		$uploadfield = new_addslashes($uploadfield);
		$this->insert($uploadfield);
		$aid = $this->insert_id();
		$uploadedfile['aid'] = $aid;
		return $aid;
	}
	
	/*
	 * 附件删除接口
	 * @param string 附件id
	 */
	public function api_delete($aid) {
		$aid = trim($aid);
		if($aid=='') return false;
		$attachment = surphp::load_core_class('attachment');
		return $attachment->delete(array('aid'=>$aid));	
	}
}