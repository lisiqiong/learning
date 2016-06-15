#说明：
1.完全采用redis作为数据库实现微博的登录<br/>
2.发布<br/>
3.微博的显示<br/>

#学习使用redis建立合适的数据类型来满足需求<br/>
#一、用户信息<br/>
将数据以string类型存储<br/>
incr global:userid  （存储用户自增id）<br/>
set user:userid:1:username zhangshan<br/>
set user:userid:1:password 1212121212<br/>
set user:username:zhangshan:userid 1<br/>

#二、关注与粉丝<br/>
将关注他人与自己粉丝数据以set集合类型存储<br/>
sadd followed:1  2        (将用户id2存入成id的粉丝)<br/>
sadd following:1  3      （用户id1关注用户id3）<br/>

#三、微博发布<br/>
将微博发布分为<br/>
1.发布微博内容并以hash类型存储微博发布的内容相关信息<br/>
incr global:postid                （存储微博自增id）<br/>
hset post:postid:$postid userid 2       （存储微博的用户id）<br/>
hset post:postid:$postid username dongzi                    （存储微博的用户id）<br/>
hset post:postid:$postid time      1466020851                （存储微博的发布时间）<br/>
hset post:postid:$postid content     这是一条微博内容         （存储微博内容）<br/>
​
2.获取用户的所有粉丝及用户自身的id<br/>
smembers followed:2   （获取用户id2的所有粉丝）<br/>

3.将发布的postid与用户信息关联<br/>
lpush recivepost:3 $postid   （将用户发布的最新id$postid给用户id3）<br/>

4.单独建立一个list类型存入自己发送的所有微博id<br/>
lpush userpostid:1 $postid<br/>




