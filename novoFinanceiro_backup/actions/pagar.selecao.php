<?php 

include "../../conn.php";

$idtarefa = $_POST['idtarefa']; // 1 saida, 2 entrada
$id_user = $_POST['logado'];
$id = $_POST['saidas'];
$tabela = "saida";
if($idtarefa == 2){
	$tabela = "entrada";
	$id = $_POST['entradas'];
}



foreach($id as $saida):

$id_pro = $saida;
$data_hoje = date("Y-m-d");

//AQUI ELE VAI RODAR O PAGAMENTO DA SAÍDA

$result = mysql_query("SELECT * FROM $tabela where id_$tabela = '$saida'");
$row = mysql_fetch_array($result);

$regiao = $row['id_regiao'];


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
	mysql_query("UPDATE $tabela set status = '2', data_pg = '$data_hoje', id_userpg = '$id_user' where id_$tabela = '$saida'");
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


endforeach;
header("Location: ../../financeiro/novofinanceiro.php?regiao=".$regiao);
?>