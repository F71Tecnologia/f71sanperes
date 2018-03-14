<?
	////////////////////////////////
	//REGISTRO DE TRAILER DO LOTE//
	//////////////////////////////

	$BANCO = '341';
	$LOTE_SERVICO = '0001';
	$TIPO_REGISTRO = '5';	
	//??BRANCO
	$numSequenciaCorrente = $numSequenciaCorrente+1;
	$QUANT_REGISTROS = $numSequenciaCorrente+1;
	$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
	$VALOR =array_sum($arrayValorTotalCorrente);
	$VALOR = number_format($VALOR, 2, ".","");
	$remover = array(".", "-", "/",",");
	$VALORF  = str_replace($remover, "", $VALOR);	
	$VALOR = sprintf("%018d",$VALORF);
	$ZERO = '000000000000000000';

	$OCORRENCIAS = ' ';
	$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);	
	
	$handle = fopen('BANCOS/ITAU/CONTA_CORRENTE/'.$CONSTANTE.'_'.'CORRENTE'."_".$DD."_".$MM."_".$ANO.".txt", "a");
	fwrite($handle, $BANCO , 3);	
	fwrite($handle, $LOTE_SERVICO , 4);	
	fwrite($handle, $TIPO_REGISTRO , 1);
	$BRANCO= ' ';
	$BRANCO = sprintf("% 9s",$BRANCO);	
	fwrite($handle, $BRANCO , 9);
	fwrite($handle, $QUANT_REGISTROS , 6);
	fwrite($handle, $VALOR , 18);	
	fwrite($handle, $ZERO , 18);
	$BRANCO= ' ';
	$BRANCO = sprintf("% 171s",$BRANCO);	
	fwrite($handle, $BRANCO , 171);
	fwrite($handle, $OCORRENCIAS , 10);
	fwrite($handle, "\r\n");		
	
	/////////////////////////////////////
	// REGISTRO DE TRAILER DO ARQUIVO //
	///////////////////////////////////
	
	//CONTROLE
	$BANCO = '341';
	$LOTE_SERVICO = '9999';
	$TIPO_REGISTRO = '9';
	
	//TOTAIS
	$QUANT_LOTES = '000001';
	
	$numSequenciaCorrente = $numSequenciaCorrente+1;
	$QUANT_REGISTROS = $numSequenciaCorrente;
	$QUANT_REGISTROS = $QUANT_REGISTROS+2;
	$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
	
	$handle = fopen('BANCOS/ITAU/CONTA_CORRENTE/'.$CONSTANTE.'_'.'CORRENTE'."_".$DD."_".$MM."_".$ANO.".txt", "a");
	fwrite($handle, $BANCO , 3);	
	fwrite($handle, $LOTE_SERVICO , 4);	
	fwrite($handle, $TIPO_REGISTRO , 1);
	$BRANCO = ' ';
	$BRANCO = sprintf("% 9s",$BRANCO);	
	fwrite($handle, $BRANCO , 9);
	fwrite($handle, $QUANT_LOTES , 6);	
	fwrite($handle, $QUANT_REGISTROS , 6);	
	$BRANCO = ' ';
	$BRANCO = sprintf("% 211s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
	fwrite($handle, $BRANCO , 211);
	fwrite($handle, "\r\n");
	$BRANCO = ' ';
	$BRANCO = sprintf("% 29s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
	fwrite($handle, $BRANCO , 29);

?>