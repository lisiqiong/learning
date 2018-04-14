<?php



/*function three_sum($arr)
{
    $n = count($arr);
    $return = array();
    for ($i=0; $i < $n; $i++) {
        $left = $i + 1;
        $right = $n - 1;
        while ($left <= $right) {
            $sum = $arr[$i] + $arr[$left] + $arr[$right];
            if ($sum < 0) {
                $left++;
            } elseif ($sum > 0) {
                $right--;
            } else {
                $numbers = $arr[$i] . ',' . $arr[$left] . ',' . $arr[$right];
                if (!in_array($numbers, $return)) {
                    $return[] = $numbers;
                }
                $left++;
                $right--;
            }
        }
    }
    return $return;
}*/


/**
 *@desc 获取一个数值数组中的随机三个值相加为0的所有情况
 **/
function three_sum($arr)
{
    $return = [];    
    $n = count($arr);
    for($i=0;$i<$n;$i++)
    {   
        $left = $i+1;
        $right = $n-1;
        while($left <= $right){
            $sum =  $arr[$i] + $arr[$left] + $arr[$right];
            if($sum<0){
               $left++; 
            }elseif($sum>0){
               $right--;
            }else{
                $sumStr = $arr[$i].','.$arr[$left].','.$arr[$right];
                if(!in_array($sumStr,$return)){
                    $return[] = $sumStr;
                }
                $left++;
                $right++;
            }

        }
    }
    return $return;
}




$arr = [-10, -9, -8, -4, -2, 0, 1, 2, 3, 4, 5, 6, 9];
print_r(three_sum($arr));















