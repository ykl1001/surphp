<?php
surphp::load_app_func('comment', 'blog');
class commentAction extends Action{
	public function post(){
		if (($id = $this->get('id'))) {
			$content = $this->get('content');
			$ip      = ip();
			$top_id  = $id;
			$to_id   = $this->get('to_id');
			$reply   = $to_id > 0 ? 1 : 0;
			$create_at = SYS_TIME;
			$data = array(
					'uid'=>$this->user_id,
			        'username'=>$this->user_name,
					'content'=>$content,
					'to_id'=>$to_id,
					'top_id'=>$top_id,
					'ip'=>$ip,
					'reply'=>$reply,
					'create_at'=>$create_at			            
			);
			$comment_id = M('comment')->insert($data,TRUE);
			if ($comment_id > 0) {
				$data['id'] = $comment_id;
				$res['html'] = formatComment(array($data));
				$this->ajaxReturn('1',$res);
			}else{
				// do nothing
			}
		}
		$this->ajaxReturn('0');
	}
	
	public function delete(){
		if (($id = $this->get('id'))) {
			$rs = M('comment')->delete('id='.$id);
			if($rs){
				// delete childern
				M('comment')->delete('to_id='.$id);
				$this->ajaxReturn('1');
			}
		}
		$this->ajaxReturn('0');
	}
}