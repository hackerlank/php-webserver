<?php
/**
 * 
 * sys_memcache
 * @author zhoupengqian01
 */
class sys_memcache
{
	/**
	 * @var Memcache $memcache Memcached 缓存连接对象
	 * @access public
	 */
	var $memcache = NULL;

	/**
	 * @var string $prefix 变量前缀
	 */
	var $prefix = '';


    /**
     * 数据查询的统计
     *
     * @var Array
     */
    static public $querys = array();


    /**
     * 数据缓存沲
     *
     * @var Array
     */
    static public $data = array();

	/**
	 * 构造函数
	 * @param string $host Memcached 服务器的主机名或IP地址或者为服务器组相关信息
	 * @param int $port 端口号
	 * @param int $timeout 超时时间
	 */
	function __construct($host = 'localhost', $port = 11211, $timeout = 60)
	{    	
    	$host = is_array($host) ? $host : array(array('host' => $host, 'port' => $port,'persistent' => true));

    	

    	//如果是服务器分组则添加所有的服务器分组
    	$connect = "";
    	foreach ($host as $m)
    	{
    		$connect .= $m['host'] . ":" .$m['port'] . "\t"; 
    		$this->memcache = sys_memcachePool::getInstance()->getMemcache($m);    		
    	}
    	    	
    	if (GLOBAL_CACHE && sys_define::DEBUG_MODE)
	       self::$querys[] = "connect " . $connect;
    }


	/**
	 * 析构函数
	 */
	//function __destruct()
	//{
    //	$this->memcache->close();
  	//}

	/**
	 * 在cache中设置键为$key的项的值，如果该项不存在，则新建一个项
	 * @param string $key 键值
	 * @param mix $var 值
	 * @param int $expire 到期秒数
	 * @param int $flag 标志位
	 * @return bool 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function set($key, $var, $expire = 0, $flag = 0,$serializeType = null)
    {
		$key = $this->prefix . $key;

	    if (GLOBAL_CACHE && sys_define::DEBUG_MODE)
	       self::$querys[] = "set " . $key;

	    if (GLOBAL_CACHE && isset(self::$data[$key]))
			self::$data[$key] = '';

		$value = sys_serialization::serialize($var,$serializeType);

		$i = 3;
		while(($res = $this->memcache->set($key, $value, $flag, $expire)) === false && $i >0)
		{
			$i--;
		}
		if ($res === false)
		{
			// 删除数据，防止脏数据
			$this->memcache->delete($this->prefix . $key);
		}

		return $res;
	}


	/**
	 * 在cache中获取键为$key的项的值
	 * @param string $key 键值
	 * @return string 如果该项不存在，则返回false
	 * @access public
	 */
    function get($key,$serializeType = null)
    {
		//查看数组大小   	 	
    	$key = (empty($this->prefix)) ? $key : $this->prefix . $key;

		if (GLOBAL_CACHE && sys_define::DEBUG_MODE)
			self::$querys[] = "get " . (is_array($key) ? implode(',', $key) : $key);

		if (is_array($key))
		{
			$v_data = $k_data = array();
			foreach ($key as $v)
			{
				if (!empty(self::$data[$v]))
					$v_data[$v] = self::$data[$v];
				else
					$k_data[] = $v;
			}

			if (count($k_data) > 0)
			{
				$k_data = $this->memcache->get($k_data);
				if (is_array($k_data) && count($k_data) > 0)
				{
					foreach ($k_data as $key => $value) {
						 $k_data[$key] = sys_serialization::unserialize($value,$serializeType);
					}
					$v_data = array_merge($v_data, $k_data);			//合并到返回数组
					if(GLOBAL_CACHE) self::$data = array_merge(self::$data, $k_data);	//合并到缓存数组
				}
			}

			return $v_data;
		}
		else
		{
			if (empty(self::$data[$key])){
				$ret = $this->memcache->get($key);
				$ret = sys_serialization::unserialize($ret,$serializeType);
				if(GLOBAL_CACHE){
					 self::$data[$key] = $ret;
				}
				return $ret;
			}

			return self::$data[$key];
		}
	}


	function add($key, $var, $expire = 0, $flag = 0,$serializeType = null)
	{
		$value = sys_serialization::serialize($var,$serializeType);		
		$ret = $this->memcache->add($key, $value , $flag, $expire);
		return $ret;
	}


	/**
	 * 在MC中获取为$key的自增ID
	 *
	 * @param string $key	 自增$key键值
	 * @param integer $count 自增量,默认为1
	 * @return 				 成功返回自增后的数值,失败返回false
	 */
	function increment($key, $count = 1)
	{
		return $this->memcache->increment($key, $count);
	}
	
	
	/**
	 * 清空cache中所有项
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function flush()
    {
		return $this->memcache->flush();
	}


	/**
	 * 删除在cache中键为$key的项的值
	 * @param string $key 键值
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function delete($key)
    {
    	if (isset(self::$data[$key]))
			self::$data[$key] = '';
			
		if (GLOBAL_CACHE && sys_define::DEBUG_MODE)
			self::$querys[] = "delete " . $key;

		$ret = $this->memcache->delete($this->prefix . $key,0);
		return $ret;
    }
}
?>