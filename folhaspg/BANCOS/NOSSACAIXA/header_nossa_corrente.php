<?
					///////////////////////////////////
					//	REGISTRO HEADER DE ARQUIVO	//
					/////////////////////////////////
					
					$BANCO = '341';
					$LOTE_SERVICO = '0000';
					$TIPO_REGISTRO = '0';
					//??BRANCOS X(6)
					$LAYOUT_ARQUIVO = '080';

					
					$TIPO_INSCRICAO_EMPRESA = '2';
					$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
					$remover = array(".", "-", "/",",");
					$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
					$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);	
					//?BRANCOS X(20)
					
					$AGENCIA = $row_banco['agencia'];					
 					$AGENCIA = substr($AGENCIA, 0, 4); 
					$AGENCIA = sprintf("%05d",$AGENCIA);
					
					//?BRANCOS X(01)
					$NUMERO_CONTA = $row_banco['conta'];
					$remover = array(".", "-", "/",",");
					$NUMERO_CONTAF = str_replace($remover, "", $NUMERO_CONTA);
					$NUMERO_CONTAF = substr($NUMERO_CONTAF, 0, 6);				//estava dando problema dia 09/06/2009
					$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);
					//?BRANCOS X(01)
					$DAC_AGENCIA_CONTA_DEBITADA = '0';
					
					$NOME_EMPRESA = $row_master['razao'];
					$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
					
					$NOME_BANCO = $row_banco['nome'];
					$NOME_BANCO = sprintf("% -30s",$NOME_BANCO);					
					//?BRANCOS X(10)
					$CODIGO_REMESSA = '1';
					$DATA_GERACAO_ARQUIVO = date('dmY');
					$HORA_GERACAO_ARQUIVO = date('His');										
					$ZERO = '000000000';
					$DENSIDADE_DE_GRAVACAO = '0';
					$DENSIDADE_DE_GRAVACAO = sprintf("%05d",$DENSIDADE_DE_GRAVACAO);
					//?BRANCOS X(69)
										
					$handle = fopen('BANCOS/ITAU/CONTA_CORRENTE/'.$CONSTANTE.'_'.'CORRENTE'."_".$DD."_".$MM."_".$ANO.".txt", "w+");
					
					fwrite($handle, $BANCO,3);
					fwrite($handle, $LOTE_SERVICO,4);
					fwrite($handle, $TIPO_REGISTRO, 1);
					$BRANCOS = ' ';
					$BRANCOS = sprintf("% 6s", $BRANCOS);
					fwrite($handle, $BRANCOS, 6);
 					fwrite($handle, $LAYOUT_ARQUIVO, 3);
					fwrite($handle, $TIPO_INSCRICAO_EMPRESA, 1);
					fwrite($handle, $NUMERO_INSCRICAO_EMPRESA, 14);	
					$BRANCOS = ' ';
					$BRANCOS = sprintf("%20s", $BRANCOS);
					fwrite($handle, $BRANCOS, 20);										
					fwrite($handle, $AGENCIA, 5);
					$BRANCOS = ' ';
					fwrite($handle, $BRANCOS, 1);
					fwrite($handle, $NUMERO_CONTA, 12);
					$BRANCOS = ' ';
					fwrite($handle, $BRANCOS, 1);
					fwrite($handle, $DAC_AGENCIA_CONTA_DEBITADA, 1);
					fwrite($handle, $NOME_EMPRESA, 30);
					fwrite($handle, $NOME_BANCO, 30);
					$BRANCOS = ' ';
					$BRANCOS = sprintf("% 10s", $BRANCOS);
					fwrite($handle, $BRANCOS, 10);										
					fwrite($handle, $CODIGO_REMESSA, 1);
					fwrite($handle, $DATA_GERACAO_ARQUIVO, 8);
					fwrite($handle, $HORA_GERACAO_ARQUIVO, 6);
					fwrite($handle, $ZERO, 9);					
					fwrite($handle, $DENSIDADE_DE_GRAVACAO, 5);
					$BRANCOS = ' ';
					$BRANCOS = sprintf("% 69s", $BRANCOS);
					fwrite($handle, $BRANCOS, 69);
					fwrite($handle, "\r\n");
						
					///////////////////////////////
					//REGISTRO DE HEADER DE LOTE//
					/////////////////////////////

					$BANCO = '341'; //VALOR CONSTANTE
					$LOTE_SERVICO = '0001';
					$TIPO_REGISTRO = '1';
					$OPERACAO = 'C';	
					$SERVICO = '30'; //TIPO DE PAGAMENTO
					$FORMA_LANCAMENTO = '01'; //CREDITO EM CONTA CORRENTE
					
					$LAYOUT_LOTE = '040';
					$TIPO_INSCRICAO_EMPRESA = '2';									
					$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
					$remover = array(".", "-", "/",",");
					$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
					$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);							
					$AGENCIA = $row_banco['agencia'];
 					$CODIGO_AGENCIA_EMPRESA = substr($AGENCIA, 0, 4); 
					$CODIGO_AGENCIA_EMPRESA = sprintf("%05d",$CODIGO_AGENCIA_EMPRESA);												
					
					$NUMERO_CONTA = $row_banco['conta'];
					$remover = array(".", "-", "/",",");					 
					$NUMERO_CONTAF = str_replace($remover, "", $NUMERO_CONTA);
					$NUMERO_CONTAF = substr($NUMERO_CONTAF, 0, 6);						//estava dando erro 09/06/2009
					$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);
					$DAC_AGENCIA_CONTA_DEBITADA = '0';
					
					$NOME_EMPRESA = $row_master['razao'];
					$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
					
					$FINALIDADE_LOTE = ' ';
					$FINALIDADE_LOTE = sprintf("% -30s",$FINALIDADE_LOTE);
					
					$HISTORICO_CC = '';
					$HISTORICO_CC = sprintf("% 10s" , $HISTORICO_CC);
					
					$resultEmpresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_user[id_regiao]'");
					$rowEmpresa = mysql_fetch_array($resultEmpresa);
					
					//ENDEREÇO DA EMPRESA
					$LOGRADOURO_EMPRESA = $rowEmpresa['endereco'];
					$remover = array(".", "-", "/",",");
					$LOGRADOURO_EMPRESA = str_replace($remover, "", $LOGRADOURO_EMPRESA);	
					$LOGRADOURO_EMPRESA = sprintf("% -30s",$LOGRADOURO_EMPRESA);
					
					$NUMERO_EMPRESA = '';
					$NUMERO_EMPRESA = sprintf("%05d",$NUMERO_EMPRESA);
					
					$COMPLEMENTO_EMPRESA = '';
					$COMPLEMENTO_EMPRESA = sprintf("% -15s",$COMPLEMENTO_EMPRESA);
					
					
					$CIDADE_EMPRESA =  '';
					$CIDADE_EMPRESA = sprintf("% -20s",$CIDADE_EMPRESA);
					
					$CEP_EMPRESA = '';
					$CEP_EMPRESA = sprintf("%08d",$CEP_EMPRESA);					

					$ESTADO_EMPRESA = '';
					$ESTADO_EMPRESA = sprintf("% 2s",$ESTADO_EMPRESA);					
					
					$OCORRENCIAS = ' ';
					$OCORRENCIAS = sprintf("% 10s",$OCORRENCIAS);
					
					$handle = fopen('BANCOS/ITAU/CONTA_CORRENTE/'.$CONSTANTE.'_'.'CORRENTE'."_".$DD."_".$MM."_".$ANO.".txt", "a");

					fwrite($handle, $BANCO,3);
					fwrite($handle, $LOTE_SERVICO,4);
					fwrite($handle, $TIPO_REGISTRO, 1);					
					fwrite($handle, $OPERACAO, 1);					
					fwrite($handle, $SERVICO, 2);
					fwrite($handle, $FORMA_LANCAMENTO, 2);
					fwrite($handle, $LAYOUT_LOTE, 3);
					$BRANCO = ' ';
					fwrite($handle, $BRANCO, 1);
					fwrite($handle, $TIPO_INSCRICAO_EMPRESA, 1);
					fwrite($handle, $NUMERO_INSCRICAO_EMPRESA, 14);
					$BRANCO = ' ';
					$BRANCO = sprintf("% 20s",$BRANCO);
					fwrite($handle, $BRANCO, 20);
					fwrite($handle, $CODIGO_AGENCIA_EMPRESA, 5);
					$BRANCO = ' ';
					fwrite($handle, $BRANCO, 1);					
					fwrite($handle, $NUMERO_CONTA, 12);
					$BRANCO = ' ';
					fwrite($handle, $BRANCO, 1);
					fwrite($handle, $DAC_AGENCIA_CONTA_DEBITADA, 1);					
					fwrite($handle, $NOME_EMPRESA, 30);
					
					fwrite($handle, $FINALIDADE_LOTE, 30);
					fwrite($handle, $HISTORICO_CC, 10);
					fwrite($handle, $LOGRADOURO_EMPRESA, 30);					
					fwrite($handle, $NUMERO_EMPRESA, 5);
					fwrite($handle, $COMPLEMENTO_EMPRESA, 15);
					fwrite($handle, $CIDADE_EMPRESA, 20);
					fwrite($handle, $CEP_EMPRESA, 8);
					fwrite($handle, $ESTADO_EMPRESA, 2);
					$BRANCO = ' ';
					$BRANCO = sprintf("% 8s",$BRANCO);
					fwrite($handle, $BRANCO, 8);
					fwrite($handle, $OCORRENCIAS ,10);										
					fwrite($handle, "\r\n");																		

?>