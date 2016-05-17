<?php

define('ROOT_PATH', realpath('..'));

require( ROOT_PATH.'/sys/bootstrap.php' );

sys_control::noCache();

sys_control::performAction(); 
