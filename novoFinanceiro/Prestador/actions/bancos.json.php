<?php 
include "../../../conn.php";

$id_projeto = $_GET['projeto'];

$qr_bancos = mysql_query("SELECT id_banco,nome FROM bancos WHERE id_projeto = '$id_projeto' AND interno = '1' AND status_reg = '1'");

$array_response = array();
while($row_bancos = mysql_fetch_assoc($qr_bancos)):
	$row_bancos['nome'] = utf8_encode($row_bancos['nome']);
	$array_response[] = $row_bancos;
	
	
endwhile;

echo json_encode($array_response);

?>