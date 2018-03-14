<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_user = $_POST['logado'];
$idtarefa = $_POST['idtarefa']; // 1 saida, 2 entrada
$data_hoje = date("Y-m-d");
if($idtarefa == 1){
	$id = $_POST['saidas'];
}else{
	$id = $_POST['entrada'];
}



foreach($id as $saida):
//AQUI ELE VAI DELETAR A SAÍDA ou entrada
if($idtarefa == 1){
	$query = mysql_query("UPDATE saida set status = '0' where id_saida = '$saida'");
	if($query){
		echo '1';
	}else{
		echo mysql_error();
	}

}else{
	$query = mysql_query("UPDATE entrada set status = '0' where id_entrada = '$saida'");
	if($query){
		echo '1';
	}else{
		echo mysql_error();
	}

}
endforeach;
?>