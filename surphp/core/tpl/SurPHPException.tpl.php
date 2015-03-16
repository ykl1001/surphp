<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $title;?></title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<style>
body{
	font-family: Tahoma, Calibri, Verdana, arial, sans-serif;
	font-size:14px;
}
a{text-decoration:none;color:#8FBC8B;}
a:hover{ text-decoration:none;color:#B0C4DE;}
h2{
	border-bottom:1px solid #DDD;
	padding:8px 0;
    font-size:25px;
}
.title{
	margin:4px 0;
	color:#FF7F50;
	font-weight:bold;
}
.message,#trace{
	padding:1em;
	border:solid 1px #000;
	margin:10px 0;
	background:#FFD;
	line-height:150%;
}
.message{
	background:#FAF0E6;
	color:#2E2E2E;
		border:1px solid #E0E0E0;
}
#trace{
	background:#EEE8AA;
	border:1px solid #E0E0E0;
	color:#535353;
}
.notice{
    padding:10px;
	margin:5px;
	color:#F0E68C;
	background:#808080;
	border:1px solid #000;
}
.red{
	color:#F60;
	font-weight:bold;
}
</style>
</head>
<body>
<div class="notice">
<h2><?php echo $title;?></h2>
<div >您可以选择 [ <A HREF="<?php echo($_SERVER['PHP_SELF'])?>">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="<?php echo(SITE_URL);?>">回到首页</A> ]</div>
<?php if(isset($e['file'])) {?>
<p><strong>错误位置:</strong><span class="red"><?php echo $e['file'] ;?></span>　第<span class="red"><?php echo $e['line'];?></span>行</p>
<?php }?>
<p class="title">[ 错误信息 ]</p>
<p class="message"><?php echo $e['message'];?></p>
<?php if(isset($e['trace'])) {?>
<p class="title">[ 堆栈跟踪 ]</p>
<p id="trace">
<?php echo nl2br($e['trace']);?>
</p>
<?php }?>
</div>
<div align="center" style="color:#C0C0C0;margin:5pt;font-family:Calibri">
    Copyright © 2013 SurPHP
</div>
</body>
</html>