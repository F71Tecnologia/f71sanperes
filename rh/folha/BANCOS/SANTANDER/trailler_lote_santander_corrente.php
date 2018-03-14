<?
	////////////////////////////////
	//REGISTRO DE TRAILER DO LOTE//
	//////////////////////////////
	/* CONTROLE */
	$BANCO = '033';
	$LOTE_SERVICO = '0001';
	$TIPO_REGISTRO = '5';	
	//??CNAB
	
	$numSequenciaCorrente = $numSequenciaCorrente+1;
	$QUANT_REGISTROS = $numSequenciaCorrente;
	
	$numSequenciaLote = $numSequenciaCorrente-1; //NUMERO SEQUENCIAL MENOS OS REGISTO DE HEADER DE ARQUIVO
	$QUANT_REGISTROS_LOTE = $numSequenciaLote;
	$QUANT_REGISTROS_LOTE = sprintf("%06d",$QUANT_REGISTROS_LOTE);
	
	//$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
	$VALOR =array_sum($arrayValorTotalCorrente);
	$VALOR = number_format($VALOR, 2, ".","");
	$remover = array(".", "-", "/",",");
	$VALORF  = str_replace($remover, "", $VALOR );	
	$VALOR = sprintf("%018d",$VALORF);

	$QUANT_DE_MOEDA = $VALORF;
	$QUANT_DE_MOEDA = sprintf("%018d",$QUANT_DE_MOEDA);
	
	$NUM_AVISO_DEBITO = '0'; //VALOR FIXO
	$NUM_AVISO_DEBITO = sprintf("%06d",$NUM_AVISO_DEBITO);	
	//??CNAB
	
	$OCORRENCIAS = '00';
	$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);	
	/*
	$handle = fopen('BANCOS/SANTANDER/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");	
	fwrite($handle, $BANCO , 3);	
	fwrite($handle, $LOTE_SERVICO , 4);	
	fwrite($handle, $TIPO_REGISTRO , 1);
	$USO_FEBRABAN_CNBB = ' ';
	$USO_FEBRABAN_CNBB = sprintf("% 9s",$USO_FEBRABAN_CNBB);	
	fwrite($handle, $USO_FEBRABAN_CNBB , 9);
	fwrite($handle, $QUANT_REGISTROS_LOTE , 6);
	fwrite($handle, $VALOR , 18);	
	fwrite($handle, $QUANT_DE_MOEDA , 18);	
	fwrite($handle, $NUM_AVISO_DEBITO , 6);
	$USO_FEBRABAN_CNBB = ' ';
	$USO_FEBRABAN_CNAB = sprintf("% 165s",$USO_FEBRABAN_CNAB);	
	fwrite($handle, $USO_FEBRABAN_CNAB , 165);
	fwrite($handle, $OCORRENCIAS , 10);	
	fwrite($handle, "\r\n");	
	*/

?>