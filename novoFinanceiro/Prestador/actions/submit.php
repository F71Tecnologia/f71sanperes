<?php 

include "../../../conn.php";


$id_prestador = $_REQUEST['id_prestador'];
$id_regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$id_banco = $_REQUEST['banco'];
$id_user = $_COOKIE['logado'];
$nome = $_REQUEST['nome'];
$especifica = '';
$tipo = $_REQUEST['tipo'];
$valor = $_REQUEST['valor'];
$data_vencimento = implode('-',array_reverse(explode('/',$_REQUEST['vencimento'])));
$saidas = explode(',',$_REQUEST['saidas']);
$taxa = $_REQUEST['taxa'];
$cod_taxa = $_REQUEST['cod_taxa'];
$tipo_doc = $_REQUEST['tipo_doc'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$id_pagamento = $_REQUEST['id_pagamento'];


switch ($tipo_doc){
	case 1:
		$coluna_tipo = 'darf_irrf';
		break;
	case 2:
		$coluna_tipo = 'darf_csll';
		break;
	case 3:
		$coluna_tipo = 'gps';
		break;
	case 4:
		$coluna_tipo = 'irrf';
		break;
	default:
		$coluna_tipo = NULL;
		break;
}

if(empty($coluna_tipo)){
	echo json_encode(array('erro' => '1'));
	exit;
}



$sql = "INSERT INTO saida (
	id_regiao,
	id_projeto,
	id_banco,
	id_user,
	nome,
	id_nome,
	especifica,
	tipo,
	adicional,
	valor,
	data_proc,
	data_vencimento,
	comprovante,
	status
) VALUES (
	'$id_regiao',
	'$id_projeto',
	'$id_banco',
	'$id_user',
	'$nome',
	'0',
	'$especifica',
	'$tipo',
	'0,00',
	'$valor',
	CURDATE(),
	'$data_vencimento',
	'2',
	'1'
)";

$qr = mysql_query($sql);
$id_saida = mysql_insert_id();

// ENVIANDO ARQUIVOS ANEXADOS
$diretorio = $_SERVER['DOCUMENT_ROOT'] . '/intranet/comprovantes_tmp/' ;
$diretorio_destino = $_SERVER['DOCUMENT_ROOT'] . '/intranet/comprovantes/';
$anexos = (empty($_REQUEST['campo_anexo'])) ? array() : $_REQUEST['campo_anexo']; 
foreach($anexos as $anexo){  
    
    $partes = explode('.',$anexo);
    
    mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida', '.{$partes[1]}');");
    
    $id_saida_files = mysql_insert_id();
    
    copy($diretorio.$anexo, $diretorio_destino.$id_saida_files.'.'.$id_saida.'.'.$partes[1]);

}


// INSERIANDO NA TABELA DE CONTROLE
if(empty($id_pagamento)){
	mysql_query("INSERT prestador_pagamento SET 
		id_prestador = '$id_prestador',
		mes  = '$mes',
		ano = '$ano',
		{$coluna_tipo} = '$valor',
		{$coluna_tipo}_taxa = '$taxa',
		{$coluna_tipo}_cod = '$cod_taxa', 
		{$coluna_tipo}_saida = '$id_saida',
		status_pagamaneto = '1'");
}else{
	mysql_query("UPDATE prestador_pagamento SET 
		{$coluna_tipo} = '$valor',
		{$coluna_tipo}_taxa = '$taxa',
		{$coluna_tipo}_cod = '$cod_taxa', 
		{$coluna_tipo}_saida = '$id_saida'
		WHERE id_pagamento = '{$id_pagamento}' LIMIT 1;");
}

// INSERINDO O AS SAIDAS REFERENTES
$ultimo_pg = mysql_insert_id();
foreach($saidas as $id_saida):
	mysql_query("INSERT prestador_saidas SET id_pagamento = '$ultimo_pg' , id_saida = '$id_saida'");
endforeach;;



echo ($qr) ? json_encode(array('erro' => '0')) : json_encode(array('erro' => mysql_error()));
?>