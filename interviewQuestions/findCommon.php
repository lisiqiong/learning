
<?php

/**
**@desc 寻找两个有序数组里相同的元素
@param array $arr1
@param array $arr2
@return array
 **/
function findCommon($arr1,$arr2)
{
    $common = [];
    $i = $j = 0;
    $count1 = count($arr1);
    $count2 = count($arr2);
    while( $i<$count1  && $j<$count2 )
    {
        if($arr1[$i]<$arr2[$j]){
            $i++;
        }elseif($arr1[$i]>$arr2[$j]){
            $j++;    
        }else{
            $common[] = $arr2[$i];    
            $i++;
            $j++;
        }
    }
    return array_unique($common);
}

$arr1 = [2,5,7,19,13,45];
$arr2 = [7,6,3,19,34,2];
$test = findCommon($arr1,$arr2);
print_r($test);










