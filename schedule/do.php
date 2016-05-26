<?php
require_once("Schedule.class.php");
$crontab = new Schedule();
//实例化类调用run公共方法即可
$crontab->run();
?>
