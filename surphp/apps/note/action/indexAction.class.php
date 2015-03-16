<?php
class indexAction extends Action{
    function __construct(){
    	parent::__construct();
        $this->requireLogin();
    }
    
    public function lists(){
    	if ($this->post('dosubmit')) {
    		$note_content = $this->post('content');
    		$note_category = $this->post('category');
    		$note_tag = $this->post('tag');
    		$note_background = $this->post('background');
    		if (preg_match('/^http:\/\/(.*)/',$note_content)) {
    			$content = file_get_contents($note_content, FALSE);
    			if (preg_match('/(<title>)(.*)(<\/title>)/',$content, $title)) {
    				$note_content = $title[2].'<br/><a href="'.$note_content.'">'.$note_content.'</a>';
    				$title = $title[2].'【链接】';
    			}
    		}else{
    			$title = remove_xss(new_addslashes(mStr($note_content,20)));
    		}
    		$post_data = array('uid'=>$this->user_id,
    				'title'=>$title,
    				'content'=>remove_xss(new_addslashes($note_content)),
    				'category'=>$note_category,
    				'tag'=>$note_tag,
    				'background'=>$note_background,
    				'modifytime'=>SYS_TIME,
    				'createtime'=>SYS_TIME
    		);
    		$inserid = M('note')->insert($post_data,true);
    		if ($inserid > 0) {
    			M('tag','blog')->add($note_tag, $this->user_id);
    		}
    	}
        $page = isset($_GET['p']) ? intval($_GET['p']) : 1;
    	    	
    	$where = 'uid='.$this->user_id;
    	if (isset($_GET['cid'])) {
    		$where .= ' AND category='.$_GET['cid'];
    	}
    	if (isset($_GET['tag'])) {
    		$where .= ' AND tag like \'%'.$_GET['tag'].'%\'';
    	}
    	$notes = M('note')->listinfo($where,'orderlist,id DESC',$page);
    	$category = M('category')->select('uid='.$this->user_id,'*','10','','','id');
    	$alltag = M('tag','blog')->all($this->user_id);
    	$this->setTitle('我的记事本');
    	include $this->template('lists');
    }
    
    public function add(){
    	$category = M('category')->select('uid='.$this->user_id,'*');
    	$tag = M('tag','blog')->all($this->user_id);
        include $this->template('add');
    } 
    
    public function modify(){
    
    	if (isset($_POST['dosubmit'])) {
    		 
    		$data = array(
    				'content'=>remove_xss(new_addslashes($_POST['content'])),
    				'category'=>$_POST['category'],
    				'tag'=>$_POST['tag'],
    				'background'=>$_POST['background'],
    				'modifytime'=>SYS_TIME
    		);
    		$res = M('note')->update($data,'id='.intval($_GET['id']));
    		if ($res) {
    			M('tag','blog')->add($_POST['tag'],$this->user_id);
    			$this->ajaxReturn(1);
    		}else {
    			$this->ajaxReturn(0);
    		}
    		 
    	}else {
    		if (isset($_GET['id']) && $_GET['id']) {
    			$note = M('note')->get_one('id='.intval($_GET['id']));
    		}
    		$category = M('category','blog')->select('uid='.$this->user_id,'*');
    		$tag = M('tag','blog')->all($this->user_id);
    		if (!empty($note)) {
    			include $this->template('modify');
    		}else {
    			$this->ajaxReturn(0,'笔记不存在','','html');
    		}
    
    	}
    }    
    
    public function delete(){
    	if (isset($_GET['id']) && $_GET['id'] > 0 && M('note')->delete('id='.intval($_GET['id']))) {
    		$this->success('操作成功！',U('note/index/lists'));
    	}else {
    		$this->error('操作不成功！',U('note/index/lists'));
    	}
    }
    
    public function show(){
    	if (isset($_GET['id']) && ($note = M('note')->get_one('id='.intval($_GET['id']))) ) {
    		$category = M('category','blog')->select('uid='.$this->user_id,'*');
    		$alltag = M('tag','blog')->all($this->user_id);
    		// update pv
    		M('note')->update('pv=pv+1', 'id='.intval($_GET['id']));
    	
    		include $this->template('show');
    	}else {
    		$this->ajaxReturn(0,'笔记不存在！','','html');
    	}
    }
}