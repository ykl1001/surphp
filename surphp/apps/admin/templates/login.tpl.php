<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理员登录</title>
<link href="<?php echo CSS_PATH?>public.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH?>jquery.min.js?v=1.0"></script>
<link href="<?php echo CSS_PATH?>dialog.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>dialog.js"></script>
<style>
.body{margin:0 auto; width:960px;}
.main{margin:0 auto; width:960px;display:inline-block;}
.main .title{padding: 2px 5px;}
.main .row{padding:10px 0;}
.main .submit{padding:5px 30px;}
.main .text{float:left;}
.main .input{float:left;}
.passport{width:80%;border-bottom:1px #ccc solid;padding:0 10px 20px 0;margin:0 auto;}
.passport .shead{margin:0 auto;font-size:20px;text-align:center;padding:20px 0;}
.passport .login{width:406px;padding:0 15px;margin-left:260px;}
.passport .row{display:inline-block;width:100%;}
.passport .login .name{float:left;width:70px;text-align:right;}
.passport .login .value{float:left;width:306px;}
.passport .login .value .vcode{width:70px;}
.row .button{padding:5px 15px;*padding:5px 5px;}
.passport .login .signup{line-height:29px;float:left;margin-right:10px;}
.login .value a{margin-left:10px;}
.login .value .checkbox{margin:0 10px 0 0;}
.bottom{color:#C0C0C0;margin:5pt;font-family:Calibri;margin:0 auto;width:960px;text-align:center;padding:5px;line-height:30px;}
.bottom a.sugg{margin-left:30px;}
/*表单验证*/
.onShow,.onFocus,.onError,.onCorrect,.onLoad,.onTime{display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline; vertical-align:middle;background:url(<?php echo IMG_PATH?>msg_bg.png) no-repeat;   color:#444;line-height:18px;padding:2px 10px 2px 23px; margin-left:10px;_margin-left:5px}
.onShow{background-position:3px -147px;border-color:#40B3FF;color:#959595}
.onFocus{background-position:3px -147px;border-color:#40B3FF;}
.onError{background-position:3px -47px;border-color:#40B3FF; color:red}
.onCorrect{background-position:3px -247px;border-color:#40B3FF;}
.onLamp{background-position:3px -200px}
.onTime{background-position:3px -1356px}
</style>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({autotip:true,formid:"loginform",onerror:function(msg){}});
	$('#username').formValidator({onshow:"请输入用户名",onfocus:"用户名至少2-20个字符"}).inputValidator({min:2,max:20,onerror:"用户名长度不正确"}).regexValidator({regexp:"ps_username",datatype:"enum",onerror:"用户名格式不正确"});
	$("#password").formValidator({onshow:"请输入密码",onfocus:"密码至少6-10个字符"}).inputValidator({min:6,max:10,onerror:"密码至少6-10个字符"});
	$("#vcode").formValidator({onshow:"请输入验证码"}).ajaxValidator({
        type : "get",
        url : "<?php echo U('api/vcode/code');?>",
        data :"",
        datatype : "html",
        async:'false',
        success : function(data){
            if( data == "1" ) {
                return true;
            } else {
                return false;
            }
        },
        buttons: $("#dosubmit"),
        onerror : "验证码不正确",
        onwait : "在检查验证码..."
    });
});

function changecode(){
	var currentDate = new Date();
	document.getElementById('vcodeimage').src = '<?php echo U('api/vcode/get');?>'+'&t=' + currentDate.getTime();
}
</script>
</head>
<body>
<div class="body">
    <div class="main">
        <div class="passport">
	        <form action="<?php echo U('admin/index/login');?>" method="post" id="loginform">
	        <div class="shead">管理员登录</div>        
	        <div class="login">
	            <div class="row"><div class="name">用户名：</div><div class="value"><input type="text" id="username" name="username" value=""></div></div>
	            <div class="row"><div class="name">密码：</div><div class="value"><input type="password" id="password" name="password" value=""></div></div>
	            <div class="row"><div class="name">验证码：</div><div class="value"><input type="text" id="vcode" class="vcode" name="vcode" value=""><a href="javascript:;" onclick="changecode()"><img id="vcodeimage" src="<?php echo U('api/vcode/get','t='.SYS_TIME);?>"/></a></div></div>
	            <div class="row">
	                <div class="name">&nbsp;</div>
	                <div class="value">
	                    <div class="signup"><input type="submit" class="button" name="dosubmit" id="dosubmit" value="登录" /></div>
	                </div>
	            </div>
	        </div>
	        </form>
	    </div>
    </div>
	<div class="bottom">
	Copyright © 2013 
	</div>
</div>
</body>
</html>    