<?php


include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('../classes/CalculosComumClass.php');

$mes = "01";
$ano = "2016";

/**
 * OBJETO
 */
$call = new calculosComumClass();

/**
 * QUANTIDADE DE DOMINGOS
 * NECESSÁRIO PARA CALCULO DE DSR 
 */
$domingos = $call->qntDomingos($mes,$ano);

/**
 * METODO DE CALCULO DE AD. NOTURNO
 * NECESSÁRIO PARA CALCULO DE DSR
 */
echo "Ad.Noturno: " . $call->calcAdicionalNoturno(1067.00, 176.00, 180, 120) . "<br />";

/**
 * CALCULO DE DSR 
 */
echo "DSR: " . $call->calcDSR() . "<br />";

exit();
