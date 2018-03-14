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
            <td></td>  
               <td><input type="button" value="CONTINUAR" class="continuar"  style="display:none;"/>
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
      <b>LICITA&Ccedil;&Atilde;O</b></td>
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
    <td colspan="2" align="center"><b><font size="30">LICITA&Ccedil;&Atilde;O</font></b></td>
    </tr>
       <tr>
    <td colspan="2" style="height:200px;">&nbsp;</td>
    </tr>
    <tr>
    <td colspan="2" style="height:430px;" align="left" >
    <table  style="margin-left:50px; margin-right:50px" >
    <tr>
    <td style="font-size:22px"> 
      <p>DE ACORDO COM OS CRIT&Eacute;RIOS definidos no edital <?=$row_compra['nedital']?>:</p>
      <p>&nbsp;</p>
      <p>APÓS ANÁLISE Da comiss&Atilde;o de licita&Ccedil;&Atilde;o, dos or&Ccedil;amentos dos fornecedores:</p>
      <?
	  $qry_forn = mysql_query("SELECT * FROM fornecedor_site WHERE id_compra='$id_compra' AND anexo_proposta <> '0'");
	  
	  while($dados = mysql_fetch_assoc($qry_forn))
	  {
	  echo "<p>".$dados['razao']." / ".$dados['cnpj']."</p>";  
	  }
	  
	  $qry_nomef = mysql_query("SELECT * FROM fornecedor_site WHERE fornecedor_site_id='$row_compra[fornecedor_escolhido]'");
	  $dadosnomef = mysql_fetch_assoc($qry_nomef);
	  ?>
      <p>&nbsp;</p>
      <p>o fornecedor <?=$dadosnomef['razao']; ?> foi escolhido por apresentar conformidade ao edital apresentando as melhores condi&Ccedil;&Otilde;es para sua contrata&Ccedil;&Atilde;o.</p>
      <p>&nbsp;</p>
      <p>encaminho ao setor jur&Iacute;dico o processo para conclus&Atilde;o de cadastramento e levantamento da documenta&Ccedil;&Atilde;o necess&Aacute;ria</p></td>
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
    <td colspan="2" style="font-size:22px"><?php echo $row_user['nome'];?></td>
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


<?php

switch($row_compra['fornecedor_escolhido']){

	case 1: $escolhido = $row_compra["fornecedor1"];
			$valor = $row_compra["preco1"];
	break;
	
	case 2: $escolhido = $row_compra["fornecedor2"];
			$valor = $row_compra["preco2"];
	break;
	
	case 3: $escolhido = $row_compra["fornecedor3"];
			$valor = $row_compra["preco3"];
	break;

	
}


$qry_fornecedor = mysql_query("SELECT * FROM fornecedores WHERE id_fornecedor='$escolhido'");
$dados_forn = mysql_fetch_assoc($qry_fornecedor);


$result_cont = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$dados_forn[id_regiao]'");
$row_cont = mysql_num_rows($result_cont);
$row_cont = $row_cont + 1;
$num_id = sprintf("%03s", $row_cont);
$numero = $num_id."/".date('Y');


mysql_query("INSERT INTO prestadorservico (id_regiao, id_projeto, numero, aberto_por, aberto_em, contratado_por, contratado_em, c_fantasia, c_razao, c_endereco, c_cnpj, c_ie, c_im, c_tel, c_email, c_responsavel, valor, id_compra) VALUES('$dados_forn[id_regiao]', '$dados_forn[id_projeto]', '$numero',  '$_COOKIE[logado]', NOW(), '$_COOKIE[logado]', NOW(), '$dados_forn[nome]', '$dados_forn[razao]', '$dados_forn[endereco]', '$dados_forn[cnpj]', '$dados_forn[ie]', '$dados_forn[im]', '$dados_forn[tel]', '$dados_forn[email]', '$dados_forn[contato]', '$valor', '$_GET[compra]')");


?>
  
  
   
   <div class="rodape2">

  
  </div>
  
 
          
   </div>
 </div>
</body>
</html>