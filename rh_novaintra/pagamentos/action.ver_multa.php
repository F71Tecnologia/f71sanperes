<?php 
require("../../conn.php");


$id_recisao = $_REQUEST['id_rescisao'];
$id_clt     = $_REQUEST['id_clt']; 

$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = $id_clt");
$row_clt = mysql_fetch_assoc($qr_clt);

$query_saida_multa = mysql_query("SELECT * FROM saida_files as a 
                                                   INNER JOIN saida as b
                                                   ON a.id_saida = b.id_saida
                                                   WHERE b.id_clt = '$id_clt' AND b.tipo = '167' AND a.multa_rescisao = 1 AND b.status !=0");
$row_saida_multa = mysql_fetch_assoc($query_saida_multa);                
$num_saida_multa = mysql_num_rows($query_saida_multa);

$anexo = $row_saida_multa['id_saida_file'].'.'.$row_saida_multa['id_saida'].$row_saida_multa['tipo_saida_file'];
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
    <h3><?php echo $row_clt['id_clt']?> - <?php echo $row_clt['nome'];?> </h3>
    <form action="" method="post" enctype="multipart/form-data" name="form1" id="form">
        <table width="90%" border="1" align="center" cellpadding="5" cellspacing="0">
            <tr class="titulo">
                <td>Nº DA SAÍDA</td>
                <td>DATA DE VENCIMENTO</td>
                <td>DATA DE PAGAMENTO</td>
                <td>ANEXO</td>
            </tr>
            <tr>
                <td  align="center"><?php echo $row_saida_multa['id_saida']?></td>
                <td  align="center"><?php echo implode('/',array_reverse(explode('-',$row_saida_multa['data_vencimento'])))?></td>
                <td  align="center"><?php echo implode('/',array_reverse(explode('-',$row_saida_multa['data_pg'])))?></td>
                <td align="center">
                   <a href="../../comprovantes/<?php echo $anexo?>"  target="_blank">
                       <img src="../../imagens/ver_anexo.gif" width="15" height="15"  />
                    </a>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>