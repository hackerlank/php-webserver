<?php
/**
 * SESSION操作类
 *
 * @package sys
 * 
 * Here is an example:
 * <code>
 * sys_session::start();
 * $s = sys_session::getInstance();
 * $s->a = 1;
 * $s->b = 2;
 * $s->setNamespace('cartoon');
 * $s->cat = 'TOM';
 * $s->mice = 'JERRY';
 * $s->setNamespace();
 * if(isset($s->a)) unset($s->a);
 * echo '<pre>';var_dump($_SESSION);echo '</pre>';
 * sys_session::destroy();
 * </code>
 */
class sys_session {
	private static $session;
	private $data;


	private function __construct()
	{
		$this->data = &$_SESSION;
	}


	/**
	 * 开始SESSION
	 *
	 */
	public static function start()
	{
		if (! session_id()) {
			session_start();
		}
	}


	/**
	 * 单件模式获取SESSION实例
	 *
	 * @return sys_session
	 */
	public static function getInstance()
	{
		if (! self::$session) {
			self::$session = new sys_session();
		}
		return self::$session;
	}


	/**
	 * 设置SESSION命名空间
	 *
	 * @param string|null $namespace 空间名称
	 */
	public function setNamespace($namespace = NULL)
	{
		if (is_null($namespace)) {
			$this->data = &$_SESSION;
		} else {
			$this->data = &$_SESSION [$namespace];
		}
	}


	/**
	 * 获取SESSION值
	 *
	 * @param string $name 键
	 * @return mixed 值
	 */
	public function __get($name)
	{
		return $this->data [$name];
	}


	/**
	 * 设置SESSION值
	 *
	 * @param string $name 键
	 * @param mixed $value 值
	 */
	public function __set($name, $value)
	{
		$this->data [$name] = $value;
	}


	/**
	 * 指定 键 的SESSION是否被设置过
	 *
	 * @param string $name 键
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->data [$name]);
	}


	/**
	 * 删除SESSION
	 *
	 * @param string $name 要删除session的键
	 */
	public function __unset($name)
	{
		unset($this->data [$name]);
	}


	/**
	 * 销毁SESSION
	 *
	 * @return bool
	 */
	public static function destroy()
	{
		return session_destroy();
	}
}
