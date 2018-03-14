<?php
include('conn.php');
$id_projeto=$_GET['projeto'];
$id_regiao=$_GET['regiao'];
$id_clt=$_GET['id'];





?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="adm/css/estrutura.css" rel="stylesheet" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>


<table class="relacao">
<tr>


<td>Evento</td>
<td>Data</td>
<td>Data de retorno</td>
<td>Dias</td>
</tr>
<?php


$qr_historico_eventos=mysql_query("SELECT * FROM rh_eventos WHERE id_clt = '$id_clt' AND id_regiao = '$id_regiao' AND id_projeto = '$id_projeto' AND status = '1' ")or die (mysql_error());

$qr_historico_ferias=mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' AND regiao = '$id_regiao' AND projeto = '$id_projeto' AND status = '1' ")or die(mysql_error());

$qr_historico_rescisao=mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id_clt' AND id_regiao = '$id_regiao' AND id_projeto = '$id_projeto' AND status = '1' ")or die(mysql_error());
$qr_historico_clt=mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt' AND id_regiao = '$id_regiao' AND id_projeto = '$id_projeto' AND status!=0")or die(mysql_error());

settype($historico_nome,'array');
settype($historico_inicio,'array');
settype($historico_fim,'array');
settype($historico_duracao,'array');




while($row_clt=mysql_fetch_assoc($qr_historico_clt)):

	$historico_nome[] = 'ADMISSÃO';
	$historico_inicio[] = $row_clt['data_entrada'];
	$historico_fim[] = '';
	$historico_duracao[] ='';


endwhile;	




while($row_evento=mysql_fetch_assoc($qr_historico_eventos)):

	$historico_nome[]=$row_evento['nome_status'];
	$historico_inicio[]=$row_evento['data'];
	$historico_fim[]=$row_evento['data_retorno'];
	$historico_duracao[]=$row_evento['dias'];


endwhile;




while($row_ferias=mysql_fetch_assoc($qr_historico_ferias)):

	$historico_nome[] = 'FÉRIAS';
	$historico_inicio[] = $row_ferias['data_ini'];
	$historico_fim[] = $row_ferias['data_fim'];
	$historico_duracao[] =($row_ferias['data_fim']- $row_ferias['data_ini']);


endwhile;		
	


while($row_recisao=mysql_fetch_assoc($qr_historico_rescisao)):

	$historico_nome[] = 'RESCISÃO';
	$historico_inicio[] = $row_recisao['data_demi'];
	$historico_fim[] = '';
	$historico_duracao[] ='';


endwhile;		
	

array_multisort($historico_inicio,$historico_fim,$historico_duracao,$historico_nome);

foreach($historico_inicio as $chave=> $inicio) {
	?>
	
	<tr>
    	<td><?php echo $historico_nome[$chave]; ?></td>
        <td><?php echo $historico_inicio[$chave]; ?></td>
        <td><?php echo $historico_fim[$chave];?></td>
        <td><?php echo $historico_duracao[$chave];?></td>
	</tr>
	
	<?php
	}


?>



</table>



</body>
</html>
