<?php 

include "../../../conn.php";

$id_regiao = $_GET['regiao'];
$qr_projetos = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$id_regiao' AND status_reg = '1' ");

$array_response = array();

while($row_projetos = mysql_fetch_assoc($qr_projetos)){
	
	$row_projetos['nome'] = utf8_encode($row_projetos['nome']);
	$array_response[]= $row_projetos;
}

echo json_encode($array_response);

?>