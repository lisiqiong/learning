<?php
include("function.php");
//如果用户已经登录调整到微博列表页面
if(isLogin()!=false){
    header("location:home.php");
    exit;
}
$username = I('username');
$password = I('password');
if(!$username || !$password){
    exit('数据输入不完整');
}
$r = redis_connect();
$userid = $r->get("user:username:".$username.":userid");
if(!$userid){
    exit('用户不存在');
}
$password = $r->get("user:userid:".$userid."password:".$password);
if(!password){
    exit('密码输入错误');
}
/**设置cookie登录成功**/
setcookie('username',$username);
setcookie('userid',$userid);
header("location:home.php");

?>
