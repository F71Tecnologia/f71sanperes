<?php
if(empty($_COOKIE['logado'])){
   header("Location: ../login.php?entre=true");
   exit;
} else {
   include "../conn.php";
}

/*session_start();
if(!isset($_SESSION['adm'])) {
	header("Location: login.php?entre"); 
    exit;
}

if(isset($_GET['logout'])) {
  session_unset($_SESSION['adm']);
  session_destroy($_SESSION['adm']);
  header("Location: login.php?logout");
  exit;
}*/
?>