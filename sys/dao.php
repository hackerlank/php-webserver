<?php
/**
 * dao类
 * @package sys
 * 
 */

class sys_dao {
	private $data;
	private static $instance;


	public function sys_dao()
	{
	}


	/**
	 * 取得sys_dao对象的实例
	 *
	 * @return sys_dao
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
	 * 定义魔术函数__call，用以实现getXxxDao()方法的调用，以更直接的方式取得一个Dao实例
	 *
	 * @param 方法名 $name
	 * @param 方法参数 $arguments
	 * @return object
	 */
	public function __call($name, $arguments)
	{
		$posOfGet = strpos($name,'get');
		if ($posOfGet !== 0) {
			throw new cls_exception_support('there is no method name of [' . $name . ']');
		}
		$className = substr($name,3);
		if (substr($className,- 3) != 'Dao') {
			throw new cls_exception_support('there is no method name of [' . $name . ']');
		}
		$className = substr($className,0,strlen($className) - 3);
		return $this->getDao(strtolower($className));
	}


	/**
	 * 创建具体的dao类
	 *
	 * @param string $daoName 要创建的dao类名称
	 * @return object 具体的dao对象
	 */
	public function getDao($daoName)
	{
		// read from cache
		if (isset($this->data [$daoName]))
			return $this->data [$daoName];
			
		// new one object
		$clsname = 'cls_dao_' . $daoName;
		$object = new $clsname();
		$this->data [$daoName] = $object;
		return $object;
	}

}

