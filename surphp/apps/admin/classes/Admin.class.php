<?php
class Admin{
	final static public function loadAdmin(){
		static $isloaded;
		if (!isset($isloaded)) {
			$classpath = SUR_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'action'.DIRECTORY_SEPARATOR.'adminAction.class.php';
			include $classpath;
		}
	}
}