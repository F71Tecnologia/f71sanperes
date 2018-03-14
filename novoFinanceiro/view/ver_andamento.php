<?php
include ("../include/restricoes.php");
include('../../conn.php');

$id_andamento = $_GET['id_andamento'];
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

$id_pg_anterior = @mysql_result(mysql_query("SELECT andamento_anexo_id FROM proc_andamento_anexo WHERE andamento_id = '$id_andamento' AND andamento_anexo_ext != 'pdf' AND  andamento_anexo_status = 1 AND andamento_anexo_id < '$id_anexo' ORDER BY andamento_anexo_ordem DESC;"),0);

$qr_total = ( mysql_query("SELECT * FROM proc_andamento_anexo WHERE andamento_id = '$id_andamento' AND andamento_anexo_ext != 'pdf' AND  andamento_anexo_status = 1 ORDER BY andamento_anexo_ordem ASC;"));
while($row_total = mysql_fetch_assoc($qr_total)) { 
$i++;

	if($i == 1 and $id_pg_anterior != 0){
	echo '<a href="ver_andamento.php?id_anexo='.$id_pg_anterior.'&id_andamento='.$id_andamento.'" class="pg"> << Anterior </a>';
		
	}
	
	
		
		
		if($prox_pg == 1){			
			$proximo = '<a href="ver_andamento.php?id_anexo='.$row_total['andamento_anexo_id'].'&id_andamento='.$id_andamento.'" class="pg"> Próximo >> </a>';
			$prox_pg = 0;
		}
		
		if($id_anexo == $row_total['andamento_anexo_id']){
			$prox_pg = 1;
			$color	 = 'color:#F7F7F7; font-size:22px;';
				
		} else {
			$color='';	
		}
	
	
	echo '<a href="ver_andamento.php?id_anexo='.$row_total['andamento_anexo_id'].'&id_andamento='.$id_andamento.'" class="pg"><span style="'.$color.'">'.$i.'</span></a>';
	
	
	
}

echo $proximo;
?>

</div>

<table align="center">
	<tr>
		<td align="center">
<?php
if(isset($_GET['id_andamento'])) {
	$id_andamento = mysql_real_escape_string($_GET['id_andamento']);
	$status_id = mysql_real_escape_string($_GET['status_id']);
	
	
	$qr_anexo = mysql_query("SELECT * FROM proc_andamento_anexo WHERE andamento_id = '$id_andamento' AND andamento_anexo_id='$id_anexo' AND  andamento_anexo_status = 1;") or die(mysql_error());
	$row_anexo = mysql_fetch_assoc($qr_anexo);
	
	echo '<img src="../../gestao_juridica/processo_trabalhista/dados_trabalhador/anexos//'.$row_anexo['andamento_anexo_nome'].'.'.$row_anexo['andamento_anexo_ext'].'" width="60%" height="60%"/>';
	
	?>
    
			</td>
		</tr>
	</table>
<?php
}

if(isset($_GET['id_movimento'])) {

	$id_movimento = mysql_real_escape_string($_GET['id_movimento']);
	$qr_anexo = mysql_query("SELECT * FROM proc_trab_mov_anexos WHERE proc_trab_mov_id = '$id_movimento' AND proc_trab_mov_status = 1;") or die(mysql_error());
	$row_anexo = mysql_fetch_assoc($qr_anexo);
	
	echo '<img src="movimentos_anexos/'.$row_anexo['proc_trab_mov_nome'].'.'.$row_anexo['proc_trab_mov_extensao'].'" width="60%" height="60%"/>';
	
	?>
			</td>
		</tr>
	</table>

<?php
}

?>	


</body>
</html>
