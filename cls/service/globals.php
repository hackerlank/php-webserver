<?php
/**
 * @author guoyunfeng
 */
class cls_service_globals extends sys_serviceabs 
{	
    public function __construct()
    {
        parent::__construct();
    }	

    public static function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
                return strtolower(str_replace("-","",trim(com_create_guid(), '{}')));
        }	
        return strtolower(sprintf('%04X%04X%04X%04X%04X%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));
    }

    /**
     * 获取每日是否可以刷新
     * $lastFlushTime (时间戳) 上次更新时间
     * $perFlushHour 每日刷新小时
     * $perFlushMin 每日刷新分钟
     * $perFlushSec 每日刷新秒
     */
    public static function isFlushTime($lastFlushTime,$perFlushHour="0",$perFlushMin="0",$perFlushSec="0")
    {
        $today = date("Y-m-d ".$perFlushHour.":".$perFlushMin.":".$perFlushSec,time());
        $flushTime = strtotime($today);

        if($lastFlushTime<$flushTime)//上次刷新时间小于今天刷新时间
        {
                if(time()>=$flushTime || $lastFlushTime<($flushTime-24*3600))
                {
                        return true;
                }
        }
        return false;
    }

    public static function get_param($param, $key)
    {
        if(!isset($param[$key]))
        {
                return null;
        }

        return $param[$key];
    }
    
    public static function getIp()
    {
        $sIp = '';
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
          $sIp = $_SERVER["HTTP_CLIENT_IP"];
        }
        if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $aIp = explode (",", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if($sIp)
            {
                array_unshift($aIp, $sIp); $sIp = '';
            }
            for($i = 0; $i < count($aIp); $i++)
            {
                if (!eregi ("^(10|172\.16|192\.168)\.", $aIp[$i]))
                {
                    $sIp = $aIp[$i];
                    break;
                }
            }
        }
        elseif(!empty($_SERVER["REMOTE_ADDR"]))
        {
          $sIp = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
          $sIp = '';//无法获取
        }
        return $sIp;
    }
    
    public static function generateSalt()
    {
        return substr(md5(time()),0,6);
    }
}