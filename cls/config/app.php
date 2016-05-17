<?php

class cls_config_app
{
    /**
     * upush SDK相关配置
     * @var type 
     */
    public static $app = [
                    'yuex'=> [
                        'app_name'=> '约x',
                        'app_key'=> '212f41a62a6840bc6412162d',
                        'master_secret'=> '26a8b35014b729c78fae9202',
                    ],
    ];
    
    public static $aUcenter = [
        'login' => 'http://192.168.150.13/ucenter/web_app/index.php?cmd=loginbase.checklogin',
        //'checklogin'
    ];
}