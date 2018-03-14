<?php 

include "../../conn.php";
include "../../classes/uploadfile.php";

//$id_entrada = $_REQUEST['id_entrada'];

$id_entrada = 2224;

//if (isset($_GET["delete"]) && $_GET["delete"] == true) {
//    $name = $_POST["filename"];
//    if (file_exists('uploads/' . $name)) {
//        unlink('uploads/' . $name);
//        echo json_encode(array("res" => true));
//    } else {
//        echo json_encode(array("res" => false));
//    }
//
//} else {

$diretorio = "uploads/";
$upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
$upload->arquivo($_FILES['Filedata']);
$upload->verificaFile();
mysql_query("INSERT INTO entrada_files (id_entrada,tipo_files) VALUES ('2224','.$upload->extensao');");
$query_max = mysql_query("SELECT MAX(id_files) FROM entrada_files");
$id = mysql_result($query_max,0);

$upload->NomeiaFile($id);
$upload->Envia();

echo $diretorio.'/'.$id.'.'.$upload->extensao;

//}
?>