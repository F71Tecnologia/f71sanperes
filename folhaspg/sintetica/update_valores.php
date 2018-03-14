<?php 
include('../../conn.php');
include('../../classes/valor_proporcional.php');

$Trab = new proporcional();

$caso  = $_REQUEST['caso'];
$valor = $_REQUEST['valor'];
$id_folha_participante = $_REQUEST['id_folha_participante'];

$query_participante = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha_pro = '".$id_folha_participante."'");
$row_participante   = mysql_fetch_assoc($query_participante);

$autonomo = $row_participante['id_autonomo'];

$qr_folha    = mysql_query("SELECT * FROM folhas WHERE id_folha = '$row_participante[id_folha]' AND status = '2'");
$row_folha   = mysql_fetch_assoc($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];

$valor_banco = str_replace(',','.',str_replace('.','',$valor));

include('calculos_folha.php');

$base_ajax = $salario;

if($caso == 'rendimentos') {

	$liquido_ajax = $salario + $valor_banco - $row_participante['desconto'];
	$update       = mysql_query("UPDATE folha_autonomo SET adicional = '".$valor_banco."', salario_liq = '".$liquido_ajax."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
} if($caso == 'descontos') {

	$liquido_ajax = $salario + $row_participante['adicional'] - $valor_banco;
	$update       = mysql_query("UPDATE folha_autonomo SET desconto = '".$valor_banco."', salario_liq = '".$liquido_ajax."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
} if($caso == 'faltas') {

	$Trab        -> calculo_proporcional($salario_limpo, ($dias-$valor));
	$base_ajax    = $Trab -> valor_proporcional;
	$liquido_ajax = $base_ajax + $row_participante['adicional'] - $row_participante['desconto'];
	$update       = mysql_query("UPDATE folha_autonomo SET faltas = '".$valor."', salario = '".$base_ajax."', salario_liq = '".$liquido_ajax."' WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1");
	
}

$dados_finais = array('base' => number_format($base_ajax,2,',','.'), 'liquido' => number_format($liquido_ajax,2,',','.'), 'erro' => ($update) ? '1' : '0');
$dados_finais = json_encode($dados_finais);

echo $dados_finais; ?>