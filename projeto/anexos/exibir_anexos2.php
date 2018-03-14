<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

include('../../funcoes.php');
//include('../../adm/include/criptografia.php');


$id_projeto = $_GET['pro'];	
$id_anexo 	=  $_GET['id'];	
$tipo 		= $_GET['tp'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Untitled Document</title>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>

body{
	background-color:#E0E0E0;	
}


#paginacao{
	margin:0;
	width:100%;
	height: 30px;
	text-align:center;	
	background-color: #333;
	padding-top:10px;
}

.pg{

margin-left:5px;
text-decoration:none;
font-size:18px;
color:#CCC;
font-weight:bold;
width:30px;
height:auto;
border:1px transparent solid; 

}

.pg:hover{
text-decoration:underline;	
color: #FFF;
border:1px #FFF solid; 
}
</style>


</head>


<body>
<div id="paginacao">
<?php 
$prox_pag = 0;
$i =0;




////ANEXO ANTERIOR
$id_pg_anterior = @mysql_result(mysql_query("SELECT anexo_id FROM projeto_anexos WHERE anexo_projeto = '$id_projeto'   AND  anexo_status = 1 AND anexo_id < '$id_anexo'  AND anexo_tipo='$tipo' ORDER BY anexo_ordem DESC;"),0);

if($id_pg_anterior != 0){
			echo '<a href="exibir_anexos2.php?id='.$id_pg_anterior.'&tp='.$tipo.'&pro='.$id_projeto.'" class="pg"> << Anterior  </a>';	
	}
///////////////////	




///PEGANDO  O TOTAL DE ANEXOS
$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto='$id_projeto'  AND anexo_tipo='$tipo' AND anexo_status='1' ");
while($row_anexo = mysql_fetch_assoc($qr_anexos)):

$i++;



if($prox_pg == 1){			
			$proximo = '<a href="exibir_anexos2.php?id='.$row_anexo['anexo_id'].'&tp='.$tipo.'&pro='.$id_projeto.'" class="pg"> Próximo >> </a>';
			$prox_pg = 0;
		}
		
		
		
		if($id_anexo == $row_anexo['anexo_id']){
			$prox_pg = 1;
			$color_num_pagina	 = 'color:#F7F7F7; font-size:22px;';
			$imagem  = '<img  width="672" height="950" src="../../projeto/anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'"/>';
				
		} else {
			$color_num_pagina='';	
		}



	echo '<a href="exibir_anexos2.php?id='.$row_anexo['anexo_id'].'&tp='.$tipo.'&pro='.$id_projeto.'" class="pg"> <span style="'.$color_num_pagina.'">'.$i.'</span></a>';
	

endwhile;

echo $proximo;
?>

<table align="center">
	<tr>
		<td align="center">
        <?php echo $imagem;?>
        </td>
</tr>
</table>


?>
</body>
</html>	