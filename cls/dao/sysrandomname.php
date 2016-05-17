<?php
/**
 * 系统随机名字
 * @author guoyunfeng
 */
class cls_dao_sysrandomname extends sys_daoabs 
{
    const TABLE_NAME = 'sys_random_name';
    const MEMCACHE_KEY_PREFIX = '_sysrandom';

    public function __construct()
    {
            parent::__construct();
            $this->mc = new sys_memcache(sys_define::$common_memcache);
    }

    /*
     * 获得所有系统随机的名字
     * 1为前缀，2为后缀
     */
    public function getAllRandomName($type)
    {
            $mkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$type);
            $ret = $this->mc->get($mkey);
            if(is_array($ret)) return $ret;

            $row = array();
            $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$system_db);
            $ret = $this->daoHelper->fetchAll('type=:type',array('type'=>$type));
            if(is_array($ret))
            {
                    foreach($ret as $key=>$value)
                    {
                            $row[$key] = $value['name'];
                    }
                    $this->mc->set($mkey,$row);
            }
            return $row;
    }
}