<?php
/**
 * dao类
 * @package sys
 * 
 */

class sys_redisPool {
	private $data;
	private static $instance;


	public function sys_dao()
	{
	}


	/**
	 * 取得Redis对象的实例
	 *
	 * @return Redis
	 */
	public static function getInstance()
	{
		if (! self::$instance) {
			$className = __CLASS__;
			self::$instance = new $className();
		}
		
		return self::$instance;
	}
	

	/**
	 * 获取Redis
	 * *
	 */
	public function getRedis($config)
	{
		$redisName = "".$config['host'].$config['port'];		
		// read from cache
		if (isset($this->data[$redisName]))
			return $this->data[$redisName];
		
		$redis = new Redis();
		$redis->connect($config['host'], $config['port']);	
		$redis->select($config['select']);
		
		$this->data[$redisName] = $redis;	
		return $redis;
	}

}

