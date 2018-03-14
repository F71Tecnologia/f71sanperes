<?php 
require_once("../../conn.php");
// busca clt
$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$_GET[ID]'");
$row_clt = mysql_fetch_assoc($qr_clt);
// busca etnia
$qr_etnia 	= mysql_query("SELECT nome FROM etnias WHERE id = '$row_clt[etnia]'");
$etinia = @mysql_result($qr_etnia,0);
// buscando a escolatiadade
$qr_escolaridade = mysql_query("SELECT nome FROM escolaridade WHERE id = '$row_clt[escolaridade]'");
$escolaridade = @mysql_result($qr_escolaridade,0);
// buscando salario
$qr_curso = mysql_query("SELECT salario,cbo_codigo FROM curso WHERE id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_assoc($qr_curso);
// buscando o horario
$qr_horario = mysql_query("SELECT horas_mes FROM rh_horarios WHERE id_horario = '$row_clt[rh_horario]'");
$total_mes = mysql_fetch_assoc($qr_horario);
$total_mes = $total_mes['horas_mes'];
$horas_semanal = ceil($total_mes/4);
if($horas_semanal > 44){
	$horas_semanal = 44;
}
// buscando o status
$status_demi = array('60','61','62','81','100','80','63');
$datas_demi = array();
if(in_array($row_clt['status'],$status_demi)){
	$datas_demi['Data de admissão'] = $row_clt['data_entrada'];
	$datas_demi['Data de demissão'] = $row_clt['data_demi'];
}else{
	$datas_demi['Data de admissão'] = $row_clt['data_entrada'];
}
// verificando deficiencia
$deficiencia = array();
if(!empty($row_clt['deficiencia'])){
	$qr_deficiencia = mysql_query("SELECT nome FROM deficiencias WHERE id = '$row_clt[deficiencia]'");
	$deficiencia['Tipo de deficiência'] = @mysql_result($qr_deficiencia,0);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Visualizar cadastro</title>
<style type="text/css">
body{
	margin:0px;
	font-family:Verdana, Geneva, sans-serif;
	background-color: #F3F3F3;
}
</style>
<link href="css/estilo_visu.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="base">
<table width="100%">
	<tr>
    	<td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	  <td width="33%" align="right">Nome:</td>
	  <td width="63%"><?=$row_clt['nome']?></td>
	  <td width="4%">&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Sexo:</td>
	  <td><?=$row_clt['sexo']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Etinia:</td>
	  <td><?=$etinia?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Grau de instrução:</td>
	  <td><?=$escolaridad?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Nascimento:</td>
	  <td><?=$row_clt['data_nasci']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr align="right">
	  <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">PIS:</td>
	  <td><?=$row_clt['pis']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">CPF:</td>
	  <td><?=$row_clt['cpf']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Carteira de trabalho:</td>
	  <td><?= $row_clt['campo1'].'/'.$row_clt['serie_ctps'].'-'.$row_clt['uf_ctps']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Salario:</td>
	  <td><?=$row_curso['salario']?></td>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="right">Horas trabalhadas:</td>
	  <td><?=$horas_semanal.'hs/semana'?></td>
	  <td>&nbsp;</td>
    </tr>
	<?php 
		foreach($datas_demi as $nome =>$data){
			echo '<tr>';
			echo '<td>'.$nome.'</td>';
			echo '<td>'.$data.'</td>';
			echo '<td></td>';
			echo '<tr>';
		}
	?>
	<tr>
	  <td align="right">&nbsp;</td>
	  <td>&nbsp;</td>
	  <td>&nbsp;</td>
    </tr>
	<?php 
		foreach($deficiencia as $nome => $tipo){
			echo '<tr>';
			echo '<td>'.$nome.'</td>';
			echo '<td>'.$tipo.'</td>';
			echo '<td></td>';
			echo '<tr>';
			
		}
	?>
</table>
</div>
</body>
</html>