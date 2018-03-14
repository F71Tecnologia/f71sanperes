<?php
include('include/restricoes.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../conn.php');

$id = $_GET['id'];

$qr_documentos = mysql_query("UPDATE modelo_documentos SET  documento_status = 0 WHERE documento_id = '$id' LIMIT 1");

header("Location: index.php?m=$link_master");

?>