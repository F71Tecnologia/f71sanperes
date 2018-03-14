<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');

$id = mysql_real_escape_string($_GET['id']);
$regiao = mysql_real_escape_string($_GET['regiao']);
$tp = mysql_real_escape_string($_GET['tp']);


if($tp == 1 ){

mysql_query("UPDATE advogados SET adv_status = 0 WHERE adv_id = '$id'");

} elseif($tp == 2) {

mysql_query("UPDATE prepostos SET prep_status = 0 WHERE prep_id = '$id'");	
}
header("Location:index.php?regiao=$regiao");


?>