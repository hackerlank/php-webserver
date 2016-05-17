<?php
/**
 * 用户
 * @author guoyunfeng
 */
class cls_service_user extends sys_serviceabs 
{   
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 获取用户
     * @param type $iUid
     * @param type $iAppId
     * @return type
     */
    public function getUser($iUid,$iAppId)
    {
        $oUserDao = $this->daoLocator->getDao("user");
        $aRet = $oUserDao->getUser($iUid,$iAppId);
        
        return $aRet? $aRet :cls_config_ErrorCode::$code_enum['EN_FAILURE'];
    }   
    
    /**
     * 更新用户信息
     * @param type $aData
     * @param type $iUid
     * @param type $iAppId
     * @return type
     */
    public function updateUser($aData,$iUid,$iAppId)
    {
        $oUserDao = $this->daoLocator->getDao("user");
        if(empty($iUid) || empty($aData))
        {
            return cls_config_ErrorCode::$code_enum['EN_FAILURE'];
        }
        if(isset($aData['password']))
        {
            $aData['salt'] = cls_service_globals::generateSalt();
            $aData['password'] = $this->mcryptPassword($aData['password'],$aData['salt']);
        }
        $iRet = $oUserDao->updateUser($aData,$iUid,$iAppId);
        
        return $iRet!==false ? $iRet :cls_config_ErrorCode::$code_enum['EN_FAILURE'];
    }

    /**
     * 添加用户
     * @param type $aUserInfo
     * @return type
     */
    public function addUser($aUserInfo)
    {
        $oUserDao = $this->daoLocator->getDao("user");
        
        $aUserInfo['reg_time'] = time();
        $aUserInfo['reg_ip'] = sys_utils::getClientIP();
        $aUserInfo['salt'] = cls_service_globals::generateSalt();
        $aUserInfo['password'] = $this->mcryptPassword($aUserInfo['password'],$aUserInfo['salt']);
        
        $iRet = $oUserDao->addUser($aUserInfo);
        return $iRet? $iRet :cls_config_ErrorCode::$code_enum['EN_FAILURE'];
    }
    
    /**
     * 添加临时用户
     * @param type $aUserInfo
     * @return type
     */
    public function addTempUser($aUserInfo=NULL)
    {
        $aUserInfo['id'] = 0;
        $aUserInfo['reg_time'] = time();
        $aUserInfo['reg_ip'] = sys_utils::getClientIP();
        $aUserInfo['username'] = '@anonymous';
        $aUserInfo['token'] = $this->generateToken($aUserInfo['username'],$aUserInfo['reg_time']);
        //login memcache
        $this->setLoginUser($aUserInfo);
        return $aUserInfo;
    }
    
    /**
     * login
     * @param type $sUsername
     * @param type $sPassword
     * @param type $iAppId
     * @return type
     */
    public function login($sUsername,$sPassword,$iAppId)
    {
        $oUserDao = $this->daoLocator->getDao("user");
        if(empty($sUsername) ||empty($sPassword) || empty($iAppId))
        {
            return cls_config_ErrorCode::$code_enum['EN_FAILURE'];
        }
        if(!!($aUserInfo = $oUserDao->findUserByName($sUsername,$iAppId)))
        {
            if($aUserInfo['password'] == $this->mcryptPassword($sPassword, $aUserInfo['salt']))
            {
                $aUserInfo['token'] = $this->generateToken($aUserInfo['id'],$aUserInfo['salt']);
                
                //login memcache
                //$this->addMemcache($aUserInfo['token'], $aUserInfo);
                $this->setLoginUser($aUserInfo);
                //每次登陆，更新salt
                $this->updateUser(['password'=>$sPassword], $aUserInfo['id'], $iAppId);
                return $aUserInfo;
            }
            else
            {
                return cls_config_ErrorCode::$code_enum['EN_FAILURE'];
            }
        }
        return cls_config_ErrorCode::$code_enum['EN_FAILURE'];
    }
    
    /**
     * 
     * @param type $sToken
     * @param type $iAppId
     * @return type
     */
    public function checkLogin($sToken,$iAppId=0)
    {
        $this->mc = new sys_memcache(sys_define::$login_memcache);
        return $this->mc->get($sToken);
    }
    
    /**
     * find user
     * @param type $sUserName
     * @param type $iAppId
     * @return type
     */
    public function findUserByName($sUserName,$iAppId)
    {
        $userDao = $this->daoLocator->getDao("user");
        return $userDao->findUserByName($sUserName,$iAppId);
    }
    
    /**
     * 设置登陆状态
     * @param type $aUserInfo
     * @return type
     */
    public function setLoginUser($aUserInfo=NULL)
    {
        if(!empty($aUserInfo['token']))
        {
            $this->addMemcache($aUserInfo['token'], $aUserInfo);
        }
        return $aUserInfo;
    }
    
    /**
     * 
     * @param type $sKey
     * @param type $xData
     * @return type
     */
    public function addMemcache($sKey,$xData)
    {
        //login memcache
        $this->mc = new sys_memcache(sys_define::$login_memcache);
        return $this->mc->set($sKey,$xData);
    }
    
    /*
    public function userLoginLog($value)
    {
        $logDao = $this->daoLocator->getDao("loglogin");
        $logDao->addLogin($value);
    }
    */
    
    protected function mcryptPassword($sPassword,$sSalt)
    {
        if(!empty($sSalt))
        {
            return md5($sPassword.$sSalt);
        }
        return false;
    }
    
    protected function generateToken($iUid,$sSalt)
    {
        return md5($iUid.$sSalt);
    }
}