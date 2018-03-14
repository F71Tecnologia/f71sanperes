<?php

header('Content-Type: text/html; charset=iso-8859-1');
include("../../../conn.php");
include("../../../wfunction.php");
include("../../../classes/ContabilHistoricoClass.php");
include("../../../classes/global.php");

$objHistorico = new ContabilHistoricoPadraoClass();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar') {
    if(isset($_REQUEST['id_historico'])){
        $objHistorico->setIdHistorico($_REQUEST['id_historico']);
    }
    $objHistorico->setTexto(utf8_decode($_REQUEST['texto']));
    $objHistorico->setStatus(1);
    $objHistorico->setDataCad(date('Y-m-d H:i:s'));
    if ($objHistorico->salvar()) {
        echo json_encode(array('status' => TRUE, 'msg' => 'Salvo Com sucesso.', 'id' => $objHistorico->getIdHistorico()));
    } else {
        echo json_encode(array('status' => FALSE, 'msg' => 'Erro Ao salvar'));
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir') {
    
    $objHistorico->setIdHistorico($_REQUEST['id']);
    if ($objHistorico->inativa()) {
        echo json_encode(array('status' => TRUE));
    } else {
        echo json_encode(array('status' => FALSE));
    }
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'getHistorico') {
    
    $objHistorico->setIdHistorico($_REQUEST['id']);
    $objHistorico->getHistoricoById();
    $objHistorico->getRow();
    if ($objHistorico->getNumRows() > 0) {
        $array = array(
            'texto' => utf8_encode($objHistorico->getTexto()),
            'id_historico' => $objHistorico->getIdHistorico(),
            'status' => $objHistorico->getStatus()
        );

        echo json_encode(array('status' => TRUE, 'dados'=>$array));
    } else {
        echo json_encode(array('status' => FALSE));
    }
}
