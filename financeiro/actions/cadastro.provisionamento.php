<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_provisao = $_REQUEST['provisao'];
$data_cad = date("Y-m-d");
$user = $_COOKIE['logado'];
$valor = str_replace(",",".",str_replace(".","",$_REQUEST['valor']));
$descricao = $_REQUEST['descricao'];
$data_provisao = $_REQUEST['dataProvisao'];


$sql = "INSERT INTO provisionamento (
	id_provisao,	 	 	 	
	data_cad_provisionamento,		 	
	user_cad_provisionamento,	 	 	 	 	 	 	
	valor_provisionamento,	 	
	descricao_provisionamento,	 	 	 	 	 	 	 
	data_provisionamento		 	 	 	 	 	 	 
) VALUES (
	'$id_provisao',
	'$data_cad',
	'$user',
	'$valor',
	'$descricao',
	'$data_provisao'
)";


$query = mysql_query($sql);

if($query){
	print "<script>
				parent.window.location.reload();
			</script>";
}else{
	print $sql;
}

/*
id_provisao	 	 	 	
data_cad_provisionamento		 	
user_cad_provisionamento	 	 	 	 	 	 	
valor_provisionamento	 	
descricao_provisionamento	 	 	 	 	 	 	 
data_provisionamento		 	 	 	 	 	 	 
status_provisionamento
*/
?>