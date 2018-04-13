<?php


/**
 *@desc 实现数组反转
 **/

function reverse($arr)
{
    //思想，首尾依次替换值，中间位值不替换
    $n  = count($arr);
    $left = 0;
    $right = $n-1;
    while($left<$right)
    {   
        $temp = $arr[$left];
        $arr[$left++] = $arr[$right];
        $arr[$right--] = $temp;
    }
    return $arr;

}

$arr = ['a','e','测试','哈哈哈','ok','test','xuxu'];
$result = reverse($arr);
print_r($result);




