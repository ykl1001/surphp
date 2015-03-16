<?php
define('IN_ADMIN', TRUE);
$show_dialog = true;
include $this->admin_tpl('header','attachment');
echo Form::images('img');

echo Form::upfiles('file');