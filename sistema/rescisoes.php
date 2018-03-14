<?php
include('../conn.php');

$id_projeto = $_REQUEST['projeto'];
?>


<table style="border-collapse: collapse; font-size: 12px;" border="1">
    
<?php
$qr_clt = mysql_query("SELECT *
    
                        FROM rh_recisao WHERE id_regiao = 45 AND id_projeto = '3302' AND status = 1"); 
while($clt = mysql_fetch_assoc($qr_clt)){
    
   

    $qr_multa_anexo = mysql_query("SELECT * FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            WHERE B.id_clt = '$clt[id_clt]' AND B.tipo IN(167,170) AND A.multa_rescisao = 1") or die(mysql_error());
   
    
    $qr_multa_comprovante = mysql_query("SELECT C.* FROM saida_files as A
                            INNER JOIN saida as B
                            ON A.id_saida = B.id_saida
                            INNER JOIN saida_files_pg as C
                            ON C.id_saida = B.id_saida
                            WHERE B.id_clt = '$clt[id_clt]' AND B.tipo IN(167,170) AND A.multa_rescisao = 1");
    
    
    $qr_recisao_anexo = mysql_query("select CONCAT(C.id_saida_file,'.',C.id_saida,'.pdf') as rescisao_anexo 
                                     from pagamentos_especifico as A
                                    INNER JOIN saida as B
                                    ON A.id_saida = B.id_saida
                                    INNER JOIN saida_files as C
                                    ON C.id_saida = B.id_saida
                                    WHERE A.id_clt = '$clt[id_clt]';") or die(mysql_error());
    
     $qr_recisao_comprovante  = mysql_query(" SELECT  CONCAT(C.id_pg,'.',C.id_saida,'.pdf') as rescisao_comprovante 
                                FROM pagamentos_especifico as A
                                INNER JOIN saida as B
                                ON A.id_saida = B.id_saida
                                INNER JOIN saida_files_pg as C
                                ON C.id_saida = B.id_saida
                                WHERE A.id_clt = '$clt[id_clt]';");
    
 
    
   
   
   
   
    if($clt['id_projeto'] != $projetoAnt){
        
        $nome_pasta = $clt[nome_projeto].' Fotos'; 
        
        echo '<tr height="50">
                <td colspan="4">'.$clt[nome_projeto].'</td>
            </tr>';
        echo '<tr>
            <td>id_clt</td>
                <td>NOME</td> 
                <td>DT DEMISSÃO</td>
                <td>ANEXO RESCISÃO</td>
                <td>COMPROVANTE RESCISÃO</td>
                <td>ANEXO MULTA</td>
                <td>COMPROVANTE MULTA</td>
             </tr>';
    }
    ?>
    <tr>            
            <td><?php echo $clt['id_clt']?></td>
            <td><?php echo $clt['nome']?></td>
            <td><?php echo $clt['data_demi'];?></td>
           <td>              
            <?php                
                  while($row_rescisao_anexo = mysql_fetch_assoc($qr_recisao_anexo) ){ 
                    $origem_rescisao_anexo =  "../comprovantes/$row_rescisao_anexo[rescisao_anexo]";
                    echo "<a href='$origem_rescisao_anexo'>VER</a>";
                    echo '<br>';            }           
            ?>     
           </td>
           <td>               
            <?php
                while($row_rescisao_comprovante = mysql_fetch_assoc($qr_recisao_comprovante) ){  
                 $origem_rescisao_comprovante =  "../comprovantes/$row_rescisao_comprovante[rescisao_comprovante]";
                echo "<a href='$origem_rescisao_comprovante'>VER</a>";
                echo '<br>';
            }           
            ?>     
           </td>
           
           <td>              
            <?php                
                  while($row_multa_anexo = mysql_fetch_assoc($qr_multa_anexo) ){ 
                    $origem_multa_anexo =  "../comprovantes/$row_multa_anexo[id_saida_file].$row_multa_anexo[id_saida].pdf";
                    echo "<a href='$origem_multa_anexo'>VER</a>";
                    echo '<br>';            }           
            ?>     
           </td>
           <td>               
            <?php
                while($row_multa_comprovante = mysql_fetch_assoc($qr_multa_comprovante) ){  
                 $origem_multa_comprovante =  "../comprovantes/$row_multa_comprovante[id_pg].$row_multa_comprovante[id_saida]_pg.pdf";
                echo "<a href='$origem_multa_comprovante'>VER</a>";
                echo '<br>';
            }           
            ?>     
           </td>
    </tr> 
   <?php 
    
    $projetoAnt = $clt['id_projeto'];
    
}



?>
</table>