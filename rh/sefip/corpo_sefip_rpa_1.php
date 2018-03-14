<?php
include '../../wfunction.php';

$caracteres = array(".",",","-","_","{","}","[","]","(",")",";",":","\"","\'","/", "*", "&", "\\", "@", "$", "&");
//
//function RemoveCaracteres($variavel) {	
//	$variavel = str_replace("  ", " ", $variavel);
//	$variavel = str_replace("(", "", $variavel);
//	$variavel = str_replace(")", "", $variavel);
//	$variavel = str_replace("-", "", $variavel);
//	$variavel = str_replace("/", "", $variavel);
//	$variavel = str_replace(":", "", $variavel);
//	$variavel = str_replace(",", " ", $variavel);
//	$variavel = str_replace(".", "", $variavel);
//	$variavel = str_replace(";", "", $variavel);
//	$variavel = str_replace("\"", "", $variavel);
//	$variavel = str_replace("\'", "", $variavel);
//	return $variavel;        
//}
//
//function RemoveEspacos($variavel) {
//	$variavel = str_replace(" ", "", $variavel);	
//	return $variavel;
//}
//
//function RemoveLetras($variavel) {
//	$letras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
//    foreach($letras as $letra) {
//		$variavel = str_replace($letra, '', $variavel);
//	}
//	return $variavel;
//}
//
//function Valor($variavel) {
//	$variavel = str_replace(".", "", $variavel);
//	return $variavel;
//}
//
//function RemoveAcentos($str, $enc = "iso-8859-1") {
//$acentos = array(
//	'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
//	'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
//	'C' => '/&Ccedil;/',
//	'c' => '/&ccedil;/',
//	'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
//	'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
//	'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
//	'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
//	'N' => '/&Ntilde;/',
//	'n' => '/&ntilde;/',
//	'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
//	'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
//	'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
//	'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
//	'Y' => '/&Yacute;/',
//	'y' => '/&yacute;|&yuml;/',
//	'a.' => '/&ordf;/',
//	'o.' => '/&ordm;/'
//);
//   return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
//}

// Consulta da Empresa
if($row_master['id_master'] == 6) {
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '28' ") or die(mysql_error());
    $empresa 	= mysql_fetch_assoc($qr_empresa);
} else if($row_master['id_master'] != 6 AND $row_master['id_master'] != 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
    $empresa 	= mysql_fetch_assoc($qr_empresa);
}else if( $row_master['id_master'] == 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '38'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
    $empresa 	= mysql_fetch_assoc($qr_empresa);
}
$codigo_recolhimento = '115';

// CONDI��ES PARA D�CIMO TERCEITO
if($decimo_terceiro == 1){   
    $mes_competencia    = 13;
    $modalidade_arquivo = 1;
}else {  
    $mes_competencia    = $mes;
    $modalidade_arquivo = NULL;
}

// Buscando Ids de Sefips Anteriores
settype($ids_anteriores,'array');  
$qr_sefips_anteriores2 = mysql_query("SELECT B.id_clt 
                                    FROM sefip AS A
                                    INNER JOIN rh_folha_proc AS B ON (A.folha = B.id_folha)
                                    WHERE B.status = 3 AND A.regiao = $regiao
                                    GROUP BY B.id_clt;");
while($sefip_anterior2 = mysql_fetch_assoc($qr_sefips_anteriores2)){
    $ids_anteriores[] = $sefip_anterior2['id_clt'];
}
 
///////////////////////////////////////////////////
///////// Linha 1 (Registro Tipo 00) //////////////
///////////////////////////////////////////////////
$tipo_registro = '00';
$tipo_registro = sprintf("%02s",$tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

$brancos = NULL;
$brancos = sprintf("%51s", $brancos);
fwrite($arquivo, $brancos, 51);

$tipo_remessa = '1';
$tipo_remessa = sprintf("%01s", $tipo_remessa);
fwrite($arquivo, $tipo_remessa, 1);

$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

$inscricao_responsavel = RemoveCaracteres($empresa['cnpj']);
$inscricao_responsavel = str_replace('.','', $inscricao_responsavel);
$inscricao_responsavel = substr($inscricao_responsavel, 0, 14);
$inscricao_responsavel = sprintf("%014s", $inscricao_responsavel);
fwrite($arquivo, $inscricao_responsavel, 14);

$nome_responsavel = RemoveAcentos($empresa['razao']);
$nome_responsavel = regexCaracterIgualConsecutivo(exPersonalizada($nome_responsavel,$caracteres), 3);
$nome_responsavel = substr($nome_responsavel, 0, 30);
$nome_responsavel = sprintf("%-30s", $nome_responsavel);
fwrite($arquivo, $nome_responsavel, 30);

$nome_pessoa_contato = regexCaracterIgualConsecutivo(exPersonalizada(RemoveAcentos($empresa['responsavel']),$caracteres,array('[[:digit:]]')),3);
$nome_pessoa_contato = substr($nome_pessoa_contato, 0, 20);
$nome_pessoa_contato = sprintf("%-20s", $nome_pessoa_contato);
fwrite($arquivo, $nome_pessoa_contato, 20);

//$logradouro = RemoveEspacos($empresa['endereco']);
$endereco = explode('-', RemoveAcentos($empresa['endereco']));
$logradouro = substr(regexCaracterIgualConsecutivo(exPersonalizada($endereco[0],$caracteres),3), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

//$bairro = explode('-', RemoveAcentos($empresa['endereco']));
//$bairro = str_replace(' ', '', $endereco[1]);
$bairro = substr(regexCaracterIgualConsecutivo(exPersonalizada($endereco[1],$caracteres),3), 0, 20);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

$cep = RemoveCaracteres($empresa['cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

//$cidade = explode('-', RemoveAcentos($empresa['endereco']));
//$cidade = str_replace(' ', '', $cidade[2]);
$cidade = substr(regexCaracterIgualConsecutivo(exPersonalizada($endereco[2],$caracteres),3), 0, 20);
$cidade = sprintf("%-20s", $cidade);
fwrite($arquivo, $cidade, 20);

$unidade_federecao = sprintf("%02s", $empresa['uf']);
fwrite($arquivo, $unidade_federecao, 2);

$telefone_contato = RemoveCaracteres($empresa['tel']);
$telefone_contato = sprintf("%12s", $telefone_contato);
fwrite($arquivo, $telefone_contato, 12);

$endereco_internet_contato = $empresa['email'];
$endereco_internet_contato = sprintf("%-60s", $endereco_internet_contato);
fwrite($arquivo, $endereco_internet_contato, 60);

$competencia = $ano.$mes;
$competencia = sprintf("%06s",$competencia);
fwrite($arquivo, $competencia, 6);

//$codigo_recolhimento = $codigo_recolhimento;
$codigo_recolhimento = sprintf("%03s", $codigo_recolhimento);
fwrite($arquivo, $codigo_recolhimento, 3);

// Prazo até o 7º dia do mês seguinte a folha (1-Prazo 2-Atraso)
$data_prazo = date('Y-m-d', mktime('0','0','0', $mes + 1, 07, $ano));

if($data_comparacao > $data_prazo) {
	$indicador_recolhimento_fgts = 2;
} else {
	$indicador_recolhimento_fgts = 1;
}

if($decimo_terceiro == 1){    
    $indicador_recolhimento_fgts = '';
}
$indicador_recolhimento_fgts = sprintf("%1s", $indicador_recolhimento_fgts);
fwrite($arquivo, $indicador_recolhimento_fgts, 1);

//$modalidade_arquivo = $modalidade_arquivo;
$modalidade_arquivo = sprintf("%1s", $modalidade_arquivo);
fwrite($arquivo, $modalidade_arquivo, 1);

if($indicador_recolhimento_fgts == 2) {
    $data_recolhimento_fgts = $data;
} else {
    $data_recolhimento_fgts = NULL;
}

$data_recolhimento_fgts = sprintf("%8s", $data_recolhimento_fgts);
fwrite($arquivo, $data_recolhimento_fgts, 8);

// Prazo até o 7º dia do mês seguinte a folha (1-Prazo 2-Atraso)
$data_prazo = date('Y-m-d', mktime('0','0','0', $mes + 1, 10, $ano)); 

//if($decimo_terceiro == 1){ para compet�ncia 13, deve ser posterior a  20/12/AAAA  
//    $data_prazo = date('Y-m-d', mktime('0','0','0', 12, 20, $ano)); 
//}else{
//    $data_prazo = date('Y-m-d', mktime('0','0','0', $mes + 1, 10, $ano));
//}   

if($data_comparacao > $data_prazo) {
	$indicador_recolhimento = 2;
} else {
	$indicador_recolhimento = 1;
}

$indicador_recolhimento = sprintf("%1s", $indicador_recolhimento);
fwrite($arquivo, $indicador_recolhimento, 1);

if($indicador_recolhimento == 2) {
	$data_recolhimento = $data;
} else {
	$data_recolhimento = NULL;
}  

$data_recolhimento = sprintf("%8s", $data_recolhimento);
fwrite($arquivo, $data_recolhimento, 8);

$indice_recolhimento = NULL;
$indice_recolhimento = sprintf("%7s", $indice_recolhimento);
fwrite($arquivo, $indice_recolhimento, 7);

$tipo_inscricao_fornecedor = '1';
$tipo_inscricao_fornecedor = sprintf("%01s", $tipo_inscricao_fornecedor);
fwrite($arquivo, $tipo_inscricao_fornecedor, 1);

$inscricao_fornecedor = '27915735000100';  // por que usa o cnpj da NASAJON SISTEMAS LTDA 'AKI'   SABINO
$inscricao_fornecedor = sprintf("%14s", $inscricao_fornecedor);
fwrite($arquivo, $inscricao_fornecedor, 14);

$brancos = NULL;
$brancos = sprintf("%18s", $brancos);
fwrite($arquivo, $brancos, 18);

$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");
// Fim para Linha 1

// Consulta da Empresa
if($row_master['id_master'] == 6) {	
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' ");
    $empresa 	= mysql_fetch_assoc($qr_empresa);
//    $codigo_recolhimento = '115'; 
} else if($row_master['id_master'] != 6 AND $row_master['id_master'] != 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
    $empresa 	= mysql_fetch_assoc($qr_empresa);
//    $codigo_recolhimento = '115';
}else if( $row_master['id_master'] == 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '38'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
    $empresa 	= mysql_fetch_assoc($qr_empresa);
//    $codigo_recolhimento = '115';     
}
    $codigo_recolhimento = '115';
//$codigo_recolhimento j� foi declarado e atribuido o valor 115 na linha 76; Por que isso aqui novamente ?
///////////////////////////////////////////////////
///////// Linha 2 (Registro Tipo 10) //////////////
/////////////////////////////////////////////////// 
//if($parte_sefip == 2 or isset($sefip_folha)) {
$tipo_registro = '10';
$tipo_registro = sprintf("%02s",$tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

$inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
$inscricao_empresa = str_replace('.','', $inscricao_empresa);
$inscricao_empresa = substr($inscricao_empresa, 0, 14);
$inscricao_empresa = sprintf("%014s", $inscricao_empresa);
fwrite($arquivo, $inscricao_empresa, 14);

$zeros = NULL;
$zeros = sprintf("%036s", $zeros);
fwrite($arquivo, $zeros, 36);

$nome_empresa = RemoveAcentos($empresa['razao']);
$nome_empresa = regexCaracterIgualConsecutivo(exPersonalizada($nome_empresa,$caracteres),3);
$nome_empresa = substr($nome_empresa, 0, 30);
$nome_empresa = sprintf("%-40s", $nome_empresa);
fwrite($arquivo, $nome_empresa, 40);

//$logradouro = RemoveEspacos($empresa['endereco']);
$endereco = explode('-', RemoveAcentos($empresa['endereco']));
$logradouro = substr(regexCaracterIgualConsecutivo(exPersonalizada($endereco[0],$caracteres),3), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

//$bairro = explode('-', RemoveAcentos($empresa['endereco']));
//$bairro = str_replace(' ', '', $bairro[1]);
$bairro = substr(regexCaracterIgualConsecutivo(exPersonalizada($endereco[1],$caracteres),3), 0, 20);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

$cep = RemoveCaracteres($empresa['cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

/*
$cidade = explode('-', RemoveAcentos($empresa['endereco']));
$cidade = str_replace(' ', '', $cidade[2]);
*/
$cidade = $empresa['cidade'];
$cidade = substr(regexCaracterIgualConsecutivo(exPersonalizada($cidade,$caracteres),3), 0, 20);
$cidade = sprintf("%-20s", RemoveAcentos($cidade));
fwrite($arquivo, $cidade, 20);

/*
$unidade_federecao = explode('-', $empresa['endereco']);
$unidade_federecao = str_replace(' ', '', $unidade_federecao[3]);
*/
$unidade_federecao = $empresa['uf'];
$unidade_federecao = sprintf("%02s", $unidade_federecao);
fwrite($arquivo, $unidade_federecao, 2);

$telefone_contato = RemoveCaracteres($empresa['tel']);
$telefone_contato = sprintf("%12s", $telefone_contato);
fwrite($arquivo, $telefone_contato, 12);

$indicador_alteracao_endereco = 'n';
$indicador_alteracao_endereco = sprintf("%1s", $indicador_alteracao_endereco);
fwrite($arquivo, $indicador_alteracao_endereco, 1);

$cnae = RemoveCaracteres($empresa['cnae']);
$cnae = sprintf("%7s", $cnae);
fwrite($arquivo, $cnae, 7);

$indicador_alteracao_cnae = 'n';
$indicador_alteracao_cnae = sprintf("%1s", $indicador_alteracao_cnae);
fwrite($arquivo, $indicador_alteracao_cnae, 1);

$aliquota_rat = '10';
$aliquota_rat = sprintf("%2s", $aliquota_rat);
fwrite($arquivo, $aliquota_rat, 2);

$codigo_centralizacao = '0';
$codigo_centralizacao = sprintf("%1s", $codigo_centralizacao);
fwrite($arquivo, $codigo_centralizacao, 1);

$SIMPLES = '1';
$SIMPLES = sprintf("%1s", $SIMPLES);
fwrite($arquivo, $SIMPLES, 1);

$FPAS = '515';
$FPAS = sprintf("%3s", $FPAS);
fwrite($arquivo, $FPAS, 3);

$codigo_outras_entidades = '0115';
$codigo_outras_entidades = sprintf("%04s", $codigo_outras_entidades);
fwrite($arquivo, $codigo_outras_entidades, 4);

$codigo_pagamento_GPS = '2100';
$codigo_pagamento_GPS = sprintf("%4s", $codigo_pagamento_GPS);
fwrite($arquivo, $codigo_pagamento_GPS, 4);

$percentual_isencao_filantropia = NULL;
$percentual_isencao_filantropia = sprintf("%5s", $percentual_isencao_filantropia);
fwrite($arquivo, $percentual_isencao_filantropia, 5);


$familia = @mysql_result(mysql_query("SELECT SUM(a5022) as total_familia FROM rh_folha_proc WHERE id_folha_proc IN($ids)"),0);
// $maternidade = @mysql_result(mysql_query("SELECT SUM(a6005) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36'"),0);
//Salario maternidade
$qr_folha_2 = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '{$_GET['folha']}'");	
$row_folha2 = mysql_fetch_assoc($qr_folha_2);
if(isset($_GET['folha'])) {    
//    $familia = @mysql_result(mysql_query("SELECT SUM(a5022) as total_familia FROM rh_folha_proc WHERE id_folha_proc IN($ids)"),0);       
//    //Salario maternidade
//    $qr_folha_2 = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$_GET[folha]'");
//    $row_folha2 = mysql_fetch_assoc($qr_folha_2);

    if($row_folha2['data_proc'] > date('2010-06-30')) {
        $maternidade = @mysql_result(mysql_query("SELECT SUM(a6005) FROM rh_folha_proc WHERE id_folha_proc IN($ids)"),0);
    } else {
        $maternidade = @mysql_result(mysql_query("SELECT SUM(salbase) FROM rh_folha_proc WHERE id_folha_proc IN($ids)  AND status_clt = 50"),0);
    }
} else {
//    $familia = @mysql_result(mysql_query("SELECT SUM(a5022) as total_familia FROM rh_folha_proc WHERE id_folha_proc IN($ids)"),0);
//    // $maternidade = @mysql_result(mysql_query("SELECT SUM(a6005) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36'"),0);
//     //Salario maternidade
//    $qr_folha_2 = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$_GET[folha]'");	
//    $row_folha2 = mysql_fetch_assoc($qr_folha_2);

    if($row_folha2['data_proc'] > date('2010-06-30')) {
        $maternidade = @mysql_result(mysql_query("SELECT SUM(a6005) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36'  AND id_folha_proc IN($ids)  	 "),0);
    } else {
        ////Alterado de sal_base para sallimpo_real
        $maternidade = @mysql_result(mysql_query("SELECT SUM(sallimpo_real) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36'  AND status_clt = 50 AND id_folha_proc IN($ids)"),0);
    }
}

$familia     = number_format($familia,2,'','');
$maternidade = number_format($maternidade,2,'','');

if($decimo_terceiro == 1){   
    $familia     = NULL;
    $maternidade = NULL;
} 

$salario_familia = $familia;
$salario_familia = Valor($salario_familia);
$salario_familia = sprintf("%015s", $salario_familia);
fwrite($arquivo, $salario_familia, 15);

$salario_maternidade = $maternidade;
$salario_maternidade = sprintf("%015s", $salario_maternidade);
fwrite($arquivo, $salario_maternidade, 15);

$contrib_desc_empregado = NULL;
$contrib_desc_empregado = sprintf("%015s", $contrib_desc_empregado);
fwrite($arquivo, $contrib_desc_empregado, 15);

$indicador_valor_negativo = NULL;
$indicador_valor_negativo = sprintf("%01s", $indicador_valor_negativo);
fwrite($arquivo, $indicador_valor_negativo, 1);

$valor_devido_previdencia = NULL;
$valor_devido_previdencia = sprintf("%014s", $valor_devido_previdencia);
fwrite($arquivo, $valor_devido_previdencia, 14);

$banco = NULL;
$banco = sprintf("%3s", $banco);
fwrite($arquivo, $banco, 3);

$agencia = NULL;
$agencia = sprintf("%4s", $agencia);
fwrite($arquivo, $agencia, 4);

$conta = NULL;
$conta = sprintf("%9s", $conta);
fwrite($arquivo, $conta, 9);

$zeros = NULL;
$zeros = sprintf("%045s", $zeros);
fwrite($arquivo, $zeros, 45);

$brancos = NULL;
$brancos = sprintf("%4s", $brancos);
fwrite($arquivo, $brancos, 4);

$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1); 

fwrite($arquivo, "\r\n");
//}
// Fim para Linha 2

///////////////////////////////////////////////////
///////// Linha 3 (Registro Tipo 12) //////////////
/////////////////////////////////////////////////// 
//if($parte_sefip == 3 or isset($sefip_folha)) {
$tipo_registro = '12';
$tipo_registro = sprintf("%02s",$tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

$inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
$inscricao_empresa = str_replace('.','', $inscricao_empresa);
$inscricao_empresa = substr($inscricao_empresa, 0, 14);
$inscricao_empresa = sprintf("%014s", $inscricao_empresa);
fwrite($arquivo, $inscricao_empresa, 14);

$zeros = NULL;
$zeros = sprintf("%036s", $zeros);
fwrite($arquivo, $zeros, 36);

$deducao_13salario_licensa_maternidade = NULL;
$deducao_13salario_licensa_maternidade = sprintf("%015s", $deducao_13salario_licensa_maternidade);
fwrite($arquivo, $deducao_13salario_licensa_maternidade, 15);

$receita_evento_desportivo = NULL;
$receita_evento_desportivo = sprintf("%015s", $receita_evento_desportivo);
fwrite($arquivo, $receita_evento_desportivo, 15);

$indicativo_origem_receita = NULL;
$indicativo_origem_receita = sprintf("%1s", $indicativo_origem_receita);
fwrite($arquivo, $indicativo_origem_receita, 1);

$comercializacao_producao_fisica = NULL;
$comercializacao_producao_fisica = sprintf("%015s", $comercializacao_producao_fisica);
fwrite($arquivo, $comercializacao_producao_fisica, 15);

$comercializacao_producao_juridica = NULL;
$comercializacao_producao_juridica = sprintf("%015s", $comercializacao_producao_juridica);
fwrite($arquivo, $comercializacao_producao_juridica, 15);

$outras_informacoes_processo = NULL;
$outras_informacoes_processo = sprintf("%11s", $outras_informacoes_processo);
fwrite($arquivo, $outras_informacoes_processo, 11);

$outras_informacoes_processo_ano = NULL;
$outras_informacoes_processo_ano = sprintf("%4s", $outras_informacoes_processo_ano);
fwrite($arquivo, $outras_informacoes_processo_ano, 4);

$outras_informacoes_vara = NULL;
$outras_informacoes_vara = sprintf("%5s", $outras_informacoes_vara);
fwrite($arquivo, $outras_informacoes_vara, 5);

$outras_informacoes_periodo_inicio = NULL;
$outras_informacoes_periodo_inicio = sprintf("%6s", $outras_informacoes_periodo_inicio);
fwrite($arquivo, $outras_informacoes_periodo_inicio, 6);

$outras_informacoes_periodo_fim = NULL;
$outras_informacoes_periodo_fim = sprintf("%6s", $outras_informacoes_periodo_fim);
fwrite($arquivo, $outras_informacoes_periodo_fim, 6);

$compensacao_valor_corrigido = NULL;
$compensacao_valor_corrigido = sprintf("%015s", $compensacao_valor_corrigido);
fwrite($arquivo, $compensacao_valor_corrigido, 15);

$compensacao_periodo_inicio = NULL;
$compensacao_periodo_inicio = sprintf("%6s", $compensacao_periodo_inicio);
fwrite($arquivo, $compensacao_periodo_inicio, 6);

$compensacao_periodo_fim = NULL;
$compensacao_periodo_fim = sprintf("%6s", $compensacao_periodo_fim);
fwrite($arquivo, $compensacao_periodo_fim, 6);

$recolhimento_competencia_anteriores_inss = NULL;
$recolhimento_competencia_anteriores_inss = sprintf("%015s", $recolhimento_competencia_anteriores_inss);
fwrite($arquivo, $recolhimento_competencia_anteriores_inss, 15);

$recolhimento_competencia_anteriores_outros = NULL;
$recolhimento_competencia_anteriores_outros = sprintf("%015s", $recolhimento_competencia_anteriores_outros);
fwrite($arquivo, $recolhimento_competencia_anteriores_outros, 15);

$recolhimento_competencia_anteriores_comercializacao = NULL;
$recolhimento_competencia_anteriores_comercializacao = sprintf("%015s", $recolhimento_competencia_anteriores_comercializacao);
fwrite($arquivo, $recolhimento_competencia_anteriores_comercializacao, 15);

$recolhimento_competencia_anteriores_comercializacao = NULL;
$recolhimento_competencia_anteriores_comercializacao = sprintf("%015s", $recolhimento_competencia_anteriores_comercializacao);
fwrite($arquivo, $recolhimento_competencia_anteriores_comercializacao, 15);

$recolhimento_competencia_anteriores_receita = NULL;
$recolhimento_competencia_anteriores_receita = sprintf("%015s", $recolhimento_competencia_anteriores_receita);
fwrite($arquivo, $recolhimento_competencia_anteriores_receita, 15);

$parcelamento_fgts = NULL;
$parcelamento_fgts = sprintf("%015s", $parcelamento_fgts);
fwrite($arquivo, $parcelamento_fgts, 15);

$parcelamento_fgts = NULL;
$parcelamento_fgts = sprintf("%015s", $parcelamento_fgts);
fwrite($arquivo, $parcelamento_fgts, 15);

$parcelamento_fgts = NULL;
$parcelamento_fgts = sprintf("%015s", $parcelamento_fgts);
fwrite($arquivo, $parcelamento_fgts, 15);

$valores_pagos_cooperativas = NULL;
$valores_pagos_cooperativas = sprintf("%015s", $valores_pagos_cooperativas);
fwrite($arquivo, $valores_pagos_cooperativas, 15);

$implementacao_futura = NULL;
$implementacao_futura = sprintf("%045s", $implementacao_futura);
fwrite($arquivo, $implementacao_futura, 45);

$brancos = NULL;
$brancos = sprintf("%6s", $brancos);
fwrite($arquivo, $brancos, 6);

$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");
//}
// Fim para Linha 3
if($mes != 13){
///////////////////////////////////////////////////
///////// Linha 4 (Registro Tipo 13) //////////////
/////////////////////////////////////////////////// 
//if($parte_sefip == 4 or isset($sefip_folha)) {

$ids_anteriores_sql = implode(',',$ids_anteriores);

// Consultas para buscar participantes da folha
$qr_folha = mysql_query("SELECT rh_folha_proc.id_clt, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos 
                        FROM rh_folha_proc 
                        INNER JOIN rh_clt 
                        ON rh_folha_proc.id_clt = rh_clt.id_clt 
                        WHERE rh_folha_proc.id_folha_proc IN($ids) 
                        AND rh_folha_proc.status = '3'
                        AND rh_folha_proc.id_folha_proc IN($ids_anteriores_sql)
                        AND rh_folha_proc.id_clt != 4237 AND rh_folha_proc.id_clt != 4071 AND rh_folha_proc.id_clt != 4265 AND rh_folha_proc.id_clt != 4365 ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC");

// Loop dos participantes
while($folha = @mysql_fetch_assoc($qr_folha)) {
    // Consulta de alteração cadastral
    $qr_alteracao = mysql_query("SELECT * FROM log WHERE sefip = '1' AND sefip_id = '$folha[id_clt]' AND sefip_ano = '$ano' AND sefip_mes = '$mes' AND sefip_codigo != '' ORDER BY id_log DESC LIMIT 0,3");
    $numero_alteracao = mysql_num_rows($qr_alteracao);
    // Verificando se existiu alteração cadastral
    if(!empty($numero_alteracao)) {
        while($alteracao = mysql_fetch_assoc($qr_alteracao)) {
            // Consulta para dados dos participantes
            $qr_empregado = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$folha[id_clt]' AND id_regiao = '$folha[id_regiao]'");
            $empregado = mysql_fetch_assoc($qr_empregado);
            
            // Inicio
            $tipo_registro = '13';
            $tipo_registro = sprintf("%02s",$tipo_registro);
            fwrite($arquivo, $tipo_registro, 2);

            $tipo_inscricao = '1';
            $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
            fwrite($arquivo, $tipo_inscricao, 1);

            $inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
            $inscricao_empresa = str_replace('.','', $inscricao_empresa);
            $inscricao_empresa = substr($inscricao_empresa, 0, 14);
            $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
            fwrite($arquivo, $inscricao_empresa, 14);

            $zeros = NULL;
            $zeros = sprintf("%036s", $zeros);
            fwrite($arquivo, $zeros, 36);

            $pis_pasep_ci = RemoveCaracteres($empregado['pis']);
            $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
            fwrite($arquivo, $pis_pasep_ci, 11);

            $data_admissao = implode('', array_reverse(explode('-', $empregado['data_entrada'])));
            $data_admissao = sprintf("%08s", $data_admissao);
            fwrite($arquivo, $data_admissao, 8);

            $categoria_trabalhador = '01';
            $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
            fwrite($arquivo, $categoria_trabalhador, 2);

            $matricula_trabalhador = $empregado['id_clt'];
            $matricula_trabalhador = sprintf("%11s", $matricula_trabalhador);
            fwrite($arquivo, $matricula_trabalhador, 11);

            $numero_ctps = RemoveCaracteres($empregado['campo1']);
            $numero_ctps = RemoveLetras($numero_ctps);
            $numero_ctps = sprintf("%07s", $numero_ctps);
            fwrite($arquivo, $numero_ctps, 7);

            $serie_ctps = RemoveCaracteres(trim($empregado['serie_ctps']));
            $serie_ctps = RemoveLetras($serie_ctps);
            $serie_ctps = sprintf("%05s", $serie_ctps);
            fwrite($arquivo, $serie_ctps, 5);

            $nome_trabalhador = RemoveAcentos($empregado['nome']);
            $nome_trabalhador = regexCaracterIgualConsecutivo(exPersonalizada($nome_trabalhador, $caracteres, array('[[:digit:]]')),3);
            $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
            fwrite($arquivo, $nome_trabalhador, 70);

            $codigo_empresa_CAIXA = '';
            $codigo_empresa_CAIXA = sprintf("%14s", $codigo_empresa_CAIXA);
            fwrite($arquivo, $codigo_empresa_CAIXA, 14);

            $codigo_trabalhador_CAIXA = '';
            $codigo_trabalhador_CAIXA = sprintf("%11s", $codigo_trabalhador_CAIXA);
            fwrite($arquivo, $codigo_trabalhador_CAIXA, 11);

            $codigo_alteracao_cadastral = $alteracao['sefip_codigo'];
            $codigo_alteracao_cadastral = sprintf("%3s", $codigo_alteracao_cadastral);
            fwrite($arquivo, $codigo_alteracao_cadastral, 3);

            $novo_conteudo_campo = RemoveAcentos($alteracao['sefip_valor']);
            $novo_conteudo_campo = RemoveCaracteres($novo_conteudo_campo);
            $novo_conteudo_campo = sprintf("%70s", $novo_conteudo_campo);
            fwrite($arquivo, $novo_conteudo_campo, 70);

            $brancos = NULL;
            $brancos = sprintf("%94s", $brancos);
            fwrite($arquivo, $brancos, 94);

            $final_linha = '*';
            $final_linha = sprintf("%01s", $final_linha);
            fwrite($arquivo, $final_linha, 1);

            fwrite($arquivo, "\r\n");
            // Fim
            // Fim do Loop da alteração cadastral
        }
    }
    // Resetando os valores para o próximo loop
    unset($qr_empregado);
    unset($empregado);
    // Fim do Loop dos participantes
}
// Resetando todos os valores
unset($qr_folha_pai);
unset($folha_pai);
unset($qr_folha);
unset($folha);
unset($qr_alteracao);
unset($numero_alteracao);
unset($alteracao);
//}
// Fim para Linha 4


    ///////////////////////////////////////////////////
    ///////// Linha 5 (Registro Tipo 14) //////////////
    /////////////////////////////////////////////////// 
    //if($parte_sefip == 5 or isset($sefip_folha)) {
    // Consulta para buscar participantes da folha
    $qr_folha = mysql_query("SELECT rh_folha_proc.id_clt, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos 
                            FROM rh_folha_proc 
                            INNER JOIN rh_clt 
                            ON rh_folha_proc.id_clt = rh_clt.id_clt 
                            WHERE rh_folha_proc.id_folha_proc IN($ids) ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC") or die(mysql_error());

    // Loop dos participantes
    while($folha = mysql_fetch_assoc($qr_folha)) {
    // Verificando se ele já foi incluso
        if(!in_array($folha['id_clt'],$ids_anteriores)) {
            // Consulta de alteração de endereço
            $qr_alteracao = mysql_query("SELECT * FROM log WHERE sefip = '1' AND sefip_id = '{$folha['id_clt']}' AND sefip_ano = '$ano' AND sefip_mes = '$mes' AND sefip_codigo = ''");
            $numero_alteracao = mysql_num_rows($qr_alteracao);

        // Verificando se o participante não está incluso no Sefip OU se existiu alteração de endereço do participante
            if(@!in_array($folha['id_clt'], $participantes) or !empty($numero_alteracao)) {
                // Consulta para dados dos participantes
                $qr_empregado = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$folha[id_clt]' AND id_regiao = '$folha[id_regiao]'");
                $empregado = mysql_fetch_assoc($qr_empregado);

                // Inicio
                $tipo_registro = '14';
                $tipo_registro = sprintf("%02s",$tipo_registro);
                fwrite($arquivo, $tipo_registro, 2);

                $tipo_inscricao = '1';
                $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
                fwrite($arquivo, $tipo_inscricao, 1);

                $inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
                $inscricao_empresa = str_replace('.','', $inscricao_empresa);
                $inscricao_empresa = substr($inscricao_empresa, 0, 14);
                $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
                fwrite($arquivo, $inscricao_empresa, 14);

                $zeros = NULL;
                $zeros = sprintf("%036s", $zeros);
                fwrite($arquivo, $zeros, 36);

                $pis_pasep_ci = RemoveCaracteres($empregado['pis']);
                $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
                fwrite($arquivo, $pis_pasep_ci, 11);

                $data_admissao = implode('', array_reverse(explode('-', $empregado['data_entrada'])));
                $data_admissao = sprintf("%08s", $data_admissao);
                fwrite($arquivo, $data_admissao, 8);

                $categoria_trabalhador = '01';
                $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
                fwrite($arquivo, $categoria_trabalhador, 2);

                $nome_trabalhador = RemoveAcentos($empregado['nome']);
                $nome_trabalhador = regexCaracterIgualConsecutivo(exPersonalizada($nome_trabalhador, $caracteres, array('[[:digit:]]')),3);
                $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
                fwrite($arquivo, $nome_trabalhador, 70);

                $numero_ctps = RemoveCaracteres(trim($empregado['campo1']));
                $numero_ctps = RemoveLetras($numero_ctps);
                $numero_ctps = sprintf("%07s", $numero_ctps);
                fwrite($arquivo, $numero_ctps, 7);

                $serie_ctps = RemoveCaracteres(trim($empregado['serie_ctps']));
                $serie_ctps = RemoveLetras($serie_ctps);
                $serie_ctps = sprintf("%05s", $serie_ctps);
                fwrite($arquivo, $serie_ctps, 5);

                $logradouro = RemoveAcentos($empregado['endereco']);
                $logradouro = regexCaracterIgualConsecutivo(exPersonalizada($logradouro,$caracteres),3);
                $logradouro = sprintf("%-50s", $logradouro);
                fwrite($arquivo, $logradouro, 50);

                $bairro = RemoveAcentos($empregado['bairro']);
                $bairro = regexCaracterIgualConsecutivo(exPersonalizada($bairro,$caracteres),3);
                $bairro = sprintf("%-20s", $bairro);
                fwrite($arquivo, $bairro, 20);

                $cep = RemoveCaracteres($empregado['cep']);
                $cep = substr($cep, 0, 8);
                $cep = sprintf("%08s", $cep);
                fwrite($arquivo, $cep, 8);

                $cidade = RemoveAcentos($empregado['cidade']);
                $cidade = regexCaracterIgualConsecutivo(exPersonalizada($cidade,$caracteres),3);
                $cidade = sprintf("%-20s", $cidade);
                fwrite($arquivo, $cidade, 20);

                $unidade_federecao = $empregado['uf'];
                $unidade_federecao = sprintf("%02s", $unidade_federecao);
                fwrite($arquivo, $unidade_federecao, 2);

                $brancos = NULL;
                $brancos = sprintf("%103s", $brancos);
                fwrite($arquivo, $brancos, 103);

                $final_linha = '*';
                $final_linha = sprintf("%01s", $final_linha);
                fwrite($arquivo, $final_linha, 1);

                fwrite($arquivo, "\r\n");
                // Fim
            // Fim da verificação de alteração cadastral
            }
        // Resetando os valores para o próximo loop
            unset($qr_empregado);
            unset($empregado);
        // Fim da Verificação de inclusão
        }
    // Fim do Loop dos participantes
    }
}
// Resetando todos os valores
unset($qr_folha_pai);
unset($folha_pai);
unset($qr_folha);
unset($folha);
unset($qr_alteracao);
unset($numero_alteracao);
//}
// Fim para Linha 5

/////REGISTRO 20

    ///////////////////////////////////////////////////
    /////// Linha 6 e 7 (Registro Tipo 30 e 32) ///////
    /////////////////////////////////////////////////// 
//if($parte_sefip == 6 or isset($sefip_folha)) {
    ///////////////////////////////////////////////////
    /////////// Linha 6 (Registro Tipo 30 ) ///////////
    /////////////////////////////////////////////////// 

//ALTERADO DIA 09/08/2011 ADICIONADO O CAMPO 'rh_folha_proc.folha_proc_desconto_outra_empresa' NO SELECT PARA MOSTRAR NO REGISTRO 'valor_descontado_segurado'

// Consultas para buscar participantes da folha 
$qr_folha = mysql_query("SELECT A.id_clt as id_trab ,C.nome,C.pis,B.mes,B.ano, '2' as tipo_contratacao,C.data_entrada, '01' as categoria
FROM rh_folha_proc AS A
INNER JOIN rh_folha as B ON B.id_folha = A.id_folha
INNER JOIN rh_clt as C ON A.id_clt = C.id_clt
WHERE A.id_folha_proc IN($ids)
    
UNION

SELECT A.id_autonomo as id_trab, B.nome,B.pis,A.mes_competencia as mes,A.ano_competencia as ano,B.tipo_contratacao, '' as data_entrada,'13' as categoria
FROM rpa_autonomo as A
INNER JOIN autonomo as B ON A.id_autonomo = B.id_autonomo
INNER JOIN rpa_saida_assoc as C ON C.id_rpa = A.id_rpa
INNER JOIN saida as D ON D.id_saida = C.id_saida 
WHERE B.id_regiao = $regiao AND B.id_projeto = $projeto AND B.status_reg = 1 
AND MONTH(A.data_geracao) = $mes AND YEAR(A.data_geracao) = $ano AND C.tipo_vinculo = 1	 AND C.tipo_vinculo = 1	

ORDER BY REPLACE(REPLACE(pis,'.',''),'-',''),data_entrada,categoria ASC" 
);

$numero_folha = mysql_num_rows($qr_folha);

// Loop dos participantes 
while($folha = mysql_fetch_assoc($qr_folha)) {   
    if($folha['tipo_contratacao'] == 2) {

        $qr_folha_proc = mysql_query("SELECT A.id_clt, B.id_curso, C.cbo_codigo,B.pis, B.data_entrada, B.nome, B.campo1,B.serie_ctps,B.data_nasci, A.data_proc, A.status_clt,A.a6005,   A.sallimpo_real,
        A.salbase,A.ids_movimentos,A.status_clt, D.tipo_terceiro, A.desconto_inss, A.folha_proc_desconto_outra_empresa,A.rend, D.ids_movimentos_estatisticas, A.a5020, A.inss_rescisao, A.valor_ferias, A.base_inss, A.base_inss_13_rescisao
        FROM rh_folha_proc as A
        INNER JOIN rh_clt as B
        ON B.id_clt = A.id_clt
        INNER JOIN curso as C
        ON C.id_curso = B.id_curso
        INNER JOIN rh_folha as D
        ON D.id_folha = A.id_folha
        WHERE A.id_folha = '{$_GET['folha']}'  AND A.id_clt = '{$folha['id_trab']}' AND A.status = 3;") ;                     

        $row_folha_proc = mysql_fetch_assoc($qr_folha_proc);   

        $qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '{$row_folha_proc['cbo_codigo']}'");
        $row_cbo = mysql_fetch_assoc($qr_cbo);
        $num_cbo = mysql_num_rows($qr_cbo);

        if(empty($num_cbo)) {
            $cbo = $row_folha_proc['cbo_codigo'];
        } else {
            $cbo = $row_cbo['cod'];
        }

        //VERIFICA��O CARTEIRA DE TRABALHO
         if(empty($row_folha_proc['campo1'])) {
            $numero_carteira_trabalho = $a++;
        } else {
            $numero_carteira_trabalho = $row_folha_proc['campo1'];
        }

        if(empty($row_folha_proc['serie_ctps'])) {
            $serie_carteira_trabalho = $b++;
        } else {
            $serie_carteira_trabalho = $row_folha_proc['serie_ctps'];
        }

        // Verificando se tem rescisao para fazer calculo
        if($_COOKIE['logado'] == 87 || $_COOKIE['logado'] == 255){
            // $dados_rescisao = $objRescisao->getRescisaoFolha($folha['id_trab'],$mes, $ano);     
            echo '<pre>';
            print_r($dados_rescisao);
            echo'</pre>';
        }

        $qr_recisao   = mysql_query("SELECT *,MONTH(data_demi) as mes_recisao,
                                       IF(YEAR(data_adm) = YEAR(data_demi),
                                       (DATEDIFF(data_demi, data_adm))+1, 
                                       (DATEDIFF(data_demi, '$ano-01-01')) + 1) as 15dias,
                                       IF(motivo = 65, aviso_valor,'') as aviso_pg_funcionario,
                                       IF(motivo != 65, aviso_valor,'') as aviso_credito,
                                       IF(motivo = 64, a479,'') as multa_a479,
                                       IF(motivo = 63, a480,'') as multa_a480
                                     FROM rh_recisao
                                       WHERE id_clt = '$folha[id_trab]'  
                                       AND YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND motivo IN (50,60,61,62,63,64,65,66,81,101,991,992,993) AND status = 1") or die(mysql_error());

        $row_recisao = mysql_fetch_assoc($qr_recisao);
        $num_recisao = mysql_num_rows($qr_recisao);

        if($num_recisao == 0){
            // Remuneração sem 13º
            if($row_folha_proc['data_proc'] > date('2010-06-30')) {
                $valor_sefip = $row_folha_proc['a6005']+$row_folha_proc['sallimpo_real'];  
            } else {
                $valor_sefip = $row_folha_proc['salbase'];
            }
        }else {
            $valor_sefip = $row_recisao['saldo_salario']; 
            $valor_sefip += $row_recisao['insalubridade']; 
            //$valor_sefip += $row_recisao['periculosidade_30']; 
            //  $valor_sefip += $row_recisao['aviso_credito']; 
            // $valor_sefip += $row_recisao['lei_12_506']; 
            //   echo $empregado['nome'].' = '.$valor_sefip.'<br>';
            $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao,categoria                                  
                      FROM rh_movimentos_rescisao as A 
                      INNER JOIN
                      rh_movimentos as B
                      ON A.id_mov = B.id_mov
                      WHERE A.id_clt = '$row_recisao[id_clt]' 
                      AND A.id_rescisao = '$row_recisao[id_recisao]' 
                      AND A.status = 1 AND A.incidencia = '5020,5021,5023'") or die(mysql_error());
            while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  
                if($row_movimentos['categoria'] == 'CREDITO'){
                    $valor_sefip += $row_movimentos['valor'];
                }elseif($row_movimentos['categoria'] == 'DEBITO' or $row_movimentos['categoria'] == 'DESCONTO'){
                    $valor_sefip -= $row_movimentos['valor'];
                }
            }
        }

        $array_licenca = array(52,50,51,90);                   
        // Mais Movimentos
        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt  WHERE id_movimento IN($row_folha_proc[ids_movimentos_estatisticas]) AND id_clt = '$folha[id_trab]'");
        while($row_movimento = @mysql_fetch_array($qr_movimentos)) {
            if(in_array($row_folha_proc['status_clt'], $array_licenca) and $row_movimento['lancamento'] == 2){ continue; }
            $incidencias = explode(',', $row_movimento['incidencia']);			  
            foreach($incidencias as $incidencia) {
                if($incidencia == 5023) { // FGTS       
                    if($row_movimento['tipo_movimento'] == 'CREDITO') {
                        $valor_sefip += $row_movimento['valor_movimento'];
                    } elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
                        $valor_sefip -= $row_movimento['valor_movimento'];
                    }
                }
            }
        } 

        if($num_recisao == 0){
            $valor_sefip  = ($row_folha_proc['base_inss'] <=0)? 0.01:$row_folha_proc['base_inss'];
            $valor_sefip  = number_format($valor_sefip,2,'','');
        }else {
            //  $valor_ferias = $row_folha_proc['valor_ferias'];
             //  $valor_sefip += $valor_ferias;
            $valor_sefip  = ($row_folha_proc['base_inss'] <=0)? 0.01:$row_folha_proc['base_inss'];
            //$valor_sefip  = number_format($valor_sefip,2,'','');
            $valor_sefip = $valor_sefip;
        }
        ///condi��o 13�
        if($mes == 13){  $valor_sefip = NULL; }            

        // Consulta de 13º
        if($row_folha_proc['tipo_terceiro'] != 3 and $mes == 12) {

           $remuneracao_13 = mysql_result(mysql_query("select  (salbase+rend)as decimo_terceiro  from rh_folha as A
                                   INNER JOIN rh_folha_proc as B
                                   ON A.id_folha = B.id_folha
                                   WHERE A.terceiro = 1 AND A.tipo_terceiro != 3 
                                   AND A.ano = '$ano'
                                   AND id_clt = '$folha[id_trab]'
                                   AND A.mes = '$mes'"), 0);     

        }elseif($mes == 11 && $decimo_terceiro == 1) {
            $remuneracao_13 = mysql_result(mysql_query("select  (salbase+rend)as decimo_terceiro   from rh_folha as A
                                   INNER JOIN rh_folha_proc as B
                                   ON A.id_folha = B.id_folha
                                   WHERE A.terceiro = 1 AND A.tipo_terceiro = 3 
                                   AND A.ano = '$ano'
                                   AND id_clt = '$folha[id_trab]'
                                   AND A.mes = '$mes'"), 0);    
        }
        $remuneracao_13 = number_format($remuneracao_13,2,'','');
        // Verificando se não tem desconto de inss
        $ocorrencia = '';
        // Verificando se tem desconto de inss em outra empresa
        if($row_folha_proc['desconto_inss'] == 1) {
            $ocorrencia = '05';
            $valor_descontado_segurado = @number_format(($row_folha_proc['a5020'] + $row_folha_proc['inss_rescisao']),2,'','');
        }else {
            $ocorrencia = '';
            $valor_descontado_segurado = '';
        }                


        ///////////////////////////////////////
        ////BASE DE CALCULO 13� PREV, 13� GPS
        ////////////////////////////////////////
        if($decimo_terceiro != 1){
            if(!empty($num_recisao) and $row_recisao['15dias'] >=15) {
                $base_calculo_13_salario_mov = number_format($row_recisao['dt_salario'] + $row_recisao['terceiro_ss'],2,'',''); 
                $base_calculo_13_gps         = number_format($row_recisao['dt_salario'] + $row_recisao['terceiro_ss'],2,'',''); 
                $previdencia_dt              = $row_recisao['previdencia_dt'];  
            }  else {
                 $qr_decimo = mysql_query("SELECT B.valor_dt, B.inss, B.ir_dt, B.fgts_dt,B.salliquido, C.terceiro_ss
                                        FROM rh_folha as A
                                        INNER JOIN rh_folha_proc as B
                                        ON A.id_folha = B.id_folha
                                        INNER JOIN rh_recisao as C
                                        ON C.id_clt = B.id_clt
                                        where B.id_clt = '$folha[id_trab]' AND A.terceiro = 1 AND A.tipo_terceiro IN(2,3) AND A.ano = '$ano'");
                $row_decimo = mysql_fetch_assoc($qr_decimo);

                $base_calculo_13_salario_mov = $row_decimo['salliquido']; 
                $base_calculo_13_gps         = $row_decimo['valor_dt'];   
                $previdencia_dt              = $row_decimo['previdencia_dt']; 
            }  
               /// echo $row_recisao['nome'].'_'.$row_recisao['mes_recisao'].'_'.(int)$mes.'<br>';

            switch($row_folha_proc['status_clt']){
                 //licença maternidade
                // dispensa sem justa causa
                case 81: 
                case 61: 
                case 64: 
                case 66: 
                case 50:   if($row_recisao['mes_recisao'] == (int)$mes) { $base_calculo_13salario_movimento = $base_calculo_13_salario_mov;}
                break;


                case 63:
                case 65:
                         $base_calculo_13salario_movimento = $base_calculo_13_salario_mov; 
                         $base_calculo_13salario_gps       = $base_calculo_13_gps;		
                break;
            } 
        } else {  

            if($mes == 12 or $mes ==13){
                $mes_decimo = 12;
             } else {
                 $mes_decimo = $mes;
             }

             $remuneracao_13 = mysql_result(mysql_query("select  (B.valor_dt+rend)as decimo_terceiro   from rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE A.terceiro = 1 
                                    AND A.ano = '$ano'
                                    AND id_clt = '$folha[id_trab]'
                                    AND A.mes = '$mes_decimo'"), 0);  
             $remuneracao_13 = number_format($remuneracao_13,2,'','');
             $base_calculo_13salario_movimento = str_replace('.','',str_replace(',','',number_format($folha['salbase'] + $folha['rend'],2,',','.'))); // AMANDA
        }
         
        // Remuneração com 13º
        $remuneracao_13 = ($mes == 13)?'':Valor($remuneracao_13);
        $remuneracao_13 = sprintf("%015s", $remuneracao_13);
        fwrite($arquivo, $remuneracao_13, 15);

        $classe_contribuicao = '';
        $classe_contribuicao = sprintf("%2s", $classe_contribuicao);
        fwrite($arquivo, $classe_contribuicao, 2);                  

        // Verificando se não tem desconto de inss
        $ocorrencia = '';
        
        // Verificando se tem desconto de inss em outra empresa
        if ($folha['desconto_inss'] == 1) {
            $ocorrencia = '05';
            $valor_descontado_segurado = @number_format($folha['folha_proc_desconto_outra_empresa'], 2, '', '');
        } else {
            $ocorrencia = '';
            $valor_descontado_segurado = '';
        }
        
        $ocorrencia = sprintf("%2s", $ocorrencia);
        fwrite($arquivo, $ocorrencia, 2);
        
        unset($previdencia_dt);
        if($mes != 12 and !empty($base_calculo_13salario_movimento)){    
            $base_calculo_13salario_gps = '';
        }
        ////

        $tipo_registro = '30';
        $tipo_registro = sprintf("%02s",$tipo_registro);
        fwrite($arquivo, $tipo_registro, 2);

        $tipo_inscricao = '1';
        $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
        fwrite($arquivo, $tipo_inscricao, 1);

        $inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
        $inscricao_empresa = str_replace('.','', $inscricao_empresa);
        $inscricao_empresa = substr($inscricao_empresa, 0, 14);
        $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
        fwrite($arquivo, $inscricao_empresa, 14);

        $tipo_inscricao_tomador = '';
        $tipo_inscricao_tomador = sprintf("%1s", $tipo_inscricao_tomador);
        fwrite($arquivo, $tipo_inscricao_tomador, 1);

        $inscricao_tomador = '';
        $inscricao_tomador = sprintf("%14s", $inscricao_tomador);
        fwrite($arquivo, $inscricao_tomador, 14);

        $pis_pasep_ci = RemoveCaracteres($row_folha_proc['pis']);
        $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
        fwrite($arquivo, $pis_pasep_ci, 11);

        $data_admissao = implode('', array_reverse(explode('-', $row_folha_proc['data_entrada'])));
        $data_admissao = sprintf("%08s", $data_admissao);
        fwrite($arquivo, $data_admissao, 8);

        $categoria_trabalhador = '01';
        $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
        fwrite($arquivo, $categoria_trabalhador, 2);

        $nome_trabalhador = RemoveAcentos($row_folha_proc['nome']);
        $nome_trabalhador = regexCaracterIgualConsecutivo(exPersonalizada($nome_trabalhador, $caracteres, array('[[:digit:]]')),3);
        $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
        fwrite($arquivo, $nome_trabalhador, 70);

        $matricula_empregado = sprintf("%05s", $folha['id_trab']);
        $matricula_empregado = sprintf("%11s", $matricula_empregado);
        fwrite($arquivo, $matricula_empregado, 11);

        $numero_ctps = RemoveCaracteres(trim($numero_carteira_trabalho));
        $numero_ctps = RemoveEspacos($numero_ctps);
        $numero_ctps = RemoveLetras($numero_ctps);
        $numero_ctps = sprintf("%07s", $numero_ctps);
        fwrite($arquivo, $numero_ctps, 7);

        $serie_ctps = RemoveCaracteres(trim($serie_carteira_trabalho));
        $serie_ctps = RemoveEspacos($serie_ctps);
        $serie_ctps = RemoveLetras($serie_ctps);
        $serie_ctps = sprintf("%05s", $serie_ctps);
        fwrite($arquivo, $serie_ctps, 5);

        $data_opcao = implode('', array_reverse(explode('-', $row_folha_proc['data_entrada'])));
        $data_opcao = sprintf("%08s", $data_opcao);
        fwrite($arquivo, $data_opcao, 8);

        $data_nascimento = implode('', array_reverse(explode('-', $row_folha_proc['data_nasci'])));
        $data_nascimento = sprintf("%08s", $data_nascimento);
        fwrite($arquivo, $data_nascimento, 8);

        $cbo = RemoveCaracteres($cbo);
        $cbo = '0'.str_replace('.', '', substr($cbo,0,4));
        $cbo = sprintf("%05s", $cbo);
        fwrite($arquivo, $cbo, 5);

        $remuneracao_sem_13 = $valor_sefip;
        $remuneracao_sem_13 = Valor($remuneracao_sem_13);
        $remuneracao_sem_13 = sprintf("%015s", $remuneracao_sem_13);
        fwrite($arquivo, $remuneracao_sem_13, 15);

        // Remuneração com 13º
        if($num_recisao != 0){
           $remuneracao_13 = $row_folha_proc['base_inss_13_rescisao'];
        }else { 
            if($mes == 12 or $mes ==13){
                $mes_decimo = 12;
             } else {
                 $mes_decimo = $mes;
             }

             $remuneracao_13 = mysql_result(mysql_query("select  (B.valor_dt+rend)as decimo_terceiro   from rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE A.terceiro = 1 
                                    AND A.ano = '$ano'
                                    AND id_clt = '$folha[id_trab]'
                                    AND A.mes = '$mes_decimo'"), 0);  
             $remuneracao_13 = number_format($remuneracao_13,2,'','');
        }

//        // Remuneração com 13º
//        $remuneracao_13 = ($mes == 13)?'':Valor($remuneracao_13);
//        $remuneracao_13 = sprintf("%015s", $remuneracao_13);
//        fwrite($arquivo, $remuneracao_13, 15);
//
//        $classe_contribuicao = '';
//        $classe_contribuicao = sprintf("%2s", $classe_contribuicao);
//        fwrite($arquivo, $classe_contribuicao, 2);                  
//
//        $ocorrencia = sprintf("%2s", $ocorrencia);
//        fwrite($arquivo, $ocorrencia, 2);

        $valor_descontado_segurado = $valor_descontado_segurado;
        $valor_descontado_segurado = Valor($valor_descontado_segurado);
        $valor_descontado_segurado = sprintf("%015s", $valor_descontado_segurado);
        fwrite($arquivo, $valor_descontado_segurado, 15);

        $remuneracao_base_calculo_contribuicao_previdenciaria = str_replace('.','',$remuneracao_base_calculo_contribuicao_previdenciaria);
        $remuneracao_base_calculo_contribuicao_previdenciaria = sprintf("%015s", $remuneracao_base_calculo_contribuicao_previdenciaria);
        fwrite($arquivo, $remuneracao_base_calculo_contribuicao_previdenciaria, 15);
        unset($remuneracao_base_calculo_contribuicao_previdenciaria);

        //22
        $base_calculo_13salario_movimento = sprintf("%015s", str_replace('.','',$base_calculo_13salario_movimento));
        fwrite($arquivo, $base_calculo_13salario_movimento, 15);
        unset($base_calculo_13salario_movimento);
        //
        //23
        $base_calculo_13salario_gps = sprintf("%015s", str_replace('.','',$base_calculo_13salario_gps));
        fwrite($arquivo, $base_calculo_13salario_gps, 15);
        unset($base_calculo_13salario_gps);
        //

        $brancos = NULL;
        $brancos = sprintf("%98s", $brancos);
        fwrite($arquivo, $brancos, 98);

        $final_linha = '*';
        $final_linha = sprintf("%01s", $final_linha);
        fwrite($arquivo, $final_linha, 1);

        fwrite($arquivo, "\r\n");

        unset($valor_sefip,$remuneracao_13,$valor_ferias,$ferias, $base_calculo_13salario_gps,$base_calculo_13_salario_mov, $base_calculo_13salario_movimento);
        // Fim do Loop dos participantes

        // Linha 7 (Registro Tipo 32)


        // Consultas para buscar participantes da folha
        /*
        $qr_folha = mysql_query("SELECT  rh_recisao.data_adm, rh_recisao.data_demi, rh_recisao.nome,  rh_recisao.motivo,  rh_folha_proc.id_clt, rh_folha_proc.id_folha, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.status_clt, rh_folha_proc.sallimpo, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos, rh_folha_proc.fgts, rh_folha_proc.fgts_ferias, rh_folha_proc.status_clt, rh_folha_proc.dias_trab, rh_folha_proc.data_proc, rh_folha_proc.salbase, rh_folha_proc.folha_proc_desconto_outra_empresa, rh_folha_proc.folha_proc_diferenca_inss, rh_clt.pis   FROM rh_folha_proc 
        INNER JOIN rh_clt 
        ON rh_folha_proc.id_clt = rh_clt.id_clt
        INNER JOIN rh_recisao
        ON rh_recisao.id_clt =  rh_folha_proc.id_clt
        WHERE rh_folha_proc.id_folha_proc IN($ids) AND YEAR(rh_recisao.datas_demi) = '$ano' AND MONTH(rh_recisao.data_demi) = '$mes' AND rh_recisao.motivo IN (50,60,61,62,63,64,65,66,81,101,991,992,993)  ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC") or die(mysql_error());*/


        //$numero_folha = mysql_num_rows($qr_folha);

        // Loop dos participantes
        //while($folha = mysql_fetch_assoc($qr_folha)) {

        /* Se for rescisão informa um mês antes da mesma
        $mes_seguinte = date('m/Y', mktime('0', '0', '0', $mes + 1, 1, $ano));
        list($mes_rescisao,$ano_rescisao) = explode('/', $mes_seguinte);

        $qr_movimentacao    = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$folha[id_clt]' AND YEAR(data) = '$ano_rescisao' AND MONTH(data) = '$mes_rescisao' AND cod_status IN ('60','61','62','63','64','65','81','101','991','992','993') AND status = '1'");
        $row_movimentacao   = mysql_fetch_assoc($qr_movimentacao);
        $total_movimentacao = mysql_num_rows($qr_movimentacao);

        if(empty($total_movimentacao)) {
               // Senão informa o movimento no mês corrente da folha
               $qr_movimentacao    = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$folha[id_clt]' AND YEAR(data) = '$ano' AND MONTH(data) = '$mes' AND cod_status IN (20,30,50,52,70,80,90,100,110) AND status = '1'");
               $row_movimentacao   = mysql_fetch_assoc($qr_movimentacao);
               $total_movimentacao = mysql_num_rows($qr_movimentacao);
        } */

        // Movimentação de Salário Maternidade e Rescisão
        //$qr_movimentacao    = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$folha[id_clt]' AND YEAR(data) = '$ano' AND MONTH(data) = '$mes' AND cod_status IN (50,60,61,62,63,64,65,66,81,101) AND status = '1'"); 


        //Verifica se o trabalhador tem recisao	
        /*$qr_movimentacao    = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$folha[id_clt]'  AND YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND motivo IN (50,60,61,62,63,64,65,66,81,101,991,992,993)") or die(mysql_error());
        $row_movimentacao   = mysql_fetch_assoc($qr_movimentacao);
        $total_movimentacao = mysql_num_rows($qr_movimentacao);*/


        // Havendo movimentação dentro das diretrizes anteriores continua...

        //MOvimentação salário  maternidade

        if($row_folha_proc['status_clt'] == 50){

               $qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$folha[id_trab]' AND cod_status = 50");
               $row_evento = mysql_fetch_assoc($qr_eventos);
               $verifica_maternidade = mysql_num_rows($qr_eventos);

        }


        if((($num_recisao != 0 and $row_folha_proc['status_clt'] != 10) or $row_folha_proc['status_clt'] == 50) and $mes != 13  ){
    ///////////////////////////////////////////////////
    /////////// Linha 7 (Registro Tipo 32 ) ///////////
    /////////////////////////////////////////////////// 
        $tipo_registro = '32';
        $tipo_registro = sprintf("%02s",$tipo_registro);
        fwrite($arquivo, $tipo_registro, 2);

        $tipo_inscricao = '1';
        $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
        fwrite($arquivo, $tipo_inscricao, 1);

        $inscricao_empresa = RemoveCaracteres($empresa['cnpj']);
        $inscricao_empresa = str_replace('.','', $inscricao_empresa);
        $inscricao_empresa = substr($inscricao_empresa, 0, 14);
        $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
        fwrite($arquivo, $inscricao_empresa, 14);

        $tipo_inscricao_tomador = '';
        $tipo_inscricao_tomador = sprintf("%1s", $tipo_inscricao_tomador);
        fwrite($arquivo, $tipo_inscricao_tomador, 1);

        $inscricao_tomador = '';
        $inscricao_tomador = sprintf("%14s", $inscricao_tomador);
        fwrite($arquivo, $inscricao_tomador, 14);

        $pis_pasep_ci = RemoveCaracteres($row_folha_proc['pis']);
        $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
        fwrite($arquivo, $pis_pasep_ci, 11);

        $data_admissao = implode('', array_reverse(explode('-', $row_folha_proc['data_entrada'])));
        $data_admissao = sprintf("%08s", $data_admissao);
        fwrite($arquivo, $data_admissao, 8);

        $categoria_trabalhador = '01';
        $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
        fwrite($arquivo, $categoria_trabalhador, 2);

        $nome_trabalhador = RemoveAcentos($row_folha_proc['nome']);
        $nome_trabalhador = regexCaracterIgualConsecutivo(exPersonalizada($nome_trabalhador, $caracteres, array('[[:digit:]]')),3);
        $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
        fwrite($arquivo, $nome_trabalhador, 70);

        // Buscando Código de Movimentação
        // $codigo_movimentacao = 'Z5';
        if($num_recisao !=0) {
            $codigo = $row_recisao['motivo'];
        } else {
            $codigo = $row_folha_proc['status_clt'];
        }

        switch($codigo) {
               case 50:
                  $codigo_movimentacao = 'Q1';
                  $indicador_recolhimento = ' ';
               break;
               case 60:
                  $codigo_movimentacao = 'H';
                   $indicador_recolhimento = ' '; 
               break;
               case 61:
               case 64:
               case 66: 
               case 991:
                  $codigo_movimentacao = 'I1';
                  $indicador_recolhimento = 'S';	
               break;

               case 62:
               case 993:
                  $codigo_movimentacao = 'L';
                  $indicador_recolhimento = 'S';	
               break;

               case 63:
               case 65:  
               case 70:
                  $codigo_movimentacao = 'J';
                  $indicador_recolhimento = ' ';

               break;	
               case 81:
                  $codigo_movimentacao = 'S2';
                   $indicador_recolhimento = ' ';

               break;
               case 101:
                  $codigo_movimentacao = 'U1';
                   $indicador_recolhimento = ' ';
               break;

               case 992:
                  $codigo_movimentacao = 'I4';
                  $indicador_recolhimento = 'S';	
               break;

        }

        $codigo_movimentacao = sprintf("%-2s", $codigo_movimentacao);
        fwrite($arquivo, $codigo_movimentacao, 2);

        ///DAta movimentação
        if($verifica_maternidade != 0) {
            $dt_mov = $row_evento['data'];
        } else {           
            $dt_mov = $row_recisao['data_demi'];
        }

        $data_movimentacao = implode('', array_reverse(explode('-', $dt_mov)));
        $data_movimentacao = sprintf("%8s", $data_movimentacao);
        fwrite($arquivo, $data_movimentacao, 8);

        $indicador_recolhimento = $indicador_recolhimento;
        $indicador_recolhimento = sprintf("%1s", $indicador_recolhimento);
        fwrite($arquivo, $indicador_recolhimento, 1);

        $brancos = NULL;
        $brancos = sprintf("%225s", $brancos);
        fwrite($arquivo, $brancos, 225);

        $final_linha = '*';
        $final_linha = sprintf("%01s", $final_linha);
        fwrite($arquivo, $final_linha, 1);

        fwrite($arquivo, "\r\n");
        }

        unset($num_recisao,$verifica_maternidade);
        
     }else //FIM CLT
     {
         
         
     /*   
        if($folha['tipo_contratacao'] == 1 and $mes != 13){ ///AUTONOMO 


          $qr_autonomo = mysql_query("SELECT A.id_autonomo,B.nome,B.pis, B.data_entrada,B.data_nasci,B.id_curso, D.id_saida,A.valor, D.tipo,C.tipo_vinculo,A.mes_competencia,A.ano_competencia, F.cod as cbo,
                                        A.valor_inss,A.base_inss
                                    FROM rpa_autonomo as A
                                      INNER JOIN autonomo as B
                                      ON A.id_autonomo = B.id_autonomo
                                      INNER JOIN rpa_saida_assoc as C
                                      ON C.id_rpa = A.id_rpa
                                      INNER JOIN saida as D
                                      ON D.id_saida = C.id_saida
                                      LEFT JOIN curso as E
                                      ON E.id_curso = B.id_curso
                                      LEFT JOIN rh_cbo as F
                                      ON F.id_cbo = E.cbo_codigo
                                      WHERE B.id_regiao = '$regiao' AND B.id_projeto = '$projeto' AND B.status_reg = 1 
                                        AND MONTH(A.data_geracao) = $mes AND YEAR(A.data_geracao) = $ano AND C.tipo_vinculo = 1		
                                      AND A.id_autonomo = '$folha[id_trab]'");
          
          
      
          
          $row_aut = mysql_fetch_assoc($qr_autonomo);  
         
                   $REGISTRO_30['TIPO_REGISTRO']                = 30;
                   $REGISTRO_30['TIPO_INSCRICAO']               = 1;
                   $REGISTRO_30['INSCRICAO_EMPRESA']            = $empresa['cnpj'];
                   $REGISTRO_30['TIPO_INSCRICAO_TOMADOR']       = '';
                   $REGISTRO_30['INSCRICAO_TOMADOR']            = '';
                   $REGISTRO_30['PIS_PASEP']                    = $row_aut['pis'];
                   $REGISTRO_30['DATA_ADMISSAO']                = NULL;
                   $REGISTRO_30['CATEGORIA_TRABALHADOR']        = '13';
                   $REGISTRO_30['NOME_TRABALHADOR']             = $row_aut['nome'];
                   $REGISTRO_30['MATRICULA_TRABALHADOR']          = NULL;
                   $REGISTRO_30['NUMERO_CTPS']                  = NULL;
                   $REGISTRO_30['SERIE_CTPS']                   = NULL;
                   $REGISTRO_30['DATA_OPCAO']                   = NULL;
                   $REGISTRO_30['DATA_NASCIMENTO']              = NULL;
                   $REGISTRO_30['CBO']                          =  '225125'; //CBO DE M�DICO
                   $REGISTRO_30['REMUNERACAO_SEM_13�']          = $row_aut['valor'];
                   $REGISTRO_30['REMUNERACAO_13�']              = '';
                   $REGISTRO_30['CLASSE_DE_CONTIBUICAO']        = '';
                   $REGISTRO_30['OCORRENCIA']                   = ($row_aut['valor_inss'] == '0.00')? '05': '';;
                   $REGISTRO_30['VALOR_DESCONTADO_SEGURADO']    = '';
                   $REGISTRO_30['REMUNERACAO_BASE_CALC_CONTRIB_PREV'] = '';
                   $REGISTRO_30['BASE_CALC_13_SAL_PREV_SOCIAL']       = '';
                   $REGISTRO_30['BASE_CALC_13_SAL_PREV_SOCIAL_GPS']     = '';
                   $REGISTRO_30['BRANCOS']     = NULL;
                   $REGISTRO_30['FIM']     = '*';
                   
                         
                   
                   $tipo_registro = sprintf("%02s",$REGISTRO_30['TIPO_REGISTRO'] );
                   fwrite($arquivo, $tipo_registro, 2);
                   
                   $tipo_inscricao = sprintf("%01s",   $REGISTRO_30['TIPO_INSCRICAO']);
                   fwrite($arquivo, $tipo_inscricao, 1);

                   $inscricao_empresa = RemoveCaracteres($REGISTRO_30['INSCRICAO_EMPRESA']);
                   $inscricao_empresa = str_replace('.','', $inscricao_empresa);
                   $inscricao_empresa = substr($inscricao_empresa, 0, 14);
                   $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
                   fwrite($arquivo, $inscricao_empresa, 14);

                   $tipo_inscricao_tomador = '';
                   $tipo_inscricao_tomador = sprintf("%1s", $tipo_inscricao_tomador);
                   fwrite($arquivo, $tipo_inscricao_tomador, 1);

                   $inscricao_tomador =  $REGISTRO_30['INSCRICAO_TOMADOR'];
                   $inscricao_tomador = sprintf("%14s", $inscricao_tomador);
                   fwrite($arquivo, $inscricao_tomador, 14);

                   $pis_pasep_ci = RemoveCaracteres( $REGISTRO_30['PIS_PASEP']);
                   $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
                   fwrite($arquivo, $pis_pasep_ci, 11);

                   $data_admissao = implode('', array_reverse(explode('-', $REGISTRO_30['DATA_ADMISSAO'])));
                   $data_admissao = sprintf("%8s", $data_admissao);
                   fwrite($arquivo, $data_admissao, 8);

                   $categoria_trabalhador = $REGISTRO_30['CATEGORIA_TRABALHADOR'];
                   $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
                   fwrite($arquivo, $categoria_trabalhador, 2);

                   $nome_trabalhador = RemoveAcentos($REGISTRO_30['NOME_TRABALHADOR']);
                   $nome_trabalhador = RemoveCaracteres($nome_trabalhador);
                   $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
                   fwrite($arquivo, $nome_trabalhador, 70);

                   $matricula_empregado = sprintf("%5s", $REGISTRO_30['MATRICULA_TRABALHADOR']);
                   $matricula_empregado = sprintf("%11s", $matricula_empregado);
                   fwrite($arquivo, $matricula_empregado, 11);

                 

                   $numero_ctps = RemoveCaracteres(trim( $REGISTRO_30['NUMERO_CTPS']));
                   $numero_ctps = RemoveEspacos($numero_ctps);
                   $numero_ctps = RemoveLetras($numero_ctps);
                   $numero_ctps = sprintf("%7s", $numero_ctps);
                   fwrite($arquivo, $numero_ctps, 7);

                   $serie_ctps = RemoveCaracteres(trim($REGISTRO_30['SERIE_CTPS']));
                   $serie_ctps = RemoveEspacos($serie_ctps);
                   $serie_ctps = RemoveLetras($serie_ctps);
                   $serie_ctps = sprintf("%5s", $serie_ctps);
                   fwrite($arquivo, $serie_ctps, 5);

                   $data_opcao = implode('', array_reverse(explode('-', $REGISTRO_30['DATA_OPCAO'])));
                   $data_opcao = sprintf("%8s", $data_opcao);
                   fwrite($arquivo, $data_opcao, 8);

                   $data_nascimento = implode('', array_reverse(explode('-', $REGISTRO_30['DATA_NASCIMENTO'])));
                   $data_nascimento = sprintf("%8s", $data_nascimento);
                   fwrite($arquivo, $data_nascimento, 8);

                   $cbo = RemoveCaracteres($REGISTRO_30['CBO'] );
                   $cbo = '0'.str_replace('.', '', substr($cbo,0,4));
                   $cbo = sprintf("%05s", $cbo);
                   fwrite($arquivo, $cbo, 5);


                   $remuneracao_sem_13 = $REGISTRO_30['REMUNERACAO_SEM_13�'] ;
                   $remuneracao_sem_13 = Valor($remuneracao_sem_13);
                   $remuneracao_sem_13 = sprintf("%015s", $remuneracao_sem_13);
                   fwrite($arquivo, $remuneracao_sem_13, 15);

                   
                   // Remuneração com 13º
                   $remuneracao_13 = Valor($REGISTRO_30['REMUNERACAO_13�'] );
                   $remuneracao_13 = sprintf("%015s", $remuneracao_13);
                   fwrite($arquivo, $remuneracao_13, 15);



                   $classe_contribuicao = $REGISTRO_30['CLASSE_DE_CONTIBUICAO'];
                   $classe_contribuicao = sprintf("%2s", $classe_contribuicao);
                   fwrite($arquivo, $classe_contribuicao, 2);                  
                   
                   
                   $ocorrencia = sprintf("%2s", $REGISTRO_30['OCORRENCIA'] );
                   fwrite($arquivo, $ocorrencia, 2);
                   
                   $valor_descontado_segurado = $REGISTRO_30['VALOR_DESCONTADO_SEGURADO'];
                   $valor_descontado_segurado = Valor($valor_descontado_segurado);
                   $valor_descontado_segurado = sprintf("%015s", $valor_descontado_segurado);
                   fwrite($arquivo, $valor_descontado_segurado, 15);

                   $remuneracao_base_calculo_contribuicao_previdenciaria = str_replace('.','',$REGISTRO_30['REMUNERACAO_BASE_CALC_CONTRIB_PREV']);
                   $remuneracao_base_calculo_contribuicao_previdenciaria = sprintf("%015s", $remuneracao_base_calculo_contribuicao_previdenciaria);
                   fwrite($arquivo, $remuneracao_base_calculo_contribuicao_previdenciaria, 15);
                   unset($remuneracao_base_calculo_contribuicao_previdenciaria);
                    
                   //22
                   $base_calculo_13salario_movimento = sprintf("%015s", str_replace('.','',$REGISTRO_30['BASE_CALC_13_SAL_PREV_SOCIAL'] ));
                   fwrite($arquivo, $base_calculo_13salario_movimento, 15);
                   unset($base_calculo_13salario_movimento);
                   //
                   //23
                   $base_calculo_13salario_gps = sprintf("%015s", str_replace('.','',$REGISTRO_30['BASE_CALC_13_SAL_PREV_SOCIAL_GPS']));
                   fwrite($arquivo, $base_calculo_13salario_gps, 15);
                   unset($base_calculo_13salario_gps);
                   //

                 
                   $brancos = sprintf("%98s", $REGISTRO_30['BRANCOS'] );
                   fwrite($arquivo, $brancos, 98);

                  
                   $final_linha = sprintf("%01s", $REGISTRO_30['FIM'] );
                   fwrite($arquivo, $final_linha, 1);

                   fwrite($arquivo, "\r\n");
}   
*/
}


unset($valor_sefip,$remuneracao_13,$valor_ferias,$ferias, $base_calculo_13salario_gps,$base_calculo_13_salario_mov, $base_calculo_13salario_movimento,$REGISTRO_30);
}// Fim do Loop dos participantes linha 32

// Resetando os valores para o próximo loop
unset($qr_empregado);
unset($empregado);
unset($qr_curso);
unset($curso);

// Resetando todos os valores
unset($qr_folha_pai);
unset($folha_pai);
unset($qr_folha);
unset($folha);

//}

// Fim para Linha 6 e 7
    ///////////////////////////////////////////////////
    /////////// Linha 8 (Registro Tipo 90 ) ///////////
    /////////////////////////////////////////////////// 
//if($parte_sefip == 7 or isset($sefip_folha)) { 

$tipo_registro = '90';
$tipo_registro = sprintf("%02s",$tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

$tipo_registro = '9';
for($i=0; $i<51; $i++) {
    $tipo_registro .= 9;
}
$tipo_registro = sprintf("%51s",$tipo_registro);
fwrite($arquivo, $tipo_registro, 51);

$brancos = NULL;
$brancos = sprintf("%306s", $brancos);
fwrite($arquivo, $brancos, 306);

$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);


echo '<pre>';
//print_r($ARRAY);
echo '</pre>';
//}

// Fim para Linha 8
?>