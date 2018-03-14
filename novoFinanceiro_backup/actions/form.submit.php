<?php

include_once "../../conn.php";
/*print_r($_POST);*/
 
//--------------------------------------------------------------------||
//- AQUI COMEÇA A RODAR A SEGUNDA PARTE.. ONDE CADASTRAREMOS A SAÍDA -||
//- CASO SEJA 1 VAI CADASTRAR UMA SAÍDA, CASE SEJA 2 VAI CADASTAR UM -||
//- NOVO TIPO DE SAÍDA												 -||
//--------------------------------------------------------------------||

//CADASTRANDO SAIDAS

/*projeto
banco
grupo
tipo
nome
descricao
adicional
real
data*/

//id_saida	id_regiao	id_projeto	id_banco	id_user	nome	especifica	tipo	adicional	valor	data_proc	data_vencimento	data_pg	comprovante	tipo_arquivo	id_userpg	id_compra	campo3	status
$id_user = $_REQUEST['logado'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$nome = $_REQUEST['nome'];
$especifica = utf8_decode($_REQUEST['descricao']);
$tipo = $_REQUEST['tipo'];
$adicional = $_REQUEST['adicional'];
$valor = $_REQUEST['real'];
$data_credito = $_REQUEST['data'];
$data_proc = date('Y-m-d H:i:s');
$data_proc2 = date('Y-m-d');
$valor = str_replace(".","", $valor);
$adicional = str_replace(".","", $adicional);

$query_nomes = mysql_query("SELECT id_nome,nome FROM entradaesaida_nomes WHERE id_nome = '$nome'");
$row_nomes = mysql_fetch_assoc($query_nomes);
$nome = $row_nomes['nome'];
$id_nome = $row_nomes['id_nome'];
function ConverteData($Data){
	 if(strstr($Data, "/")) {
		 $rstData = implode('-', array_reverse(explode('/', $Data)));
		 return $rstData;
	 } elseif(strstr($Data, "-")) {
		$rstData = implode('/', array_reverse(explode('-', $Data)));
		return $rstData;
	 }
}
$data_credito2 = ConverteData($data_credito);
if($tipo == "19") { // VERIFICA SE É IGUAL A SAÍDA DE CAIXA
	$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
	$row_banco = mysql_fetch_array($result_banco);
	$saldo_atual = $row_banco['saldo'];
	$adicional = str_replace(",",".",$adicional);
	$valor = str_replace(",",".",$valor);
	$saldo_atual = str_replace(",",".",$saldo_atual);
	$valor_adicional = $adicional + $valor;
	$sobra = $saldo_atual - $valor_adicional;
	$adicional = number_format($adicional,2,",","");
	$valor = number_format($valor,2,",","");
	$valor_adicional = number_format($valor_adicional,2,",","");
	$sobra = number_format($sobra,2,",","");
	$verifica_caixinha = mysql_query("SELECT * FROM caixinha WHERE id_regiao = '$regiao'");
	$row_saldo_verifica = mysql_fetch_array($verifica_caixinha);
	$row_verifica = mysql_num_rows($verifica_caixinha);

	if(!empty($row_verifica)) {  // VERIFICA SE JA HOUVE SAÍDA DE CAIXA PARA REGIÃO SELECIONADA
		  $saldo_atual_caixinha = str_replace(",",".", $row_saldo_verifica['saldo']);
		  $valor_adicional_ff = str_replace(",",".", $valor_adicional);
		  $soma_do_caixinha = $saldo_atual_caixinha + $valor_adicional_ff;
		  $saldo_somado_caixinha = number_format($soma_do_caixinha,2,",","");
		  mysql_query("UPDATE caixinha SET saldo = '$saldo_somado_caixinha' WHERE id_caixinha = '$row_saldo_verifica[0]'") or die("Erro");
	  } else {  // SE NÃO HOUVE SAÍDA DE CAIXA, ELE INSERE A 1ª SAÍDA DE CAIXA DESSA REGIÃO
	   mysql_query("INSERT INTO caixinha(id_projeto,id_regiao,saldo,id_banco) VALUES 
										('$projeto','$regiao','$valor_adicional','$banco')") 
	   or die ("Erro");
	  } // AQUI TERMINA SE JA HOUVE OU NÃO SAÍDA DE CAIXA

	// INSERE SAÍDA!
	mysql_query("
	INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante)
	VALUES ('$regiao', '$projeto', '$banco', '$id_user', '$nome','$id_nome', '$especifica', '$tipo', '$adicional', '$valor','$data_proc', '$data_proc2',  '2', '0')") or die("Erro");
	
	echo mysql_insert_id();
	mysql_query("UPDATE bancos SET saldo = '$sobra' WHERE id_banco = '$banco'") or die(mysql_error());
	exit;
}
// AKI TERMINA TUDO QUE FOR REFERENTE A CAIXINHA

mysql_query("INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, comprovante) 
VALUES ('$regiao','$projeto','$banco','$id_user','$nome', '$id_nome','$especifica','$tipo','$adicional','$valor','$data_proc','$data_credito2', '0')")or die("Erro");
echo mysql_insert_id();
			
/* OBS
// VERIFICANDO SE ESSA SAÍDA JA FOI CADASTRADA POR OUTRO USUÁRIO
$result_verifica = mysql_query("SELECT * FROM saida WHERE valor = '$valor' AND data_vencimento = '$data_credito2'") or die(mysql_error());
$row_num_verifica = mysql_num_rows($result_verifica);
*/

/*
<script language= "JavaScript">

alert("Informações cadastradas com sucesso!");

opener.location.reload();

location.href="saidas.php?regiao=<?=$regiao?>&insert=true";

</script>

*/
?>