<?php
/**
 * 调用外部服务的接口配置
 * 对应队列脚本为cron_request（冗余，一般由服务方的cron_interface启动运行队列脚本）
 */
class cls_config_request
{
    public static $aRequest = [
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