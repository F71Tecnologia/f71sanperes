<?php
include "../conn.php";

$projeto	=	$_GET['pro'];
$id_regiao	=	$_GET['id_reg'];
$clt		=	$_GET['clt'];	

$result_clt= mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y')as data_nasci FROM rh_clt WHERE id_clt = '$clt'");
$row = mysql_fetch_array($result_clt);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa)
?>

<HTML>
<!-- saved from url=(0020)http://www.corel.com -->
<HEAD>
	<TITLE>SOLICITA&Ccedil;&Atilde;O DE PIS</TITLE>
	<META http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
	<META NAME="Generator" CONTENT="CorelDRAW X4">
	<META NAME="Date" CONTENT="11/24/2008">

<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
#apDiv1 {
	position:absolute;
	left:334px;
	top:87px;
	width:246px;
	height:121px;
	z-index:1;
}
#apDiv2 {
	position:absolute;
	left:81px;
	top:228px;
	width:193px;
	height:17px;
	z-index:2;
}
#apDiv3 {
	position:absolute;
	left:293px;
	top:228px;
	width:439px;
	height:18px;
	z-index:3;
}
#apDiv4 {
	position:absolute;
	left:82px;
	top:262px;
	width:650px;
	height:18px;
	z-index:4;
}
#apDiv5 {
	position:absolute;
	left:325px;
	top:297px;
	width:193px;
	height:16px;
	z-index:5;
}
#apDiv6 {
	position:absolute;
	left:540px;
	top:298px;
	width:193px;
	height:16px;
	z-index:6;
}
#apDiv7 {
	position:absolute;
	left:85px;
	top:334px;
	width:590px;
	height:18px;
	z-index:7;
}
#apDiv8 {
	position:absolute;
	left:83px;
	top:367px;
	width:150px;
	height:17px;
	z-index:8;
}
#apDiv9 {
	position:absolute;
	left:250px;
	top:367px;
	width:45px;
	height:17px;
	z-index:9;
}
#apDiv10 {
	position:absolute;
	left:311px;
	top:368px;
	width:419px;
	height:17px;
	z-index:10;
}
#apDiv11 {
	position:absolute;
	left:83px;
	top:402px;
	width:353px;
	height:13px;
	z-index:11;
}
#apDiv12 {
	position:absolute;
	left:447px;
	top:402px;
	width:32px;
	height:16px;
	z-index:12;
}
#apDiv13 {
	position:absolute;
	left:493px;
	top:402px;
	width:63px;
	height:17px;
	z-index:13;
}
#apDiv14 {
	position:absolute;
	left:82px;
	top:432px;
	width:141px;
	height:20px;
	z-index:14;
}
#apDiv15 {
	position:absolute;
	left:231px;
	top:432px;
	width:40px;
	height:19px;
	z-index:15;
}
#apDiv16 {
	position:absolute;
	left:276px;
	top:432px;
	width:36px;
	height:21px;
	z-index:16;
}
#apDiv17 {
	position:absolute;
	left:331px;
	top:434px;
	width:172px;
	height:16px;
	z-index:17;
}
#apDiv18 {
	position:absolute;
	left:83px;
	top:467px;
	width:131px;
	height:16px;
	z-index:18;
}
#apDiv19 {
	position:absolute;
	left:226px;
	top:469px;
	width:50px;
	height:16px;
	z-index:19;
}
#apDiv20 {
	position:absolute;
	left:286px;
	top:470px;
	width:27px;
	height:14px;
	z-index:20;
}
#apDiv21 {
	position:absolute;
	left:331px;
	top:469px;
	width:168px;
	height:16px;
	z-index:21;
}
#apDiv22 {
	position:absolute;
	left:509px;
	top:469px;
	width:46px;
	height:15px;
	z-index:22;
}
#apDiv23 {
	position:absolute;
	left:83px;
	top:502px;
	width:648px;
	height:15px;
	z-index:23;
}
#apDiv24 {
	position:absolute;
	left:82px;
	top:536px;
	width:231px;
	height:14px;
	z-index:24;
}
#apDiv25 {
	position:absolute;
	left:318px;
	top:536px;
	width:182px;
	height:17px;
	z-index:25;
}
#apDiv26 {
	position:absolute;
	left:510px;
	top:536px;
	width:57px;
	height:16px;
	z-index:26;
}
#apDiv27 {
	position:absolute;
	left:585px;
	top:535px;
	width:142px;
	height:17px;
	z-index:27;
}
#apDiv28 {
	position:absolute;
	left:336px;
	top:609px;
	width:247px;
	height:121px;
	z-index:28;
}
#apDiv29 {
	position:absolute;
	left:84px;
	top:750px;
	width:196px;
	height:17px;
	z-index:29;
}
#apDiv30 {
	position:absolute;
	left:295px;
	top:751px;
	width:441px;
	height:15px;
	z-index:30;
}
#apDiv31 {
	position:absolute;
	left:83px;
	top:785px;
	width:651px;
	height:16px;
	z-index:31;
}
#apDiv32 {
	position:absolute;
	left:327px;
	top:817px;
	width:193px;
	height:16px;
	z-index:32;
}
#apDiv33 {
	position:absolute;
	left:541px;
	top:817px;
	width:195px;
	height:16px;
	z-index:33;
}
#apDiv34 {
	position:absolute;
	left:84px;
	top:860px;
	width:649px;
	height:13px;
	z-index:34;
}
#apDiv35 {
	position:absolute;
	left:85px;
	top:891px;
	width:150px;
	height:16px;
	z-index:35;
}
#apDiv36 {
	position:absolute;
	left:247px;
	top:891px;
	width:47px;
	height:16px;
	z-index:36;
}
#apDiv37 {
	position:absolute;
	left:307px;
	top:892px;
	width:425px;
	height:15px;
	z-index:37;
}
#apDiv38 {
	position:absolute;
	left:83px;
	top:925px;
	width:355px;
	height:15px;
	z-index:38;
}
#apDiv39 {
	position:absolute;
	left:444px;
	top:924px;
	width:35px;
	height:17px;
	z-index:39;
}
#apDiv40 {
	position:absolute;
	left:492px;
	top:924px;
	width:64px;
	height:16px;
	z-index:40;
}
#apDiv41 {
	position:absolute;
	left:84px;
	top:957px;
	width:139px;
	height:15px;
	z-index:41;
}
#apDiv42 {
	position:absolute;
	left:232px;
	top:958px;
	width:43px;
	height:15px;
	z-index:42;
}
#apDiv43 {
	position:absolute;
	left:281px;
	top:959px;
	width:35px;
	height:15px;
	z-index:43;
}
#apDiv44 {
	position:absolute;
	left:333px;
	top:958px;
	width:164px;
	height:14px;
	z-index:44;
}
#apDiv45 {
	position:absolute;
	left:506px;
	top:958px;
	width:48px;
	height:15px;
	z-index:45;
}
#apDiv46 {
	position:absolute;
	left:83px;
	top:993px;
	width:137px;
	height:15px;
	z-index:46;
}
#apDiv47 {
	position:absolute;
	left:226px;
	top:995px;
	width:46px;
	height:16px;
	z-index:47;
}
#apDiv48 {
	position:absolute;
	left:286px;
	top:993px;
	width:30px;
	height:16px;
	z-index:48;
}
#apDiv49 {
	position:absolute;
	left:332px;
	top:993px;
	width:166px;
	height:15px;
	z-index:49;
}
#apDiv50 {
	position:absolute;
	left:506px;
	top:997px;
	width:49px;
	height:14px;
	z-index:50;
}
#apDiv51 {
	position:absolute;
	left:585px;
	top:1061px;
	width:137px;
	height:16px;
	z-index:51;
}
#apDiv52 {
	position:absolute;
	left:509px;
	top:1061px;
	width:67px;
	height:15px;
	z-index:52;
}
#apDiv53 {
	position:absolute;
	left:318px;
	top:1061px;
	width:178px;
	height:15px;
	z-index:53;
}
#apDiv54 {
	position:absolute;
	left:81px;
	top:1061px;
	width:228px;
	height:16px;
	z-index:54;
}
#apDiv55 {
	position:absolute;
	left:83px;
	top:1026px;
	width:645px;
	height:15px;
	z-index:55;
}

.pagina, #content {
    height: auto !important;
}
-->
</style>
<link href="../net1.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY BGCOLOR="#FFFFFF">
<div id="apDiv1">
  <p class="linha"><?=$row_empresa['cnpj']?></p>
  <p class="linha"><?=$row_empresa['endereco']?><br>
    <br>
  </p>
</div>
<div class="linha" id="apDiv2"><?=$row_empresa['cnpj']?></div>
<div class="linha" id="apDiv3"><?=$row_empresa['nome']?></div>
<div class="linha" id="apDiv4"><?=$row_empresa['endereco']?></div>
<div class="linha" id="apDiv5"><?=$row_empresa['tel']?></div>
<div class="linha" id="apDiv6"><?=$row_empresa['fax']?></div>
<div class="linha" id="apDiv7"><?=$row['nome']?></div>
<div class="linha" id="apDiv8"><?=$row['data_nasci']?></div>
<div class="linha" id="apDiv9"><?=$row['sexo']?></div>
<div class="linha" id="apDiv10"><?=$row['mae']?></div>
<div class="linha" id="apDiv11"><?=$row['naturalidade']?></div>
<div class="linha" id="apDiv12"></div>
<div class="linha" id="apDiv13"></div>
<div class="linha" id="apDiv14"><?=$row['campo1']?></div>
<div class="linha" id="apDiv15"><?=$row['serie_ctps']?></div>
<div class="linha" id="apDiv16"><?=$row['uf_ctps']?></div>
<div class="linha" id="apDiv17"><?=$row['cpf']?></div>
<div class="linha" id="apDiv18"><?=$row['rg']?></div>
<div class="linha" id="apDiv19"><?=$row['orgao']?></div>
<div class="linha" id="apDiv20"><?=$row['uf_rg']?></div>
<div class="linha" id="apDiv21"><?=$row['titulo']?></div>
<div class="linha" id="apDiv22"><?=$row['secao']?></div>
<div class="linha" id="apDiv23"><?=$row['endereco']?></div>
<div class="linha" id="apDiv24"><?=$row['bairro']?></div>
<div class="linha" id="apDiv25"><?=$row['cidade']?></div>
<div class="linha" id="apDiv26"><?=$row['uf']?></div>
<div class="linha" id="apDiv27"><?=$row['cep']?></div>
<div class="linha" id="apDiv28">  
  <p class="linha"><?=$row_empresa['cnpj']?></p>
<p class="linha"><?=$row_empresa['endereco']?><br></div>
<div class="linha" id="apDiv29"><?=$row_empresa['cnpj']?>
</div>
<div class="linha" id="apDiv30"><?=$row_empresa['nome']?></div>
<div class="linha" id="apDiv31"><?=$row_empresa['endereco']?></div>
<div class="linha" id="apDiv32"><?=$row_empresa['tel']?></div>
<div class="linha" id="apDiv33"><?=$row_empresa['fax']?></div>
<div class="linha" id="apDiv34"><?=$row['nome']?></div>
<div class="linha" id="apDiv35"><?=$row['data_nasci']?></div>
<div class="linha" id="apDiv36"><?=$row['sexo']?></div>
<div class="linha" id="apDiv37"><?=$row['mae']?></div>
<div class="linha" id="apDiv38"><?=$row['naturalidade']?></div>
<div class="linha" id="apDiv39"></div>
<div class="linha" id="apDiv40"></div>
<div class="linha" id="apDiv41"><?=$row['campo1']?></div>
<div class="linha" id="apDiv42"><?=$row['serie_ctps']?></div>
<div class="linha" id="apDiv43"><?=$row['uf_ctps']?></div>
<div class="linha" id="apDiv44"><?=$row['cpf']?></div>
<div class="linha" id="apDiv45"></div>
<div class="linha" id="apDiv46"><?=$row['rg']?></div>
<div class="linha" id="apDiv47"><?=$row['orgao']?></div>
<div class="linha" id="apDiv48"><?=$row['uf_rg']?></div>
<div class="linha" id="apDiv49"><?=$row['titulo']?></div>
<div class="linha" id="apDiv50"></div>
<div class="linha" id="apDiv51"><?=$row['cep']?></div>
<div class="linha" id="apDiv52"><?=$row['uf']?></div>
<div class="linha" id="apDiv53"><?=$row['cidade']?></div>
<div class="linha" id="apDiv54"><?=$row['bairro']?></div>
<div class="linha" id="apDiv55"><?=$row['endereco']?></div>

<TABLE WIDTH=795 BORDER=0 align="left" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="bordaescura1px">
<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=67 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=231 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=19 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=19 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=259 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=259 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=6 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=6 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=144 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=144 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=1 HEIGHT=1></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=1><IMG SRC="images/hex1.gif" WIDTH=67 HEIGHT=1></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=43></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=43></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=67 HEIGHT=160></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex1.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=19 HEIGHT=160></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=259 HEIGHT=160><IMG SRC="images/hex2.jpg" HEIGHT=160 WIDTH=259 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=6 HEIGHT=160></TD>
	<TD COLSPAN=2 ROWSPAN=9 WIDTH=145 HEIGHT=160><IMG SRC="images/hex3.jpg" HEIGHT=160 WIDTH=145 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=67 HEIGHT=160></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=8></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=8></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=22></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=22><IMG SRC="images/hex4.jpg" HEIGHT=22 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=6></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=6></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=10></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=10><IMG SRC="images/hex5.jpg" HEIGHT=10 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex6.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=65></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=65></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex7.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=7></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=7></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=10></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=10></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=100></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=100></TD>
	<TD COLSPAN=6 ROWSPAN=1 WIDTH=660 HEIGHT=100><IMG SRC="images/hex8.jpg" HEIGHT=100 WIDTH=660 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=100></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=239></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=239></TD>
	<TD COLSPAN=5 ROWSPAN=1 WIDTH=659 HEIGHT=239><img src="images/hex9.jpg" height=239 width=659 align=TOP border=0></TD>
	<TD COLSPAN=2 ROWSPAN=1 WIDTH=68 HEIGHT=239></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=12></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=12></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=67 HEIGHT=160></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex10.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=19 HEIGHT=160></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=259 HEIGHT=160><IMG SRC="images/hex11.jpg" HEIGHT=160 WIDTH=259 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=6 HEIGHT=160></TD>
	<TD COLSPAN=2 ROWSPAN=9 WIDTH=145 HEIGHT=160><IMG SRC="images/hex12.jpg" HEIGHT=160 WIDTH=145 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=9 WIDTH=67 HEIGHT=160></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=8></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=8></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=22></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=22><IMG SRC="images/hex13.jpg" HEIGHT=22 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=6></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=6></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=10></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=10><IMG SRC="images/hex14.jpg" HEIGHT=10 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex15.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=65></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=65></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=14></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=14><IMG SRC="images/hex16.jpg" HEIGHT=14 WIDTH=231 ALIGN=TOP BORDER=0></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=7></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=231 HEIGHT=7></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=10></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=10></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=100></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=100></TD>
	<TD COLSPAN=6 ROWSPAN=1 WIDTH=660 HEIGHT=100><IMG SRC="images/hex17.jpg" HEIGHT=100 WIDTH=660 ALIGN=TOP BORDER=0></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=100></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=8></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=8></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=235></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=235></TD>
	<TD COLSPAN=6 ROWSPAN=1 WIDTH=660 HEIGHT=235><img src="images/hex18.jpg" height=235 width=660 align=TOP border=0></TD>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=67 HEIGHT=235></TD>
</TR>

<TR ALIGN=LEFT VALIGN=TOP>
	<TD COLSPAN=1 ROWSPAN=1 WIDTH=1 HEIGHT=45></TD>
	<TD COLSPAN=8 ROWSPAN=1 WIDTH=794 HEIGHT=45></TD>
</TR>

</TABLE>
</BODY>
</HTML>
