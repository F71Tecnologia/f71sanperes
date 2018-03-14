<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "../adm/include/criptografia.php";

$id_compra  = mysql_real_escape_string($_GET['compra']);

///usuario
$qr_user  = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user); 

//master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master= '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

//compra
$qr_compra  = mysql_query("SELECT * FROM compra WHERE id_compra = '$id_compra'");
$row_compra = mysql_fetch_assoc($qr_compra);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

<style>

.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
<table cellpadding="0" cellspacing="0" border="2px" width="100%">
<tr><td><br /><br />
   
   <table style="font-size: 12px;; height:"100%"; width="100%"">
   <tr>
    <td><img src="../imagens/logomaster<?php echo $row_user['id_master']; ?>.gif"  /></td><td><?php echo $row_master['razao']; ?><br />Nº do Processo: <?php echo $row_compra['num_processo']; ?></td>
   </tr>
    <tr>
    <td colspan="2" style="height:75px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" align="center"><font size="30"><b> COTAÇÃO ORÇAMENTÁRIA</b></font></td>
    </tr>
       <tr>
    <td colspan="2" style="height:75px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" style="height:730px;" >
    <table style="margin-left:50px; margin-right:50px" width="90%">
    <tr>
    <td align="left" bgcolor="#999999">FORNECEDOR 1</td><td colspan="4">&nbsp;</td>
    </tr>
        <tr>
    <td colspan="5">DESCRIÇÃO DO PROCESSO:</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>ITEM:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESCRIÇÃO:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>MARCA:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>OBSERVA&Ccedil;&Atilde;O:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top">VALOR DOS IMPOSTOS</td>
    <td align="left" valign="top">VALOR DOS FRETE</td>
    <td align="left" valign="top">DESCONTOS</td>
    <td align="left" valign="top">PREÇO UNITÁRIO</td>
    <td align="left" valign="top">PREÇO FINAL</td>
    </tr>
    <tr>
    <td align="left" valign="top">1,00</td>
    <td align="left" valign="top">15,00</td>
    <td align="left" valign="top">2,00</td>
    <td align="left" valign="top">1500,00</td>
    <td align="left" valign="top">1500,00</td>
    </tr>
    <tr>
    <td align="left" valign="top">NECESSÁRIO PARA:</td>
    <td align="left" valign="top">DATA DO PEDIDO</td>
    <td align="left" valign="top">DATA PARA ENTREGA</td>
    <td align="left" valign="top">QUANTIDADE</td>
    <td align="left" valign="top">SOLICITADO POR:</td>
    </tr>
    <tr>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">06/07/2012</td>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">2</td>
    <td align="left" valign="top">ANDERSON</td>
    </tr>
    </table>
    <br />
        <table style="margin-left:50px; margin-right:50px" width="90%">
    <tr>
    <td align="left" bgcolor="#999999">FORNECEDOR 2</td><td colspan="4">&nbsp;</td>
    </tr>
        <tr>
    <td colspan="5">DESCRIÇÃO DO PROCESSO:</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>ITEM:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESCRIÇÃO:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>MARCA:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>OBSERVA&Ccedil;&Atilde;O:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top">VALOR DOS IMPOSTOS</td>
    <td align="left" valign="top">VALOR DOS FRETE</td>
    <td align="left" valign="top">DESCONTOS</td>
    <td align="left" valign="top">PREÇO UNITÁRIO</td>
    <td align="left" valign="top">PREÇO FINAL</td>
    </tr>
    <tr>
    <td align="left" valign="top">1,00</td>
    <td align="left" valign="top">15,00</td>
    <td align="left" valign="top">2,00</td>
    <td align="left" valign="top">1500,00</td>
    <td align="left" valign="top">1500,00</td>
    </tr>
    <tr>
    <td align="left" valign="top">NECESSÁRIO PARA:</td>
    <td align="left" valign="top">DATA DO PEDIDO</td>
    <td align="left" valign="top">DATA PARA ENTREGA</td>
    <td align="left" valign="top">QUANTIDADE</td>
    <td align="left" valign="top">SOLICITADO POR:</td>
    </tr>
    <tr>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">06/07/2012</td>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">2</td>
    <td align="left" valign="top">ANDERSON</td>
    </tr>
    </table>
    <br />
        <table style="margin-left:50px; margin-right:50px" width="90%">
    <tr>
    <td align="left" bgcolor="#999999">FORNECEDOR 3</td><td colspan="4">&nbsp;</td>
    </tr>
        <tr>
    <td colspan="5">DESCRIÇÃO DO PROCESSO:</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>ITEM:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESCRIÇÃO:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>MARCA:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>OBSERVA&Ccedil;&Atilde;O:</b></td>
    <td align="left">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top">VALOR DOS IMPOSTOS</td>
    <td align="left" valign="top">VALOR DOS FRETE</td>
    <td align="left" valign="top">DESCONTOS</td>
    <td align="left" valign="top">PREÇO UNITÁRIO</td>
    <td align="left" valign="top">PREÇO FINAL</td>
    </tr>
    <tr>
    <td align="left" valign="top">1,00</td>
    <td align="left" valign="top">15,00</td>
    <td align="left" valign="top">2,00</td>
    <td align="left" valign="top">1500,00</td>
    <td align="left" valign="top">1500,00</td>
    </tr>
    <tr>
    <td align="left" valign="top">NECESSÁRIO PARA:</td>
    <td align="left" valign="top">DATA DO PEDIDO</td>
    <td align="left" valign="top">DATA PARA ENTREGA</td>
    <td align="left" valign="top">QUANTIDADE</td>
    <td align="left" valign="top">SOLICITADO POR:</td>
    </tr>
    <tr>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">06/07/2012</td>
    <td align="left" valign="top">10/07/2012</td>
    <td align="left" valign="top">2</td>
    <td align="left" valign="top">ANDERSON</td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td colspan="2" style="height:40px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2">_________________________________</td>
    </tr>
    <tr>
    <td colspan="2"><?php echo $row_user['nome'];?></td>
    </tr>
    <tr>
    <td colspan="2" style="height:60px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2">Rio de Janeiro, 07 de Julho de 2012</td>
    </tr>
    <tr>
    <td colspan="2" style="height:40px;"><br /><br /></td>
    </tr>
   </table>
   
</td>
</tr>
</table>
  
  
   
   <div class="rodape2">

  
  </div>
  
 
          
   </div>
 </div>
</body>
</html>