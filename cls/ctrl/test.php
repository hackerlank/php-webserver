<?php
/**
 * test controller
 */
class cls_ctrl_test extends sys_ctrlabs
{

    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        $testServer =  $this->serviceLocator->getService("test");
        $testServer->test();
    }
}
