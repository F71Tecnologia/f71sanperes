<?php
include('../include/restricoes.php');
include('../../../conn.php');

$projeto = $_POST['projeto'];
$master  = $_POST['master'];
$ano	 = $_POST['ano'];

// Consulta do Projeto
$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

// Consulta de Região
$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

// Consulta da Empresa
$qr_empresa  = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$row_projeto[id_regiao]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$empresa   = explode('-', $row_projeto['local']);
$municipio = $empresa[0];
$estado    = $empresa[1];

// Consulta do Master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$master'");
$row_master = mysql_fetch_assoc($qr_master);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Extrato de Execu&ccedil;&atilde;o F&iacute;sico Financeiro</title>
</head>
<body style="text-align:center; margin:0; background-color:#efefef; font-family:Arial, Helvetica, sans-serif; font-size:13px;">
<table style="margin:50px auto; width:460px; border:1px solid #222; text-align:left; padding:10px; background-color:#fff;">
  <tr style="background-color:#ccc;">
    <td colspan="3" align="center">EXTRATO DE EXECU&Ccedil;&Atilde;O F&Iacute;SICO FINANCEIRO</td>
  </tr>
  <tr>
    <td width="149" valign="top"><p align="center">MUNIC&Iacute;PIO:</p></td>
    <td width="427" valign="top"><p align="center"><?php echo $municipio; ?></p></td>
  </tr>
  <tr>
    <td width="149" valign="top"><p align="center">ESTADO:</p></td>
    <td width="427" valign="top"><p align="center"><?php echo $estado; ?></p></td>
  </tr>
  <tr>
    <td width="149" valign="top"><p align="center">PROJETO:</p></td>
    <td width="427" valign="top"><p align="center"><?php echo $row_projeto['nome']; ?></p></td>
  </tr>
  <tr style="background-color:#ccc;">
    <td width="192" valign="top"><p align="center">M&Ecirc;S DE COMPET&Ecirc;NCIA</p></td>
    <td width="192" valign="top"><p align="center">VALOR REPASSE</p></td>
    <td width="192" valign="top"><p align="center">N&ordm; NF</p></td>
  </tr>
  <?php $meses = array('01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro', '13' => 'Dezembro 13º');
  		foreach($meses as $mes => $nome_mes) {
			
		    $qr_repasse  = mysql_query("SELECT * FROM entrada WHERE tipo = '12' AND id_projeto = '$projeto' AND month(data_pg) = '$mes' AND year(data_pg) = '$ano'");
			$row_repasse = mysql_fetch_assoc($qr_repasse);
			$total_repasse += str_replace(',', '.', $row_repasse['valor']); ?>

          <tr>
            <td width="192" valign="top"><p align="center"><?php echo $nome_mes.' '.$ano; ?></p></td>
            <td width="192" valign="top"><p align="center"><?php echo number_format(str_replace(',', '.', $row_repasse['valor']),'2',',','.'); ?></p></td>
            <td width="192" valign="top"><p align="center">&nbsp;</p></td>
          </tr>

  <?php } ?>
  <tr style="background-color:#ccc;">
    <td width="192" valign="top"><p align="center">TOTAL DO EXERC&Iacute;CIO</p></td>
    <td width="192" valign="top"><p align="center"><?php echo number_format($total_repasse,'2',',','.'); ?></p></td>
    <td width="192" valign="top"><p align="center">&nbsp;</p></td>
  </tr>
</table>
</body>
</html>