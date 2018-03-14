<?php
require("../../conn.php");
$id_file = array($_GET['id_file']);
if(isset($_GET['multi'])){
	$id_file = explode(',',$_GET['multi']);
}

foreach($id_file as $id):
	mysql_query("DELETE FROM doc_files WHERE id_file = '$id' LIMIT 1");
endforeach;
header("Location: ../../principal.php");
?>