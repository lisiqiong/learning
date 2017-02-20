<?php

$client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
if (!$client->connect('172.168.6.26', 9501, 0.5))
{
        die("connect failed.");
}
//向服务器发送数据
if (!$client->send("hello world"))
{
        die("send failed.");
}
//从服务器接收数据
$data = $client->recv();
if (!$data)
{
        die("recv failed.");
}
echo $data;
//关闭连接
$client->close();





?>
