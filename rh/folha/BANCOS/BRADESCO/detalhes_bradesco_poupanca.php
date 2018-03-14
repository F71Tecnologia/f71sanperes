<?
// ARQUIVO DE TRANZAÇÕES (DETALHES) DO BANCO BRADESCO

$COD_REGISTRO_TRANSACAO = '1';	//VALOR CONSTANTE
//RESERVA
$COD_AGENCIA_FUNCIONARIO = $row_clt['agencia'];
$COD_AGENCIA_FUNCIONARIO = sprintf("%05d",$COD_AGENCIA_FUNCIONARIO);
/*
$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO = '';
$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO = sprintf("%05d",$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO);
*/ 
$NUMERO_CONTA_FUNCIONARIO = $row_clt['conta'];
$NUMERO_CONTA_FUNCIONARIO = sprintf("%07d",$NUMERO_CONTA_FUNCIONARIO);		  
		  
$DIGITO_CONTA_FUNCIONARIO =trim($row_clt['conta']);
$str = $DIGITO_CONTA_FUNCIONARIO;
$ultimoDigitoConta = $str{strlen($str)-1};
$DIGITO_CONTA_FUNCIONARIO = $ultimoDigitoConta;		  
//RESERVA
		  
$NOME = strtoupper($row_clt['nome']);
$NOME = sprintf("% 38s",$NOME);
		  
$COD_FUNC = $row_clt['cod'];
$COD_FUNC = sprintf("%06d",$COD_FUNC);
		  
$VALOR = $row_clt['salliquido'];
$remover = array(".", "-", "/",",");
$VALOR = str_replace($remover, "", $VALOR);
$VALOR = sprintf("%013d",$VALOR);
		  
$COD_SERVICO = '298';	//VALOR CONSTANTE
//RESREVA
//RESERVA
$numeroSequencial = $numeroSequencial + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$numeroSequencial);
	  
$handle = fopen('BANCOS/BRADESCO/CONTA_POUPANCA/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt", "a");
fwrite($handle, $COD_REGISTRO_TRANSACAO, 1);
$RESERVA = ' ';
$RESERVA = sprintf("% 61s",$RESERVA);
fwrite($handle, $RESERVA, 61);
fwrite($handle, $COD_AGENCIA_FUNCIONARIO, 5);
fwrite($handle, $NUMERO_RAZAO_DA_CONTA_FUNCIONARIO, 5);		  
fwrite($handle, $NUMERO_CONTA_FUNCIONARIO, 7);
fwrite($handle, $DIGITO_CONTA_FUNCIONARIO, 1);
$RESERVA = ' ';
$RESERVA = sprintf("% 2s",$RESERVA);
fwrite($handle, $RESERVA, 2);
fwrite($handle, $NOME, 38);
fwrite($handle, $COD_FUNC, 6);
fwrite($handle, $VALOR, 13);
fwrite($handle, $COD_SERVICO, 3);
$RESERVA = ' ';
$RESERVA = sprintf("% 8s",$RESERVA);
fwrite($handle, $RESERVA, 8);
$RESERVA = ' ';
$RESERVA = sprintf("% 44s",$RESERVA);
fwrite($handle, $RESERVA, 44);
fwrite($handle, $NRO_SEQUENCIAL, 6);
fwrite($handle, "\r\n");
?>