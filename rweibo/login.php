<?php
include("function.php");
include("header.php");
//如果用户已经登录调整到微博列表页面
if(isLogin()!=false){
    header("location:home.php");
    exit;
}
$user = I('username');
$pass = I('password');
if(!$user || !$pass){
    error('数据输入不完整');
}
$r = redis_connect();
$userid = $r->get("user:username:".$user.":userid");
if(!$userid){
    error('用户不存在');
}
$password = $r->get("user:userid:".$userid.":password");
if($password!=$pass){
    error('密码输入错误');
}
/**设置cookie登录成功**/
setcookie('username',$user);
setcookie('userid',$userid);
header("location:home.php");

?>
