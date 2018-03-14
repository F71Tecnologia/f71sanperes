<?php

$reg = $_REQUEST['reg'];
$pro = $_REQUEST['pro'];
$tipo = $_REQUEST['tipo'];
$tela = $_REQUEST['tela'];
$id = $_REQUEST['id'];

include('../conn.php');
include ("../classes/LogClass.php");

$log = new Log();

$log->gravaLog('Ficha Financeira', "Ficha Financeira Visualizada - Funcionário ID:{$id}");

if(mysql_insert_id() != 0) {
    header("Location: ../relatorios/fichafinanceira_clt.php?reg={$reg}&pro={$pro}&tipo={$tipo}&tela={$tela}&id={$id}");
}

?>

