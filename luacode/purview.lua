--调用json公共组件
cjson = require("cjson")
fun = require("ttq.fun") -- 引用公用方法文件
conf = require("ttq.ini") --引用配置文件
reds = require("ttq.redis_pool") --引用redis连接池
mysqld = require("ttq.mysql_pool") --引用mysql连接池
--参数校验
check_arg =   fun:check_post_arg() --调用参数校验方法
arg_tables = {} --存储post的参数信息
if check_arg['status'] ==0 then
    --参数校验通过，获取返回的参数，并将参数拼接
    arg_tables= check_arg['arg_tables']
    get_info = string.format("%s:%s:%s",arg_tables['appid'],arg_tables['ip'],arg_tables['appkey'])
else
    ngx.say(fun:resJson(-1,check_arg['msg']))
    return;
end

--1.首先通过redis查找
--2.没有找到再找数据库
--3.根据appid查询项目是否授权
--4.项目获取权限成功，再查询ip是否被限制了
local res,err,value = reds:get_key(get_info)
if not res then
    ngx.say(fun:resJson(-1,err))
    return
end
if value == ngx.null then
     
    --redis数据未空,根据appid查询，查询信息是否一致
    local sql_appid =  string.format("select * from ttq_appid_list   where appid= '%s' and appkey='%s'   limit 1 ",arg_tables['appid'],arg_tables['appkey'])   
    local res,msg,result = mysqld:query(sql_appid)
    --连接失败报错
    if not res then
        ngx.say(fun:resJson(-1,msg))
    end
     
    --未查找数据报错 
    if table.maxn(result)== 0 then
        ngx.say(fun:resJson(-1,'appid验证失败，被拦截'))
        return
    end

    --项目权限获取成功，需要验证ip是否被允许
    local sql = string.format("select * from ttq_appid_white_list where appid='%s' and ip= '%s' limit 1 ",arg_tables['appid'],arg_tables['ip'])
    res,msg,result = mysqld:query(sql)
    if table.maxn(result)==0 then
		ngx.say(fun:resJson(-1,'该项目，非法操作或没有授予权限，被拦截'))
		return
	end

    --所有验证通过，最后写入redis缓存
	ok, err = reds:set_key(get_info,1)
	ngx.say(fun:resJson(0,'该项目鉴权成功,可以访问'));
	return
end
--3.redis找到了信息鉴权成功
ngx.say(fun:resJson(0,"该项目鉴权成功,可以访问!"))
