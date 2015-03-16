<?php
class categoryAction extends Action{
	public function add(){
		$this->requireLogin();
		if ($this->user_id && $cname = new_addslashes($this->post('cname'))) {
			// check is unique
			if (M('category')->count('uid='.$this->user_id.' AND cname=\''.$cname.'\'')) {
				$this->ajaxReturn(0,'','分类名重复');
			}
			
			// check cat num limit
			if (M('category')->count('uid='.$this->user_id) > 10) {
				$this->ajaxReturn(0,'','分类数量超过限制');
			}
			
			$data = array('uid'=>$this->user_id,
					'cname'=>$cname);
			$cid = M('category')->insert($data,TRUE);
			if ($cid){
				$this->ajaxReturn(1);
			}
		}
		$this->ajaxReturn(0,'','未添加成功');
	} 
	
	public function delete(){
		$this->requireLogin();
		if ($this->user_id && intval($this->get('cid')) > 0) {
			if (M('category')->delete('id='.intval($this->get('cid'))) ){
				$this->success('成功删除分类');
			}
		}
		$this->success('未成功删除分类');
	}
}