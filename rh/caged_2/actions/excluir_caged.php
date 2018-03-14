<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include "../../../conn.php";

$id_caged = $_POST['id_caged'];
print_r($_POST['id_caged']);

$qr_caged = mysql_query("DELETE FROM caged WHERE id_caged = '$id_caged' LIMIT 1");
exit;
?>