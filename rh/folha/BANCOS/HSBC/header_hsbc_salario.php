<?php
			$numeroSequencial = 1;
			/////////////////////////////////////////
			//	REGISTRO DE HEADER BANCO HSBC //
			///////////////////////////////////////
			$COD_REGISTRO = '0';	//VALOR CONSTANTE. 
			$COD_REMESSA = '1'; 	//VALOR CONSTANTE
			$LITERAL1 = "REMESSA";	//VALOR CONSTANTE
			$COD_SERVICO = '03';	//VALOR CONSTANTE			
			$LITERAL2 = "CREDITO C/C";//VALOR CONSTANTE
			$LITERAL2 = sprintf("% -15s",$LITERAL2);
			
			$IDENTIFICA = $row_banco['agencia'];
			$IDENTIFICA = substr($IDENTIFICA, 0, 4);
			$IDENTIFICA = sprintf("%05d",$IDENTIFICA);
			
			$RAZAO = '07050'; //Razão da Conta corrente da empresa
			$RAZAO = sprintf("%05d",$RAZAO);
			
			$EMPRESA = $row_banco['conta'];
			$EMPRESA = substr($EMPRESA, 0, 5);
			$EMPRESA = sprintf("%07d",$EMPRESA);
			
			$str = $row_banco['conta'];
			$ultimoDigitoConta = $str{strlen($str)-1};
			$NOBANCO = $ultimoDigitoConta;
			$NOBANCO = sprintf("% -1s",$NOBANCO);
			
			$ID = ' ';
			$RESERVA = ' ';
			
			$COD_EMPRESA = $row_banco['cod_convenio'];	//INSERIR CÓDIGO DO CLIENTE NO BANCO. PARA CADA CONTA, DEVE HAVER UM CÓDIGO
			$COD_EMPRESA = sprintf("%05d",$COD_EMPRESA);
			
			
			$NOME_EMPR = $row_master['razao'];
			$NOME_EMPR = sprintf("% -25s",$NOME_EMPR);
			
			$COD_BANCO = '237';	//VALOR CONSTANTE			
			
			$NOME_BCO = 'HSBC';	//VALOR CONSTANTE
			$NOME_BCO = sprintf("% -15s",$NOME_BCO);
			
			$DT_GRAVACAO = date('dmY');
			$DENSIDADE = '01600';
			$DENSIDADE = sprintf("%05s",$DENSIDADE);
			$LITERAL3 = 'BPI';	//VALOR CONSTANTE
			$DT_DEBITO = $d.$m.$a; //DATA DO DÉBITO, ENVIADO PELO FORMÁRIO
			$ID_MOEDA = ' ';
			$ID_SECULO = 'N';					
			
			$NUMERO_SEQ = sprintf("%06d", $numeroSequencial);
			
			$handle = fopen('BANCOS/HSBC/CONTA_SALARIO/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt", "w+");
			fwrite($handle, $COD_REGISTRO, 1);
			fwrite($handle, $COD_REMESSA, 1);
			fwrite($handle, $LITERAL1, 7);
			fwrite($handle, $COD_SERVICO, 2);
			fwrite($handle, $LITERAL2, 15);
			fwrite($handle, $IDENTIFICA, 5);
			fwrite($handle, $RAZAO, 5);
			fwrite($handle, $EMPRESA, 7);
			fwrite($handle, $NOBANCO, 1);
			fwrite($handle, $ID, 1);
			fwrite($handle, $RESERVA, 1);
			fwrite($handle, $COD_EMPRESA, 5);
			fwrite($handle, $NOME_EMPR, 25);
			fwrite($handle, $COD_BANCO, 3);
			fwrite($handle, $NOME_BCO, 15);
			fwrite($handle, $DT_GRAVACAO, 8);
			fwrite($handle, $DENSIDADE, 5);
			fwrite($handle, $LITERAL3, 3);
			fwrite($handle, $DT_DEBITO, 8);
			fwrite($handle, $ID_MOEDA, 1);
			fwrite($handle, $ID_SECULO, 1);
			$RESERVA = ' ';
			$RESERVA = sprintf("% 74s" ,$RESERVA);
			fwrite($handle, $RESERVA, 74);
			fwrite($handle, $NUMERO_SEQ, 6);								
			fwrite($handle, "\r\n");
?>