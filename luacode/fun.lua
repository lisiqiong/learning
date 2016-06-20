local _M = {}

--返回json信息公用方法
function _M.resJson(status,mes)
	local arr_return = {}
--	arr_return['status'] = status
--	arr_return['msg'] = mes
--	return cjson.encode(arr_return)
    ngx.say(111)
end


return _M
