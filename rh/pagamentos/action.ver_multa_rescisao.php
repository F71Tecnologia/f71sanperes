<?php 
require("../../conn.php");
require("../../funcoes.php");


$id_recisao = $_REQUEST['id_rescisao'];
$id_clt     = $_REQUEST['id_clt']; 
$multa = $_REQUEST['multa'];
$rescisao = $_REQUEST['rescisao'];

$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);


if(!empty($multa)){
    //MULTA
    $query_saida = mysql_query("SELECT * FROM saida_files as a 
                                                       INNER JOIN saida as b
                                                       ON a.id_saida = b.id_saida
                                                       WHERE b.id_clt = '$id_clt' AND b.tipo IN(167,170) AND a.multa_rescisao = 1 AND b.status !=0");
    $titulo = 'MULTA';

} elseif(!empty($rescisao)){
    
    $query_saida = mysql_query("SELECT B.id_saida, B.data_vencimento, B.data_pg, B.valor
                                FROM pagamentos_especifico AS A 
                                INNER JOIN saida as B
                                ON A.id_saida = B.id_saida  
                                WHERE B.status != '0' 
                                AND  A.id_clt = '$id_clt' AND (B.tipo = '51' or B.tipo = '170') ");
    $titulo = 'RESCISÃO';

    
}
 
$num_saida = mysql_num_rows($query_saida);
?>

<html>
<head>    
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>

<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>

<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js"></script>
<link rel="stylesheet" type="text/css" href="../../uploadfy/css/uploadify.css"/>
<style>
    h3{ font-size: 14px;}
    table{
        font-size: 12px;
        border-collapse: collapse;
}
table tr.titulo{
    background-color:  #cccccc;
    font-weight: bold;
}
</style>
</head>
<body>
    <h4><?php echo $titulo;?> </h4>
    <h3><?php echo $row_clt['id_clt']?> - <?php echo $row_clt['nome'];?> </h3>
    <form action="" method="post" enctype="multipart/form-data" name="form1" id="form">
        <table width="90%" border="1" align="center" cellpadding="5" cellspacing="0">
            <tr class="titulo">
                <td>Nº DA SAÍDA</td>
                <td>DATA DE VENCIMENTO</td>
                <td>DATA DE PAGAMENTO</td>
                <td>VALOR</td>
                <td>ANEXO</td>
                <td>COMPROVANTE</td>
            </tr>
            <?php while($row_saida = mysql_fetch_assoc($query_saida)){
                
                ///COMPROVANTE DE PAGAMENTO
                $qr_saida_files_pg = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_saida[id_saida]'");                 
                $row_comprovante = mysql_fetch_assoc($qr_saida_files_pg);
                
                $comprovante = '../../comprovantes/'.$row_comprovante['id_pg'].'.'.$row_comprovante['id_saida'].'_pg'.$row_comprovante['tipo_pg'];
                  
                
              if(!empty($multa)){
                  $anexo = '../../comprovantes/'.$row_saida['id_saida_file'].'.'.$row_saida['id_saida'].$row_saida['tipo_saida_file'];
                  
              }  elseif(!empty($rescisao)){
                  $link_rescisao = str_replace('+','--',encrypt("$row_clt[id_regiao]&$id_clt&$id_recisao"));
                  $anexo = '../recisao/nova_rescisao_2.php?enc='.$link_rescisao;
              }
                
                ?>
            
            <tr>
                <td  align="center"><?php echo $row_saida['id_saida']?></td>
                <td  align="center"><?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?></td>
                <td  align="center"><?php echo implode('/',array_reverse(explode('-',$row_saida['data_pg'])))?></td>
                <td  align="center"><?php echo number_format($row_saida['valor'],2,',','.')?></td>
                <td align="center">
                   <a href="<?php echo $anexo?>"  target="_blank">
                       <img src="../../imagens/ver_anexo.gif" width="15" height="15"  />
                    </a>
                </td>
                <td align="center">
                    <?php if(mysql_num_rows($qr_saida_files_pg) !=0 ) {?>
                   <a href="<?php echo $comprovante;?>"  target="_blank">
                       <img src="../../imagens/ver_anexo.gif" width="15" height="15"  />
                    </a>
                 <?php } ?>
                </td>
               
            </tr>
           
            
            <?php } ?>
        </table>
    </form>
</body>
</html>