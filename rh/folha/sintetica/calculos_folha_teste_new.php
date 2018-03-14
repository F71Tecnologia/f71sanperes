<?php

$salario_limpo  = $row_participante['salario'];
$tipo_insalubr  = $row_participante['tipo_insalubridade'];
$qnt_salInsalu  = $row_participante['qnt_salminimo_insalu'];

// 13º Salário
include('dt.php');


// Rescisão
if(empty($decimo_terceiro)) {
	include('rescisao.php');
}



// Quando não for 13º nem Rescisão segue os Cálculos da Folha
if(empty($decimo_terceiro) and empty($num_rescisao)) {
		
		// Eventos
                include('eventos.php'); 
                
		// Férias
		include('ferias.php');
		
		
		// Faltas
		$qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND id_Mov IN(62,232) AND mes_mov = '$mes' AND ano_mov = '$ano'");
		while($faltas = mysql_fetch_assoc($qr_faltas)) {
			$ids_movimentos_estatisticas[] = $faltas['id_movimento'];
			$ids_movimentos_update_geral[] = $faltas['id_movimento'];
			$dias_faltas	              += $faltas['qnt'];
			$ids_movimentos_parcial[]      = $faltas['id_movimento'];
		}
                
                
                
		//////////////////////////////////////////////////
		// Dias Trabalhados, Valor por Dia e Sal?rio /////
                //////////////////////////////////////////////////
                
                /////////////////////////////////////////////////////
		// Contratado depois do Início da Folha ////////////
		////////////////////////////////////////////////////
		if($row_participante['data_entrada'] >= $data_inicio and $row_participante['data_entrada'] <= $data_fim) {
			
                    $inicio = explode('-',$row_participante['data_entrada']);
                    $fim    = explode('-', $data_fim);	
                    
                    
                    if($inicio[1] == '02' and ($inicio[2] == 28 or $inicio[2] == 29)){ 
                        $dia_inicio = 30 ;
                        
                    }  else {
                          $dia_inicio = $inicio[2] ;
                    }
                    
                  
                    $dias_entrada =  (30 - $dia_inicio) + 1;    
                    $dias         = $dias_entrada - $dias_evento - $dias_ferias ;                       

                    // Calculando Dias da Entrada
                    //$dias_entrada = abs(30 - (int)floor((strtotime($fim) - strtotime($inicio)) / 86400) - 1);	
                    $novo_clt = 1;
                        
		}else {
                    /////////////////////////////////////////
                    unset($dias_entrada);
                    $dias = $total_dias_folha  - $dias_evento - $dias_ferias ;
                }
          
                
            
		if($dias < 0) { $dias = 0; }
		$Trab     -> calculo_proporcional($salario_limpo, $dias);
		$valor_dia = $Trab -> valor_dia;
		$salario   = $Trab -> valor_proporcional;
                
                
                
		////////////////////////////////////////////////////////
		//////// INSALUBRIDADE  ////////
		////////////////////////////////////////////////////////
		 if ($row_participante['insalubridade'] == 1 and ($dias - $dias_faltas) > 0) {
                
                            $qnt_dias_insalubridade = $dias - $dias_faltas;
                            $INSALUBRIDADE_         = $CALC_NEW->Calcula_insalubridade($qnt_dias_insalubridade, $tipo_insalubr,$qnt_salInsalu);
                            $INFO_MOV               = $CALC_NEW->get_info_movimento($INSALUBRIDADE_['cod']);
                            $verifica_insalubridade = mysql_num_rows(mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov IN('$INSALUBRIDADE_[id_mov]',200) AND mes_mov ='$mes' AND ano_mov = '$ano' AND status = 1"));

                    
                            if($verifica_insalubridade == 0){   
                            /*   mysql_query("INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg) 
                                           VALUES 
                                           ('$clt','$regiao','$projeto','$mes','$ano','{$INFO_MOV['id_mov']}','{$INFO_MOV['cod']}','{$INFO_MOV['categoria']}','{$INFO_MOV['nome']}',NOW(),'{$_COOKIE[logado]}','{$INSALUBRIDADE_['valor_proporcional']}','{$INSALUBRIDADE_['percentual']}','1','5020,5021,5023',1, 1)") or die(mysql_error());
                          */  }                           
                
                 } else {
                     
                       ////VERIFICA SE EXISTE OU NÃO O MOVIMENTO DE INSALUBRIDADE E ADICIONA CASO NÃO TENHA
                        $qr_verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (56,235,200) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ") ;
                        $row_insalubridade       = mysql_fetch_assoc($qr_verifica_insalubridade);                         
                        $verifica_insalubridade  = mysql_num_rows($qr_verifica_insalubridade);

                        if($verifica_insalubridade !=0){                                        
                           // mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
                        }
                 }                
		unset($novo_clt);
		
	
	
		// Definindo Variáveis importantes para Base de Cálculos
		$base	   = $salario;
		$base_inss = $salario;
		$base_irrf = $salario;
		$base_fgts = $salario;
                
		// Movimentos que não incidem no Salário Base
		$movimentos_base = array('7003','8006','9500');
		
		// Movimentos Proporcionais
		$movimentos_proporcionais = array('6006','6007','8004','9000');
		
		
		// Movimentos
                
                ///ALTERAÇÃO PARA NÃO PEGAR OS MOVIMENTOS DO TIPO SEMPRE NAS FÉRIAS
                ///alteração de quando tiver licença
                /// if(((isset($dias_ferias) and $dias_ferias < 30) or !isset($dias_ferias)) and $sem_mov_sempre != true ){ 
                
             
                   if(((isset($dias_ferias) and $dias_ferias <= 30) or !isset($dias_ferias)) and $regiao == 48 ){// POG PARA VIAMÃO
                       
                       if($dias_ferias == 30){
                           $condicao = 'AND id_mov NOT IN(56,235)';
                       } else {
                           $condicao = '';
                       }
                       
                         $sql_mov_sempre = "SELECT * FROM rh_movimentos_clt
                                            WHERE id_clt = '$clt'
                                            AND status = '1'
                                            AND lancamento = '2' 
                                            $condicao UNION";                      
                         
                         
                   }else if((isset($dias_ferias) and $dias_ferias < 30) or !isset($dias_ferias)  ){
                    
                    
                                  
                    $sql_mov_sempre = " SELECT * FROM rh_movimentos_clt
                                            WHERE id_clt = '$clt'
                                            AND status = '1'
                                            AND lancamento = '2' UNION";
                }else {
                    $sql_mov_sempre ='';
                }
                
                   $qr_movimentos = mysql_query(" $sql_mov_sempre
                                            SELECT * FROM rh_movimentos_clt
                                            WHERE id_clt = '$clt'
                                            AND status = '1'
                                            AND lancamento = '1'
                                            AND mes_mov = $mes
                                            AND ano_mov = $ano
                                            AND cod_movimento != '8000'");
               
               while($row_movimento = mysql_fetch_array($qr_movimentos)) {
					
                                        
		  if($row_participante['status'] == 20){                      
                     if($row_movimento['id_mov'] != 56) continue;                      
                  }
                  
                       //CONDIÇÂO PARA QUANDO O CLT TIVER 30  DIAS DE LICENÇA NÂO APARECER OS MOVIMENTOS DE DSR E ADICIONAL NOTURNO
                    if(($sinaliza_evento == true and $dias == 0) and
                            ($row_movimento['id_mov'] ==  66 
                            or $row_movimento['id_mov'] ==  61
                            or $row_movimento['id_mov'] ==  199)){
                    continue;
                    }
		
			
                   /////PEGANDO A INSALUBRIDADE PARA GRAVAR NO REGISTRO DO CLT NA rh_folha_proc
                   if($row_movimento['id_mov'] == 56){                       
                       $insalubridade_20 = $row_movimento['valor_movimento'];                       
                   }
                   if($row_movimento['id_mov'] == 209){                       
                       $insalubridade_40 = $row_movimento['valor_movimento'];                       
                   }
                    
                   
                   
			// Criando Array para Update em Movimentos
			if($row_movimento['lancamento'] == 1) {
				$ids_movimentos_update_geral[]  = $row_movimento['id_movimento'];
			}
			$ids_movimentos_estatisticas[]      = $row_movimento['id_movimento'];
			$ids_movimentos_parcial[]          = $row_movimento['id_movimento'];
			$ids_movimentos_update_individual[] = $row_movimento['id_movimento'];
			
			// Acrescenta os Movimentos de Crédito nos Rendimentos e no Salário Base
			if($row_movimento['tipo_movimento'] == 'CREDITO') {
				if(!in_array($row_movimento['cod_movimento'], $movimentos_base)) {
					$base += $row_movimento['valor_movimento'];
				}
				$movimentos_rendimentos += $row_movimento['valor_movimento'];
						  
			// Acrescenta os Movimentos de Débito nos Descontos e no Salário Base
			} elseif($row_movimento['tipo_movimento'] == 'DEBITO' or $row_movimento['tipo_movimento'] == 'DESCONTO') {
				if(!in_array($row_movimento['cod_movimento'], $movimentos_base)) {
					$base -= $row_movimento['valor_movimento'];
				}
				$movimentos_descontos += $row_movimento['valor_movimento'];
			}
				
					  
			// Acrescenta os Movimentos nas Bases de INSS e IRRF
			$incidencias = explode(',', $row_movimento['incidencia']);			
		
			
				if(in_array(5020,$incidencias)) { // INSS	
				
					
					if($row_movimento['tipo_movimento'] == 'CREDITO') {
						$base_inss += $row_movimento['valor_movimento'];
						
					} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {						
						$base_inss -= $row_movimento['valor_movimento'];
					}
					
				}
							  
				if(in_array(5021,$incidencias)) { // IRRF
					if($row_movimento['tipo_movimento'] == 'CREDITO') {
						$base_irrf += $row_movimento['valor_movimento'];
					} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
						$base_irrf -= $row_movimento['valor_movimento'];
					}
				}
				
				if(in_array(5023,$incidencias)) { // FGTS
					if($row_movimento['tipo_movimento'] == 'CREDITO') {
						$base_fgts += $row_movimento['valor_movimento'];
					} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
						$base_fgts -= $row_movimento['valor_movimento'];
					}					
				}
							  
			
					  
			// Vale Refeição (Débito)
			if($row_movimento['cod_movimento'] == '8003' and empty($decimo_terceiro)) {
				$base_refeicao = $row_movimento['valor_movimento'];
				$vale_refeicao = $base_refeicao * 0.20;
			}
			
			// Salário Família Mês Anterior
			if($row_movimento['cod_movimento'] == '50220') {
				$familia_mes_anterior = $row_movimento['valor_movimento'];
			}
				  
		} 
                // Fim dos Movimentos
	
                
                // Salário Maternidade
		if($row_evento['cod_status'] == 50) {
			$salario_maternidade     = $valor_dia * $dias_evento;
			$movimentos_rendimentos += $salario_maternidade;
			$base_inss 				+= $salario_maternidade;
			$base_irrf 				+= $salario_maternidade - $salario;
			$base_fgts 				+= $salario_maternidade - $salario;
		}
		
                
                
                
                
		
                $INSS_ = $CALC_NEW->Calcula_INSS($base_inss, $row_participante['tipo_contratacao'], $row_participante['desconto_inss'],$row_participante['tipo_desconto_inss'],$row_participante['salario_outra_empresa'],$row_participante['desconto_outra_empresa']  );
                $inss               = $INSS_['valor_inss']; 
                $percentual_inss    = $INSS_['percentual_inss']; 
		$faixa_inss         = $INSS_['faixa_inss']; 
		//$inss_completo = $inss + $inss_ferias;
		$inss_completo      = $inss;
                
		////////////////////////////////////////
		//////// CÁLCULO DO IRRF ///////////////
		////////////////////////////////////////
		$base_irrf  -= $inss;
               	
		
                $IRRF_  = $CALC_NEW->Calcula_IRRF($base_irrf, $clt, $row_participante['tipo_contratacao']);   
		$irrf            =  $IRRF_['valor_irrf'] ;
		$percentual_irrf = str_replace('.',',',$IRRF_['percentual_irrf'] * 100);
		$faixa_irrf      = $IRRF_['percentual_irrf'];
		$fixo_irrf       = $IRRF_['valor_parcela_deducao_irrf'];
		$ddir            = $IRRF_['valor_deducao_dep_ir_total'];
		$filhos_irrf     = $IRRF_['qnt_dependente_irrf'];
		$deducao_irrf    = $ddir;	
		$irrf_completo   = $irrf + $irrf_ferias;
              
		if(empty($irrf_completo)) {
			$base_irrf = NULL;
			$ddir	   = NULL;
		}
		
		
                
				
		////////////////////////////////////////
		//////// CÁLCULO DO FGTS ///////////////
		////////////////////////////////////////
		$fgts          = $base_fgts * 0.08;
		$fgts_completo = $fgts + $fgts_ferias;
                
		
		///////////////////////////////////////////////////
		//////// CÁLCULO DO SALÁRIO FAMÍLIA ///////////////
		//////////////////////////////////////////////////                
                 if( $row_evento['cod_status'] != 20 and $dias > 0 and $dias_ferias != 30 and empty($decimo_terceiro)){ //condição para não pagar salario familia quando estiver sob licença medica e não tiver dias trabalhados
                 
                    $FAMILIA_           = $CALC_NEW->Calcula_salariofamilia($salario_limpo - $familia_mes_anterior, $clt,$row_participante['tipo_contratacao']);
                    $filhos_familia     = $FAMILIA_['qnt_dependente'];
                    $familia            = $FAMILIA_['valor_sal_familia'];
                    $fixo_familia       = $FAMILIA_['fixo_familia'];
                 } 
                
                 
		///////////////////////////////////////////////////
		////////VALE TRANSPORTE (DÉBITO) /////////////////
		//////////////////////////////////////////////////
		if($row_participante['transporte'] == '1' and empty($decimo_terceiro) and $regiao != '10' and $row_participante['status'] != 20) {
		
              
		/*	$qr_vale_transporte = mysql_query("SELECT vale.valor_total_func
												 FROM rh_vale_r_relatorio vale 
										   INNER JOIN rh_vale_protocolo protocolo 
												   ON vale.id_protocolo = protocolo.id_protocolo
												WHERE vale.id_func = '$clt'
												  AND vale.valor_total_func != ''
												  AND protocolo.mes = '$mes'
												  AND protocolo.ano = '$ano'");
                    
                        
                     
		       @$vale_transporte = mysql_result($qr_vale_transporte,0);
			
                       
                     if($_COOKIE['logado'] == 87 ){
                     
                  //   $verifica_vale = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND mes_mov = '$mes' AND ano_mov = '$ano'  AND")
                         
                         
                                      
                     $qr_vale_transporte = mysql_query("SELECT B.valor FROM rh_vale as A
                                                        LEFT JOIN  rh_tarifas as B
                                                        ON A.id_tarifa1 = B.id_tarifas                                                       
                                                        WHERE A.id_clt = '$clt'");
                     
                     @$vale_transporte = mysql_result($qr_vale_transporte,0);
                     //echo $salario_limpo .'<br>';
                     
                     
                     }
                	$limite_transporte = $salario_limpo * 0.06;
			
			if($vale_transporte > $limite_transporte) {
				$vale_transporte = $limite_transporte;
			}
                        
                     */
                    
                    ///ALTERADO DIA 28/03/2013
                    $vale_transporte = $salario_limpo * 0.06;
                        //VERIFICA SE TRABALHOU OS 30 DIAS 
                        if($dias < 30){
                           $vale_dia = $vale_transporte / 30;
                           $vale_transporte = round($vale_dia * $dias,2);
                        }
                        
                     
                        
				
		}
			
	
		
		///////////////////////////////////////////////////
		//////// CONTRIBUIÇÃO SINDICAL ////////////////////
		//////////////////////////////////////////////////
               if(!empty($row_participante['rh_sindicato']) and $row_participante['ano_contribuicao'] != $ano and ($dias_evento != 30)) {  
                    
                
                $data_inicio_mk = explode('-',$data_inicio);
                $data_fim_mk 	= explode('-',$data_fim);
                $data_entrada   = explode('-',$row_participante['data_entrada']);

                $data_inicio_mk = mktime(0,0,0,$data_inicio_mk[1],$data_inicio_mk[2],$data_inicio_mk[0]);
                $data_fim_mk	= mktime(0,0,0,$data_fim_mk[1],$data_fim_mk[2],$data_fim_mk[0]);
                $data_entrada	= mktime(0,0,0,$data_entrada[1]+1,$data_entrada[2],$data_entrada[0]);


                $qr_sindicato  = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$row_participante[rh_sindicato]'");
                $row_sindicato = mysql_fetch_array($qr_sindicato);

                if($row_sindicato['mes_desconto'] == $mes_int) {

                        $sindicato = $valor_dia;


                } elseif(($data_entrada >= $data_inicio_mk) and ($data_entrada <= $data_fim_mk) and $row_sindicato['mes_desconto'] <= $mes_int){


                        //CONDIÇÃO IMPLEMENTADA NO DIA 07/052012		
                        //pegando a folha do mes anterior
                        $qr_verifica_folha  	= mysql_query("SELECT * FROM `rh_folha` WHERE regiao = '$row_folha[regiao]' AND projeto = '$row_folha[projeto]' AND mes = ($mes-1)  AND ano <= $ano ORDER BY ano DESC");
                        $row_verifica_folha 	= mysql_fetch_assoc($qr_verifica_folha); 
                        //verificando se o clt está na folha anterior
                        $verifica_folha_proc 	= mysql_num_rows(mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$row_verifica_folha[id_folha]' AND id_clt = '$clt'"));
                        if($verifica_folha_proc == 1) {

                        $sindicato = $valor_dia;
                        }

                   }
                   
                }
		/*	$sindicato = $valor_dia;
		}
		*/
		
		
		// Rendimentos
		$rendimentos = $movimentos_rendimentos + $valor_ferias;
				
		// Descontos
		$descontos = $movimentos_descontos + $desconto_ferias + $vale_refeicao + $vale_transporte + $sindicato ;
		
               
		// Salário Liquido
		if(empty($ferias)) {
                    
                    ////CONDIÇÃO PARA QUEM TIVER SOB LICENÇA MÉDICA PARA ZERAR O TOTAL LIQUIDO,
                    //POIS QUANDO SE ESTÁ DE LICENÇA, A EMPRESA NÃO PAGA E SIM O INSS,
                    //PORÉM PRECISA CONSTAR NO SEFIP POR TEM QUE VIR NA FOLHA PARA EFEITO DE INFORMAÇÃO
                    //OBS. Não possui desconto de IRRF
                 /*     if($row_clt['status'] == 20){
                        
                    
                
                      
                      $irrf_completo = 0;
                      $irrf = 0;
                      $descontos = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
                      $liquido   =   $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
                        
                      
                        $verifica_mov_licenca = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_mov = 233 AND mes_mov = $mes  AND ano_mov= $ano  AND id_clt = '$clt' AND status = 1");
                    
                       if(mysql_num_rows($verifica_mov_licenca) == 0){
                            
                          
                            $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = 233");
                            $row_mov = mysql_fetch_assoc($qr_mov);
                           
                            
                            
                            mysql_query("INSERT INTO rh_movimentos_clt   (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg) 
                                                                          VALUES 
                                                                            ('$clt','$regiao','$projeto','$mes','$ano','$row_mov[id_mov]','$row_mov[cod]','$row_mov[categoria]','$row_mov[descicao]',NOW(),'$_COOKIE[logado]','$descontos','','1','',1, 1) ");
                            $ultimo_id = mysql_insert_id();
                            $ids_movimentos_estatisticas[]      = $ultimo_id;
                            $ids_movimentos_parcial[]           = $ultimo_id;
                            $ids_movimentos_update_individual[] = $ultimo_id;
                            unset($ultimo_id);
                        }
                        
                        
                    
                        
                    }else {
                    $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
                    }
                      * */
                      
                    $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
                    
                    
                    
                } else{
                    
                    if( $dias_ferias == 0){  
                        
                             $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
                             
                      }elseif($dias_ferias <= 30 and $regiao == 48 ) { 
                          
                          $liquido     = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia ;
                             
                    }elseif($dias_ferias < 30 ) { 
                             
                             $liquido     = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia ;
                    } else {
                        $liquido = 0;
                    }
                
                }
                // Fim da verificação de 13º ou Rescisão
                
  
}
?>