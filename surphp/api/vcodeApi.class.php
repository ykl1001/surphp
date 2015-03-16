<?php
class vcodeApi extends Api{
    public function get(){
        surphp::load_core_class('Session',1);
        $vcode = surphp::load_core_class('verifycode',1);
        if (isset($_GET['code_len']) && intval($_GET['code_len'])) $vcode->code_len = intval($_GET['code_len']);
        if ($vcode->code_len > 8 || $vcode->code_len < 2) {
            $vcode->code_len = 4;
        }
        if (isset($_GET['font_size']) && intval($_GET['font_size'])) $vcode->font_size = intval($_GET['font_size']);
        if (isset($_GET['width']) && intval($_GET['width'])) $vcode->width = intval($_GET['width']);
        if ($vcode->width <= 0) {
            $vcode->width = 90;
        }
        if (isset($_GET['height']) && intval($_GET['height'])) $vcode->height = intval($_GET['height']);
        if ($vcode->height <= 0) {
            $vcode->height = 30;
        }
        if (isset($_GET['font_color']) && trim(urldecode($_GET['font_color'])) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode($_GET['font_color'])))) $vcode->font_color = trim(urldecode($_GET['font_color']));
        if (isset($_GET['background']) && trim(urldecode($_GET['background'])) && preg_match('/(^#[a-z0-9]{6}$)/im', trim(urldecode($_GET['background'])))) $vcode->background = trim(urldecode($_GET['background']));
        $vcode->doimage();
        $_SESSION['code'] = $vcode->get_code();
    }
    
    public function code(){
        surphp::load_core_class('Session',1);
        return strcasecmp($_SESSION['code'] , $this->data['vcode']) == 0 ? 1 : 0;
    }
    
}