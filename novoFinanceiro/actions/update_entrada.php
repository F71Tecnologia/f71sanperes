<?php 
// notas

include "../../conn.php";

$nome  			 = utf8_decode($_REQUEST['nome']);
$descricao 		 = addslashes(utf8_decode($_REQUEST['descricao']));
$id_entrada      = $_REQUEST['id_entrada'];
$data_vencimento = implode('-',array_reverse(explode('/',$_POST['data_vencimento'])));
$qr_entrada 	 =	mysql_query("SELECT * FROM entrada WHERE id_entrada  = '$id_entrada' LIMIT 1");
$row_entrada	 = 	mysql_fetch_assoc($qr_entrada);


$update 		= 	mysql_query("UPDATE entrada SET nome = '$nome', 
						especifica = '$descricao',data_vencimento = '$data_vencimento',
						valor = '$_POST[valor]'  
						WHERE id_entrada = '$id_entrada' LIMIT 1;");
//$ids_notas = $_REQUEST['check_nota']; // :ARRAY



// UPDATE SUBTIPO
	/*$subtipo = $_REQUEST['subtipo'];
	$n_subtipo = $_REQUEST['n_subtipo'];
	mysql_query("UPDATE entrada SET subtipo = '$subtipo' , n_subtipo = '$n_subtipo' WHERE id_entrada = '$id_entrada' LIMIT 1;");
*/


/*
if(!empty($ids_notas)){
	
	
	
	// DELETANDO VINCULO SE EXISTIR
	mysql_query("DELETE FROM notas_assoc WHERE id_entrada = '$id_entrada'");
		
	foreach($ids_notas as $id_nota){
		//$id_nota = $_REQUEST['radio_nota'];
		
		
		// verificando se exitem notas associadas
		$qr_notas_assoc = mysql_query("SELECT * FROM notas_assoc WHERE id_entrada = '$id_entrada'");
		$num_notas_assoc = mysql_num_rows($qr_notas_assoc);
		$row_notas_assoc = mysql_fetch_assoc($qr_notas_assoc);
		
		if(!empty($num_notas_assoc)){
			mysql_query("UPDATE notas_assoc SET id_notas = '$id_nota' WHERE id_notas = '$row_notas_assoc[id_notas]' AND id_entrada = '$row_notas_assoc[id_entrada]' LIMIT 1");
			
		}else{
			
			mysql_query("INSERT INTO notas_assoc (id_notas, id_entrada) VALUES ('$id_nota', '$id_entrada')");
		}
		
		mysql_query("INSERT INTO notas_assoc (id_notas, id_entrada) VALUES ('$id_nota', '$id_entrada')");
		
	}
	
}*/
if(!$update){
	echo '1';
}

?>