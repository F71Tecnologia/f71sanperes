<?php
if (empty($_COOKIE['logado'])) {
    header('Location: login.php');
    exit;
}


include("../conn.php");
include("../wfunction.php");

?>
<html>
    
</html>