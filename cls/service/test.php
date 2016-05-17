<?php
/**
 * test service
 */
class cls_service_test extends sys_serviceabs {
    
    public function test()
    {
        $pay = new cls_model_paylog();
        
        $pay->log_id = 1;
        $pay->game_id = 101;
        $pay->save();
        
        $pay->find();
        var_dump($pay);
    }
}
