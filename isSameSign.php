<?php

/**
 *@desc 判断输入的两个数字是否相同（是否同为正数，或负数）
 ***/
function isSameSign($a,$b)
{
    if ($a*$b<0) {
        return true;
    } else {
        return false;
    }
}
$a = 8;
$b = -9;
$res = isSameSign($a,$b);
if($res){
    echo "符号不相同";
}else{
    echo "符号不相同";
}


