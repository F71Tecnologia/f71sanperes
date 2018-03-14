<?php 

include "../../conn.php";
include "../../classes/uploadfile.php";

$id_entrada = $_REQUEST['id_entrada'];

$diretorio = "../comprovantes/entrada";
$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();
mysql_query("INSERT INTO entrada_files (id_entrada,	tipo_files) VALUES ('$id_entrada','.$upload->extensao');");
$query_max = mysql_query("SELECT MAX(id_files) FROM entrada_files");
$id = mysql_result($query_max,0);

$upload->NomeiaFile($id);
$upload->Envia();

echo $diretorio.'/'.$id.'.'.$upload->extensao;
?>