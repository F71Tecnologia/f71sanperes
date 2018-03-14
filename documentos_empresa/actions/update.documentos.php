<?php 
require("../../conn.php");

$id_documento = $_REQUEST['id_documento'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$id_master = $_POST['id_master'];
$dia_documento = $_POST['dia'];
$categoria = $_POST['categoria'];
$funcionarioCriador = $_POST['funcionarioCriador'];
$frequencia_documento = $_POST['frequencia_documento'];
$mes_referencia_documento = $_POST['mes_referencia_documento'];

$responsaveis = $_POST['funcionario'];
$regiao = $_POST['regiao'];

$sql_documentos = "UPDATE documentos SET nome_documento = '$nome', descricao_documento = '$descricao', id_master = '$id_master', dia_documento = '$dia_documento', id_categoria = '$categoria', frequencia_documento = '$frequencia_documento', mes_referencia_documento = '$mes_referencia_documento' WHERE id_documento = '$id_documento' LIMIT 1";

mysql_query($sql_documentos);

mysql_query("DELETE FROM doc_responsaveis WHERE id_documento = '$id_documento'");

foreach($responsaveis as $key => $valor){
	
	mysql_query("INSERT INTO doc_responsaveis (id_funcionario, id_documento, ids_regioes) VALUES (".$valor.", ".$id_documento.", '".$regiao[$key]."');");
}

header("Location: ../cadastro.php?mt=$id_master");

?>