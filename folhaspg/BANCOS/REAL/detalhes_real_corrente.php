<?
				////////////////////////////////////////////////////////////////////////
				//REGISTRO DE DETALHES - SEGMENTO A (OBRIGATÓRIO - REMESSA / RETORNO)//
				//////////////////////////////////////////////////////////////////////					
				
			  	$BANCO = '356'; //VALOR CONSTANTE
				$LOTE_SERVICO = '0001';
				$TIPO_REGISTRO = '3';	
				
				$numSequenciaCorrenteREAL = $numSequenciaCorrenteREAL+1;
				$QUANT_REGISTROS = $numSequenciaCorrenteREAL;
				$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
				
				$SEGMENTO = 'A';
				
				$TIPO_MOVIMENTACAO = '0'; //INCLUSAO
				$CODIGO_MOVIMENTACAO = '00';
				
				//DADOS DA EMPRESA
				$NUMERO_CNPJ_EMPRESA = $row_master['cnpj'];
				$remover = array(".", "-", "/",",");
				$NUMERO_CNPJ_EMPRESA = str_replace($remover, "", $NUMERO_CNPJ_EMPRESA);
				$NUMERO_CNPJ_EMPRESA = sprintf("%014s",$NUMERO_CNPJ_EMPRESA);
				$NOME_DA_EMPRESA = sprintf("%- 20s",$row_master['nome']);
				//DADOS DO BANCO DA EMPRESA
				$AGENCIA = $row_banco['agencia'];					
 				$AGENCIA = substr($AGENCIA, 0, 4); 
				$AGENCIA = sprintf("%04d",$AGENCIA);
				
				$NUMERO_CONTA = $row_banco['conta'];
				$remover = array(".", "-", "/",",");					 
				$NUMERO_CONTAT = str_replace($remover, "", $NUMERO_CONTA);
				$NUMERO_CONTAF = substr($NUMERO_CONTAT, 0, 7);							//NUMERO DA CONTA SEM O DIGITO
					
				$NUMERO_CONTA = sprintf("%07s",$NUMERO_CONTAF);
				
				
				//FAVORECIDO
				$COMPENSACAO_FAVORECIDO = ''; //NÃO FORNECER
				$COMPENSACAO_FAVORECIDO = sprintf("%03d",$COMPENSACAO_FAVORECIDO);
				
				$BANCO_FAVORECIDO = 356;
				$BANCO_FAVORECIDO = sprintf("%03d",$BANCO_FAVORECIDO);
				
				$CODIGO_AGENCIA_FAVORECIDO = $row_clt['agencia'];
				$CODIGO_AGENCIA_FAVORECIDO = substr($CODIGO_AGENCIA_FAVORECIDO, 0, 4);
				$CODIGO_AGENCIA_FAVORECIDO = sprintf("%04d",$CODIGO_AGENCIA_FAVORECIDO);
					
				/*$str = $row_clt['agencia'];
				$ultimoDigitoAgencia = $str{strlen($str)-1};			
				$DV_AGENCIA_FAVORECIDO = $ultimoDigitoAgencia;*/
				$DV_AGENCIA_FAVORECIDO = ' ';
				$DV_AGENCIA_FAVORECIDO = sprintf("% 1s",$DV_AGENCIA_FAVORECIDO);
				
				$NUMERO_CONTA_FAVORECIDO = $row_clt['conta'];
				$remover = array(".", "-", "/",",");				
				$NUMERO_CONTA_FAVORECIDO = str_replace($remover, "", $NUMERO_CONTA_FAVORECIDO);				
				$NUMERO_CONTA_FAVORECIDO = sprintf("%08d",$NUMERO_CONTA_FAVORECIDO);
								
				$str = $row_clt['conta'];
				$ultimoDigitoConta = $str{strlen($str)-1};			
				$DV_CONTA_FAVORECIDO = $ultimoDigitoConta;
					
				$DV_AGENCIA_CONTA = ' ';				
				$DV_AGENCIA_CONTA = sprintf("% 1s",$DV_AGENCIA_CONTA);
				
				$NOME_FAVORECIDO = $row_clt['nome'];
				$NOME_FAVORECIDO = sprintf("% -40s",$NOME_FAVORECIDO);
				
				//CRÉDITO
				$data = $regiao.date('dmYHis').$row_clt[0];;
				$NOSSO_NUMERO = '$data';
				$NOSSO_NUMERO = sprintf("% -20s",$NOSSO_NUMERO);
				
				
				$DATA_PAGAMENTO = date("dmy", mktime(0,0,0, $m, $d, $a));
				//$DATA_PAGAMENTO = $d.$m.$y;
				$DATA_PAGAMENTO = sprintf("%06d",$DATA_PAGAMENTO);
				
				$TIPO_MOEDA = 'BRL';
				$TIPO_MOEDA = sprintf("% -3s",$TIPO_MOEDA);
				
				$VALOR_PAGAMENTO  = $row_clt['salario_liq'];
				$arrayValorTotalCorrente[] = $VALOR_PAGAMENTO ;
				$remover = array(".", "-", "/",",");
				$VALOR_PAGAMENTOF  = str_replace($remover, "", $VALOR_PAGAMENTO );
				$VALOR_PAGAMENTO  = sprintf("%013d" ,$VALOR_PAGAMENTOF );				
				
				$QUANTIDADE_MOEDA = $VALOR_PAGAMENTOF;
				$QUANTIDADE_MOEDA = sprintf("%015d",$QUANTIDADE_MOEDA);				
				
				$NOSSO_NUMERO = '';
				$NOSSO_NUMERO = sprintf("% -20s",$NOSSO_NUMERO);
				
				$DATA_REAL = $d.$m.$a;//não usar
				$DATA_REAL = sprintf("%08d",$DATA_REAL);
				
				$VALOR_REAL = $VALOR_PAGAMENTOF;
				$VALOR_REAL = sprintf("%015d",$VALOR_REAL);
				
				$INFORMACAO2 = ' ';
				$INFORMACAO2 = sprintf("% -40s",$INFORMACAO2);
				
				$CODIGO_FINALIDADE_DOC = '06';
				$CODIGO_FINALIDADE_DOC = sprintf("% -2s",$CODIGO_FINALIDADE_DOC);
				
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% -10s",$USO_FEBRABAN_CNBB);
				
				$AVISO_FAVORECIADO = 0;
				$AVISO_FAVORECIADO = sprintf("%01d", $AVISO_FAVORECIADO);
				
				$OCORRENCIAS = '00';
				$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);
				
				//ESCREVENCO NO ARQUIVO
				$handle = fopen('BANCOS/REAL/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");
			  	
				fwrite($handle, "1", 1);
				fwrite($handle, "02", 2); //IDENTIFICAÇÃO DA EMPRESA
				fwrite($handle, $NUMERO_CNPJ_EMPRESA, 14);
				fwrite($handle, $NOME_DA_EMPRESA, 20);
				
				$RESERVADO_BRANCOS = sprintf("% 25s"," ");
				fwrite($handle, $RESERVADO_BRANCOS, 25);
				
				fwrite($handle, $CODIGO_AGENCIA_FAVORECIDO , 4);
				fwrite($handle, $NUMERO_CONTA_FAVORECIDO , 8);
				
				$RESERVADO_BRANCOS = sprintf("% 8s"," ");
				fwrite($handle, $RESERVADO_BRANCOS, 8);
				
				fwrite($handle, $NOME_FAVORECIDO , 40);
				fwrite($handle, $DATA_PAGAMENTO , 6);
				fwrite($handle, $VALOR_PAGAMENTO , 13);
				fwrite($handle, "001", 3);
				
				$RESERVADO_BRANCOS = sprintf("% 6s"," ");
				fwrite($handle, $RESERVADO_BRANCOS, 6);
				
				fwrite($handle, $AGENCIA, 4);
				fwrite($handle, $NUMERO_CONTA, 7);
				
				$RESERVADO_BRANCOS = sprintf("% 33s"," ");
				fwrite($handle, $RESERVADO_BRANCOS, 33);
				
				fwrite($handle, $QUANT_REGISTROS , 6);
				/*
				fwrite($handle, $BANCO, 3);
				fwrite($handle, $LOTE_SERVICO , 4);
				fwrite($handle, $TIPO_REGISTRO , 1);
				fwrite($handle, $QUANT_REGISTROS , 5);
				fwrite($handle, $SEGMENTO , 1);
				fwrite($handle, $TIPO_MOVIMENTACAO , 1);
				fwrite($handle, $CODIGO_MOVIMENTACAO , 2);
				fwrite($handle, $COMPENSACAO_FAVORECIDO , 3);
				fwrite($handle, $BANCO_FAVORECIDO , 3);
				fwrite($handle, $NUMERO_CONTA_FAVORECIDO , 12);
				fwrite($handle, $DV_CONTA_FAVORECIDO , 1);
				fwrite($handle, $DV_AGENCIA_CONTA , 1);
				fwrite($handle, $NOME_FAVORECIDO , 30);
				fwrite($handle, $NOSSO_NUMERO , 20);
				fwrite($handle, $DATA_PAGAMENTO , 8);
				fwrite($handle, $TIPO_MOEDA , 3);
				fwrite($handle, $QUANTIDADE_MOEDA , 15);
				fwrite($handle, $VALOR_PAGAMENTO , 15);
				fwrite($handle, $NOSSO_NUMERO , 20);
				fwrite($handle, $DATA_REAL , 8);
				fwrite($handle, $VALOR_REAL , 15);
				fwrite($handle, $INFORMACAO2 , 40);
				fwrite($handle, $CODIGO_FINALIDADE_DOC , 2);
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% 10s",$USO_FEBRABAN_CNBB);
				fwrite($handle, $USO_FEBRABAN_CNBB , 10);
				fwrite($handle, $AVISO_FAVORECIADO , 1);
				fwrite($handle, $OCORRENCIAS , 10);
				fwrite($handle, "\r\n");
				
				////////////////////////////////////////////////////////////////////////
				//REGISTRO DE DETALHES - SEGMENTO B (OBRIGATÓRIO - REMESSA / RETORNO)//
				//////////////////////////////////////////////////////////////////////
			  	$BANCO = '356'; //VALOR CONSTANTE
				$LOTE_SERVICO = '0001';
				$TIPO_REGISTRO = '3';	
				
				$numSequenciaCorrente = $numSequenciaCorrente+1;
				$QUANT_REGISTROS = $numSequenciaCorrente;
				$QUANT_REGISTROS = sprintf("%05d",$QUANT_REGISTROS);
				
				$SEGMENTO = 'B';
				
				//CNAB
				////////////////////////
				//DADOS COPLEMENTARES//
				//////////////////////
				
				//FAVORECIDO
				
				$TIPO_INSCRICAO_FAVORECIDO = '1';
				$TIPO_INSCRICAO_FAVORECIDO = sprintf("%01d",$TIPO_INSCRICAO_FAVORECIDO);
				
				$NUMERO_INSCRICAO_FAVORECIDO = $row_clt['cpf'];
				$remover = array(".", "-", "/",",");
				$NUMERO_INSCRICAO_FAVORECIDO  = str_replace($remover, "", $NUMERO_INSCRICAO_FAVORECIDO);
				$NUMERO_INSCRICAO_FAVORECIDO = sprintf("%014s",$NUMERO_INSCRICAO_FAVORECIDO);
				
				$LOGRADOURO_FAVORECIDO = $row_clt['endereco'];
				$LOGRADOURO_FAVORECIDO  = sprintf("% -30s",$LOGRADOURO_FAVORECIDO );
				
				$NUMERO_FAVORECIDO = '';
				$NUMERO_FAVORECIDO = sprintf("%05d",$NUMERO_FAVORECIDO);
				
				$COMPLEMENTO_FAVORECIDO = '';
				$COMPLEMENTO_FAVORECIDO = sprintf("% -15s",$COMPLEMENTO_FAVORECIDO);
				
				$BAIRRO_FAVORECIDO = $row_clt['bairro'];

				$BAIRRO_FAVORECIDO = sprintf("% -15s",$BAIRRO_FAVORECIDO);
				
				$CIDADE_FAVORECIDO = $row_clt['cidade'];
				$CIDADE_FAVORECIDO = sprintf("% -20s",$CIDADE_FAVORECIDO);
				
				$CEP_FAVORECIDO = $row_clt['cep'];
				$remover = array(".", "-", "/",",");
				$CEP_FAVORECIDO  = str_replace($remover, "", $CEP_FAVORECIDO );
				$CEP_FAVORECIDO  = sprintf("%05d",$CEP_FAVORECIDO);
				
				$COMPLEMENTO_CEP_FAVORECIDO = '';
				$COMPLEMENTO_CEP_FAVORECIDO = sprintf("% -3s",$COMPLEMENTO_CEP_FAVORECIDO);
				
				$ESTADO_FAVORECIDO = $row_clt['uf'];
				$ESTADO_FAVORECIDO = sprintf("% -2s",$ESTADO_FAVORECIDO);
				
				//PAGAMENTO
				$VENCIMENTO = $d.$m.$a;
				$VENCIMENTO  = sprintf("%08d",$VENCIMENTO );
				
				$VALOR_DOCUMENTO  = $row_clt['salario_liq'];
				//$arrayValorTotalCorrente[] = $VALOR_PAGAMENTO ;
				$remover = array(".", "-", "/",",");
				$VALOR_DOCUMENTO = str_replace($remover, "", $VALOR_DOCUMENTO );
				$VALOR_DOCUMENTO = sprintf("%015d",$VALOR_DOCUMENTO);
				
				$ABATIMENTO = '0';
				$ABATIMENTO = sprintf("%015d",$ABATIMENTO);
				
				$DESCONTO = '0';
				$DESCONTO = sprintf("%015d",$DESCONTO);
				
				$MORA = '0';
				$MORA = sprintf("%015d",$MORA);
				
				$MULTA = '0';
				$MULTA = sprintf("%015d",$MULTA);
				
				$COD_DOC_FAVOREVIDO = $row_clt['id_clt'];
				$COD_DOC_FAVOREVIDO = sprintf("% -15s",$COD_DOC_FAVOREVIDO);

				
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% -15s",$USO_FEBRABAN_CNBB);				

				fwrite($handle, $BANCO , 3);
				fwrite($handle, $LOTE_SERVICO , 4);
				fwrite($handle, $TIPO_REGISTRO , 1);
				fwrite($handle, $SEGMENTO , 1);
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% 3s", $USO_FEBRABAN_CNBB);
				fwrite($handle, $USO_FEBRABAN_CNBB , 3);
				fwrite($handle, $TIPO_INSCRICAO_FAVORECIDO , 1);
				fwrite($handle, $NUMERO_INSCRICAO_FAVORECIDO, 14);
				fwrite($handle, $LOGRADOURO_FAVORECIDO , 30);
				fwrite($handle, $NUMERO_FAVORECIDO , 5);
				fwrite($handle, $COMPLEMENTO_FAVORECIDO , 15);
				fwrite($handle, $BAIRRO_FAVORECIDO , 15);
				fwrite($handle, $CIDADE_FAVORECIDO , 20);
				fwrite($handle, $CEP_FAVORECIDO , 5);
				fwrite($handle, $COMPLEMENTO_CEP_FAVORECIDO , 3);
				fwrite($handle, $ESTADO_FAVORECIDO , 2);
				fwrite($handle, $VENCIMENTO , 8);
				fwrite($handle, $VALOR_DOCUMENTO , 15);
				fwrite($handle, $ABATIMENTO , 15);
				fwrite($handle, $DESCONTO , 15);
				fwrite($handle, $MORA , 15);
				fwrite($handle, $MULTA , 15);
				fwrite($handle, $COD_DOC_FAVOREVIDO , 15);
				$USO_FEBRABAN_CNBB = ' ';
				$USO_FEBRABAN_CNBB = sprintf("% -15s",$USO_FEBRABAN_CNBB);				
				fwrite($handle, $USO_FEBRABAN_CNBB , 15);
				*/
				fwrite($handle, "\r\n");
				

?>