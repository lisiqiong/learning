
--连接mysql
local mysql = require "resty.mysql"
local mysql_pool = {}
function mysql_pool.get_connect()
    if ngx.ctx[mysql_pool] then
        return true,ngx.ctx[mysql_pool]
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
    
    ngx.ctx[mysql_pool] = db
    return true,ngx.ctx[mysql_pool]

end


return mysql_pool

