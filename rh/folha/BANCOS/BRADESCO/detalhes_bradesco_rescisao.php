<?php
///////////////////////////////////////////////////////////
//	ARQUIVO DE TRANZAÇÕES (DETALHES) DO BANCO BRADESCO	//
/////////////////////////////////////////////////////////

$COD_REGISTRO_TRANSACAO = '1';	//VALOR CONSTANTE
//RESERVA (61 BRANCOS)

$COD_AGENCIA_FUNCIONARIO = $row_clt['agencia'];
$COD_AGENCIA_FUNCIONARIO = substr($COD_AGENCIA_FUNCIONARIO, 0, 4);
$COD_AGENCIA_FUNCIONARIO = sprintf("%05d",$COD_AGENCIA_FUNCIONARIO);

//CODIGO DA RAZAO PARA CORRENTISTAS
$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO = '0730'; //CONTA SALÁRIO CODIGO 07.30 COM ZERO ALINHADO A DIREITA tirei o 8
$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO = sprintf("%05d",$NUMERO_RAZAO_DA_CONTA_FUNCIONARIO);

$NUMERO_CONTA_FUNCIONARIO = str_replace(' ','',str_replace('.','',str_replace('-','',$row_clt['conta'])));
$NUMERO_CONTA_FUNCIONARIO = sprintf("%08d",$NUMERO_CONTA_FUNCIONARIO);
$NUMERO_CONTA_FUNCIONARIO = substr($NUMERO_CONTA_FUNCIONARIO, 0, 7);
$NUMERO_CONTA_FUNCIONARIO = sprintf("%07d",$NUMERO_CONTA_FUNCIONARIO);

$DIGITO_CONTA_FUNCIONARIO = $row_clt['conta'];
$str = $DIGITO_CONTA_FUNCIONARIO;
$ultimoDigitoConta = $str{strlen($str)-1};
$DIGITO_CONTA_FUNCIONARIO = $ultimoDigitoConta;
$DIGITO_CONTA_FUNCIONARIO = sprintf("% -1s",$DIGITO_CONTA_FUNCIONARIO);
//RESERVA (2 BRANCOS)
		  
$NOME = strtoupper($row_clt['nome']);
$NOME = sprintf("% -38s",$NOME);
//$COD_FUNC = $row_clt['cod'];
$COD_FUNC = $row_clt['campo3'];
$COD_FUNC = sprintf("%06d",$COD_FUNC);
		  
//$VALOR_PAGAMENTO  = $row_clt['salliquido'];
$VALOR_PAGAMENTO  = $row_clt['total_liquido'];
$this->arrayValorTotal[$row_clt[tipo_conta][0]][] = $VALOR_PAGAMENTO;
$remover = array(".", "-", "/",",");
$VALOR_PAGAMENTOF  = str_replace($remover, "", $VALOR_PAGAMENTO );
$VALOR  = sprintf("%013d" ,$VALOR_PAGAMENTOF );				

		  
$COD_SERVICO = '298';	//VALOR CONSTANTE
//RESREVA (8 BRANCOS)
//RESERVA (44 BRANCOS)

$this->numeroSequencial[$row_clt[tipo_conta][0]] = $this->numeroSequencial[$row_clt[tipo_conta][0]] + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$this->numeroSequencial[$row_clt[tipo_conta][0]]);

if($row_clt[tipo_conta] == 'corrente'){
    $handle = fopen("../folha/BANCOS/BRADESCO/{$this->CAMINHO}_C.txt", "a");
}else if($row_clt[tipo_conta] == 'salario'){
    $handle = fopen("../folha/BANCOS/BRADESCO/{$this->CAMINHO}_S.txt", "a");
}
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