<?php
	  include('include/restricoes.php');
	  include('../conn.php');
	  include("../classes/m2brimagem.class.php");
	  $projeto = $_GET['id_projeto'];
	  $excluir = $_GET['excluir'];
	  
	
	  
	  if($excluir) {
		  $qr_anexo  = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_id = '".$excluir."'");
		  $row_anexo = mysql_fetch_assoc($qr_anexo);
		  mysql_query("DELETE FROM projeto_anexos WHERE anexo_id = '".$excluir."' LIMIT 1");
		  unlink('../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao']);
		  unset($qr_anexos, $total_anexos);
	  } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Anexos do Projeto</title>
<style type="text/css">
body {
	margin:0; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	font-size:12px;
	text-align:center;
}
img {
	border:0;
}
h3{
	
	color:#FFF; 
	text-transform:uppercase;
	font-size:14px;
	text-align:center;
	background-color:#C58B8B;
	padding:5px;
	
		
	}
.documento {
	float:left; margin:0 20px 20px 0;
	font-size:12px;
	background-color:#F7F7F7;
	padding:5px;
	text-align:center;
}
</style>
</head>
<body>
<div class="documento" style="font-size:12px">

<?php
	echo htmlspecialchars(stripslashes($row_projeto['descricao']));
?>
 </div>
 
 <?php

 echo '<hr style="clear:both; border:2px solid #CCC;">';
 
 

//Mostra os anexos
	
$tipos = array( 4 =>'Termo de Rescisão', 5 =>'Publicação do Termo de Rescisão');

$array_imagem_ext = array('jpg','gif', 'png', 'jpeg');
foreach($tipos as $tipo =>$nome){
	
			
							
							echo '<h3>'.$nome.'</h3>';
							$qr_anexos    = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$projeto' AND anexo_tipo = '$tipo' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
								  $total_anexos = mysql_num_rows($qr_anexos);
								  
								  if(!empty($total_anexos)) {
									  
										while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
											
											
											if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
					
														echo '<div class="documento"><br><a target="_blank" href="../../projeto/anexos/exibir_anexos2.php?pro='.$row_anexo['anexo_projeto'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../classes/img.php?foto=../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
															
													} elseif($row_anexo['anexo_extensao'] == 'pdf'){
														
													echo '<div class="documento"><br><a target="_blank" href="../../projeto/anexos/'.$row_anexo['anexo_nome'].'.pdf"><img src="../../img_menu_principal/pdf.png" width="200" height="150" border="0" /></a><br></div> </div>';
													
														
													}
									
										}
								  
								  } else {
									  
									  echo 'Sem anexos do '.$nome;
									  
								  }
								  
								  unset($qr_anexos, $total_anexos);
								  
								  echo '<hr style="clear:both; border:2px solid #CCC;">';
}



	  
?>	  


      
</body>
</html>