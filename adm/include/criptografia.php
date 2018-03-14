<?php

// Recebendo a variável criptografada
if (!empty($_GET['m'])) {
    $enc = $_REQUEST['m'];
    $enc = str_replace("--", "+", $enc);
    $link = decrypt($enc);
    $decript = explode("&", $link);
    $Master = $decript[0];
}
// ---------------------------
// Encriptografando a variável
$link_master = encrypt("$Master&12");
$link_master = str_replace("+", "--", $link_master);
// ---------------------------

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$m = $row_user['id_master'];
$link_master = encrypt($m . "&12");
$link_master = str_replace("+", "--", $link_master);

if (empty($_GET['m']) && !isset($_REQUEST['method'])) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?m=$link_master");
} else {
    if ($Master != $row_user['id_master']  && !isset($_REQUEST['method'])) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?m=$link_master");
    }
}

?>