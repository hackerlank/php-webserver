<?php
/**
 * service类
 * @package sys
 * 
 */

class sys_service {

    private $data;
    private static $instance;

    public $club_id;
    public $team_id;	

    public function sys_service()
    {
        
    }

    /**
     * 取得sys_service对象的实例
     *
     * @return sys_service
     */
    public static function getInstance()
    {
        if (! self::$instance) {
            $className = __CLASS__;
            self::$instance = new $className();
        }

        return self::$instance;
    }

    /**
     * 定义魔术函数__call，用以实现getXxxService()方法的调用，以更直接的方式取得一个Service实例
     *
     * @param 方法名 $name
     * @param 方法参数 $arguments
     * @return object
     */
    public function __call($name, $arguments)
    {
        $posOfGet = strpos($name,'get');
        if ($posOfGet !== 0) {
                throw new cls_exception_support('there is no method name of [' . $name . ']');
        }
        $className = substr($name,3);
        if (substr($className,- 7) != 'Service') {
                throw new cls_exception_support('there is no method name of [' . $name . ']');
        }
        $className = substr($className,0,strlen($className) - 7);
        return $this->getService(strtolower($className));
    }

    /**
     * 创建具体的service类
     *
     * @param string $serviceName 要创建的service类名称
     * @return object 具体的service对象
     */
    public function getService($serviceName)
    {
        // read from cache
        if (isset($this->data[$serviceName]))
            return $this->data[$serviceName];

        // new one object
        $clsname = 'cls_service_' . $serviceName;
        $object = new $clsname();
        $this->data[$serviceName] = $object;
        return $object;
    }

    /**
    * 获取当前用户club_id
    */
    public static function getCurrentClubId(){
        $service = self::getInstance();
        return $service->club_id;
    }

    /**
    * 获取当前用户team_id
    */
    public static function getCurrentTeamId(){
        $service = self::getInstance();
        return $service->team_id;
    }

    /**
    * 设置当前用户club_id
    */
    public static function setCurrentUser($club_id,$team_id){
        $service = self::getInstance();
        $service->club_id = $club_id;
        $service->team_id = $team_id;
    }

}

