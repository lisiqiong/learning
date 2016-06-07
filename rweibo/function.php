<?php
header('Content-Type:text/html;charset=utf-8;');

/*
 *@desc 连接redis操作方法
 */
function redis_connect(){
    $redis = new Redis();
    $redis->connect('127.0.0.1',6379);
    return $redis;
}

/*
 *@desc 接收数据方法
 **/
function I($post){
    if(empty($post)){
     return false;
    }
    return trim($_POST[$post]);
}

?>
