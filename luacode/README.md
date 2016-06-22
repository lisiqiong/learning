1.penresty的lualib目录下<br/>
2.purview.sql为签权相关表<br/>
3.调用是通过openresty的nginx调用ttq目录下的purview.lua文件<br/>
    比如我的安装路径为：/data/local/openresty/nginx/conf/nginx.conf<br/>
        location /lua{<br/>
            lua_code_cache on;<br/>
            content_by_lua_file /data/local/openresty/lualib/ttq/purview.lua;<br/>
        }<br/>
