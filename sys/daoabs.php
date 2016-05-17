<?php
/**
 * dao基础类
 * @package sys
 * 
 */

abstract class sys_daoabs {
	
	protected $daoHelper;
	protected $mc;
	protected $redis;
	
	protected function __construct()
	{
	}

	protected function makeKey($postfix)
	{
		return sys_define::PROJECT_NAME . $postfix;
	}

}
