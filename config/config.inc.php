<?php
/*
 * Created on 2013-4-14
 *
 * @author yangkunlin
 * @copyright surphp@yangkunlin 2013
 */
if (!defined('SUR_PATH')) exit();

return array(
	/* 常用配置 */
	'DB_TYPE'			=>	'mysql',		// 数据库类型
	'DB_HOST'			=>	SAE_MYSQL_HOST_M,	// 数据库服务器地址
	'DB_NAME'			=>	SAE_MYSQL_DB,			// 数据库名
	'DB_USER'			=>	SAE_MYSQL_USER,		// 数据库用户名
	'DB_PWD'			=>	SAE_MYSQL_PASS,	// 数据库密码
	'DB_PORT'			=>	SAE_MYSQL_PORT,			// 数据库端口
	'DB_PREFIX'         =>	'sp_',			// 数据库表前缀
	'DB_CHARSET'		=>	'utf8',			// 数据库编码

	/* 网站配置 */
	'COOKIE_PREFIX'	    =>	'TEst87x_',	// cookie前缀
    'COOKIE_DOMAIN'	    =>	'',	// cookie域名
    'COOKIE_PATH'	    =>	'/',	// cookie路径
    'AUTH_KEY'          =>'HpFJPrTRqwq7x8rG',
	'DEVELOP_MODE'		=>  true,
	'TIMEZONE'          => 'Etc/GMT-8', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
	'TPL_NAME'          => 'default',
	'JS_PATH' => '/static/js/',
	'CSS_PATH' => '/static/css/',
	'IMG_PATH' => '/static/image/',
    'UPLOAD_PATH'       => WEBROOT.'uploadfile/',
    'UPLOAD_URL'    =>'/uploadfile/',
	'ADMIN_LOG' => 1,
	'ERRORLOG' => 0,
	'ADMIN_LOG' => 1,
	'ERRORLOG' => 0,
	'ERRORLOG_SIZE'     => 20,//MB
	'MAXLOGINFAILEDTIMES'=>4,
    'SITE_URL' =>'http://kunlin.sinaapp.com/',
); 