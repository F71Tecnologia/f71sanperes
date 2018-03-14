<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

setlocale (LC_ALL, 'ptbr-utf-8');

include "conn.php";
$regiao = $_REQUEST['regiao'];

$mes2 = date('F');

$mes = date('m');
switch ($mes) {
case 1:
$mes = "Janeiro";
break;
case 2:
$mes = "Fevereiro";
break;
case 3:
$mes = "Março";
break;
case 4:
$mes = "Abril";
break;
case 5:
$mes = "Maio";
break;
case 6:
$mes = "Junho";
break;
case 7:
$mes = "Julho";
break;
case 8:
$mes = "Agosto";
break;
case 9:
$mes = "Setembro";
break;
case 10:
$mes = "Outubro";
break;
case 11:
$mes = "Novembro";
break;
case 12:
$mes = "Dezembro";
break;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
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
.style7 {color: #003300}
.style9 {color: #FF0000}
.style10 {
	color: #FF0000;
	font-size: 12px;
	font-weight: bold;
}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style20 {
	font-size: 13px;
	font-weight: bold;
}
.style24 {font-size: 11px; color: #FFFFFF; font-weight: bold; }
.style25 {
	font-size: 11px;
	font-weight: bold;
}
.style26 {
	font-size: 14px;
	font-weight: bold;
}
.style27 {color: #FF0000; font-weight: bold; font-size: 14px; }
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="3" background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="left" valign="middle" bgcolor="#FFFFCC"><span class="style10"><img src="imagensfinanceiro/relatoriososcip.gif" alt="relata" width="25" height="25" align="absmiddle" /> </span><span class="style3">VISUALIZAR CONTROLE 
    DE GASTO DA OSCIP</span> </td>
    <td width="26" rowspan="3" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top"><div align="center">
      <p align="left"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="style2">Visualiza&ccedil;&atilde;o dos gastos do Ano de <span class="style9">&lt;SELECIONA O ANO&gt;</span>, <span class="style9">&lt;SELECIONA O PROJETO&gt;</span>:</span></strong></p>
      </div></td>
  </tr>
  <tr>
    <td height="170" colspan="2" align="center" valign="middle"><p align="center"><strong>
      <input type="submit" name="Submit" value="JANEIRO" id="Submit" />
       &lt; <span class="style10">disponivel</span> &gt; <br />
      <input type="submit" name="Submit2" value="FEVEREIRO" id="Submit2" />
      <br />
      <input type="submit" name="Submit3" value="MAR&Ccedil;O" id="Submit3" />
      <br />
      <input type="submit" name="Submit4" value="ABRIL" id="Submit4" />
      <br />
      <input type="submit" name="Submit5" value="MAIO" id="Submit5" />
      <br />
      <input type="submit" name="Submit6" value="JUNHO" id="Submit6" />
    </strong><br />
    <strong>
    <input type="submit" name="Submit7" value="JULHO" id="Submit7" />
    <br />
    <input type="submit" name="Submit22" value="AGOSTO" id="Submit22" />
    <br />
    <input type="submit" name="Submit32" value="SETEMBRO" id="Submit32" />
    <br />
    <input type="submit" name="Submit42" value="OUTUBRO" id="Submit42" />
    <br />
    <input type="submit" name="Submit52" value="NOVEMBRO" id="Submit52" />
    <br />
    <input type="submit" name="Submit62" value="DEZEMBRO" id="Submit62" />
    </strong></p>    </td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="28" colspan="2" align="right" valign="top" bgcolor="#FFFFCC"><div align="left"><img src="imagensfinanceiro/controledecotacoes.gif" alt="cotas" width="25" height="25" align="absmiddle" /> <span class="style3">CONTROLE DE COTA&Ccedil;&Otilde;ES </span></div></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="69" colspan="2" align="right" valign="top">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td height="25" colspan="2" align="right" valign="top" class="style3">&nbsp;</td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  
  <tr>
    <td background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" height="19" align="right" valign="top" class="style3">&nbsp;</td>
    <td width="349" align="center" valign="middle" class="style3"></td>
    <td background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
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