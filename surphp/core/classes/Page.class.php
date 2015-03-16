<?php
class Page{
    // 总行数
    var $totalRows;
    
    // 总页数
    var $totalPages;
    
    //当前页码
    var $currentPage;
    
    //额外调用函数
    var $extra;
    
    // 每页显示行数
    var $prepage;
    
    // 显示页面区间
    var $setpage;
    
    // 生成的分页代码
    var $html;
    
    var $urlrule;
    
     /**
     * 分页
     *
     * @param $num 信息总数
     * @param $curr_page 当前分页
     * @param $perpage 每页显示数
     * @param $urlrule URL规则
     * @param $array 需要传递的数组，用于增加额外的方法
     */
    function __construct($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(),$setpages = 8){
        $this->totalRows   = $num;
        $this->currentPage = $curr_page;
        $this->prepage     = $perpage;
        $this->extra       = $array;
        $this->setpage     = $setpages;
        
        if($urlrule == '') {
            $this->urlrule = $this->url_par('page={$page}');
        }
        
        $this->totalPages = ceil($this->totalRows / $this->prepage);
    }
    

    function show() {
        $this->html = '';
        if($this->totalRows > $this->prepage) {
            $page = $this->setpage+1;
            $offset = ceil($this->setpage/2-1);
            
            $from = $this->currentPage - $offset;
            $to = $this->currentPage + $offset;
            $more = 0;
            if($page >= $this->totalPages) {
                $from = 2;
                $to = $this->totalPages-1;
            } else {
                if($from <= 1) {
                    $to = $page-1;
                    $from = 2;
                }  elseif($to >= $this->totalPages) {
                    $from = $this->totalPages-($page-2);
                    $to = $this->totalPages-1;
                }
                $more = 1;
            }
            //$this->html .= '<a class="pag_lt" href="'.$this->pageurl($this->urlrule, 1, $this->extra).'">&lt;&lt;</a><span style="float:left;">共'.$this->totalRows.L('page_item').'</span>';
            if($this->currentPage>0) {
                $this->html .= ' <span class="page_num"><a href="'.$this->pageurl($this->urlrule, $this->currentPage-1, $this->extra).'" class="page_numlt this_page">上一页</a>';
                if($this->currentPage==1) {
                    $this->html .= ' <span>1</span>';
                } elseif($this->currentPage>6 && $more) {
                    $this->html .= ' <a href="'.$this->pageurl($this->urlrule, 1, $this->extra).'">1</a>..';
                } else {
                    $this->html .= ' <a href="'.$this->pageurl($this->urlrule, 1, $this->extra).'">1</a>';
                }
            }
            for($i = $from; $i <= $to; $i++) {
                if($i != $this->currentPage) {
                    $this->html .= ' | <a href="'.$this->pageurl($this->urlrule, $i, $this->extra).'">'.$i.'</a>';
                } else {
                    $this->html .= ' | <span>'.$i.'</span>';
                }
            }
            if($this->currentPage < $this->totalPages) {
                if($this->currentPage < $this->totalPages-5 && $more) {
                    $this->html .= ' | ..<a href="'.$this->pageurl($this->urlrule, $this->totalPages, $this->extra).'">'.$this->totalPages.'</a> <a href="'.$this->pageurl($this->urlrule, $this->currentPage+1, $this->extra).'" class="page_numlt">下一页</a></span>';
                } else {
                    $this->html .= ' | <a href="'.$this->pageurl($this->urlrule, $this->totalPages, $this->extra).'">'.$this->totalPages.'</a> <a href="'.$this->pageurl($this->urlrule, $this->currentPage+1, $this->extra).'" class="page_numlt">下一页</a></span>';
                }
            } elseif($this->currentPage == $this->totalPages) {
                $this->html .= ' | <span>'.$this->totalPages.'</span> <a href="'.$this->pageurl($this->urlrule, $this->currentPage, $this->extra).'" class="page_numlt">下一页</a></span>';
            } else {
                $this->html .= ' | <a href="'.$this->pageurl($this->urlrule, $this->totalPages, $this->extra).'">'.$this->totalPages.'</a> <a href="'.$this->pageurl($this->urlrule, $this->currentPage+1, $this->extra).'" class="page_numlt">下一页</a></span>';
            }
        }
        
        return $this->html;
    }
    /**
     * 返回分页路径
     *
     * @param $urlrule 分页规则
     * @param $page 当前页
     * @param $array 需要传递的数组，用于增加额外的方法
     * @return 完整的URL路径
     */
    function pageurl($urlrule, $page, $array = array()) {
        if(strpos($urlrule, '~')) {
            $urlrules = explode('~', $urlrule);
            $urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
        }
        $findme = array('{$page}');
        $replaceme = array($page);
        if (is_array($array)) foreach ($array as $k=>$v) {
            $findme[] = '{$'.$k.'}';
            $replaceme[] = $v;
        }
        $url = str_replace($findme, $replaceme, $urlrule);
        $url = str_replace(array('http://','//','~'), array('~','/','http://'), $url);
        return $url;
    }

    /**
     * URL路径解析，pages 函数的辅助函数
     *
     * @param $par 传入需要解析的变量 默认为，page={$page}
     * @param $url URL地址
     * @return URL
     */
    function url_par($par, $url = '') {
        if($url == '') $url = get_url();
        $pos = strpos($url, '?');
        if($pos === false) {
            $url .= '?'.$par;
        } else {
            $querystring = substr(strstr($url, '?'), 1);
            parse_str($querystring, $pars);
            $query_array = array();
            foreach($pars as $k=>$v) {
                if($k != 'page') $query_array[$k] = $v;
            }
            $querystring = http_build_query($query_array).'&'.$par;
            $url = substr($url, 0, $pos).'?'.$querystring;
        }
        return $url;
    }
}