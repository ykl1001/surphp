<?php
surphp::load_core_class('cookie');
class passportAction extends Action{
    public function login(){
    	if ($this->post('dosubmit') && $this->post('username')) {
    	    surphp::load_core_class('Session',1);
    	    if (isset($_SESSION['code']) && strcasecmp($_SESSION['code'], $this->post('vcode')) != 0) {
    			$this->error('验证码不正确');
    		}
    		
    		$username = $this->post('username');
    		$password = $this->post('password');
    		$user = M('user')->get_one('username=\''.$username.'\'');
    		if ($user) {
    			if ($user['password'] == password($password,$user['encrypt'])) {
    				surphp::load_core_class('Session',1);
    				$_SESSION['userid'] = $user['userid'];
    				$_SESSION['username'] = $user['username'];
    				
    				//init cookie
    				if ($this->post('auto')){
    					$auth_key = md5(surphp::load_config('config', 'AUTH_KEY').$_SERVER['HTTP_USER_AGENT']);
    					$txt = $user['userid'].'\t'.$user['password'];
    					$auth_key = authcode($txt,'ENCODE',$auth_key);
    					Cookie::setCookie('auth', $auth_key, SYS_TIME + 1036800);
    				}    				
    				
    				$this->success('登录成功',SITE_URL);
    			}else{
    				$this->error('密码不正确');
    			}
    		}else {
    			$this->error('用户不存在');
    		}
    	}
        $this->setTitle('用户登录');
        include $this->template('login','user');
    }
    
    public function signup(){
    	if ($this->post('dosubmit')) {
    		surphp::load_core_class('Session',1);
    		
    		if (isset($_SESSION['code']) && strcasecmp($_SESSION['code'], $this->post('vcode')) != 0) {
    			$this->error('验证码不正确');
    		}
    		
    		// check username
    		if ($res = M('user')->count('username=\''.$this->get('username').'\'')) {
    			$this->error('用户已存在');
    		}
    		
    		// check email
    		if ($res = M('user')->count('email=\''.$this->get('email').'\'')) {
    			$this->error('Email已存在');
    		}
    		
    		$password = password($this->post('password'));
    		$user = array(
    				'email'=>$this->post('email'),
    				'username'=>$this->post('username'),
    				'password'=>$password['password'],
    				'encrypt'=>$password['encrypt'],
    				'createtime'=>SYS_TIME
    		);
    		$userid = M('user')->insert($user, true);
    		if ($userid) {
    			$_SESSION['userid'] = $userid;
    			$_SESSION['username'] = $this->post('username');
    			
    			$default_cat = array('uid'=>$userid,'cname'=>'默认'); 
    			M('category')->insert($default_cat);
    			$default_cat = array('uid'=>$userid,'cname'=>'工作');
    			M('category')->insert($default_cat);
    			$default_cat = array('uid'=>$userid,'cname'=>'学习');
    			M('category')->insert($default_cat);
    			$default_cat = array('uid'=>$userid,'cname'=>'生活');
    			M('category')->insert($default_cat);
    			$default_cat = array('uid'=>$userid,'cname'=>'娱乐');
    			M('category')->insert($default_cat);
    			
    			$this->success('注册成功',SITE_URL);
    		}else {
    			$this->error('无法创建用户，请稍后重试！');
    		}
    	}
        $this->setTitle('用户注册');
        include $this->template('signup','user');
    }
    
    public function checkemail(){
    	$res = M('user')->count('email=\''.$this->get('email').'\'');
    	if ($res > 0) {
    		$this->ajaxReturn('1','','','html');
    	}else{
    		$this->ajaxReturn('0','','','html');
    	}
    }
    
    public function checkuname(){
    	$res = M('user')->count('username=\''.$this->get('username').'\'');
    	if ($res > 0) {
    		$this->ajaxReturn('1','','','html');
    	}else{
    		$this->ajaxReturn('0','','','html');
    	}
    }
    
    public function logout(){
    	surphp::load_core_class('Session',1);
    	unset($_SESSION['userid']);
    	unset($_SESSION['username']);
    	Cookie::delCookie('auth');
    	$this->success('退出成功！',SITE_URL);
    }
}