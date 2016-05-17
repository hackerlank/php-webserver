<?php
/**
 * dao类
 * @package sys
 * 
 */

class sys_memcachePool {
	private $data;
	private static $instance;


	public function sys_dao()
	{
	}


	/**
	 * 取得Memcache对象的实例
	 *
	 * @return Memcache
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
	 * 获取Memcache
	 * *
	 */
	public function getMemcache($config)
	{
		$memcahceName = "".$config['host'].$config['port'];		
		// read from cache
		if (isset($this->data[$memcahceName]))
			return $this->data[$memcahceName];	

		if(!isset($config['persistent']))
	    {
	    	$config['persistent'] = true;
	    }

		$memcache = new Memcache();
		$memcache->addServer($config['host'], $config['port'] , $config['persistent']);
		$memcache->setCompressThreshold(2000, 0.2);
		
		$this->data[$memcahceName] = $memcache;	
		return $memcache;
	}

}

