<?
	/////////////////////////////////////
	// REGISTRO DE TRAILER DO ARQUIVO //
	///////////////////////////////////
	
	/* CONTROLE */
	$BANCO = '356';
	$LOTE_SERVICO = '9999';
	$TIPO_REGISTRO = '9';
	//??CNAB
	
	/* TOTAIS */
	$QUANT_LOTES = '000001';
	
	$numSequencia = $numSequencia+1;
	$QUANT_REGISTROS = $numSequencia;
	$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

	$QUANT_CONTAS_CONCIL = '000001';
	$handle = fopen('BANCOS/REAL/CONTA_SALARIO/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");
	fwrite($handle, $BANCO , 3);	
	fwrite($handle, $LOTE_SERVICO , 4);	
	fwrite($handle, $TIPO_REGISTRO , 1);
	$USO_FEBRABAN_CNBB = ' ';
	$USO_FEBRABAN_CNAB = sprintf("% 9s",$USO_FEBRABAN_CNAB);	
	fwrite($handle, $USO_FEBRABAN_CNAB , 9);
	fwrite($handle, $QUANT_LOTES , 6);	
	fwrite($handle, $QUANT_REGISTROS , 6);	
	fwrite($handle, $QUANT_CONTAS_CONCIL , 6);
	$USO_FEBRABAN_CNBB = ' ';
	$USO_FEBRABAN_CNAB = sprintf("% 205s",$USO_FEBRABAN_CNAB);	
	fwrite($handle, $USO_FEBRABAN_CNAB , 205);	
	fclose($handle);		

?>