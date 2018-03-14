<?php 
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='../login.php'>Logar</a>";
	exit;
} else {
	include('../conn.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relatório Personalizado</title>
<link rel="shortcut icon" href="../favicon.ico">
<link rel="stylesheet" href="css/estrutura.css" type="text/css">
</head>
<body style="margin-top:30px; margin-bottom:30px;">
<table cellspacing="0" cellpadding="0" class="relacao" style="width:920px; border:0px; page-break-after:always;">
 <tr> 
    <td align="center">
    	<h1>Criando Relatório Personalizado </h1>  
    </td>
  </tr>
  <tr> 
    <td>

	</td>
  </tr>
</table>
</body>
</html>
<?php } ?>