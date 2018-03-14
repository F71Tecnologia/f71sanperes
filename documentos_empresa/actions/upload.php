<?php 

require("../../conn.php");
require("../../classes/uploadfile.php");

$data 			= date("Y-m-d");
$id_documento 	= $_POST['id_documento'];
$id_funcionario = $_POST['id_funcionario'];
$id_regiao 		= $_POST['id_regiao'];
$tipo 			= $_POST['tipo'];
$mes_file 		= $_POST['mes_selecionado'];
$diretorio 		= '../documentos';

$upload = new UploadFile($diretorio,array('gif','jpg','doc','docx','xls','xlsx','pdf','re','txt','rar','zip'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();
$sql = "INSERT INTO doc_files 	(id_documento,		id_funcionario,		id_regiao,		data_file,	mes_file,		tipo_file) VALUES 
								('$id_documento',	'$id_funcionario',	'$id_regiao', 	'$data',	'$mes_file', 	'$upload->extensao');";

$insert 	= mysql_query($sql);
$queryMax 	= mysql_query("SELECT MAX(id_file) FROM doc_files");
$idMax 		= mysql_result($queryMax,0);
$upload->NomeiaFile($idMax);
$upload->Envia();
if($insert){
	echo 'Enviado com sucesso';
}else{
	echo 'Erro ao enviar';
}
?>