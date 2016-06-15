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
    //我关注好友
    $r->sadd("following:".$user['userid'],$uid);
    //对方就多了一个粉丝
    $r->sadd("followed:".$uid,$user['userid']);
}else{   
    //取消关注,删除关注用户的id，删除关注人的粉丝信息
    $r->srem("following:".$user['userid'],$uid);
    $r->srem("followed:".$uid,$user['userid']);
}
//根据传递过来的userid查找username
$uname = $r->get("user:userid:".$uid.":username");
header("location:profile.php?u=".$uname);
include("bottom.php");

?>
