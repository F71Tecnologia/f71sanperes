<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_provisao = $_REQUEST['provisao'];
$query = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$id_provisao';");
$row = mysql_fetch_assoc($query);


mysql_query("UPDATE provisao SET status_provisao = '0' WHERE id_provisao = '$id_provisao'");
header("Location: ../novofinanceiro2.php?regiao=$row[id_regiao]");
?>