CREATE TABLE `sp_session` (
  `sessionid` char(32) NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL,
  `lastvisit` int(10) unsigned NOT NULL default '0',
  `roleid` tinyint(3) unsigned default '0',
  `groupid` tinyint(3) unsigned NOT NULL default '0',
  `m` char(20) NOT NULL,
  `c` char(20) NOT NULL,
  `a` char(20) NOT NULL,
  `data` char(255) NOT NULL,
  PRIMARY KEY  (`sessionid`),
  KEY `lastvisit` (`lastvisit`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `sp_user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(20) NOT NULL,
  `email` char(32) NOT NULL DEFAULT '',
  `age` tinyint(3) unsigned NOT NULL,
  `gender` tinyint(3) unsigned NOT NULL,
  `password` char(32) NOT NULL,
  `encrypt` char(6) NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tagname` varchar(127) NOT NULL,
  `note_num` int(10) unsigned NOT NULL,
  `blog_num` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `num_order` (`note_num`,`blog_num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `title` varchar(127) NOT NULL,
  `content` text NOT NULL,
  `category` int(11) NOT NULL,
  `tag` varchar(127) NOT NULL,
  `pv` int(10) unsigned NOT NULL,
  `background` varchar(127) NOT NULL,
  `orderlist` int(10) unsigned NOT NULL,
  `modifytime` int(10) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`category`,`orderlist`,`modifytime`,`createtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `top_id` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `username` varchar(127) NOT NULL,
  `content` text NOT NULL,
  `to_id` int(10) unsigned NOT NULL,
  `ip` char(15) NOT NULL,
  `reply` int(10) unsigned NOT NULL,
  `create_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `top_id` (`top_id`,`to_id`,`reply`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `cname` varchar(127) NOT NULL,
  `note_num` int(10) unsigned NOT NULL,
  `blog_num` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_blog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `title` varchar(127) NOT NULL,
  `content` text NOT NULL,
  `category` int(11) NOT NULL,
  `tag` varchar(127) NOT NULL,
  `pv` int(10) unsigned NOT NULL,
  `orderlist` int(10) unsigned NOT NULL,
  `modifytime` int(10) unsigned NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`category`,`orderlist`,`createtime`),
  KEY `modifytime` (`modifytime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_suggestion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `email` varchar(127) NOT NULL,
  `qq` char(20) NOT NULL,
  `ip` char(15) NOT NULL,
  `posttime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_admin` (
  `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `roleid` smallint(5) DEFAULT '0',
  `encrypt` varchar(6) DEFAULT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) unsigned DEFAULT '0',
  `email` varchar(40) DEFAULT NULL,
  `realname` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`userid`),
  KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `sp_admin` (`userid`, `username`, `password`, `roleid`, `encrypt`, `lastloginip`, `lastlogintime`, `email`, `realname`) VALUES
(1, 'admin', 'f0b7f823878b4f0e7374a53f6dd28d67', 1, '11GY8Y', '127.0.0.1', 1371293834, 'admin@seec.com.cn', '信息技术部');

CREATE TABLE IF NOT EXISTS `sp_times` (
  `username` char(40) NOT NULL,
  `ip` char(15) NOT NULL,
  `logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `isadmin` tinyint(1) NOT NULL DEFAULT '0',
  `times` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`,`isadmin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_admin_role` (
  `roleid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`roleid`),
  KEY `listorder` (`listorder`),
  KEY `disabled` (`disabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `sp_admin_role` (`roleid`, `rolename`, `description`, `listorder`, `disabled`) VALUES
(1, '超级管理员', '超级管理员', 0, 0);

CREATE TABLE IF NOT EXISTS `sp_admin_role_priv` (
  `roleid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `m` char(20) NOT NULL,
  `c` char(20) NOT NULL,
  `a` char(20) NOT NULL,
  `data` char(30) NOT NULL DEFAULT '',
  `siteid` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `roleid` (`roleid`,`m`,`c`,`a`,`siteid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_log` (
  `logid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field` varchar(15) NOT NULL,
  `value` int(10) unsigned NOT NULL DEFAULT '0',
  `module` varchar(15) NOT NULL,
  `file` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `querystring` varchar(255) NOT NULL,
  `data` mediumtext NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`logid`),
  KEY `module` (`module`,`file`,`action`),
  KEY `username` (`username`,`action`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `sp_site` (
  `siteid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(30) DEFAULT '',
  `dirname` char(255) DEFAULT '',
  `domain` char(255) DEFAULT '',
  `site_title` char(255) DEFAULT '',
  `keywords` char(255) DEFAULT '',
  `description` char(255) DEFAULT '',
  `release_point` text,
  `default_style` char(50) DEFAULT NULL,
  `template` text,
  `setting` mediumtext,
  `uuid` char(40) DEFAULT '',
  PRIMARY KEY (`siteid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `sp_site` (`siteid`, `name`, `dirname`, `domain`, `site_title`, `keywords`, `description`, `release_point`, `default_style`, `template`, `setting`, `uuid`) VALUES
(1, 'sinaapp', '', 'http://kunlin.sinaapp.com/', 'sinaapp', 'sinaapp', 'sinaapp', '', 'default', 'default', 'array (\n  ''upload_maxsize'' => ''2048'',\n  ''upload_allowext'' => ''jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf'',\n  ''watermark_enable'' => ''1'',\n  ''watermark_minwidth'' => ''100'',\n  ''watermark_minheight'' => ''50'',\n  ''watermark_img'' => ''statics/images/water/NEWmark.png'',\n  ''watermark_pct'' => ''50'',\n  ''watermark_quality'' => ''80'',\n  ''watermark_pos'' => ''9'',\n)', 'e6289764-38ad-11e0-a028-001a4d46712d');

CREATE TABLE IF NOT EXISTS `sp_cache` (
  `filename` char(50) NOT NULL,
  `path` char(50) NOT NULL,
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`filename`,`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sp_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL DEFAULT '',
  `parentid` smallint(6) NOT NULL DEFAULT '0',
  `m` char(20) NOT NULL DEFAULT '',
  `c` char(20) NOT NULL DEFAULT '',
  `a` char(20) NOT NULL DEFAULT '',
  `data` char(100) NOT NULL DEFAULT '',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  `display` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `listorder` (`listorder`),
  KEY `parentid` (`parentid`),
  KEY `module` (`m`,`c`,`a`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `sp_menu` (`id`, `name`, `parentid`, `m`, `c`, `a`, `data`, `listorder`, `display`) VALUES
(7, 'extend', 0, 'admin', 'extend', 'init_extend', '', 7, '1'),
(977, 'extend_all', 7, 'admin', 'extend_all', 'init', '', 0, '1'),
(1073, 'delete_menu', 31, 'admin', 'menu', 'delete', '', 0, '0'),
(1072, 'edit_menu', 31, 'admin', 'menu', 'edit', '', 0, '0'),
(1053, 'role_delete', 50, 'admin', 'role', 'delete', '', 0, '0'),
(1052, 'member_manage', 50, 'admin', 'role', 'member_manage', '', 0, '0'),
(1050, 'role_edit', 50, 'admin', 'role', 'edit', '', 0, '0'),
(1038, 'settingsave', 30, 'admin', 'setting', 'save', '', 0, '0'),
(1042, 'admin_delete', 54, 'admin', 'admin_manage', 'delete', '', 0, '0'),
(1040, 'admin_edit', 54, 'admin', 'admin_manage', 'edit', '', 0, '0'),
(31, 'menu_manage', 977, 'admin', 'menu', 'init', '', 8, '1'),
(1023, 'delete_log', 997, 'admin', 'log', 'delete', '', 0, '0'),
(972, 'editinfo', 970, 'admin', 'admin_manage', 'public_edit_info', '', 0, '1'),
(971, 'editpwd', 970, 'admin', 'admin_manage', 'public_edit_pwd', '', 1, '1'),
(970, 'admininfo', 10, 'admin', 'admin_manage', 'init', '', 0, '1'),
(959, 'basic_config', 30, 'admin', 'setting', 'init', '', 10, '1'),
(979, 'safe_config', 30, 'admin', 'setting', 'init', '&tab=2', 11, '1'),
(853, 'role_priv', 50, 'admin', 'role', 'role_priv', '', 0, '0'),
(852, 'priv_setting', 50, 'admin', 'role', 'priv_setting', '', 0, '0'),
(815, 'site_del', 64, 'admin', 'site', 'del', '', 0, '0'),
(814, 'site_edit', 64, 'admin', 'site', 'edit', '', 0, '0'),
(67, 'add_site', 64, 'admin', 'site', 'add', '', 0, '0'),
(64, 'site_management', 30, 'admin', 'site', 'init', '', 2, '1'),
(58, 'admin_add', 54, 'admin', 'admin_manage', 'add', '', 0, '1'),
(51, 'role_add', 50, 'admin', 'role', 'add', '', 0, '1'),
(50, 'role_manage', 49, 'admin', 'role', 'init', '', 0, '1'),
(49, 'admin_setting', 1, 'admin', '', '', '', 0, '1'),
(54, 'admin_manage', 49, 'admin', 'admin_manage', 'init', '', 0, '1'),
(35, 'menu_add', 31, 'admin', 'menu', 'add', '', 0, '1'),
(30, 'correlative_setting', 1, 'admin', 'admin', 'admin', '', 0, '1'),
(1, 'sys_setting', 0, 'admin', 'setting', 'init', '', 1, '1'),
(997, 'log', 977, 'admin', 'log', 'init', '', 0, '1'),
(2, 'module', 0, 'admin', 'module', 'init', '', 2, '1'),
(29, 'module_manage', 2, 'admin', 'module', '', '', 0, '1'),
(857, 'attachment_manage', 29, 'attachment', 'manage', 'init', '', 0, '1');

CREATE TABLE IF NOT EXISTS `sp_attachment` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` char(15) NOT NULL,
  `filename` char(50) NOT NULL,
  `filepath` char(200) NOT NULL,
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `fileext` char(10) NOT NULL,
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isthumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `downloads` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uploadtime` int(10) unsigned NOT NULL DEFAULT '0',
  `uploadip` char(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `authcode` char(32) NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `authcode` (`authcode`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `sp_admin_panel` (
  `menuid` mediumint(8) unsigned NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` char(32) DEFAULT NULL,
  `url` char(255) DEFAULT NULL,
  `datetime` int(10) unsigned DEFAULT '0',
  UNIQUE KEY `userid` (`menuid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;