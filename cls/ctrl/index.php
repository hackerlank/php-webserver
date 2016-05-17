<?php
/**
 * @author guoyunfeng
 */
class cls_ctrl_index extends sys_ctrlabs
{

    public function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
        return new cls_view_html('index.main.html', array());
    }
}