<?php
if(empty($_COOKIE['logado'])){
   header("Location: ../../login.php?entre=true");
   exit;
} else {
   include "../../conn.php";
}


?>