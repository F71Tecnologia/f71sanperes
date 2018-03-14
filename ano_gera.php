<?php
include('conn.php');

function diasemana($data) {
    $ano =  substr("$data", 0, 4);
    $mes =  substr("$data", 5, -3);
    $dia =  substr("$data", 8, 9);
    
    $diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );
    
    switch($diasemana) {
        case "0": $diasemana = "Domingo";       
            break;
        case "1": $diasemana = "Segunda-feira"; 
            break;
        case "2": $diasemana = "Terça-feira";   
            break;
        case "3": $diasemana = "Quarta-feira";  
            break;
        case "4": $diasemana = "Quinta-feira";  
            break;
        case "5": $diasemana = "Sexta-feira";   
            break;
        case "6": $diasemana = "Sábado";        
            break;
    }
    
    return $diasemana;
}

$k = 0;

while ($k != 366){
    $data = date('Y-m-d', strtotime('2017-01-01'. " +$k days"));
    $dia = diasemana($data);
    
    if(($dia == 'Sábado') || ($dia == 'Domingo')){
        $fds = 1;
    }else{
        $fds = 0;
    }
    
    $qry = "INSERT INTO ano (data, nome, status, fds) VALUES ('{$data}', '{$dia}', 1, {$fds});";
    $sql = mysql_query($qry) or die(mysql_error());
    
    $k++;
}