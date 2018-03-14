<?php

/* Método para Exclusão de Cooperativa
         * Autor: Lucas Praxedes
         * Arquivos que utilizam:
         * - cooperativa_nova.php    */
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include "../../classes/LogClass.php";
$log = new Log();

$id_coop = $_REQUEST['id_coop'];
$sql = mysql_query("UPDATE cooperativas SET status_reg = 0 WHERE id_coop = $id_coop");
$log->gravaLog('Gestão de Cooperativas', "Cooperativa Excluida: ID{$id_coop}");
                  
?>
