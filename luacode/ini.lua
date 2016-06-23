--配置相关方法
local _CONF = {}
--返回redis配置文件
function _CONF.redis()
    local  redis_config = {host='127.0.0.1',pass='123456',port=6379} --redis配置项 
    return redis_config
end

--返回mysql配置文件
function _CONF.mysql() 
    local mysql_config = {host='127.0.0.1',port=3306,database='test',user='root',password='123456'} --mysql的配置项
    return mysql_config
end

return _CONF
