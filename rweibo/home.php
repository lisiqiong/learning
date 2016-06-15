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
//获取微博信息列表
//$newposter = showWeiboList($user['userid']);
//计算几个粉丝，几个关注
$myfans = $r->sCard("followed:".$user['userid']);
$mystar = $r->sCard("following:".$user['userid']);

?>
<div id="postform">
<form method="POST" action="post.php">
<?php echo $uname;?>，有什么新鲜事想告诉大家?
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
/*获取最新的50微博信息列表,列出自己发布的微博及我关注用户的微博
*1.根据推送的信息获取postid
*2.根据postid获取发送的信息
*/
$r->ltrim("recivepost:".$user['userid'],0,49);
$postid_arr = $r->sort("recivepost:".$user['userid'],array('sort'=>'desc'));
if($postid_arr){
    foreach($postid_arr as $postid){
        $p = $r->hmget("post:postid:".$postid,array('userid','username','time','content'));
        $weiboList .=  '<div class="post"><a class="username" href="profile.php?u='.$p['username'].'">'.$p['username'].'</a>'.$p['content'].'<br><i>'.formattime($p['time']).'前发布</i></div>'; 
    }
    echo $weiboList;
}else{
    echo '<div class="post" >这个家伙很懒，还未发布消息哦~</div>';
}
include("bottom.php");
?>
