<?php

require("../conn.php");
require("../classes/uploadfile.php");
if (!empty($_FILES['atestado'])) {
    $data = date('YmdHis');
    $upload = new UploadFile("../anexo_atestado", array('jpg', 'gif', 'png', 'pdf'));
    $upload->arquivo($_FILES['atestado']);
    $upload->verificaFile();
    $upload->Subpasta($_POST['reg']);
    $upload->Subpasta($_POST['projeto']);
    $upload->Subpasta($_POST['tipo_contratacao'] . '_' . $_POST['ID_participante']);
    $upload->NomeiaFile($_POST['tipo_contratacao'] . '_' . $_POST['ID_participante'] . '_' . $data);
    $upload->Envia();
    if ($upload->erro == 1) {
        echo $upload->erro;
    } else {
        $caminho .= $_POST['reg'] . "/";
        $caminho .= $_POST['projeto'] . "/";
        $caminho .= $_POST['tipo_contratacao'] . '_' . $_POST['ID_participante'] . "/";
        $caminho .= $_POST['tipo_contratacao'] . '_' . $_POST['ID_participante'] . '_' . $data . "." . $upload->extensao;
        
        $query = "INSERT INTO anexo_eventos (nome,id_evento,data) VALUE 
                  ('" . $caminho . "','{$_REQUEST['id_evento']}',NOW())";
        mysql_query($query) or die(mysql_error());
        echo 'Arquivo ';

        echo $caminho;
        echo " salvo com sucesso!";
    }
}
?>

