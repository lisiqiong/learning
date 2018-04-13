<?php


/**
 ***@desc 随机打乱数组
 ***/
function custom_shuffle($arr)
{

    $n = count($arr);
    for($i=0;$i<$n;$i++){
        $rand = mt_rand(0,$n-1);
        if($i!=$rand){
            $temp = $arr[$i];
            $arr[$i] = $arr[$rand];
            $arr[$rand] = $temp;
        }

    }

    return $arr;
}
$arr = [333,89,34,11,2,43,6,78,90];
$test = custom_shuffle($arr);
print_r($test);






