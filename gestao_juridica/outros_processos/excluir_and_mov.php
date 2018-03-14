<?php
include ("../include/restricoes.php");
include('../../conn.php');
include('../../funcoes.php');

$id_processo = mysql_real_escape_string($_GET['id_processo']);


if(isset($_GET['tp'])) {
	$id_andamento = mysql_real_escape_string($_GET['id']);
	
	
	$tipo_trabalhador =  mysql_real_escape_string($_GET['tp']);
	$qr_andammento = mysql_query("UPDATE proc_trab_andamento SET	andamento_status = 0 WHERE andamento_id='$id_andamento' LIMIT 1");
	
	
	

		header("Location:  dados_processo/ver_processo.php?id_processo=$id_processo");
	

}

if(isset($_GET['id_movimento'])) {

$id_movimento = $_GET['id_movimento'];
mysql_query("UPDATE proc_trab_movimentos SET status = 0 WHERE proc_trab_mov_id = '$id_movimento'");

header("Location: dados_processo/ver_processo.php?id_processo=$id_processo");

}
?>