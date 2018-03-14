<?php 
require('../conn.php');
$id    = $_POST['ID'];
$valor = str_replace('.', '', $_POST['valor']);
$valor = str_replace(',', '.', $valor);
mysql_query("UPDATE folha_cooperado SET ajuda_custo = '$valor' WHERE id_folha_pro = '$id' LIMIT 1");
?>