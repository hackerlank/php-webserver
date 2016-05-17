<?php
/**
 * log类
 * @package sys
 * 
 */

class sys_log {

	private static $logger;

	public function sys_log()
	{		
	}

	public static function getLogger(){
		if (! self::$logger) {
			sys_bootstrap::loadClass( ROOT_PATH.'/lib/log4php/Logger.php' );
			Logger::configure(ROOT_PATH.'/lib/log4php/log4php.xml');
			self::$logger = Logger::getRootLogger();
		}		
		return self::$logger;
	}
	

	public static function format($tip, $params){
		return $tip . ' params=' . json_encode($params);
	}
}

