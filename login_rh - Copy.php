<?php
if(empty($_COOKIE['logado'])){
	print "<center>Efetue o Login<br><a href='login.php'>Logar</a></center>";
	exit;
}

include "conn.php";
include "classes/funcionario.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;

if (empty($_REQUEST['senha'])){
?>

<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'>
<link href="net1.css" rel="stylesheet" type="text/css">
</head>
<body style="background-color: #E8FFF3; " leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<br>
<br><br>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top">
<form action="login_rh.php" method="post" name="form1">
<br>
<div class="style3" style="color:#000;">Login para Acesso a Gestão de Recursos Humanos</div>
<table width="400" height="194" border="0" cellpadding="0" cellspacing="0" background="imagens/caixa_logo.gif">
<tr> 
<td height="40" colspan="3" style="color:#F00; text-align:center; font-size:11px; font-weight:bold;">
<?php if($_GET['senha_errada']) { ?>
Senha incorreta
<?php } ?>
</td>
</tr>
<tr class="linha"> 
<td width="134"><div align="right" class="style3">Nome:</div></td>
<td width="200">&nbsp;&nbsp;<input name='login' type='text' class='campotexto' id='login' value='<?=$nome?>' disabled='disabled'></td>
<td width="66">&nbsp;</td>
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
              <input type="submit" name="Submit2" value="Enviar" class="campotexto">
            </td>
            <td align="right">&nbsp;</td>
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

$senha = trim($_REQUEST['senha']);
$url = "principalrh.php?regiao=$regiao&id=1";

include "classes/logar.php";

$logando = new logar();
$logando -> LoginRH($senha,3,$url);

}
?>