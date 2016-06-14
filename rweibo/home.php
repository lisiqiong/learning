<?php
include("function.php");
include("header.php");
//如果没有登录调到登录页
$user = isLogin();
if($user==false){
    header("location:index.php");
    exit;
}
$r = redis_connect();
//取出自己发的和粉主推过来的信息
$r->ltrim('recivepost:'.$user['userid'],0,49);
$newposter = $r->sort('recivepost:'.$user['userid'],array('sort'=>'desc','get'=>'post:postid:*:content'));

//计算几个粉丝，几个关注
$myfans = $r->sCard("followed:".$user['userid']);
$mystar = $r->sCard("following:".$user['userid']);

?>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $uname;?>,有什么新鲜事想告诉大家?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="content"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="发布"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?php echo $myfans;?> 粉丝<br>
<?php echo $mystar;?> 关注<br>
</div>
</div>
<?php 
    foreach($newposter as $v){
?>
<div class="post">
<a class="username" href="profile.php?u=test">test</a><?php echo $v;?><br>
<i>11 分钟前 通过 web发布</i>
</div>
<?php
}
include("bottom.php");
?>
