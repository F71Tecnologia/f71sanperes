<?php 
require("../../conn.php");

$id_documento = $_GET['id_documento'];
$id_master = $_GET['id_master'];

mysql_query("UPDATE documentos SET status_documento = '0' WHERE id_documento = '$id_documento' LIMIT 1");
header("Location: ../cadastro.php?mt=$id_master");

?>