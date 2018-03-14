<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");

$usuario = carregaUsuario();
$objFolha = new Folha();

$criteria = new stdClass();
$criteria->id_folha = $_POST['id_folha'];
$dadosFolha = $objFolha->getListaFolhas($criteria);