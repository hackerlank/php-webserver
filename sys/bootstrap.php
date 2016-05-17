<?php
/**
 * 加载类
 * @package sys
 * 
 */
class sys_bootstrap {	
    protected static $loadPHP = array();
    /**
     * Constructor
     *
     */
    protected function __construct()
    {
        
    }
    
    public static function registerAutoload($func = 'sys_bootstrap::loadFrameClass', $enable = true)
    {
        $enable ? spl_autoload_register($func) : spl_autoload_unregister($func);
    }
    
    
     /**
     * Load class
     *
     * @param string $className
     * @param string $dir
     * @return boolean
     */
    public static function loadFrameClass($className, $dir = '')
    {      
        if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }

    	$fname = ROOT_PATH.'/'.str_replace('_', '/', $className).'.php';    
    	self::loadClass($fname);

        return class_exists($className, false) || interface_exists($className, false);
    }
    
    
    public static function loadClass($className, $dir = '')
    {
    	if (isset(self::$loadPHP[$className])) {
        	return true;
        }

      	if (is_file($className)){
         	include $className;
         	self::$loadPHP[$className] = 1;
         	return true;
      	}
      	return false;
    }
}


sys_bootstrap::loadClass( ROOT_PATH.'/include/config.php' );

sys_bootstrap::registerAutoload();

set_exception_handler('sys_control::exceptionHandler');
set_error_handler('sys_control::error_handler',E_ALL );
error_reporting(E_ALL);
if (substr(php_sapi_name(), 0, 3) === 'cli' || sys_define::DEBUG_MODE) {
    ini_set("display_errors",'On');
} else {
    ini_set("display_errors",'Off');
}
ini_set('error_log',ROOT_PATH.'/log/error_log.log');

//set_error_handler('sys_control::error_handler');

function dump($v) {
    echo '<pre>';var_dump($v);echo '<pre>';
}



ini_set('date.timezone', 'Asia/Shanghai');

