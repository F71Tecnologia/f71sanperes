<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include "../include/criptografia.php";

$id_notificacao = mysql_real_escape_string($_GET['id_noti']);

$qr_anexo = mysql_query("SELECT * FROM notificacao_anexos WHERE notificacao_id = '$id_notificacao' AND anexo_status = 1");




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title>
<head>
<style>
.anexos{
width:200px;
height:150px;
float:left;
margin:5px;
border:1px solid #CCC;
text-align:center;
	
}
 
h3{
background-color: #E0E0E0;
color:#999;	
text-align:center;
	
}
</style>
</head>
<body>
<h3>ANEXO(S) DA NOTIFICAÇÃO</h3>
<?php
while($row_anexo = mysql_fetch_assoc($qr_anexo)):

switch ($row_anexo['anexo_extensao']){
	
	case 'pdf' :  echo '<div class="anexos">
				  		<a href="anexo_notificacoes/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'"><img src="../../imagens/Acrobat1.png" width="200" height="150"/>"
						</a>
						</div>
						';
			
   
   default : 	echo '<div class="anexos">
				  		<a href="visualizar_anexo.php?id='.$row_anexo['anexo_id'].'&noti_id='.$row_anexo['notificacao_id'].'" target="_blank"><img src="anexo_notificacoes/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="200" height="150"/>
						</a><br>
						Página '.$row_anexo['anexo_ordem'].'
						
						</div>
						';		
}






endwhile;
?>




</body>
</html>