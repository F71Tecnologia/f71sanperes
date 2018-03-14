<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$regiao = $_REQUEST['regiao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style28 {
	font-size: 10px;
	font-weight: bold;
}
.style7 {color: #003300}
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="2" background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="left" valign="middle" bgcolor="#FFFFCC"><img src="imagensfinanceiro/cadastrofornecedores.gif" alt="cotas" width="25" height="25" align="absmiddle" /> <span class="style3">VISUALIZA&Ccedil;&Atilde;O DE FORNECEDORES</span>  <span style="float:right;"> <?php include('reportar_erro.php'); ?> </span>
    <span style="clear:right"></span></td>
    <td width="26" rowspan="2" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top" bgcolor="#FFFFFF"><div align="center">
      <p align="left"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<span class="style2">Visualiza&ccedil;&atilde;o dos Fornecedores por data de Cadastro</span></strong></p>
      </div></td>
  </tr>
  
  <tr>
    <td height="27" background="layout/esquerdo.gif">&nbsp;</td>
    <td colspan="2" rowspan="2" align="center" valign="top" bgcolor="#FFFFFF"><table width="100%" border="0" align="center">
      <tr>
      <td width="4%" bgcolor="#FFFFCC" class="style28">Cód</td>
        <td width="13%" bgcolor="#FFFFCC" class="style28">Nome</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">Endere&ccedil;o</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">Tipo de servi&ccedil;os</td>
        <td width="15%" bgcolor="#FFFFCC" class="style28">e-mail</td>
        <td width="11%" bgcolor="#FFFFCC" class="style28">Tel</td>
        <td width="9%" bgcolor="#FFFFCC" class="style28">Contato</td>
      </tr>
      <?php
	  $cont = "1";
	  $result_fornecedores = mysql_query("SELECT * FROM fornecedores WHERE id_regiao = '$regiao'");
	  while($row_fornecedores = mysql_fetch_array($result_fornecedores)){
	  if($cont3 % 2){ $color3="#f0f0f0"; }else{ $color3="#dddddd"; }
      print "
	  <tr class='style28' bgcolor=$color3>
                <td class=border2>$row_fornecedores[0]</td>
				<td class=border2>$row_fornecedores[nome]</td>
        <td class=border2>$row_fornecedores[endereco]</td>
        <td class=border2>$row_fornecedores[produto]</td>
        <td class=border2>$row_fornecedores[email]</td>
        <td class=border2>$row_fornecedores[tel]</td>
        <td class=border3>$row_fornecedores[contato]</td>
      </tr>";
	  $cont ++;
	  }
      ?>
    </table></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="69" background="layout/esquerdo.gif">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="right" valign="top" bgcolor="#FFFFFF" class="style3">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="19" align="left" valign="middle" bgcolor="#FFFFFF" class="style3">
    <div align="left"><strong>
	<?php print "<a href='cadfornecedores.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?>
    <img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" border="0"/>
    CADASTRAR FORNECEDOR </strong></div></td>
    <td align="left" valign="middle" bgcolor="#FFFFFF" class="style3"><div align="left"><strong><img src="imagensfinanceiro/saidas.gif" alt="saida" width="25" height="25" align="absmiddle" /> REMOVER FORNECEDOR </strong></div></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="349" height="19" align="right" valign="top" bgcolor="#FFFFFF" class="style3">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4"><img src="layout/baixo.gif" width="750" height="38" />
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
</body>
</html>
<?php

}

?>