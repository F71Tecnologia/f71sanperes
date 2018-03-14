<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit; 
} 
 
include('../conn.php');
/*
/////COMPROVANTES SAÍDA TIPO TARIFAS
$MES     = 04;
$REGIAO  = 45;
$PROJETO = 3320;

$qr_saida = mysql_query("SELECT * FROM saida 
                        WHERE tipo = 243 
                        AND id_projeto = $PROJETO 
                        AND MONTH(data_vencimento) = $MES 
                        AND status = 2 ");
while($saida = mysql_fetch_assoc($qr_saida)):
   
    
    $verifica_comprovante = mysql_num_rows(mysql_query("SELECT * FROM saida_files WHERE id_saida = '$saida[id_saida]'"));
    if($verifica_comprovante == 0){    
            $insert = "INSERT INTO `saida_files` ( id_saida, tipo_saida_file) VALUES  ( '$saida[id_saida]', '.pdf');";

         
            if(mysql_query($insert)){
               $id_pg = mysql_insert_id(); 
                if(copy('anexo_niteroi.pdf', '../comprovantes/'.$id_pg.'.'.$saida['id_saida'].'.pdf')){  echo $saida['id_saida'].'OK';
                                                                                                     } else {
                                                                                                        echo $saida['id_saida'].'ERRO';
                                                                                                     }
             } 
 
    } else {
        echo 'não alterado';
    } 
endwhile;
 * 
 * 
 */
?>