<?php
/*
 *@desc redis类操作文件
 *
 **/
class MyRedis{
    private $redis;
    public $error;
    /*
     *@desc 实例化redis类
     **/
    public function __construct($host='127.0.0.1',$port='6379'){
        $this->redis = new Redis();
        $this->redis->connect($host,$port);
        return $this->redis;
    }
  
    /*
     *@desc 设置字符串类型的值，以及失效时间
     **/
    public function set($key,$value=0,$timeout=0){
        if(empty($value)){
            $this->error = "设置键值不能够为空哦~";
            return $this->error;
        }
        $res = $this->redis->set($key,$value);
        if($timeout){
            $this->redis->expire($key,$timeout);
        }
        return $res;
    }

    /**
     *@desc 获取字符串类型的值
     **/
    public function get($key){
        return $this->redis->get($key);
    }

    


}

?>
