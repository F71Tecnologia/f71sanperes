<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{
include "conn.php";
include "includes.php";
include "classes/abreviacao.php";
$id_user = $_COOKIE['logado'];
$sql = "SELECT * FROM funcionario where id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
$grupo_usuario = $row_user['grupo_usuario'];
$regiao_usuario = $row_user['id_regiao'];
$apelido_usuario = $row_user['nome1'];
$perfil_usuario = $row_user['tipo_usuario'];
if($row_user['id_master'] == "1"){
	$css = "<link href='net1.css' rel='stylesheet' type='text/css'>";
}else{
	$css = "<link href='net2.css' rel='stylesheet' type='text/css'>";
}
$mes = date('m');
$ano = date('Y');
$mes_h = date('m');
$dia_h = date('d');
$dia_select = date("d", mktime (0, 0, 0, $mes_h  , $dia_h + 30 , $ano));
$mes_select = date("m", mktime (0, 0, 0, $mes_h  , $dia_h + 30 , $ano));
if($id_user == '5' or $id_user == '32'){
//-------------VERIVICANDO AS CONTAS PARA HOJE------------------
$result_jr = mysql_query("SELECT * FROM saida where id_regiao = '$regiao_usuario' and status = '1'
and data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento");
$result_banco_jr = mysql_query("SELECT * FROM bancos where id_regiao='$regiao_usuario' and saldo LIKE '-%'");
$linha_jr = mysql_num_rows($result_jr);
$linha_banco_jr = mysql_num_rows($result_banco_jr);
if($linha_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr CONTA(S) A PAGAR HOJE');</script>";
}else{
}
if($linha_banco_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_banco_jr SALDO(S) NEGATIVO(S)');</script>";
}
}
if($id_user == '3' or $id_user == '27'){
//-------------VERIVICANDO SE EXISTEM PEDIDOS DE COMPRAS------------------
$result_jr2 = mysql_query("SELECT * FROM compra where acompanhamento = '1' and status_reg = '1'");
$linha_jr2 = mysql_num_rows($result_jr2);
if($linha_jr2 > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr2 SOLICITAÇÕES DE COMPRA');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO OU COMBUSTIVEL ACEITO-------------------
$result_chamado = mysql_query("SELECT id_suporte FROM suporte where user_cad = '$id_user' and status = '2'");
$cont_chamado = mysql_num_rows($result_chamado);
if($cont_chamado > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $cont_chamado CHAMADOS RESPONDIDOS NO SUPORTE ON-LINE');</script>";
}
/*
$RECom1 = mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '2' and id_user = '$id_user'");
$ContCom1 = mysql_num_rows($RECom1);
if($ContCom1 > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom1 PEDIDOS DE COMBUSTIVEL LIBERADOS');</script>";
}
*/
if($id_user == '9' or $id_user == '1'){
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO-------------------
$result_chamado = mysql_query("SELECT id_suporte FROM suporte where status = '1' or status = '3'");
$cont_chamado = mysql_num_rows($result_chamado);
if($cont_chamado > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\n$cont_chamado CHAMADOS ABERTOS NO SUPORTE ON-LINE');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE REEMBOLSO-------------------
if($id_user == '32' or $id_user == '27'){
$REReem= mysql_query("SELECT id_reembolso FROM fr_reembolso WHERE status = '1'");
$ContReem = mysql_num_rows($REReem);
if($ContReem > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContReem PEDIDOS DE REEMBOLSO EM ABERTO');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE COMBUSTIVEL-------------------
if($id_user == '32' or $id_user == '27'){
$RECom= mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '1'");
$ContCom = mysql_num_rows($RECom);
if($ContCom > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom PEDIDOS DE COMBUSTIVEL EM ABERTO');</script>";
}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<script src="js/jquery-1.3.2.js" language="javascript"></script>
<script type="text/javascript" src="documentos_empresa/js/jquery.tools.min.js"></script>
<script type="text/javascript">
// script do cancelamento
$().ready(function(){
	$(".status").tooltip();
});
</script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<script type="text/javascript" src="js/ramon.js"></script>
<script type="text/javascript">
function confirmacao(url,mensagem){
	if(window.confirm(mensagem)){
		location.href=url;
	}
}
function envia(url){	
	window.open(url,"Enviardocumento","width=400,height=350")
}
</script>
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href='net1.css' rel='stylesheet' type='text/css'>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
<script type="text/javascript">
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<style type="text/css">
.tooltip {
	display:none;
	font-size:12px;
	color:#333;
	height:82px;
	width:169px;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	background-color: transparent;
	background-image: url(documentos_empresa/img/white_arrow.png);
	background-repeat: no-repeat;
	background-position: left top;
	padding: 20px;
}
*html .tooltip {
	height:122px;
	width:209px;
}
<!--
body {
	margin:0px;
	background-color: #E2E2E2;
	/*background-color:#E2E2E2;*/
	/*background:url(imagens/fundologin.gif)*/
}
.tab {
	background: #FFFFFF;
}
.over {
	background: #000000;
	/*#E2E2E2*/
}
.normal {
	background-color:#FFF;
}
.over {
	background-color:#E2E2E2;
}
-->
</style>
<script type="text/javascript">
$(function(){
	$('tr.normal')
		.mouseover(function(){
			$(this).addClass('over');
		})
		.mouseout(function(){
			$(this).removeClass('over');
	});
});
</script>
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
      <table width="750" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border-left:1px solid #888; border-right:1px solid #888; font-weight: bold;">
        <tr> 
          <td height="25" colspan="2" bgcolor="#aaaaaa">
          <marquee scrolldelay="100" scrollamount="5" hspace="0" truespeed="truespeed"> 
          <div align="center"><font color="#ffffff" face="Verdana, Arial, Helvetica, sans-serif" size="1"><strong>A  medida real de um homem não se vê na forma como se comporta no conforto, mas em como se mantém durante o desafio.</strong></font></div>
          </marquee></td>
        </tr>
        <tr> 
          <td height="69" width="165" align="right" valign="top"> <?=$menu?></td>
          <td width="549" align="center" valign="top" >
          <br />
          <?php
		  $usuarios_avancados = array('1', '5', '9', '68', '75', '77', '87');
		  if(in_array($id_user, $usuarios_avancados)) {
		  ?>
          <table width="100%" border="0" cellspacing="2" cellpadding="0">
            <tr>
              <td  align="center" valign="middle">
              <a href='ver_tudo.php?id=19' target="_blank">
              <img src='imagensmenu2/ver_user_master.gif' width="65" height="59" border='0'></a>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <a href='suporte/admsuporte.php' target="_blank">
              <img src='imagensmenu2/helpdesk.gif' alt='HELP DESK' width="50" height="50" border='0'></a>
              </td>
              </tr>
            </table> 
            <?
		  	}
		  	?>
<br />
<table width="100%" border="0" cellspacing="10" cellpadding="0">
<tr>
<td width="35%" class="showdois"><span style="font-size:23px; color:#036;">&gt;</span> Calendário</td>
<td width="45%" class="showdois"><span style="font-size:23px; color:#036;">&gt;</span> Aniversariantes do m&ecirc;s</td>
</tr>
  <tr>
    <td align="center" valign="top" class=""><?php $f=""; include "dfcalendar.php"; ?></td>
    <td align="left" valign="top" class="style3">
      <div id="niver"><table>
        <?php 
		  $niver = mysql_query ("SELECT *,date_format(data_nasci, '%d/%m') as data_nasci1 FROM funcionario where 
		  month(data_nasci) = '$mes' AND status_reg = '1' and data_nasci != '0000-00-00'
		  ORDER BY month(data_nasci),day(data_nasci)");
		  
		 while ($aniversarios = mysql_fetch_array($niver)) {
		 $nomeNiver = explode(" ",$aniversarios['nome']);
		 
		 print "<tr><td width='80%'><div style='color:#C44; font-size:10px'><b>$nomeNiver[0] $nomeNiver[1]</b></div></td><td width='20%'><div style='color:#C44; font-size:10px'><b>$aniversarios[data_nasci1]</b></div></td></tr>";  
		  
		  } 
		  
		  ?>
        </table>
      </div></td>
    </tr>
</table>
<!-- ############################### feriados JR - 05/03/2010 as 17hs ################################ -->
<table width="100%" border="0" cellspacing="10" cellpadding="0">
<tr>
<td width="45%" class="showdois"><span style="font-size:23px; color:#036;">&gt;</span> Feriados do m&ecirc;s</td>
</tr>
  <tr>
    <td align="left" valign="top" class="style3">
      <div id="niver"><table  width="100%">
        <?php 
		  $feriado = mysql_query ("SELECT *,date_format(data, '%d/%m') as data FROM rhferiados where 
		  month(data) = '$mes_h' AND status = '1' ORDER BY month(data),day(data)");
		  
		 while ($feriados = mysql_fetch_array($feriado)) {
		 $nomeferiado = $feriados['nome'];
		 
		 print "<tr>
		 <td width='80%'>
		 <div style='color:#C44; font-size:10px'>
		 <b>$nomeferiado</b></div></td>
		 <td width='20%'>
		 <div style='color:#C44; font-size:10px'>
		 <b>$feriados[data]</b></div></td></tr>";  
		  
		  } 
		  
		  ?>
        </table>
      </div></td>
    </tr>
</table>
<!-- ######################## AQUI COMEÇA A PRINTAR AS TABELAS DAS TAREFAS ############################### -->
<?php
#NOVAS TABELAS DE TAREFAS
$REGrup = mysql_query("SELECT *,date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM tarefa where status_reg = '1' and grupo = '$grupo_usuario'") 
or die(mysql_error());
$NumGrup = mysql_num_rows($REGrup);
$RERece = mysql_query("SELECT *, date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM tarefa where usuario = '$apelido_usuario' and tipo_tarefa < '5' and status_reg = '1' ORDER BY id_tarefa DESC") 
or die(mysql_error());
$NumRece = mysql_num_rows($RERece);
$REConc = mysql_query("SELECT *, date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM tarefa where usuario = '$apelido_usuario' and tipo_tarefa = '5' and status_reg = '1' ORDER BY id_tarefa DESC") 
or die(mysql_error());
$NumConc = mysql_num_rows($REConc);
$RECopi = mysql_query("SELECT *, date_format(data_entrega, '%d/%m/%Y')as data_entrega FROM tarefa where criador = '$apelido_usuario' and tipo_tarefa < '5' and status_reg = '1' and copia = '1' ORDER BY id_tarefa DESC") 
or die(mysql_error());
$NumCopi = mysql_num_rows($RECopi);
if($NumGrup == 0){
	$tabGrup = "style='display:none'";
}
if($NumRece == 0){
	$tabRece = "style='display:none'";
}
if($NumConc == 0){
	$tabConc = "style='display:none'";
}
if($NumCopi == 0){
	$tabCopi = "style='display:none'";
}
if($NumGrup == 0 and $NumRece == 0 and $NumConc == 0 and $NumCopi == 0){
	$button = "style='display:none'";
}
$bord = "style='border-bottom:#000 solid 1px;'";
?>
<form action="ver_tarefa.php" method="post" name="form1" onSubmit="return validaForm()">
<input type="hidden" name="id" value="3">
<table width="98%" border="0" cellspacing="0" cellpadding="0" <?=$tabGrup?> >
  <tr>
    <td class="showdois" colspan="6"><span style="font-size:23px; color:#036;">&gt;</span> Grupo</td>    </tr>
  <tr>
    <td width="5%" <?=$bord?> align="center">&nbsp;</td>
    <td colspan="2" <?=$bord?> align="center"><div style='font-size:13px; color:#000'><b>Data entrega</b></div></td>
    <td width="38%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Assunto</b></div></td>
    <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>De</b></div></td>
    <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Regiao</b></div></td>
  </tr>
<?php
while($Row = mysql_fetch_array($REGrup)){
	
	if($Row['status_tarefa'] == "1"){
		$statusTarefa = "Pendente";
		$corLink = "#F00";
	}else{
		$statusTarefa = "Entregue";
		$corLink = "#00F";
	}
	
	$RERE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$Row[id_regiao]'");
	$RowRE = mysql_fetch_array($RERE);
	
	$bord1 = "style='border-bottom:#dddddd solid 1px;'";
?>
  <tr height="30" class="normal">
    <td align="center" <?=$bord1?>><input name='tarefa[]' type='checkbox' id="tarefa" value='<?=$Row['id_tarefa']?>'></td>
    <td width="8%" align="center" <?=$bord1?>><img src='imagens/read<?=$Row['status_tarefa']?>.gif' alt='<?=$statusTarefa?>' width="16" height="14"></td>
    <td width="17%"  <?=$bord1?>>
    <div>
    <a href='#' onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Grupo','750','450','yes');" 
    style='text-decoration:none; font-size:12px; color:<?=$corLink?>'>
    <?=$Row['data_entrega']?></a></div></td>
    <td  <?=$bord1?>><div style='font-size:13px; color:#000'><?=$Row['tarefa']?></div></td>
    <td  <?=$bord1?>><div style='font-size:13px; color:#000'>
      <?=$Row['criador']?>
    </div></td>
    <td  <?=$bord1?>><div style='font-size:13px; color:#000'>
      <?=$RowRE['regiao']?>
    </div></td>
  </tr>
<?php
}
?>
  </table>
<br>
<table width="98%" border="0" cellspacing="0" cellpadding="0" <?=$tabRece?> >
  <tr>
    <td class="showdois" colspan="6"><span style="font-size:23px; color:#036;">&gt;</span> Tarefas Recebidas</td>
  </tr>
  <tr><td colspan="6">&nbsp;</td></tr>
  <tr>
    <td width="2%" align="center">&nbsp;</td>
    <td width="2%" align="center">&nbsp;</td>
    <td width="30%"><div style='font-size:13px; color:#000'><b>De</b></div></td>
    <td width="35%"><div style='font-size:13px; color:#000'><b>Assunto</b></div></td>
    <td width="1%"><div style='font-size:13px; color:#000'>&nbsp;</div></td>
    <td width="30%"><div style='font-size:13px; color:#000'><b>Data de entrega</b></div></td>
  </tr>
  <?php
while($Row = mysql_fetch_array($RERece)){
	
	if($Row['status_tarefa'] == "1"){
		$statusTarefa = "Pendente";
		$corLink = "#F00";
	}else{
		$statusTarefa = "Entregue";
		$corLink = "#00F";
	}
	
	$RERE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$Row[id_regiao]'");
	$RowRE = mysql_fetch_array($RERE);
	
	$bord1 = "style='border-bottom:#333 dotted 1px;'";
?>
  <tr onMouseover="this.style.backgroundColor='dee3ed';this.style.cursor='pointer';" onMouseout="this.style.backgroundColor='white';" height="30">
    <td align="center" onMouseover="this.style.cursor='default';" <?=$bord1?>><input name='tarefa[]' type='checkbox' id="tarefa" value='<?=$Row['id_tarefa']?>'></td>
    <td width="8%" align="center" onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Recebida','750','450','yes');" <?=$bord1?>><img src='imagens/read<?=$Row['status_tarefa']?>.gif' alt='<?=$statusTarefa?>' width="16" height="14"></td>
    <td width="17%" onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Recebida','750','450','yes');" <?=$bord1?>><div> <span style="font-size:13px; color:#000">
      <?=$Row['criador']?>
    </span>(<span style="font-size:13px; color:#000">
    <?=$RowRE['regiao']?>
    </span>)</div></td>
    <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Recebida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'><?=$Row['tarefa']?></div></td>
    <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Recebida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>&nbsp;</div></td>
    <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Recebida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'><?=$Row['data_entrega']?></a></div></td>
  </tr>
  <?php
}
?>
</table>
<br>
  <table width="98%" border="0" cellspacing="0" cellpadding="0" <?=$tabConc?>>
    <tr>
      <td class="showdois" colspan="6"><span style="font-size:23px; color:#036;">&gt;</span> Tarefas Enviadas Conclu&iacute;das</td>
    </tr>
    <tr>
      <td width="5%" <?=$bord?> align="center">&nbsp;</td>
      <td colspan="2" <?=$bord?> align="center"><div style='font-size:13px; color:#000'><b>Data entrega</b></div></td>
      <td width="38%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Assunto</b></div></td>
      <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>De</b></div></td>
      <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Regiao</b></div></td>
    </tr>
    <?php
while($Row = mysql_fetch_array($REConc)){
	
	if($Row['status_tarefa'] == "1"){
		$statusTarefa = "Pendente";
		$corLink = "#F00";
	}else{
		$statusTarefa = "Entregue";
		$corLink = "#00F";
	}
	
	$RERE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$Row[id_regiao]'");
	$RowRE = mysql_fetch_array($RERE);
	
	$bord1 = "style='border-bottom:#dddddd solid 1px;'";
?>
    <tr onMouseover="this.style.backgroundColor='dee3ed';this.style.cursor='pointer';" onMouseout="this.style.backgroundColor='white';" height="30">
      <td align="center" <?=$bord1?>><input name='tarefa[]' type='checkbox' id="tarefa" value='<?=$Row['id_tarefa']?>'></td>
      <td width="8%" align="center" <?=$bord1?>><img src='imagens/read<?=$Row['status_tarefa']?>.gif' alt='<?=$statusTarefa?>' width="16" height="14"></td>
      <td width="17%" onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Concluida','750','450','yes');" <?=$bord1?>><div> 
        <?=$Row['data_entrega']?>
      </a></div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Concluida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$Row['tarefa']?></div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Concluida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$Row['usuario']?>
      </div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','Tarefa_Concluida','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$RowRE['regiao']?>
      </div></td>
    </tr>
    <?php
}
?>
  </table>
<br>
  <table width="98%" border="0" cellspacing="0" cellpadding="0" <?=$tabCopi?>>
    <tr>
      <td class="showdois" colspan="6"><span style="font-size:23px; color:#036;">&gt;</span> C&oacute;pias de Tarefas</td>
    </tr>
    <tr>
      <td width="5%" <?=$bord?> align="center">&nbsp;</td>
      <td colspan="2" <?=$bord?> align="center"><div style='font-size:13px; color:#000'><b>Data entrega</b></div></td>
      <td width="38%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Assunto</b></div></td>
      <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Para</b></div></td>
      <td width="16%" <?=$bord?>><div style='font-size:13px; color:#000'><b>Regiao</b></div></td>
    </tr>
    <?php
while($Row = mysql_fetch_array($RECopi)){
	
	if($Row['status_tarefa'] == "1"){
		$statusTarefa = "Pendente";
		$corLink = "#F00";
	}else{
		$statusTarefa = "Entregue";
		$corLink = "#00F";
	}
	
	$RERE = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$Row[id_regiao]'");
	$RowRE = mysql_fetch_array($RERE);
	
	$bord1 = "style='border-bottom:#dddddd solid 1px;'";
?>
    <tr onMouseover="this.style.backgroundColor='dee3ed';this.style.cursor='pointer';" onMouseout="this.style.backgroundColor='white';" height="30">
      <td align="center" <?=$bord1?>><input name='tarefa[]' type='checkbox' id="tarefa" value='<?=$Row['id_tarefa']?>'></td>
      <td width="8%" align="center" <?=$bord1?>><img src='imagens/read<?=$Row['status_tarefa']?>.gif' alt='<?=$statusTarefa?>' width="16" height="14"></td>
      <td width="17%" onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','CopiasT','750','450','yes');" <?=$bord1?>><div> 
        <?=$Row['data_entrega']?>
      </a></div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','CopiasT','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$Row['tarefa']?></div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','CopiasT','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$Row['usuario']?>
      </div></td>
      <td onClick="javascript:abrir('ver_tarefa.php?id=1&tarefa=<?=$Row['id_tarefa']?>','CopiasT','750','450','yes');" <?=$bord1?>><div style='font-size:13px; color:#000'>
        <?=$RowRE['regiao']?>
      </div></td>
    </tr>
    <?php
}
?>
  </table>
  <br />
  <input type="submit" value="Deletar Selecionadas" <?=$button?> class="botao">
</form>
<?php
$query_documentos = mysql_query("SELECT * FROM documentos AS doc, doc_responsaveis AS res
			   	   				 WHERE doc.id_documento = res.id_documento
				   				 AND doc.status_documento = '1' AND res.id_funcionario = '$id_user' AND res.status = '1'");
$num_documentos = mysql_num_rows($query_documentos);
$mes_envio = array();
$documentos = array();
$alerta_atrazo_user = array();
while($row_documentos = mysql_fetch_assoc($query_documentos)) {
	
		switch($row_documentos['frequencia_documento']) {
			case 1:
					//sprintf('%02d',$row_documentos['mes_referencia_documento'] + );
					$cont = 1;
					do{
						
						$mes_envio[] = date('m', mktime('0','0','0',$cont,1,date('Y')));
						$cont++;
					}while($cont < 13);
				break;
			case 2:
				// caso for trimestral soma o mes de referencia + 3 e atribui o mes de envio				
				$cont = 1;
				do{
					$cont = $cont + 3;
					$mes_envio[] = date('m', mktime('0','0','0',$row_documentos['mes_referencia_documento'] + $cont - 1,1,date('Y')));
					
				}while($cont < 13);
				break;
			case 3: 
				$cont = 1;
				do{
					$cont = $cont + 6;
					$mes_envio[] = date('m', mktime('0','0','0',$row_documentos['mes_referencia_documento'] + $cont - 1,1,date('Y')));
				}while($cont < 13);
				break;
			case 4: 
				$cont = 1;
				do{
					$cont = $cont + 12;
					$mes_envio[] = date('m', mktime('0','0','0',$row_documentos['mes_referencia_documento'] + $cont - 1,1,date('Y')));
				}while($cont < 13);
				break;
		  }
		  if(in_array(date("m"),$mes_envio)){
				$id_documento[] = $row_documentos['id_documento'];
				$documentos[] = $row_documentos['nome_documento'];
				$dia[] = $row_documentos['dia_documento']."/".date("m")."/".date("Y");
				
				// Corrigir Erro
				$regioes_encarregadas = array();
				$regioes_enviadas = array();
				$regioes_canceladas = array();
				
				// Consulta dos Responsáveis do Documento
				$query_responsavel = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$row_documentos[id_documento]' AND id_funcionario = '$id_user'");
				$row_responsavel = mysql_fetch_assoc($query_responsavel);
				$num_rows_responsavel = mysql_num_rows($query_responsavel);
				// Criei uma Array com as Ids Regioes Encarregadas
				$regioes_encarregadas = explode(',', $row_responsavel['ids_regioes']);
				// Consulta dos Documentos Enviados								  
				$query_file = mysql_query("SELECT * FROM doc_files WHERE id_documento = '$row_documentos[id_documento]' AND mes_file = '".date('m')."'");
				while($row_file = mysql_fetch_assoc($query_file)) {
					if($row_file['recebimento_file'] == '2'){
						// cria um array com as regioes canceladas
						$regioes_canceladas[] = $row_file['id_regiao'];
					}else{
						// Criei uma Array com as Ids Regioes Enviadas
						$regioes_enviadas[] = $row_file['id_regiao'];
					}
				}
				
				// Criei uma Array com as Regiões que não foram enviadas
				$intercecao = array_diff($regioes_encarregadas,$regioes_enviadas);
				$intercecao = array_diff($intercecao,$regioes_canceladas);
				if(!empty($intercecao)){
					$regioes[] = $intercecao;
				}
				
				$todas_as_regioes[] = $regioes_encarregadas;
				$todas_as_regioes_enviadas[] = $regioes_enviadas;
				$todas_as_regioes_nao_enviadas[] = $intercecao;
				$todas_as_regioes_canceladas[] = $regioes_canceladas;
				/*
				echo "Interceção: "; print_r($intercecao); echo "<br />";
				
				echo "Regioes enviadas: ";print_r($regioes_enviadas);echo "<br />";	
				echo "Regioes não enviadas: ";print_r($regioes);echo "<br />";
				echo "TODAS AS REGIOES: "; print_r($regioes_encarregadas); echo "<br />";
				*/
				// Resetei as Duas Arrays
				unset($regioes_encarregadas,$regioes_enviadas,$regioes_canceladas);
				
				
		  }
unset($mes_envio);
}
/*
echo "todas as regioes: ";print_r($todas_as_regioes);echo "<br />";
echo "Enviadas: ";print_r($todas_as_regioes_enviadas);echo "<br />";
echo "Não enviadas: ";print_r($todas_as_regioes_nao_enviadas);echo "<br />";
echo "Canceladas: ";print_r($todas_as_regioes_canceladas);echo "<br />";
*/
if(!empty($num_documentos)){
	if(!empty($intercecao)){
?>
<table width="98%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="showdois" colspan="6"><span style="font-size:23px; color:#036;">&gt;</span> Documentos a ser enviados</td>
    </tr>
    <tr>
      <td colspan="2"><div style='font-size:13px; color:#000'><b>Documento</b></div></td>
      <td width="38%" ><div style='font-size:13px; color:#000'><b>Dia</b></div></td>
      <td width="16%" ><div style='font-size:13px; color:#000'><b>Regiao</b></div></td>
      <td width="16%" ><div style='font-size:13px; color:#000'></div></td>
    </tr>
    <?php
	foreach($documentos as $key => $doc) {
		if(!empty($regioes[$key])) { ?>
    <tr height="30" bgcolor="<? if($alternateColor++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>">
      <td width="8%" align="center"><img src="imagensmenu2/Documentos.png" /></td>
      <td width="30%"><a href="documentos_empresa/dados_documento.php?id_doc=<?=$id_documento[$key]?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><?=$id_documento[$key]." - ".$doc?></a></td>
      <td><?php 
	  echo $dia[$key];
	  ?></td>
      <td width="50%">
	  <table width="100%">
	  <?php
			foreach($todas_as_regioes[$key] as $chave_regoes => $id_regiao){
				$query_regioes = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
				$row_regiao = mysql_fetch_assoc($query_regioes);
				
				if(in_array($row_regiao['id_regiao'],$todas_as_regioes_nao_enviadas[$key])){
					  
					  // alerta de envio de arquivo não enviado
					  $data_envio = implode("",explode("/",$dia[$key]));
					  $data_hoje = implode("",explode("/",date("d/m/Y")));
					 
						  
						  $falta_user =  $dia[$key] - date("d");
						if($falta_user <= 2 and $falta_user > 0){
							$alerta_atrazo_user[] = "<script>alert('Faltam $falta_user dias para ser enviado o documento $row_doc[nome_documento]')</script>";
						}elseif($falta_user < 0){
							$alerta_atrazo_user[] = "<script>alert('O documento $doc esta atrasado ".($falta_user * -1)." Dias')</script>";
						}elseif($falta_user == 0){
							$alerta_atrazo_user[] = "<script>alert('Hoje vence o documento $doc');</script>";
						}
		 
					
					echo "<tr><td><a class='status' title='Clique aqui para enviar este documento.' href=\"documentos_empresa/envio.php?id_doc=$id_documento[$key]&id_fun=$id_user&id_regiao=$id_regiao&mes=".date("m")."\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe', height: '200' } )\">$row_regiao[id_regiao] - $row_regiao[regiao]</a></td>";
					echo "
						<td></td>
						</tr>";
						
				}elseif(in_array($row_regiao['id_regiao'],$todas_as_regioes_canceladas[$key])){
					print "
							<tr>
								<td colspan=\"2\" bgcolor=\"#FF8888\">Cancelado - $row_regiao[id_regiao] - $row_regiao[regiao]</td>
							</tr>
						";	
				
				}elseif(in_array($row_regiao['id_regiao'],$todas_as_regioes_enviadas[$key])){
					$query_id_files = mysql_query("SELECT id_file,recebimento_file FROM doc_files WHERE id_documento = '$id_documento[$key]' AND id_funcionario = '$id_user' AND id_regiao = '$row_regiao[id_regiao]'");
					$row_id_file = mysql_fetch_assoc($query_id_files);
					
						print "<tr>
									<td>$id_regiao - $row_regiao[regiao]</td>
									<td><a href=\"documentos_empresa/protoenvio.php?id_file=$row_id_file[id_file]\" target=\"_blank\" title=\"Exibir protocolo de recebimento\"><img src=\"documentos_empresa/img/DOC.png\"/></a></td>
							<tr>
						";
				}
				
								
				
			}// fim do for each
	  ?>
      </table>
     
     </td>
    </tr>
    <?php 
		}
		
	}// FIM DO FOREACH
	if(!empty($alerta_atrazo_user)){
		// remove alert repetidos
		$alerta_atrazo_user = array_flip($alerta_atrazo_user);
		$alerta_atrazo_user = array_flip($alerta_atrazo_user);
		foreach($alerta_atrazo_user as $alerta){
			echo $alerta;
		}
		
	}
	?>
  </table>  
<?php
	}//
		
	}?>
<?php 
$mes_selecionado = date("m");
if(isset($_REQUEST['mes'])){
	$mes_selecionado = $_REQUEST['mes'];
}
$ano_selecionado = date("Y");
if(isset($_GET['ano'])){
	$ano_selecionado = $_GET['ano'];
}
?>
<?php
//Filtro para usuarios
$acesso_documentos = array('75','5','27','77','33','36','78');
if(in_array($id_user,$acesso_documentos)) {
?>
<table width="100%" align="center" cellpadding="5" cellspacing="0" style="font-family:'Trebuchet MS', Arial, Helvetica, sans-serif;" >
    <tr>
        <td class="showdois">
        <span style="font-size:23px; color:#036;">&gt;</span>  Documentos a receber</td>
    </tr>
    <tr>
        <td >
          <form id="form2" name="form2" method="get" action="">
            <table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td width="101" align="right"> Mes </td>
                <td width="41" align="right"><label>
                  <select name="mes" id="mes">
                    <?php 
				  $query_meses = mysql_query("SELECT * FROM ano_meses ORDER BY num_mes ASC");
				  while($row_meses = mysql_fetch_assoc($query_meses)){
					if($mes_selecionado == $row_meses['num_mes']){
						echo "<option value='$row_meses[num_mes]' selected='selected'>$row_meses[nome_mes]</option>";
					}else{
				  		echo "<option value='$row_meses[num_mes]'>$row_meses[nome_mes]</option>";
					}
				  }
				  
				  ?>
                  
                    </select>
                </label></td>
                <td width="60" align="right">Ano</td>
                <td width="81"  ><select name="ano" id="ano">
                  <option value="2010">2010</option>
                  <option value="2011">2011</option>
                  <option value="2012">2012</option>
                </select></td>
                <td width="117"><input type="submit" name="button" id="button" value="Procurar" />
                </td>
                </tr>
            </table>
          </form></td>
    </tr>
    <tr>
        <td>  
<?php 
// buscando todas os documentos e responsaveis;
$sql_documentos = "SELECT * FROM documentos WHERE status_documento = 1 AND data_documento BETWEEN '2010-01-01' AND '$ano_selecionado-$mes_selecionado-31' ORDER BY nome_documento ASC";
$qr_doc = mysql_query($sql_documentos);
$num_doc = mysql_num_rows($qr_doc);
$mes_recebimento = array();
$alert_hoje = array();
$alert_proximo = array();
$alerta_atrazo = array();
while($row_doc = mysql_fetch_assoc($qr_doc)) {
	switch($row_doc['frequencia_documento']) {
		case 1: 
				
				$cont = 1;
				do{
					$mes_recebimento[] = date('m', mktime('0','0','0',$cont,1,date('Y')));
					$cont++;				
				}while($cont < 13);
				break;
		case 2: 
				// caso for trimestral soma o mes de referencia + 3 e atribui o mes de envio				
				$cont = 1;
				do{
					$cont = $cont + 3;
					$mes_recebimento[] = date('m', mktime('0','0','0',$row_doc['mes_referencia_documento'] + $cont - 1,1,date('Y')));	
					$cont++;
				}while($cont < 13);
				break;
		case 3:
				// caso for semestral soma o mes de referencia + 6 e atribui o mes de envio				
				$cont = 1;
				do{
					$cont = $cont + 6;
					$mes_recebimento[] = date('m', mktime('0','0','0',$row_doc['mes_referencia_documento'] + $cont - 1,1,date('Y')));
					$cont++;
				}while($cont < 13);
				break;
		case 4:
				// caso for anual soma o mes de referencia + 12 e atribui o mes de envio
				$cont = 1;
				do{
					$cont = $cont + 12;		
					$mes_recebimento[] = date('m', mktime('0','0','0',$row_doc['mes_referencia_documento'] + $cont - 1,1,date('Y')));
					$cont++;
				}while($cont < 13);
				break;
	}
	
	
	// verifica se esta no mes para receber
	/*if($id_user == '75'){
		print "<pre>";
		print_r($mes_recebimento);
	}*/
	if(in_array($mes_selecionado,$mes_recebimento)) {
		    $regioes = array();
			$qr_res = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$row_doc[id_documento]' AND status = '1'");
			while($row_res = mysql_fetch_assoc($qr_res)) {
					$ids_reg = explode(',', $row_res['ids_regioes']);
					
					foreach($ids_reg as $valor) {
							if(!in_array($valor, $regioes)) {
									
									$regioes[] = $valor;
									
							}
					}
					
					unset($ids_reg, $valor); 
					
			} ?>
            
                   <table width="100%" align="center" cellpadding="2" cellspacing="2" style="margin-bottom:20px; width:95%; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; font-size:12px; line-height:22px; border-bottom:1px solid #999;">
                        <tr style="border-bottom:1px solid #999;" bgcolor="#F1F1F1">
 <td colspan="8" align="left" ><spam class="showdois">&nbsp;<a href="documentos_empresa/dados_documento.php?id_doc=<?=$row_doc['id_documento']?>" onclick="return hs.htmlExpand(this, { objectType: 'iframe'} )"><?=$row_doc['id_documento']." - ".$row_doc['nome_documento']." ".$row_doc['dia_documento']."/".$mes_selecionado."/".date("Y")?></a></spam></td>
                        </tr>
                        <tr class='textopq' style="border-bottom:1px solid #999;">
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Região</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Responsáveis</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Status</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Baixar</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Protocolo</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Cancelar</b></td>
                            <td style="border-bottom:1px solid #999;border-top:1px solid #999;"><b>Reenvio</b></td>
                        </tr>
                        <?php foreach($regioes as $chave => $valor) { ?>
                        <tr bgcolor="<? if($alternateColor++%2==0) { ?>#F9F9F9<? } else { ?>#FFFFFF<? } ?>" class="atrasado">
                            <td class='textopq'>
								<?php
								// Nome das regioes
                                $qr_nome_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$valor'");
                                $row_nome_regiao = mysql_fetch_assoc($qr_nome_regiao);
                                echo $row_nome_regiao['id_regiao']." - ".$row_nome_regiao['regiao'];
                                ?>
                            </td>
                            <td class='textopq'><?php
								// Nome dos responsaveis 
								 $qr_responsaveis = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$row_doc[id_documento]' AND status = '1'");
                                       while($row_responsaveis = mysql_fetch_assoc($qr_responsaveis)) {
                                            
                                            $ids_reg = explode(',', $row_responsaveis['ids_regioes']);
                                        
                                            if(in_array($valor, $ids_reg)) {
                                                    $qr_nome = mysql_query("SELECT id_funcionario,nome FROM funcionario WHERE id_funcionario = '$row_responsaveis[id_funcionario]'");
                                                    $row_nome_fun = mysql_fetch_assoc($qr_nome);
                                                    $nome = explode(' ', $row_nome_fun['nome']);
                                                    $nome = $nome[0].' '.$nome[1];
                                                    $responsaveis[] = $row_nome_fun['id_funcionario']." - ".$nome;
													$ids_responsaveis[] = $row_nome_fun['id_funcionario'];
                                                    
                                                    
                                            }
                                        
                                        }
                                        
                                        echo implode('<br>', $responsaveis);                                        
                                        unset($responsaveis);
                                  ?>
                            </td>
                          <td align="center">
								<?php 
                                // Imagem de status
                                $qr_files 	= mysql_query("SELECT * FROM doc_files WHERE id_documento = '$row_doc[id_documento]' AND mes_file = '$mes_selecionado' AND id_regiao = '$valor'");
                                $num_files 	= mysql_num_rows($qr_files);
								$envios 	= array();
								if($num_files > 1){
									while($files = mysql_fetch_assoc($qr_files)){
										$envios[] = $files['id_file'];
									}
									
								}else{
									$files = mysql_fetch_assoc($qr_files);
								}
								
							//////////////////////
								if(empty($num_files)){
									// Atribuindo os valores a serem alertados	
                                    // calculo de falta							
                                    $falta = (int)((
                                     mktime(0,0,0, $mes_selecionado, $row_doc['dia_documento'], $ano_selecionado) 
                                     - 
                                     mktime(0,0,0, $mes_selecionado, date("d"), $ano_selecionado)
                                     ) / 86400);
                                    if($falta <= 2 and $falta > 0){
                                        $alert_proximo[] = $row_doc['id_documento'] ." - " .$row_doc['nome_documento'];
                                        
                                    }elseif($falta < 0){
                                        $alerta_atrazo[] = $row_doc['id_documento'] ." - " .$row_doc['nome_documento']." ".abs($falta)." Dias de atrazo ";
										$dias_de_atrazo = abs($falta);
                                    }
                                    if($falta == 0){
                                        $alert_hoje[] = $row_doc['id_documento'] ." - " .$row_doc['nome_documento'];
                                    }
                                    
									
									$data_vencimento = $ano_selecionado."-".$mes_selecionado."-".sprintf('%02d',$row_doc['dia_documento']);
                                    $data_envio = date("Y-m-d");
                                    
                                    if($data_envio >= $data_vencimento){
                                        $img_status =  "<img src=\"documentos_empresa/img/finalizado.png\"  class='status' title='Documento trazado $dias_de_atrazo dias.' alt='Atrazado'/>";
                                    }else{
                                        $img_status = "<img src=\"documentos_empresa/img/aberto.png\" alt='Aberto' class='status' title='Documento em aberto'/>";
                                    }
																	
								} elseif($num_files > 1) {
									
									$qr_file_doc_multiplo = mysql_query("SELECT * FROM doc_files WHERE id_file IN('".implode("', '",$envios)."') AND recebimento_file = '1';");
									$num_file_doc_multiplo = mysql_num_rows($qr_file_doc_multiplo);
									$num_documentos_enviados = count($envios);
									$num_documentos_recebidos = $num_file_doc_multiplo;
									$intercecao_documentos  = $num_documentos_enviados - $num_documentos_recebidos;
									if($intercecao_documentos <> 0){
										$img_status = "<img src=\"documentos_empresa/img/replicado.png\" alt='Enviado' class='status' title='Documento enviado'/>";
									}else{
										$img_status =  "<img src=\"documentos_empresa/img/respondido.png\" class='status' title='Documento concluido'/>"; 
									}
                                   												
								}elseif(empty($files['recebimento_file'])) {
								   	$qr_enviado = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$files[id_funcionario]'");
                                   	$img_status =  "<img src=\"documentos_empresa/img/replicado.png\" />";
                               } elseif($files['recebimento_file'] == 1){
								   
								  $qr_enviado = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$files[id_recebimento_file]'");
                                  $img_status =  "<img src=\"documentos_empresa/img/respondido.png\" class='status'
								  					title='Documento recebido em ".implode("/",array_reverse(explode("-",$files['data_recebimento_file'])))." 
													por ".mysql_result($qr_enviado,0)."'/>";
                               }elseif($files['recebimento_file'] == 2){
								   $qr_enviado = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$files[id_recebimento_file]'");
                                   $img_status = "<img src=\"documentos_empresa/img/cancelado.png\" class=\"status\" title=\"Documento Cancelado\" />";
                               }
                               
                               
                             echo $img_status;
							////////////////////////
                               ?>
                          </td>
<?php
	 				/////////////////////////////////////////////////////////////////
                   if(!empty($num_files)){
					   
					   if($num_files > 1){
						   
							
							if($intercecao_documentos <> 0){
								print "<td class='textopq'><a href=\"documentos_empresa/multiplosDoc.php?documento=$row_doc[id_documento]&mes=$mes&regiao=$valor\" target=\"_blank\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe'} )\"> Baixar </a></td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>";
									  if($id_user != 33 and $id_user != 36):
									  		print "<a href=\"documentos_empresa/actions/reenvio.php?multi=".implode(",",$envios)."\" class='status' title='Solicitar reenvio'>
										  			<img src=\"documentos_empresa/img/Arrow-cycle-18.png\" />
										  			</a>";
									  endif;
									  print "</td>";
							}else{
								print "<td class='textopq'>
										<a href=\"documentos_empresa/multiplosDoc.php?documento=$row_doc[id_documento]&mes=$mes&regiao=$valor\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe'} )\">
										 Recebido
										</a>
									  </td>
									  <td align=\"center\" class='textopq'><a href=\"documentos_empresa/protoenvio.php?id_file=$envios[0]&recebimento=true&multi=".implode(",",$envios)."\" target=\"_blank\" title=\"Emitir relatorio\">
										<img src=\"documentos_empresa/img/DOC.png\"/>
									  </a></td>
									  <td>&nbsp;</td>
									  <td>";
									  if($id_user != 33 and $id_user != 36):
									  	print "<a href=\"documentos_empresa/actions/reenvio.php?multi=".implode(",",$envios)."\" class='status' title='Solicitar reenvio'>
										  <img src=\"documentos_empresa/img/Arrow-cycle-18.png\" />
										</a>";
									  endif;
									  print "</td>";
							}
							
					   }else{
						   switch($files['recebimento_file']){
							   case 0:
									print "<td class='textopq'><a href=\"documentos_empresa/actions/recebimento.documentos.php?id_file=$files[id_file]\" target=\"_blank\"> Baixar </a></td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>";
									  if($id_user != 33 and $id_user != 36):
										  print "<a href=\"documentos_empresa/actions/reenvio.php?id_file=$files[id_file]\" class='status' title='Solicitar reenvio'>
													<img src=\"documentos_empresa/img/Arrow-cycle-18.png\" />
												</a>";
									  endif;
									  print "</td>";
									  break;
								case 1:
									print "<td class='textopq'>
										<a href=\"documentos_empresa/actions/recebimento.documentos.php?id_file=$files[id_file]\" target=\"_blank\">
										 Recebido
										</a>
									  </td>
									  <td align=\"center\"><a href=\"documentos_empresa/protoenvio.php?id_file=$files[id_file]&recebimento=true\" target=\"_blank\" title=\"Emitir relatorio\">
										<img src=\"documentos_empresa/img/DOC.png\"/>
									  </a></td>
									  <td>&nbsp;</td>
									  <td>";
									  if($id_user != 33 and $id_user != 36):
										  print "<a href=\"documentos_empresa/actions/reenvio.php?id_file=$files[id_file]\" class='status' title='Solicitar reenvio'>
													<img src=\"documentos_empresa/img/Arrow-cycle-18.png\" />
												  </a>";
									  endif;
									  print "</td>";
									  break;
									  
								case 2:
									print "<td colspan=\"4\" align=\"center\">
											Cancelado
										  </td>";
									 break;
									
						   }
					   }
					   
					   
					   
					   
					   
						 	
						print "</tr>";// Fecnhamento do tr
				
				}else{ // if(in_array($mes_selecionado,$mes_recebimento))
					// pegando os ids dos funcionarios
					
					$ids_responsaveis = implode(",",$ids_responsaveis);
					if($id_user != 33 and $id_user != 36):
							$bt_cancelar =  "<a onclick=\"confirmacao('documentos_empresa/actions/cancelar.envio.php?documento=$row_doc[id_documento]&mes_file=$mes_selecionado&id_regiao=$valor&funcionario=$ids_responsaveis','Tem certeza que deseja cancelar o envio desse documento?')\" href='#' class='status' title='Cancelar envio do documento'>
								<img src=\"documentos_empresa/img/Symbol-Stop.png\" />
							</a>";
							
					endif;
					print "<td></td>
					  	<td></td>
						<td align=\"center\">$bt_cancelar</td>
						<td></td>
						</tr>";						
					}
					 
				unset($ids_responsaveis);
				
				/////////////////////////////////////////
} // while($row_doc = mysql_fetch_assoc($qr_doc)) 
?>
                    </table>
                   
            
			
	<?php }
	unset($regioes,$mes_recebimento,$meses);
	
  }// fim do loop
    
  //Montando Mensagem de alerta
  $alert_final = "";
  if(!empty($alerta_atrazo)){
	$alerta_atrazo = array_flip($alerta_atrazo);
	$alerta_atrazo = array_flip($alerta_atrazo);
  	 $alert_final .= "Documentos atrasados: \\n";
	 foreach($alerta_atrazo as $atrazo){
		 $alert_final .= $atrazo . " \\n";		
	 }
  }
  if(!empty($alert_hoje)){
	  $alert_hoje = array_flip($alert_hoje);
	  $alert_hoje = array_flip($alert_hoje);
	  $alert_final .= "\\n Documentos vencidos hoje: \\n";
	  foreach($alert_hoje as $hoje){
		  $alert_final .= $hoje." \\n";
	  }
  }
  if(!empty($alert_proximo)){
	  $alert_proximo = array_flip($alert_proximo);
	  $alert_proximo = array_flip($alert_proximo);
	  $alert_final .= "\\n Proximo documentos a vencer: \\n";
	  foreach($alert_proximo as $proximo){
		  $alert_final .= $proximo . "\\n";
	  }
  }
	if(!empty($alert_final)){
			echo "<script>alert('$alert_final');</script>";		
	}
  
  if(!empty($num_doc)){
  ?>
  
  <table align="center" cellpadding="5" cellspacing="5">
	<tr>
    	<td colspan="10" align="center">Status</td>
    </tr>
	<tr>
	  <td align="center"><img src="documentos_empresa/img/aberto.png" alt="" /></td>
	  <td align="center"><img src="documentos_empresa/img/replicado.png" alt="" /></td>
	  <td align="center"><img src="documentos_empresa/img/respondido.png" alt=""/></td>
	  <td align="center"><img src="documentos_empresa/img/finalizado.png" alt=""/></td>
       <td align="center"><img src="documentos_empresa/img/cancelado.png" alt=""/></td>
	  </tr>
	<tr>
		<td align="center"><span style="font-size:10px">Aberto</span></td>
        <td align="center"><span style="font-size:10px">Enviado</span></td>
        <td align="center"><span style="font-size:10px">Concluído</span></td>
        <td align="center"><span style="font-size:10px">Atrasado</span></td>
        <td align="center"><span style="font-size:10px">Cancelado</span></td>
	</tr>	
</table>
  <?php 
  }else{// FIm do   if(!empty($num_doc)){
	print "<table align=\"center\">
		<tr>
			<td><span style=\"font-family:'Trebuchet MS', Arial, Helvetica, sans-serif'; font-size:12px;\">Nenhum documento cadastrado</span></td>
		</tr>
		</table>
	";  
  }
  }// LIMITE USUARIOS?>
</td>
	</tr>
</table>
<script language="javascript">
    
function validaForm(){
	d = document.form1;
	
	input_box = confirm("Deseja realmente Deletar as tarefas selecionadas?!");
	
	if (input_box == false){
		return false;
	}
	
	return true;
}
</script>
<!-- 
          -->
		  
          </td>
        </tr>
        <tr valign="top"> 
          <td colspan="4" bgcolor="#666666" >
          <div align="center" class="rodape2"><b><?=$row_master['razao']?></b> - Acesso Restrito a Funcion&aacute;rios</div></td>
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