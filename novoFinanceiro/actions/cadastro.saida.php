<?php 
	//--------------------------------------------------------------------||
	//- AQUI COMEÇA A RODAR A SEGUNDA PARTE.. ONDE CADASTRAREMOS A SAÍDA -||
	//- CASO SEJA 1 VAI CADASTRAR UMA SAÍDA, CASE SEJA 2 VAI CADASTAR UM -||
	//- NOVO TIPO DE SAÍDA												 -||
	//--------------------------------------------------------------------||
$id = $_REQUEST['id'];
//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);


switch($id){
	case 1:
//CADASTRANDO SAIDAS
$ultimo_id = $_REQUEST['Ultimo_ID'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$nome = $_REQUEST['nome'];
$especifica = $_REQUEST['especifica'];
$tipo = $_REQUEST['tipo'];
$adicional = $_REQUEST['adicional'];
$valor = $_REQUEST['valor'];
$data_credito = $_REQUEST['data_credito'];
$comprovante = $_REQUEST['comprovante'];
$data_proc = date('Y-m-d H:i:s');
$data_proc2 = date('Y-m-d');
$valor = str_replace(".","", $valor);
$adicional = str_replace(".","", $adicional);

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
		  mysql_query("UPDATE caixinha SET saldo = '$saldo_somado_caixinha' WHERE id_caixinha = '$row_saldo_verifica[0]'") or die(mysql_error());
	  } else {  // SE NÃO HOUVE SAÍDA DE CAIXA, ELE INSERE A 1ª SAÍDA DE CAIXA DESSA REGIÃO
	   mysql_query("INSERT INTO caixinha(id_projeto,id_regiao,saldo,id_banco) VALUES 
										('$projeto','$regiao','$valor_adicional','$banco')") 
	   or die ("$mensagem_erro<br><br>".mysql_error());
	  } // AQUI TERMINA SE JA HOUVE OU NÃO SAÍDA DE CAIXA

	// INSERE SAÍDA!
	mysql_query("UPDATE saida SET  id_regiao = '$regiao' ,id_projeto = '$projeto'  ,id_banco = '$banco' ,id_user = '$id_user' ,nome = '$nome' ,especifica = '$especifica' ,tipo = '$tipo',adicional = '$adicional',valor = '$valor' ,data_proc = '$data_proc',data_vencimento = '$data_proc2',status = '2' WHERE id_saida = '$ultimo_id' LIMIT 1;") 
	or die ("$mensagem_erro<br><br>".mysql_error());
	
	
	mysql_query("UPDATE bancos SET saldo = '$sobra' WHERE id_banco = '$banco'") or die(mysql_error()); 
	
	exit;
}
// AKI TERMINA TUDO QUE FOR REFERENTE A CAIXINHA
mysql_query("UPDATE saida SET  id_regiao = '$regiao', id_projeto =  '$projeto', id_banco =  '$banco', 
			id_user = '$id_user', nome = '$nome', especifica = '$especifica', tipo = '$tipo', adicional = '$adicional',
			valor = '$valor', data_proc = '$data_proc', data_vencimento = '$data_credito2'
			WHERE id_saida = '$ultimo_id' LIMIT 1;") or die ("$mensagem_erro<br><br>".mysql_error());
// VERIFICANDO SE ESSA SAÍDA JA FOI CADASTRADA POR OUTRO USUÁRIO
$result_verifica = mysql_query("SELECT * FROM saida WHERE valor = '$valor' AND data_vencimento = '$data_credito2'") or die(mysql_error());
$row_num_verifica = mysql_num_rows($result_verifica);

/*
<script language= "JavaScript">

alert("Informações cadastradas com sucesso!");

opener.location.reload();

location.href="saidas.php?regiao=<?=$regiao?>&insert=true";

</script>

*/
break;
	case 2:
	// CADASTRANDO TIPOS DE ENTRADAS E SAIDAS
	// QUANDO O TIPO FOR 0(ZERO) SERÁ SAÍDA / SE FOR 1 SERÁ ENTRADA
	$tipo = $_REQUEST['tipo'];
	$regiao = $_REQUEST['regiao'];
	$nome = $_REQUEST['nome'];
	$descricao = $_REQUEST['descricao'];
	mysql_query("INSERT INTO entradaesaida(nome,descricao,tipo) VALUES ('$nome','$descricao','$tipo')") 
	or die ("$mensagem_erro<br><br>".mysql_error());
	if($tipo == 0) {
		$link = "saidas.php?regiao=$regiao?insert=true";
	} else {
		$link = "entradas.php?regiao=$regiao?insert=true";
	}
	print "
	<script>
	alert(\"Informações cadastradas com sucesso!\");
	opener.location.reload();
	location.href=\"$link\"
	</script>";
	break;
} // FINALIZANDO CASE
?>
