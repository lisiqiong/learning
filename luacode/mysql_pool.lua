module("mysql_pool", package.seeall)

local dbConfig = require"config"
local mysql = require("resty.mysql")

local mysql_pool = {}


function mysql_pool:get_connect()
    if ngx.ctx[mysql_pool] then
    return true, ngx.ctx[mysql_pool]
    end

local client, errmsg = mysql:new()
    if not client then
    return false, "mysql.socket_failed: " .. (errmsg or "nil")
    end

    client:set_timeout(10000)  --10秒

    local options = {
        host = dbConfig.DBHOST,
        port = dbConfig.DBPORT,
        user = dbConfig.DBUSER,
        password = dbConfig.DBPASSWORD,
        database = dbConfig.DBNAME
    }

local result, errmsg, errno, sqlstate = client:connect(options)
    if not result then
    return false, "mysql.cant_connect: " .. (errmsg or "nil") .. ", errno:" .. (errno or "nil") ..
    ", sql_state:" .. (sqlstate or "nil")
    end

    local query = "SET NAMES " .. dbConfig.DEFAULT_CHARSET
local result, errmsg, errno, sqlstate = client:query(query)
    if not result then
    return false, "mysql.query_failed: " .. (errmsg or "nil") .. ", errno:" .. (errno or "nil") ..
    ", sql_state:" .. (sqlstate or "nil")
    end

    ngx.ctx[mysql_pool] = client
    return true, ngx.ctx[mysql_pool]
    end


function mysql_pool:close()
    if ngx.ctx[mysql_pool] then
ngx.ctx[mysql_pool]:set_keepalive(60000, 1000)
    ngx.ctx[mysql_pool] = nil
    end
    end


    function mysql_pool:query(sql, flag)
local ret, client = self:get_connect(flag)
    if not ret then
    return false, client, nil
    end

    local result, errmsg, errno, sqlstate = client:query(sql)
self:close()

    if not result then
    errmsg = concat_db_errmsg("mysql.query_failed:", errno, errmsg, sqlstate)
    return false, errmsg, sqlstate
    end

    return true, result, sqlstate
    end

    return mysql_pool



