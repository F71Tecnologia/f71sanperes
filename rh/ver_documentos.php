<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<head>
<style>

.imagem {
			width:250px;	 
			height:180px; 
			
			float:left;
			display:block;
			text-align:center;
			
}

</style>
</head>
<body>



<div style="width:500px;heigth:auto;display:block; background-color:#CCC;">
<h3>ANEXO(S) </h3>
<?php

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');

$id_clt = mysql_real_escape_string($_GET['clt']);
$id_upload = mysql_real_escape_string($_GET['id']);


 
$qr_anexo = mysql_query("SELECT * FROM documento_clt_anexo WHERE id_clt = '$id_clt' AND id_upload = '$id_upload' AND anexo_status = 1 ORDER BY ordem, anexo_extensao;") or die(mysql_error());
while($row_anexo = mysql_fetch_assoc($qr_anexo)):

switch($row_anexo['anexo_extensao']) {
	
	case 'pdf':  echo '<div class="imagem"> <a href="documentos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" target="_blank" >					
							<img src="../imagens/Acrobat1.png" width="200" height="150"/>
						</a></div>';
	break;
	
	
	case 'doc':  echo '<div class="imagem"> <a href="documentos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" target="_blank">					
									<img src="../imagens/word.jpg" width="200"	 height="150"/>
								</a></div>';
	break;
	


	default: echo '<div class="imagem"><a href="visualiza_documento.php?id_clt='.$id_clt.'&id_anexo='.$row_anexo['anexo_id'].'&id_documento='.$id_upload.'" target="_blank"><img src="documentos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="200"	 height="150" /></a><br>
		Página '.$row_anexo['ordem'].'  </div>';
	break;



}

endwhile;
?>
		
</div>
</body>
</html>