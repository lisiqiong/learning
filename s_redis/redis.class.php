<?php
/*
 *@desc redis类操作文件
 *
 **/
class MyRedis{
    private $redis;
    /*
     *@desc 实例化redis类
     **/
    public function __construct($host='127.0.0.1',$port='6379'){
        $this->redis = new Redis();
        $this->redis->connect($host,$port);
        return $this->redis;
    }
  
    public function get($key){
        return $this->redis->get($key);
    }

}

?>
