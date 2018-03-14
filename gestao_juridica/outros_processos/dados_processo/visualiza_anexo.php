<?php
include ("../../include/restricoes.php");
include('../../../conn.php');

$id_andamento = $_GET['id_andamento'];
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>


<table align="center">
	<tr>
		<td align="center">
<?php
if(isset($_GET['id_andamento'])) {
	$id_andamento = mysql_real_escape_string($_GET['id_andamento']);
	$status_id = mysql_real_escape_string($_GET['status_id']);
	
	
	$qr_anexo = mysql_query("SELECT * FROM proc_andamento_anexo WHERE andamento_id = '$id_andamento' AND andamento_anexo_status = 1;") or die(mysql_error());
	$row_anexo = mysql_fetch_assoc($qr_anexo);
	
	echo '<img src="anexos/'.$row_anexo['andamento_anexo_nome'].$row_anexo['andamento_anexo_ext'].'" width="50%" height="50%"/>';
	
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
	
	echo '<img src="movimentos_anexos/'.$row_anexo['proc_trab_mov_nome'].$row_anexo['proc_trab_mov_extensao'].'" width="50%" height="50%"/>';
	
	?>
			</td>
		</tr>
	</table>

<?php
}

?>	


</body>
</html>
