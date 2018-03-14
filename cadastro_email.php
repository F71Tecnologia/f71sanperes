<?php
include('conn.php');
include "funcoes.php";
ini_set('display_errors', '0');

$qr_funcionario = mysql_query("SELECT * FROM funcionario  WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func       = mysql_fetch_assoc($qr_funcionario);

$msg	        = $_GET['msg'];
$menssagem      = array(1 => '** Para que o novo sistema de e-mail funcione perfeitamente,<br> é necessário cadastrar seu e-mail na Intranet.') ;
					

if(isset($_POST['enviar'])){
	
	$email    = $_POST['email'];
	$senha    = $_POST['senha'];
	$master_id = $_POST['master_id'];
	$dominio   = $_POST['dominio_email'];
	
		
	$email = $email.'@'.$dominio;
	
	
	$servidor_dominio = mysql_result(mysql_query("SELECT email_servidor FROM master WHERE id_master = '$master_id'"),0);
	$servidor = '{'.$servidor_dominio.':143/novalidate-cert}INBOX';
	ini_set('display_errors', '1');
	$mbox = @imap_open($servidor , $email, $senha) ;	
	$erro = imap_last_error();	
	
	if(!empty($erro)) {	
		
		$erro = 'E-mail ou senha incorreto.';
		
		
		} else {
		
			mysql_query("INSERT INTO funcionario_email_assoc (id_master, id_funcionario,email, senha)
													VALUES
													('$master_id','$_COOKIE[logado]','$email','$senha')") or die(mysql_error());														
			echo "<script> alert('Seu e-email foi cadastrado com sucesso!');
			location.href='index.php';
			</script>";												
															
			exit;		
		}
	
		
	
	
	

}




$qr_usuario  = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_usuario = mysql_fetch_assoc($qr_usuario); 

$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_usuario[id_master]' ");
$row_master = mysql_fetch_assoc($qr_master);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>::Intranet::</title>
<script src="jquery/jquery-1.4.2.min.js"></script>
<script src="jquery/base64.js"></script>
<style>

.ok{
background-color:#090;
font-weight:bold;
color:#FFF;	
padding:2px;
}
.email_incorreto{
background-color: #FF5B5B;
font-weight:bold;
color:#FFF;	
padding:2px;
}
</style>
</head>

<body>
<form name="form" action="cadastro_email.php" method="post"  id="form" >
<table align="center" style="background-color:#EEE">
	<tr>
    <td  align="center" colspan="2"><span style="color:#000;font-size:14px;"><?php echo $menssagem[$msg]?></span></td>
    </tr>
    <tr>
    	<td colspan="2" style="color:#FF6A6A" align="center"><?php if(isset($erro)) echo $erro; ?></td>
    </tr>
    
	<tr>
    
        <td align="right">E-mail:</td>
        <td><input type="text" name="email" value="" size="16" id="email" autocomplete="off"/>
	        <input type="hidden" name="dominio_email"  id="dominio_email"  value="<?php echo $row_master['dominio_email']; ?>"/>
	        <span style="font-style:italic;color:#666;">@<?php echo $row_master['dominio_email']?></span></td>
  </tr>
    <tr>
    	<td align="right"> Senha: </td>
    	<td> <input  type="password" name="senha" id="senha" /> </td>
    </tr>
    <tr>
    	<td colspan="2"><div class="menssagem"></div></td>
    </tr>
    <tr align="center">
    	
    	<td colspan="2">
        <input type="hidden" name="master_id"  id="master_id"  value="<?php echo $row_master['id_master']; ?>"/>
        <input type="submit" name="enviar"  value="Enviar"  id="enviar" class="enviar"/>
        
        </td>
    </tr>
</table>
</form>
</body>
</html>
