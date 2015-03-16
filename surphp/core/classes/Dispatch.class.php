<?php

class Dispatch {
	//路由配置
	private $route_config = '';
	
	function __construct(){
		surphp::load_core_class('Param');
		surphp::load_core_class('Action');
		surphp::load_core_class('Error');
		surphp::load_core_class('SurPHPException');
		$this->route_config = surphp::load_config('route', '');
		$this->init();
	}

    public function init() {
        //定义错误处理函数
        set_error_handler(array('Error','captureNormal'), E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        set_exception_handler( array( 'Error', 'captureException' ) );
        //register_shutdown_function( array( 'Error', 'captureShutdown' ) );
        
    	$controller = $this->route_app();
    	if (method_exists($controller, ROUTE_A)) {
			if (preg_match('/^[_]/i', ROUTE_A)) {
				exit('You are visiting a private action');
			} else {
				call_user_func(array($controller, ROUTE_A));
			}
		} else {
			exit('Action '.ROUTE_C.'Action->'.ROUTE_A.'() does not exist.');
		}
    }
        
    private function route_app(){
        $uri = $_SERVER['REQUEST_URI'];
        if (isset($this->route_config[$uri]) || (strpos($uri, '/') === 0 && isset($this->route_config[$uri = substr($uri, 1)]))){
            // for urlrewrite
            $app        = $this->route_config[$uri]['app'];
            $controller = $this->route_config[$uri]['c'];
            $action     = $this->route_config[$uri]['a'];
        }else if($route = $this->explode_uri($uri)){
            $app        = $route[0];
            $controller = $route[1];
            $action     = $route[2];
        }else {
            $app        = Param::get('m');
            $app        = $this->safe_replace($app);
            $controller = Param::get('c');
            $controller = $this->safe_replace($controller);
            $action     = Param::get('a');
            $action     = $this->safe_replace($action);
        }
        
        if(!$app){
            $app        = $this->route_config['default']['app'];
            $controller = $this->route_config['default']['c'];
            $action     = $this->route_config['default']['a'];
        }
        if (!$controller) {
        	exit('controller didn\'t give');
        }
        if(!$action){
            $action = 'init';
        }
        define('ROUTE_M', $app);
		define('ROUTE_C', $controller);
		define('ROUTE_A', $action);//echo ROUTE_M.'/'.ROUTE_C.'/'.ROUTE_A;
		if (ROUTE_M == 'api'){
		    surphp::load_core_class('api');
		    $filepath = SUR_PATH.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR.$controller.'Api.class.php';
		    if(file_exists($filepath)){
		        include $filepath;
		        $controller = $controller.'Api';
		        if (class_exists($controller,FALSE)){
		            if (method_exists($controller, ROUTE_A)) {
		                if (preg_match('/^[_]/i', ROUTE_A)) {
		                    exit('You are visiting a private action');
		                } else {
		                    // do api work
		                    $api = new $controller;
		                    $data = call_user_func(array(&$api, $action));
		                    
		                    $format = (in_array( $_REQUEST['format'] ,array('xml','json','php') ) ) ? $_REQUEST['format']:'json';
		                    if($format=='json'){
		                        exit(json_encode($data));
		                    }elseif ($format=='xml'){
		                         
		                    }elseif($format=='php'){
		                        dump($data);
		                    }else{
		                        echo $data;
		                    }
		                }
		                
		            }else {
		                exit('Api '.ROUTE_C.'Api->'.ROUTE_A.'() does not exist.');
		            }
		            
		        }else{
		             exit('Api class '.$controller.' does not exist.');
		        }
		    }else {
		        exit('Api '.$controller.'Api.class.php does not exist.');
		    }
		}else {
		    $filepath = SUR_PATH.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.$app.DIRECTORY_SEPARATOR.'action'.DIRECTORY_SEPARATOR.$controller.'Action.class.php';

		    if(file_exists($filepath)){
		        include $filepath;
		        $controller = $controller.'Action';
		        if(class_exists($controller,FALSE)){
		            return new $controller;
		        }else{
		            exit('Controller class '.$controller.' does not exist.');
		        }
		    } else {
		        exit('Controller '.$app.'/'.$controller.'Action.class.php does not exist.');
		    }
		}
        
        
    }
    
    private function safe_replace($str){
        return str_replace(array('/', '.'), '', $str);
    }
    
    private function explode_uri($uri){
        if (strpos($uri, '/') === 0) {
            $uri = substr($uri, 1);
        }
        $route = explode('/', $uri);
        if (isset($route[2]) && ( ($end = strpos($route[2], '/')) !== false  || ($end = strpos($route[2], '?')) !== false )) {
            $route[2] = substr($route[2], 0, $end);
        }
        return count($route) == 3 ? $route : false;
    }
}
?>