<?php
if(empty($_COOKIE['logado'])){
   header("Location: ../../login.php?entre=true");
   exit;
} else {
 include('../../conn.php');
	
}

if(empty($_COOKIE['logado3']) and empty($_COOKIE['logado2'])){
	header('location: ../login.php?entre=true');
}
?>