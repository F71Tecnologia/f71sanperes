<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/global.php");
include("../../classes/SaidaClass.php");
include("../../classes/BorderoClass.php");

$global = new GlobalClass();
$objBordero = new BorderoClass();
$usuario = carregaUsuario();

if($_REQUEST['method'] == 'deletar') {
    $objBordero->deletaBordero($_REQUEST['id']);
    echo json_encode($objBordero->retorno);
    exit;
}

if($_REQUEST['method'] == 'removerSaida') {
    $objBordero->removerSaidaDoBordero($_REQUEST['id_bordero'], $_REQUEST['id_saida']);
    echo json_encode($objBordero->retorno);
    exit;
}

