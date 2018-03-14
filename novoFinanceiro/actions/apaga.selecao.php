<?php 
include "../../conn.php";

$id_user = $_POST['logado'];
$idtarefa = $_POST['idtarefa']; // 1 saida, 2 entrada
$data_hoje = date("Y-m-d");
if($idtarefa == 1){
	$id = $_POST['saidas'];
}else{
	$id = $_POST['entradas'];
}



foreach($id as $saida):
//AQUI ELE VAI DELETAR A SAÍDA ou entrada
if($idtarefa == 1){
	$regiao = mysql_query("SELECT id_regiao FROM saida WHERE id_saida = '$saida'");
	$query = mysql_query("UPDATE saida SET status = '0', id_deletado = '$id_user', data_deletado = NOW()  WHERE id_saida = '$saida' LIMIT 1");

}else{
	$regiao = mysql_query("SELECT id_regiao FROM entrada WHERE id_entrada = '$saida'");
	$query = mysql_query("UPDATE entrada SET status = '0', id_deletado = '$id_user', data_deletado = NOW()  WHERE id_entrada = '$saida' LIMIT 1");
}

endforeach;
header("Location: ../../financeiro/novofinanceiro.php?regiao=".@mysql_result($regiao,0));
?>