<?php
$id_saida   = $_REQUEST['id_saida'];
$token      = $_REQUEST['tokenImg']; 
$tipo_anexo = $_REQUEST['tipo_anexo']; //1 - ANEXO, 2 - COMPROVANTE
$arquivo    = $_FILES['file'];


//mysql_query("UPDATE saida SET tipo_arquivo = '', comprovante = '2' WHERE id_saida = '$_REQUEST[Ultimo_ID]' LIMIT 1;");
//mysql_query("INSERT INTO saida_files (tipo_saida_file, id_saida) VALUES ('.$upload->extensao', '$_REQUEST[Ultimo_ID]');");
echo strlen($token);
//$id_saida_file = 


$origem = $arquivo['tmp_name'];

$arquivo_destino = "../../comprovantes/".$id_saida_files.'.'.$id_saida.'.';




?>
