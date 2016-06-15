<?php
include("function.php");
include("header.php");
$content = I('content');
if(!$content){
    error('内容不能够为空');
}

$user = isLogin();
if($user==false){
    header("location:index.php");
    exit();
}

$r = redis_connect();
$postid = $r->incr('global:postid');
//$r->set("post:postid:".$postid.":time",time());
//$r->set("post:postid:".$postid.":userid",$user['userid']);
//$r->set("post:postid:".$postid.":content",$content);

$r->hmset("post:postid:".$postid,array('userid'=>$user['userid'],'username'=>$user['username'],'time'=>time(),'content'=>$content));

//把微博推给自己的粉丝
$fans = $r->smembers("followed:".$user['userid']);
$fans[] = $user['userid'];
foreach($fans as $fansid){
    $r->lpush('recivepost:'.$fansid,$postid);
}
//单独累计个人发布的信息
$r->lpush('userpostid:'.$user['userid'],$postid);
header("location:home.php");
exit;
include("bottom.php");
?>
