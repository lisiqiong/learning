--调用json公共组件
local cjson = require "cjson"

--返回json信息公用方法
function resJson(status,mes)
	local arr_return = {}
	arr_return['status'] = status
	arr_return['mes'] = mes
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
local get_info
local project
local ip
for k,v in pairs(arg) do
   get_info = v
    --ngx.say("[GET ] key:", k, " v:", v)
end



local ta_get  = lua_string_split(get_info,":")
project = ta_get[1]
ip = ta_get[2]

--连接redis
local redis = require "resty.redis"
local red = redis:new()
red:set_timeout(1000) -- 1 sec
local ok, err = red:connect("127.0.0.1", 6379)
if not ok then
	ngx.say(resJson(-5,"failed to connect redis"))
	return
end
ok, err = red:select(0)
if not ok then
	ngx.say(resJson(-3,"redis connect failed"))    
	return
end

--连接mysql
local mysql = require "resty.mysql"
local db, err_mysql = mysql:new()
if not db then
    ngx.say(resJson(-4,"failed to instantiate mysql: "))
    return
end
db:set_timeout(1000) -- 1 sec
local ok, err_mysql, errno, sqlstate = db:connect{
     host = "127.0.0.1",
             port = 3306,
             database = "test",
             user = "root",
             password = "123456",
             max_packet_size = 1024 * 1024 
}
if not ok then
   -- ngx.say("failed to connect: ", err_mysql, ": ", errno, " ", sqlstate)
	ngx.say(resJson(-2,"mysql connect failed"))
    return
end




--1.首先通过redis查找
--2.没有找到再找数据库，（如果数据有,提示正常操作，提示没有非法操作）
local res,err = red:get(get_info)
if res == ngx.null then
--查询数据库有没有值
    local sql = string.format("select * from ttq_white_list where project_name='%s' and ip= '%s' limit 1 ",project,ip)
    res, err, errno, sqlstate = db:query(sql)
    if table.maxn(res)==0 then
		--ngx.say("bad result: ", err, ": ", errno, ": ", sqlstate, ".")
		ngx.say(resJson(-1,'该项目，非法操作或没有授予权限，被拦截'))
		return
	end
    --数据找到了，将数据写入redis缓存
	ok, err = red:set(get_info,1)
	ngx.say(resJson(1,'该项目鉴权成功,可以访问'));
	return
end
--3.redis找到了信息鉴权成功
ngx.say(resJson(1,"该项目鉴权成功,可以访问!"))


