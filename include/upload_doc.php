<?php

require("../classes/uploadfile.php");
if(!empty($_FILES['Filedata'])){
	$upload = new UploadFile("../documentos", array('jpg','gif','png'));
	$upload->arquivo($_FILES['Filedata']);
	$upload->verificaFile();
	$upload->Subpasta($_POST['reg']);
	$upload->Subpasta($_POST['projeto']);
	$upload->Subpasta($_POST['tipo_contratacao'].'_'.$_POST['ID_participante']);
	$upload->NomeiaFile($_POST['tipo_contratacao'].'_'.$_POST['ID_participante'].'_'.$_POST['tipo_documento']);
	$upload->Envia();
	if($upload->erro == 1){
		echo $upload->erro;
	}else{
		echo $_POST['reg']."/";
		echo $_POST['projeto']."/";
		echo $_POST['tipo_contratacao'].'_'.$_POST['ID_participante']."/";
		echo $_POST['tipo_contratacao'].'_'.$_POST['ID_participante'].'_'.$_POST['tipo_documento'];
	}
}

?>

