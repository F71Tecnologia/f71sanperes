<?php 
require_once('../conn.php');
$id   = $_POST['id'];
$data = implode('-', array_reverse(explode('/', $_POST['data'])));
mysql_query("UPDATE fr_combustivel SET data_libe = '$data' WHERE id_combustivel = '$id' LIMIT 1");
?>