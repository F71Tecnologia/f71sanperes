<?php 
include "../../conn.php";

$id_folha = $_POST['id_folha'];
$query_folha = mysql_query("SELECT qnt_dias FROM folhas WHERE id_folha = '$id_folha'");
$result = @mysql_result($query_folha,0);

echo $result;
?>