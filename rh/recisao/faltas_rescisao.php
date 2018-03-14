<?php
$ultimo_periodo_vencido = count($periodos_vencidos) - 1;
$ultimo_periodo_vencido = explode('/', $periodos_vencidos[$ultimo_periodo_vencido]);
$falta_aquisitivo_ini   = explode('-', $ultimo_periodo_vencido[0]);
$falta_aquisitivo_end   = explode('-', $ultimo_periodo_vencido[1]);

if($falta_aquisitivo_ini[1] == 12) {
	$limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
}

if($falta_aquisitivo_end[1] == 1) {
	$limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
}
 
$qr_faltas1  = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'");
$row_faltas1 = mysql_fetch_array($qr_faltas1);

$qr_faltas2  = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
$row_faltas2 = mysql_fetch_array($qr_faltas2);
	
$qnt_faltas  = $row_faltas1['faltas'] + $row_faltas2['faltas'];

if($_COOKIE['logado'] == 349){
    print_r($falta_aquisitivo_ini);
    echo "SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]'<br>";
    echo "SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'<br>";
    echo "Leonardo::: $qnt_faltas";
}

$qnt_dias_fv = 30;

if($qnt_faltas <= 5) {
	$qnt_dias_fv = 30;
} elseif($qnt_faltas >= 6 and $qnt_faltas <= 14) {
	$qnt_dias_fv = 24;
} elseif($qnt_faltas >= 15 and $qnt_faltas <= 23) {
	$qnt_dias_fv = 18;
} elseif($qnt_faltas >= 24 and $qnt_faltas <= 32) {
	$qnt_dias_fv = 12;
} elseif($qnt_faltas > 32) {
	$qnt_dias_fv = 0;
} else {
	$qnt_dias_fv = 30;
}
	
unset($falta_aquisitivo_ini,
	  $falta_aquisitivo_end,
	  $limite_falta1,
	  $limite_falta2,
	  $qr_faltas1,
	  $qr_faltas2,
	  $row_faltas1,
	  $row_faltas2, 
	  $qnt_faltas);

$falta_aquisitivo_ini = explode('-', $aquisitivo_ini);
$falta_aquisitivo_end = explode('-', $data_demissao);

if($falta_aquisitivo_ini[1] == 12) {
	$limite_falta1 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta1 = "mes_mov >= '$falta_aquisitivo_ini[1]'";
}

if($falta_aquisitivo_end[1] == 1) {
	$limite_falta2 = "mes_mov = '$falta_aquisitivo_ini[1]'";
} else {
	$limite_falta2 = "mes_mov <= '$falta_aquisitivo_ini[1]'";
}

$qr_faltas1  = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta1 AND (($limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]') OR mes_mov = 16)");
$row_faltas1 = mysql_fetch_array($qr_faltas1);

$meio_faltas = NULL;

for($ano = $falta_aquisitivo_ini[0]+1; $ano < $falta_aquisitivo_end[0]; $ano++) {
	$qr_faltas2   = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND ano_mov = '$ano'");
	$row_faltas2  = mysql_fetch_array($qr_faltas2);
	$meio_faltas += $row_faltas2['faltas'];
        echo "qr_faltas2: SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND ano_mov = '$ano'<br>";
}

$qr_faltas3  = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'");
$row_faltas3 = mysql_fetch_array($qr_faltas3);

$qnt_faltas  = $row_faltas1['faltas'] + $meio_faltas + $row_faltas3['faltas'];

if(in_array($_COOKIE['logado'], $programadores)){
    echo "<br>Teste RAMON: {$qnt_faltas} = {$row_faltas1['faltas']} + {$meio_faltas} + {$row_faltas3['faltas']}";
    echo "<br>qr_faltas1:<br> SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND (($limite_falta1 AND ano_mov = '$falta_aquisitivo_ini[0]') OR mes_mov = 16)";
    echo "<br>qr_faltas3:<br> SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov IN( '232','456') AND (status = '1' or status = '5') AND $limite_falta2 AND ano_mov = '$falta_aquisitivo_end[0]'";    
}

$qnt_dias_fp = 30;

//if ($qnt_faltas <= 5) {
//    $qnt_dias_fp = 30;
//} elseif ($qnt_faltas >= 6 and $qnt_faltas <= 14) {
//    $qnt_dias_fp = 24;
//} elseif ($qnt_faltas >= 15 and $qnt_faltas <= 23) {
//    $qnt_dias_fp = 18;
//} elseif ($qnt_faltas >= 24 and $qnt_faltas <= 32) {
//    $qnt_dias_fp = 12;
//} elseif ($qnt_faltas > 32) {
//    $qnt_dias_fp = 0;
//} else {
//    $qnt_dias_fp = 30;
//}

if ($qnt_faltas <= 5) {
    $qnt_dias_fp = $meses_ativo_fp * 2.5;
} elseif ($qnt_faltas > 5 and $qnt_faltas <= 14) {
    $qnt_dias_fp = $meses_ativo_fp * 2;
} elseif ($qnt_faltas > 14 and $qnt_faltas <= 23) {
    $qnt_dias_fp = $meses_ativo_fp * 1.5;
} elseif (($qnt_faltas > 24 and $qnt_faltas <= 32)) {
    $qnt_dias_fp = $meses_ativo_fp * 1;
} elseif ($qnt_faltas > 32) {
    $qnt_dias_fp = 0;   
}

// 4529 - CAMILA GARCIA
if($id_clt == 4529){
    $qnt_dias_fp = 18;
}

if(in_array( $_COOKIE['logado'], $programadores)){
    echo "<pre>FALTAS FERIAS PROPOSCIONAIS: $qnt_dias_fp
        ID_CLT = $id_clt
        </pre>";
}


?>