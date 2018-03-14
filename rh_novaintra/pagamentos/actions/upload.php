<?php 
require("../../../conn.php");
require("../../../classes/uploadfile.php");

$id_saida = $_REQUEST['id_saida'];
$tipo_gps = $_REQUEST['tipo_gps'];

$obj = new UploadFile('../../../comprovantes',array('pdf'));
$obj->arquivo($_FILES['Filedata']);
$obj->verificaFile();

$sql = "INSERT INTO saida_files (id_saida,tipo_saida_file, tipo_gps) VALUES ('$id_saida','.pdf', '$tipo_gps')";
mysql_query($sql);

$qr_ultimo_id 	= mysql_query("SELECT MAX(id_saida_file) FROM saida_files");
$ultimo_id		= mysql_result($qr_ultimo_id,0);
$obj->NomeiaFile($ultimo_id.'.'.$id_saida);
if($obj->Envia()){
	echo 1;
}
?>