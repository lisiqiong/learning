<?php
include("header.php");
include("function.php");
?>
<div id="postform">
<form method="POST" action="post.php">
xxx,有什么新鲜事想告诉大家?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="content"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="发布"></td></tr>
</table>
</form>
<div id="homeinfobox">
0 粉丝<br>
0 关注<br>
</div>
</div>
<div class="post">
<a class="username" href="profile.php?u=test">test</a> hello<br>
<i>11 分钟前 通过 web发布</i>
</div>
<?php
include("bottom.php");
?>
