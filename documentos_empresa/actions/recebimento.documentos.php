<?php 
require("../../conn.php");
$id = $_GET['id_file'];

$sql = "SELECT * FROM doc_files WHERE id_file = '$id'";
$query = mysql_query($sql);
$row_file = mysql_fetch_assoc($query);


if($row_file['recebimento_file'] == 0){
	$update = "UPDATE doc_files SET recebimento_file = '1', data_recebimento_file = '".date("Y-m-d")."', id_recebimento_file = '$_COOKIE[logado]' WHERE id_file = '$id'";
	mysql_query($update);
}
echo "<script>location.href='../documentos/$row_file[id_file].$row_file[tipo_file]'</script>";
?>