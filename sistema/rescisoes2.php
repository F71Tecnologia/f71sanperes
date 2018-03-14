<?php
include('../conn.php');
include('../funcoes.php');
include('../classes/mpdf54/mpdf.php');


$id_projeto = $_REQUEST['projeto'];
?>


<table style="border-collapse: collapse; font-size: 12px;" border="1">
    
<?php
$qr_clt = mysql_query("SELECT *    
                        FROM rh_recisao WHERE id_regiao = 45 AND id_projeto = '3302' AND status = 1"); 

while($row_recisao = mysql_fetch_assoc($qr_clt)){
    
   
    $nome_pasta = 'rescisao/'.$row_recisao['id_clt'].'_'.$row_recisao['nome'];
                       
    if(!file_exists($nome_pasta)){                           
        mkdir($nome_pasta);
    }
                       
                       
                       
    $link = str_replace('+', '--', encrypt("$row_recisao[id_regiao]&$row_recisao[id_clt]&$row_recisao[id_recisao]")); 
    
     if(substr($row_recisao['data_proc'],0,10) >= '2013-04-04'){                  
                  $link_nova_rescisao = "nova_rescisao_2.php?enc=$link" ;                    
                } else {
                     $link_nova_rescisao = "nova_rescisao.php?enc=$link" ;
                }
                        
                        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/rh/recisao/'.$link_nova_rescisao;
                        $logUrl[$id_saida] = $url;
                        $descricao = "$row_recisao[id_clt]_$row_recisao[nome].pdf";
                        $saveAS = $nome_pasta.'/'.$descricao;
                        $linkDownload[$id_saida][] = $descricao;
                        $arrayFilesRemove[] = $saveAS;

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_NOBODY, false);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $html = curl_exec($ch);
                        curl_close($ch);

                        $mpdf = new mPDF();
                        $mpdf->SetDisplayMode('fullpage');
                        $html = utf8_encode($html);
                        $stylesheet = file_get_contents('../rh/recisao/rescisao.css');
                        $mpdf->WriteHTML($stylesheet, 1);
                        $mpdf->WriteHTML($html);
                        $mpdf->Output($saveAS, "F");
                        unset($mpdf);
                        $recisoes[$id_saida] = $url;                        
         
    
            $qr_recisao_comprovante  = mysql_query(" SELECT  CONCAT(C.id_pg,'.',C.id_saida,'_pg.pdf') as rescisao_comprovante, C.id_pg
                                       FROM pagamentos_especifico as A
                                       INNER JOIN saida as B
                                       ON A.id_saida = B.id_saida
                                       INNER JOIN saida_files_pg as C
                                       ON C.id_saida = B.id_saida
                                       WHERE A.id_clt = '$row_recisao[id_clt]' AND B.estorno NOT IN(1,2);");               
         
             while($row_rescisao_comprovante = mysql_fetch_assoc($qr_recisao_comprovante)){
              
              
                 
                     $origem_rescisao_comprovante  =  "../comprovantes/$row_rescisao_comprovante[rescisao_comprovante]";
                     $destino_rescisao_comprovante = $nome_pasta.'/anexo_'.$row_rescisao_comprovante['id_pg'].'.pdf';
            
                     
                       
                    if(file_exists($origem_rescisao_comprovante)){
                              if(copy($origem_rescisao_comprovante, $destino_rescisao_comprovante)){
                                   var_dump(file_exists($origem_rescisao_comprovante));
                 echo '<br>';
                                  
                              } else {
                                  echo $row_recisao[id_clt].'___comprovante rescisão erro'; 
                                  echo '<br>';
                              }
                    }
             }
                
           
      
           /////MULTA ANEXO
              $qr_multa_anexo = mysql_query("SELECT CONCAT(A.id_saida_file,'.',A.id_saida,'.pdf') as multa_anexo, A.id_saida_file FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            WHERE B.id_clt = '$clt[id_clt]' AND B.tipo IN(167,170) AND A.multa_rescisao = 1") or die(mysql_error());
   
            $row_multa_anexo = mysql_fetch_assoc($qr_multa_anexo);
                    
             $origem_multa_anexo    =  "../comprovantes/$row_multa_anexo[multa_anexo]";
             $destino_multa_anexo = $nome_pasta.'/anexo_multa_'.$row_multa_anexo[id_saida_file].'.pdf';
            
            if(file_exists($origem_multa_anexo)){
           
                     
                     if(copy($origem_multa_anexo, $destino_multa_anexo)){
                      
                     } else {
                         echo $row_recisao[id_clt].'___anexo multa erro'; 
                     }
                    
            }         
              
        /////MULTA COMPROVANTE        
        $qr_multa_comprovante = mysql_query("SELECT CONCAT(C.id_pg,'.',A.id_saida,'_pg.pdf') as multa_comprovante, C.id_pg FROM saida_files as A
                                INNER JOIN saida as B
                                ON A.id_saida = B.id_saida
                                INNER JOIN saida_files_pg as C
                                ON C.id_saida = B.id_saida
                                WHERE B.id_clt = '$clt[id_clt]' AND B.tipo IN(167,170) AND A.multa_rescisao = 1");           
       $row_multa_comprovante = mysql_fetch_assoc($qr_multa_anexo);
                    
             $origem_multa_comprovante    =  "../comprovantes/$row_multa_comprovante[multa_comprovante]";
             $destino_multa_comprovante = $nome_pasta.'/anexo_multa_'.$row_multa_comprovante[id_pg].'.pdf';
            
            if(file_exists($origem_multa_comprovante)){           
                     
                     if(copy($origem_multa_comprovante, $destino_multa_comprovante)){
                      
                     } else {
                         echo $row_recisao[id_clt].'___comprovante multa erro'; 
                     }
                    
            }                        
                       
                        
                      
}




?>
</table>