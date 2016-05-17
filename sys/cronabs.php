<?php
/**
 * cron基础类
 * @package sys
 * 
 */

class sys_cronabs {
	/**
	 * @var sys_service
	 */
	protected $serviceLocator;
	
	public function __construct()
	{		
		$this->serviceLocator = sys_service::getInstance();
	}
	
	public function lockPort($port)
	{
		if (!$port) return true;

		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$ret = socket_bind($sock, "127.0.0.1", $port);
		if (!$ret) {
			return false; 
		}
		socket_listen($sock);
		return true;
	}
	
	public function unlockPort($port)
	{
		return true;
	}
	
	public function Lock($key){
		$lockDao = sys_dao::getInstance()->getDao("lock");
		return $lockDao->Lock(0,$key,60);	//最大锁60秒
	}

	public function unlock($key)
	{
		$lockDao = sys_dao::getInstance()->getDao("lock");
		return $lockDao->unLock(0,$key);
	}

	public function isLock($key)
	{
		$lockDao = sys_dao::getInstance()->getDao("lock");
		return $lockDao->isLock(0,$key);
	}
}

