<?php 
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');

$id_documento = mysql_real_escape_string($_GET['id']);
$qr_anexo = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_documento_id = '$id_documento'") or die(mysql_error());
$row_anexo = mysql_fetch_assoc($qr_anexo);








?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<?php
switch($row_anexo['extensao_arquivo']){
	case '.jpg': echo '<a href="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'"  target="_blank">
						<img src="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'" width="200" height="320"/>
					  </a>';
		break;	
	case '.gif': echo '<a href="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'" target="_blank">
							<img src="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'" width="200" height="320"/> 
						</a>';
		break;	
		case '.png': echo '<a href="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'"  target="_blank">
								<img src="prestador_documentos/'.$row_anexo['nome_arquivo'].$row_anexo['extensao_arquivo'].'"  width="200" height="320"/> 
						   </a>';
		break;	
	
	}


?>
<body>
</body>
</html>
