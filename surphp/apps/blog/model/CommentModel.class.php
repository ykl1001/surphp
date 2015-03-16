<?php
class CommentModel extends Model{
	public function __construct(){
		parent::__construct('comment');
	}
	
	public function getComment($top_id, $to_id = 0){
		$where = 'top_id='.$top_id.' AND to_id='.$to_id;
		$data = $this->listinfo($where,'id DESC',1);
		
		if(!empty($data)){
			foreach ($data as &$comment){
				$comment['reply_data'] = $this->getComment($top_id,$comment['id']);
			}
		}
		
		return $data;
	}
}