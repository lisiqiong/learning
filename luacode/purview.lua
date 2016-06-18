local cjson = require "cjson"
local arr_return = {}
local json_return = {}

local redis = require "resty.redis"
local red = redis:new()
red:set_timeout(1000) -- 1 sec

local ok, err = red:connect("127.0.0.1", 6379)
if not ok then
    json_return['status'] = 1;
    json_return['mes'] = err;
    ngx.say(cjson.encode(json_return));
    return
end
ok, err = red:select(1)
if not ok then
    ngx.say("failed to select db: ", err)
    return
end

local keys = "ttq:192.168.3.2"
ngx.say("select result: ", ok)
local res,err = red:get(keys)
ngx.say("redis获取key成功，值为：",err)
ngx.say(res)
print(type(res))
if not res  then
    ngx.say("该项目不可以操作")
    return
end

--ok, err = red:set("ttq:192.168.3.2", "192.168.3.2")
--if not ok then
  --  ngx.say("failed to set dog: ", err)
    --return
--end

local mysql = require "resty.mysql"
local db, err_mysql = mysql:new()
if not db then
    ngx.say("failed to instantiate mysql: ", err_mysql)
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
    ngx.say("failed to connect: ", err_mysql, ": ", errno, " ", sqlstate)
    return
end
--ngx.say("connected to mysql.")
sql = "select * from a limit 1"
res, err, errno, sqlstate = db:query(sql)
if not res then
    ngx.say("bad result: ", err, ": ", errno, ": ", sqlstate, ".")
    return
end

ngx.say(res)
local code = 'cs';
arr_return['mes'] = "成功",code;
arr_return['stauts'] = 1;
arr_return['data'] = res;
ngx.say(cjson.encode(arr_return));

