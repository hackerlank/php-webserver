<?php
/*
 * 错误码
 * @auther guoyunfeng
 */
class cls_config_ErrorCode
{
	public static $code_enum = array(

 		/* 1 - 200 通用返回码 */		
		'EN_SUCESS'			=>1, 1=>"成功",
		'EN_FAILURE'			=>2, 2=>"失败",
            
		'EN_NOT_GOLD'			=>3, 3=>"金币不够",
		'EN_NOT_CD'			=>4, 4=>"CD时间没到",
		'EN_CD_NUMBER'			=>5, 5=>"CD搜索次数上限",
		'EN_NOT_ENOUGH_VIP_LEVEL'       =>6, 6=>"VIP等级不够",
		'EN_LOCK'			=>7, 7=>"业务并发",
		'EN_CMD_FAILURE'		=>8, 8=>"没有找到对应的指令",
		'EN_MATCH_ERROR'		=>9, 9=>'比赛引擎错误',
		'EN_CMD_CLASSFAILURE'           =>10, 10=>"没有找到对应的指令的处理类",
		'EN_NOT_LADDER' 		=>11, 11=>"天梯赛积分不足",
                'EN_NOT_ENGOUGH_PVP'            =>98, 98=>'PVP行动点不够',
		'EN_SYSTEM_ERROR'               =>99, 99=>"系统错误",
            
		/* 10 - 19 扣费返回码 */		
		'EN_PAY_NOBUY'			=>11, 11=>"扣费业务不能购买",
		'EN_PAY_NODATA'			=>12, 12=>"扣费业务数据有问题",
		'EN_PAY_BUSINESS'		=>13, 13=>"扣费业务错误",
            
		/* 20 - 29 比赛返回码 */		
		'EN_MATCH_NOCHALLENGE'		=>20, 20=>"比赛不能挑战",
		'EN_MATCH_NO_CLUB_LEVEL'	=>21, 21=>"俱乐部等级不够",
            
		/* 30 - 39 概率返回码 */
		'EN_BAD_LUCK'                   =>30, 30=>"不幸运",
		
 		/* 201-300 注册和新手引导 */
		'EN_REGISTER_NAME_EXIST'   =>201, 201=>"用户名已存在",
		'EN_REGISTER_NAME_ERROR'   =>202, 202=>"用户名不合法",
		
                'EN_PARAM'			=> '-1','-1'=> "参数不合法",
                
	);
}