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
<title>提示信息</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<link href="<?php echo CSS_PATH;?>public.css" rel="stylesheet" type="text/css">
<script>
var timer = <?php echo $waitSecond?>;
var timerid = 0;
function Jump(){
  if(timer == 0){
      window.location.href = '<?php echo $jumpUrl?>';
      clearInterval(timerid);
  }
  document.getElementById('timer').innerHTML = timer;
  timer--;
}
window.onload = function(){
	timerid = setInterval("Jump()" , 1000);	
};
</script>
<base target="_self" />
<?php if ($status == 1){?>
  <div class="Prompt">
  	<div class="Prompt-inner">
      <p><i class="ico-ok"></i><?php echo $message?></p>
      <br/><span id="timer"><?php echo $waitSecond?></span><span>秒后跳转到指定页面，</span><a href="<?php echo $jumpUrl?>">如果您的浏览器没有自动跳转，请点击这里</a>
  </div>
  </div>
<?php } ?>
<?php if ($status == 0){?>
  <div class="Prompt">
  	<div class="Prompt-inner">
       <div class="box-ver"><i class="ico-error"></i><?php echo $message?></div>
       <br/><span id="timer"><?php echo $waitSecond?></span><span>秒后跳转到指定页面，</span><a href="<?php echo $jumpUrl?>">如果您的浏览器没有自动跳转，请点击这里</a>
    </div>
    </div>
<?php } ?>
</body>
</html>