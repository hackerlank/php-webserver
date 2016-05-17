<?php
/***
 */
ini_set('max_execution_time', 0);
ini_set('memory_limit','1000M');
define('GLOBAL_CACHE',false);
define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
require_once( ROOT_PATH.'/sys/bootstrap.php' );


$params = array(
	'ErrorCode'=> array('file'=>'/web_app/fm2014/src/config/ErrorCode.js','class'=>'cls_config_ErrorCode','Property'=>'code_enum','key_type'=>'int','nojsfeild'=>array() ),			
);

foreach ($params as $name => $config) {
	$class = new ReflectionClass($config['class']);
	$ret = $class->getStaticPropertyValue($config['Property']);

  $nojsfeild = $config['nojsfeild'];
  foreach ($ret as $key => $v) {
		if(!checkKEY($config,$key)){
			unset($ret[$key]);
		}  	
		if(isset($ret[$key]) && is_array($ret[$key])){
			foreach($ret[$key] as $fild =>$fildV){		
				if (in_array($fild, $nojsfeild)) {
				  unset($ret[$key][$fild]);
				}
			}
		}	
	}
  $data = $ret;
	$str1 = 'var '.$name.' = ';		
	$str1.= json_encode($data,JSON_UNESCAPED_UNICODE);
	$str1.= ';'."\n";

	file_put_contents(ROOT_PATH.$config['file'], $str1);
}
exit();

function convertUrlQuery($query) {
  $queryParts = explode('&', $query);
  $params = array();
  foreach ($queryParts as $param) {
          $item = explode('=', $param);
          $params[$item[0]] = $item[1];
  }
  return $params;
}


function checkKEY($config,$key) {
  if($config['key_type'] == 'int' && !is_int($key)){  
  	return false;  	
  }
  return true;
}
