<?php
/**
 * 对外开放接口配置
 * 对应队列脚本cron_interface（架设服务时需后台启动）
 */
class cls_config_interface
{
    public static $aInterface = [
        'ucenter' => [
            'login' => [
                'url'  => '',
                'async'=> false,
                'queue' => [
                    'host' => '',
                    'port' => '',
                    'tube' => '',//callback class
                ],
            ],
        ],
    ];
}