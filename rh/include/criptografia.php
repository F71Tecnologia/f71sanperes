<?php
// Recebendo a variável criptografada
if (!empty($_GET['m'])){
$enc = $_REQUEST['m'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc);
$decript = explode("&",$link);
$Master = $decript[0];
}
// ---------------------------

// Encriptografando a variável
$link_master = encrypt("$Master&12");
$link_master = str_replace("+","--",$link_master);
// ---------------------------
?>