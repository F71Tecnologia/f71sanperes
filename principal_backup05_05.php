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
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<script type="text/javascript" src="js/ramon.js"></script>


<script type="text/javascript">
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
<!--
body {
	margin:0px;
	/*background-color:#E2E2E2;*/
	/*background:url(imagens/fundologin.gif)*/
}
.style29 {
	font-family:Arial, Helvetica, sans-serif arial;
	font-weight:bold;
}
.style30 {
	color: #663300
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
          <marquee scrolldelay="100" scrollamount="5" hspace="10" truespeed="truespeed"> 
          <div align="center"><font color="#ffffff" face="Verdana, Arial, Helvetica, sans-serif" size="1"><strong>O perfume sempre perdura na mão que oferece a flor. (Halda Béjar) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Quando faço o bem, sinto-me bem, e quando faço o mal, sinto-me mal. Eis a minha religião. (Abraham Lincoln) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nunca acendas um fogo que não possas apagar. (Provérbio Chinês)
          </strong></font></div>
          </marquee></td>
        </tr>
        <tr> 
          <td height="69" width="165" align="right" valign="top"> <?=$menu?></td>
          <td width="549" align="center" valign="top" >
          <br />
          <?php
		  $usuarios_avancados = array('1', '5', '9', '68', '75');
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
				   				 AND doc.status_documento = '1' AND res.id_funcionario = '$id_user'");
$num_documentos = mysql_num_rows($query_documentos);
$mes_envio = array();

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
		  $documentos = array();
		  if(in_array(date("m"),$mes_envio)){
				$id_documento[] = $row_documentos['id_documento'];
				$documentos[] = $row_documentos['nome_documento'];
				$dia[] = $row_documentos['dia_documento']."/".date("m")."/".date("Y");
				
				// Corrigir Erro
				$regioes_encarregadas[] = 0;
				$regioes_enviadas[] = 0;
				
				// Consulta dos Responsáveis do Documento
				$query_responsavel = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$row_documentos[id_documento]' AND id_funcionario = '$id_user'");
				$row_responsavel = mysql_fetch_assoc($query_responsavel);
				$num_rows_responsavel = mysql_num_rows($query_responsavel);
				// Criei uma Array com as Ids Regioes Encarregadas
				$regioes_encarregadas = explode(',', $row_responsavel['ids_regioes']);

				// Consulta dos Documentos Enviados								  
				$query_file = mysql_query("SELECT * FROM doc_files WHERE id_documento = '$row_documentos[id_documento]'");
				while($row_file = mysql_fetch_assoc($query_file)) {
					// Criei uma Array com as Ids Regioes Enviadas
					$regioes_enviadas[] = $row_file['id_regiao'];
				}
				
				// Criei uma Array com as Regiões que não foram enviadas
				$intercecao = array_diff($regioes_encarregadas,$regioes_enviadas);
				if(!empty($intercecao)){
					$regioes[] = $intercecao;
				}

				// Resetei as Duas Arrays
				unset($regioes_encarregadas,$regioes_enviadas);
				
				
		  }
unset($mes_envio);
}
if(!empty($num_documentos)){
	if(!empty($regioes)){
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
      <td width="30%"><?=$id_documento[$key]." - ".$doc?></td>
      <td><?php 
	  $data_envio = implode("",explode("/",$dia[$key]));
	  $data_hoje = implode("",explode("/",date("d/m/Y")));
	  
	  if($data_envio <= $data_hoje){
		$documento_a_enviar[] = $doc;
	  } 
	  echo $dia[$key];
	  ?></td>
      <td width="50%">
	  <table width="100%">
	  <?php
			foreach($regioes[$key] as $id_regiao){
				$query_regioes = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
				while($row_regiao = mysql_fetch_assoc($query_regioes)){
					echo "<tr><td><a href=\"documentos_empresa/envio.php?id_doc=$id_documento[$key]&id_fun=$id_user&id_regiao=$id_regiao&mes=".date("m")."\" onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">".$id_regiao." - ".$row_regiao['regiao']."</a></td><tr>";
				}
			}
	  ?>
      </table>
     </td>
    </tr>
    <?php 
		}
		
	}// FIM DO FOREACH
	if(!empty($documento_a_enviar)){
		echo "<script>alert('Atenção! \\n Você precisa enviar os documentos: ".implode(', ',$documento_a_enviar)."!');</script>";
	}
	?>

  </table>  
<?php
	}//
		

	}?>

<?php 

if(isset($_REQUEST['mes'])){
	$mes_selecionado = $_REQUEST['mes'];
}else{
	$mes_selecionado = date("m");
}

?>

<?php
$acesso_documentos = array('75','5','9','27');
if(in_array($id_user,$acesso_documentos)) {

?>

<table width="95%" cellpadding="5" style="font-family:'Trebuchet MS', Arial, Helvetica, sans-serif;" >
    <tr>
        <td class="showdois">
        <span style="font-size:23px; color:#036;">&gt;</span>  Documentos a receber
        </td>
    </tr>
    <tr>
        <td >
          <form id="form2" name="form2" method="get" action="">
            <table width="400" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td align="right"> Mes </td>
                <td><label>
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
                <td><label>
                  <input type="submit" name="button" id="button" value="Procurar" />
                </label></td>
              </tr>
            </table>
          </form></td>
    </tr>
    <tr>
        <td>  
<?php 

// buscando todas os documentos e responsaveis;
$qr_doc = mysql_query("SELECT * FROM documentos WHERE status_documento = '1' ORDER BY nome_documento ASC");


$mes_recebimento = array();

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
	if(in_array($mes_selecionado,$mes_recebimento)) {
		    $regioes = array();

			$qr_res = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = $row_doc[id_documento]");
			while($row_res = mysql_fetch_assoc($qr_res)) {
				 
					$ids_reg = explode(',', $row_res['ids_regioes']);
					
					foreach($ids_reg as $valor) {
							if(!in_array($valor, $regioes)) {
									$regioes[] = $valor;
							}
					}
					
					unset($ids_reg, $valor); 
					
			} ?>
            

                   <table cellpadding="2" cellspacing="2" style="margin-bottom:20px; width:95%; border:1px solid #CCC; font-family:'Trebuchet MS', Arial, Helvetica, sans-serif; font-size:12px; line-height:22px;">
                        <tr>
                            <td colspan="5" align="center"><?=$row_doc['id_documento']?> - <?=$row_doc['nome_documento']?></td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Região</td>
                            <td>Responsáveis</td>
                            <td>Status</td>
                            <td>Enviado por:</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php foreach($regioes as $valor) { ?>
                        <tr style="font-weight:bold;" bgcolor="<? if($alternateColor++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>">
                            <td><?
                            $qr_nome_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$valor'");
							$row_nome_regiao = mysql_fetch_assoc($qr_nome_regiao);
                            echo $row_nome_regiao['id_regiao']." - ".$row_nome_regiao['regiao'];
                            ?></td>
                            <td>
                             <?php $qr_responsaveis = mysql_query("SELECT * FROM doc_responsaveis WHERE id_documento = '$row_doc[id_documento]'");
                                   while($row_responsaveis = mysql_fetch_assoc($qr_responsaveis)) {
                                        
                                        $ids_reg = explode(',', $row_responsaveis['ids_regioes']);
                                    
                                        if(in_array($valor, $ids_reg)) {
                                                $qr_nome = mysql_query("SELECT id_funcionario,nome FROM funcionario WHERE id_funcionario = '$row_responsaveis[id_funcionario]'");
												$row_nome_fun = mysql_fetch_assoc($qr_nome);
                                                $nome = explode(' ', $row_nome_fun['nome']);
                                                $nome = $nome[0].' '.$nome[1];
                                                $responsaveis[] = $row_nome_fun['id_funcionario']." - ".$nome;
                                        }
                                    
                                    }
                                    
                                    echo implode(',', $responsaveis);
                                    unset($responsaveis);
                                    ?>
                            </td>
                          <td>
                            <?php $qr_files = mysql_query("SELECT * FROM doc_files WHERE id_documento = '$row_doc[id_documento]' AND mes_file = '$mes_selecionado' AND id_regiao = '$valor'");
							$num_files = mysql_num_rows($qr_files);
							$files = mysql_fetch_assoc($qr_files);
							if(empty($num_files)) {
								
							   	$data_vencimento = $row_doc['dia_documento'].date("m-Y");
								$data_vencimento = implode('',explode('-',$data_vencimento));
								$data_envio = date('d-m-Y');
								$data_envio = implode('',explode('-',$data_envio));
								if($data_envio >= $data_vencimento){
								 	echo "<img src=\"documentos_empresa/img/finalizado.png\" alt='Atrazado'/>";
								}else{
									echo "<img src=\"documentos_empresa/img/aberto.png\" alt='Aberto'/>";
								}
								// verificação de envio 
								$falta = $row_doc['dia_documento'] - date("d");
								if($falta <= 2 and $falta >= 0){
									$alerta_atrazo = "<script>alert('Faltam $falta para ser enviado o documento $row_doc[nome_documento]')</script>";
								}elseif($falta <= 0){
									$alerta_atrazo = "<script>alert('O documento $row_doc[nome_documento] esta atrazado ".($falta * -1)." Dias')</script>";
								
								}
								
						   } elseif($files['recebimento_file'] == 0) {
                               echo "<img src=\"documentos_empresa/img/replicado.png\" alt='Enviado' />";
						   } elseif($files['recebimento_file'] == 1){
						   	  echo "<img src=\"documentos_empresa/img/respondido.png\"/>";
						   }?>
                          </td>
                          <td><?php 
                          $qr_enviado = mysql_query("SELECT login FROM funcionario WHERE id_funcionario = '$files[id_funcionario]'");
                          if(mysql_num_rows($qr_enviado) != 0){
                            echo mysql_result($qr_enviado,0);
                          ?>
                         </td>
                          <td><a href="documentos_empresa/actions/recebimento.documentos.php?id_file=<?=$files['id_file']?>" target="_blank">Baixar</a></td>
                        </tr>
                        <?php }else{ ?>

                          <td></td>
                        </tr>
						<?php 
						}
						} ?>
                    </table>
                    
            
			
	<?php }
	unset($regioes,$mes_recebimento,$meses);
	
		
}
?>
<table align="center">
	<tr>
    	<td colspan="8" align="center">Status</td>
    </tr>
	<tr>
		<td><span style="font-size:10px">Aberto</span><img src="documentos_empresa/img/aberto.png" /></td>
        <td><span style="font-size:10px">Enviado</span><img src="documentos_empresa/img/replicado.png" /></td>
        <td><span style="font-size:10px">Concluído</span><img src="documentos_empresa/img/respondido.png"/></td>
        <td><span style="font-size:10px">Atrazado</span><img src="documentos_empresa/img/finalizado.png"/></td>
	</tr>	
</table>
		</td>
	</tr>
</table>

  <?php }// LIMITE USUARIOS?>


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
		  <br>
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