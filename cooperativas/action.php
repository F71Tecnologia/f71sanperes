<?php

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/global.php');
include('../classes/cooperativa.php');

$action = $_REQUEST['action'];

switch ($action) {
    case 'excluir':
        $dados = array(
            'id_coop' => $_REQUEST['id_coop'],
            'status_reg' => '0'
        );
        $result = cooperativa::update($dados);
        $html = ($result)?'ATENÇÃO: Registro excluido!':'ATENÇÃO: Erro ao excluir registro!';
        echo utf8_encode($html);
        break;

    default:
        break;
}
