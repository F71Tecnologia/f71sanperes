<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../funcoes.php');
include('../../../classes/formato_data.php');
include('../../../classes/abreviacao.php');


$id_clt = mysql_real_escape_string($_GET['clt']);
$id_projeto = mysql_real_escape_string($_GET['pro']);
$id_regiao = mysql_real_escape_string($_GET['id_reg']);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<style type="text/css">
table {
	font-size:12px;
	}
tr.titulo{
	background-color:#818181;
	color: #FFF;
	font-size:12px;
	text-align:center;
	font-weight:bold;
	
}
tr.linha_um{
	background-color:   #EEE;

}

tr.linha_dois{
	background-color:    #FBFBFB;

}
tr.titulo_pagina {
background-color: #D3A9A9;
color:#fff;
text-align:center;
font-weight:bold;
	

}
</style>
<body>
<table width="100%">
<tr class="titulo_pagina" >
	<td colspan="5">FÉRIAS</td>

</tr>


<tr class="titulo">
	<td>MÊS</td>
    <td>ANO</td>
    <td>DATA DE INÍCIO</td>
    <td>DATA DE TÉRMINO</td>
    <td>DATA DE RETORNO</td>
    
</tr>

<?php
$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY ano ASC");
while($row_ferias = mysql_fetch_assoc($qr_ferias)){
?>
<tr class="linha_<?php if(($i++ % 2) == 0) {echo 'um'; } else {echo 'dois';}?>">
	<td align="center"><?php echo $row_ferias['mes']; ?></td>
    <td align="center"><?php echo $row_ferias['ano']; ?></td>
    <td align="center"><?php echo formato_brasileiro($row_ferias['data_ini']); ?></td>
    <td align="center"><?php echo formato_brasileiro($row_ferias['data_fim']); ?></td>
    <td align="center"><?php echo formato_brasileiro($row_ferias['data_retorno']); ?></td>

</tr>

<?php
}
?>
</table>

</body>
</html>
