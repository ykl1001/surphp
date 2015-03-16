<?php

final class Db_Factory {

	/**
	 * 当前数据库工厂类静态实例
	 */
	private static $db_factory;
	
	/**
	 * 数据库配置列表
	 */
	protected $config = array();
	
	/**
	 * 数据库操作实例化对象
	 */
	protected $db_driver = null;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
	    $this->config = surphp::load_config('config');
	}
	
	/**
	 * 返回当前工厂类的单例对象
	 * @return object
	 */
	public static function get_instance() {
		if(!is_object(Db_Factory::$db_factory)) {
			Db_Factory::$db_factory = new Db_Factory();
		}
		return Db_Factory::$db_factory;
	}
	
	/**
	 * 获取数据库操作实例
	 * @return $db_driver 数据库操作实例
	 */
	public function generate_db() {
		if(!is_object($this->db_driver)) {
			$this->db_driver = $this->connect();
		}
		return $this->db_driver;
	}
	
	/**
	 *  加载数据库驱动
	 * @return object
	 */
	public function connect() {
		$db_driver = null;
		switch($this->config['DB_TYPE']) {
			case 'mysql' :
				surphp::load_core_class('Mysql', '', 0);
				$db_driver = new Mysql();
				break;
			case 'mysqli' :// @todo
				surphp::load_core_class('Mysqli','',0);
				$db_driver = new Mysqli();
				break;
			default :
				surphp::load_core_class('Mysql', '', 0);
				$db_driver = new Mysql();
		}
		// 初始化数据库
		$db_driver->open($this->config);
		return $db_driver;
	}

	/**
	 * 关闭数据库连接
	 * @return void
	 */
	protected function close() {
	    $this->db_driver->close();
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		$this->close();
	}
}
?>