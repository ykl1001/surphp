<?php
/**
 * 
 * seesion处理类
 * @author Administrator
 *
 */
class Session{
    // second 半小时
    protected $gc_maxlifetime = 1800;
    protected $table_name = 'session';
    protected $db;
    
    /**
     * 
     * 构造方法
     */
    public function __construct(){
        $this->db = M($this->table_name); 
        session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
        session_start();
    }
    
    /**
     * 
     * open session
     */
    public function open($save_path, $session_name){
        // do nothing
        return(true);        
    }
    
    /**
     * 
     * close session
     */
    public function close(){
        $this->gc($this->gc_maxlifetime);
        return(true);
    }
    
    /**
     * 
     * read session
     * @param string $session_id
     */
    public function read($session_id){
        $r = $this->db->get_one(array('sessionid'=>$session_id), 'data');
        return $r ? $r['data'] : '';
    }
    
    public function write($session_id, $sess_data){
        $uid      = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
        $roleid   = isset($_SESSION['roleid']) ? $_SESSION['roleid'] : 0;
        $groupid  = isset($_SESSION['groupid']) ? $_SESSION['groupid'] : 0;
        $m        = defined('ROUTE_M') ? ROUTE_M : '';
        $c        = defined('ROUTE_C') ? ROUTE_C : '';
        $a        = defined('ROUTE_A') ? ROUTE_A : '';
        $data     = strlen($sess_data) > 255 ? '' : $sess_data;
        $ip       = ip();
        $insert_data = array(
                            'sessionid'=>$session_id,
							'userid'=>$uid,
							'ip'=>$ip,
							'lastvisit'=>SYS_TIME,
							'roleid'=>$roleid,
							'groupid'=>$groupid,
							'm'=>$m,
							'c'=>$c,
							'a'=>$a,
							'data'=>$data,
        );
        $insert_id = $this->db->insert($insert_data, TRUE, TRUE);
        return $insert_id ? TRUE : FALSE;
    }
    
    /**
     * 
     * destory session
     */
    public function destroy($session_id){
        return $this->db->delete(array('sessionid'=>$session_id));
    }
    
    /**
     * 
     * gc session
     * @param int $maxlifetime
     */
    public function gc($maxlifetime){
        $expire = SYS_TIME - $maxlifetime;
        return $this->db->delete('lastvisit < '.$expire);
    }
}