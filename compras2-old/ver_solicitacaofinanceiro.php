<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "../adm/include/criptografia.php";

include('include/cabecalho.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Imprimir Autoriza&ccedil;&atilde;o</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

<script type="text/javascript" src="include/botoes.js"></script>

<style media="print">
table.menu{
 visibility:hidden;
 margin:0;
 
 
}

</style>
<style media="screen">
table.menu{

 width:100%;
 text-align:center;
 border:3px solid #CCC;
 padding-top:10px;
 margin-bottom:10px;
 
}


</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
     
    <table class="menu">
    
    	<tr>
        	<td> <input type="button" value="IMPRIMIR" class="imprimir"/></td>
            <td><input type="button" value="CONTINUAR" class="continuar"  style="display:none;"/></td>        
        </tr>        
    </table>
    
    <table width="100%"  border="1px" style="border-color:#000; font-size:19px" cellspacing="0" cellpadding="1">
	<tr>
    <td style="border-bottom-width:0px; border-bottom-color:#FFF">
    <table width="100%" cellpadding="0" style="border-color:#000;" border="1px" cellspacing="0">
    <tr>
    <td width="15%" align="center"><img src="../imagens/logomaster<?php echo $row_master['id_master']?>.gif"  /></td>
    <td width="60%" align="center"><b><?php echo $row_master['razao']?></b></td>
    <td width="25%" align="center">UF RESPONSÁVEL<br><b>ILR-RJ</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    <tr>
    <td>
    <table width="100%" cellpadding="0" style="border-color:#000;" border="1" cellspacing="0">
    <tr>
    <td width="60%" align="left">TITULO:<br /><b>SOLICITA&Ccedil;&Atilde;O AO FINANCEIRO</b></td>
    <td width="20%" align="center">CODIFICAÇÃO<br><b>NOR-2000-001</b></td>
    <td width="10%" align="center">VERSÃO<br><b>01</b></td>
    <td width="10%" align="center">PÁGINA<br><b>1 / 1</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
    
    
<table cellpadding="0" cellspacing="0"  width="100%">
    <tr>
    <td colspan="2" style="height:120px; font-size:22px"><br />Nº do Processo: <?php echo $row_compra['num_processo']?></td>
    </tr>
    <tr>
    <td colspan="2" align="center"><b><font size="30">SOLICITA&Ccedil;&Atilde;O AO FINANCEIRO</font></b></td>
    </tr>
       <tr>
    <td colspan="2" style="height:120px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" style="height:630px;" align="left" >
    <table style=" font-size:22px ;margin-left:50px; margin-right:50px" >
    <tr>
    <td> 
      <p>AO<br />
      SETOR DE COMPRAS</p>
      <p>&nbsp;</p>
      <p>FICOU APROVADO PELO(A) DIRETOR(A) DE COMPRAS (funcionario), O OR&Ccedil;AMENTO DE (prestador), PARA AQUIDI&Ccedil;&Atilde;O DE (produto), NO VALOR DE (pre&ccedil;o), TOTALIZANDO (pre&ccedil;o) A SER ENTREGUE EM (data entrega) OBSERVANDO O DESCRITO EM NOSSO REGULAMENTO DE COMPRAS.</p>
<p>ENCAMINHO PROCESSO AO SETOR FINANCEIRO.</p></td>
    </tr>
    </table>
    <table style="margin-left:50px; font-size: 15px; margin-right:50px" width="90%">
    
    <tr>
    <td align="left">&nbsp;</td><td colspan="4" align="left"><?php echo $nome_fornecedor1; ?></td>
    </tr>
        <tr>
    <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>ITEM:</b></td>
    <td align="left"><?php echo $row_compra['nome_produto'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESCRIÇÃO:</b></td>
    <td align="left"><?php echo $row_compra['descricao_produto'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>MARCA:</b></td>
    <td align="left"><?php echo $row_compra['marca1'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>OBSERVA&Ccedil;&Atilde;O:</b></td>
    <td align="left"><?php echo $row_compra['obs1'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top">VALOR DOS IMPOSTOS</td>
    <td align="left" valign="top">VALOR DOS FRETE</td>
    <td align="left" valign="top">DESCONTOS</td>
    <td align="left" valign="top">PREÇO UNITÁRIO</td>
    <td align="left" valign="top">PREÇO FINAL</td>
    </tr>
    <tr>
    <td align="left" valign="top"><?php echo $row_compra['imposto1'];?></td>
    <td align="left" valign="top"><?php echo $row_compra['frete1'];?></td>	
    <td align="left" valign="top"><?php echo $row_compra['desconto1'];?></td>
    <td align="left" valign="top"><?php echo $row_compra['valor_uni1'];?></td>
    <td align="left" valign="top"><?php echo $row_compra['frete1'] + $row_compra['imposto1'] + $row_compra['valor_uni1'] - $row_compra['desconto1'];?></td>
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
    <tr>
    <td colspan="2" style="height:40px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px">_________________________________</td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px">REGULA&Ccedil;&Atilde;O/AVALIA&Ccedil;&Atilde;O</td>
    </tr>
    <tr>
    <td colspan="2" style="height:60px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px"><b>EXEMPLAR Nº 00 - Vigência <?php echo $dia?>/<?php echo $mesnum;?>/<?php echo $ano; ?></b></td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px"><b>PROIBIDA A REPRODUÇÃO</b></td>
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