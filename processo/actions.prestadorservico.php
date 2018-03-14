<?php
include('../conn.php');
include('../wfunction.php');

//EDITANDO A DATA DO DOCUMENTO DO PRESTADOR
if(validate($_REQUEST['method']) && $_REQUEST['method'] == "editaData"){
    $dt = date("Y-m-d", strtotime(str_replace("/","-",$_REQUEST['valor'])));
    
    $campos = array("data_vencimento"=>$dt);
    $where = array("prestador_documento_id"=>$_REQUEST['id']);
    sqlUpdate("prestador_documentos",$campos,$where);
    
    $return['status'] = 1;
    echo json_encode($return);
    exit;
}

//EXCLUIR DOCUMENTO DO PRESTADOR
if(validate($_REQUEST['method']) && $_REQUEST['method'] == "excluirDoc"){
    
    $resSel = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_documento_id = {$_REQUEST['id']}");
    $row = mysql_fetch_assoc($resSel);
    
    mysql_query("DELETE FROM prestador_documentos WHERE prestador_documento_id = {$_REQUEST['id']}");
    $file = "prestador_documentos/{$row['nome_arquivo']}{$row['extensao_arquivo']}";
    
    if(is_file($file))
        unlink($file);
    
    $return['status'] = 1;
    echo json_encode($return);
    exit;
}
?>
