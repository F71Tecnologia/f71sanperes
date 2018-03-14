<?php 

include "../../conn.php";
include "../../classes/uploadfile.php";


	$diretorio = "../../comprovantes";
	$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
	$upload->arquivo($_FILES['Filedata']);
	$upload->verificaFile();
        
        
	mysql_query("INSERT INTO saida_files_pg (tipo_pg, id_saida) VALUES ('.$upload->extensao', '$_REQUEST[Ultimo_ID]');") or die(mysql_error());
	$query_max = mysql_query("SELECT MAX(id_pg) FROM saida_files_pg");
	$id = mysql_result($query_max,0);
	
	
	
	$upload->NomeiaFile($id.'.'.$_REQUEST['Ultimo_ID'].'_pg');
	$upload->Envia();
	
	echo $id.'.'.$_REQUEST['Ultimo_ID'].'_pg.'.$upload->extensao;
        
?>