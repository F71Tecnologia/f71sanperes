<?php 
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";

if(isset($_GET['encriptar'])){
	
$link_enc = encrypt($_GET['encriptar']);
$link_enc = str_replace('+', '--', $link_enc);
echo $link_enc;
exit();

}
?>