<?php
include $this->admin_tpl('header');?>
<script type="text/javascript">
<!--
	$(function(){
		SwapTab('setting','on','',5,<?php echo $_GET['tab'] ? $_GET['tab'] : '1'?>);
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});		
		$("#js_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_js_path')?>",onfocus:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_js_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_js_path').L('setting_end_with_x')?>"});
		$("#css_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_css_path')?>",onfocus:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_css_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_css_path').L('setting_end_with_x')?>"});
		
		$("#img_path").formValidator({onshow:"<?php echo L('setting_input').L('setting_img_path')?>",onfocus:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_img_path').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_img_path').L('setting_end_with_x')?>"});
			
		$("#upload_url").formValidator({onshow:"<?php echo L('setting_input').L('setting_upload_url')?>",onfocus:"<?php echo L('setting_upload_url').L('setting_end_with_x')?>"}).inputValidator({onerror:"<?php echo L('setting_upload_url').L('setting_input_error')?>"}).regexValidator({regexp:"(.+)\/$",onerror:"<?php echo L('setting_upload_url').L('setting_end_with_x')?>"});
		
	})
//-->
</script>
<form action="?m=admin&c=setting&a=save" method="post" id="myform">
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
            <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',5,1);"><?php echo L('setting_basic_cfg')?></li>
            <li id="tab_setting_2" onclick="SwapTab('setting','on','',5,2);"><?php echo L('setting_safe_cfg')?></li>
</ul>
<div id="div_setting_1" class="contentList pad-10">
<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_js_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[JS_PATH]" id="js_path" size="50" value="<?php echo JS_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_css_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[CSS_PATH]" id="css_path" size="50" value="<?php echo CSS_PATH?>"/></td>
  </tr> 
  <tr>
    <th width="120"><?php echo L('setting_img_path')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[IMG_PATH]" id="img_path" size="50" value="<?php echo IMG_PATH?>" /></td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_upload_url')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setconfig[UPLOAD_URL]" id="upload_url" size="50" value="<?php echo $UPLOAD_URL?>" /></td>
  </tr>
</table>
</div>
<div id="div_setting_2" class="contentList pad-10 hidden">
	<table width="100%"  class="table_form">
  <tr>
    <th width="120"><?php echo L('setting_admin_log')?></th>
    <td class="y-bg">
	  <input name="setconfig[ADMIN_LOG]" value="1" type="radio" <?php echo ($ADMIN_LOG==TRUE) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input name="setconfig[ADMIN_LOG]" value="0" type="radio" <?php echo ($ADMIN_LOG==FALSE) ? ' checked' : ''?>> <?php echo L('setting_no')?>
     </td>
  </tr>
  <tr>
    <th width="120"><?php echo L('setting_error_log')?></th>
    <td class="y-bg">
	  <input name="setconfig[ERRORLOG]" value="1" type="radio" <?php echo ($ERRORLOG==TRUE) ? ' checked' : ''?>> <?php echo L('setting_yes')?>&nbsp;&nbsp;&nbsp;&nbsp;
	  <input name="setconfig[ERRORLOG]" value="0" type="radio" <?php echo ($ERRORLOG==FALSE) ? ' checked' : ''?>> <?php echo L('setting_no')?>
     </td>
  </tr> 
  <tr>
    <th><?php echo L('setting_error_log_size')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[ERRORLOG_SIZE]" id="errorlog_size" size="5" value="<?php echo $ERRORLOG_SIZE?>"/> MB</td>
  </tr>     

  <tr>
    <th><?php echo L('setting_maxloginfailedtimes')?></th>
    <td class="y-bg"><input type="text" class="input-text" name="setting[MAXLOGINFAILEDTIMES]" id="maxloginfailedtimes" size="10" value="<?php echo $MAXLOGINFAILEDTIMES?>"/></td>
  </tr>

</table>
</div>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</div>
</div>
</form>
</body>
<script type="text/javascript">

function SwapTab(name,cls_show,cls_hide,cnt,cur){
    for(i=1;i<=cnt;i++){
		if(i==cur){
			 $('#div_'+name+'_'+i).show();
			 $('#tab_'+name+'_'+i).attr('class',cls_show);
		}else{
			 $('#div_'+name+'_'+i).hide();
			 $('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}

</script>
</html>