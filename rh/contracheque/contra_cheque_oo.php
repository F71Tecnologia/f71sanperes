<?php

/*
 * PHP-DOC - contra_cheque_oo.php 
 * 
 * Procedimentos de instânciamento de classes e carga de variáveis com valores
 *
 * ??/??/????
 * 
 * @package ContraCheque 
 * @access public   
 * 
 * @version
 * 
 * Versão: 3.0.0000 - ??/??/???? - N. Def. - Versão Inicial
 * Versão: 3.0.6292 - 11/02/2016 - Jacques - Carga de variável com número de dependentes para a classe de contra-cheque.
 * 
 */

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/EmpresaClass.php");
include_once("../../classes/FolhaClass.php");
include_once("../fpdf/fpdf.php");
include_once("../../classes/ContraChequeClass.php");
include_once("../../funcoes.php");


//VARIÁVEIS DE AMBIENTE
$dadosEmpresa   = array();
$dadosFolha     = array();
$dadosClt       = array();
$dadosFolhaClt  = array();

// RECEBENDO VARIAVEIS
//$enc        = $_REQUEST['enc'];
//$enc        = str_replace("--", "+", $enc);
//$link       = decrypt($enc);
//$decript    = explode("&", $link);
//$regiao     = $decript[0];
//$clt        = $decript[1];
//$id_folha   = $decript[2];
$enc        = $_REQUEST['enc'];
$ini        = $_REQUEST['ini'];//passar 00
$id_funcao  = $_REQUEST['fun'];
$enc        = str_replace("--", "+", $enc);
$link       = decrypt($enc);
$decript    = explode("&", $link);
$regiao     = ($_REQUEST['id_regiao']) ? $_REQUEST['id_regiao'] : $decript[0];
$idClt      = ($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : $decript[1];
$id_folha   = ($_REQUEST['id_folha']) ? $_REQUEST['id_folha'] : $decript[2];

if($_COOKIE['debug'] == 666) {
    print_array($decript);exit;
}

$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$RowFolha = mysql_fetch_array($REFolha);
$idfolha    = $id_folha;

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto= '$RowFolha[projeto]'");
$row_projeto= mysql_fetch_assoc($qr_projeto);
$projeto        = $row_projeto['id_projeto'];

//INSTÂNCIAS
$folha = new Folha();

//DADOS DA EMPRESA
$resourceEmpresa = getEmpresa($regiao, $projeto);
while($rowsEmpresas = mysql_fetch_assoc($resourceEmpresa)){
    $dadosEmpresa = $rowsEmpresas;
}
//
//echo "<pre>";
//    print_r($dadosEmpresa);
//echo "</pre>";

//DADOS DA FOLHA
$resource_folha = $folha->getFolhaById($idfolha, array('id_folha','mes','ano','terceiro','tipo_terceiro'));
while($rowsFolha = mysql_fetch_assoc($resource_folha)){
    $dadosFolha = $rowsFolha;
}

$clts = $folha->getCltsByIdFolha($idfolha, $idClt, $ini, $id_funcao, true);

if(count($clts) > 0){
    $contracheque = new ContraCheque(true);
}

if($_REQUEST['validate']){
    include_once('cabecalho_valida.php');
}

foreach ($clts as $clt) {
    
    $dadosClt       = array();
    $dadosFolhaClt  = array();
 
//if($_COOKIE['logado'] == 179){
//    echo "<pre>";
//        print_r($movimentos);
//    echo "</pre>";
//} 
    //DADOS DO CLTs
    $resource_clt = $folha->getDadosClt($clt, $dadosFolha['mes'], $dadosFolha['ano']);
    while($rowClt = mysql_fetch_assoc($resource_clt)){
        $dadosClt = $rowClt;
    } 

    //DADOS DA FOLHA PROCESSADA
    $resource_dados_folha = $folha->getDadosFolhaById($idfolha, null, $clt);
    while($rowDadosFolha = mysql_fetch_assoc($resource_dados_folha)){
        $dadosFolhaClt = $rowDadosFolha;
    }
    
    //DADOS FINANCEIRO
    $folha->getFichaFinanceira($clt, $dadosFolha['ano'], $dadosFolha['mes'], $dadosFolha['terceiro']);
    $movimentos = $folha->getDadosFicha();

    if($_COOKIE['logado'] == 179 || $_COOKIE['debug'] == 666){
//        echo "<pre>";
//            print_r($movimentos);
//        echo "</pre>";
    }


    //MOVIMENTOS DE CRÉDITO
    $mov_credito = array(80045);
    $mov_c = $folha->getMovCredito();
    while($rows_credito = mysql_fetch_assoc($mov_c)){
        $mov_credito[] = $rows_credito['cod'];
    }
 
//if($_COOKIE['logado'] == ){
//    
//}

//DADOS REGIÃO
$qry_regiao = "SELECT * FROM regioes WHERE id_regiao = {$regiao}";
$sql_regiao = mysql_query($qry_regiao) or die(mysql_error);
$row_regiao = mysql_fetch_assoc($sql_regiao);
 
    //if($_COOKIE['logado'] == 179){
    //    echo "<pre>";
    //        print_r($mov_credito);
    //    echo "</pre>";
    //} 

    //MOVIMENTOS DE DEBITO
    $mov_debito = array(5070,5037,5035,5036);
    $mov_d = $folha->getMovDebito();
    while($rows_debito = mysql_fetch_assoc($mov_d)){
        $mov_debito[] = $rows_debito['cod'];
    }

    $mov = array();
    foreach ($movimentos as $key => $values){
        if(in_array($key, $mov_credito)){
            $mov["credito"][$key] = $values;
        }else if(in_array($key,$mov_debito)){
            $mov["debito"][$key] = $values;
        }
    }

    //DADOS REGIÃO
    $qry_regiao = "SELECT * FROM regioes WHERE id_regiao = {$regiao}";
    $sql_regiao = mysql_query($qry_regiao) or die(mysql_error);
    $row_regiao = mysql_fetch_assoc($sql_regiao);

    //CRIANDO UM ARRAY MAIS LIMPO
    $dados = array(
        "logo"              => "../../imagens/logomaster".$row_regiao['id_master'].".gif",
        "empresa"           => $dadosEmpresa['razao'],
        "cnpj"              => $dadosEmpresa['cnpj'],
        "endereco"          => $dadosEmpresa['logradouro'],
        "numero"            => ($dadosEmpresa['numero'] > 0) ? $dadosEmpresa['numero'] : 'S/N',
        "complemento"       => $dadosEmpresa['complemento'],
        "bairro"            => $dadosEmpresa['bairro'],
        "cidade"            => $dadosEmpresa['cidade'],
        "uf"                => $dadosEmpresa['uf'],
        "cep"               => $dadosEmpresa['cep'],
        "telefone"          => $dadosEmpresa['tel'],
        "fax"               => $dadosEmpresa['fax'],
        "mes"               => $dadosFolha['mes'],
        "ano"               => $dadosFolha['ano'],
        "nome"              => $dadosClt['nome'],
        
        "status_especifica" => $dadosClt['status_especifica'],
        "status"            => $dadosClt['status_clt'],
        
        "id"                => $dadosClt['id_clt'],
        "cod_funcionario"   => $dadosClt['matricula'],
        "cargo"             => $dadosClt['nome_curso'],
        "data_admissao"     => $dadosClt['data_entrada'],
        "unidade"           => $dadosClt['locacao'],
        "pis"               => $dadosClt['pis'],
        "cpf"               => $dadosClt['cpf'],
        "rg"                => $dadosClt['rg'],
        "carteira_trabalho" => $dadosClt['ctps'],
        "serie_carteira_trabalho" => $dadosClt['serie_ctps'],
        "banco"             => $dadosClt['razao'],
        "agencia"           => $dadosClt['agencia'],
        "conta_corrente"    => $dadosClt['conta'],
        "valor_ferias"      => $dadosFolhaClt['valor_ferias'],
        "valor_bruto"       => $dadosFolhaClt['salbase'],
        "rend"              => $dadosFolhaClt['rend'],
        "base_inss"         => $dadosFolhaClt['base_inss'],
        "base_irrf"         => $dadosFolhaClt['base_irrf'],
        "base_fgts"         => $dadosFolhaClt['base_fgts'],
        "dependentes"       => $dadosFolhaClt['DEP_IRRF'],
        "salario_base"      => $dadosFolhaClt['sallimpo'],
        
        "salario_liq"       => $dadosFolhaClt['salliquido'],
        "t_imprenda"        => $dadosFolhaClt['t_imprenda'],
        "fgts"              => $dadosFolhaClt['fgts'],
        "movimentos"        => $mov
     );

    
//    $contracheque->setDuplicado(true);
//    $contracheque->setTipo(array("pdf"));
    $contracheque->setDados($dados);
    $contracheque->getContraCheque();
}

if($_REQUEST['validate']){       
    include_once('footer_valida.php');
}

if((!isset($_REQUEST['validate']))){
    if(count($clts) > 0){
        $contracheque->closePdf();
    } else if(!empty ($id_funcao)){
        echo 'Nenhum clt para esta função';
    }
}