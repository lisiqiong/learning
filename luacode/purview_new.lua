--调用json公共组件
local cjson = require "cjson"
local redis_config = {host='127.0.0.1',pass='123456',port=6379} --redis配置项 
local mysql_config = {host='127.0.0.1',port=3306,database='test',user='root',password='123456'} --mysql的配置项


--返回json信息公用方法
function resJson(status,mes)
	local arr_return = {}
	arr_return['status'] = status
	arr_return['msg'] = mes
	return cjson.encode(arr_return)
end

--字符串按指定字符拆分公用方法
function lua_string_split(str, split_char)    
    local sub_str_tab = {}; 
    while (true) do        
        local pos = string.find(str, split_char);  
        if (not pos) then            
            local size_t = table.getn(sub_str_tab)
            table.insert(sub_str_tab,size_t+1,str);
            break;  
            end
    
        local sub_str = string.sub(str, 1, pos - 1);              
        local size_t = table.getn(sub_str_tab)
        table.insert(sub_str_tab,size_t+1,sub_str);
        local t = string.len(str);
        str = string.sub(str, pos + 1, t);   
    end    
    return sub_str_tab;
end


--接收变量
local arg = ngx.req.get_uri_args()
local get_info,project,ip,appid
for k,v in pairs(arg) do
   get_info = v
    --ngx.say("[GET ] key:", k, " v:", v)
end

--验证传递的参数是否合法
--if get_info == ngx.null then
  --  ngx.say(resJson(-1,"参数传递错误，非法操作，被拦截"));
--end

--将获取的变量拆分为table类型
local ta_get  = lua_string_split(get_info,":")
project = ta_get[1]
ip = ta_get[2]
appid = ta_get[3]


--连接redis
local redis = require "resty.redis"
local red = redis:new()
red:set_timeout(1000) -- 1 sec
local ok, err = red:connect(redis_config['host'],redis_config['port'])
if not ok then
	ngx.say(resJson(-1,"failed to connect redis"))
	return
end
--设置redis密码
local count
count, err = red:get_reused_times()
if 0 == count then
    ok, err = red:auth(redis_config['pass'])
    if not ok then
        ngx.say(resJson(-1,"redis failed to auth"))
        return
    end
elseif err then
    ngx.say(resJson(-1,"redis failed to get reused times"))
    return
end
--选择redis数据库
ok, err = red:select(0)
if not ok then
	ngx.say(resJson(-1,"redis connect failed"))    
	return
end

--连接mysql
local mysql = require "resty.mysql"
local db, err_mysql = mysql:new()
if not db then
    ngx.say(resJson(-1,"failed to instantiate mysql: "))
    return
end
db:set_timeout(1000) -- 1 sec
local ok, err_mysql, errno, sqlstate = db:connect{
     host = mysql_config['host'],
             port = mysql_config['port'],
             database = mysql_config['database'],
             user = mysql_config['user'],
             password = mysql_config['password'],
             max_packet_size = 1024 * 1024 
}
if not ok then
   -- ngx.say("failed to connect: ", err_mysql, ": ", errno, " ", sqlstate)
	ngx.say(resJson(-1,"mysql connect failed"))
    return
end




--1.首先通过redis查找
--2.没有找到再找数据库
--3.根据appid查询项目是否授权
--4.项目获取权限成功，再查询ip是否被限制了
local res,err = red:get(get_info)
if res == ngx.null then
    --数据找到了,根据appid查询，查询信息是否一致
    local sql_appid =  string.format("select * from ttq_appid_list   where appid= '%s' limit 1 ",appid)   
    res = db:query(sql_appid)
    if table.maxn(res)== 0 then
        ngx.say(resJson(-1,'找不到您传递的appid，被拦截'))
        return
    end
    --appid获取的项目名与参数传递的是否一致
    local project_name = res[1]['project_name']
    if project_name~=project then
        ngx.say("appid验证失败，未找到指定项目，被拦截")
        return
    end

    --项目权限获取成功，需要验证ip是否被允许
    local sql = string.format("select * from ttq_white_list where project_name='%s' and ip= '%s' limit 1 ",project,ip)
    res = db:query(sql)
    if table.maxn(res)==0 then
		--ngx.say("bad result: ", err, ": ", errno, ": ", sqlstate, ".")
		ngx.say(resJson(-1,'该项目，非法操作或没有授予权限，被拦截'))
		return
	end

    --所有验证通过，最后写入redis缓存
	ok, err = red:set(get_info,1)
	ngx.say(resJson(0,'该项目鉴权成功,可以访问'));
	return
end
--3.redis找到了信息鉴权成功
ngx.say(resJson(0,"该项目鉴权成功,可以访问!"))

