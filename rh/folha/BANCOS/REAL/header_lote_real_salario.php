<?

					///////////////////////////////
					//REGISTRO DE HEADER DE LOTE//
					/////////////////////////////
										
					/* DADOS DE CONTROLE */
					$BANCO = '356'; //VALOR CONSTANTE
					$LOTE_SERVICO = '0001';
					$TIPO_REGISTRO = '1';
					
					/* DADOS DO SERVIÇO */
					$OPERACAO = 'C'; //LANÇAMENTO DE CRÉDITO
					$SERVICO = '30';  //TIPO DE SERVIÇO 30 (OAGAMENTO DE SALÁRIOS)
					$FORMA_LANCAMENTO = '01'; //CREDITO EM CONTA CORRENTE
					$LAYOUT_LOTE = '030';
					//??CNAB
					
					/* DADOS DA EMPRESA */
					//INSCRICAO					
					$TIPO_INSCRICAO_EMPRESA = '2';									
					$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
					$remover = array(".", "-", "/",",");
					$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
					$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);					
					
					//CONVÊNIO
					$AGENCIA = $row_banco['agencia'];
 					$CODIGO_AGENCIA_EMPRESA = substr($AGENCIA, 0, 4); 
					$CODIGO_AGENCIA_EMPRESA = sprintf("%05d",$CODIGO_AGENCIA_EMPRESA);
					
					$str = $row_banco['agencia'];
					$ultimoDigitoAgencia = $str{strlen($str)-1};			
					$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;	
					$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);
					
					$NUMERO_CONTA = $row_banco['conta'];
					$remover = array(".", "-", "/",",");
					$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
					$NUMERO_CONTAF = substr($NUMERO_CONTA, 0, 7);
					$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);
					
					$str = $row_banco['conta'];
					$ultimoDigitoConta = $str{strlen($str)-1};			
					$DV_CONTA_EMPRESA = $ultimoDigitoConta;	
					$DV_CONTA_EMPRESA = sprintf("%0d",$DV_CONTA_EMPRESA);
					
					$DV_CONTA_AGENCIA = $DV_AGENCIA_EMPRESA;
					
					//CODIGO DE CONVENIO
					$AGENCIA = $row_banco['agencia'];
 					$AGENCIA = substr($AGENCIA, 0, 4); 
					
					$CONVENIO_EMPRESA = $AGENCIA.$NUMERO_CONTAF.'PG';
					$CONVENIO_EMPRESA = sprintf("% -20s",$CONVENIO_EMPRESA);										
					
					
					$NOME_EMPRESA = $row_master['razao'];
					$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
					
					/* INFORMAÇÃO 1 */
					$INFORMACAO1 = ' ';
					$INFORMACAO1 = sprintf("% -40s",$INFORMACAO1);
					
					/* ENDEREÇO DA EMPRESA */
					$resultEmpresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_regiao[id_regiao]'");
					$rowEmpresa = mysql_fetch_array($resultEmpresa);					
					
					$LOGRADOURO_EMPRESA = $rowEmpresa['endereco'];
					$remover = array(".", "-", "/",",");
					$LOGRADOURO_EMPRESA = str_replace($remover, "", $LOGRADOURO_EMPRESA);	
					$LOGRADOURO_EMPRESA = sprintf("% -30s",$LOGRADOURO_EMPRESA);
					
					$NUMERO_EMPRESA = '0';
					$NUMERO_EMPRESA = sprintf("%05d",$NUMERO_EMPRESA);
					
					$COMPLEMENTO_EMPRESA = '';
					$COMPLEMENTO_EMPRESA = sprintf("% -15s",$COMPLEMENTO_EMPRESA);
					
					$CIDADE_EMPRESA =  '';
					$CIDADE_EMPRESA = sprintf("% -20s",$CIDADE_EMPRESA);
					
					$CEP_EMPRESA = '0';
					$CEP_EMPRESA = sprintf("%05d",$CEP_EMPRESA);
					
					$COMPLEMENRO_CEP_EMPRESA = '';
					$COMPLEMENRO_CEP_EMPRESA = sprintf("% 3s",$COMPLEMENRO_CEP_EMPRESA);
					
					$ESTADO_EMPRESA = '';
					$ESTADO_EMPRESA = sprintf("% 2s",$ESTADO_EMPRESA);
					
					$USO_FEBRABAN_CNBB = ' ';
					$USO_FEBRABAN_CNBB = sprintf("% 8s",$USO_FEBRABAN_CNBB);
					
					$OCORRENCIAS = '00';
					$OCORRENCIAS = sprintf("% 10s",$OCORRENCIAS);
					/*
					$handle = fopen('BANCOS/REAL/CONTA_SALARIO/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "a");

					fwrite($handle, $BANCO,3);
					fwrite($handle, $LOTE_SERVICO,4);
					fwrite($handle, $TIPO_REGISTRO, 1);
					
					fwrite($handle, $OPERACAO, 1);
					fwrite($handle, $SERVICO, 2);
					fwrite($handle, $FORMA_LANCAMENTO, 2);
					fwrite($handle, $LAYOUT_LOTE, 3);					
					$USO_FEBRABAN_CNBB = ' ';					
					fwrite($handle, $USO_FEBRABAN_CNBB, 1);
					fwrite($handle, $TIPO_INSCRICAO_EMPRESA, 1);
					fwrite($handle, $NUMERO_INSCRICAO_EMPRESA, 14);
					fwrite($handle, $CONVENIO_EMPRESA, 20);
					fwrite($handle, $CODIGO_AGENCIA_EMPRESA, 5);
					fwrite($handle, $DV_AGENCIA_EMPRESA, 1);
					fwrite($handle, $NUMERO_CONTA, 12);
					fwrite($handle, $DV_CONTA_EMPRESA, 1);
					fwrite($handle, $DV_CONTA_AGENCIA, 1);
					fwrite($handle, $NOME_EMPRESA, 30);
					fwrite($handle, $INFORMACAO1, 40);
					fwrite($handle, $LOGRADOURO_EMPRESA, 30);
					fwrite($handle, $NUMERO_EMPRESA, 5);
					fwrite($handle, $COMPLEMENTO_EMPRESA, 15);
					fwrite($handle, $CIDADE_EMPRESA, 25);
					fwrite($handle, $CEP_EMPRESA, 5);
					fwrite($handle, $COMPLEMENRO_CEP_EMPRESA, 3);
					fwrite($handle, $ESTADO_EMPRESA, 2);
					$USO_FEBRABAN_CNBB = ' ';
					$USO_FEBRABAN_CNBB = sprintf("% 8s",$USO_FEBRABAN_CNBB);
					fwrite($handle, $USO_FEBRABAN_CNBB, 8);
					fwrite($handle, $OCORRENCIAS ,10);	
					fwrite($handle, "\r\n");					
					*/

?>