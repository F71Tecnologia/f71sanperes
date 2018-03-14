<?php
if(isset($_GET['id_noti'])){
	include('../../conn.php');
	
	
		$id_noti = $_GET['id_noti'];		
		$update = mysql_query("UPDATE notificacoes SET notificacao_status = '0'	WHERE notificacao_id = '$id_noti' LIMIT 1");
		if($update) {
		header("Location:listagem_notificacao.php");	
		}
}

?>