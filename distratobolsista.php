<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login";
}else{

$id_user = $_COOKIE['logado'];
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net.css" rel="stylesheet" type="text/css">
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

//-->
</script>

<style type="text/css">
<!--
.style4 {font-family: Arial, Helvetica, sans-serif}
.style5 {color: #FF0000}
-->
</style>
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr align="center" valign="top"> 
          <td width="20" rowspan="2"> <div align="center"></div></td>
          <td align="left"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><br>
                  <span class="style4">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="../imagens/certificadosrecebidos.gif" width="120" height="86" align="middle">--><strong>TERMO DE DISTRATO DE BOLSA-AUX&Iacute;LIO</strong></span></td>
              </tr>
            </table>          
            <blockquote>
              <h5 class="style4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pelo  presente instrumento, distrato de presta&ccedil;&atilde;o de servi&ccedil;os aut&ocirc;nomo, que fazem de  um lado<strong> o</strong> <strong>INSTITUTO  SORRINDO PARA A VIDA - ISPV</strong>, OSCIP n.&ordm; 08026012349/200440  , inscrita no CNPJ sob n.&ordm; 06.888.897/0001-18,  com escrit&oacute;rio localizado na Alameda  Lorena n.&ordm; 800 - conjunto 1411, neste ato representada pelo seu Diretor  o senhor <strong>Urbano L&uacute;cio Esteves Junior</strong>, <strong>RG. 12.236.114-3</strong>, inscrito  no <strong>CPF. Sob n. &ordm; 064.860.838/75</strong>, aqui apresentada como <strong>CONTRATANTE</strong>, e de outro,<strong> <span class="style5">&lt;NOME&gt;</span>, RG. N&ordm;.<span class="style5">&lt;Identidade&gt;</span></strong> e inscrito no <strong>CPF n&ordm;.<span class="style5">&lt;CPF&gt;</span>, </strong>domiciliado na <span class="style5"><strong>&lt;Endere&ccedil;o&gt;</strong></span>, que tem ajustado o presente distrato nas Cl&aacute;usulas e  condi&ccedil;&otilde;es seguintes:</h5>
              <p class="style4"><strong>&nbsp;</strong><strong>I - OBJETO</strong></p>
              <p class="style4">O  presente Termo de Distrato tem por objeto o Contrato BOLSA-AUX&Iacute;LIO  assinado em <span class="style5"><strong>&lt;data_cadastro_bolsista&gt;</strong></span>. </p>
              <p class="style4"><strong>II - QUITA&Ccedil;&Atilde;O</strong></p>
              <p class="style4">Fica  quitada, toda e qualquer pend&ecirc;ncia e obriga&ccedil;&atilde;o do Contrato BOLSA-AUX&Iacute;LIO.</p>
              <p class="style4"><br>
                  <strong>III &ndash; OUTRAS DISPOSI&Ccedil;&Otilde;ES</strong></p>
              <p class="style4">Ficam  os Termos restantes do Contrato BOLSA-AUX&Iacute;LIO tamb&eacute;m renunciados e quitados, podendo atrav&eacute;s de  comunica&ccedil;&atilde;o e aceite entre as partes ser retomado a qualquer tempo. </p>
              <p class="style4">E  por estarem assim, juntas, advindas descontratadas firmam o presente  instrumento em 02 (duas) vias de igual teor, na presen&ccedil;a da testemunha abaixo  que tamb&eacute;m subscrevem.</p>
              <p class="style4"><br>
                    <span class="style5"><strong>&lt;Local&gt;</strong></span>, <span class="style5"><strong>&lt;data&gt;</strong></span>. </p>
              <p class="style4">&nbsp;</p>
              <p class="style4">__________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  ___________________________________<br>
                  <strong>&nbsp;&nbsp;&nbsp;&nbsp; INSTITUTO SORRINDO PARA A VIDA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BOLSISTA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>&nbsp; </p>
              <p align="center" class="style4">&nbsp;</p>
              <p align="center" class="style4">_______________________________________________<br>
                  <strong>SOE - SISTEMA OBJETIVO DE ENSINO <br>
              </strong></p>
              <hr>
              <div align="center">
<?php
$end = new empresa();
$end -> endereco('black','13px');
?></div>
              <p class="style4">&nbsp;</p>
            </blockquote>
          </td>
          <td width="20" rowspan="2">&nbsp;</td>
        </tr>
        
        <tr> 
          <td bgcolor="#8FC2FC" class="igreja" height="12"> 
            <div align="center"></div></td>
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
