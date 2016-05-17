<?php
/**
 * php视图类
 * @package cls_view
 * 
 */

class cls_view_php implements sys_viewinterface {
	private $var;


	public function __construct($var)
	{
		$this->var = $var;
	}

	/**
	 * 显示
	 *
	 */
	function display()
	{
		//header("Content-Type:application/json; charset=utf-8");
		echo serialize($this->var);
	}
	
	function fetch(){}
}
