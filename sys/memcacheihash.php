<?php
/**
 * 
 * sys_memcache
 * @author zhoupengqian01
 */
class sys_memcacheihash
{
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
     * memcache实例（每个端口一个）
     */
    static public $instances = array();
    
	public function __construct()
	{
    }

    private function getMemcache($uid)
    {
		//使用哪个服务
    	$serverId = $uid % count(sys_define::$hash_memcache);
		
    	if(isset(self::$instances[$serverId]) && is_object(self::$instances[$serverId]))
		{
			return self::$instances[$serverId];	
		}
		
		$conf = sys_define::$hash_memcache[$serverId];
		
		$memcache = sys_memcachePool::getInstance()->getMemcache($m['host'], $m['port']);
		
		
		//$memcache = new Memcache();
		//$memcache->addServer($conf['host'], $conf['port'], false);
		
		self::$instances[$serverId] = $memcache;
    	
		return self::$instances[$serverId];
    }
    
	/**
	 * 在cache中设置键为$key的项的值，如果该项不存在，则新建一个项
	 * @param string $key 键值
	 * @param mix $var 值
	 * @param int $expire 到期秒数
	 * @param int $flag 标志位
	 * @return bool 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function set($uid, $key, $var, $expire = 0, $flag = 0)
    {
		$memcache = $this->getMemcache($uid);
    	
    	$key = $this->prefix . $key;

	    if (sys_define::DEBUG_MODE)
	       self::$querys[] = "set " . $key . ' ' . $var;

	    if (isset(self::$data[$key]))
			self::$data[$key] = '';

		$i = 3;
		while(($res = $memcache->set($key, $var, $flag, $expire)) === false && $i >0)
		{
			$i--;
		}
		if ($res === false)
		{
			// 删除数据，防止脏数据
			$memcache->delete($this->prefix . $key);
		}

		return $res;
	}


	/**
	 * 在cache中获取键为$key的项的值
	 * @param string $key 键值
	 * @return string 如果该项不存在，则返回false
	 * @access public
	 */
    function get($uid, $key)
    {
		$memcache = $this->getMemcache($uid);
    	
    	$key = (empty($this->prefix)) ? $key : $this->prefix . $key;

		if (sys_define::DEBUG_MODE)
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
				$k_data = $memcache->get($k_data);
				if (is_array($k_data) && count($k_data) > 0)
				{
					$v_data = array_merge($v_data, $k_data);			//合并到返回数组
					self::$data = array_merge(self::$data, $k_data);	//合并到缓存数组
				}
			}

			return $v_data;
		}
		else
		{
			if (empty(self::$data[$key]))
				self::$data[$key] = $memcache->get($key);

			return self::$data[$key];
		}
	}


	/**
	 * 在MC中获取为$key的自增ID
	 *
	 * @param string $key	 自增$key键值
	 * @param integer $count 自增量,默认为1
	 * @return 				 成功返回自增后的数值,失败返回false
	 */
	function add($uid, $key, $var, $expire = 0, $flag = 0)
	{
		$memcache = $this->getMemcache($uid);
		$ret = $memcache->add($key, $var, $flag, $expire);
		return $ret;
	}

	function increment($uid, $key, $count = 1)
	{
		$memcache = $this->getMemcache($uid);
		return $memcache->increment($key, $count);
	}
	
	
	/**
	 * 清空cache中所有项
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function flush()
    {
//		return $memcache->flush();
	}


	/**
	 * 删除在cache中键为$key的项的值
	 * @param string $key 键值
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function delete($uid,$key)
    {
    	$memcache = $this->getMemcache($uid);
    	
    	if (isset(self::$data[$key]))
			self::$data[$key] = '';
			
		if (sys_define::DEBUG_MODE)
			self::$querys[] = "delete " . $key;

		$ret = $memcache->delete($this->prefix . $key);
		return $ret;
    }
}
?>