<?php

function RemoveCaracteres($variavel) {	
	$variavel = str_replace("  ", " ", $variavel);
	$variavel = str_replace("(", "", $variavel);
	$variavel = str_replace(")", "", $variavel);
	$variavel = str_replace("-", "", $variavel);
	$variavel = str_replace("/", "", $variavel);
	$variavel = str_replace(":", "", $variavel);
	$variavel = str_replace(",", " ", $variavel);
	$variavel = str_replace(".", "", $variavel);
	$variavel = str_replace(";", "", $variavel);
	$variavel = str_replace("\"", "", $variavel);
	$variavel = str_replace("\'", "", $variavel);
	return $variavel;        
}

function RemoveEspacos($variavel) {
	$variavel = str_replace(" ", "", $variavel);	
	return $variavel;
}

function RemoveLetras($variavel) {
	$letras = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    foreach($letras as $letra) {
		$variavel = str_replace($letra, '', $variavel);
	}
	return $variavel;
}

function Valor($variavel) {
	$variavel = str_replace(".", "", $variavel);
	return $variavel;
}

function RemoveAcentos($str, $enc = "iso-8859-1") {
$acentos = array(
	'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
	'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
	'C' => '/&Ccedil;/',
	'c' => '/&ccedil;/',
	'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
	'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
	'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
	'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
	'N' => '/&Ntilde;/',
	'n' => '/&ntilde;/',
	'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
	'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
	'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
	'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
	'Y' => '/&Yacute;/',
	'y' => '/&yacute;|&yuml;/',
	'a.' => '/&ordf;/',
	'o.' => '/&ordm;/'
);
   return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
}




// Consulta da Empresa
if($row_master['id_master'] == 6) {
	
	
        $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '28' ") or die(mysql_error());
	$empresa 	= mysql_fetch_assoc($qr_empresa);
        $codigo_recolhimento = '115';

} else if($row_master['id_master'] != 6 AND $row_master['id_master'] != 8){
	
	$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
	$empresa 	= mysql_fetch_assoc($qr_empresa);
	$codigo_recolhimento = '115';

}else if( $row_master['id_master'] == 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '38'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
	$empresa 	= mysql_fetch_assoc($qr_empresa);
	$codigo_recolhimento = '115';
}





////CONDI��es para d�cimo terceiro

if($decimo_terceiro == 1){
    
    $mes_competencia    = 13;
    $modalidade_arquivo = 1;

}else {    
    $mes_competencia    = $mes;
    $modalidade_arquivo = NULL;
}

// Buscando Ids de Sefips Anteriores
settype($ids_anteriores,'array');  
$qr_sefips_anteriores2 = mysql_query("select B.id_clt FROM sefip as A
                                        INNER JOIN rh_folha_proc as B
                                        ON A.folha = B.id_folha
                                        WHERE B.status = 3 AND A.regiao = $regiao
                                        GROUP BY B.id_clt ");
while($sefip_anterior2 = mysql_fetch_assoc($qr_sefips_anteriores2)){
    
    $ids_anteriores[] = $sefip_anterior2['id_clt'];
    
}



 
//////////////////////////////////////////////////
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
$nome_responsavel = RemoveCaracteres($empresa['razao']);
$nome_responsavel = substr($nome_responsavel, 0, 30);
$nome_responsavel = sprintf("%-30s", $nome_responsavel);
fwrite($arquivo, $nome_responsavel, 30);

$nome_pessoa_contato = RemoveAcentos($empresa['responsavel']);
$nome_pessoa_contato = substr($nome_pessoa_contato, 0, 20);
$nome_pessoa_contato = sprintf("%-20s", $nome_pessoa_contato);
fwrite($arquivo, $nome_pessoa_contato, 20);

$logradouro = RemoveEspacos($empresa['endereco']);
$logradouro = explode('-', RemoveAcentos($logradouro));
$logradouro = substr(RemoveCaracteres($logradouro[0]), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

$bairro = explode('-', RemoveAcentos($empresa['endereco']));
$bairro = str_replace(' ', '', $bairro[1]);
$bairro = substr(RemoveCaracteres($bairro), 0, 20);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

$cep = RemoveCaracteres($empresa['cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

$cidade = explode('-', RemoveAcentos($empresa['endereco']));
$cidade = str_replace(' ', '', $cidade[2]);
$cidade = substr(RemoveCaracteres($cidade), 0, 20);
$cidade = sprintf("%-20s", $cidade);
fwrite($arquivo, $cidade, 20);

$unidade_federecao = explode('-', $empresa['endereco']);
$unidade_federecao = str_replace(' ', '', $unidade_federecao[3]);
$unidade_federecao = substr($unidade_federecao, 0, 2);
$unidade_federecao = sprintf("%02s", $unidade_federecao);
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

$codigo_recolhimento = $codigo_recolhimento;
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
//



$modalidade_arquivo = $modalidade_arquivo;
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
$data_prazo = date('Y-m-d', mktime('0','0','0', $mes + 1, 15, $ano));

if($data_comparacao > $data_prazo) {
	$indicador_recolhimento = 2;
} else {
	$indicador_recolhimento = 1;
}

$indicador_recolhimento = sprintf("%1s", $indicador_recolhimento);
fwrite($arquivo, $indicador_recolhimento, 1);
//

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

$inscricao_fornecedor = '27915735000100';
$inscricao_fornecedor = sprintf("%14s", $inscricao_fornecedor);
fwrite($arquivo, $inscricao_fornecedor, 14);

$brancos = NULL;
$brancos = sprintf("%18s", $brancos);
fwrite($arquivo, $brancos, 18);

$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");

//}

// Fim para Linha 1


// Consulta da Empresa
if($row_master['id_master'] == 6) {
	
	$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' ");
	$empresa 	= mysql_fetch_assoc($qr_empresa);
        $codigo_recolhimento = '115';

} else if($row_master['id_master'] != 6 AND $row_master['id_master'] != 8){
	
	$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
	$empresa 	= mysql_fetch_assoc($qr_empresa);
	$codigo_recolhimento = '115';

}else if( $row_master['id_master'] == 8){
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '38'"); //$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao'");
	$empresa 	= mysql_fetch_assoc($qr_empresa);
	$codigo_recolhimento = '115';
        
        
      
}


// Linha 2 (Registro Tipo 10)

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
$nome_empresa = RemoveCaracteres($empresa['razao']);
$nome_empresa = substr($nome_empresa, 0, 30);
$nome_empresa = sprintf("%-40s", $nome_empresa);
fwrite($arquivo, $nome_empresa, 40);

$logradouro = RemoveEspacos($empresa['endereco']);
$logradouro = explode('-', RemoveAcentos($logradouro));
$logradouro = substr(RemoveCaracteres($logradouro[0]), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

$bairro = explode('-', RemoveAcentos($empresa['endereco']));
$bairro = str_replace(' ', '', $bairro[1]);
$bairro = substr(RemoveCaracteres($bairro), 0, 20);
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
 $cidade = substr(RemoveCaracteres($cidade), 0, 20);
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



if(isset($_GET['folha'])) {
	
	
    
    
	$familia = @mysql_result(mysql_query("SELECT total_familia FROM rh_folha WHERE id_folha = '$_GET[folha]'"),0);
	
        
	//Salario maternidade
	$qr_folha_2 = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$_GET[folha]'");
	$row_folha2 = mysql_fetch_assoc($qr_folha_2);
	
	if($row_folha2['data_proc'] > date('2010-06-30')) {			
			$maternidade = @mysql_result(mysql_query("SELECT SUM(sallimpo_real) FROM rh_folha_proc WHERE id_folha_proc IN($ids)  AND status_clt = 50"),0);
            } else {		
		  $maternidade = @mysql_result(mysql_query("SELECT SUM(salbase) FROM rh_folha_proc WHERE id_folha_proc IN($ids)  AND status_clt = 50"),0);
	}
        
} else {
	$familia = @mysql_result(mysql_query("SELECT SUM(total_familia) AS total_familia FROM rh_folha WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND regiao != '36' "),0);

	
	// $maternidade = @mysql_result(mysql_query("SELECT SUM(a6005) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36'"),0);
	
	 //Salario maternidade
	$qr_folha_2 = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$_GET[folha]'");	
	$row_folha2 = mysql_fetch_assoc($qr_folha_2);
	
	if($row_folha2['data_proc'] > date('2010-06-30')) {
			
			$maternidade = @mysql_result(mysql_query("SELECT SUM(sallimpo_real) FROM rh_folha_proc WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND id_regiao != '36' AND status_clt = 50 AND id_folha_proc IN($ids)  	 "),0);
		
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








// Linha 3 (Registro Tipo 12)

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


// Linha 4 (Registro Tipo 13)

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










// Linha 5 (Registro Tipo 14)

//if($parte_sefip == 5 or isset($sefip_folha)) {

// Consulta para buscar participantes da folha
$qr_folha = mysql_query("SELECT rh_folha_proc.id_clt, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos 
                        FROM rh_folha_proc 
                        INNER JOIN rh_clt 
                        ON rh_folha_proc.id_clt = rh_clt.id_clt 
                        WHERE rh_folha_proc.id_folha_proc IN($ids) ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC");

// Loop dos participantes
while($folha = mysql_fetch_assoc($qr_folha)) {
	
// Verificando se ele já foi incluso
if(!in_array($folha['id_clt'],$ids_anteriores)) {

// Consulta de alteração de endereço
$qr_alteracao = mysql_query("SELECT * FROM log WHERE sefip = '1' AND sefip_id = '$folha[id_clt]' AND sefip_ano = '$ano' AND sefip_mes = '$mes' AND sefip_codigo = ''");
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

$logradouro = RemoveAcentos(trim($empregado['endereco']));
$logradouro = RemoveCaracteres($logradouro);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

$bairro = RemoveAcentos(trim($empregado['bairro']));
$bairro = RemoveCaracteres($bairro);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

$cep = RemoveCaracteres($empregado['cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

$cidade = RemoveAcentos(trim($empregado['cidade']));
$cidade = RemoveCaracteres($cidade);
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



// Linha 6 e 7 (Registro Tipo 30 e 32)

//if($parte_sefip == 6 or isset($sefip_folha)) {
	
// Linha 6 (Registro Tipo 30)

///PEGANDO OS CLTS QUE FORAM TRANSFERIDOS NO M�S DA COMPET�NCIA
$qr_verifica_transferencia = mysql_query("SELECT A.id_clt, A.id_projeto_de, A.id_projeto_para, DATE_FORMAT(A.data_proc,'%Y-%m-%d') as data_proc, B.nome 
                                        FROM rh_transferencias as A
                                        INNER JOIN rh_clt as B
                                        ON B.id_clt = A.id_clt
                                        WHERE A.id_projeto_de != A.id_projeto_para
                                        AND MONTH(data_proc) = '$mes' AND YEAR(data_proc) = '$ano' ") or die(mysql_error());
if(mysql_num_rows($qr_verifica_transferencia) != 0){

    while($row_transferencia = mysql_fetch_assoc($qr_verifica_transferencia)){
        
        $id_projeto_de   = $row_transferencia['id_projeto_de'];
        $id_projeto_para = $row_transferencia['id_projeto_para'];
        
        $ids_tranferidos_de[$id_projeto_de][]     = $row_transferencia['id_clt'];
        $ids_tranferidos_para[$id_projeto_para][] = $row_transferencia['id_clt'];        
        
    
        $dt_mov_transferidos[$row_transferencia['id_clt']]  = $row_transferencia['data_proc'];

        echo $row_transferencia['nome'].' - '.$row_transferencia['data_proc'].' ==> '.$row_transferencia['id_clt'];
        echo '<br>';
    }
    
}


///PEGANDO IDS PARA ADICIONAR NA CONSULTA DA FOLHA
if(sizeof($ids_tranferidos_de[$projeto]) != 0 and sizeof($ids_tranferidos_para[$projeto]) != 0) {
    
    $sql_ids_tranferidos = array_merge($ids_tranferidos_de[$projeto], $ids_tranferidos_para[$projeto]);
    
} elseif($ids_tranferidos_de[$projeto] != 0 ){
    
    $sql_ids_tranferidos = implode(',',$ids_tranferidos_de[$projeto]);
    
}elseif($ids_tranferidos_para[$projeto] != 0 ){
    
   $sql_ids_tranferidos = implode(',',$ids_tranferidos_para[$projeto]);
    
}

///VERIFICANDO CLT's QUE FORAM TRANSFERIDOS DE UNIDADE
$sql_transferidos = (!empty($sql_ids_tranferidos))?"OR (rh_clt.id_clt IN($sql_ids_tranferidos) AND rh_folha_proc.mes = $mes)":'';


//ALTERADO DIA 09/08/2011 ADICIONADO O CAMPO 'rh_folha_proc.folha_proc_desconto_outra_empresa' NO SELECT PARA MOSTRAR NO REGISTRO 'valor_descontado_segurado'



// Consultas para buscar participantes da folha 
$qr_folha = mysql_query("SELECT rh_folha_proc.id_clt, rh_folha_proc.id_folha, rh_folha_proc.id_regiao,rh_folha_proc.sallimpo_real,rh_folha_proc.rend, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.status_clt, rh_folha_proc.sallimpo, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos, rh_folha_proc.fgts, rh_folha_proc.fgts_ferias, rh_folha_proc.status_clt, rh_folha_proc.dias_trab, rh_folha_proc.data_proc, rh_folha_proc.salbase, rh_folha_proc.folha_proc_desconto_outra_empresa, rh_folha_proc.folha_proc_diferenca_inss,
rh_folha.tipo_terceiro,rh_folha_proc.id_projeto
FROM rh_folha_proc 
INNER JOIN rh_folha
ON rh_folha.id_folha = rh_folha_proc.id_folha
INNER JOIN rh_clt 
ON rh_folha_proc.id_clt = rh_clt.id_clt 

WHERE rh_folha_proc.id_folha_proc IN($ids) 
$sql_transferidos
ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC");
$numero_folha = mysql_num_rows($qr_folha) or die(mysql_error());

// Loop dos participantes 
while($folha = mysql_fetch_assoc($qr_folha)) {
	
    
    
	/*
	// 	Verificação de folha de 13º
			$query_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha[id_folha]'");
			$row_folha = mysql_fetch_assoc($query_folha);
			
			
			if($row_folha['mes'] == '11'){
				if($row_folha['terceiro'] == '1'){
					continue;
				}
			}
			
			if($row_folha['mes'] == '12'){
				if($row_folha['terceiro'] == '1'){
					continue;
				}
			}
			// FIM - Verificação de folha de 13º
             */           
                        

// Consulta para dados dos participantes
$qr_empregado = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$folha[id_clt]' AND id_regiao = '$folha[id_regiao]'");
$empregado = mysql_fetch_assoc($qr_empregado);

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$empregado[id_curso]'");
$curso    = mysql_fetch_array($qr_curso);

$qr_cbo  = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$curso[cbo_codigo]'");
$row_cbo = mysql_fetch_assoc($qr_cbo);
$num_cbo = mysql_num_rows($qr_cbo);

if(empty($num_cbo)) {
	$cbo = $curso['cbo_codigo'];
} else {
	$cbo = $row_cbo['cod'];
}




if(!in_array($folha['id_clt'], $ids_tranferidos_de[$projeto]) and !in_array($folha['id_clt'], $ids_tranferidos_para[$projeto])) {
    

        // Inicio
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
        $nome_trabalhador = RemoveCaracteres($nome_trabalhador);
        $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
        fwrite($arquivo, $nome_trabalhador, 70);

        $matricula_empregado = sprintf("%05s", $empregado['id_clt']);
        $matricula_empregado = sprintf("%11s", $matricula_empregado);
        fwrite($arquivo, $matricula_empregado, 11);

        if(empty($empregado['campo1'])) {
                $numero_carteira_trabalho = $a++;
        } else {
                $numero_carteira_trabalho = $empregado['campo1'];
        }

        if(empty($empregado['serie_ctps'])) {
                $serie_carteira_trabalho = $b++;
        } else {
                $serie_carteira_trabalho = $empregado['serie_ctps'];
        }

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

        $data_opcao = implode('', array_reverse(explode('-', $empregado['data_entrada'])));
        $data_opcao = sprintf("%08s", $data_opcao);
        fwrite($arquivo, $data_opcao, 8);

        $data_nascimento = implode('', array_reverse(explode('-', $empregado['data_nasci'])));
        $data_nascimento = sprintf("%08s", $data_nascimento);
        fwrite($arquivo, $data_nascimento, 8);

        $cbo = RemoveCaracteres($cbo);
        $cbo = '0'.str_replace('.', '', substr($cbo,0,4));
        $cbo = sprintf("%05s", $cbo);
        fwrite($arquivo, $cbo, 5);


        // Verificando Férias
        $qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$folha[id_clt]' AND status = '1' AND ano = '$ano' ORDER BY id_ferias DESC LIMIT 1");
        $row_ferias = mysql_fetch_array($qr_ferias);
        $num_ferias = mysql_num_rows($qr_ferias);

        $inicio_sefip = $ano.'-'.$mes.'-01';
        $fim_sefip    = $ano.'-'.$mes.'-'.cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

        if(!empty($num_ferias)) {

                // Início das Férias entre o Início e Fim da Folha
                if($row_ferias['data_ini'] >= $inicio_sefip and $row_ferias['data_ini'] <= $fim_sefip) {

                        $inicio = $row_ferias['data_ini'];		
                        // Se o Fim das Férias for antes do Fim da Folha
                        if($row_ferias['data_retorno'] < $fim_sefip) {
                                $fim = $row_ferias['data_retorno'];
                        // Fim das Férias depois do Fim da Folha
                        } else {
                                $fim = date('Y-m-d', strtotime("$fim_sefip + 1 day"));
                        }

                        $ferias = true;

                // Fim das Férias entre o Início e Fim da Folha
                } elseif($row_ferias['data_fim'] >= $inicio_sefip and $row_ferias['data_fim'] <= $fim_sefip) {

                        // Se o Início das Férias for depois do Início da Folha
                        if($row_ferias['data_ini'] > $inicio_sefip) {
                                $inicio = $row_ferias['data_ini'];
                        // Início das Férias antes do Início da Folha
                        } else {
                                $inicio = $inicio_sefip;
                        }

                        $fim = $row_ferias['data_retorno'];		
                        $ferias = true;

                }

                // Tem Férias
                if(isset($ferias)) {

                        $dias_ferias = abs((int)floor((strtotime($inicio) - strtotime($fim)) / 86400));

                        list($nulo, $mes_inicio, $nulo) = explode('-', $row_ferias['data_ini']);
                        list($nulo, $mes_fim,    $nulo) = explode('-', $row_ferias['data_fim']);

                        if($mes_inicio == $mes_fim) {
                                $valor_ferias = $row_ferias['total_remuneracoes'];
                        } else {
                                $valor_ferias = ($row_ferias['total_remuneracoes'] / 30) * $dias_ferias;
                        }		
                }
        }
        // Fim da verificação de Férias


        // Remuneração sem 13º
        if($folha['data_proc'] > date('2010-06-30')) {
                $valor_sefip = $folha['sallimpo_real'];
        } else {
                $valor_sefip = $folha['salbase'];
        }

        $valor_sefip += $valor_ferias;



        // Mais Movimentos
        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt  WHERE id_movimento IN(".$folha['ids_movimentos'].")");
        while($row_movimento = @mysql_fetch_array($qr_movimentos)) {

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

        if($valor_sefip <=0){ $valor_sefip = 0.01;}

        $valor_sefip = number_format($valor_sefip,2,'','');


        ///condi��o 13�
        if($mes == 13){
            $valor_sefip = NULL;

        }


        $remuneracao_sem_13 = $valor_sefip;
        $remuneracao_sem_13 = Valor($remuneracao_sem_13);
        $remuneracao_sem_13 = sprintf("%015s", $remuneracao_sem_13);
        fwrite($arquivo, $remuneracao_sem_13, 15);


        // Consulta de 13º
        if($folha['tipo_terceiro'] != 3 and $mes == 12) {


            $remuneracao_13 = mysql_result(mysql_query("select  (salbase+rend)as decimo_terceiro  from rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE A.terceiro = 1 AND A.tipo_terceiro != 3 
                                    AND A.ano = '$ano'
                                    AND id_clt = '$empregado[id_clt]'
                                    AND A.mes = '$mes'"), 0);

                /*ANTES DE ALTERAR O SEFIP PRARA FAZER A COMPETENCIA 13
                $qr_13 = mysql_query("SELECT b.salbase, b.ids_movimentos FROM rh_folha a INNER JOIN rh_folha_proc b ON a.id_folha = b.id_folha 
                    WHERE a.terceiro = '1' AND a.ano = '$ano' AND b.id_clt = '$empregado[id_clt]'");
                while($row_13 = mysql_fetch_assoc($qr_13)) {

                        $remuneracao_13 += $row_13['salbase'];	
                        $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt
                                                                                                  WHERE id_movimento IN(".$row_13['ids_movimentos'].")");
                        while($row_movimento = @mysql_fetch_array($qr_movimentos)) {

                                // Acrescenta os Movimentos na Base de FGTS
                                $incidencias = explode(',', $row_movimento['incidencia']);

                                foreach($incidencias as $incidencia) {

                                        if($incidencia == 5023) { // FGTS
                                                if($row_movimento['tipo_movimento'] == 'CREDITO') {
                                                        $remuneracao_13 += $row_movimento['valor_movimento'];
                                                } elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
                                                        $remuneracao_13 -= $row_movimento['valor_movimento'];
                                                }
                                        }

                                }

                        } // Fim dos Movimentos

                }
                */



        }elseif($mes == 12) {
             $remuneracao_13 = mysql_result(mysql_query("select  (salbase+rend)as decimo_terceiro   from rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE A.terceiro = 1 AND A.tipo_terceiro = 3 
                                    AND A.ano = '$ano'
                                    AND id_clt = '$empregado[id_clt]'
                                    AND A.mes = '$mes'"), 0);    
        }
        $remuneracao_13 = number_format($remuneracao_13,2,'','');



        // Remuneração com 13º
        $remuneracao_13 = Valor($remuneracao_13);
        $remuneracao_13 = sprintf("%015s", $remuneracao_13);
        fwrite($arquivo, $remuneracao_13, 15);


        $classe_contribuicao = '';
        $classe_contribuicao = sprintf("%2s", $classe_contribuicao);
        fwrite($arquivo, $classe_contribuicao, 2);

        // Verificando se não tem desconto de inss
        $ocorrencia = '';

        if($empregado['desconto_inss']) {
             $ocorrencia = '05';
        }

        $ocorrencia = sprintf("%2s", $ocorrencia);
        fwrite($arquivo, $ocorrencia, 2);
        //


        // Verificando se tem rescisao para fazer calculo
        $qr_recisao   = mysql_query("SELECT *,
                                        IF(YEAR(data_adm) = YEAR(data_demi),
                                        (DATEDIFF(data_demi, data_adm))+1, 
                                        (DATEDIFF(data_demi, '$ano-01-01')) + 1) as 15dias  
                                      FROM rh_recisao
                                        WHERE id_clt = '$folha[id_clt]'  
                                        AND YEAR(data_demi) = '$ano' AND MONTH(data_demi) = '$mes' AND motivo IN (50,60,61,62,63,64,65,66,81,101,991,992,993) AND status = 1") or die(mysql_error());

        $row_recisao = mysql_fetch_assoc($qr_recisao);
        $num_recisao = mysql_num_rows($qr_recisao);

        if($decimo_terceiro != 1){     

                            if(!empty($num_recisao) and $row_recisao['15dias'] >=15) {

                                $base_calculo_13_salario_mov = $row_recisao['dt_salario'] + $row_recisao['terceiro_ss']; 
                                $base_calculo_13_gps         = $row_recisao['dt_salario'];    
                                $previdencia_dt              = $row_recisao['previdencia_dt'];                        


                            }  else {

                                 $qr_decimo = mysql_query("SELECT B.valor_dt, B.inss, B.ir_dt, B.fgts_dt,B.salliquido, C.terceiro_ss
                                                        FROM rh_folha as A
                                                        INNER JOIN rh_folha_proc as B
                                                        ON A.id_folha = B.id_folha
                                                        INNER JOIN rh_recisao as C
                                                        ON C.id_clt = B.id_clt
                                                        where B.id_clt = '$folha[id_clt]' AND A.terceiro = 1 AND A.tipo_terceiro IN(2,3) AND A.ano = '$ano'");
                                $row_decimo = mysql_fetch_assoc($qr_decimo);

                                $base_calculo_13_salario_mov = $row_decimo['salliquido']; 
                                $base_calculo_13_gps         = $row_decimo['valor_dt'];   
                                $previdencia_dt              = $row_decimo['previdencia_dt']; 
                            }  

                        switch($folha['status_clt']){
                                 //licença maternidade
                                case 50:  $base_calculo_13salario_movimento = $base_calculo_13_salario_mov;
                                break;
                                // dispensa sem justa causa
                                case 63:
                                case 65:
                                case 61:                        
                                case 64: 
                                case 66: $base_calculo_13salario_movimento = $base_calculo_13_salario_mov; 
                                         $base_calculo_13salario_gps       = $base_calculo_13_gps;		
                                break;
                        }            


        } else {  

            if($tipo_dt == 3){    $base_calculo_13salario_movimento = str_replace('.','',str_replace(',','',number_format($folha['salbase'] + $folha['rend'],2,',','.')));
            }    
        } 

        unset($previdencia_dt);

        $valor_descontado_segurado = number_format($folha['folha_proc_diferenca_inss'],2,'','');
        $valor_descontado_segurado = Valor($valor_descontado_segurado);
        $valor_descontado_segurado = sprintf("%015s", $valor_descontado_segurado);
        fwrite($arquivo, $valor_descontado_segurado, 15);

        $remuneracao_base_calculo_contribuicao_previdenciaria = str_replace('.','',$remuneracao_base_calculo_contribuicao_previdenciaria);
        $remuneracao_base_calculo_contribuicao_previdenciaria = sprintf("%015s", $remuneracao_base_calculo_contribuicao_previdenciaria);
        fwrite($arquivo, $remuneracao_base_calculo_contribuicao_previdenciaria, 15);
        unset($remuneracao_base_calculo_contribuicao_previdenciaria);


        if($mes != 12 and !empty($base_calculo_13salario_movimento)){    
                $base_calculo_13salario_gps = '';
        }

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


}
// Fim do Loop dos participantes

// Linha 7 (Registro Tipo 32)


// Consultas para buscar participantes da folha
/*
$qr_folha = mysql_query("SELECT  rh_recisao.data_adm, rh_recisao.data_demi, rh_recisao.nome,  rh_recisao.motivo,  rh_folha_proc.id_clt, rh_folha_proc.id_folha, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.status_clt, rh_folha_proc.sallimpo, rh_folha_proc.sallimpo_real, rh_folha_proc.ids_movimentos, rh_folha_proc.fgts, rh_folha_proc.fgts_ferias, rh_folha_proc.status_clt, rh_folha_proc.dias_trab, rh_folha_proc.data_proc, rh_folha_proc.salbase, rh_folha_proc.folha_proc_desconto_outra_empresa, rh_folha_proc.folha_proc_diferenca_inss, rh_clt.pis   FROM rh_folha_proc 
INNER JOIN rh_clt 
ON rh_folha_proc.id_clt = rh_clt.id_clt
INNER JOIN rh_recisao
ON rh_recisao.id_clt =  rh_folha_proc.id_clt
WHERE rh_folha_proc.id_folha_proc IN($ids) AND YEAR(rh_recisao.data_demi) = '$ano' AND MONTH(rh_recisao.data_demi) = '$mes' AND rh_recisao.motivo IN (50,60,61,62,63,64,65,66,81,101,991,992,993)  ORDER BY REPLACE(REPLACE(rh_clt.pis,'.',''),'-','') ASC") or die(mysql_error());*/

 
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
if($folha['status_clt'] == 50){	
	$qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$folha[id_clt]' AND cod_status = 50");
	$row_evento = mysql_fetch_assoc($qr_eventos);
	$verifica_maternidade = mysql_num_rows($qr_eventos);
}




if(($num_recisao != 0 and $folha['status_clt'] != 10) or $folha['status_clt'] == 50 
    or (in_array($folha['id_clt'], $ids_tranferidos_de[$projeto]) or in_array($folha['id_clt'], $ids_tranferidos_para[$projeto]))){

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
$nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
fwrite($arquivo, $nome_trabalhador, 70);


// Buscando Código de Movimentação
// $codigo_movimentacao = 'Z5';

if(in_array($folha['id_clt'], $ids_tranferidos_de[$projeto])){
    
       $codigo_movimentacao = 'N1';
       $indicador_recolhimento = ' ';
       $dt_mov  = $dt_mov_transferidos[$folha['id_clt']];

}elseif(in_array($folha['id_clt'], $ids_tranferidos_para[$projeto])){
    
       $codigo_movimentacao = 'N3';
       $indicador_recolhimento = ' ';
       $dt_mov  = $dt_mov_transferidos[$folha['id_clt']]; 
    
    
} else {

        $codigo = ($num_recisao != 0) ? $row_recisao['motivo'] : $folha['status_clt'] ;

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

  


        ///DAta movimentação
        if($verifica_maternidade != 0) {	
                $dt_mov = $row_evento['data'];	
        } else {	
                $dt_mov = $row_recisao['data_demi'];
        }


}

$codigo_movimentacao = sprintf("%-2s", $codigo_movimentacao);
fwrite($arquivo, $codigo_movimentacao, 2);

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

unset($num_recisao);
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








// Linha 8 (Registro Tipo 90)

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

//}

// Fim para Linha 8
?>