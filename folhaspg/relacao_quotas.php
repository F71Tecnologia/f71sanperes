<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relação de Quotas</title>
</head>
<body>
<?php
// Arquivo Conexão
include('../conn.php');
include('../funcoes.php');

// Cooperado
$cooperado = $_REQUEST['id'];

// Array Meses
$meses = array('Erro','Janeiro','Fevereiro','MarÇo','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$qr  = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$cooperado'");
$row = mysql_fetch_array($qr);
?>
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center" style="text-align:center; background-color:#ddd;">
  <tr>
    <th colspan="3" align="center"><?=$row['nome']?></td>
  </tr>
  <tr>
    <th width="28%">PARCELA</th>
    <th width="38%">M&Ecirc;S</th>
    <th width="34%">VALOR</th>
  </tr>
<?php $qr_total_pago = mysql_query("SELECT SUM(a.quota) FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'");
	  $total_pago	= @mysql_result($qr_total_pago,0);

	  $qr_quotas = mysql_query("SELECT a.quota, date_format(b.data_inicio, '%m') AS mes FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3' ORDER BY b.data_inicio ASC");
	  while($row_quotas = mysql_fetch_assoc($qr_quotas)) {
		  
		  if($cor++%2==0) {
			  $fundo_cor = '#fafafa'; 
		  } else {
			  $fundo_cor = '#f3f3f3';
		  }
		  
		  $parcela++;
		  
		  echo '<tr style="background-color:'.$fundo_cor.'; text-align:center;">
					<td>'.$parcela.'</td>
					<td>'.strtoupper($meses[(int)$row_quotas['mes']]).'</td>
					<td>R$ '.number_format($row_quotas['quota'],2,',','.').'</td>		
				</tr>';
	  } ?>
  <tr style="font-weight:bold;">
    <td>COTA: <?=number_format($row['cota'],2,',','.')?></td>
    <td align="right">TOTAL PAGO:</td>
    <td align="center">R$ <?=number_format($total_pago,2,',','.')?></td>
  </tr>
</table>
</body>
</html>