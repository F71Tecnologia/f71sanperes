<?
				/////////////////////////////////////////////////////
				//REGISTRO DE DETALHES - SEGMENTO A (OBRIGATÓRIO )//
				///////////////////////////////////////////////////
				$handle = fopen('BANCOS/BRASIL/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");
				$BANCO = '001';
				fwrite($handle, $BANCO,3);					
				
				$LOTE_SERVICO = '0001';
				fwrite($handle, $LOTE_SERVICO,4);
					
				$TIPO_REGISTRO = '3';
				fwrite($handle, $TIPO_REGISTRO,1);
				
				$numSequenciaCorrente = $numSequenciaCorrente+1;
				$QUANT_REGISTROS = $numSequenciaCorrente;
				$QUANT_REGISTROS = sprintf("%05d",$QUANT_REGISTROS);
				fwrite($handle, $QUANT_REGISTROS, 5);
				
				$SEGMENTO = 'A';
				fwrite($handle, $SEGMENTO, 1);
				
				$TIPO_MOVIMENTACAO = '0';
				fwrite($handle, $TIPO_MOVIMENTACAO, 1);
				
				$CODIGO_INSTRUCAO_MOVIMENTACAO = '00';
				fwrite($handle, $CODIGO_INSTRUCAO_MOVIMENTACAO, 2);
				
				$CODIGO_CAMARA_COMPENSACAO = '000';
				fwrite($handle, $CODIGO_CAMARA_COMPENSACAO, 3);

				$CODIGO_BANCO_FAVORECIDO = '001';
				$CODIGO_BANCO_FAVORECIDO = sprintf("%03d",$CODIGO_BANCO_FAVORECIDO);
				fwrite($handle, $CODIGO_BANCO_FAVORECIDO, 3);
				
				$AGENCIA = $row_clt['agencia'];					
				$CODIGO_AGENCIA_FAVORECIDO = substr($AGENCIA, 0, 4); 
				$CODIGO_AGENCIA_FAVORECIDO = sprintf("%05d", $CODIGO_AGENCIA_FAVORECIDO);
				fwrite($handle, $CODIGO_AGENCIA_FAVORECIDO, 5);
					
				$str = $row_clt['agencia'];	
				$ultimoDigitoAgencia = $str{strlen($str)-1};			
				$DV_AGENCIA_FAVORECIDO = $ultimoDigitoAgencia;
				fwrite($handle, $DV_AGENCIA_FAVORECIDO, 1);
								
				$CONTA_FAVORECIDO = $row_clt['conta'];
				$remover = array(".", "-", "/",",");				
				$CONTA_FAVORECIDOF = str_replace($remover, '', $CONTA_FAVORECIDO);
				$CONTA_FAVORECIDOF = substr($CONTA_FAVORECIDOF, 0, -1);
				$CONTA_FAVORECIDO = sprintf("%012s",$CONTA_FAVORECIDOF);
				fwrite($handle, $CONTA_FAVORECIDO, 12);
		
				$DV_CONTA_FAVORECIDO = substr($row_clt['conta'], -1);
				fwrite($handle, $DV_CONTA_FAVORECIDO, 1);

				$DV_AGENCIA_CONTA = ' ';
				fwrite($handle, $DV_AGENCIA_CONTA, 1);
				
				$NOME_FAVORECIDO = $row_clt['nome'];
				$NOME_FAVORECIDO = sprintf("% -30s",$NOME_FAVORECIDO);					
				fwrite($handle, $NOME_FAVORECIDO, 30);
				
				$NUMERO_SISTEMA = '';
				$NUMERO_SISTEMA = sprintf("% -20s",$NUMERO_SISTEMA);
				fwrite($handle, $NUMERO_SISTEMA, 20);								
				
				$DATA_PAGAMENTO = $d.$m.$a;
				$DATA_PAGAMENTO = sprintf("%08d",$DATA_PAGAMENTO);
				fwrite($handle, $DATA_PAGAMENTO, 8);
											
				$TIPO_MOEDA = 'BRL';
				fwrite($handle, $TIPO_MOEDA, 3);	
				
				$QUANTIDADE_MOEDA  = '000000000000000';
				fwrite($handle, $QUANTIDADE_MOEDA, 15);
				
				$VALOR_PAGAMENTO  = $row_clt['salario_liq'];
				$arrayValorTotalCorrente[] = $VALOR_PAGAMENTO ;
				$remover = array(".", "-", "/",",");
				$VALOR_PAGAMENTOF  = str_replace($remover, "", $VALOR_PAGAMENTO );
				$VALOR_PAGAMENTO  = sprintf("%015d" ,$VALOR_PAGAMENTOF );
				fwrite($handle, $VALOR_PAGAMENTO, 15);
				
				$NOSSO_NUMERO = ' ';
				$NOSSO_NUMERO  = sprintf("% -20s" , $NOSSO_NUMERO );
				fwrite($handle, $NOSSO_NUMERO, 20);
				
				$DATA_REAL_EFETIVACAO = ' ';
				$DATA_REAL_EFETIVACAO  = sprintf("% 8s" , $DATA_REAL_EFETIVACAO );
				fwrite($handle, $DATA_REAL_EFETIVACAO, 8);
				
				$VALOR_REAL_EFETIVACAO = ' ';
				$VALOR_REAL_EFETIVACAO  = sprintf("% 15s" , $VALOR_REAL_EFETIVACAO );
				fwrite($handle, $VALOR_REAL_EFETIVACAO, 15);
				
				$INFORMACAO2 = ' ';
				$INFORMACAO2  = sprintf("% 40s" , $INFORMACAO2 );
				fwrite($handle, $INFORMACAO2, 40);

				$BRANCO = ' ';
				$BRANCO  = sprintf("% 12s" , $BRANCO );
				fwrite($handle, $BRANCO, 12);
				
				$AVISO_FAVORECIDO = '0';
				fwrite($handle, $AVISO_FAVORECIDO, 1);	
				
				$OCORRENCIAS = ' ';
				$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);
				fwrite($handle, $OCORRENCIAS, 10);
				fwrite($handle, "\r\n");
				
				///////////////////////////////////////
				//REGISTRO DE DETALHES - SEGMENTO B //
				/////////////////////////////////////				
				$BANCO = '001';
				fwrite($handle, $BANCO,3);					
				
				$LOTE_SERVICO = '0001';
				fwrite($handle, $LOTE_SERVICO,4);
					
				$TIPO_REGISTRO = '3';
				fwrite($handle, $TIPO_REGISTRO,1);
				
				$numSequenciaCorrente = $numSequenciaCorrente+1;
				$QUANT_REGISTROS = $numSequenciaCorrente;
				$QUANT_REGISTROS = sprintf("%05d",$QUANT_REGISTROS);
				fwrite($handle, $QUANT_REGISTROS, 5);
				
				$SEGMENTO = 'B';
				fwrite($handle, $SEGMENTO, 1);
				
				$BRANCO = ' ';
				$BRANCO  = sprintf("% 3s" , $BRANCO );
				fwrite($handle, $BRANCO, 3);
				
				$TIPO_INSCRICAO_FAVORECIDO = '1';
				fwrite($handle, $TIPO_INSCRICAO_FAVORECIDO, 1);
				
				$NUMERO_INSCRICAO_FAVORECIDO = $row_clt['cpf'];
				$remover = array(".", "-", "/",",");
				$NUMERO_INSCRICAO_FAVORECIDO  = str_replace($remover, "", $NUMERO_INSCRICAO_FAVORECIDO);
				$NUMERO_INSCRICAO_FAVORECIDO = sprintf("%014s",$NUMERO_INSCRICAO_FAVORECIDO);
				fwrite($handle, $NUMERO_INSCRICAO_FAVORECIDO, 14);				

				$LOGRADOURO_FAVORECIDO = ' ';
				$LOGRADOURO_FAVORECIDO  = sprintf("% -30s",$LOGRADOURO_FAVORECIDO );
				fwrite($handle, $LOGRADOURO_FAVORECIDO, 30);
				
				$NUMERO_FAVORECIDO = '0';
				$NUMERO_FAVORECIDO = sprintf("%05d",$NUMERO_FAVORECIDO);
				fwrite($handle, $NUMERO_FAVORECIDO, 5);
				
				
				$COMPLEMENTO_FAVORECIDO = ' ';
				$COMPLEMENTO_FAVORECIDO = sprintf("% -15s",$COMPLEMENTO_FAVORECIDO);
				fwrite($handle, $COMPLEMENTO_FAVORECIDO, 15);
				
				$BAIRRO_FAVORECIDO = ' ';
				$BAIRRO_FAVORECIDO = sprintf("% -15s",$BAIRRO_FAVORECIDO);
				fwrite($handle, $BAIRRO_FAVORECIDO, 15);

				$CIDADE_FAVORECIDO =' ';
				$CIDADE_FAVORECIDO = sprintf("% -20s",$CIDADE_FAVORECIDO);
				fwrite($handle, $CIDADE_FAVORECIDO, 20);
				
				$CEP_FAVORECIDO = '0';
				$CEP_FAVORECIDO  = sprintf("%05d",$CEP_FAVORECIDO);
				fwrite($handle, $CEP_FAVORECIDO, 5);
				
				$COMPLEMENTO_CEP_FAVORECIDO = ' ';
				$COMPLEMENTO_CEP_FAVORECIDO = sprintf("% -3s",$COMPLEMENTO_CEP_FAVORECIDO);
				fwrite($handle, $COMPLEMENTO_CEP_FAVORECIDO, 3);
				
				$ESTADO_FAVORECIDO = ' ';
				$ESTADO_FAVORECIDO = sprintf("% -2s",$ESTADO_FAVORECIDO);
				fwrite($handle, $ESTADO_FAVORECIDO, 2);
				
				//PAGAMENTO
				$VENCIMENTO = '0';
				$VENCIMENTO  = sprintf("%08d",$VENCIMENTO );
				fwrite($handle, $VENCIMENTO, 8);
				
				$VALOR_DOCUMENTO  = '0';
				$VALOR_DOCUMENTO = sprintf("%015d",$VALOR_DOCUMENTO);
				fwrite($handle, $VALOR_DOCUMENTO, 15);
				
				$ABATIMENTO = '0';
				$ABATIMENTO = sprintf("%015d",$ABATIMENTO);
				fwrite($handle, $ABATIMENTO, 15);
				
				$DESCONTO = '0';
				$DESCONTO = sprintf("%015d",$DESCONTO);
				fwrite($handle, $DESCONTO, 15);
				
				$MORA = '0';
				$MORA = sprintf("%015d",$MORA);
				fwrite($handle, $MORA, 15);
				
				$MULTA = '0';
				$MULTA = sprintf("%015d",$MULTA);
				fwrite($handle, $MULTA, 15);
				
				$COD_DOC_FAVOREVIDO = $row_clt['id_autonomo'];
				$COD_DOC_FAVOREVIDO = sprintf("% -15s",$COD_DOC_FAVOREVIDO);
				fwrite($handle, $COD_DOC_FAVOREVIDO, 15);
				
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% -15s",$USO_FEBRABAN_CNBB);	
				fwrite($handle, $USO_FEBRABAN_CNBB, 15);
				fwrite($handle, "\r\n");
			
?>