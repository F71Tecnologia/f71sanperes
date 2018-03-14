<?php
include("../../../conn.php");
$idProjeto= $_POST['idProjeto'];
$NomeArquivo= $_POST['NomeArquivo'];
$idArquivo= $_POST['idArquivo'];
$caminho="../nfse_anexos/$idProjeto/$NomeArquivo";

if($idArquivo !=''){
    $DeleteArquivo= "DELETE FROM nfse_anexos WHERE id= '$idArquivo'";
    $QueryDeleteArquivo= mysql_query($DeleteArquivo);

    unlink($caminho);
    print '1';
}
?>
