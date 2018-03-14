<?php


if(empty($_COOKIE['logado'])){
   header("Location: ../login.php?entre=true");
   exit;
}

session_start();

if(isset($_SESSION['adm']))
{
	header("Location: index.php");
	}
	



 include "../conn.php";


include "../classes/funcionario.php";



$user = new funcionario();
$user -> MostraUser(0);
$nome = $user -> nome1;
$regiao = $user -> id_regiao;

if($_POST['pronto'] == "login") {
	
//EFETUANDO PESQUISA PARA LOGAR
	
	$senha = trim($_POST['senha']);
	
	/*$url = '../adm/index.php';
	include('../classes/logar.php');
	
	$logando = new logar();
	$logando->LoginAdministracao($senha,4,$url);
	*/
	
	
	
	$qr_login = mysql_query("SELECT * FROM senhas WHERE id_senha = '4' AND senha = '$senha' ");
	$login = mysql_num_rows($qr_login);
	if(!empty($login)) {
		if(!isset($_SESSION['adm'])) {
		    session_start();
		    $_SESSION['adm'] = $_POST['usuario'];
		}
	    header("Location: index.php");
	} else {
		header("Location: login.php?senha=errada");
    }
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'>
<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	margin:0px;
	background-color:   #FFFFE8;;
}
</style>
</head>
<body >
<br><br><br>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top">
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1">
<br>
<div class="style3">Login para Acesso a Administra&ccedil;&atilde;o</div>
<table width="400" height="194" border="0" cellpadding="0" cellspacing="0" background="../imagens/caixa_logo.gif">
<tr> 
<td height="40" colspan="3" style="color:#F00; text-align:center; font-size:11px; font-weight:bold;">
<?php if($_GET['senha_errada']) { ?>
Senha incorreta
<?php } ?>
</td>
</tr>
<tr class="linha"> 
<td width="134"><div align="right" class="style3">Nome:</div></td>
<td width="200">&nbsp;&nbsp;<input name="login" type="text" value="<?=$nome?>" disabled="disabled" class="campotexto"></td>
<td width="66">&nbsp;</td>
</tr>
<tr class="linha"> 
<td height="40"> 
<div align="right" class="style3">Senha:</div></td>
<td>&nbsp;&nbsp;<input name="senha" type="password" class="campotexto"></td>
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
<input type="submit" name="Submit" value="Enviar" class="campotexto">
</td>
<td align="right">&nbsp;</td>
</tr>
</table>
<input type="hidden" name="usuario" value="<?=$_COOKIE['logado']?>" />
<input type='hidden' name='regiao' value=<?=$regiao?>/>
<input type="hidden" name="pronto" value="login" />
</form>
</td>
</tr>
</table>
</body>
</html>