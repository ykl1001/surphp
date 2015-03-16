<?php
class indexAction extends Action{
	public function suggestion(){
		
		if($this->post('dosubmit')){
			$content = $this->post('content');
			if ($content) {
				$data = array('content'=>$content,'ip'=>ip(),'email'=>$this->post('email'),'qq'=>$this->post('qq'),'posttime'=>SYS_TIME);
				$insertid = M('suggestion')->insert($data, TRUE);
				if ($insertid) {
					$this->ajaxReturn(1);
				}else {
					$this->ajaxReturn(0,'','未成功提交');
				}
			}else {
				$this->ajaxReturn(0,'','内容不能为空');
			}
			
		}
		
		include $this->template('suggestion');
	}
}