<?php
header('Content-Type:text/html;charset=utf-8;');
require_once('redis.class.php');
$redis = new MyRedis();
//$res = $redis->get('test1');
//echo "键值为test1的值为：".$res;
//$redis->set('test2','test two  hhh');
//$redis->set('test3');
print_r($redis->get('test2'));
?>
