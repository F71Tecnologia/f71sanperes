<?php

/**
 * INICIANDO VARIAVEIS
 */
$count      = 1;
$totalSemanaPerdida = array();
$dt_inicial = "2016-01-01";
$dt_final   = "2016-12-31";


/**
 * ARRAY DE FALTAS
 */
$arrayDeFaltas = array(
                    "2016-04-01",
                    "2016-04-02",     
                    "2016-04-05", 
                    "2016-04-21", 
                    "2016-04-24", 
                    "2016-04-25", 
                    "2016-04-26", 
                    "2016-04-27"     
                );

/**
 * OBJETOS
 */
$begin      = new DateTime($dt_inicial);
$end        = new DateTime($dt_final);
$end        = $end->modify('+1 day'); 
$interval   = new DateInterval('P1D');
$daterange  = new DatePeriod($begin, $interval ,$end);

/**
 * 
 */
foreach($daterange as $date){
    /**
     * $date->format("N")
     * Representação numérica ISO-8601 
     * do dia da semana (adicionado no PHP 5.1.0)	
     * 1 (para Segunda) até 7 (para Domingo)
     */
    if($date->format("N") == 7){
        $count++;
    }

    $arraySemanas[$count][$date->format("N")] = $date->format("Y-m-d");

}

$a = 0;
for($i=1;$i <= count($arraySemanas);$i++){
    if($a != $i){
        $a = $i;
        foreach ($arraySemanas[$a] as $key => $values){
            if(in_array($values, $arrayDeFaltas)){
                $totalSemanaPerdida[$a] = 1;
            }
        }            
    }
}

echo "<pre>";
    print_r($arrayDeFaltas);
    print_r($totalSemanaPerdida);
    echo "Total de DSR Perdido: " . count($totalSemanaPerdida) . "<br />";
echo "</pre>";

    