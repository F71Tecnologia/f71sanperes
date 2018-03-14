<?php 
	require("../../../classes/uploadfile.php");
	require("../../../conn.php");
	
	
	$diretorio = "../../../comprovantes_tmp";
	$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
	$upload->arquivo($_FILES['Filedata']);
	$upload->verificaFile();
	
	$nome = uniqid(date('d-m-Y').'_');
	
	$upload->NomeiaFile($nome);
	$upload->Envia();
	
	$array_response = array(
		'nome_file' => $nome,
		'extencao' =>$upload->extensao,
		'nome' => $nome .'.' .$upload->extensao,
		'diretorio' => $diretorio
	);
	
	echo json_encode($array_response);
	
?>