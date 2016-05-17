<?php
/**
 * 初始化
 * @author guoyunfeng
 */
class cls_service_init extends sys_serviceabs 
{	
    public function __construct()
    {
        parent::__construct();
    }
    
    /*
     * 随机取名(最多只做5次检查)
     */
    public function randomName($user_id=0)
    {		
        $sysDao = $this->daoLocator->getDao("sysrandomname");

        $beforeNameConfig = $sysDao->getAllRandomName ( 1 );
        $afterNameConfig = $sysDao->getAllRandomName ( 2 );
        $beforeNumConfig = count ( $beforeNameConfig );
        $afterNumConfig = count ( $afterNameConfig );

        $userDao = $this->daoLocator->getDao("user");
        for($i = 0;  $i < 5 ; $i++){
                $beforeNum = mt_rand ( 0, $beforeNumConfig - 1 );
                $afterNum = mt_rand ( 0, $afterNumConfig - 1 );
                $userName = $beforeNameConfig [$beforeNum] . $afterNameConfig [$afterNum];
                $ret = $userDao->findUserByName ( $userName,$user_id );
                if(empty($ret))
                {
                        return $userName;
                }
        }
        return $userName;
    }	
}