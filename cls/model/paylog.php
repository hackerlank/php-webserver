<?php
/**
 * test model
 */
class cls_model_paylog extends sys_armodel
{
    public function getMapping()
    {
        return array(
                'label'=>array(
                        'log_id'=>'log_id',
                        'game_id'=>'game_id',
                ),
                'columns'=>array(
                        'log_id'=>'int(10)',
                        'game_id'=>'smallint(5)',
                ),
                'pk'=>'`log_id`',
                //'partitionKey'=>"log_id|substr,'###',0,10",
                'extra'=>'ENGINE=InnoDB DEFAULT CHARSET=utf8',
        );
    }

    protected function getTableName()
    {
        return "paylog";
    }
    
    protected function getDbName()
    {
        return "fm_main_db";
    }
}