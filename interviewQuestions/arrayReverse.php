<?php


/**
 *@desc 实现数组反转
 **/
function reverse($arr)
{
    $n = count($arr);
    $left = 0;
    $right = $n-1;
    while($left<$right){
        //error_log('left--:'.$left.'--right:'.$right.'--');
        $temp = $arr[$left];
        $arr[$left++] = $arr[$right];
        error_log('$arr--:'.print_r($arr,true));
        $arr[$right--] = $temp;
        //error_log('left--:'.$left.'--right:'.$right);
    }
    return $arr;
}


$arr = ['a','e','测试','哈哈哈','ok','test','xuxu'];
$result = reverse($arr);
print_r($result);




