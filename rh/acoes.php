<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>:: Intranet ::</title>
</head>
<body>
<?php
include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$acao = $_REQUEST['acao'];
$id_protocolo = $_REQUEST['id_protocolo'];
$mes_referencia = $_REQUEST['mes_referencia'];
$data_ini = $_REQUEST['data_ini'];
$data_final = $_REQUEST['data_final'];
$mes = $_REQUEST['mes'];
$status = $_REQUEST['status'];
$regiao = $_REQUEST['regiao'];

if($status == 'criar') {
    
	$data_ini_MYSQL = implode('-', array_reverse(explode('/',$data_ini)));
	$data_final_MYSQL = implode('-', array_reverse(explode('/',$data_final)));
	$ANO = date('Y');
	
	// Analisa se o protocolo já do mês já foi cadastrado.
	$result = mysql_query("SELECT * FROM rh_vale_protocolo WHERE id_reg = '$regiao' AND mes = '$mes' AND ano = '$ANO'");
	$num_row_verifica = mysql_num_rows($result);
	
	if(empty($num_row_verifica)) {
			 
		mysql_query("INSERT rh_vale_protocolo SET id_reg = '$regiao', mes = '$mes', ano = '$ANO', data_ini = '$data_ini_MYSQL', data_fim = '$data_final_MYSQL', user = '$id_user', data = CURDATE()");
		
	} else {	
	
		$qr_mes = mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$mes = mysql_fetch_array($qr_mes);
		echo "<script> alert('O mês de $mes[nome_mes] não pode ser gerado novamente!'); </script>";
		
	}
	
}

if($acao = 'removerarquivo') {
	
	mysql_query("DELETE FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao'")or die(mysql_error());
	
	mysql_query("DELETE FROM rh_vale_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND status = 'GRAVADO'")or die(mysql_error());
	
	mysql_query("DELETE FROM rh_vale_protocolo WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND status = 'IMPRESSO'")or die(mysql_error());
	
}

if($acao = 'removerprotocolo') {
	
	 mysql_query("DELETE FROM rh_vale_protocolo WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao' AND mes = '$mes_referencia'")or die(mysql_error());
	 
}

echo "<script>location.href=\"rh_valerelatorios.php?regiao=$regiao\";</script>";
?>
</body>
</html>