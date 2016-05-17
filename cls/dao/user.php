<?php
/**
 * 用户信息
 * @author guoyunfeng
 */
class cls_dao_user extends sys_daoabs 
{
    const TABLE_NAME = 'user';	
    const MEMCACHE_KEY_PREFIX = '_user';

    public function __construct()
    {
        parent::__construct();
        $this->mc = new sys_memcache(sys_define::$common_memcache);
        //$this->redis = sys_redisPool::getInstance()->getRedis(sys_define::$common_redis);
    }

    /**
     * 获得用户信息
     * @param type $iUid
     * @param type $iAppId
     * @return type
     */
    public function getUser($iUid,$iAppId)
    {
        $sMkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$iUid);
        $aRet = $this->mc->get($sMkey);
        if(!empty($aRet) && is_array($aRet))
        {
            return $aRet;
        }
        $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$main_db);
        $aRet = $this->daoHelper->fetchSingle('id=:id AND appid=:appid',['id'=>$iUid,'appid'=>$iAppId]);
        if(!empty($aRet) && is_array($aRet))
        {
            $this->mc->set($sMkey,$aRet);
        }
        else if ($aRet == false)
        {
            sys_log::getLogger()->fatal(sys_log::format('getUser',['id'=>$iUid,'appid'=>$iAppId]));
        }
        return $aRet;
    }

    /**
     * 
     * @param type $sUserName
     * @param type $iAppId
     * @return type
     */
    public function findUserByName($sUserName,$iAppId)
    {
        $sUserName = strtolower($sUserName);
        $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$main_db);
        return $this->daoHelper->fetchSingle('username=:username and appid=:appid',['username'=> $sUserName, 'appid'=>$iAppId]);
    }
    
    /**
     * 创建用户
     * @param type $aUserInfo
     * @return type
     */
    public function addUser($aUserInfo)
    {
        $aFeilds = array_keys($aUserInfo);
        $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$main_db);
        $iInsertId = $this->daoHelper->add($aFeilds, $aUserInfo,null,1);
        return $iInsertId;
    }

    /**
     * 修改用户
     * @param type $aUserInfo
     * @param type $iUid
     * @param type $iAppId
     * @return type
     */
    public function updateUser($aUserInfo,$iUid,$iAppId)
    {
        $feilds = array_keys($aUserInfo);
        $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$main_db);
        $iRet =  $this->daoHelper->update($feilds, $aUserInfo,'id ='.$iUid .' AND appid ='.$iAppId);
        if($iRet === false)
        {
           sys_log::getLogger()->fatal(sys_log::format('updateUser',['id'=>$iUid,'appid'=>$iAppId])); 
           return $iRet;
        }
        
        $sMkey = $this->makeKey(self::MEMCACHE_KEY_PREFIX.$iUid);
        $aRet = $this->daoHelper->fetchSingle('id=:id AND appid=:appid',['id'=>$iUid,'appid'=>$iAppId]);
        if(!empty($aRet) && is_array($aRet))
        {
            $this->mc->set($sMkey,$aRet);
        }
        else if ($aRet == false)
        {
            sys_log::getLogger()->fatal(sys_log::format('updateUser',['id'=>$iUid,'appid'=>$iAppId]));
        }
        
        return $iRet;
    }
}