<?php
header('Content-Type:text/html;charset=utf-8;');

/*
 *@desc 连接redis操作方法
 */
function redis_connect(){
    static $redis = null;
    if($redis!==null){
        return $redis;
    }
    $redis = new Redis();
    $redis->connect('127.0.0.1',6379);
    return $redis;
}

/*
 *@desc 接收数据方法
 **/
function I($post){
    if(empty($post)){
     return false;
    }
    return trim($_POST[$post]);
}


/**
 *@desc 判断是否登录
 ***/
function isLogin(){
    $username = $_COOKIE['username'];
    $userid = $_COOKIE['userid'];
    if(!$username || !$userid){
        return false;
    }
    return array('userid'=>$userid,'username'=>$username);
}

/**
 *@desc error公用方法
 **/
function error($msg){
    echo $msg;
    include("bottom.php");
    exit;
}

function showWeiboList($userid){
    $r = redis_connect();
    $r->ltrim('recivepost:'.$userid,0,49);
    $newposter = $r->sort('recivepost:'.$userid,array('sort'=>'desc'));
    $weiboList = "";
    if($newposter){
        foreach($newposter as $postid){
            $p = $r->hmget("post:postid:".$postid,array('userid','username','time','content'));
            $weiboList .=  '<div class="post"><a class="username" href="profile.php?u='.$p['username'].'">'.$p['username'].'</a>'.$p['content'].'<br><i>'.formattime($p['time']).'前发布</i></div>'; 
        }
    }
    return $weiboList;
}

function formattime($time){
    $restime = time()-$time;
    if($restime>=86400){
        return floor($restime/86400)."天";
    }else if($restime>=3600){
        return floor($restime/3600)."时";
    }else if($restime>=60){
        return floor($restime/60)."分";
    }else{
        return $restime."秒";
    }

}


?>
