
--连接mysql
local mysql = require "resty.mysql"
local mysql_pool = {}
function mysql_pool:get_connect()
    if ngx.ctx[mysql_pool] then
        return true,'返回mysql连接池成功',ngx.ctx[mysql_pool]
    end
    local db, err_mysql = mysql:new()
    if not db then
        return false,"failed to instantiate mysql"
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
	    --ngx.say(fun.resJson(-1,"mysql connect failed"))
        return false,"mysql conncet failed"
    end
    --存储mysql连接池并返回
    ngx.ctx[mysql_pool] = db
    return true,'mysql连接成功',ngx.ctx[mysql_pool]

end


--关闭mysql连接池
function mysql_pool:close()
    if ngx.ctx[mysql_pool] then
        ngx.ctx[mysql_pool]:set_keepalive(60000, 1000)
        ngx.ctx[mysql_pool] = nil
    end
end


--执行sql查询
function mysql_pool:query(sql)
    --ngx.say(sql)
    local ret,msg,client = self:get_connect()
    --连接数据库失败，返回错误信息
    if not ret then
       return false,msg
    end
    --连接成功后执行sql查询,执行失败返回错误信息
    local res,errmsg,errno,sqlstate = client:query(sql)
    --self:close()
    if not res then
        return false,errmsg
    end
    --ngx.say(res[1]['appid'])
    --ngx.say(res[1]['ip'])
    --执行成功，返回信息
    return true,"查询信息成功",res
end

return mysql_pool

