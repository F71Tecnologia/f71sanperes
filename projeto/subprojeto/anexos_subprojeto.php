<?php 

include('include/restricoes.php');
include('../../conn.php');
	  
	  $id_subprojeto = $_GET['id'];
	  $excluir = $_GET['excluir'];
	  
	  if($excluir) {
		  $qr_anexo  = mysql_query("SELECT * FROM subprojeto_anexos WHERE anexo_id = '".$excluir."'");
		  $row_anexo = mysql_fetch_assoc($qr_anexo);
		  mysql_query("UPDATE subprojeto_anexos SET anexo_status=0 WHERE anexo_id='$excluir' LIMIT 1");
		  unlink('sub_anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao']);
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

<?php 
$array_imagem_ext = array('jpg','gif', 'png', 'jpeg');



$qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_subprojeto = '$id_subprojeto'");
$row_subprojeto = mysql_fetch_assoc($qr_subprojeto);

 if($row_subprojeto['tipo_contrato'] =='TERMO ADITIVO'){ echo '<h3>Motivo do aditivo</h3>'; } else {echo '<h3>Objeto do contrato</h3>';} ?>


<div class="documento"> 

<?php
	echo htmlspecialchars(stripslashes($row_subprojeto['descricao']));
?>
 </div>
 
 <?php

 echo '<center><hr style="clear:both; border:2px solid #CCC;"></center>';

$qr_subprojeto=mysql_query("Select tipo_subprojeto FROM subprojeto WHERE id_subprojeto='$id_subprojeto'");
$row_sub=mysql_fetch_assoc($qr_subprojeto);

$qr_anexos    = mysql_query("SELECT * FROM subprojeto_anexos WHERE anexo_projeto = '$id_subprojeto' AND anexo_tipo = '2' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
	  $total_anexos = mysql_num_rows($qr_anexos);
		echo '<h3>'.$row_sub['tipo_subprojeto'].'</h3>'; 
		
		 
	  if(!empty($total_anexos)) {
		  
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
				
				
				
				if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
					
					echo '<div class="documento"><br><a target="_blank" href="../../projeto/subprojeto/exibir_anexos_subprojeto2.php?subpro='.$row_anexo['anexo_projeto'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../classes/img.php?foto=../projeto/subprojeto/sub_anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
						
				} elseif($row_anexo['anexo_extensao'] == 'pdf'){
					
				echo '<div class="documento"><br><a target="_blank" href="sub_anexos/'.$row_anexo['anexo_nome'].'.pdf"><img src="../../img_menu_principal/pdf.png" width="200" height="150" border="0" /></a><br></div> </div>';
				
					
				}
				
				
			}
	  
	  } else {
		  
		  echo 'Sem anexos do '.$row_sub['tipo_subprojeto'];
		  
	  }
	  
	  unset($qr_anexos, $total_anexos);
	  
	  echo '<center><hr style="clear:both; border:2px solid #CCC;"></center>';
	  
	  $qr_anexos    = mysql_query("SELECT * FROM subprojeto_anexos WHERE anexo_projeto = '$id_subprojeto' AND anexo_tipo = '1' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
	  $total_anexos = mysql_num_rows($qr_anexos);
	  echo '<h3> Programa de trabalho</h3>'; 
	  if(!empty($total_anexos)) {
		  
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
				
			if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
				
				echo '<div class="documento"><br><a target="_blank" href="../../projeto/subprojeto/exibir_anexos_subprojeto2.php?subpro='.$row_anexo['anexo_projeto'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../classes/img.php?foto=../projeto/subprojeto/sub_anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
						
				} elseif($row_anexo['anexo_extensao'] == 'pdf'){
					
				echo '<div class="documento"><br><a target="_blank" href="sub_anexos/'.$row_anexo['anexo_nome'].'.pdf"><img src="../../img_menu_principal/pdf.png" width="200" height="150" border="0" /></a></div> </div>';
				
					
				}
			}
	  
	  } else {
		  echo 'Sem anexos do programa de trabalho';
		  
	  }
	  
	  
	  
	
	   echo '<center><hr style="clear:both; border:2px solid #CCC;"></center>';
	  
	  $qr_anexos    = mysql_query("SELECT * FROM subprojeto_anexos WHERE anexo_projeto = '$id_subprojeto' AND anexo_tipo = '3' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
	  $total_anexos = mysql_num_rows($qr_anexos);
	  echo '<h3> Proposta de Parceria</h3>'; 
	  if(!empty($total_anexos)) {
		  
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
				
			if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
					
				echo '<div class="documento"><br><a target="_blank" href="../../projeto/subprojeto/exibir_anexos_subprojeto2.php?subpro='.$row_anexo['anexo_projeto'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../classes/img.php?foto=../projeto/subprojeto/sub_anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
						
				} elseif($row_anexo['anexo_extensao'] == 'pdf'){
					
				echo '<div class="documento"><br><a target="_blank" href="sub_anexos/'.$row_anexo['anexo_nome'].'.pdf"><img src="../../img_menu_principal/pdf.png" width="200" height="150" border="0" /></a><br></div> </div>';
				
					
				}
			}
	  
	  } else {
		  echo 'Sem anexos do programa de trabalho';
		  
	  }
	  
	   ?>
</body>
</html>