<?php
//phpinfo();
$serv = new swoole_server("172.168.6.26", 9502); 

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {  
            echo "Client: Connect.\n";
            });

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
            $serv->send($fd, "Server接收到的数据为: ".$data);
            });

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
            echo "Client: Close.\n";
            });

//启动服务器
$serv->start(); 



?>
