<?php


include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');




$id_oscip=$_GET['id'];
$tipo=$_GET['tp'];


$qr_oscip=mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_oscip='$id_oscip'");
$row_oscip=mysql_fetch_array($qr_oscip);


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Untitled Document</title>
<style>

body {
	margin:0; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
}
img {
	border:0;
}
.documento {
	float:left; margin:0 20px 20px 0;

}

h3{
	
	color:#FFF; 
	text-transform:uppercase;
	font-size:14px;
	text-align:center;
	background-color:#C58B8B;
	padding:5px;
	margin:0;
	
		
	}
.documento {
	float:left; margin:0 20px 20px 0;
	font-size:12px;
	background-color:#F7F7F7;
	padding:5px;
	text-align:center;
}
.gerar {
text-decoration:none;
background-color: #EBEBEB;
padding:3px;
color: #000;
float:right;
border: solid 1px #000;
}
.gerar:hover {
text-decoration:underline;	
}
</style>


</head>

<body>



<?php
//pdf
$qr_anexos=mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='$id_oscip' AND extensao='pdf' AND status=1 ORDER BY anexo_ordem ASC");
$total=mysql_num_rows($qr_anexos);




if(!empty($total)){
	
			
	
		echo '<h3>Arquivos em PDF</h3>';
					
					while($row_pdf=mysql_fetch_assoc($qr_anexos)):
					
					echo ' <div class="documento" style="text-align:center;font-size:13px;color:#F90;font-weigth:bold;" class="documento"><br><a target="_blank" href="anexos_oscip/'.$row_pdf['id_anexo'].'.'.$row_pdf['extensao'].'"><img src="../../imagens/Acrobat1.png" width="50" height="50"alt="Visualizar documento"/></a><br> ' ;
					
					if($row_pdf['tipo_anexo']==1)
					
					{echo 'Publicação';
					
					
					} else
					{ 
										 
					 if($row_oscip['tipo_oscip'] == 'Documentos da diretoria'){ echo '<br>'.$row_pdf['nome_diretoria'];} else{
					 echo $row_oscip['tipo_oscip'];
					 
					 }
						
					
					}
					
					
					echo'</div> ';	
					
					
					endwhile;
	echo '<hr style="clear:both; border:2px solid #CCC;">';				
		}
		


	 
	




 
	  

	
	  
?>


<?php

//publicacao
$qr_anexos=mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='$id_oscip' AND tipo_anexo='1' AND status=1  AND extensao != 'pdf' ORDER BY anexo_ordem ASC");
$total_anexo=mysql_num_rows($qr_anexos);


echo '<br><h3>Publicações </h3>';
if(!empty($total_anexo) )
			{
			
		
					
				while($row_anexo=mysql_fetch_assoc($qr_anexos)) {
			
							echo ' <div class="documento"><br><a target="_blank" href="../../adm/adm_contratos/paginas_oscip.php?m='.$link_master.'&id='.$row_anexo['id_oscip'].'&pg='.$row_anexo['anexo_ordem'].'&tp='.$row_anexo['tipo_anexo'].'"><img src="../../classes/img.php?foto=../adm/adm_contratos/anexos_oscip/'.$row_anexo['id_anexo'].'.'.$row_anexo['extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a><br><b>Página</b> '.$row_anexo['anexo_ordem'].'</div> ';			
							
							
					}
					
						echo '<div style="width:100% heigth:30px;;clear:left;"><a href="gerar_pdf.php?id='.$id_oscip.'&tipo=1" class="gerar" target="_blank">GERAR PDF </a></div>';	
				  
					} else {
					  
					  echo 'Sem anexos da	 publicação';
					  
				  }
				  
				  unset($qr_anexos, $total_anexos);
				  
				  echo '<hr style="clear:both; border:2px solid #CCC;">';
				  
	  
	  
//documento	  
	  
$qr_anexos=mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='$id_oscip' AND tipo_anexo='2'  AND   status=1 AND extensao != 'pdf'ORDER BY anexo_ordem ASC");
$total_anexos=mysql_num_rows($qr_anexos);



 if(!empty($total_anexos)){
	 
		  
		  echo '<h3>'.$row_oscip['tipo_oscip'].'</h3>';
		  
			
			
			while($row_anexo = mysql_fetch_assoc($qr_anexos)) :
				
				
				echo '<div class="documento"><br><a target="_blank" href="../../adm/adm_contratos/paginas_oscip.php?m='.$link_master.'&id='.$row_anexo['id_oscip'].'&pg='.$row_anexo['anexo_ordem'].'&tp='.$row_anexo['tipo_anexo'].'"><img src="../../classes/img.php?foto=../adm/adm_contratos/anexos_oscip/'.$row_anexo['id_anexo'].'.'.$row_anexo['extensao'].'&w=200&h=150" width="200" height="150" border="0" /></a>';
				
				
			
				
			if($row_oscip['tipo_oscip'] == 'Documentos da diretoria'){ echo '<br>'.$row_anexo['nome_diretoria'];}					 
			
			echo '<br><b>Página</b> '.$row_anexo['anexo_ordem'];
				
				echo '</div>';
				
			endwhile;
			
			
			echo '<div style="width:100% heigth:30px;;clear:left;"><a href="gerar_pdf.php?id='.$id_oscip.'&tipo=2" class="gerar" target="_blank">GERAR PDF </a></div>';	
				  	
			}  else {
		  
			echo 'Sem anexos dos documentos';
		  
	  }

?> 
</body>
</html>
