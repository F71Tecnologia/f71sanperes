<?php  
include ("../include/restricoes.php");
include "../../conn.php";
header('Content-Type: text/html; charset=iso-8859-1');
$id = $_GET['ID'];
$tablela = $_GET['tipo'];
$query = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y - %h:%m:%s') as data_proc FROM $tablela WHERE id_$tablela =  '$id'");
$row = mysql_fetch_assoc($query);
$query_funcionario = mysql_query("SELECT id_funcionario,nome FROM funcionario WHERE id_funcionario = '$row[id_user]'");
$query_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]'");
$row_banco_saida = mysql_fetch_assoc($query_banco);
$query_tipo = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_assoc($query_tipo);

switch($row_tipo['grupo']){
	case 1: $grupo = "Folha";
	case 2: $grupo = "Reserva";
	case 3: $grupo = "Taxa administrativa";
	case 4: $grupo = "Transferência ISPV";
}


?>
<style type="text/css">
body, table{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
}
#titulo {
	padding:5px;
	background-color:#E5E5E5;
	font-weight:bold;
	font-style:italic;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<div id="titulo">
<?php echo 'Código: '.$id.' <br>Nome: '.$tablela. ' '. $row['nome'];?> 
</div>
<table width="100%" cellpadding="0" cellspacing="0">
 
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
	<tr>
      <td align="right" bgcolor="#F7F7F7">Grupo:</td>
      <td height="18"> <?=$grupo?></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td height="18">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Tipo:</td>
      <td height="18"> <?=$row_tipo['nome']?></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td height="18">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Especifica&ccedil;&atilde;o:</td>
      <td height="18">&nbsp;        <?=$row['especifica']?></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td height="15">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Criado por:</td>
      <td height="18">&nbsp;<?php echo '<strong>'. @mysql_result($query_funcionario,0).'</strong> - <strong>'.@mysql_result($query_funcionario,0,1).'</strong>'; ?></td>
    </tr>
    <tr>
    	<td width="124">&nbsp;</td>
    	<td width="587" height="15">&nbsp;</td>
    </tr>
    <tr>
      <td height="18" align="right" bgcolor="#F7F7F7">Data cria&ccedil;&atilde;o: </td>
      <td>&nbsp;        <?=$row['data_proc'];?></td>
    </tr>
    <tr>
    	<td height="15">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Banco: </td>
      <td>&nbsp;        <?= $row_banco_saida['id_banco'].' - '.$row_banco_saida['nome']?></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Agencia: </td>
      <td>&nbsp;        <?=$row_banco_saida['agencia']?></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right" bgcolor="#F7F7F7">Conta: </td>
      <td>&nbsp;        <?=$row_banco_saida['conta']?></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td align="right" bgcolor="#F7F7F7">Valor: </td>
      <td height="15" bgcolor="#FFFFFF">&nbsp;        <b><?php $total =  str_replace(",",'.',$row['valor']) +  str_replace(",",'.',$row['adcional']); echo 'R$ ', number_format($total,2,',','.');?></b></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    	<td height="20">&nbsp;</td>
    </tr>
</table>
