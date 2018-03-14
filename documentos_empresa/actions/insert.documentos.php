<?php 
require("../../conn.php");
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$data = date("Y-m-d");
$id_master = $_POST['id_master'];
$dia_documento = $_POST['dia'];
$categoria = $_POST['categoria'];
$funcionarioCriador = $_POST['funcionarioCriador'];
$frequencia_documento = $_POST['frequencia_documento'];
$mes_referencia_documento = $_POST['mes_referencia_documento'];

$responsaveis = $_POST['funcionario'];
$regiao = $_POST['regiao'];

$sql_documentos = "INSERT INTO documentos (nome_documento, descricao_documento, data_documento, id_master, dia_documento, id_categoria, frequencia_documento, mes_referencia_documento, id_funcionario)";
$sql_documentos .= " VALUES (";
$sql_documentos .= "'".$nome."', ";
$sql_documentos .= "'".$descricao."', ";

$sql_documentos .= "'".$data."', ";

$sql_documentos .= $id_master.", ";

$sql_documentos .= "'".$dia_documento."', ";

$sql_documentos .= $categoria.", ";

$sql_documentos .= $frequencia_documento.", ";

$sql_documentos .= "'".$mes_referencia_documento."', ";

$sql_documentos .= $funcionarioCriador."); ";

mysql_query($sql_documentos);

$query_documentos = mysql_query("SELECT MAX(id_documento) FROM documentos");
$ultimoID = mysql_fetch_assoc($query_documentos);

foreach($responsaveis as $key => $valor){
	
	mysql_query("INSERT INTO doc_responsaveis (id_funcionario, id_documento, ids_regioes) VALUES (".$valor.", ".$ultimoID['MAX(id_documento)'].", '".$regiao[$key]."');");
}

header("Location: ../cadastro.php?mt=".$id_master."&certo");


?>