<?php
/**
 * session
 * @author guoyunfeng
 *
 */
class cls_service_session extends sys_serviceabs 
{
    /**
     * 过期时间24*3600*30
     * @var unknown_type
     */
    const EXPIRE = 2592000;

    public function __construct()
    {
            parent::__construct();
    }	

    /**
     * 设置session
     */
    public function setSession($sessionInfo)
    {
            if(!is_array($sessionInfo))
            {
                    return false;
            }

            if(!isset($sessionInfo['login_id']))
            {
                    return false;
            }
            $login_id = $sessionInfo['login_id'];
            $sessionDao = $this->daoLocator->getDao('session');
            $session_id = null;
            if(!isset($sessionInfo['session_id']))
            {
                    $session_id = $this->createSessionId($login_id);
            }
            else
            {
                    $session_id = $sessionInfo['session_id'];
                    unset($sessionInfo['session_id']);
            }
            $this->destroySession($session_id,$sessionInfo['app_id']);		
            $sessionInfo['ip'] = sys_utils::getClientIP();
            $sessionInfo['login_time'] = CURR_TIME;			
            $ret = $sessionDao->set($session_id,$sessionInfo,self::EXPIRE);
            if(!$ret){		
                    return null;
            }
            return $session_id;
    }


    /**
     * 获取session
     */
    public function getSession($session_id,$app_id)
    {
            $sessionDao = $this->daoLocator->getDao('session');
            return $sessionDao->get($session_id,$app_id);
    }

    /**
     * 删除session
     */
    public function destroySession($session_id,$app_id)
    {		
            $sessionDao = $this->daoLocator->getDao('session');
            $sessionDao->delete($session_id,$app_id);
    }

    /**
     * 生成sessionid算法
     */
    private function createSessionId($login_id)
    {		
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';		
            $value = substr(str_shuffle(str_repeat($pool, 5)), 0, 40);
            return sha1($value.$login_id.time());
    }
}