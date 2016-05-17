<?php
/**
 * 单元测试基础类
 * @package sys
 * 
 */

class tests_base extends PHPUnit_Framework_TestCase {
	/**
	 * @var tests_base
	 */
	protected $login_id = 'ddbrf';
	protected $server_id = 10086;

	protected $serviceLocator;
	protected $session_id;
	protected $club_id;
	
	public function __construct()
	{		
		$this->serviceLocator = sys_service::getInstance();
	}

	protected function setUp()
  {
      $_GET['server_id'] = $this->server_id;
      $loginCtrl = $this->getCtrlObject('cls_ctrl_interface_login', array('host_type' => 'web','login_id'=> $this->login_id));
      $ret = $loginCtrl->login();

      $this->assertEquals($ret['ret'], cls_config_ErrorCode::$code_enum['EN_SUCESS']);
      $this->assertEquals($ret['data']['login_id'], $this->login_id);
      $this->session_id = $ret['session_id'];    

      $clubServer =  $this->serviceLocator->getService ( "club" );
      $this->club_id = $ret['data']['club_id'];    
      
      $_GET['server_id'] = $this->server_id;      
      $_GET['session_id'] = $this->session_id;      
  }
 
  protected function tearDown()
  {
      //print "\t tests_base::tearDown()";
  }

  protected function getCtrlObject($ctrlName,$data = array())
  {
  	$ctrlobj = new $ctrlName();
  	$reflectionClass = new ReflectionClass($ctrlName);
    $reflectionProperty = $reflectionClass->getProperty('data');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($ctrlobj, $data);
    return $ctrlobj;
  }


	public static function setUpBeforeClass()
  {
      //print "\t tests_base::setUpBeforeClass()";
  }

  public static function tearDownAfterClass()
  {
      //print "\t tests_base::tearDownAfterClass()";
  }
}

