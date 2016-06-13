<?php
include("function.php");
include("header.php");
if(isLogin()==false){
   header("location:index.php");
   exit;
}
$r = redis_connect();
$username = trim($_GET['u']);
$prouid = $r->get("user:username:".$username.":userid");
if(!$prouid){
    header("location:index.php");
    exit;
}
$user = isLogin();
//判断用户判断该用户是否被关注
$isFollow = $r->sismember("following:".$user['userid'],$prouid);
$f_status = $isFollow?'1':'0';
$FollowStr = $f_status?'取消关注':'关注他';

?>
<h2 class="username"><?php echo $username;?></h2>
<a href="follow.php?uid=<?php echo $prouid;?>&f=<?php echo $f_status;?>" class="button"><?php echo $FollowStr;?></a>

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
