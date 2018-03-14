<?php
// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

// Incluindo Arquivos
require('../conn.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');

// Id da Folha
list($nulo,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// Consulta da Folha
$qr_folha    = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
									  date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
									  date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
							     FROM folhas WHERE id_folha = '$folha' AND status = '2'");
$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto    = mysql_result($qr_projeto, 0, 0);

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' AND status = '2' ORDER BY nome ASC");
$total_participantes = mysql_num_rows($qr_participantes);

// Definindo Mês da Folha
$meses_pt = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

if(!empty($decimo_terceiro)) {
	switch($tipo_terceiro) {
		case 1:
		$mes_folha = '13&ordm; Primeira parcela';
		break;
		case 2:
		$mes_folha = '13&ordm; Segunda parcela';
		break;
		case 3:
		$mes_folha = '13&ordm; Integral';
		break;
	}
} else {
	$mes_folha = "$meses_pt[$mes_int] / $ano";
}

// Encriptografando Links
$link_voltar     = 'folha.php?id=9&enc='.str_replace('+', '--', encrypt("$regiao"));
$link_add_remove = 'folha2.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha&2"));
$link_finaliza   = 'sintetica/update.php?enc='.str_replace('+', '--', encrypt("$folha&$regiao"));

// Definindo Usuários para Finalizar a Folha
$acesso_finalizacao = array('9','33','68','77','5');

// Bloqueio Administração
echo bloqueio_administracao($regiao);
?>