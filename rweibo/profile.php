<?php
include("header.php");
include("function.php");
if(isLogin()==false){
   header("location:index.php");
   exit;
}
$r = redis_connect();
$username = trim($_GET['u']);
$userid = $r->get("user:username:".$username.":userid");
if(!$userid){
    error('非法登录');
    exit;
}
//判断用户判断该用户是否被关注
$isFollow = $r->sismember("following:".$username,$userid);
$f_status = $isFollow?1:0;
$FollowStr = $isF?'取消关注':'关注他';

?>
<h2 class="username"><?php echo $username;?></h2>
<a href="follow.php?uid=<?php echo $userid;?>&f=<?php echo $f_status;?>" class="button"><?php echo $FollowStr;?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>
<?php
include("bottom.php");
?>
