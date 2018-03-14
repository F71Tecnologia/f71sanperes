<?php
include('../../conn.php');

$id_rescisao   = $_POST['id'];
$coluna 	   = $_POST['coluna'];
$valor  	   = $_POST['valor'];
$tipo		   = $_POST['tipo'];
$total_tipo    = $_POST['total_tipo'];
$total_liquido = $_POST['total_liquido'];

if($coluna) {

	$qr_valor_anterior = mysql_query("SELECT {$coluna} FROM rh_recisao WHERE id_recisao = '{$id_rescisao}'");
	$valor_anterior = mysql_result($qr_valor_anterior, 0);

	if($valor_anterior != $valor) {
		
		if($tipo == 'credito' or $tipo == 'debito') {
		
			$valor = trataValor($valor);
			
			switch($tipo) {
				case 'credito':
					mysql_query("UPDATE rh_recisao SET total_rendimento = '{$total_tipo}' WHERE id_recisao = '{$id_rescisao}' LIMIT 1");
				break;
				case 'debito':
					mysql_query("UPDATE rh_recisao SET total_deducao = '{$total_tipo}' WHERE id_recisao = '{$id_rescisao}' LIMIT 1");
				break;
			}
		
			mysql_query("UPDATE rh_recisao SET total_liquido = '{$total_liquido}' WHERE id_recisao = '{$id_rescisao}' LIMIT 1");
			
		}
		
		mysql_query("UPDATE rh_recisao SET {$coluna} = '{$valor}' WHERE id_recisao = '{$id_rescisao}' LIMIT 1");
		
		insereLog($id_rescisao, $coluna, $valor_anterior, $valor);
		
	}
	
}

function insereLog($id_rescisao, $coluna, $valor_anterior, $valor) {
	mysql_query("INSERT INTO rh_rescisao_edicao_log (id_rescisao, coluna, valor_anterior, valor_novo, id_funcionario, data) VALUES ('{$id_rescisao}', '{$coluna}', '{$valor_anterior}', '{$valor}', '".$_COOKIE['logado']."', NOW())");
}

function trataValor($valor) {
	$valor = str_replace(' ', '', $valor);
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
	return $valor;
}
