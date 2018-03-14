<?php
	  include('../adm/include/restricoes.php');
	  include('../conn.php');
	  include("../classes/m2brimagem.class.php");
	  $id_compra = $_GET['compra'];	
	
 ?>
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
$qr_compra = mysql_query("SELECT * FROM compra WHERE id_compra = '$id_compra'");
$row_compra = mysql_fetch_assoc($qr_compra);

$nome_forne[1] = @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor1]'"),0);
$nome_forne[2]= @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor2]'"),0);
$nome_forne[3] = @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor3]'"),0);



//Mostra os anexos
	


foreach($nome_forne as $tipo =>$nome){
	
			
							
							echo '<h3>'.$nome.'</h3>';
							$qr_anexos    = mysql_query("SELECT * FROM anexo_compra WHERE id_compra  = '$id_compra' AND fornecedor = '$tipo' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
							 $total_anexos = mysql_num_rows($qr_anexos);
								  
								  if(!empty($total_anexos)) {
									  
										while($row_anexo = mysql_fetch_assoc($qr_anexos)) {
											
											
											if(in_array($row_anexo['anexo_extensao'],$array_imagem_ext)) {
					
														echo '<div class="documento"><br><a target="_blank" href="exibir_anexos.php?compra='.$row_anexo['id_compra'].'&id='.$row_anexo['anexo_id'].'&tp='.$row_anexo['fornecedor'].'"><img src="anexo_compras/'.$row_anexo['anexo_id'].'.'.$row_anexo['anexo_extensao'].'" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> </div>';
															
													} elseif($row_anexo['anexo_extensao'] == 'pdf'){
														
													echo '<div class="documento"><br><a target="_blank" href="anexo_compras/'.$row_anexo['anexo_id'].'.pdf"><img src="../img_menu_principal/pdf.png" width="200" height="150" border="0" /></a><br></div> </div>';
													
														
													}
									
										}
								  
								  } else {
									  
									  echo 'Sem anexos.';
									  
								  }
								  
								  unset($qr_anexos, $total_anexos);
								  
								  echo '<hr style="clear:both; border:2px solid #CCC;">';
}



	  
?>	  


      
</body>
</html>