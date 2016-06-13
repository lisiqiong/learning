<?php
include("function.php");
include("header.php");
if(isLogin()==false){
    header("location:index.php");
    exit;
}
$user = isLogin();
$uid = trim($_GET['uid']);
$f = trim($_GET['f']);
$r = redis_connect();
if($f==0){
    //将关注与被关注的数据结构存入redis
    $r->sadd("following:".$user['userid'],$uid);
    $r->sadd("followed:".$uid,$user['userid']);
}else{   
    //取消关注
    $r->srem("following:".$user['userid'],$uid);
    $r->srem("followed:".$uid,$user['userid']);
}
//根据传递过来的userid查找username
$uname = $r->get("user:userid:".$uid.":username");
header("location:profile.php?u=".$uname);
include("bottom.php");

?>
