<?php
class Error {
    // CATCHABLE ERRORS
     public static function captureNormal( $number, $message, $file, $line )
     {         
         if($number == 8) return '';
         
         $errfile = str_replace(SUR_PATH,'',$file);
         if(surphp::load_config('config','DEVELOP_MODE')) {
             //echo '<div style="font-size:12px;text-align:left; border-bottom:1px solid #9cc9e0; border-right:1px solid #9cc9e0;'
             //.'padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>errorcode:' . $number . ',' 
             //. $message . ',file:<font color="blue">' . $errfile . '</font>,line' . $line .'<br /><a href="http://www.baidu.com/s?wd='.urlencode($message).'" target="_blank" style="color:red">Need Help?</a></span></div>';
             $trace         = debug_backtrace();
             
             $e['message']  = $message;
             $e['file']     = isset($trace[0]['file']) ? $trace[0]['file'] : '';
             $e['class']    = isset($trace[0]['class'])? $trace[0]['class']:'';
             $e['function'] = $trace[0]['function'];
             $e['line']     = isset($trace[0]['line']) ? $trace[0]['line'] : '';
             $traceInfo     = '';
             foreach($trace as $t)
             {
                 if (isset($t['file'])){
                     $traceInfo .= $t['file'].' ('.$t['line'].') ';
                 }
                 if (isset($t['class'])) {
                 	$traceInfo .= $t['class'].$t['type'].$t['function'];
                 }
                 
                 $traceInfo .= '('.@implode(', ', $t['args']);
                    $traceInfo .=")<br/>";
                 
             }
             $e['trace']  = $traceInfo;
             $title       = '系统发生错误';
             include CORE_PATH.'tpl'.DIRECTORY_SEPARATOR.'SurPHPException.tpl.php';
         } else {
             error_log('<?php exit;?>'.date('m-d H:i:s',SYS_TIME).' | '.$number.' | '.str_pad($message,30).' | '.$errfile.' | '.$line."\r\n", 3, CACHE_PATH.'error_log.php');
         }
     }
     
    // EXTENSIONS
     public static function captureException( $exception )
     {
         // Display content $exception variable
         $title       = '系统抛出异常';
         $e = $exception->__toString();
         include CORE_PATH.'tpl'.DIRECTORY_SEPARATOR.'SurPHPException.tpl.php';
         exit;
     }
     
    // UNCATCHABLE ERRORS
     public static function captureShutdown( )
     {
         $error = error_get_last( );
         if( $error ) {
             ## IF YOU WANT TO CLEAR ALL BUFFER, UNCOMMENT NEXT LINE:
             # ob_end_clean( );
             
            // Display content $error variable
             echo '<pre>';
             print_r( $error );
             echo '</pre>';
         } else { return true; }
     }
 
}