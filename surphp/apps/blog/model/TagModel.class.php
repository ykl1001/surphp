<?php
class TagModel extends Model{
    public function __construct(){
    	parent::__construct('tag');
    }
    
    public function add($tags, $uid = 0, $type='blog'){
    	$tags = explode(',', $tags);
    	if (!empty($tags)) {
    		foreach ($tags as $tag){
    			$data = $this->get_one('uid='.$uid.' AND tagname=\''.$tag.'\'','*');
    			if (empty($data)) {
    				$this->insert(array('uid'=>$uid,'tagname'=>$tag, 'blog_num'=>1, 'note_num'=>1));
    			}else {
    				if ($type == 'blog') {
    					$field = 'blog_num=blog_num+1';
    				}else{
    					$field = 'note_num=note_num+1';
    				}
    				
    				$this->update($field,'id='.$data['id']);
    			}
    		}
    	}
    	
    }
    
    public function all($uid){
    	$tags = M('tag')->select('uid='.$uid,'tagname',10,'note_num,blog_num DESC');
    	
    	$data = array();
    	if (!empty($tags)) {
    		foreach ($tags as $tag){
    			$data[] = $tag['tagname'];
    		}
    	}
    	
    	return $data;
    }
    
}