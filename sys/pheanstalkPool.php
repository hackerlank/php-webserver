<?php
/**
 * dao类
 * @package sys
 * 
 */

class sys_pheanstalkPool {
	private $data;
	private static $instance;

	
	public function __construct() {        
	}
	
	/**
	 * 取得Pheanstalk对象的实例
	 *
	 * @return Pheanstalk
	 */
	public static function getInstance()
	{
		if (! self::$instance) {
			sys_bootstrap::loadClass( ROOT_PATH.'/lib/Pheanstalk/pheanstalk_init.php' );
			$className = __CLASS__;
			self::$instance = new $className();
		}
		
		return self::$instance;
	}
	
	/**
	 * 获取Pheanstalk
	 * *
	 */
	public function getPheanstalk($host = 'localhost', $port = 11300)
	{
		$pheanstalkName = "".$host.$port;
		// read from cache
		if (isset($this->data[$pheanstalkName]))
			return $this->data[$pheanstalkName];	
	
		$pheanstalk = new Pheanstalk_Pheanstalk($host,$port);
		$this->data[$pheanstalkName] = $pheanstalk;	
		return $pheanstalk;
	}

	public function getTubeName($server_id,$key){
		return "".$key.$server_id;
	}
}

