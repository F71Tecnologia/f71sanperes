<?php 

include "../../conn.php";
require("../../classes/uploadfile.php");


$upload = new UploadFile('logo',array('jpg','gif','png'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();

$nome_arquivo = uniqid('logo_');

	
	$upload->NomeiaFile($nome_arquivo);
	$upload->Envia();
	
	echo json_encode(array('nome_arquivo' => $nome_arquivo.'.'.$upload->extensao, 'img' => 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_parceiros/logo/'.$nome_arquivo.'.'.$upload->extensao));
	



?>