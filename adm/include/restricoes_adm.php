<?php
session_cache_limiter(540);
session_start();

echo  $_SESSION['logado_adm'].'-';
/*
if(!isset($_SESSION['logado_adm'])){
   header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
   exit;
} */

?>
