<?php
include("function.php");
$username = I('username');
$password = I('password');
$pwd = I('password2');
if(!$username || !$password || !$pwd){
    exit('用户名密码不能够为空~');
}
if($password!=$pwd){
    exit('两次密码输入不一致哦~');
}

$r = redis_connect();
//判断用户是否注册过
$info = $r->get("user:username:".$username.":userid");
if($info){
    exit('该用户已经注册过');
}
//将用户数据存入redis中
$userid = $r->incr('global:userid');
$r->set("user:userid:".$userid.":username",$username);
$r->set("user:userid:".$userid.":password",$password);
$r->set("user:username:".$username.":userid",$userid);
header("location:./home.php");
?>
