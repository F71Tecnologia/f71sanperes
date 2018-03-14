<?php 

require("../conn.php");
$id_pro = $_POST['id_pro'];
$id_user = $_POST['id_user'];
$data_hoje = date("Y-m-d");
$query = mysql_query("UPDATE saida SET 
			status = '2',
			data_pg = '$data_hoje',
			id_userpg = '$id_user'
			WHERE 
			id_saida = '$id_pro'
			LIMIT 1;");
if($query){
	if($_POST['tipo'] == "66"){
		mysql_query("UPDATE compra SET acompanhamento = '6' where id_compra = '$_POST[id_compra]'");
	}
	echo '1';
}
?>