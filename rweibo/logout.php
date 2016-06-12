<?php
include "function.php";
if(isLogin()!=false){
    setcookie("userid","",time()-3600);
    setcookie("username","",time()-3600);
    header("location:index.php");
    exit;
}

?>
