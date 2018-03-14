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
 margin-bottom:30px;
 
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
            <td><input type="button" value="DEFERIR  " class="deferir"  style="display:none;"/></td>  
               <td><input type="button" value="INDEFERIR" class="indeferir"  style="display:none;"/>
               <input type="hidden" name="id_compra" value="<?php echo $id_compra;?>" id="id_compra"/></td>        
        </tr>        
    </table>
    
<table cellpadding="0" cellspacing="0"  width="100%">
<tr><td>
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
    <td width="60%" align="left">TITULO:<br />
      <b>APROVA&Ccedil;&Atilde;O DA AVALIA&Ccedil;&Atilde;O</b></td>
    <td width="20%" align="center">CODIFICAÇÃO<br><b>NOR-2000-001</b></td>
    <td width="10%" align="center">VERSÃO<br><b>01</b></td>
    <td width="10%" align="center">PÁGINA<br><b>1 / 1</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
    <tr>
    <td colspan="2" style="height:250px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" align="center"><b><font size="30">APROVA&Ccedil;&Atilde;O DA AVALIA&Ccedil;&Atilde;O</font></b></td>
    </tr>
       <tr>
    <td colspan="2" style="height:200px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" style="height:430px;" align="left" >
    <table  style="margin-left:50px; margin-right:50px" >
    <tr>
    <td style="font-size:22px"> 
      <p>AVALIAMOS AS TOMADAS DE DECIS&Atilde;O E DEFERIMOS A COMPRA DO(S) ITENS RELACIONADOS PELO SETOR DE COMPRAS:</p>
      <p><?php echo $row_compra['descricao_compra'];?></p>
     <!-- <table>
      	<tr>
        	<td>Fornecedor:</td>
            <td> <?php echo $fornecedor_escolhido;?></td>
        </tr>
        <tr>
        	<td>PRODUTO:</td>
            <td><?php echo $row_compra['nome_produto'];?></td>
        </tr>
        <tr>
        	<td>PREÇO</td>
        	<td><?php echo $row_compra['preco_final'];?></td>
        </tr>
        <tr>
        	<td>Prazo de entrega:</td>
        	<td><?php echo implode('/',array_reverse(explode('-',$row_compra['prazo']))); ?></td>
        </tr>
        
        
      </table>-->
      
    </td>
    </tr>
    </table>
    </td>
    <tr> 
    <td colspan="2" style="height:40px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" style="font-size:22px">_________________________________</td>
    </tr>
    <tr>
    <td colspan="2" style="font-size:22px">REGULA&Ccedil;&Atilde;O/AVALIA&Ccedil;&Atilde;O</td>
    </tr>
     <tr>
    	<td align="center" style="font-size:22px"><?php echo $row_user['nome']; ?></td>
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