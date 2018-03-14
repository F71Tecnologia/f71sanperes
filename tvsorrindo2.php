<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_user = $_COOKIE['logado'];
$id_bolsista = $_REQUEST['bol'];
$id_projeto = $_REQUEST['pro'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '20' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('20','$id_bolsista','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' and tipo = '20'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

if(empty($_REQUEST['tipo'])){
	$tipo = "1";
}else{
	$tipo = $_REQUEST['tipo'];
}

if($tipo == "2"){
$id_clt = $_REQUEST['clt'];

$result_bol = mysql_query("SELECT * FROM rh_clt where id_clt = '$id_clt' and id_projeto = '$id_projeto'");
$row_bol = mysql_fetch_array($result_bol);

$result_tv = mysql_query("SELECT * FROM tvsorrindo where id_clt = '$id_clt' and id_projeto = '$id_projeto'");
$row_tv = mysql_fetch_array($result_tv);

}else{
	
	
$result_bol = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data2, date_format(data_entrada, '%d/%m/%Y')as nova_data2 FROM autonomo where id_autonomo = '$id_bolsista'");
$row_bol = mysql_fetch_array($result_bol);

if($row_bol['id_bolsista'] != "0"){
	$result_tv = mysql_query("SELECT * FROM tvsorrindo where id_bolsista = '$row_bol[id_bolsista]' and id_projeto = '$row_bol[id_projeto]'");
	$row_tv = mysql_fetch_array($result_tv);
}else{
	$result_tv = mysql_query("SELECT * FROM tvsorrindo where id_bolsista = '$row_bol[id_autonomo]' and id_projeto = '$row_bol[id_projeto]'");
	$row_tv = mysql_fetch_array($result_tv);
}

}


$id_regiao2 = $row_bol['id_regiao'];

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao2'");
$row_regi = mysql_fetch_array($result_regi);

$dia = date('d');
$mes = date('n');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$NomeMes = $meses[$mes];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];
mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('4','$row_bol[0]','$data_cad', '$user_cad')");
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net1.css" rel="stylesheet" type="text/css">
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
.style28 {font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
.style32 {
	font-size: 12px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
.style33 {font-family: Verdana, Arial, Helvetica, sans-serif}
.style34 {font-size: 10px}
.style41 {font-size: 12px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; }
-->
</style>
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
        <tr align="center" valign="top">
          <td width="20" rowspan="2"> <div align="center"></div></td>
          <td align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><br>
                  <span class="style4"><?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?></span></td>
              </tr>
            </table>
            <p class="style28"> 
              <center>
                <span class="campotexto style32"><strong>BEM VINDO A TV SORRINDO<br>
                CAPACITA&Ccedil;&Atilde;O 24 HORAS </strong></span>
              </center>
              <span class="campotexto style32 style33"><br>
              <br>
              Para acessar a TV sorrindo visite <br>
              <strong>www.tvsorrindo.com.br e www.soeinterativo.com.br<br>
              </strong></span><br>
              <span class="campotexto style32 style33"><strong>Participante:  
			  <?=$row_bol['nome']?><br>
              Seu usu&aacute;rio &eacute; o seu CPF: <?php print "$row_bol[cpf]"; ?><br>
              Sua senha &eacute;:</strong></span><span class="style33 style32 style5"><strong> <?php print "$row_tv[senha]"; ?> </strong></span><span class="style33 style32 campotexto"><strong><br>
              <br>
              Onde voc&ecirc; entrar&aacute; dados do seu curso e material 
                visual e did&aacute;tico dispon&iacute;vel para download.<br>
                <br>
Os v&iacute;deos e todo material reproduzidos e/ou apresentados no site www.tvsorrindo.com.br e www.soeinterativo.com.br s&atilde;o de exclusivo crit&eacute;rio educacional, n&atilde;o podendo ser reproduzidos total ou parcialmente em nenhum outro meio de divulga&ccedil;&atilde;o seja em massa ou individualmente. O usu&aacute;rio da Capacita&ccedil;&atilde;o on-line que desrespeitar este termo estar&aacute; sendo indiciado dentro dos par&acirc;metros das Legisla&ccedil;&otilde;es de Direito autoral vigentes.<br>
              </strong></span><span class="style41"><br>
              Declaro estar ciente de que o Acesso a Capacita&ccedil;&atilde;o &eacute; de minha inteira responsabilidade e que estarei marcando minha presen&ccedil;a e gerando certificados individualmente para todos os cursos que concluir.<br>
              <br>
              L&iacute; e concordo com os crit&eacute;rios de acesso e estarei 
                  marcando minha presen&ccedil;a e confirmando minhas mat&eacute;rias 
                  nos sites www.tvsorrindo.com.br e e www.soeinterativo.com.br e declaro estar de acordo.<br>
              <br>
              <?php print "$row_regi[regiao], $dia de $NomeMes de $ano"; ?><br>
              <br>
              <br>
              <br>
              </span> </p>
            <p class="style28"><center>
                <span class="campotexto style32 style33"><strong>                ______________________________________<br>
                <span class="style34">Assinatura de recebimento da senha de acesso aos cursos                </span></strong></span>              
                </center>
              <font size="3">
              <center>              
              </center>
              </font></p>
            <p><font size="3"> <br>
              </font> </p>          </td>
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
