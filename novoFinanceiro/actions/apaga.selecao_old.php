<?php 
include "../../conn.php";

$id_user = $_COOKIE['logado'];
$data_hoje = date("Y-m-d");
$id['saida'] = $_POST['saidas'];

$id['entrada'] = $_POST['entradas'];



foreach($id as $tabela => $saida):
foreach($saida as $id_saida){
		$query = mysql_query("UPDATE $tabela SET status = '0', id_deletado = '$id_user', data_deletado = NOW() WHERE id_$tabela = '$id_saida'  LIMIT 1");
}
endforeach;
?>