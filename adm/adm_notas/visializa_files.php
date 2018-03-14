<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');



$id_nota = $_GET['id_nota'];

if(isset($_GET['entrada'])){    
    $id_entrada = mysql_real_escape_string($_GET['entrada']);
    $query_files = mysql_query("SELECT A.*,C.nome, C.especifica FROM notas_files as A
                    INNER JOIN notas_assoc as B
                    ON A.id_notas = B.id_notas
                    INNER JOIN entrada as C
                    ON C.id_entrada = B.id_entrada
                    WHERE  B.id_entrada = '$id_entrada' AND A.status = '1'") or die(mysql_error());    
    
}else {
    $query_files = mysql_query("SELECT * FROM notas_files WHERE id_notas = '$id_nota' AND status = '1'") or die(mysql_error());
}

$total_files = mysql_num_rows($query_files);
?>
<html>
    <head>
        <title>Administração de Notas Fiscais</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            .tr_titulo { font-size: 12px; font-weight: bold; }
        </style>
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />     
        <script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script> 
        <style>
        .pdf{
            text-align: center; 
            width:100px; 
            height: 30px;
            padding-top: 80px;
            text-decoration: none; 
            background-image: url('../../imagens/pdf.gif');  
            background-repeat: no-repeat;
            background-position:  center;
            margin:2px;
            float:left;
            display:block;
        }
        .pdf:hover{
            
            background-color:  #0099ff;
            color:#FFF;
            font-weight: bold;
        }
         .imagem{
            text-align: center; 
            width:100px; 
            height: 100px;
            padding-top: 30px;      
            text-decoration: none;
            margin:2px;
            float:left;
            display:block;
            border:1px solid  #999999;
        }
        
        .imagem:hover{
           
            background-color:  #0099ff;
            color:#FFF;
            font-weight: bold;
        }
        
        </style>
    </head>
    <body>
        <div id="corpo">
                
          
                <div id="conteudo" style="text-transform:uppercase;">  
                        <?php 
                        $cont = 0;
                        while($row_files = mysql_fetch_assoc($query_files)):
                            
                          if($cont == 0){  
                              echo '<p>&nbsp;</p>';
                              echo '<h3>'.$row_files['nome'].' - '. $row_files['especifica'].'</h3>'; 
                              $cont = 1;
                              }
                          
                            
                            if($row_files['tipo'] == 'pdf'):?>                        
                                    <a href="notas/<?php echo $row_files['id_file'];?>.<?=$row_files['tipo'];?>" class="pdf" title="Clique para visualizar">
                                    Nota nº <?php echo $row_files['id_notas']?></a>
                        <?php else:?>

                    <a href="notas/<?php echo $row_files['id_file'];?>.<?=$row_files['tipo'];?>"  class="imagem"/>
                    <img src="notas/<?php echo $row_files['id_file'];?>.<?=$row_files['tipo'];?>" width="50" heigth="50" title="Clique para visualizar"/><br>
                     Nota nº <?php echo $row_files['id_notas']?>
                    </a>
                        <?php endif;?>
                        <?php endwhile; ?>
                </div>        
          
            <div style="clear:left;"></div>
            <div id="rodape">
            <?php include('../include/rodape.php'); ?>
            </div>
        </div>
    </body>
</html>

