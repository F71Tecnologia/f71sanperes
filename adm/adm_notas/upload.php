<?php 
include "../../conn.php";
require("../../classes/uploadfile.php");
require_once ("../../wfunction.php");

$id_nota = $_REQUEST['id_nota'];

$upload = new UploadFile('notas',array('jpg','gif','pdf'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();

$sql_insert = "INSERT INTO notas_files (id_notas, tipo, status) VALUES ('$id_nota' , '$upload->extensao', '1')";
$insert = mysql_query($sql_insert);

$id_file = (int) @mysql_insert_id();

if($insert){
	
	$upload->NomeiaFile($id_file);
	$upload->Envia();
	
        $query = "SELECT id_entrada FROM notas_assoc WHERE id_notas = $id_nota";
        $result = mysql_query($query);
        if(mysql_num_rows($result) > 0){
            
            $id_entrada = mysql_fetch_assoc($result);
            
            $updateEntradaFile = "UPDATE entrada_files SET status = '0' WHERE id_entrada = $id_entrada";
            $queryEntradaFile = mysql_query ($updateEntradaFile);
            
            $query2 = "INSERT INTO entrada_files (id_entrada,tipo_files,status) VALUES ({$id_entrada['id_entrada']},'.$extensao',1)";
            $r2 = mysql_query($query2);
            $id_entrada_files = mysql_insert_id();
            copy($diretorio.'/'.$id_file.'.'.$extensao, "../../novoFinanceiro/comprovantes/entrada/{$id_entrada_files}.$extensao");
            copy($diretorio.'/'.$id_file.'.'.$extensao, "../../finan/comprovantes/entrada/{$id_entrada_files}.$extensao");
            
        }
        
	echo json_encode(array('erro'=> false, 'ID' => $id_file, 'img' => 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_notas/notas/'.$id_file.'.'.$upload->extensao));
	
}else{
	
	echo json_encode(array('erro'=> true));
	
}

?>