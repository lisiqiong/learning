<?php

$i = 1;
while ($i<=10) {
    echo '$i++:'.$i++.'$i='.$i.PHP_EOL;
}


exit;
$n = 4;
$left = 0;
$right = $n-1;
while($left<$right){
    error_log('---'.$left);
    $left++;
    //$right--;
}

