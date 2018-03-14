<?php 
include "../../conn.php";




if($_REQUEST['tipo_anexo'] == 'anexo'){
    $qr_delete = mysql_query("DELETE FROM saida_files WHERE id_saida_file = '$_REQUEST[id]' LIMIT 1");

    if(!$qr_delete){
            echo '0';	
    }
}

if($_REQUEST['tipo_anexo'] == 'comprovante'){
    
    
    $qr_delete = mysql_query("DELETE FROM saida_files_pg WHERE id_pg = '$_REQUEST[id]' LIMIT 1");
     if(!$qr_delete){
            echo '0';	
    }
    
}

?>