<?php 
require("../conn.php");

if(isset($_POST['clt'])){
	mysql_query("UPDATE rh_clt SET foto = '0' WHERE id_clt = $_POST[clt] LIMIT 1");
	unlink("../fotosclt/".$_POST['nome']);
}else{
	mysql_query("UPDATE autonomo SET foto = '0' WHERE id_autonomo = $_POST[ID] LIMIT 1");
	unlink("../fotos/".$_POST['nome']);
}

?>