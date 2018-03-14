<?php
if(empty($_COOKIE['logado'])){
   header("Location: ../../login.php?entre=true");
   exit;
} else {
 
	$host =  $_SERVER['SCRIPT_NAME'] ;
	
	$pastas = explode('/',$host);
	
	$total= count($pastas);
	
	if($total == 3){
	include'conn.php';
	} else if($total == 4){
			include '../conn.php';
	} elseif($total == 5){
		include '../../conn.php';
	} elseif($total == 6){
		include '../../../conn.php';
	}
}

if(empty($_COOKIE['logado3']) and empty($_COOKIE['logado2'])){
	header('location: ../login.php?entre=true');
}
?>