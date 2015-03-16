<?php
class Cookie{
    public static function setCookie($name, $value='', $expire = 0){
        $prefix = surphp::load_config('config','COOKIE_PREFIX');
        $name   = $prefix.$name;
        $domain = surphp::load_config('config','COOKIE_DOMAIN');
        $path   = surphp::load_config('config','COOKIE_PATH');
        $secure = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
        $expire = empty($value) && $expire == 0 ? SYS_TIME - 3600 : $expire;
        return setcookie($name, authcode($value, 'ENCODE'), $expire, $path, $domain, $secure);
    }
    
    public static function delCookie($name){
        $prefix = surphp::load_config('config','COOKIE_PREFIX');
        $name   = $prefix.$name;
        $domain = surphp::load_config('config','COOKIE_DOMAIN');
        $path   = surphp::load_config('config','COOKIE_PATH');
        $secure = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
        return setcookie($name, '', SYS_TIME - 3600, $path, $domain, $secure);
    }
    
    public static function getCookie($name, $default=''){
        $prefix = surphp::load_config('config','COOKIE_PREFIX');
        $name   = $prefix.$name;
        return isset($_COOKIE[$name]) ? authcode($_COOKIE[$name]) : '';
    }
}