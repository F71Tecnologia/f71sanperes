<?php
include ("include/restricoes.php");
include('../conn.php');

$id_documento = $_GET['id_documento'];
$id_anexo = $_GET['id_anexo'];
$id_clt = $_GET['id_clt'];

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

$id_pg_anterior = @mysql_result(mysql_query("SELECT anexo_id FROM documento_clt_anexo  WHERE id_upload = '$id_documento' AND anexo_extensao != 'pdf' AND  anexo_status = 1 AND anexo_id < '$id_anexo' ORDER BY ordem DESC;"),0);

$qr_total =  mysql_query("SELECT * FROM documento_clt_anexo WHERE  id_upload = '$id_documento' AND anexo_extensao != 'pdf' AND  id_clt = '$id_clt' AND anexo_status = 1 ORDER BY ordem ASC;");
while($row_total = mysql_fetch_assoc($qr_total)) { 
$i++;

	if($i == 1 and $id_pg_anterior != 0){
	echo '<a href="visualiza_documento.php?id_anexo='.$id_pg_anterior.'&id_documento='.$id_documento.'&id_clt='.$id_clt.'" class="pg"> << Anterior </a>';
		
	}
	
	
		
		
		if($prox_pg == 1){			
			$proximo = '<a href="visualiza_documento.php?id_anexo='.$row_total['anexo_id'].'&id_documento='.$id_documento.'&id_clt='.$id_clt.'" class="pg"> Próximo >> </a>';
			$prox_pg = 0;
		}
		
		if($id_anexo == $row_total['anexo_id']){
			$prox_pg = 1;
			$color	 = 'color:#F7F7F7; font-size:22px;';
				
		} else {
			$color='';	
		}
	
	
	echo '<a href="visualiza_documento.php?id_anexo='.$row_total['anexo_id'].'&id_documento='.$id_documento.'&id_clt='.$id_clt.'" class="pg"><span style="'.$color.'">'.$i.'</span></a>';
	
	
	
}

echo $proximo;
?>

</div>

<table align="center">
	<tr>
		<td align="center">
<?php
if(isset($_GET['id_anexo'])) {
	
	
	
	
	$qr_anexo = mysql_query("SELECT * FROM documento_clt_anexo WHERE anexo_id = '$id_anexo' AND anexo_status = 1 AND  id_clt = '$id_clt';") or die(mysql_error());
	$row_anexo = mysql_fetch_assoc($qr_anexo);
	
	echo '<img src="documentos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="60%" height="60%"/>';
	
	?>
    
			</td>
		</tr>
	</table>
<?php
}
?>


</body>
</html>
