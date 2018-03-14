<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "../adm/include/criptografia.php";

include('include/cabecalho.php');


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


$ano = date('Y');
$dia = date('d');

$mes = sprintf('%02s',date('m'));

$mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'"), 0);




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
            <td><input type="button" value="CONTINUAR" class="continuar"  style="display:none;"/></td>        
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
    <b>SOLICITA&Ccedil;&Atilde;O DE COMPRA</b></td>
    <td width="20%" align="center">CODIFICAÇÃO<br><b>NOR-2000-001</b></td>
    <td width="10%" align="center">VERSÃO<br><b>01</b></td>
    <td width="10%" align="center">PÁGINA<br><b>1 / 1</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
   
   <table style="font-size: 22px;" height:"100%"; width="100%">
    <tr>
    <td colspan="2" style="height:180px;" align="center" valign="middle">Nº do Processo: <?php echo $row_compra['num_processo']; ?></td>
    </tr>
       <tr>
    <td colspan="2" style="height:270px;" align="center" valign="middle"><font size="30"><b>SOLICITA&Ccedil;&Atilde;O DE COMPRA</b></font></td>
    </tr>
    <tr>
    <td colspan="2" style="height:430px;" >
    <table style="margin-left:50px; margin-right:50px">
    <tr>
    <td align="left" valign="top"><b>PRODUTO:</b></td>
    <td align="left"><?php echo $row_compra['nome_produto'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESCRIÇÃO:</b></td>
    <td align="left"><?php echo $row_compra['descricao_produto'];?> </td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>QUANTIDADE:</b></td>
    <td align="left"><?php echo $row_compra['quantidade'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>VALOR MÉDIO:</b></td>
    <td align="left">R$ <?php echo $row_compra['valor_medio'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>NECESSÁRIO PARA:</b></td>
    <td align="left"><?php echo $row_compra['necessidade'];?></td>
    </tr>
    <tr>
    <td align="left" valign="top"><b>DESC. DA NECESIDADE:</b></td>
    <td align="left"><?php echo $row_compra['descricao_produto'];?> </td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td colspan="2" style="height:40px;">&nbsp;</td>
    </tr>    
    </table>
    
    <table style="font-size: 22px;" height:"100%"; width="100%">
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
    <td colspan="2" align="center" style="font-size:18px"><b>EXEMPLAR Nº 00 - Vigência <?php echo $dia?>/<?php echo $mesnum;?>/<?php echo $ano; ?></b></td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px"><b>PROIBIDA A REPRODUÇÃO</b></td>
    </tr>
   
   </table>
   
</td>
</tr>
</table>
  
  
          
   </div>
 </div>
</body>
</html>