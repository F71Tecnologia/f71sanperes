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
    $letras = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    foreach ($letras as $letra) {
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
        'o.' => '/&ordm;/',
        '' => '/&acute;/'
    );
    return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
}

// Buscando Ids de Sefips Anteriores
//if($parte_sefip == 4 or $parte_sefip == 5 or isset($sefip_folha)) {
$qr_sefips_anteriores = mysql_query("SELECT * FROM sefip");
settype($ids_anteriores, 'array');
while ($sefip_anterior = mysql_fetch_assoc($qr_sefips_anteriores)) {
    $qr_folha = mysql_query("SELECT * FROM folha_cooperado
										 WHERE status = '3'
										   AND mes = '$sefip_anterior[mes]'
										   AND ano = '$sefip_anterior[ano]'");
    while ($folha = mysql_fetch_assoc($qr_folha)) {
        if (!in_array($folha['id_folha_proc'], $ids_anteriores)) {
            $ids_anteriores[] = $folha['id_folha_proc'];
        }
    }
}
//}
// Consulta da Empresa

$qr_folha2 = mysql_query("SELECT * FROM folha_cooperado INNER JOIN folhas ON folha_cooperado.id_folha = folhas.id_folha WHERE folha_cooperado.id_folha_pro IN($ids) ORDER BY folhas.id_folha ASC") or die(mysql_error());
$row_folha2 = mysql_fetch_assoc($qr_folha2);

$qr_cooperativa = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '35'");
$cooperativa = mysql_fetch_assoc($qr_cooperativa);



// Linha 1 (Registro Tipo 00)
//if($parte_sefip == 1 or isset($sefip_folha)) {
//1
$tipo_registro = '00';
$tipo_registro = sprintf("%02s", $tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

//2
$brancos = NULL;
$brancos = sprintf("%51s", $brancos);
fwrite($arquivo, $brancos, 51);

//3
$tipo_remessa = '1';
$tipo_remessa = sprintf("%01s", $tipo_remessa);
fwrite($arquivo, $tipo_remessa, 1);

//4
$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

//5
$inscricao_responsavel = RemoveCaracteres($cooperativa['cnpj']);
$inscricao_responsavel = str_replace('.', '', $inscricao_responsavel);
$inscricao_responsavel = substr($inscricao_responsavel, 0, 14);
$inscricao_responsavel = sprintf("%014s", $inscricao_responsavel);
fwrite($arquivo, $inscricao_responsavel, 14);


//6
//Razão
$nome_responsavel = RemoveAcentos($cooperativa['fantasia']);
$nome_responsavel = substr($nome_responsavel, 0, 30);
$nome_responsavel = sprintf("%-30s", $nome_responsavel);
fwrite($arquivo, $nome_responsavel, 30);

//7
$nome_pessoa_contato = RemoveAcentos($cooperativa['contato']);
$nome_pessoa_contato = substr($nome_pessoa_contato, 0, 20);
$nome_pessoa_contato = sprintf("%-20s", $nome_pessoa_contato);
fwrite($arquivo, $nome_pessoa_contato, 20);


//8
$logradouro = RemoveEspacos($cooperativa['endereco']);
$logradouro = explode('-', RemoveAcentos($logradouro));
$logradouro = substr(RemoveCaracteres($logradouro[0]), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

//9
$bairro = RemoveAcentos($cooperativa['bairro']);
//$bairro = str_replace(' ', '', $bairro[1]);
$bairro = RemoveCaracteres($bairro);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

//10
$cep = RemoveCaracteres($cooperativa['cooperativa_cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

//11
$cidade = RemoveAcentos($cooperativa['cidade']);
//cidade = str_replace(' ', '', $cidade[2]);
//$cidade = substr(RemoveCaracteres($cidade), 0, 20);
$cidade = RemoveCaracteres($cidade);
$cidade = sprintf("%-20s", $cidade);
fwrite($arquivo, $cidade, 20);

//12
$unidade_federecao = $cooperativa['cooperativa_uf'];
//$unidade_federecao = str_replace(' ', '', $unidade_federecao[3]);
//$unidade_federecao = substr($unidade_federecao, 0, 2);
$unidade_federecao = sprintf("%02s", $unidade_federecao);
fwrite($arquivo, $unidade_federecao, 2);

//13
$telefone_contato = RemoveCaracteres($cooperativa['tel']);
$telefone_contato = sprintf("%12s", $telefone_contato);
fwrite($arquivo, $telefone_contato, 12);

//14
$endereco_internet_contato = $cooperativa['email'];
$endereco_internet_contato = sprintf("%-60s", $endereco_internet_contato);
fwrite($arquivo, $endereco_internet_contato, 60);

//15
$competencia = $ano . $mes;
$competencia = sprintf("%06s", $competencia);
fwrite($arquivo, $competencia, 6);


//16
$codigo_recolhimento = '211';
$codigo_recolhimento = sprintf("%03s", $codigo_recolhimento);
fwrite($arquivo, $codigo_recolhimento, 3);


//17
$indicador_recolhimento_fgts = sprintf("%1s", '');
fwrite($arquivo, $indicador_recolhimento_fgts, 1);

//18
$modalidade_arquivo = '1';
$modalidade_arquivo = sprintf("%1s", $modalidade_arquivo);
fwrite($arquivo, $modalidade_arquivo, 1);


//19
$data_recolhimento = sprintf("%8s", '');
fwrite($arquivo, $data_recolhimento, 8);

//20
$indicador_recolhimento = sprintf("%1s", '1');
fwrite($arquivo, $indicador_recolhimento, 1);


if ($indicador_recolhimento == 2) {
    $data_recolhimento = $data;
} else {
    $data_recolhimento = NULL;
}

//21
$data_recolhimento = sprintf("%8s", $data_recolhimento);
fwrite($arquivo, $data_recolhimento, 8);

//22
$indice_recolhimento = NULL;
$indice_recolhimento = sprintf("%7s", $indice_recolhimento);
fwrite($arquivo, $indice_recolhimento, 7);

//23
$tipo_inscricao_fornecedor = '1';
$tipo_inscricao_fornecedor = sprintf("%01s", $tipo_inscricao_fornecedor);
fwrite($arquivo, $tipo_inscricao_fornecedor, 1);

//24
$inscricao_fornecedor = '27915735000100';
$inscricao_fornecedor = sprintf("%14s", $inscricao_fornecedor);
fwrite($arquivo, $inscricao_fornecedor, 14);

//25
$brancos = NULL;
$brancos = sprintf("%18s", $brancos);
fwrite($arquivo, $brancos, 18);

//26
$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");

//}
// Fim para Linha 1
// Linha 2 (Registro Tipo 10)
//if($parte_sefip == 2 or isset($sefip_folha)) {
//1
$tipo_registro = '10';
$tipo_registro = sprintf("%02s", $tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

//2
$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

//3
$inscricao_empresa = RemoveCaracteres($cooperativa['cnpj']);
$inscricao_empresa = str_replace('.', '', $inscricao_empresa);
$inscricao_empresa = substr($inscricao_empresa, 0, 14);
$inscricao_empresa = sprintf("%014s", $inscricao_empresa);
fwrite($arquivo, $inscricao_empresa, 14);

//4
$zeros = NULL;
$zeros = sprintf("%036s", $zeros);
fwrite($arquivo, $zeros, 36);

//5
//RAZÃO SOCIAL
$nome_empresa = RemoveAcentos($cooperativa['fantasia']);
$nome_empresa = substr($nome_empresa, 0, 40);
$nome_empresa = sprintf("%-40s", $nome_empresa);
fwrite($arquivo, $nome_empresa, 40);

//6
$logradouro = RemoveEspacos($cooperativa['endereco']);
$logradouro = explode('-', RemoveAcentos($logradouro));
$logradouro = substr(RemoveCaracteres($logradouro[0]), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

//7
$bairro = RemoveAcentos($cooperativa['bairro']);
//$bairro = str_replace(' ', '', $bairro[1]);
$bairro = RemoveCaracteres($bairro);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);


//8
$cep = RemoveCaracteres($cooperativa['cooperativa_cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

//9
$cidade = RemoveAcentos($cooperativa['cidade']);
//$cidade = str_replace(' ', '', $cidade[2]);
$cidade = RemoveCaracteres($cidade);
$cidade = sprintf("%-20s", RemoveAcentos($cidade));
fwrite($arquivo, $cidade, 20);

//10
$unidade_federecao = $cooperativa['cooperativa_uf'];
//$unidade_federecao = str_replace(' ', '', $unidade_federecao[3]);
//$unidade_federecao = substr($unidade_federecao, 0, 2);
$unidade_federecao = sprintf("%02s", $unidade_federecao);
fwrite($arquivo, $unidade_federecao, 2);

//11
$telefone_contato = RemoveCaracteres($cooperativa['tel']);
$telefone_contato = sprintf("%12s", $telefone_contato);
fwrite($arquivo, $telefone_contato, 12);

//12
$indicador_alteracao_endereco = 'n';
$indicador_alteracao_endereco = sprintf("%1s", $indicador_alteracao_endereco);
fwrite($arquivo, $indicador_alteracao_endereco, 1);

//13
$cnae = $cooperativa['cooperativa_cnae'];
$cnae = sprintf("%7s", $cnae);
fwrite($arquivo, $cnae, 7);

//14
$indicador_alteracao_cnae = 'n';
$indicador_alteracao_cnae = sprintf("%1s", $indicador_alteracao_cnae);
fwrite($arquivo, $indicador_alteracao_cnae, 1);

//15
$aliquota_rat = '10';
$aliquota_rat = sprintf("%2s", $aliquota_rat);
fwrite($arquivo, $aliquota_rat, 2);

//16
$codigo_centralizacao = '0';
$codigo_centralizacao = sprintf("%1s", $codigo_centralizacao);
fwrite($arquivo, $codigo_centralizacao, 1);

//17
$SIMPLES = '1';
$SIMPLES = sprintf("%1s", $SIMPLES);
fwrite($arquivo, $SIMPLES, 1);

//18
$FPAS = $cooperativa['cooperativa_fpas'];
$FPAS = sprintf("%3s", $FPAS);
fwrite($arquivo, $FPAS, 3);

//19
$codigo_outras_entidades = '0115';
$codigo_outras_entidades = sprintf("%04s", $codigo_outras_entidades);
fwrite($arquivo, $codigo_outras_entidades, 4);

//20
$codigo_pagamento_GPS = '2127';
$codigo_pagamento_GPS = sprintf("%4s", $codigo_pagamento_GPS);
fwrite($arquivo, $codigo_pagamento_GPS, 4);

//21
$percentual_isencao_filantropia = NULL;
$percentual_isencao_filantropia = sprintf("%5s", $percentual_isencao_filantropia);
fwrite($arquivo, $percentual_isencao_filantropia, 5);

if (isset($_GET['folha'])) {
    $familia = @mysql_result(mysql_query("SELECT total_familia FROM rh_folha WHERE id_folha = '$_GET[folha]'"), 0);
} else {
    $familia = @mysql_result(mysql_query("SELECT SUM(total_familia) AS total_familia FROM rh_folha WHERE status = '3' AND mes = '$mes' AND ano = '$ano' AND regiao != '36'"), 0);
}

//22
$salario_familia = number_format($familia, 2, '', '');
$salario_familia = Valor($salario_familia);
$salario_familia = sprintf("%015s", 0); //$salario_familia = sprintf("%015s", $salario_familia);
fwrite($arquivo, $salario_familia, 15);

//23
$salario_maternidade = NULL;
$salario_maternidade = sprintf("%015s", $salario_maternidade);
fwrite($arquivo, $salario_maternidade, 15);

//24
$contrib_desc_empregado = NULL;
$contrib_desc_empregado = sprintf("%015s", $contrib_desc_empregado);
fwrite($arquivo, $contrib_desc_empregado, 15);

//25
$indicador_valor_negativo = NULL;
$indicador_valor_negativo = sprintf("%01s", $indicador_valor_negativo);
fwrite($arquivo, $indicador_valor_negativo, 1);

//26
$valor_devido_previdencia = NULL;
$valor_devido_previdencia = sprintf("%014s", $valor_devido_previdencia);
fwrite($arquivo, $valor_devido_previdencia, 14);

//27
$banco = NULL;
$banco = sprintf("%3s", $banco);
fwrite($arquivo, $banco, 3);

//28
$agencia = NULL;
$agencia = sprintf("%4s", $agencia);
fwrite($arquivo, $agencia, 4);

//29
$conta = NULL;
$conta = sprintf("%9s", $conta);
fwrite($arquivo, $conta, 9);

//30
$zeros = NULL;
$zeros = sprintf("%045s", $zeros);
fwrite($arquivo, $zeros, 45);

//31
$brancos = NULL;
$brancos = sprintf("%4s", $brancos);
fwrite($arquivo, $brancos, 4);

//32
$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");

//}
// Fim para Linha 2
// Linha 3 (Registro Tipo 20)
$qr_folha3 = mysql_query("SELECT * FROM folha_cooperado INNER JOIN folhas ON folha_cooperado.id_folha = folhas.id_folha WHERE folha_cooperado.id_folha_pro IN($ids) ORDER BY folhas.id_folha ASC") or die(mysql_error());
$row_folha3 = mysql_fetch_assoc($qr_folha3);


$qr_cooperativa = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$row_folha3[coop]'");
$row_tomador = mysql_fetch_assoc($qr_cooperativa);




$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$row_master[id_master]'");


$row_empresa = mysql_fetch_assoc($qr_empresa);



//1
$tipo_registro = '20';
$tipo_registro = sprintf("%02s", $tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

//2
$tipo_inscricao = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);

//3
$inscricao_empresa = RemoveCaracteres($cooperativa['cnpj']);
$inscricao_empresa = str_replace('.', '', $inscricao_empresa);
$inscricao_empresa = substr($inscricao_empresa, 0, 14);
$inscricao_empresa = sprintf("%014s", $inscricao_empresa);
fwrite($arquivo, $inscricao_empresa, 14);


//4
$tipo_inscricao_tomador = '1';
$tipo_inscricao = sprintf("%01s", $tipo_inscricao);
fwrite($arquivo, $tipo_inscricao, 1);


//5
$inscricao_empresa = RemoveCaracteres($row_empresa['cnpj']);
$inscricao_empresa = str_replace('.', '', $inscricao_empresa);
$inscricao_empresa = substr($inscricao_empresa, 0, 14);
$inscricao_empresa = sprintf("%014s", $inscricao_empresa);
fwrite($arquivo, $inscricao_empresa, 14);

//6
$zeros = NULL;
$zeros = sprintf("%021s", $zeros);
fwrite($arquivo, $zeros, 21);


//7
if($_COOKIE['logado'] == 179){
    echo "<pre>";
        print_r($row_tomador);
    echo "</pre>";
}

$nome_empresa = RemoveAcentos($row_empresa['nome']);
$nome_empresa = RemoveAcentos($row_tomador['fantasia']);


$nome_empresa = substr($nome_empresa, 0, 40);
$nome_empresa = sprintf("%-40s", $nome_empresa);
fwrite($arquivo, $nome_empresa, 40);

//8
$logradouro = RemoveEspacos($row_empresa['endereco']);
$logradouro = RemoveEspacos($row_tomador['endereco']);


$logradouro = explode('-', RemoveAcentos($logradouro));
$logradouro = substr(RemoveCaracteres($logradouro[0]), 0, 50);
$logradouro = sprintf("%-50s", $logradouro);
fwrite($arquivo, $logradouro, 50);

//9
$bairro = explode('-', RemoveAcentos($row_empresa['endereco']));
$bairro = $row_tomador['bairro'];

$bairro = str_replace(' ', '', $bairro);
$bairro = substr(RemoveCaracteres($bairro), 0, 20);
$bairro = sprintf("%-20s", $bairro);
fwrite($arquivo, $bairro, 20);

//10
$cep = RemoveCaracteres($row_empresa['cep']);
$cep = substr($cep, 0, 8);
$cep = sprintf("%08s", $cep);
fwrite($arquivo, $cep, 8);

//11
$cidade = explode('-', RemoveAcentos($row_empresa['endereco']));
$cidade = RemoveAcentos($row_tomador['cidade']);

$cidade = str_replace(' ', '', $cidade);
$cidade = substr(RemoveCaracteres($cidade), 0, 20);
$cidade = sprintf("%-20s", $cidade);
fwrite($arquivo, $cidade, 20);

//12
$unidade_federecao = $row_empresa['uf'];
$unidade_federecao = $row_tomador['cooperativa_uf'];
$unidade_federecao = sprintf("%02s", $unidade_federecao);
fwrite($arquivo, $unidade_federecao, 2);


//13
$codigo_pg_gps = NULL;
$codigo_pg_gps = sprintf("%4s", $codigo_pg_gps);
fwrite($arquivo, $codigo_pg_gps, 4);


//14
$salario_familia = NULL;
$salario_familia = sprintf("%015s", $salario_familia);
fwrite($arquivo, $salario_familia, 15);


//15
$contrib_desc_empregado = NULL;
$contrib_desc_empregado = sprintf("%015s", $contrib_desc_empregado);
fwrite($arquivo, $contrib_desc_empregado, 15);

//16
$indicador_valor = NULL;
$indicador_valor = sprintf("%01s", $indicador_valor);
fwrite($arquivo, $indicador_valor, 1);

//17
$valor_devido_prev = NULL;
$valor_devido_prev = sprintf("%014s", $valor_devido_prev);
fwrite($arquivo, $valor_devido_prev, 14);

//18
$valor_retencao = NULL;
$valor_retencao = sprintf("%015s", $valor_retencao);
fwrite($arquivo, $valor_retencao, 15);

//19
$valor_fatura = '';
$valor_fatura = sprintf("%015s", $valor_fatura);
fwrite($arquivo, $valor_fatura, 15);

//20

$zeros = NULL;
$zeros = sprintf("%045s", $zeros);
fwrite($arquivo, $zeros, 45);

//21
$brancos = NULL;
$brancos = sprintf("%42s", $brancos);
fwrite($arquivo, $brancos, 42);

//22
$final_linha = '*';
$final_linha = sprintf("%01s", $final_linha);
fwrite($arquivo, $final_linha, 1);

fwrite($arquivo, "\r\n");

//}
// Fim para Linha 3
// Linha 6 (Registro Tipo 30)
// Consultas para buscar participantes da folha e od dados dos participantes
$qr_folha = mysql_query("SELECT folha_cooperado.id_autonomo, folha_cooperado.regiao, folha_cooperado.salario, folha_cooperado.ferias, autonomo.id_curso, autonomo.rh_cbo,folha_cooperado.inss AS inss_folha,autonomo.inss, folha_cooperado.adicional,
							REPLACE( REPLACE( autonomo.pis, '.', '' ) , '-', '' ) AS pis, autonomo.data_entrada, autonomo.nome, autonomo.campo1, autonomo.serie_ctps, autonomo.data_nasci
							FROM folha_cooperado
							INNER JOIN folhas ON folha_cooperado.id_folha = folhas.id_folha
							INNER JOIN autonomo ON folha_cooperado.id_autonomo = autonomo.id_autonomo
							WHERE folha_cooperado.id_folha_pro
							IN ($ids)
							ORDER BY  pis,autonomo.data_entrada  ASC
							") or die(mysql_error());
$numero_folha = mysql_num_rows($qr_folha);



// Loop dos participantes
while ($folha = mysql_fetch_assoc($qr_folha)) {

    $qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$folha[id_curso]'");
    $num_curso = mysql_num_rows($qr_curso);
    $curso = mysql_fetch_assoc($qr_curso);

    $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$folha[rh_cbo]'");
    $row_cbo = mysql_fetch_assoc($qr_cbo);
    $num_cbo = mysql_num_rows($qr_cbo);

    if (!empty($num_curso)) {

        $qr_cbo = mysql_query("SELECT cod FROM rh_cbo WHERE id_cbo = '$curso[cbo_codigo]'");
        $row_cbo = mysql_fetch_assoc($qr_cbo);
        $cbo = $row_cbo['cod'];
    } else {
        $cbo = $row_cbo['cod'];
    }


// Inicio
//1
    $tipo_registro = '30';
    $tipo_registro = sprintf("%02s", $tipo_registro);
    fwrite($arquivo, $tipo_registro, 2);

//2
    $tipo_inscricao = '1';
    $tipo_inscricao = sprintf("%01s", $tipo_inscricao);
    fwrite($arquivo, $tipo_inscricao, 1);

//3
    $inscricao_empresa = RemoveCaracteres($cooperativa['cnpj']);
    $inscricao_empresa = str_replace('.', '', $inscricao_empresa);
    $inscricao_empresa = substr($inscricao_empresa, 0, 14);
    $inscricao_empresa = sprintf("%014s", $inscricao_empresa);
    fwrite($arquivo, $inscricao_empresa, 14);

//4
    $tipo_inscricao_tomador = '1';
    $tipo_inscricao_tomador = sprintf("%1s", $tipo_inscricao_tomador);
    fwrite($arquivo, $tipo_inscricao_tomador, 1);

//5
    $inscricao_tomador = RemoveCaracteres($row_empresa['cnpj']);
    $inscricao_tomador = str_replace('.', '', $inscricao_tomador);
    $inscricao_tomador = substr($inscricao_tomador, 0, 14);
    $inscricao_tomador = sprintf("%14s", $inscricao_tomador);
    fwrite($arquivo, $inscricao_tomador, 14);

//6
    $pis_pasep_ci = RemoveCaracteres($folha['pis']);
    $pis_pasep_ci = str_replace('.', '', $pis_pasep_ci);
    $pis_pasep_ci = sprintf("%011s", $pis_pasep_ci);
    fwrite($arquivo, $pis_pasep_ci, 11);

//7
    $data_admissao = '';
    $data_admissao = sprintf("%8s", $data_admissao);
    fwrite($arquivo, $data_admissao, 8);

//8
    $categoria_trabalhador = '24';
    $categoria_trabalhador = sprintf("%02s", $categoria_trabalhador);
    fwrite($arquivo, $categoria_trabalhador, 2);

//9
    $nome_trabalhador = RemoveAcentos($folha['nome']);
    $nome_trabalhador = RemoveCaracteres($nome_trabalhador);
    $nome_trabalhador = sprintf("%-70s", $nome_trabalhador);
    fwrite($arquivo, $nome_trabalhador, 70);

//10
    $matricula_empregado = sprintf("%11s", NULL);  //$matricula_empregado = sprintf("%11s", $matricula_empregado);
    fwrite($arquivo, $matricula_empregado, 11);

    if (empty($folha['campo1'])) {
        $numero_carteira_trabalho = $a++;
    } else {
        $numero_carteira_trabalho = $folha['campo1'];
    }

    if (empty($folha['serie_ctps'])) {
        $serie_carteira_trabalho = $b++;
    } else {
        $serie_carteira_trabalho = $folha['serie_ctps'];
    }

//11
    $numero_ctps = RemoveCaracteres($numero_carteira_trabalho);
    $numero_ctps = RemoveEspacos($numero_ctps);
    $numero_ctps = RemoveLetras($numero_ctps);
    $numero_ctps = sprintf("%7s", NULL); //$numero_ctps = sprintf("%07s", $numero_ctps);
    fwrite($arquivo, $numero_ctps, 7);

//12
    $serie_ctps = RemoveCaracteres($serie_carteira_trabalho);
    $serie_ctps = RemoveEspacos($serie_ctps);
    $serie_ctps = RemoveLetras($serie_ctps);
    $serie_ctps = sprintf("%5s", NULL); //$serie_ctps = sprintf("%05s", $serie_ctps);
    fwrite($arquivo, $serie_ctps, 5);


//13
    $data_opcao = implode('', array_reverse(explode('-', $folha['data_entrada'])));
    $data_opcao = sprintf("%8s", NULL); //$data_opcao = sprintf("%08s", $data_opcao);
    fwrite($arquivo, $data_opcao, 8);

//14
    $data_nascimento = implode('', array_reverse(explode('-', $folha['data_nasci'])));
    $data_nascimento = sprintf("%8s", NULL); //$data_nascimento = sprintf("%08s", $data_nascimento);
    fwrite($arquivo, $data_nascimento, 8);

//15
    $cbo = RemoveCaracteres($cbo);
    $cbo = '0' . str_replace('.', '', substr($cbo, 0, 4));
    $cbo = sprintf("%05s", $cbo);
    fwrite($arquivo, $cbo, 5);




    $valor_sefip = $folha['salario'] + $folha['adicional'];

    $remuneracao_sem_13 = number_format($valor_sefip, 2, '', '');
    $remuneracao_sem_13 = Valor($remuneracao_sem_13);
    $remuneracao_sem_13 = sprintf("%015s", $remuneracao_sem_13); //$remuneracao_sem_13 = sprintf("%015s", $remuneracao_sem_13);
    fwrite($arquivo, $remuneracao_sem_13, 15);




// Remuneração com 13º
    $remuneracao_13 = number_format($remuneracao_13, 2, '', '');
    $remuneracao_13 = Valor($remuneracao_13);
    $remuneracao_13 = sprintf("%015s", 0); //$remuneracao_13 = sprintf("%015s", $remuneracao_13);
    fwrite($arquivo, $remuneracao_13, 15);

    $classe_contribuicao = NULL;
    $classe_contribuicao = sprintf("%2s", $classe_contribuicao); //$classe_contribuicao = sprintf("%2s", $classe_contribuicao);
    fwrite($arquivo, $classe_contribuicao, 2);

// Verificando se não tem desconto de inss
    $ocorrencia = NULL;

    if ($folha['inss_folha'] == 0) {
        $ocorrencia = '05';
    }

    $ocorrencia = sprintf("%2s", $ocorrencia); //$ocorrencia = sprintf("%2s", $ocorrencia);
    fwrite($arquivo, $ocorrencia, 2);
//


    $valor_descontado_segurado = '';
    $valor_descontado_segurado = sprintf("%015s", 0); //$valor_descontado_segurado = sprintf("%015s", $valor_descontado_segurado);
    fwrite($arquivo, $valor_descontado_segurado, 15);


    $remuneracao_base_calculo_contribuicao_previdenciaria = '';
    $remuneracao_base_calculo_contribuicao_previdenciaria = sprintf("%015s", 0); //$remuneracao_base_calculo_contribuicao_previdenciaria = sprintf("%015s", $remuneracao_base_calculo_contribuicao_previdenciaria);
    fwrite($arquivo, $remuneracao_base_calculo_contribuicao_previdenciaria, 15);

// Verificando se tem rescisao para fazer calculo

    /* if($folha['ferias'] == 2) {
      $base_calculo_13salario_movimento = Valor($folha['a4002']);
      }
     */
    $base_calculo_13salario_movimento = sprintf("%015s", 0); //$base_calculo_13salario_movimento = sprintf("%015s", $base_calculo_13salario_movimento);
    fwrite($arquivo, $base_calculo_13salario_movimento, 15);

//

    $base_calculo_13salario_gps = '';
    $base_calculo_13salario_gps = sprintf("%015s", 0); //$base_calculo_13salario_gps = sprintf("%015s", $base_calculo_13salario_gps);
    fwrite($arquivo, $base_calculo_13salario_gps, 15);

    $brancos = NULL;
    $brancos = sprintf("%98s", $brancos);
    fwrite($arquivo, $brancos, 98);

    $final_linha = '*';
    $final_linha = sprintf("%01s", $final_linha);
    fwrite($arquivo, $final_linha, 1);

    fwrite($arquivo, "\r\n");

    unset($valor_sefip, $remuneracao_13, $valor_ferias, $ferias);
}

// Resetando todos os valores
unset($qr_folha_pai);
unset($folha_pai);
unset($qr_folha);
unset($folha);

//}
// Fim para Linha 6 
// Linha 8 (Registro Tipo 90)
//if($parte_sefip == 7 or isset($sefip_folha)) { 

$tipo_registro = '90';
$tipo_registro = sprintf("%02s", $tipo_registro);
fwrite($arquivo, $tipo_registro, 2);

$tipo_registro = '9';
for ($i = 0; $i < 51; $i++) {
    $tipo_registro .= 9;
}
$tipo_registro = sprintf("%51s", $tipo_registro);
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