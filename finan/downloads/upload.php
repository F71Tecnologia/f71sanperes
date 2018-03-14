<?php
include '../../conn.php';

$id_entrada = $_REQUEST['id_entrada'];

if (isset($_GET["delete"]) && $_GET["delete"] == true) {
    $name = $_POST["filename"];
    
    $file_exp = explode(".", $name);
    $id_file = $file_exp[0];
    
    if (file_exists('uploads/' . $name)) {
        unlink('uploads/' . $name);
        mysql_query("UPDATE entrada_files SET status = '0' WHERE id_files = '$id_file' LIMIT 1");
        echo json_encode(array("res" => true));
    } else {
        echo json_encode(array("res" => false));
    }
    
} else {
    $file = $_FILES["file"]["name"];
    $filetype = $_FILES["file"]["type"];
    $filesize = $_FILES["file"]["size"];
    
    if (!is_dir("uploads/"))
        mkdir("uploads/", 0777);
    
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    
    $query_verifaca = "SELECT * FROM entrada_files ORDER BY id_files DESC LIMIT 1";
    $sql_verifica = mysql_query($query_verifaca) or die("Erro ao verificar entrada");
    $dados = mysql_fetch_assoc($sql_verifica);
    $id_arq = $dados['id_files']+1;
    
    $nome_arq = ($dados['id_files']+1) . "." . $ext;
    $nome_arq2 = ($dados['id_files']) . $dados['tipo_files'];
    
    if($_REQUEST['method'] == "traz_id"){
        $retorno = array("nome" => $nome_arq2);
    }
    
    if (isset($file) && move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $nome_arq)) {
        $sql = "INSERT INTO entrada_files (id_files, id_entrada, tipo_files, status) VALUES ({$id_arq}, {$id_entrada}, '.{$ext}', 1)";
        mysql_query($sql) or die(mysql_error());
    }
    
    echo json_encode($retorno);
    exit();
}
?>