<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../wfunction.php');
include('../../classes/PrestadorServicoClass.php');

$sql = mysql_query("UPDATE prestadorservico SET status = 0 WHERE id_prestador = $_REQUEST[prestador]");

header('Location: index.php');