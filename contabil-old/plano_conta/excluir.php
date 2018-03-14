<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$id = $_GET['id'];
$tb = $_GET['tb'];

switch($tb){
	case 1: mysql_query("UPDATE c_grupos SET c_grupo_status  = 0 WHERE c_grupo_id = '$id' ")or die(mysql_error());
			header("Location: listar_grupo.php?tb=$tb");
	break;
	
	case 2: mysql_query("UPDATE c_subgrupos SET c_subgrupo_status  = 0 WHERE c_subgrupo_id = '$id'");
			header("Location: listar_subgrupo.php?tb=$tb");
	break;
	
	case 3: mysql_query("UPDATE c_tipos SET c_tipo_status  = 0 WHERE c_tipo_id = '$id'");
			header("Location: listar_tipo.php?tb=$tb");
	break;
	
	case 4: mysql_query("UPDATE c_subtipos SET c_subtipo_status  = 0 WHERE c_subtipo_id = '$id'");
			header("Location: listar_subtipo.php?tb=$tb");
	break;
	
	}




?>