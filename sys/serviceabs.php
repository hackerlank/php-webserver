<?php
/**
 * service基础类
 * @package sys
 * 
 */

abstract class sys_serviceabs {
    /**
     * @var sys_dao
     */
    protected $daoLocator;
    protected $serviceLocator;
    protected $errors;

    public function __construct()
    {
        $this->daoLocator = sys_dao::getInstance();
        $this->serviceLocator = sys_service::getInstance();
    }


    /**
    *	获取当前用户club_id
    */
    public function getCurrentClubId(){
        return sys_service::getCurrentClubId();;
    }

    /**
    *	获取当前用户team_id
    */
    public function getCurrentTeamId(){
        return sys_service::getCurrentTeamId();
    }

    /**
    *	设置当前用户club_id
    */
    public function setCurrentUser($club_id,$team_id){
        return sys_service::setCurrentUser($club_id,$team_id);
    }

}
