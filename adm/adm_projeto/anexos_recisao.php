<?php include('../include/restricoes.php');
	  
	  $projeto = $_GET['id'];
	  $excluir = $_GET['excluir'];
	  
	  if($excluir) {
		  $qr_anexo  = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_id = '".$excluir."'");
		  $row_anexo = mysql_fetch_assoc($qr_anexo);
		  mysql_query("DELETE FROM projeto_anexos WHERE anexo_id = '".$excluir."' LIMIT 1");
		  unlink('../../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao']);
		  unset($qr_anexos, $total_anexos);
	  } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Anexos do Projeto</title>
<style type="text/css">
body {
	margin:0; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
}
img {
	border:0;
}
.documento {
	float:left; margin:0 20px 20px 0;
}
</style>
</head>
<body>


<?php 




//mostra o tipo 1

echo '<h3>Termo de Rescisão</h3>';
$qr_anexos    = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$projeto' AND anexo_tipo = '4' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
	  $total_anexos = mysql_num_rows($qr_anexos);
	  
		  
	  if(!empty($total_anexos)) {
		  
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
				
			echo '<div class="documento"><br><a target="_blank" href="../../projeto/anexos/exibir_anexos.php?id='.$row_anexo['anexo_projeto'].'&pg='.$row_anexo['anexo_ordem'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../classes/img.php?foto=../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
			}
	  
	  } else {
		  
		  echo 'Sem anexos do Termo de Rescisão';
		  
	  }
	  
	  unset($qr_anexos, $total_anexos);
	  
	  echo '<hr style="clear:both; border:2px solid #CCC;">';

//mostra o tipo 2

echo '<h3>Publicação do Termo de Rescisão</h3>';	  
	  $qr_anexos    = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$projeto' AND anexo_tipo = '5' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
	  $total_anexos = mysql_num_rows($qr_anexos);
	  
	  if(!empty($total_anexos)) {
		  
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
				
				echo '<div class="documento"><br><a target="_blank" href="../../projeto/anexos/exibir_anexos.php?id='.$row_anexo['anexo_projeto'].'&pg='.$row_anexo['anexo_ordem'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../classes/img.php?foto=../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
			
			
		
			
				
			}
	  
	  } else {
		  
			echo 'Sem anexos da Publicação do Termo de Rescisão';
		  
	  } 
      
      
       unset($qr_anexos, $total_anexos);
	  
	  echo '<hr style="clear:both; border:2px solid #CCC;">';
	  
  
 
	  
	  unset($qr_anexos, $total_anexos);
	
?>	  
      
</body>
</html>