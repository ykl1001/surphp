<?php
/*
 * Created on 2013-4-14
 *
 * @author yangkunlin
 * @copyright surphp@yangkunlin 2013
 */
surphp::load_core_class('Session',0);
surphp::load_core_class('Cookie');
surphp::load_app_func('comment', 'blog');
class indexAction extends Action{
    function __construct(){
    	parent::__construct();
        $this->requireLogin();        
    }
    function init(){
        $a = array(1,2,3,4,5,6,7);
        surphp::load_core_class('param');
        //$this->success('操作成功');
        //$this->error('错误','',3);
        //$this->success('成功','http://www.baidu.com',3);
        //$this->pageNotFound();
        ///$users = M('user')->select();
        //var_pre(M('user')->getLastSql()) ;
        //var_dump($users);
        
        //$_SESSION['uid'] = 2;
        //$_SESSION['data'] = $users;
        //var_pre(Cookie::setCookie('test','www',0));
        //$encode = authcode('sdfsfsfs','ENCODE'); 
        //echo $encode;
        //echo '<br/>';
        //echo authcode($encode);
        //echo '<br/>';
        //var_dump(Cookie::getCookie('test')) ;
        //foreach ($array_expression as $value) {
        //    ;
        //}
        
        //throw new SurPHPException('用户定义异常',123);
        
        include $this->template('index');
    }
    
    public function add(){
    	$this->setTitle('写博客');
    	
    	if (isset($_POST['dosubmit'])) {
    		
    		if (!isset($_POST['title']) || empty($_POST['title'])) {
    			$this->error('标题不能为空');
    		}
    		
    		if (!isset($_POST['content']) || empty($_POST['content'])) {
    			$this->error('内容不能为空');
    		}
    		
    		$data = array(
    				'uid'=>$this->user_id,
    				'title'=>remove_xss(new_addslashes($_POST['title'])),
    				'content'=>remove_xss(new_addslashes($_POST['content'])),
    				'category'=>$_POST['category'],
    				'tag'=>$_POST['tag'],
    				'modifytime'=>SYS_TIME,
    				'createtime'=>SYS_TIME
    		);
    		$inserid = M('blog')->insert($data,true);
    		if ($inserid > 0) {
    			M('tag')->add($_POST['tag'],$this->user_id);
    			$this->success('发表博客成功');
    		}else {
    			$this->error("发生错误");
    		}
    		
    	}else {
    		$category = M('category')->select('uid='.$this->user_id,'*');
    		$tag = M('tag')->all($this->user_id);
    		include $this->template('add');
    	}
    	
    }
    
    public function lists() {
    	$this->setTitle('我的博客');
    	$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
    	    	
    	$where = 'uid='.$this->user_id;
    	if (isset($_GET['cid'])) {
    		$where .= ' AND category='.$_GET['cid'];
    	}
    	if (isset($_GET['tag'])) {
    		$where .= ' AND tag like \'%'.$_GET['tag'].'%\'';
    	}
    	$blogs = M('blog')->listinfo($where,'orderlist,id DESC',$page);
    	$category = M('category')->select('uid='.$this->user_id,'*','10','','','id');
    	$alltag = M('tag')->all($this->user_id);
    	include $this->template('lists');
    }
    
    public function delete(){
    	if (isset($_GET['id']) && $_GET['id'] > 0 && M('blog')->delete('id='.intval($_GET['id']))) {
    		$this->success('操作成功！',U('blog/index/lists'));
    	}else {
    		$this->error('操作不成功！',U('blog/index/lists'));
    	}
    }
    
    public function modify(){
    	$this->setTitle('修改博客');
    	 
    	if (isset($_POST['dosubmit'])) {
    	
    		if (!isset($_POST['title']) || empty($_POST['title'])) {
    			$this->error('标题不能为空');
    		}
    	
    		if (!isset($_POST['content']) || empty($_POST['content'])) {
    			$this->error('内容不能为空');
    		}
    	
    		$data = array(
    				'title'=>remove_xss(new_addslashes($_POST['title'])),
    				'content'=>remove_xss(new_addslashes($_POST['content'])),
    				'category'=>$_POST['category'],
    				'tag'=>$_POST['tag'],
    				'modifytime'=>SYS_TIME
    		);
    		$inserid = M('blog')->update($data,'id='.intval($_POST['id']));
    		if ($inserid) {
    			M('tag')->add($_POST['tag'],$this->user_id);
    			$this->success('修改博客成功');
    		}else {
    			$this->error("发生错误");
    		}
    	
    	}else {
    		if (isset($_GET['id']) && $_GET['id']) {
    			$blog = M('blog')->get_one('id='.intval($_GET['id']));
    		}
    		$category = M('category')->select('uid='.$this->user_id,'*');
    		$tag = M('tag')->all($this->user_id);
    		if (!empty($blog)) {
    			include $this->template('modify');
    		}else {
    			include $this->template('add');
    		}
    		
    	}
    }
    
    public function show(){
    	if (isset($_GET['id']) && ($blog = M('blog')->get_one('id='.intval($_GET['id']))) ) {
    		$category = M('category')->select('uid='.$this->user_id,'*');
    		$alltag = M('tag')->all($this->user_id);
    		// update pv
    		M('blog')->update('pv=pv+1', 'id='.intval($_GET['id']));
    		
    		$comment_data = M('comment')->getComment(intval($this->get('id')));
    		
    		$comment_html = formatComment($comment_data);
    		
    		include $this->template('show');
    	}else {
    		$this->error('博客不存在！');
    	}
    }
}