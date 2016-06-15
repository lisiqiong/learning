<?php
include("function.php");
include("header.php");
$r = redis_connect();
if(!isLogin()){
    header("location:index.php");
    exit;
}
$user = isLogin();
$newuserlist = array();
$newuserlist = $r->sort('newuserlink',array('sore'=>'desc','get'=>'user:userid:*:username'));
$exists_key = (string)array_search($user['username'],$newuserlist);
unset($newuserlist[$exists_key]);
?>
<h2>热点</h2>
<i>最新注册用户</i><br>
<div>
<?php
    foreach($newuserlist as $k=>$v){
?>
<a class="username" href="profile.php?u=<?php echo $v;?>"><?php echo $v;?></a>
<?php
    }
?>
</div>

<br><i>最新的50条微博!</i><br>
<?php
$postid = $r->get("global:postid");
for($i=$postid;$i>0;$i--){
    $p = $r->hmget("post:postid:".$i,array('time','content','username','userid'));
?>
<div class="post">
<a class="username" href="profile.php?u=<?php echo $p['username'];?>"><?php echo $p['username'];?></a>
<?php echo $p['content'];?><br>
<i><?php echo formattime($p['time']);?>前发布</i>
</div>
<?php
}
include("bottom.php");
?>
