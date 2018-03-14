<?php
$endereco = "RUA A LOTE 20 QD 3";
$array = explode(" ", $endereco);
$new_array = array();

//echo "<pre>";
//print_r($array);
//echo "</pre>";

function removeNumeros($string){
    return preg_replace('/([0-9]{1,})/i', "", $string);
}

foreach($array as $val){
    if($val != "LOTE" && $val != "QD"){
        array_push($new_array, $val);
    }
}

$array2 = implode(" ", $new_array);
$array2 = removeNumeros($array2);

echo "<pre>";
print_r($array2);
echo "</pre>";
?>