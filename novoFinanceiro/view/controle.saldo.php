<?php 
include ("../include/restricoes.php");
include "../../conn.php";
// SAIDAS

$query_ultima = mysql_query("SELECT MAX(saida.id_saida) FROM bancos INNER JOIN saida ON saida.id_banco = bancos.id_banco WHERE bancos.id_banco = '$_GET[id_banco]' AND saida.status = '2'");
$ID = mysql_result($query_ultima,0);

$query_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$ID'");
$row_saida = mysql_fetch_assoc($query_saida);

$query_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_saida[id_userpg]'");
$nome_funcionario = @mysql_result($query_usuario,0);

$query_grupo = mysql_query("SELECT * FROM entradaesaida INNER JOIN entradaesaida_grupo ON entradaesaida.grupo = entradaesaida_grupo.id_grupo  WHERE entradaesaida.id_entradasaida = '$row_saida[tipo]'");
$row_grupo = mysql_fetch_assoc($query_grupo);

// ENTRADAS

$query_ultima_entrada = mysql_query("SELECT MAX(entrada.id_entrada) FROM bancos INNER JOIN entrada ON entrada.id_banco = bancos.id_banco WHERE bancos.id_banco = '$_GET[id_banco]' AND entrada.status = '2'");
$ID_entrada = mysql_result($query_ultima_entrada,0);

$query_entrada = mysql_query("SELECT * FROM entrada WHERE id_entrada = '$ID_entrada'");
$row_entrada = mysql_fetch_assoc($query_entrada);


$query_usuario_entrada = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_entrada[id_userpg]'");
$nome_funcionario_entrada = @mysql_result($query_usuario_entrada,0);

$query_grupo_entrada = mysql_query("SELECT * FROM entradaesaida  WHERE id_entradasaida = '$row_entrada[tipo]'");
$row_grupo_entrada = mysql_fetch_assoc($query_grupo_entrada);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Ultima atualiza&ccedil;&atilde;o</title>
<style type="text/css">
p {
	text-align: center;
	color: #F40000;
	font-weight: bold;
	font-size: x-small;
}
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
span.titulos {
	font-size: 13px;
	font-weight: bold;
}
span {
	font-style: italic;
}
</style>
</head>

<body>
<table width="506" border="0" align="center" cellpadding="2" cellspacing="4">
  <tr>
    <td colspan="2" align="center"><span class="titulos">Ultimas atualiza&ccedil;&otilde;es</span></td>
  </tr>
    <?php if($row_saida['data_pg'] != date("Y-m-d")):?>
  <tr>
    <td colspan="2"><p>N&atilde;o &agrave; saidas confirmadas hoje <?=date("d/m/Y");?></p></td>
  </tr>
  <?php endif;?>

  <tr>
  	<td colspan="2" align="center"><span>Sa&iacute;das</span></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Saida:</td>
    <td><?=$ID?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Nome:</td>
    <td><?=$row_saida['id_nome']." - ".$row_saida['nome'];?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Grupo:</td>
    <td><?=$row_grupo['id_grupo']." - ".$row_grupo['nome_grupo']?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Tipo:</td>
    <td><?=$row_grupo['id_entradasaida']." - ".$row_grupo['nome']?></td>
  </tr>
  <tr>
    <td width="82" align="right" bgcolor="#F3F3F3">Usu&aacute;rio:</td>
    <td width="249"><?=$row_saida['id_userpg'].' - '.$nome_funcionario?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Data:</td>
    <td><?=implode("/",array_reverse(explode("-",$row_saida['data_pg'])))?></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><span>Entradas</span></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Entrada:</td>
    <td><?=$ID_entrada?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Nome: </td>
     <td><?=$row_entrada['nome'];?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Tipo: </td>
    <td><?=$row_grupo_entrada['id_entradasaida']." - ".$row_grupo_entrada['nome']?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Usu&aacute;rio</td>
    <td width="249"><?=$row_entrada['id_userpg'].' - '.$nome_funcionario_entrada?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#F3F3F3">Data: </td>
    <td><?=implode("/",array_reverse(explode("-",$row_entrada['data_pg'])))?></td>
  </tr>
</table>
</body>
</html>