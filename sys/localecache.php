<?php
/**
 * 
 * sys_localecache
 * @author guoyunfeng
 */
class sys_localecache
{
	protected $mc;
    protected $eAccelerator = false;    

	/**
	 * 构造函数
	 * @param string $host Memcached 服务器的主机名或IP地址或者为服务器组相关信息
	 * @param int $port 端口号
	 * @param int $timeout 超时时间
	 */
	function __construct($host = 'localhost', $port = 11211, $timeout = 60)
	{    	
		$this->eAccelerator = function_exists("eaccelerator_lock");
		if(!$this->eAccelerator)
		{
			$host = is_array($host) ? $host : array(array('host' => $host, 'port' => $port,'persistent' => true));
			$this->mc = new sys_memcache($host);
		}
    }

	/**
	 * 在cache中设置键为$key的项的值，如果该项不存在，则新建一个项
	 * @param string $key 键值
	 * @param mix $var 值
	 * @param int $expire 到期秒数
	 * @return bool 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function set($key, $var, $expire = 300)
    {
		if($this->eAccelerator)	{
			return eaccelerator_put($key, $var, $expire); 
			//return eaccelerator_put($key, serialize($var), $expire); 
		}
		else {
    		return $this->mc->set($key,$var,$expire);
    	}
	}


	/**
	 * 在cache中获取键为$key的项的值
	 * @param string $key 键值
	 * @return string 如果该项不存在，则返回false
	 * @access public
	 */
    function get($key)
    {
    	if($this->eAccelerator)	{
			$data = eaccelerator_get($key);
			if($data)	{
				return $data;
				//return @unserialize($data);
			}			
			return null;
		}
		else {
			return $this->mc->get($key);
    	}
	}

	
	/**
	 * 清空cache中所有项
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function flush()
    {
    	if($this->eAccelerator)	{
			@eaccelerator_clean();  
            @eaccelerator_clear(); 
            return true;
		}
		else {

			return $this->mc->flush();
    	}
	}


	/**
	 * 删除在cache中键为$key的项的值
	 * @param string $key 键值
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function delete($key)
    {
    	if($this->eAccelerator)	{
			return eaccelerator_rm($key);
		}
		else {
			return $this->mc->delete($key);
    	}
    }
}
?>