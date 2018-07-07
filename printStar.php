printStar.php
<?php

$array = ["5","9","1","8","22","6.5"];
print_r(input($array, 10));

function input($array, $x){
    $CountNumber = 0;
    $MyArrayLength = count($array);
    for($i=0; $i<$MyArrayLength; $i++){
        if((is_numeric($array[$i]) == true) && ($array[$i] > $x))
        $CountNumber = $CountNumber + $array[$i];
    }
    return $CountNumber;
}

?>
