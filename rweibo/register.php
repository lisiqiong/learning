<?php
include("function.php");
include("header.php");
$username = I('username');
$password = I('password');
$pwd = I('password2');
if(!$username || !$password || !$pwd){
    error('用户名密码不能够为空~');
}
if($password!=$pwd){
    error('两次密码输入不一致哦~');
}

$r = redis_connect();
//判断用户是否注册过
$info = $r->get("user:username:".$username.":userid");
if($info){
    error('该用户已经注册过');
}
//将用户数据存入redis中
$userid = $r->incr('global:userid');
$r->set("user:userid:".$userid.":username",$username);
$r->set("user:userid:".$userid.":password",$password);
$r->set("user:username:".$username.":userid",$userid);
setcookie('userid',$userid);
setcookie('username',$username);
//将最新的注册的50个userid存入到队列中
$r->lpush('newuserlink',$userid);
$r->ltrim('newuserlink',0,49);
header("location:home.php");
?>
