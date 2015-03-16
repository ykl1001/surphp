<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div id="main_frameid" class="pad-10" style="_margin-right:-12px;_width:98.9%;">
<script type="text/javascript">
$(function(){if ($.browser.msie && parseInt($.browser.version) < 7) $('#browserVersionAlert').show();}); 
</script>
<div class="explain-col mb10" style="display:none" id="browserVersionAlert">
<?php echo L('ie8_tip')?></div>
<div class="col-2 lf mr10" style="width:48%">
	<h6><?php echo L('personal_information')?></h6>
	<div class="content">
	<?php echo L('main_hello')?><?php echo $admin_username?><br />
	<?php echo L('main_role')?><?php echo $rolename?> <br />
	<div class="bk20 hr"><hr /></div>
	<?php echo L('main_last_logintime')?><?php echo date('Y-m-d H:i:s',$logintime)?><br />
	<?php echo L('main_last_loginip')?><?php echo $loginip?> <br />
	</div>
</div>
<div class="col-2 col-auto">
	<h6><?php echo L('main_safety_tips')?></h6>
	<div class="content" style="color:#ff0000;">
<?php if($pc_writeable) {?>	
<?php echo L('main_safety_permissions')?><br />
<?php } ?>
<?php if(surphp::load_config('config','DEVELOP_MODE')) {?>
<?php echo L('main_safety_debug')?><br />
<?php } ?>
	<div class="bk20 hr"><hr /></div>	

	</div>
</div>
<div class="bk10"></div>
<div class="col-2 lf mr10" style="width:48%">
	<h6><?php echo L('main_product_team')?></h6>
	<div class="content">
	<?php echo L('main_copyright')?><?php echo $product_copyright?><br />
	<?php echo L('main_product_planning')?><?php echo $architecture?><br />
	<?php echo L('main_product_dev')?><?php echo $programmer;?><br />
	<?php echo L('main_product_ui')?><?php echo $designer;?><br />
	</div>
</div>
<div class="col-2 col-auto">
	<h6><?php echo L('main_sysinfo')?></h6>
	<div class="content">
	<?php echo L('main_version')?>1.0<br />
	<?php echo L('main_os')?><?php echo $sysinfo['os']?> <br />
	<?php echo L('main_web_server')?><?php echo $sysinfo['web_server']?> <br />
	<?php echo L('main_sql_version')?><?php echo $sysinfo['mysqlv']?><br />
	<?php echo L('main_upload_limit')?><?php echo $sysinfo['fileupload']?><br />	
	</div>
</div>

<div class="bk10"></div>

</div>
</body></html>