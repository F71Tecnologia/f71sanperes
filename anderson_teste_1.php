<?php
error_reporting(-1);
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}
include('conn.php');
include('wfunction.php');
/*
$data_entrada= '2013-09-01';

$data_demi     = '2013-09-30';
$data_demi_seg = strtotime($data_demi);
$prazoexp  = 2;

switch($prazoexp){
    
    case 1: //30 + 60
            $data_periodo_1 =  strtotime("+29 days", strtotime($data_entrada));
            $data_periodo_2 =  strtotime("+59 days", strtotime($data_entrada));
        break;
    case 2: //45 + 45
             $data_periodo_1 =  strtotime("+44 days", strtotime($data_entrada));
             $data_periodo_2 =  strtotime("+44 days", strtotime($data_entrada));
        break;
    case 3: ///60 + 30
             $data_periodo_1 = strtotime("+59 days", strtotime($data_entrada));
             $data_periodo_2 = strtotime("+29 days", strtotime($data_entrada));
        break;
}

if($data_demi_seg < $data_periodo_1){
    $dias = ($data_periodo_1 - $data_demi_seg)/86400;
} else {
    
      $dias = ($data_periodo_2 - $data_demi_seg)/86400;
}


echo (int)$dias. ' - '.date('Y-m-d',$data_periodo_2);
 * */
echo date_default_timezone_get();