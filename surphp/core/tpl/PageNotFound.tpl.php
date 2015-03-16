<?php
/*
 * Created on 2013-4-25
 *
 * @author yangkunlin
 * @copyright surphp@yangkunlin 2013
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>页面不存在</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<style>
body{
	font-family: Tahoma, Calibri, Verdana, arial, sans-serif;
	font-size:14px;
}
a{text-decoration:none;color:#8FBC8B;}
a:hover{ text-decoration:none;color:#B0C4DE;}
span{color:#8FBC8B;}
</style>
<script>
var timer = 3;
var timerid = 0;
function Jump(){
  if(timer == 0){
      window.location.href = '<?php echo SITE_URL?>';
      clearInterval(timerid);
  }
  document.getElementById('timer').innerHTML = timer;
  timer--;
}
window.onload = function(){
	timerid = setInterval("Jump()" , 1000);	
};
</script>
</head>
<body>
<div style="margin:0 auto;width:500px;padding:80px 0;text-align:center;border:1px solid #00ff00;"><font style="font-size:16px;color:red;font-weight:bold;margin-top:40px;">404 Page Not Found!</font>
<p><br/><span id="timer">3</span><span>秒后跳转到指定页面，</span><a href="<?php echo SITE_URL?>">如果您的浏览器没有自动跳转，请点击这里</a></p>
</div>
<div align="center" style="color:#C0C0C0;margin:5pt;font-family:Calibri">  Copyright © 2013 SurPHP </span>
</div>
</body>
</html>