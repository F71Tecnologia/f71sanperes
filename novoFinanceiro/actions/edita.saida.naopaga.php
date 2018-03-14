<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$tabela = $_GET['tipo'];
$id_saida = $_REQUEST['id_saida'];
$banco = $_REQUEST['banco'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$descricao = utf8_decode($_REQUEST['descricao']);

$data_vencimento = implode('-',array_reverse(explode('/',$_REQUEST['data'])));


$update = mysql_query("UPDATE $tabela SET id_regiao = '$regiao', id_projeto = '$projeto', id_banco = '$banco' , data_vencimento = '$data_vencimento' , especifica = '$descricao' WHERE id_$tabela = '$id_saida' LIMIT 1");
if(!$update){
	echo '1';
}

?>