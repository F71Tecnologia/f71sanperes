<?php 
include('../../conn.php');

$caso  = $_REQUEST['caso'];
$valor = $_REQUEST['valor'];
$id_folha_participante = $_REQUEST['id_folha_participante'];

$query_participante = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha_pro = '".$id_folha_participante."'");
$row_participante   = mysql_fetch_assoc($query_participante);

$qr_folha    = mysql_query("SELECT * FROM folhas WHERE id_folha = '$row_participante[id_folha]' AND status = '2'");
$row_folha   = mysql_fetch_assoc($qr_folha);

$valor_banco = str_replace(',','.',str_replace('.','',$valor));

if($caso == 'rendimentos') {

	$update = mysql_query("UPDATE folha_cooperado SET adicional = '".$valor_banco."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
} if($caso == 'descontos') {

	$update = mysql_query("UPDATE folha_cooperado SET desconto = '".$valor_banco."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
} if($caso == 'horas_trabalhadas') {

	$update = mysql_query("UPDATE folha_cooperado SET faltas = '".$valor."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
} if($caso == 'ajuda_custo') {

	$update = mysql_query("UPDATE folha_cooperado SET ajuda_custo = '".$valor_banco."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
}

echo json_encode(array('erro' => ($update) ? '1' : '0')); ?>