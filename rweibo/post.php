<?php
include("function.php");
$content = I('content');
if(!$content){
    exit('内容不能够为空');
}

$user = isLogin();
if($user==false){
    header("location:index.php");
    exit();
}

$r = redis_connect();
$postid = $r->incr('global:postid');
$r->set("post:postid:".$postid.":time",time());
$r->set("post:postid:".$postid.":userid",$user['userid']);
$r->set("post:postid:".$postid.":content",$content);



?>
