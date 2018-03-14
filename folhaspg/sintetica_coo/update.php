<?php
// Incluindo Arquivos
require('../../conn.php');
include('../../classes/formato_valor.php');
include('../../funcoes.php');

// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}

// Id e Região da Folha
list($folha,$regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Consulta da Folha
$qr_folha  = mysql_query("SELECT * FROM folhas WHERE id_folha = '$folha' AND status = '2'");
$row_folha = mysql_fetch_array($qr_folha);

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '2' ORDER BY nome ASC");
$total_participantes = mysql_num_rows($qr_participantes);
$optRegiao = getRegioes();

// Início do Loop dos Participantes da Folha
while($row_participante = mysql_fetch_array($qr_participantes)) {
	
	$ids_update[] = $row_participante['id_folha_pro'];
	
	// Totalizadores
	$salario_total     += $row_participante['salario'];
	$rendimentos_total += $row_participante['adicional'];
	$descontos_total   += $row_participante['desconto'];
	$liquido_total     += $row_participante['salario_liq'];

// Fim do Loop de Participantes
}

// Criando Update da Folha
$ids_update = implode(',',$ids_update);

mysql_query("UPDATE folhas SET participantes = '".$total_participantes."', total_bruto = '".formato_banco($salario_total)."', rendimentos = '".formato_banco($rendimentos_total)."', descontos = '".formato_banco($descontos_total)."', total_liqui = '".formato_banco($liquido_total)."', status = '3' WHERE id_folha = '".$folha."' LIMIT 1");

mysql_query("UPDATE folha_autonomo SET status = '3' WHERE id_folha_pro IN(".$ids_update.") LIMIT ".$total_participantes."");

header('Location: ../folha.php?id=9&enc='.str_replace('+', '--', encrypt("$regiao&$regiao")).'&tela=1');
exit();
?>