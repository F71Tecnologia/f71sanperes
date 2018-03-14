<?php
	require("../classes/uploadfile.php");
	require("../conn.php");
	
	$diretorio = "../comprovantes";
	$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
	$upload->arquivo($_FILES['Filedata']);
	$upload->verificaFile();
	mysql_query("UPDATE saida SET tipo_arquivo = '', comprovante = '2' WHERE id_saida = '$_REQUEST[Ultimo_ID]' LIMIT 1;");
	mysql_query("INSERT INTO saida_files (tipo_saida_file, id_saida) VALUES ('.$upload->extensao', '$_REQUEST[Ultimo_ID]');");
	$query_max = mysql_query("SELECT MAX(id_saida_file) FROM saida_files");
	$id = mysql_result($query_max,0);
	
	
	
	$upload->NomeiaFile($id.'.'.$_REQUEST['Ultimo_ID']);
	$upload->Envia();
	
	echo $id.'.'.$_REQUEST['Ultimo_ID'].'.'.$upload->extensao;
?>