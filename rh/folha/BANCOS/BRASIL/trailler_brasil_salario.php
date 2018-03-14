<?
	////////////////////////////////
	//REGISTRO DE TRAILER DO LOTE//
	//////////////////////////////
	$handle = fopen('BANCOS/BRASIL/CONTA_SALARIO/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");
	$BANCO = '001';
	fwrite($handle, $BANCO,3);					
	
	$LOTE_SERVICO = '0001';
	fwrite($handle, $LOTE_SERVICO,4);
		
	$TIPO_REGISTRO = '5';
	fwrite($handle, $TIPO_REGISTRO,1);

	$BRANCO = ' ';
	$BRANCO  = sprintf("% 9s" , $BRANCO );
	fwrite($handle, $BRANCO, 9);
				
	$numSequencia = $numSequencia+1;
	$QUANT_REGISTROS = $numSequencia + 1;
	$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
	fwrite($handle, $QUANT_REGISTROS, 6);
		
	$VALOR =array_sum($arrayValorTotalSalario);
	$VALOR = number_format($VALOR, 2, ".","");
	$remover = array(".", "-", "/",",");
	$VALORF  = str_replace($remover, "", $VALOR);	
	$VALOR = sprintf("%018d",$VALORF);
	fwrite($handle, $VALOR, 18);
	
	$QUANTIDADE_MOEDA = '000000000000000000'; 
	fwrite($handle, $QUANTIDADE_MOEDA, 18);
	
	$BRANCO = ' ';
	$BRANCO = sprintf("% 171s",$BRANCO);	
	fwrite($handle, $BRANCO , 171);

	$OCORRENCIAS = ' ';
	$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);	
	fwrite($handle, $OCORRENCIAS , 10);
	fwrite($handle, "\r\n");
	
	/////////////////////////////////////
	// REGISTRO DE TRAILER DO ARQUIVO //
	///////////////////////////////////
	$BANCO = '001';
	fwrite($handle, $BANCO,3);					
	
	$LOTE_SERVICO = '9999';
	fwrite($handle, $LOTE_SERVICO,4);
		
	$TIPO_REGISTRO = '9';
	fwrite($handle, $TIPO_REGISTRO,1);

	$BRANCO = ' ';
	$BRANCO  = sprintf("% 9s" , $BRANCO );
	fwrite($handle, $BRANCO, 9);
	
	$QUANT_LOTES = '000001';
	fwrite($handle, $QUANT_LOTES, 6);	
	
	$numSequencia = $numSequencia+1;
	$QUANT_REGISTROS = $numSequencia;
	$QUANT_REGISTROS = $QUANT_REGISTROS+2;
	$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);	
	fwrite($handle, $QUANT_REGISTROS, 6);	
	
	$QUANT_CONTAS_CONCILIADAS = '000000';
	fwrite($handle, $QUANT_CONTAS_CONCILIADAS, 6);
	
	$BRANCO = ' ';
	$BRANCO  = sprintf("% 205s" , $BRANCO );
	fwrite($handle, $BRANCO, 205);
	fwrite($handle, "\r\n");
?>