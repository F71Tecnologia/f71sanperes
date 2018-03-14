<?php
    ob_start();
    setcookie ("logado" , "", time()-3600);
    setcookie ("simulado", "", time()-3600);
    session_destroy();
    header("Location: login.php?logout=true");
?>