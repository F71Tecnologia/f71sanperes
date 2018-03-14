<?php
if(empty($_COOKIE['logado'])){
	print "<center>Efetue o Login<br><a href='login.php'>Logar</a></center>";
	exit;
}

include "conn.php";
include "classes/funcionario.php";
include "funcoes.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;

if (empty($_REQUEST['senha'])){
?>

<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'><link href="net.css" rel="stylesheet" type="text/css">
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
</head>

<body bgcolor="#00CCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br>
<br><br>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top">
<form action="login_adm.php" method="post" name="form1">
<br>
<div class="style3">Login do Administrativo</div>
<table width="400" height="194" border="0" cellpadding="0" cellspacing="0" background="imagens/caixa_logo.gif">
<tr> 
<td width="134" height="40">&nbsp;</td>
<td width="200">&nbsp;</td>
<td width="66">&nbsp;</td>
</tr>
<tr class="linha"> 
<td><div align="right" class="style3">Nome:</div></td>
<td>&nbsp;&nbsp;<input name='login' type='text' class='campotexto' id='login' value='<?=$nome?>' disabled='disabled'></td>
<td>&nbsp;</td>
</tr>
<tr  class="linha"> 
            <td height="40"> 
              <div align="right" class="style3">Senha:</div></td>
<td>&nbsp;&nbsp;<input name="senha" type="password" class="campotexto" id="senha"></td>
<td>&nbsp;</td>
</tr>
<tr> 
            <td height="15">&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr> 
            <td>&nbsp;</td>
            <td valign="top">
<input type="reset" name="Submit" value="Limpar" class="campotexto">
              <input type="submit" name="Submit2" value="Enviar" class="campotexto">
            </td>
            <td align="right">&nbsp; </td>
</tr>
</table>
<input type='hidden' name='regiao' value='<?=$regiao?>'/>
</form>
</td>
</tr>
</table>
</body>
</html>
<?php

} else {

//EFETUANDO A PESQUISA PARA LOGAR

$senha = $_REQUEST['senha'];
//$url = ($_COOKIE['logado'] == 75) ? "novoFinanceiro/index.php?regiao=$regiao" : "financeiro/novofinanceiro.php?regiao=$regiao";
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);

$url = "novoFinanceiro/index.php?enc=$linkEnc";

include "classes/logar.php";

$logando = new logar();
$logando -> LoginFinanceiro($senha,1,$url);


}
?>