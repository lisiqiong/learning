local redis = require("resty.redis")
local redis_pool = {}
--连接redis
function redis_pool.get_connect()
    if ngx.ctx[redis_pool] then
        return true,"redis连接成功",ngx.ctx[redis_pool]
    end
    local red = redis:new()
    red:set_timeout(1000) -- 1 sec
    local ok, err = red:connect(conf.redis()['host'],conf.redis()['port'])
    if not ok then
	    return false,"failed to connect redis"
    end
    --设置redis密码
    local count, err = red:get_reused_times()
    if 0 == count then
        ok, err = red:auth(conf.redis()['pass'])
        if not ok then
            return false,"redis failed to auth"
        end
    elseif err then
        return false,"redis failed to get reused times"
    end
    --选择redis数据库
    ok, err = red:select(0)
    if not ok then
        return false,"redis connect failed "
    end
    --建立redis连接池
    ngx.ctx[redis_pool] = red
    return true,'redis连接成功',ngx.ctx[redis_pool]
end


---获取key的值
function redis_pool.get_key(str)
    local res,err,client = redis_pool:get_connect()
    if not res then
        return false,err
    end
    local keys = client:get("ttq:192.168.3.3:67872")
    return true,"获取key成功",keys
end

--设置key的值
function redis_pool.set_key(str,value)
    local res,err,client = redis_pool:get_connect()
    client:set("ttq:192.168.4.3",1)
    return true
end

return redis_pool
