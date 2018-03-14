<?php 
include ("../include/restricoes.php");
include "../../conn.php";



foreach($_POST['saidas'] as $saida):

$tipo = $_REQUEST['tipo'];
$id_pro = $saida;
$id_user = $_COOKIE['logado'];
$idtarefa = $_REQUEST['idtarefa'];// entrada 2 , saida 1

$data_hoje = date("Y-m-d");

if($tipo == "pagar"):
//AQUI ELE VAI RODAR O PAGAMENTO DA SAÍDA

$result = mysql_query("SELECT * FROM saida where id_saida = '$saida'");
$row = mysql_fetch_array($result);

$result_bancos = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]'");
$row_bancos = mysql_fetch_array($result_bancos);

$valor = $row['valor'];
$adicional = $row['adicional'];
$valor_banco = $row_bancos['saldo'];

$valor = str_replace(",", ".", $valor);
$adicional = str_replace(",", ".", $adicional);
$valor_banco = str_replace(",", ".", $valor_banco);

$valor_final = $valor + $adicional;

if($idtarefa == "1"){
	$saldo_banco_final = $valor_banco - $valor_final;
}else{
	$saldo_banco_final = $valor_banco + $valor_final;
}

$valor_f = number_format($valor_final,2,",",".");
$saldo_banco_final_f = number_format($saldo_banco_final,2,",",".");
$saldo_banco_final_banco = number_format($saldo_banco_final,2,",","");


if($row['status'] == "1"){
	mysql_query("UPDATE saida set status = '2', data_pg = '$data_hoje', id_userpg = '$id_user' where id_saida = '$saida'");
	mysql_query("UPDATE bancos set saldo = '$saldo_banco_final_banco' where id_banco = '$row[id_banco]'");
	
	if($row['tipo'] == "66"){
	  mysql_query("UPDATE compra SET acompanhamento = '6' where id_compra = '$row[id_compra]'");
	}
}
/*
echo "	<br><center>
	<br>Valor da Conta: R$ $valor
	<br>Adicional: R$ $adicional
	<br>Total a pagar: R$ $valor_f
	<br>Valor no Banco: R$ $valor_banco
	<br>Saldo atualizado do Banco: R$ $saldo_banco_final_f
	<br><br><a href='financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a></center>";*/

else
	//AQUI ELE VAI DELETAR A SAÍDA
	mysql_query("UPDATE saida set status = '0' where id_saida = '$id_pro'");
	/*print "<br><br><center>Registro deletado com sucesso!<br><br>
	<a href='financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a></center>";*/
endif;

endforeach;

?>