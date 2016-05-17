<?php
/**
 * session 生成
 * @author guoyunfeng
 */
class cls_dao_session extends sys_daoabs 
{
    const MEMCACHE_KEY_PREFIX = 'session_';

    public function __construct()
    {
            parent::__construct();
            $this->mc = new sys_memcache(sys_define::$login_memcache);
    }

    public function get($key,$app_id)
    {
            $mkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$key.$app_id);
            //echo 'mkey: '.$mkey;
            return $this->mc->get($mkey,'json');
    }

    public function set($key,$val,$expire)
    {
            $mkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$key.$val['app_id']);
            return $this->mc->set($mkey,$val,$expire,0,'json');
    }

    public function delete($key,$app_id)
    {
            $mkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$key.$app_id);
            return $this->mc->delete($mkey);
    }
}