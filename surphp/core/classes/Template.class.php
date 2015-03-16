<?php
final class Template {
    
    /**
     * 
     * 编译模板处理方法
     * @param $tpl_name 模板文件 app/tpl/style/tpl_file
     */
    function tpl_compile($tpl_name, $app_name = '', $style='default') {
        if ($tpl_name){
            if (!$app_name) $app_name = ROUTE_M;
            $tpl_path = SUR_PATH.'apps'.DIRECTORY_SEPARATOR.$app_name.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$tpl_name.'.html';
            if (!file_exists($tpl_path))
            {
                // @todo log
                echo 'template files "'.$tpl_path.'" is not exist!';
                return FALSE;
            }
            $content = @file_get_contents($tpl_path);
            $content = $this->parse_content($content);
            $cache_dir = CACHE_PATH.'caches_template'.DIRECTORY_SEPARATOR.$style.DIRECTORY_SEPARATOR.$app_name.DIRECTORY_SEPARATOR;
            if (!is_dir($cache_dir)){
                mkdir($cache_dir, 0777, true);
            }
            
            $cache_file = $cache_dir.$tpl_name.'.php';
            if (file_exists($cache_file) && @filemtime($tpl_path) < @filemtime($cache_file)){
                return $cache_file;
            }
            
            if (touch($cache_dir.'index.html')){
                
                file_put_contents( $cache_file, $content );
                chmod ( $cache_file, 0777 );
                return $cache_file;
            } else {
                // @todo log
                echo 'template cache file is unwritable!';
                return FALSE;
                
            }
            
        }
    }
    
    /**
     * 
     * 编译模板
     * @param $content 模板内容
     */
    function parse_content($content) {
        $content = preg_replace ( "/\{template\s+(.+)\}/", "<?php include template(\\1); ?>", $content );
		$content = preg_replace ( "/\{include\s+(.+)\}/", "<?php include \\1; ?>", $content );
		$content = preg_replace ( "/\{php\s+(.+)\}/", "<?php \\1?>", $content );
		$content = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $content );
		$content = preg_replace ( "/\{else\}/", "<?php } else { ?>", $content );
		$content = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $content );
		$content = preg_replace ( "/\{\/if\}/", "<?php } ?>", $content );
		//for 循环
		$content = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$content);
		$content = preg_replace("/\{\/for\}/","<?php } ?>",$content);
		//++ --
		$content = preg_replace("/\{\+\+(.+?)\}/","<?php ++\\1; ?>",$content);
		$content = preg_replace("/\{\-\-(.+?)\}/","<?php ++\\1; ?>",$content);
		$content = preg_replace("/\{(.+?)\+\+\}/","<?php \\1++; ?>",$content);
		$content = preg_replace("/\{(.+?)\-\-\}/","<?php \\1--; ?>",$content);
		$content = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}/", "<?php \$n=1;if(is_array(\\1)) foreach(\\1 AS \\2) { ?>", $content );
		$content = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 AS \\2 => \\3) { ?>", $content );
		$content = preg_replace ( "/\{\/loop\}/", "<?php \$n++;}unset(\$n); ?>", $content );
		$content = preg_replace ( "/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace ( "/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace ( "/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $content );
		$content = preg_replace("/\{(\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')",$content);
		$content = preg_replace ( "/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/s", "<?php echo \\1;?>", $content );
				
		return $content;
    }
    
    /**
     * 转义 // 为 /
     *
     * @param $var	转义的字符
     * @return 转义后的字符
     */
    public function addquote($var) {
    	return str_replace ( "\\\"", "\"", preg_replace ( "/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var ) );
    }
}