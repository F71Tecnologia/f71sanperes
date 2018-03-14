<?php
require("../../conn.php");

$id_documento = $_GET['documento'];
$id_funcionario = $_GET['funcionario'];
$id_regiao = $_GET['id_regiao'];
$mes = $_GET['mes_file'];
$data = date("Y-m-d");
$user = $_COOKIE['logado'];




$query = mysql_query("INSERT INTO doc_files (data_file, id_documento, id_funcionario, id_regiao, mes_file, tipo_file, recebimento_file, data_recebimento_file, id_recebimento_file) 
						VALUES
					 (CURDATE(), '$id_documento', '$id_funcionario', '$id_regiao', '$mes', 'XXX', '2', CURDATE(), '$user')");
					 
if($query){
	header("Location: ../../principal.php");
}else{
	print "<p>Erro ao cancelar arquivo</p>";
}
?>