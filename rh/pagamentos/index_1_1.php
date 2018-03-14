<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include('../../conn.php');
include "../../funcoes.php";
include("../../wfunction.php");
$folha   = $_REQUEST['folha'];
$regiao  = $_REQUEST['regiao'];
$projeto =  $_REQUEST['projeto'];
$ano     = $_REQUEST['ano'];
$mes     = $_REQUEST['mes'];


$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);
while($row_pag = mysql_fetch_assoc($qr_pagamentos)){
    
   $ids_saida[] = $row_pag['id_saida'];
}

$ids_saida = implode(',',$ids_saida);


$qr_saidas = mysql_query("SELECT *, DATE_FORMAT(data_vencimento,'%d/%m/%Y') dt_vencimento 
                            FROM saida 
                            WHERE (YEAR(data_vencimento) = $ano or YEAR(data_pg) = $ano) 
                            AND tipo IN(168) AND MONTH(data_pg) = $mes 
                            AND id_regiao = $regiao AND id_projeto = $projeto
                           ; ");
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />

    </head>
    <body class="novaintra">
        <form action="" method="post" name="form1">
            <div id="content">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $row_regiao['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>IR Autônomo</h2>
                        <p></p>
                    </div>
                </div>
                <br class="clear">
                <table width="600">
                    <tr style="background-color:  #c2c2c2; text-align: center;">
                        <td>COD</td>
                        <td>DESCRIÇÃO</td>
                        <td>DATA DE PAGAMENTO</td>
                        <td>VALOR</td>   
                        <td>STATUS</td>
                    </tr>
                 <?php
                 while($row_saida = mysql_fetch_assoc($qr_saidas)){
                     
                 $cor = ($i++ % 2 == 0)? 'corfundo_um':'corfundo_dois' ;
                 $totalizador += $row_saida['valor'];
                 ?>   
                 <tr class="<?php echo $cor; ?>">
                     <td  align="center"><?php echo $row_saida['id_saida'];?></td>
                     <td><?php echo $row_saida['especifica'];?></td>
                     <td  align="center"><?php echo $row_saida['dt_vencimento'];?></td>
                     <td align="center">R$ <?php echo number_format($row_saida['valor'],2,',','.');?></td>
                     <td  align="center"><img src="../../imagens/bolha<?php echo $row_saida['status'];?>.png" width="20" height="20"/></td>
                 </tr>                    
                <?php
                 }
                ?>
                 <tr>
                     <td colspan="3" style="font-weight: bold;">TOTAL:</td>
                     <td>R$ <?php echo number_format($totalizador,2,',','.');?></td>
                </table>
                
        </form>
    </body>
</html>