<?php
// Criando Update da Folha
if($_GET['update'] == true) {
	$ids_update = implode(',',$ids_update);
	mysql_query("UPDATE folhas SET participantes = '".$total_participantes."', total_bruto = '".formato_banco($salario_total)."', rendimentos = '".formato_banco($rendimentos_total)."', descontos = '".formato_banco($descontos_total)."', total_liqui = '".formato_banco($liquido_total)."', status = '3' WHERE id_folha = '".$folha."' LIMIT 1");
	mysql_query("UPDATE folha_autonomo SET status = '3' WHERE id_folha_pro IN(".$ids_update.") LIMIT ".$total_participantes."");
	header('Location: folha.php?id=9&enc='.str_replace('+', '--', encrypt("$regiao&$regiao")).'&tela=1');
	exit();
}
?>