local _M = {}

--返回json信息公用方法
function _M.resJson(status,mes)
	local arr_return = {}
	arr_return['status'] = status
	arr_return['msg'] = mes
	return cjson.encode(arr_return)
end



--字符串按指定字符拆分公用方法
function _M.lua_string_split(str, split_char)    
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


--检测post过来的参数合法性
function _M.check_post_arg()
    local rule_count =  3
    --接收POST过来的数据
    ngx.req.read_body()
    local arg = ngx.req.get_post_args()
    local arg_count = 0 --存储参数个数
    local arg_table = {appid,ip,appkey}
    local get_info --参数拼接字符串，方便redis操作
    --遍历post过来的参数
    for k,v in pairs(arg) do
        arg_count = arg_count+1
        arg_table[k] = v
    end

    --参数赋值
    appid = arg_table['appid'] 
    ip = arg_table['ip']
    appkey = arg_table['appkey']
    --判断参数个数传递过来的参数要与规定的个数一致
    if rule_count == arg_count then
        if string.len(appid) == 0 then
            return  {status=-1,msg='参数传递错误，被拦截'}
        end
        if string.len(ip) == 0 then 
            return  {status=-1,msg='参数传递错误，被拦截'}
        end
        if string.len(appkey) == 0 then 
            return  {status=-1,msg='参数传递错误，被拦截'}
        end 
        ---参数正确返回参数信息
        return  {status=0,msg='参数校验成功',arg_tables=arg_table}
    else
        return  {status=-1,msg='参数传递错误，被拦截'}
    end
end

return _M
