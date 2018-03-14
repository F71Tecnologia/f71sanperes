<?php

      // Movimentos do Participante para Update
	  settype($update_movimentos_individual, 'array');
	   settype($array_movimentos_xml, 'array');
	  
	  // Incluindo os Movimentos no Update do Participantes
      if(!empty($ids_movimentos_update_individual)) {
		 
	      $ids_movimentos_update_individual = implode(',', $ids_movimentos_update_individual);
		 
	      foreach($movimentos_tabela as $codigo) {
		       $qr_movimentos_individuais  = mysql_query("SELECT
		   												     SUM(valor_movimento) 
		   											        FROM rh_movimentos_clt 
													       WHERE cod_movimento = '$codigo'
													         AND id_movimento IN($ids_movimentos_update_individual)");
               $valor = mysql_result($qr_movimentos_individuais,0);
			   
			 
			  $array_update_movimentos_individual[$codigo] = formato_banco($valor);///usado na inclusao de movimento no xml
			   
		       $update_movimentos_individual["$codigo"] = "a".$codigo." = '".formato_banco($valor)."'";
	      }
		  
	  }
	  
	
	   	
	   // Incluindo os Cálculos Separados no Update do Participantes		
	   $update_movimentos_individual['5019']  = "a5019  = '".formato_banco($sindicato)."'";
	   $update_movimentos_individual['5020']  = "a5020  = '".formato_banco($inss)."'";
	   $update_movimentos_individual['5021']  = "a5021  = '".formato_banco($irrf)."'";
	   $update_movimentos_individual['5022']  = "a5022  = '".formato_banco($familia)."'";
	   $update_movimentos_individual['5029']  = "a5029  = '".formato_banco($decimo_terceiro_credito)."'";
	   $update_movimentos_individual['5030']  = "a5030  = '".formato_banco($irrf_dt)."'";
	   $update_movimentos_individual['5031']  = "a5031  = '".formato_banco($inss_dt)."'";
	   $update_movimentos_individual['5035']  = "a5035  = '".formato_banco($inss_ferias)."'";
	   $update_movimentos_individual['5036']  = "a5036  = '".formato_banco($irrf_ferias)."'";
	   $update_movimentos_individual['5037']  = "a5037  = '".formato_banco($valor_ferias)."'";
	   $update_movimentos_individual['5044']  = "a5044  = '".formato_banco($fgts_ferias)."'";
	   $update_movimentos_individual['5049']  = "a5049  = '".formato_banco($ddir)."'";
	   $update_movimentos_individual['6005']  = "a6005  = '".formato_banco($salario_maternidade)."'";
	   $update_movimentos_individual['7001']  = "a7001  = '".formato_banco($vale_transporte)."'";
	   $update_movimentos_individual['8003']  = "a8003  = '".formato_banco($vale_refeicao)."'"; 
	   $update_movimentos_individual['80002'] = "a80002 = '".$dias_faltas."'";
	   $update_movimentos_individual['50222'] = "a50222 = '".$filhos_familia."'";
	   $update_movimentos_individual['50492'] = "a50492 = '".$filhos_irrf."'";
	   
	   // Organizando o Update do Participante
	   array_multisort($update_movimentos_individual);
	   
   

	  $update_movimentos_individual = implode(', ', $update_movimentos_individual);
	

// Criando Update do Participante
$update_participantes = "UPDATE rh_folha_proc SET cod = '".$row_clt['campo3']."', nome = '".$row_clt['nome']."', status_clt = '".$row_clt['status']."', dias_trab = '".$dias."', meses = '".$meses."', id_banco = '".$row_clt['banco']."', agencia = '".$row_clt['agencia']."', conta = '".$row_clt['conta']."', cpf = '".$row_clt['cpf']."', salbase = '".formato_banco($base)."', sallimpo = '".formato_banco($salario_limpo)."', sallimpo_real = '".formato_banco($salario)."', rend = '".formato_banco($rendimentos)."', desco = '".formato_banco($descontos)."', inss = '".formato_banco($inss)."', t_inss = '".$faixa_inss."', imprenda = '".formato_banco($irrf)."', t_imprenda = '".$faixa_irrf."', d_imprenda = '".$fixo_irrf."', fgts = '".formato_banco($fgts)."', base_irrf = '".formato_banco($base_irrf)."', salfamilia = '".formato_banco($familia)."', salliquido = '".formato_banco($liquido)."', valor_ferias = '".formato_banco($valor_ferias)."', valor_pago_ferias = '".formato_banco($desconto_ferias)."', inss_ferias = '".formato_banco($inss_ferias)."', ir_ferias = '".formato_banco($irrf_ferias)."', fgts_ferias = '".formato_banco($fgts_ferias)."', valor_dt = '".formato_banco($decimo_terceiro_credito)."', inss_dt = '".formato_banco($inss_dt)."', ir_dt = '".formato_banco($irrf_dt)."', valor_rescisao = '".formato_banco($valor_rescisao)."', valor_pago_rescisao = '".formato_banco($desconto_rescisao)."', inss_rescisao = '".formato_banco($inss_rescisao)."', ir_rescisao = '".formato_banco($irrf_rescisao)."', ".$update_movimentos_individual.", ids_movimentos = '".$ids_movimentos_update_individual."', status = '2', hora_trabalhada = '$horas' , hora_noturna = '$horas_noturnas', adicional_noturno = '$adicional_noturno_mes', horas_atraso = '$horas_atraso' WHERE id_folha_proc = '$row_participante[0]' AND alteracao_ajax = 0 LIMIT 1;\r\n";





?>