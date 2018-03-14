<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "conn.php";




$id_regiao = 45;
$id_projeto = 3320;

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);


if(!file_exists('anderson_arquivos')){    
    mkdir('anderson_arquivos', 0777);
}

$pasta_projeto = 'anderson_arquivos/'.$id_projeto.'_'.$row_projeto['nome'];
if(!file_exists($pasta_projeto)){    
    mkdir($pasta_projeto, 0777);
}


$qr_clt = mysql_query("SELECT  A.matricula, A.nome,B.nome as nome_curso ,foto, id_clt
                    FROM rh_clt as A
                    INNER JOIN curso as B
                    ON A.id_curso = B.id_curso
                    WHERE A.id_regiao = $id_regiao AND A.id_projeto = $id_projeto AND foto = 1
                    ORDER BY A.matricula ");

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title></title>

</head>
<body>
      <table style="background-color:  #cccccc; border-collapse: collapse; page-break-after:  always; font-size: 12px;" border="1" width="800">
                <tr style="background-color:  #FFF;">
                    <td colspan="3">
                        
                        <img src='imagens/logomaster6.gif' alt="" width='120' height='86' /> <br>
                        <div class="descricao"><?php echo $row_projeto['nome'];?></div> 
                    </td>
                </tr>
               <tr>
                   <td>MATRÍCULA</td>
                   <td>NOME</td>
                   <td>FUNÇÃO</td>            
               </tr>
    <?php
   
        while($row_clt = mysql_fetch_assoc($qr_clt)){      
         
         
    
        $foto_origem = "fotosclt/".$id_regiao.'_'.$id_projeto.'_'.$row_clt['id_clt'].'.gif';
        $foto_destino =  $pasta_projeto.'/'.$row_clt['matricula'].'_'.$row_clt['nome'].'.gif';
        
        if(!file_exists($foto_destino)){             
       
            if(!copy($foto_origem,$foto_destino)){

                $msg = 'FALHOU';
            } 

        }
         ?>   
     <tr bgcolor="<?php if($cor++%2==0) { echo "#FAFAFA"; } else { echo "#F3F3F3"; } ?>" style="font-weight:normal; padding:4px;">
        <td><?php echo $row_clt['matricula'];?></td>
        <td><?php echo $row_clt['nome'];?></td>
        <td><?php echo $row_clt['nome_curso'];?></td>  
    </tr>  
    
        <?php   

       
       }
       ?>
  </table>   
    
</body>
</html>
<?php } ?>