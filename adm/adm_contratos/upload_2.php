<?php 


include "../../conn.php";
require("../../classes/uploadfile.php");
$id_oscip=$_POST['id_oscip'];

$upload = new UploadFile('anexos_oscip',array('jpg','gif','pdf'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();


$sql_insert = "INSERT INTO obrigacoes_oscip_anexos ( id_anexo, extensao, anexo_publicacao, anexo_estatuto, status,tipo_anexo) VALUES ('' , '$upload->extensao', '1', '0','1','2')";
$insert = mysql_query($sql_insert);

$id_file = (int) @mysql_insert_id();



if($insert){
	
	$upload->NomeiaFile($id_file);
	$upload->Envia();
	
	echo json_encode(array('erro'=> false, 'ID' => $id_file, 'img' => 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_contratos/anexos_oscip/'.$id_file.'.'.$upload->extensao));
	
}else{
	
	echo json_encode(array('erro'=> true));
	
}

?>