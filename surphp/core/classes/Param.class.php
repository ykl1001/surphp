<?php
/**
 * 获取GPC参数
 */
class Param {

    public static function  get($name, $post =0) {
    	if($post && isset($_POST[$name])){
    	    if(!get_magic_quotes_gpc()) {
	    	    $_POST[$name] = new_addslashes($_POST[$name]);
	    	    return $_POST[$name]; 
	    	} else{
	    	    return $_POST[$name];
	    	}
    	}
    	
    	if(isset($_REQUEST[$name])){
    	    if(!get_magic_quotes_gpc()) {
	    	    $_REQUEST[$name] = new_addslashes($_REQUEST[$name]);
	    	    return $_REQUEST[$name]; 
	    	}else {
	    	    return $_REQUEST[$name];
	    	}
    	}
    }
}
?>