--调用json公共组件
cjson = require "cjson"
local fun = require("ttq.fun") -- 引用公用方法文件
local conf = require("ttq.ini") --引用配置文件

--接收POST过来的数据
ngx.req.read_body()
local arg = ngx.req.get_post_args()
local arg_count = 0 --存储参数个数
local arg_table = {appid,ip,appkey}
local appid,ip,appkey
local get_info --参数拼接字符串，方便redis操作
--遍历post过来的参数
for k,v in pairs(arg) do
    arg_count = arg_count+1
    arg_table[k] = v
end

--参数赋值
appid = arg_table['appid'] 
ip = arg_table['ip']
appkey = arg_table['appkey']

--参数校验,默认只有三个参数
if arg_count == 3 then
   if string.len(appid) == 0 then
        ngx.say(fun.resJson(-1,'参数传递错误'))
        return
   end
   if string.len(ip) == 0 then 
        ngx.say(fun.resJson(-1,'参数传递错误'))
        return
   end
   if string.len(appkey) == 0 then 
        ngx.say(fun.resJson(-1,'参数传递错误'))
        return
   end
else
    ngx.say(fun.resJson(-1,'参数传递错误，被拦截'))
    return
end

--参数校验通过，将参数拼接
get_info = string.format("%s:%s:%s",appid,ip,appkey)


--连接redis
local redis = require "resty.redis"
local red = redis:new()
red:set_timeout(1000) -- 1 sec
local ok, err = red:connect(conf.redis()['host'],conf.redis()['port'])
if not ok then
	ngx.say(fun.resJson(-1,"failed to connect redis"))
	return
end
--设置redis密码
local count
count, err = red:get_reused_times()
if 0 == count then
    ok, err = red:auth(conf.redis()['pass'])
    if not ok then
        ngx.say(fun.resJson(-1,"redis failed to auth"))
        return
    end
elseif err then
    ngx.say(fun.resJson(-1,"redis failed to get reused times"))
    return
end
--选择redis数据库
ok, err = red:select(0)
if not ok then
	ngx.say(fun.resJson(-1,"redis connect failed"))    
	return
end

--连接mysql
local mysql = require "resty.mysql"
local db, err_mysql = mysql:new()
if not db then
    ngx.say(fun.resJson(-1,"failed to instantiate mysql: "))
    return
end
db:set_timeout(1000) -- 1 sec
local ok, err_mysql, errno, sqlstate = db:connect{
     host = conf.mysql()['host'],
             port = conf.mysql()['port'],
             database = conf.mysql()['database'],
             user = conf.mysql()['user'],
             password = conf.mysql()['password'],
             max_packet_size = 1024 * 1024 
}
if not ok then
   -- ngx.say("failed to connect: ", err_mysql, ": ", errno, " ", sqlstate)
	ngx.say(fun.resJson(-1,"mysql connect failed"))
    return
end




--1.首先通过redis查找
--2.没有找到再找数据库
--3.根据appid查询项目是否授权
--4.项目获取权限成功，再查询ip是否被限制了
local res,err = red:get(get_info)
if res == ngx.null then
    --数据找到了,根据appid查询，查询信息是否一致
    local sql_appid =  string.format("select * from ttq_appid_list   where appid= '%s' and appkey='%s'   limit 1 ",appid,appkey)   
    res = db:query(sql_appid)
    if table.maxn(res)== 0 then
        ngx.say(fun.resJson(-1,'appid验证失败，被拦截'))
        return
    end
    --appid获取的项目名与参数传递的是否一致
    local project_name = res[1]['appid']
    if project_name~=appid then
        ngx.say(fun.resJson(-1,"appid验证失败，未找到指定项目，被拦截"))
        return
    end

    --项目权限获取成功，需要验证ip是否被允许
    local sql = string.format("select * from ttq_appid_white_list where appid='%s' and ip= '%s' limit 1 ",appid,ip)
    res = db:query(sql)
    if table.maxn(res)==0 then
		--ngx.say("bad result: ", err, ": ", errno, ": ", sqlstate, ".")
		ngx.say(fun.resJson(-1,'该项目，非法操作或没有授予权限，被拦截'))
		return
	end

    --所有验证通过，最后写入redis缓存
	ok, err = red:set(get_info,1)
	ngx.say(fun.resJson(0,'该项目鉴权成功,可以访问'));
	return
end
--3.redis找到了信息鉴权成功
ngx.say(fun.resJson(0,"该项目鉴权成功,可以访问!"))
