<?php
include("function.php");
include("header.php");
if(isLogin()!=false){
    header("location:home.php");
}


?>
<div id="welcomebox">
<div id="registerbox">
<h2>注册!</h2>
<b>想试试Retwis? 请注册账号!</b>
<form method="POST" action="register.php">
<table>
<tr>
  <td>用户名</td><td><input type="text" name="username"></td>
</tr>
<tr>
  <td>密码</td><td><input type="password" name="password"></td>
</tr>
<tr>
  <td>密码(again)</td><td><input type="password" name="password2"></td>
</tr>
<tr>
<td colspan="2" align="right"><input type="submit" name="doit" value="注册"></td></tr>
</table>
</form>
<h2>已经注册了? 请直接登陆</h2>
<form method="POST" action="login.php">
<table><tr>
  <td>用户名</td><td><input type="text" name="username"></td>
  </tr><tr>
  <td>密码:</td><td><input type="password" name="password"></td>
  </tr><tr>
  <td colspan="2" align="right"><input type="submit" name="doit" value="Login"></td>
</tr></table>
</form>
</div>
<ul>
<li>Redis 是一种key-value 数据库, 而且是本项目中 <b>唯一</b>使用的数据库, 没有用mysql等.</li>
<li>应用程序可以通过一致性哈希轻易的部署多台服务器</li>
<li>我的github:<a href="https://github.com/lisiqiong" target="_blank"  >https://github.com/lisiqiong</a></li>
<li>新浪博客:<a href="http://blog.sina.com.cn/lisiqiong2012" target="_blank" >http://blog.sina.com.cn/lisiqiong2012</a></li>
<li>新浪微博:lisiqiong2012</li>
</ul>
</div>
<?php
include("bottom.php");
?>
