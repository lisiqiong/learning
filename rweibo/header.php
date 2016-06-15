<?php
$user = isLogin();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="it">
<head>
<meta content="text/html; charset=UTF-8" http-equiv="content-type">
<title>redis+php实现简单微博</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="page">
<div id="header">
<a href="/"><img style="border:none" src="redis.jpg" width="400" height="86" alt="Retwis"></a>
<div id="navbar">
<a href="index.php">我的主页</a>
| <a href="timeline.php">热门</a>
<?php
if($user){
    $redis = redis_connect();
    $uname = $redis->get("user:userid:".$user['userid'].":username");
    echo "|<a href='logout.php' >退出</a>&nbsp;&nbsp;".$uname;
}else{
    echo "|<a href='index.php' >登录</a>";       
}
?>
</div>
</div>
