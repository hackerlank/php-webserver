<?php
/**
 * ctrl基础类
 * @package sys
 * 
 */

abstract class sys_ctrlabs {
	/**
	 * @var sys_service
	 */
	protected $serviceLocator;


	public function __construct()
	{
		$this->serviceLocator = sys_service::getInstance();
	}

	/**
	*	获取当前用户club_id
	*/
	public function getCurrentClubId(){
		return $this->serviceLocator->getCurrentClubId();
	}

	/**
	*	获取当前用户team_id
	*/
	public function getCurrentTeamId(){
		return $this->serviceLocator->getCurrentTeamId();
	}

	/**
	*	设置当前用户club_id
	*/
	public function setCurrentUser($club_id,$team_id){
		return $this->serviceLocator->setCurrentUser($club_id,$team_id);
	}
}