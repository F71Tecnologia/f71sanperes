<?php
include('../../adm/include/restricoes.php');
include('../../conn.php');


if(isset($_GET['cotacao'])){

	$id_compra = $_GET['compra'];
	
	mysql_query("UPDATE compra2 SET acompanhamento = 2 WHERE id_compra = '$id_compra' LIMIT 1");
    
 

}

if(isset($_GET['aprovar'])){

	$id_compra = $_GET['compra'];	
	mysql_query("UPDATE compra2 SET acompanhamento = 6 WHERE id_compra = '$id_compra' LIMIT 1");
    
 

}


if(isset($_GET['deferir'])){

	if ($GET['deferir'] == 'aberturaprocesso')
	{
	$id_compra = $_GET['compra'];	
	mysql_query("UPDATE compra2 SET acompanhamento = 7 WHERE id_compra = '$id_compra' LIMIT 1");
	}



}

if(isset($_GET['selecao'])){

	$id_compra = $_GET['compra'];	
	mysql_query("UPDATE compra2 SET acompanhamento = 50 WHERE id_compra = '$id_compra' LIMIT 1");
}


?>