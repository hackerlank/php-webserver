<?php
/***
 */
ini_set('max_execution_time', 0);
ini_set('memory_limit','1000M');
define('GLOBAL_CACHE',false);
define('ROOT_PATH', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
require_once( ROOT_PATH.'/sys/bootstrap.php' );


$params = array(
	'sys_player'=> array('table'=>'sys_player','key'=>'player_id','php_name'=>'sysPlayerList','js_name'=>'sysPlayerList','nojsfeild'=>array('PA','init_CA','player_pro','player_pro_growing',''),'nophpfeild'=>array('player_name' ,'eng_name','kit_name','kit_number','avatar','PA','height','birthday','nationality','desc','worth','weight')),
	'sys_karma'=> array('table'=>'sys_karma','key'=>'karma_id','php_name'=>'karmaConfig','js_name'=>'karmaConfig','nojsfeild'=>array('CA_addition'),'nophpfeild'=>array('karma_desc','karma_desc_data')),
	'sys_tactics'=> array('table'=>'sys_tactics','key'=>'tactics_id','php_name'=>'tacticsListConfig','js_name'=>'sysTactics','nojsfeild'=>array(),'nophpfeild'=>array('tactics_icon','tactics_desc')),
	'sys_formation'=> array('table'=>'sys_formation','key'=>'formation_id','php_name'=>'formation','js_name'=>'sysFormationList','nojsfeild'=>array(),'nophpfeild'=>array('formation_desc','formation_icon','position_point')),
	'sys_drop'=> array('table'=>'sys_drop','key'=>'drop_id','php_name'=>'dropList','js_name'=>'sysDrop','nojsfeild'=>array(),'nophpfeild'=>array()),
	'sys_cmroad_area'=> array('table'=>'sys_cmroad_area','key'=>'area_id','php_name'=>'cmroadArea','js_name'=>'sysCmroadArea','nojsfeild'=>array(),'nophpfeild'=>array('area_name')),
	'sys_cmroad'=> array('table'=>'sys_cmroad','key'=>'cmroad_id','php_name'=>'cmroadList','js_name'=>'sysCmroad','nojsfeild'=>array('area_id','area_number','visit_power','visit_static_drop','visit_random_drop','match_power','match_static_drop','match_random_drop','team_difficulty'),'nophpfeild'=>array('cmroad_name')),
	'sys_player_exp'=> array('table'=>'sys_player_exp','key'=>'level','php_name'=>'player_exp','js_name'=>'config_playerExp','nojsfeild'=>array(),'nophpfeild'=>array()),	
	'sys_wake'=> array('table'=>'sys_wake','key'=>'wake_id','php_name'=>'wakeList','js_name'=>'config_wakeList','nojsfeild'=>array('wake_target','wake_type','target_number','wake_level','trigger_probability','addition_a','addition_d','addition_d','wake_all_id','wake_target_line'),'nophpfeild'=>array('wake_desc','wake_name')),	
	'sys_club_exp'=> array('table'=>'sys_club_exp','key'=>'club_level','php_name'=>'clubLevelConfig','js_name'=>'config_clubLevelConfig','nojsfeild'=>array(),'nophpfeild'=>array()),		
	'sys_skill'=> array('table'=>'sys_skill','key'=>'skill_id','php_name'=>'skillList','js_name'=>'config_skillConfig','nojsfeild'=>array('skill_trade','skill_price','addition_a','addition_a_growing','addition_d','addition_d_growing'),'nophpfeild'=>array('skill_name','skill_icon','skill_desc')),		
	'sys_title'=> array('table'=>'sys_title','key'=>'title_id','php_name'=>'titleList','js_name'=>'config_titleConfig','nojsfeild'=>array('title_a','title_a_growing','title_d','title_d_growing',),'nophpfeild'=>array('title_name','title_desc','title_icon',)),		
	'sys_skill_exp'=> array('table'=>'sys_skill_exp','key'=>'level','php_name'=>'skillLevelConfig','js_name'=>'config_skillLevelConfig','nojsfeild'=>array(),'nophpfeild'=>array()),		
	'sys_title_exp'=> array('table'=>'sys_title_exp','key'=>'level','php_name'=>'titleLevelConfig','js_name'=>'config_titleLevelConfig','nojsfeild'=>array(),'nophpfeild'=>array()),		
	'sys_item'=> array('table'=>'sys_item','key'=>'item_id','php_name'=>'itemConfig','js_name'=>'config_itemConfig','nojsfeild'=>array(),'nophpfeild'=>array('item_name','item_desc','item_icon')),		


	

);


$querys = $_SERVER['argv'][1];
$val = convertUrlQuery($querys);

if(!isset($params[$val['table']])){
	echo 'no config!!!';
	exit();
}

$config = $params[$val['table']];

$daoHelper = new sys_daohelper(null,$config['table'],sys_define::$system_db);
$ret = $daoHelper->fetchAll();

$nojsfeild = $config['nojsfeild'];
$nophpfeild = $config['nophpfeild'];

$feilds_php = array_keys($ret[0]);
foreach($feilds_php as $filkey => $fildd){
	if (in_array($fildd, $nophpfeild)) {
	     unset($feilds_php[$filkey]);
	}
}

$feilds_js = array_keys($ret[0]);
foreach($feilds_js as $filkey => $fildd){
	if (in_array($fildd, $nojsfeild)) {
	     unset($feilds_js[$filkey]);
	}
}

$data = array();
foreach($ret as $v){
	$item = array();
	foreach($feilds_php as $fild){		
		if(is_string($v[$fild])){
			$obj = json_decode($v[$fild],true);
			$json_error = json_last_error();
			//var_dump($json_error);
			if($json_error == JSON_ERROR_NONE){
				$item[$fild] = $obj;
			} else {
				$item[$fild] = $v[$fild];
			}			
		} else {
			$item[$fild] = $v[$fild];
		}
	}
	$data[] = $item;
}

$str = '<?php'."\n";
$str.= 'class ExportConfig'."\n";
$str.= '{'."\n";
$str.= '	public static $'.$config['php_name'].' = array ('."\n";
for ($i=0; $i < count($data); $i++) { 
	 	$item = $data[$i];
		$dddd  = str_replace("\n",'',var_export($item,true));
		$dddd  = str_replace('   ','',$dddd);
		$dddd  = str_replace('  ','',$dddd);
		$dddd  = str_replace(',)',')',$dddd);
		$dddd  = str_replace(', )',')',$dddd);
		$dddd  = str_replace(' => ','=>',$dddd);
		$dddd  = str_replace(' =>','=>',$dddd);
		$dddd  = str_replace("array (",'array(',$dddd);
		$dddd  = str_replace('( ','(',$dddd);
		if(isset($config['key'])){
			$str.= "		".$item[$config['key']] .'=>' . $dddd . ",\n";
		} else {
			$str.= "		".$i .'=>' . $dddd . ",\n";
		}
}
$str.= '	);'."\n";
$str.= '}'."\n";

file_put_contents(ROOT_PATH.'/tools/ExportConfigtamp.php', $str);

unset($data);
$data = array();

foreach($ret as $v){
	$item = array();
	foreach($feilds_js as $fild){		
		if(is_string($v[$fild])){
			$obj = json_decode($v[$fild],true);	
			if(json_last_error() == JSON_ERROR_NONE){
				$item[$fild] = $obj;
			} else {
				$item[$fild] = $v[$fild];
			}			
		} else {
			$item[$fild] = $v[$fild];
		}
	}
	$data[] = $item;
}

$str1 = 'var '.$config['js_name'].' = {'."\n";
for ($i=0; $i < count($data); $i++) { 
	 	$item = $data[$i];
	 	$dddd = json_encode($item,JSON_UNESCAPED_UNICODE);
		if(isset($config['key'])){
			$str1.= '	"'.$item[$config['key']] .'":' . $dddd . ",\n";
		} else {
			$str1.= '	"'.$i .'":' . $dddd . ",\n";
		}
}
$str1.= '};'."\n";


file_put_contents(ROOT_PATH.'/tools/ExportConfigtamp.js', $str1);



function convertUrlQuery($query) {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
        }
        return $params;
}
