<?php

include('../conn.php');

if(isset($_GET['horas'])){
	
$horas_mes = mysql_real_escape_string($_GET['horas']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET horas_mes = '$horas_mes' WHERE id_horario = '$id_horario'") or die(mysql_error());

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

if(isset($_GET['dias'])){
	
$dias_mes = mysql_real_escape_string($_GET['dias']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET dias_mes = '$dias_mes' WHERE id_horario = '$id_horario'");

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

if(isset($_GET['entrada1'])){
	
$entrada1 = mysql_real_escape_string($_GET['entrada1']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET entrada_1 = '$entrada1' WHERE id_horario = '$id_horario'");

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

if(isset($_GET['saida1'])){
	
$saida1 = mysql_real_escape_string($_GET['saida1']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET saida_1 = '$saida1' WHERE id_horario = '$id_horario'");

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

if(isset($_GET['entrada2'])){
	
$entrada2 = mysql_real_escape_string($_GET['entrada2']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET entrada_2 = '$entrada2' WHERE id_horario = '$id_horario'");

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

if(isset($_GET['saida2'])){
	
$saida2 = mysql_real_escape_string($_GET['saida2']);
$id_horario = mysql_real_escape_string($_GET['horario']);


$qr_update = mysql_query("UPDATE rh_horarios SET saida_2 = '$saida2' WHERE id_horario = '$id_horario'");

if($qr_update){ echo '1'; } else {echo 0;}

exit;
	
}

?>