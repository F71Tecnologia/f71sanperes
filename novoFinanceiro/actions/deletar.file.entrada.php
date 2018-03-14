<?php 
include "../../conn.php";

$id_files = $_REQUEST['ID'];

$update = mysql_query("UPDATE entrada_files SET status = '0' WHERE id_files = '$id_files' LIMIT 1");

if(!$update){
	echo '1';
}
?>