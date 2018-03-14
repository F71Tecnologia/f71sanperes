<?php
include "../../conn.php";

$ID_saida = $_POST['id'];
$Data = implode("-",array_reverse(explode("/",$_POST['data'])));

$query = mysql_query("UPDATE saida SET data_vencimento = '$Data' WHERE id_saida = '$ID_saida'");
if($query == true){
	echo '1';
}else{
	echo '2';
}
?>