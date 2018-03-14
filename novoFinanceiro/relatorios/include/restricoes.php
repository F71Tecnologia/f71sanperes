<?php
if(empty($_COOKIE['logado'])){
	header('Location: ../../login.php');
	} else 
	if(empty($_COOKIE['logado2'])){
		header('Location: ../../financeiro/login_adm2.php');
}	
?>