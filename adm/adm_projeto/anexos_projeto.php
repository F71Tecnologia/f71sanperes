<?php
	  include('include/restricoes.php');
	  include('../../conn.php');
	  include("../../classes/m2brimagem.class.php");
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

//Descri��o dpo projeto
$array_imagem_ext = array('jpg','gif', 'png', 'jpeg');
$qr_projeto = mysql_query("SELECT descricao,status_reg FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);
echo '<h3>Objeto do contrato</h3>';
?>
 <div class="documento" style="font-size:12px">

<?php
	echo htmlspecialchars(stripslashes($row_projeto['descricao']));
?>
 </div>
 
 <?php

 echo '<hr style="clear:both; border:2px solid #CCC;">';
 
 

//Mostra os anexos
	
$tipos = array(1 =>'Proposta de parceria', 2 =>'Contratos de Gest�o', 3 =>'Proposta de trabalho', 4 =>'Termo de Rescis�o', 5 =>'Publica��o do Termo de Rescis�o');

foreach($tipos as $tipo =>$nome){
	
			if($row_projeto['status_reg'] == 1)
			{
				if($tipo == 4 or $tipo == 5) continue;	
			}

							
							echo '<h3>'.$nome.'</h3>';
							$qr_anexos    = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$projeto' AND anexo_tipo = '$tipo' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
								  $total_anexos = mysql_num_rows($qr_anexos);
								  
								  if(!empty($total_anexos)) {
									  
										while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
											
											
											if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
					
														echo '<div class="documento"><br><a target="_blank" href="../../projeto/anexos/exibir_anexos2.php?pro='.$row_anexo['anexo_projeto'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['anexo_tipo'].'"><img src="../../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="200" height="150" border="0" /></a><br><b>P�gina</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
															
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