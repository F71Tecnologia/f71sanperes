<?php

error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/ContabilImpostosClass.php");
include("../../classes/ContabilImpostosAssocClass.php");

$usuario = carregaUsuario();

$objImpostos = new ContabilImposto();
$objImpAssoc = new ContabilImpostosAssocClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'consultar_prestadores') {
    $projeto = (isset($_REQUEST['id_projeto']) && $_REQUEST['id_projeto'] > 0) ? "AND a.id_projeto = {$_REQUEST['id_projeto']}" : '';
    $regiao = (isset($_REQUEST['id_regiao']) && $_REQUEST['id_regiao'] > 0) ? "AND a.id_regiao = {$_REQUEST['id_regiao']}" : exit();

    $query = "SELECT a.id_prestador,a.c_razao AS razao, a.c_fantasia AS fantasia, a.c_cnpj AS cnpj, 
                DATE_FORMAT(a.contratado_em,'%d/%m/%Y') AS contratado_em, 
                DATE_FORMAT(a.encerrado_em,'%d/%m/%Y') AS encerrado_em,
                b.id_projeto, b.nome AS nome_projeto 
                FROM prestadorservico AS a 
                INNER JOIN projeto AS b ON a.id_projeto = b.id_projeto
                WHERE a.encerrado_em > NOW() AND a.prestador_tipo = 4 $projeto $regiao 
                ORDER BY a.id_projeto,a.c_razao";

    $result = mysql_query($query) or die($query . "<br>\n<br>\n" . mysql_error());

    while ($row = mysql_fetch_assoc($result)) {
        $prestadores[] = $row;
    }

    include_once 'consultar_prestadores.php';
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'form_impostos') {
    $objImpostos->setStatus(1);
    $objImpostos->getImpostos();

    while ($objImpostos->getRowImposto()) {
        $selectImpostos[$objImpostos->getIdImposto()] = utf8_encode($objImpostos->getSigla() . " - " . $objImpostos->getNome());
    }

    $objImpAssoc->setStatus(1);
    $objImpAssoc->setIdContrato($_REQUEST['id_prestador']);
    $objImpAssoc->getAssoc();
    while ($objImpAssoc->getRow()) {
        $assoc_impostos[] = array(
            'id_assoc' => $objImpAssoc->getIdAssoc(),
            'id_contrato' => $objImpAssoc->getIdContrato(),
            'id_imposto' => $objImpAssoc->getIdImposto(),
            'aliquota' => $objImpAssoc->getAliquota()
        );
    }
    include_once 'form_impostos.php';
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'salvar_impostos') {
    $retorno = TRUE;
    for ($i = 0; $i < count($_REQUEST['id_imposto']); $i++) {
        if (!empty($_REQUEST['id_imposto'][$i]) && !empty($_REQUEST['aliquota'][$i])) {
            $objImpAssoc->setIdImposto($_REQUEST['id_imposto'][$i]);
            $objImpAssoc->setAliquota($_REQUEST['aliquota'][$i]);
            $objImpAssoc->setIdAssoc($_REQUEST['id_assoc'][$i]);
            $objImpAssoc->setStatus(1);
            $objImpAssoc->setIdContrato($_REQUEST['id_contrato']);
            $a = $objImpAssoc->salvar();
            $retorno = $a && $retorno;
        }
    }

    echo json_encode(($retorno) ? array('msg' => 'Impostos salvos com sucesso.', 'status' => 'success') : array('msg' => 'Erro ao salvar impostos', 'status' => 'danger'));
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'excluir_imposto') {
    $objImpAssoc->setIdAssoc($_REQUEST['id_assoc']);
    echo json_encode(($objImpAssoc->inativa()) ? array('msg' => 'Escluido com sucesso', 'status' => 'success') : array('msg' => 'Erro ao excluir', 'status' => 'danger'));
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] === '') {
    
}



