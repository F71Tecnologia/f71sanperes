<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

include "../conn.php";
include "../funcoes.php";

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_regi = mysql_fetch_array($result_regi);

$data = date('d/m/Y');

$result_sexo_f = mysql_query("SELECT * FROM rh_clt where sexo = 'F' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_f = mysql_num_rows($result_sexo_f);

$result_sexo_m = mysql_query("SELECT * FROM rh_clt where sexo = 'M' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_m = mysql_num_rows($result_sexo_m);


$result_cont_total_geral = mysql_query("SELECT id_clt FROM rh_clt where id_regiao = '$regiao'");
$row_cont_total_geral = mysql_num_rows($result_cont_total_geral);

$dia = date('d');
$mes = date('m');
$ano = date('Y');
$data_antiga = date("Y-m-d", mktime (0, 0, 0, $mes  , $dia - 90, $ano));
$data_atual = date("d/m/Y");

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">

<script type="text/JavaScript" src="js/jquery-1.3.2.js"></script>
<script type="text/JavaScript" src="js/lightbox.js"></script>
<link href="js/lightbox.css" rel="stylesheet" type="text/css">

<script language="JavaScript" type="text/JavaScript">

<!--

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();

}
MM_reloadPage(true);

function popup(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup2(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup3(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup4(caminho,nome,largura,altura) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -60;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
//-->
</script>
<style type="text/css">
<!--
.style34 {
font-size: 12px;
font-weight: bold;
color: #FFFFFF;
}
.style35 {
font-family: Geneva, Arial, Helvetica, sans-serif;
font-weight: bold;
}
.style36 {font-size: 14px}
.style38 {
font-size: 16px;
font-weight: bold;
font-family: Geneva, Arial, Helvetica, sans-serif;
color: #FFFFFF;
}
.style39 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #000000;
}
a:link {
color: #006600;
}
a:visited {
color: #006600;
}
a:hover {
color: #006600;
}
a:active {
color: #006600;
}
-->
</style>
<script language="javascript">
//o parâmentro form é o formulario em questão e t é um booleano 
function ticar(form, t) { 
campos = form.elements; 
for (x=0; x<campos.length; x++) 
if (campos[x].type == "checkbox") campos[x].checked = t; 
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
</script> 
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top"> 
<table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr> 
<td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
</tr>
<tr>
<td width="21" rowspan="5" background="../layout/esquerdo.gif">&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td width="26" rowspan="5" background="../layout/direito.gif">&nbsp;</td>
</tr>
<tr>
<td background="imagens/fundo_cima.gif"><div align="center">
<p align="left" class="style6"><span class="style34">&nbsp;<strong><br>
&nbsp;
<?=$row_user['nome1']?>
<BR>
&nbsp;&nbsp;<?=$data?><BR>
&nbsp;&nbsp;Regi&atilde;o: 
<?=$row_regi['regiao']?>
</strong></span><br>
<br>
</p>
</div></td>
<td background="imagens/fundo_cima.gif">&nbsp;&nbsp;<img src="imagens/adicionar_bolsista.gif" alt="gestao" width="40" height="40" align="absmiddle"> <span class="style38">GEST&Atilde;O DE RECURSOS HUMANOS</span></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><div align="center"></div></td>
</tr>
<tr>
<td colspan="2">
<?php
  		//BLOQUEIO PAULO MONTEIRO SJR 30-03 - 13hs
  		if($id_user != '73') {
		?>
<table width="90%"  height="300px" border="1" align="center" cellspacing="0" bordercolor="#999999">
<tr>
<td colspan="4" bgcolor="#003333"><div align="right" class="style35">
<div align="center" class="style27 style36">EMPRESA</div>
</div></td>
</tr>
<tr>
<td><div align="center"><a href="#"><img src="rh/imagensrh/dadosempresa.gif" alt="empresa" width="100" height="40" 
onClick="MM_openBrWindow('rh/rh_empresa.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,width=760,height=600')"></a></div></td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/feriados.gif" alt="feriados" width="100" height="40" onClick="MM_openBrWindow('rh/rh_feriados.php?id=1&regiao=<?=$regiao?>','','width=760,height=600')"></a></div></td>
<td><div align="center"><a href="rh/rh_impostos.php?id=1&regiao=<?=$regiao?>"><img src="rh/imagensrh/taxas.gif" alt="taxas" width="120" height="40"></a></div></td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/sindicatos.gif" alt="sindicatos" width="32" height="32" onClick="MM_openBrWindow('rh/rh_sindicatos.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,width=760,height=600')"></a></div></td>
</tr>
<tr>
<td>&nbsp;</td>
<td align="center"><a href="rh/rh_vale.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/vale.gif" width="120" height="40" alt="vale"></a></td>
<td align="center"><a href="rh/rh_horarios.php?regiao=<?=$regiao?>" target="_blank"><img src="rh/imagensrh/horarios.gif" width="120" height="40" alt="horarios"></a></td>
<td>&nbsp;</td>
</tr>
<tr> 
<td colspan="4" bgcolor="#003333"><div align="center"><span class="style27 style36 style35">FUNCION&Aacute;RIOS</span></div></td>
</tr>
<tr>
<td><div align="center"><a href="rh/clt.php?regiao=<?=$regiao?>"><img src="rh/imagensrh/edicao.gif" alt="editar" width="120" height="40"></a></div></td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/eventos.gif" alt="eventos" width="100" height="40"></a></div></td>
<td><div align="center"><a href="rh/rh_movimentos.php?regiao=<?=$regiao?>&tela=1"><img src="rh/imagensrh/movimentos.gif" alt="movimentos" width="120" height="40"></a></div></td>
<td align="center" valign="middle"><a href="#"><img src="rh/imagensrh/contracheques.gif" alt="contras" width="100" height="40"></a></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/ferias.gif" alt="ferias" width="100" height="40"></a></div></td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/rescisao.gif" alt="rescis&atilde;o" width="120" height="40"></a></div></td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="4" bgcolor="#003333"><div align="center"><span class="style27 style36 style35">FOLHA, RELAT&Oacute;RIOS e IMPOSTOS</span></div></td>
</tr>
<tr>
<td><div align="center"><a href="#"><img src="rh/imagensrh/situacoes.gif" width="130" height="40" alt="folha"></a></div></td>
<td align="center"><a href="#"><img src="rh/imagensrh/ponto.gif" alt="ponto" width="100" height="40"></a></td>
<td><div align="center"><a href="#"><img src="rh/imagensrh/sefip.gif" alt="SEFIP" width="120" height="40"></a></div>  <div align="center"></div></td>
<td align="center" valign="middle"><a href="#"><img src="rh/imagensrh/gps.gif" width="100" height="40"></a></td>
</tr>

</table>
 <?php 
	  }  
		  ?>
<br>
<table width="90%" border="1" align="center" cellpadding="0" cellspacing="0">
<tr>
<td colspan="2" bgcolor="#666666"><div align="center"><span class="style27 style36 style35 style25">CONTROLE DE PARTICIPANTES NA REGI&Atilde;O AT&Eacute; A DATA ATUAL</span></div></td>
</tr>
<tr>
<td width="52%"><div align="right" class="style25"><span class="style39">Total de participantes</span>:</div></td>
<td width="48%"><div class='style25'><span class='style39'>&nbsp;&nbsp;<?=$row_cont_total_geral?></span></div></td>
</tr>
<tr>
<td colspan="2" bgcolor="#666666"><div align="center"><span class="style27 style36 style35 style25">CONTROLE DE FUNCION&Aacute;RIOS POR SITUA&Ccedil;&Atilde;O ATUAL</span></div></td>
</tr>
<?php
$cont = "0";

$result_rhstatus = mysql_query("SELECT * FROM rhstatus where status_reg = '1'");
while($row_rhstatus = mysql_fetch_array($result_rhstatus)){

$result_cont_status = mysql_query("SELECT id_clt FROM rh_clt where status = '$row_rhstatus[codigo]' and id_regiao = '$regiao'");
$row_cont_status = mysql_num_rows($result_cont_status);

if($cont % 2){ $cor_linha="#FFFFFF"; }else{ $cor_linha="#CCFFFF"; }


print "
<tr bgcolor=$cor_linha>
<td><div align='right' class='style25'><span class='style39'>($row_rhstatus[codigo]) $row_rhstatus[especifica]</span></div></td>
<td><div class='style25'><span class='style39'>&nbsp;&nbsp;$row_cont_status</span></div></td>
</tr>
";
$cont ++;
}

?>

<tr>
<td colspan="2" bgcolor="#666666"><div align="center"><span class="style27 style36 style35 style25">CONTROLE DE FUNCION&Aacute;RIOS ATIVOS POR SEXO</span></div></td>
</tr>
<tr>
<td><div align="right" class="style25"><span class="style39">Homens</span></div></td>
<td><div class='style25'><span class='style39'>&nbsp;&nbsp;<?=$row_cont_sexo_m?></span></div></td>
</tr>
<tr bgcolor="#CCFFFF">
  <td bgcolor="#CCCCCC"><div align="right" class="style25"><span class="style39">Mulheres</span></div></td>
  <td bgcolor="#CCCCCC"><div class='style25'><span class='style39'>&nbsp;&nbsp;<?=$row_cont_sexo_f?></span></div></td>
</tr>
<tr bgcolor="#CCFFFF">
  <td colspan="2" bgcolor="#666666" align="center"><span class="style27 style36 style35 style25">CONTROLE DE FUNCION&Aacute;RIOS EM EXPERI&Ecirc;NCIA</span></td>
  </tr>
<tr bgcolor="#CCFFFF">
  <td bgcolor="#FFFFFF"><div align="right" class="style25"><span class="style39">Funcionário em experiência</span></div></td>
  <td bgcolor="#FFFFFF"><div class='style25'><span class='style39'>&nbsp;
  <? 
  $result_data_entrada = mysql_query("SELECT id_clt FROM rh_clt WHERE data_entrada > '$data_antiga' AND id_regiao = '$regiao'");
  $row_datas = mysql_num_rows($result_data_entrada);
  print "$row_datas";
  ?></span></div></td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="155">&nbsp;</td>
<td width="549">&nbsp;</td>
</tr>
<tr valign="top"> 
<td height="37" colspan="4" bgcolor="#E2E2E2"> <img src="../layout/baixo.gif" width="750" height="38"> 
<?php
include "../empresa.php";
$rod = new empresa();
$rod -> rodape();
?></td>
</tr>
</table>
</td>
</tr>
</table>
<?php
}
/* Liberando o resultado */
//mysql_free_result($result);
/* Fechando a conexão */
//mysql_close($conn);
?>
</body>
</html>
