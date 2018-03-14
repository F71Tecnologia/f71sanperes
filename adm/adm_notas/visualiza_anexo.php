<?php
include ("../include/restricoes.php");
include('../../conn.php');

$id_nota = $_GET['id_nota'];
$id_anexo = $_GET['id_anexo'];
?>


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
$prox_pg =0;
$i = 0;

$id_pg_anterior = @mysql_result(mysql_query("SELECT id_file FROM notas_files WHERE id_notas = '$id_nota' AND tipo != 'pdf' AND  status = 1 AND id_file < '$id_anexo' ORDER BY ordem DESC;"),0);

$qr_total = ( mysql_query("SELECT * FROM notas_files WHERE id_notas = '$id_nota' AND tipo != 'pdf' AND  status = 1 ORDER BY ordem ASC;"));
while($row_total = mysql_fetch_assoc($qr_total)) { 
$i++;

	if($i == 1 and $id_pg_anterior != 0){
	echo '<a href="visualiza_anexo.php?id_anexo='.$id_pg_anterior.'&id_nota='.$id_nota.'" class="pg"> << Anterior </a>';
		
	}
	
	
		
		
		if($prox_pg == 1){			
			$proximo = '<a href="visualiza_anexo.php?id_anexo='.$row_total['id_file'].'&id_nota='.$id_nota.'" class="pg"> Próximo >> </a>';
			$prox_pg = 0;
		}
		
		if($id_anexo == $row_total['id_file']){
			$prox_pg = 1;
			$color	 = 'color:#F7F7F7; font-size:22px;';
				
		} else {
			$color='';	
		}
	
	
	echo '<a href="visualiza_anexo.php?id_anexo='.$row_total['id_file'].'&id_nota='.$id_nota.'" class="pg"><span style="'.$color.'">'.$i.'</span></a>';
	
	
	
}

echo $proximo;
?>

</div>

<table align="center">
	<tr>
		<td align="center">
<?php
if(isset($_GET['id_nota'])) {
	$id_andamento = mysql_real_escape_string($_GET['id_andamento']);
	$status_id = mysql_real_escape_string($_GET['status_id']);
	
	
	$qr_anexo = mysql_query("SELECT * FROM notas_files WHERE id_notas = '$id_nota' AND id_file='$id_anexo' AND  status = 1;") or die(mysql_error());
	$row_anexo = mysql_fetch_assoc($qr_anexo);
	
	echo '<img src="notas/'.$row_anexo['id_file'].'.'.$row_anexo['tipo'].'" width="60%" height="60%"/>';
	
	?>
    
			</td>
		</tr>
	</table>
<?php
}



?>	


</body>
</html>
