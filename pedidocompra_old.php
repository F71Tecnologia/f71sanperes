<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user_pedido'");
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['pagina'])){

$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];
$id_user = $_COOKIE['logado'];

$result = mysql_query("SELECT *,date_format(data_produto, '%d/%m/%Y')as data_produto, date_format(data_requisicao, '%d/%m/%Y')as data_requisicao FROM compra where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
$row_user = mysql_fetch_array($result_user);

$result_user_logado = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'", $conn);
$row_user_logado = mysql_fetch_array($result_user_logado);

$data = date('d/m/Y');

if($row['tipo'] == "1"){
$tipo = "Produto";
}else{
$tipo = "Serviço";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - GEST&Atilde;O DE COMPRAS</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
-->
</style>
<link href="net.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style31 {font-size: 12px; color: #000000; }
.style37 {
	font-size: 16px;
	color: #FF0000;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="left" valign="middle" bgcolor="#FFFFCC"><img src="imagensmenu2/compras.gif" alt="cotas" width="20" height="20" align="absmiddle" /> <span class="style3">VISUALIZANDO PEDIDO</span></td>
    <td width="26" background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  
  <tr>
    <td height="27" background="layout/esquerdo.gif">&nbsp;</td>
    <td colspan="2" rowspan="2" align="center" valign="middle"><br />
    <table width="98%" border="0" cellpadding="0" cellspacing="2" bgcolor="#E6E6E6">
      <tr>
        <td height="40" colspan="4" align="center" valign="middle"><span class="style37">N&uacute;mero do Pedido:&nbsp;<?php print "$row[num_processo]";?></span></td>
        </tr>
      <tr>
        <td width="18%" height="40" valign="middle"><div align="right" class="style31">Nome do Pedido:</div></td>
        <td height="40" colspan="3" align="left" valign="middle"><strong>&nbsp;<?php print "$row[nome_produto]";?></strong></td>
        </tr>
      <tr>
        <td height="40" valign="middle"><div align="right" class="style31">Descri&ccedil;&atilde;o do Pedido:</div></td>
        <td height="40" colspan="3" align="left" valign="middle"><strong>&nbsp;<?php print "$row[descricao_produto]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"><div align="right" class="style31">Necessidade:</div></td>
        <td height="40" colspan="3" align="left" valign="middle"><strong>&nbsp;<?php print "$row[necessidade]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"><div align="right" class="style31">Usu&aacute;rio:</div></td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row_user[nome1]";?></strong></td>
        <td height="40" align="left" valign="middle"><div align="right" class="style31">Quantidade:</div></td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[quantidade]";?></strong></td>
      </tr>
      <tr>
        <td height="40" valign="middle"><div align="right" class="style31">Pedido para a Data:</div></td>
        <td width="34%" height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[data_produto]";?></strong></td>
        <td width="25%" height="40" valign="middle"><div align="right" class="style31">Data de Processamento:</div></td>
        <td width="23%" height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$row[data_requisicao]";?></strong></td>
      </tr>
      
      <tr>
        <td height="40" valign="middle"><div align="right" class="style31">Valor M&eacute;dio:</div></td>
        <td height="40" align="left" valign="middle">&nbsp;<strong>R$&nbsp;<?php print "$row[valor_medio]";?></strong></td>
        <td height="40" valign="middle"><div align="right" class="style31">Tipo do Pedido:</div></td>
        <td height="40" align="left" valign="middle"><strong>&nbsp;<?php print "$tipo";?></strong></td>
      </tr>
    </table>
    <strong><br />
    <a href="#"></a></strong>
    <table width="98%" border="0" caellspacing="0" cellpadding="0">
      <tr>
        <td align="center">

<?php
$verifica = $row_user_logado['0'];

if($verifica == "3" or $verifica == "27" or $verifica == "1" or $verifica == "9"){		

print "
<form action='pedidocompra.php' method='post' name='form1' id='form1'>
<label>
<input type='radio' id='autorizado' name='autorizado' value='2'>
Sim Autorizo
</label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label>
<input type='radio' id='autorizado' name='autorizado' value='0'>
Não Autorizo
</label><br><Br>
<input name='regiao' type='hidden' id='regiao' value='$regiao'/>
<input name='pedido' type='hidden' id='pedido' value='$row[0]'/>
<input name='pagina' type='hidden' id='pagina' value='2'/>
<input type='submit' name='button' id='button' value='Enviar' />
</form> ";
}

		?>
        </td>
      </tr>
    </table>
    <br />
      <a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0>
      <br /></td><td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="74" background="layout/esquerdo.gif">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="349" height="19" align="right" valign="top" class="style3">&nbsp;</td>
    <td width="354" align="center" valign="middle" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#5C7E59"><img src="layout/baixo.gif" width="750" height="38" />
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
}else{               //----------------FAZENDO O UPDATE DO REGISTRO----------------------//


$regiao = $_REQUEST['regiao'];
$pedido = $_REQUEST['pedido'];
$id_user = $_COOKIE['logado'];
$data = date('Y-m-d');
$registro = $_REQUEST['autorizado'];


mysql_query("UPDATE compra SET id_user_autoriza='$id_user', data_processo='$data', status_requisicao = '$registro', acompanhamento = '$registro' where id_compra = '$pedido'") or die ("<center>ERRO!<br> tente novamente mais tarde<br><br>".mysql_error());

print "
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações cadastradas com sucesso!<br><br>
</font>
<br><br>
<a href='gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";

}

}

?>