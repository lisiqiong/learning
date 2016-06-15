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
<!--关注与取消关注-->
<h2 class="username"><?php echo $username;?></h2>
<a href="follow.php?uid=<?php echo $prouid;?>&f=<?php echo $f_status;?>" class="button"><?php echo $FollowStr;?></a>

<?php
/*获取最新的50微博信息列表,列出自己发布的微博及我关注用户的微博
*1.根据推送的信息获取postid
*2.根据postid获取发送的信息
*/
$r->ltrim("userpostid:".$prouid,0,49);
$postid_arr = $r->sort("userpostid:".$prouid,array('sort'=>'desc'));
if($postid_arr){
    foreach($postid_arr as $postid){
        $p = $r->hmget("post:postid:".$postid,array('userid','username','time','content'));
        $weiboList .=  '<div class="post"><a class="username" href="profile.php?u='.$p['username'].'">'.$p['username'].'</a>'.$p['content'].'<br><i>'.formattime($p['time']).'前发布</i></div>'; 
    }
    echo $weiboList;
}else{
    echo '<div class="post" >这个家伙很懒，还未发布消息哦~</div>';
}

?>
<?php
include("bottom.php");
?>
