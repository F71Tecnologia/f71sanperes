<?php
include('../conn.php');
include('../funcoes.php');

$folha = $_GET['folha'];
$contratacao=$_GET['tipo_contratacao'];


switch($contratacao) {
	
			case 1: $tabela = 'folha_autonomo';
			break;	
			
			case 3: $tabela = 'folha_cooperado';
			break;	
			
			case 4: $tabela = 'folha_autonomo';
			break;	
}

$qr_folha  = mysql_query("SELECT * FROM folhas WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

$total_participantes = mysql_num_rows(mysql_query("SELECT * FROM $tabela WHERE id_folha = '$folha'  AND status = '3'"));

mysql_query("UPDATE folhas SET status = '2' WHERE id_folha = '$folha' AND contratacao = '$contratacao'  LIMIT 1");
mysql_query("UPDATE $tabela SET status = '2' WHERE id_folha = '$folha'   AND status = '3'  LIMIT $total_participantes");

$linkreg = str_replace('+','--',encrypt($row_folha['regiao'].'&'.$row_folha['regiao']));

header("Location: folha.php?id=9&enc=$linkreg");
exit();
?>