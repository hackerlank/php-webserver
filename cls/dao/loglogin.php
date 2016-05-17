<?php

class cls_dao_loglogin extends sys_daoabs 
{
    const TABLE_NAME = 'login_log';

    public function __construct()
    {
            parent::__construct();
    }

    public function addLogin($value)
    {		
            $feilds = array_keys($value);
            $this->daoHelper = new sys_daohelper(null,self::TABLE_NAME,sys_define::$log_db);
            $insert_id = $this->daoHelper->add($feilds, $value,null,1);
            return $insert_id > 0 ? $insert_id : 0;
    }
}