<?php
include('../include/restricoes.php');
include('../../conn.php');

if(isset($_GET['excluir'])){


$id_entregue = $_GET['excluir'];
$master = $_GET['m'];
$qr_entregue = mysql_query("DELETE FROM  obrigacoes_entregues  WHERE entregue_id  = '$id_entregue' LIMIT 1");


header("Location: index.php?m=$master");
	 
}

?>