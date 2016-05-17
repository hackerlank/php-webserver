<?php
/**
 * json类
 * @package sys
 * 
 */

class Object {
	//
}

class sys_json {

	public static function make($func, $params = array())
	{
		$json = array();
		$json[0] = new Object;
		$json[0]->func = $func;
		$json[0]->data = new Object;
		foreach($params as $k=>$v) $json[0]->data->$k = $v;
		
		return $json;
	}

}

