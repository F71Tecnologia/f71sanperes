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
<h3>ANEXO(S) DO(S) ANDAMENTO(S)</h3>
<?php

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../../../conn.php');

$id_andamento = mysql_real_escape_string($_GET['id_andamento']);
$status_id = mysql_real_escape_string($_GET['status_id']);

 
$qr_anexo = mysql_query("SELECT * FROM proc_andamento_anexo WHERE andamento_id = '$id_andamento' AND andamento_anexo_status = 1 ORDER BY andamento_anexo_ordem;") or die(mysql_error());
while($row_anexo = mysql_fetch_assoc($qr_anexo)):

switch($row_anexo['andamento_anexo_ext']) {
	
	case 'pdf':  echo '<div class="imagem"> <a href="anexos/'.$row_anexo['andamento_anexo_nome'].'.'.$row_anexo['andamento_anexo_ext'].'" target="_blank" >					
							<img src="../../../imagens/Acrobat1.png" width="200" height="150"/>
						</a></div>';
	break;
	
	
	case 'doc':  echo '<div class="imagem"> <a href="anexos/'.$row_anexo['andamento_anexo_nome'].'.'.$row_anexo['andamento_anexo_ext'].'" target="_blank">					
									<img src="../../../imagens/word.jpg" width="200"	 height="150"/>
								</a></div>';
	break;
	


	default: echo '<div class="imagem"><a href="visualiza_anexo.php?id_andamento='.$row_anexo['andamento_id'].'&id_anexo='.$row_anexo['andamento_anexo_id'].'" target="_blank"><img src="anexos/'.$row_anexo['andamento_anexo_nome'].'.'.$row_anexo['andamento_anexo_ext'].'" width="200"	 height="150" /></a><br>
		Página '.$row_anexo['andamento_anexo_ordem'].'  </div>';
	break;



}

endwhile;
?>
		
</div>
</body>
</html>