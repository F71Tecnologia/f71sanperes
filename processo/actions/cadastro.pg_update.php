<?php
require("../../classes/uploadfile.php");
require("../../conn.php");
$id_pg = $_REQUEST['id_pg'];




		$upload = new UploadFile("../comprovantes",array('gif','pdf','jpg'));
		$upload->arquivo($_FILES['Filedata']);
		$upload->NomeiaFile($id_pg);
		$upload->verificaFile();
		$upload->Envia();
		mysql_query("UPDATE prestador_pg SET comprovante = '$upload->extensao' WHERE  id_pg = '$id_pg' LIMIT 1;");


print 'Cadastrado com sucesso!';
?>