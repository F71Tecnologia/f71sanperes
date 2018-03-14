<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include "../include/criptografia.php";

$id_anexo = mysql_real_escape_string($_GET['id']);
$id_notificacao = mysql_real_escape_string($_GET['noti_id']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Anexos da Notificação</title>

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


$id_pg_anterior = @mysql_result(mysql_query("SELECT anexo_id FROM notificacao_anexos WHERE  anexo_extensao != 'pdf' AND notificacao_id = '$id_notificacao' AND anexo_status = 1 AND anexo_id < '$id_anexo' ORDER BY anexo_ordem DESC;"),0);

$qr_total = ( mysql_query("SELECT * FROM notificacao_anexos WHERE anexo_extensao != 'pdf' AND notificacao_id = '$id_notificacao' AND  anexo_status = 1 ORDER BY anexo_ordem ASC;"));


while($row_total = mysql_fetch_assoc($qr_total)) { 
$i++;

	if($i == 1 and $id_pg_anterior != 0){
		echo '<a href="visualizar_anexo.php?id='.$id_pg_anterior.'&noti_id='.$id_notificacao.'" class="pg"> << Anterior </a>';
		
	}
	
	
		
		
		if($prox_pg == 1){			
			$proximo = '<a href="visualizar_anexo.php?id='.$row_total['anexo_id'].'&noti_id='.$id_notificacao.'" class="pg"> Próximo >> </a>';
			$prox_pg = 0;
		}
		
		if($id_anexo == $row_total['anexo_id']){
			$prox_pg = 1;
			$color	 = 'color:#F7F7F7; font-size:22px;';
				
		} else {
			$color='';	
		}
	
	
	echo '<a href="visualizar_anexo.php?id='.$row_total['anexo_id'].'&noti_id='.$id_notificacao.'" class="pg"><span style="'.$color.'">'.$i.'</span></a>';
	
	
	
}

echo $proximo;
?>

<table align="center">
	<tr>	
    	<td>
        <?php
		$qr_anexo = mysql_query("SELECT * FROM notificacao_anexos WHERE anexo_id='$id_anexo' AND anexo_status = 1;") or die(mysql_error());
		$row_anexo = mysql_fetch_assoc($qr_anexo);
		
		echo '<img src="anexo_notificacoes/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="60%" height="60%"/>';
		?>
        </td>
       </tr>
   </table>
</body>
</html>
