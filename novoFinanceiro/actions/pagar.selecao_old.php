<?php 
include ("../include/restricoes.php");
include "../../conn.php";

/*
ESSE SCRIPT E BAGUNÇADO PORQUE APROVEITEI O CODIGO DE RAMON COM SEUS BACALHAUS E ETC... PRA  NUM DA MERDA DEPOIS.
*/

$id_user = $_COOKIE['logado'];

if(sizeof($_POST['saidas']) >0) {
	
	$id['saida'] = $_POST['saidas'];
	
} else {

	$id['entrada'] = $_POST['entradas'];
}


foreach($id as $tabela => $saida):

foreach($saida as $id_saida){
	$id_pro = $id_saida;
	$data_hoje = date("Y-m-d");
	
	//AQUI ELE VAI RODAR O PAGAMENTO DA SAÍDA
	
	$result = mysql_query("SELECT * FROM $tabela WHERE id_$tabela = '$id_saida'");
	$row = mysql_fetch_array($result);
	
	$regiao = $row['id_regiao'];
	
	
	$result_bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row[id_banco]'");
	$row_bancos = mysql_fetch_array($result_bancos);
	
	$valor = $row['valor'];
	$adicional = $row['adicional'];
	$valor_banco = $row_bancos['saldo'];
	
	$valor = str_replace(",", ".", $valor);
	$adicional = str_replace(",", ".", $adicional);
	$valor_banco = str_replace(",", ".", $valor_banco);
	
	$valor_final = $valor + $adicional;
	
	if($tabela == 'saida'){
		$saldo_banco_final = $valor_banco - $valor_final;
	}else{
		$saldo_banco_final = $valor_banco + $valor_final;
	}
	
	$valor_f = number_format($valor_final,2,",",".");
	$saldo_banco_final_f = number_format($saldo_banco_final,2,",",".");
	$saldo_banco_final_banco = number_format($saldo_banco_final,2,",","");
	
	
	if($row['status'] == "1"){
		mysql_query("UPDATE $tabela set status = '2', data_pg = '$data_hoje', id_userpg = '$id_user' , hora_pg = NOW() WHERE id_$tabela = '$id_saida'");
		mysql_query("UPDATE bancos set saldo = '$saldo_banco_final_banco' WHERE id_banco = '$row[id_banco]'");
		
		if($row['tipo'] == "66"){
		  mysql_query("UPDATE compra SET acompanhamento = '6' WHERE id_compra = '$row[id_compra]'");
		}
	}
	
	echo "
		Nº da $tabela: $row[0]
		Valor da Conta: R$ $valor
		Adicional: R$ $adicional
		Total a pagar: R$ $valor_f
		Valor no Banco: R$ $valor_banco
		Saldo atualizado do Banco: R$ $saldo_banco_final_f
		
		";

}
endforeach;

?>