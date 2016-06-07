#说明：
1.完全采用redis作为数据库实现微博的登录<br/>
2.发布<br/>
3.微博的显示<br/>

#学习使用redis建立合适的数据类型来满足需求<br/>
注册用户表:user<br/>
set global:userid<br/>
set user:userid:1:username zhangshan<br/>
set user:userid:1:password 1212121212<br/>
set user:username:zhangshan:userid 1<br/>

#发微博表：post<br/>
set post:postid:3:time timestamp<br/> 
set post:postid:3:userid 5 <br/>
set post:postid:3:content 测试发布哈哈哈哈<br/>

incr global:postid<br/>
set post:postid:$postidcho "用户名密码不能够为空!";<br/>

