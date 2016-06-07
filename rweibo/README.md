1.完全采用redis作为数据库实现微博的登录
2.发布
3.微博的显示

学习使用redis建立合适的数据类型来满足需求
注册用户表:user
set global:userid
set user:userid:1:username zhangshan
set user:userid:1:password 1212121212
set user:username:zhangshan:userid 1

发微博表：post
set post:postid:3:time timestamp 
set post:postid:3:userid 5 
set post:postid:3:content 测试发布哈哈哈哈

incr global:postid
set post:postid:$postidcho "用户名密码不能够为空!";

