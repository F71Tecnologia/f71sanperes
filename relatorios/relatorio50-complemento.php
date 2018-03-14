<?php

include("../conn.php");
include("../wfunction.php");
include("../classes/PrestadorServicoClass.php");
include('../classes/global.php');

if(isset($_REQUEST['cpf'])) {
$cpf = $_REQUEST['cpf'];

$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE cpf = '{$cpf}'");

$qr_autonomo = mysql_query("SELECT * FROM autonomo WHERE cpf = '{$cpf}'");


}
?>