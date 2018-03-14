<?php 
include "../../conn.php";
require("../../classes/uploadfile.php");

$id_oscip=$_POST['id_oscip'];
$tipo=$_POST['tipo'];

$upload = new UploadFile('anexos_oscip',array('jpg','gif','pdf','png','gif','jpeg','tiff','doc'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();


$sql_insert = "INSERT INTO obrigacoes_oscip_anexos ( id_anexo, id_oscip,extensao, status,tipo_anexo) 
					VALUES
													('' , '$id_oscip','$upload->extensao', '1','$tipo')";
$insert = mysql_query($sql_insert);

$id_file = (int) @mysql_insert_id();



if($insert){
	
	$upload->NomeiaFile($id_file);
	$upload->Envia();
	
	
	if($upload->extensao == 'pdf') {
	$img = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/imagens/Acrobat1.png';	
	} else {
		$img = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_contratos/anexos_oscip/'.$id_file.'.'.$upload->extensao;
		
	}
	
	echo json_encode(array(
				'erro'=> false,
				'ID' => $id_file,
				'img' =>$img,
				'tipo' => $tipo
				)
	);
	
}else{
	
	echo json_encode(array('erro'=> true));
	
}

?>