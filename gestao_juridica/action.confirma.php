<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";


$id_notificacao = $_GET['id_noti'];

if(isset($_POST['confirmar'])){

$data_entrega = implode('-',array_reverse(explode('/', $_POST['data_entrega'])));
$id_notificacao = $_POST['id_notificacao'];

$update = mysql_query("UPDATE notificacoes SET notificacao_entregue = '1',
												notificacoes_dt_entrega= '$data_entrega',
												notificacoes_funcionario_entrega = '$_COOKIE[logado]'
											 WHERE notificacao_id = '$id_notificacao' ") or die(mysql_error());


if($update){

echo 'Confirmação concluída!';
echo '<script type="text/javascript">
		location.href = "index.php";

</script>';
	exit();
}


}

$qr_notificacoes = mysql_query("SELECT * FROM notificacoes WHERE notificacao_id = '$id_notificacao' ");
$row_notificacoes =  mysql_fetch_assoc($qr_notificacoes);


$qr_tipo = mysql_query("SELECT * FROM tipos_notificacoes WHERE tipos_notificacoes_id = '$row_notificacoes[tipos_notificacoes_id]'");
$row_tipos  = mysql_fetch_assoc($qr_tipo);


//nome regiao
if($row_notificacoes['id_regiao'] == 'todos') {
	$nome_regiao =  $row_notificacoes['id_regiao'];
} else {
	$nome_regiao  = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_notificacoes[id_regiao]' "),0);
	
}

//nome projeto	
if($row_notificacoes['id_projeto'] == 'todos') {
	$nome_projeto =  $row_notificacoes['id_projeto'];
} else {
	$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_notificacoes[id_projeto]' "),0);	
	
}


?>

<html xmlns="undefined">
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">

<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<title>CARTA DE REFER&Ecirc;NCIA</title>
<script type="text/javascript">
$(function(){

$('#data_entrega').mask('99/99/9999');


	
});


</script>
<style>
<!--
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{font-size:8.0pt;
	font-family:"Tahoma","sans-serif";}
body {
	margin-left: 5px;
	margin-top: 0px;
	margin-right: 5px;
	margin-bottom: 0px;
}
.style9 {font-family: Arial, Helvetica, sans-serif}
.style12 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 10px;
}
-->
</style>
<link href="../net1.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#FFFFFF" lang=PT-BR>
<form action="" name="form" method="post">
<table  align="center" bgcolor="#FFFFFF" class="bordaescura1px" style="padding:20px;">

<tr>
	<td colspan="2" align="center"><h3><?php echo $row_tipos['tipos_notificacoes_nome'].' - Nº:'.$row_notificacoes['notificacao_numero'];?></h3></td>
</tr>

<tr>
	<td colspan="2" align="center"><?php echo $nome_regiao.' - '.$nome_projeto;?></td>
</tr>
<tr>
	<td colspan="2" align="center">&nbsp;</td>
</tr>
 <tr>
 	<td align="right">Data de entrega:</td>
    <td align="left"><input type="text" name="data_entrega"  id="data_entrega" /></td>
 </tr>
 <tr>
 	<td colspan="2" align="center">
    <input name="id_notificacao" type="hidden" value="<?php echo $id_notificacao;?>"/>
    <input type="submit" name="confirmar" value="CONFIRMAR"/></td>
 </tr>
</table>
</form>
</body>
</html>