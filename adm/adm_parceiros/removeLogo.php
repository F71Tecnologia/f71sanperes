<?php 
@unlink($_POST['caminho']);

$update = mysql_query("UPDATE parceiros SET parceiro_logo = '' WHERE parceiro_id = '$_POST[id]' LIMIT 1 ");
?>