<?
				/////////////////////////////////////////////////////
				//REGISTRO DE DETALHES - SEGMENTO A (OBRIGATÓRIO )//
				///////////////////////////////////////////////////
			  	
				$BANCO = '341';
				$LOTE_SERVICO = '0001';
				$TIPO_REGISTRO = '3';	
				
				$numSequenciaCorrente = $numSequenciaCorrente+1;
				$QUANT_REGISTROS = $numSequenciaCorrente;
				$QUANT_REGISTROS = sprintf("%05d",$QUANT_REGISTROS);
				
				$SEGMENTO = 'A';
				
				$TIPO_MOVIMENTACAO = '000';
								
				$BANCO_FAVORECIDO = 341;
				$BANCO_FAVORECIDO = sprintf("%03d",$BANCO_FAVORECIDO);
				
				//CASO O CAMPO ($BANCO_FAVORECIDO) TENHA 341 COMO VALOR
				//??ZERO 9(1)			
				$AGENCIA = $row_clt['agencia'];
				$CODIGO_AGENCIA_FAVORECIDO = sprintf("%05d",$AGENCIA);				
				//??BRANCO X(01)
				//??ZERO 9(07)
				$CONTA_FAVORECIDO = $row_clt['conta'];
				$remover = array(".", "-", "/",",");
				
				$CONTA_FAVORECIDOF = str_replace($remover, "", $CONTA_FAVORECIDO);
				$CONTA_FAVORECIDOF = substr($CONTA_FAVORECIDOF, 0, 5);
				$CONTA_FAVORECIDO = sprintf("%012s",$CONTA_FAVORECIDOF);

				//??BRANCO X(01)
				
				$DAC_FAVORECIDO = $row_clt['conta'];
				$str = $DAC_FAVORECIDO;
				$ultimoDigitoConta = $str{strlen($str)-1};
				$DAC_FAVORECIDO = $ultimoDigitoConta;		  
				
				$NOME_FAVORECIDO = $row_clt['nome'];
				$NOME_FAVORECIDO = sprintf("% -30s",$NOME_FAVORECIDO);		
				
				$data = $regiao.date('mYHis').'c'.$row_clt[0];
				$SEU_NUMERO = $data;
				$SEU_NUMERO = sprintf("% -20s",$SEU_NUMERO);
				
				$DATA_PAGAMENTO = $d.$m.$a;
				$DATA_PAGAMENTO = sprintf("%08d",$DATA_PAGAMENTO);
											
				$TIPO_MOEDA = 'REA';
				$TIPO_MOEDA = sprintf("% -3s",$TIPO_MOEDA);																											
				
				$VALOR_PAGAMENTO  = $row_clt['salario_liq'];
				$arrayValorTotalCorrente[] = $VALOR_PAGAMENTO ;
				$remover = array(".", "-", "/",",");
				$VALOR_PAGAMENTOF  = str_replace($remover, "", $VALOR_PAGAMENTO );
				$VALOR_PAGAMENTO  = sprintf("%015d" ,$VALOR_PAGAMENTOF );				
				
				$NOSSO_NUMERO = '';
				$NOSSO_NUMERO = sprintf('% 15s', $NOSSO_NUMERO);
				//BRANCOS X(5)
				$DATA_EFETIVA = '';
				$VALOR_EFETIVO = '';
				
				$FINALIDADE_DETALHE = '';
				$FINALIDADE_DETALHE = sprintf("% 18s",$FINALIDADE_DETALHE);
				//BRANCOS X(2)
				$NUMERO_DOCUMENTO = '0';
				$NUMERO_DOCUMENTO = sprintf("%06d",$NUMERO_DOCUMENTO);
				
				$NUM_INSCRICAO_CPF = $row_clt['cpf'];				
				$remover = array(".", "-", "/",",");
				$NUM_INSCRICAO_CPF  = str_replace($remover, "", $NUM_INSCRICAO_CPF);				
				$NUM_INSCRICAO_CPF  = sprintf("%014s" ,$NUM_INSCRICAO_CPF );

				$FINALIDADE_DOC_STATUS = '06';
				
				$FINALIDADE_TED = '00004';
				//BRANCOS X(5)
				$AVISO = '0';
				
				$OCORRENCIAS = ' ';
				$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);
				
				$handle = fopen('BANCOS/ITAU/CONTA_CORRENTE/'.$CONSTANTE.'_'.'CORRENTE'."_".$DD."_".$MM."_".$ANO.".txt", "a");
			  	fwrite($handle, $BANCO, 3);
				fwrite($handle, $LOTE_SERVICO , 4);
				fwrite($handle, $TIPO_REGISTRO , 1);				
				fwrite($handle, $QUANT_REGISTROS , 5);				
				fwrite($handle, $SEGMENTO , 1);
				fwrite($handle, $TIPO_MOVIMENTACAO , 3);
				$ZERO = '000';
				fwrite($handle, $ZERO , 3);
				fwrite($handle, $BANCO_FAVORECIDO , 3);
				fwrite($handle, $CODIGO_AGENCIA_FAVORECIDO , 5);								
				$BRANCO = ' ';
				fwrite($handle, $BRANCO  , 1);
				fwrite($handle, $CONTA_FAVORECIDO  , 12);
				$BRANCO = ' ';
				fwrite($handle, $BRANCO  , 1);				
				fwrite($handle, $DAC_FAVORECIDO  , 1);				
				fwrite($handle, $NOME_FAVORECIDO , 30);
				fwrite($handle, $SEU_NUMERO , 20);				
				fwrite($handle, $DATA_PAGAMENTO , 8);
				fwrite($handle, $TIPO_MOEDA , 3);
				$ZERO = '000000000000000';
				fwrite($handle, $ZERO , 15);
				fwrite($handle, $VALOR_PAGAMENTO , 15);
				fwrite($handle, $NOSSO_NUMERO , 15);				
				$BRANCO = ' ';
				$BRANCO  = sprintf("% 5s",$BRANCO );
				fwrite($handle, $BRANCO  , 5);	
				
				$data_efetiva = '00000000';
				fwrite($handle, $data_efetiva  , 8);
				$valor_efetivo = '000000000000000';
				fwrite($handle, $valor_efetivo , 15);				
				fwrite($handle, $FINALIDADE_DETALHE , 18);
				$BRANCO = ' ';
				$BRANCO  = sprintf("% 2s",$BRANCO );
				fwrite($handle, $BRANCO  , 2);	
				$num_doc = '000000';
				fwrite($handle, $num_doc , 6);
				
				fwrite($handle, $NUM_INSCRICAO_CPF , 14);
				fwrite($handle, $FINALIDADE_DOC_STATUS , 2);
				fwrite($handle, $FINALIDADE_TED , 5);
				$BRANCO = ' ';
				$BRANCO  = sprintf("% 5s",$BRANCO );
				fwrite($handle, $BRANCO  , 5);
				fwrite($handle, $AVISO , 1);
				fwrite($handle, $OCORRENCIAS , 10);
				fwrite($handle, "\r\n");			  			  			  

?>