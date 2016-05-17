<?php
/**
 * json视图类
 * @package cls_view
 * 
 */

if (!function_exists('json_encode'))
{
	function json_encode($a=false)
	{
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a))
		{
			if (key($a) !== $i)
			{
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList)
		{
			foreach ($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		}
		else
		{
			foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

class cls_view_json implements sys_viewinterface {
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
		header("Content-Type:application/json; charset=utf-8");
		$callback = isset($_GET['callback']) ? $_GET['callback'] : '';
		if (!empty($callback)) {
			echo $callback . '(' . json_encode($this->var) . ')';
		}
		else {
			echo json_encode($this->var);
		}						
	}
	
	function fetch(){}
}