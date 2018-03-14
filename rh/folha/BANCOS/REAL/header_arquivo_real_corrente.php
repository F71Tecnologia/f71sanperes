<?
					////////////////////////////////////
					// REGISTRO DE HEADER DE ARQUIVO //
					//////////////////////////////////
					
					/* DADOS DE CONTROLE */
					$BANCO = '356'; //CÓDIGO DO BANCO NA COMPENSAÇÃO
					$COD_BANCO = '275'; //CÓDIGO DO BANCO NA COMPENSAÇÃO
					$TIPO_REGISTRO = '0'; //VALOR PADRÃO PARA HEADER DE ARQUIVO
					$TIPO_REGISTRO_PADRAO = '3'; //VALOR PADRÃO PARA HEADER DE ARQUIVO
					$NOME_DO_BANCO = "BANCO REAL S.A ";
					//??CNAB 9 POSIÇÕES EM BRANCO BRANCOS
					
					/* DADOS DA EMPRESA */	
					/* INSCRIÇÃO */
					$TIPO_INSCRICAO_EMPRESA = '2'; //TIPO DE INSCRIÇÃO DA EMPRESA, COM VALOR PADRÃO 2 (CGC / CNPJ)									
					$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj']; //CNPJ DA EMPRESA
					$remover = array(".", "-", "/",",");
					$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
					$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);
					
					$NUMERO_CONTA = $row_banco['conta'];
					$remover = array(".", "-", "/",",");
					$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
					$NUMERO_CONTA = substr($NUMERO_CONTA, 0, 7);
					$NUMERO_CONTAF = sprintf("%07s",$NUMERO_CONTA);
					
					/* DADOS DA CONTA CORRENTE */
					//AGENCIA
					$AGENCIA = $row_banco['agencia'];					
 					$AGENCIA = substr($AGENCIA, 0, 4); 
					$AGENCIAF = sprintf("%05d",$AGENCIA);
					
					$str = $row_banco['agencia'];
					$ultimoDigitoAgencia = $str{strlen($str)-1};			
					$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;
					$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);
					
					//CONVÊNIO
					$CONVENIO_EMPRESA = $AGENCIA.$NUMERO_CONTAF.'PG'; //O CODIGO DE CONVÉNIO DA EMPRESA, CONSISTE NO NUMERO DA AGENCIA COM 4 DIGITOS, SEGUIDO DO NUMERO DA CONTA CORRENTE COM 7 DIGITOS, MAIS A LITERAL FIXA "PG"
					$CONVENIO_EMPRESA = sprintf("% -20s",$CONVENIO_EMPRESA);					
										
					//CONTA
					$NUMERO_CONTA = $row_banco['conta'];
					$remover = array(".", "-", "/",",");
					$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
					$NUMERO_CONTA = substr($NUMERO_CONTA, 0, 7);
					$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTA);
					
					$str = $row_banco['conta'];
					$ultimoDigitoConta = $str{strlen($str)-1};			
					$DV_CONTA_CORRENTE  = $ultimoDigitoConta;									
					$DV_CONTA_CORRENTE = sprintf("%0d",$DV_CONTA_CORRENTE);
					
					$DV_AGENCIA_CONTA_CONVENIO = $DV_AGENCIA_EMPRESA;
					
					$NOME_EMPRESA = $row_master['razao'];
					$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
					
					$NOME_BANCO = $row_banco['nome'];
					$NOME_BANCO = sprintf("% -30s",$NOME_BANCO);
					
					//??CNAB
					
					$CODIGO_REMESSA = '1'; //VALOR FIXO PARA ARQUIVO DE REMESSA (CLIENTE -> BANCO)
					$DATA_GERACAO_ARQUIVO = date('dmy');
					$HORA_GERACAO_ARQUIVO = date('His');
					
					//SEQUENCIA PARA O ARQUIVO DETALHES_REAL_CORRENTE
					$numSequenciaCorrenteREAL = 1;
					
					$numSequenciaCorrente = 1;
					$QUANT_REGISTROS = $numSequenciaCorrente;
					$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);
					
					$LAYOUT_ARQUIVO = '040'; //VALOR PADRÃO
					$DENSIDADE_DE_GRAVACAO = '1600 ';
					$TIPO_DE_GRAVACAO = 'BPI';
					$numSequenciaCorrente = $numSequenciaCorrente+1;				
					
					
					// INICIO DA IMPRESSAO
					$handle = fopen('BANCOS/REAL/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt", "w+");
					
					fwrite($handle, $TIPO_REGISTRO,1);
					
					$RESERVADO_BANCO = ' ';
					$RESERVADO_BANCO = sprintf("% 8s",$RESERVADO_BANCO);
					fwrite($handle, $RESERVADO_BANCO,8);
					fwrite($handle, $TIPO_REGISTRO, 1);
					fwrite($handle, $TIPO_REGISTRO_PADRAO, 1);
					
					$CREDITOSCC = sprintf("%- 15s","CREDITOS C/C");
					fwrite($handle, $CREDITOSCC, 15);
					
					$NOME_DA_EMPRESA = sprintf("%- 20s","INSTITUTO SORRINDO PARA A VIDA");
					fwrite($handle, $NOME_DA_EMPRESA, 20);
					
					$RESERVADO_BANCO = sprintf("% 30s"," ");
					fwrite($handle, $RESERVADO_BANCO,30);
					fwrite($handle, $COD_BANCO, 3);					
					fwrite($handle, $NOME_DO_BANCO, 15);
					
					fwrite($handle, $DATA_GERACAO_ARQUIVO, 6);
					fwrite($handle, $DENSIDADE_DE_GRAVACAO, 5);
					fwrite($handle, $TIPO_DE_GRAVACAO, 3);
					
					$RESERVADO_BANCO = sprintf("% 86s"," ");
					fwrite($handle, $RESERVADO_BANCO,86);
					
					$NUMERO_DA_LINHA = sprintf("%06s",'1');					
					fwrite($handle, $NUMERO_DA_LINHA, 6);
					fwrite($handle, "\r\n");

?>