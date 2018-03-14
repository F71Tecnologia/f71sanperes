<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Administração de Notas Fiscais</title> 
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
<h3>ANEXO(S) DA NOTA</h3>
<?php

if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../../conn.php');

$id_nota = mysql_real_escape_string($_GET['id_nota']);
 
$qr_anexo = mysql_query("SELECT * FROM notas_files WHERE id_notas = '$id_nota' AND status = 1 ORDER BY ordem;") or die(mysql_error());
while($row_anexo = mysql_fetch_assoc($qr_anexo)):
if($row_anexo['id_file'] < 2217)
{
    $nome_arquivo = $row_anexo['id_file'];
}
else
{
    $nome_arquivo = $row_anexo['nome'];
}
switch($row_anexo['tipo']) {
	
	case 'pdf':  echo '<div class="imagem"> <a href="notas/'.$nome_arquivo.'.'.$row_anexo['tipo'].'" target="_blank" >					
							<img src="../../imagens/Acrobat1.png" width="200" height="150"/>
						</a></div>';
	break;
	
	
	case 'doc':  echo '<div class="imagem"> <a href="notas/'.$nome_arquivo.'.'.$row_anexo['tipo'].'" target="_blank">					
									<img src="../../imagens/word.jpg" width="200"	 height="150"/>
								</a></div>';
	break;
	


	default: echo '<div class="imagem"><a href="visualiza_anexo.php?id_nota='.$row_anexo['id_notas'].'&id_anexo='.$row_anexo['id_file'].'" target="_blank"><img src="notas/'.$nome_arquivo.'.'.$row_anexo['tipo'].'" width="200"	 height="150" /></a><br>
		Página '.$row_anexo['ordem'].'  </div>';
	break;



}

endwhile;
?>
		
</div>
</body>
</html>