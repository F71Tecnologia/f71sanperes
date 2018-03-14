<?php 
include('../conn.php');
include "../funcoes.php";
include "include/criptografia.php";
///condição para encrpitar o master vai ajax
if(isset($_GET['encriptar'])) {

$id_master   = $_GET['encriptar'];
$link_master = encrypt($id_master."&12");
$link_master = str_replace("+","--",$link_master);

echo $link_master;
exit();


}

?>