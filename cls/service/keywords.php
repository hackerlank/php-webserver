<?php
/**
 * 关键字
 * @author guoyunfeng
 */
class cls_service_keywords extends sys_serviceabs 
{	
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return true是关键字  false不是
     * @param unknown_type $keyword
     */
    public function isContainKeywords($keyword)
    {
        $kyes = cls_config_keywords::$keys;
        foreach ($kyes as $v)
        {
            if(strpos($keyword, $v)!==false)
            {
                return true;
            }
        }
        return false;
    }		
}