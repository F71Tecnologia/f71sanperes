<?php
// Consulta de Dados do Participante
$qr_clt  = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($qr_clt);

// Buscando a Atividade do Participante e o Salário Limpo TIPO DE INSALUBRIDADE
$qr_curso       = mysql_query("SELECT salario, nome, tipo_insalubridade, qnt_salminimo_insalu, periculosidade_30 FROM curso WHERE id_curso = '$row_clt[id_curso]'") or die(mysql_error());
//@$salario_limpo = mysql_result($qr_curso, 0, 0);
$row_curso      = mysql_fetch_assoc($qr_curso);
$salario_limpo  = $row_curso['salario'];
$tipo_insalubr  = $row_curso['tipo_insalubridade'];
$qnt_salInsalu  = $row_curso['qnt_salminimo_insalu'];

//BUSCANDO HORARIO DO CLT
$qr_horario  = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '{$row_clt['rh_horario']}'");
$row_horario = mysql_fetch_assoc($qr_horario);
$hora_mensal = $row_horario['horas_mes'];

$tipo_falta = array(1 => 'HORAS', 2 => 'DIAS');

// 13º Salário
include('dt.php');


// Rescisão
if(empty($decimo_terceiro)) {
	include('rescisao.php');
}

// Quando não for 13º nem Rescisão segue os Cálculos da Folha
if(empty($decimo_terceiro) and empty($num_rescisao)) {
		
		//Eventos
                include('eventos.php');
                
                
                /*******MÉTODO QUE SUBSTITUIU O INCLUDE ACIMA*****************/
                /*******SE DÉ MERDA DESCOMENTA A LINHA DE CIMA****************/
                $mes_referente = $ano . "-" . $mes;
                
                //OBJETO EVENTO
                /*$obj_evento = new Eventos();
                $obj_evento->validaEventoForFolha($clt, $mes_referente, $row_folha['data_inicio'], $row_folha['data_fim']);
                $inicio = $obj_evento->inicio;
                $fim = $obj_evento->fim;
                $dias_evento = $obj_evento->dias_evento;
                $msg_15_dias = $obj_evento->msg_15_dias;
                $msg_evento = $obj_evento->msg_evento;
                $evento = $obj_evento->evento;
                $sinaliza_evento = $obj_evento->sinaliza_evento;
                */
                
                /*************************************************************/
                
                
                
                  //Férias
                //include('ferias.php');                
                /** Método substituto do include de férias */                
                $InfoFerias         = $objFerias->getFeriasFolha($clt,$row_folha['data_inicio'], $row_folha['data_fim']);
                $ferias             = $InfoFerias['ferias'];
                $base_inss_ferias   = $InfoFerias['base_inss'];
                $base_fgts_ferias   = $InfoFerias['base_fgts'];
		$inss_ferias        = $InfoFerias['inss'];
		$irrf_ferias        = $InfoFerias['irrf'];
		$fgts_ferias        = $InfoFerias['fgts'];
                $valor_ferias       = $InfoFerias['valor_ferias'] ;
                $desconto_ferias    = $InfoFerias['desconto_ferias'];
                $dias_ferias        = $InfoFerias['dias_ferias'];
             
              
		
		
		// Faltas
		$qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND id_Mov IN(62,232) AND mes_mov = '$mes' AND ano_mov = '$ano'");
		while($faltas = mysql_fetch_assoc($qr_faltas)) {
			$ids_movimentos_estatisticas[] = $faltas['id_movimento'];
			$ids_movimentos_update_geral[] = $faltas['id_movimento'];
			$dias_faltas	              += $faltas['qnt'];
                        $ids_movimentos_parcial[]      = $faltas['id_movimento'];
                        $qnt_faltas                    = $faltas['qnt'];
			$tipo_qnt_faltas              += $faltas['tipo_qnt'];
		}
                
              		//////////////////////////////////////////////////
		// Dias Trabalhados, Valor por Dia e Sal?rio
                /////////////////////////////////////////////////
                
                /////////////////////////////////////////////////////
		// Contratado depois do Início da Folha ////////////
		////////////////////////////////////////////////////
		if($row_clt['data_entrada'] >= $data_inicio and $row_clt['data_entrada'] <= $data_fim) {
			
                    $inicio = explode('-',$row_clt['data_entrada']);
                    $fim    = explode('-', $data_fim);	                    
                    
                    if($inicio[1] == '02' and ($inicio[2] == 28 or $inicio[2] == 29)){ 
                        $dia_inicio = 30 ;                        
                    }  else {
                          $dia_inicio = $inicio[2] ;
                    }
                    
                  
                    $dias_entrada =  (30 - $dia_inicio)+1; 
                    $dias         = $dias_entrada - $dias_evento - $dias_ferias ;                       

                    // Calculando Dias da Entrada
                    //$dias_entrada = abs(30 - (int)floor((strtotime($fim) - strtotime($inicio)) / 86400) - 1);					
                    mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_clt = '$row_clt[id_clt]' AND id_mov = 56 AND mes_mov = '$mes' AND ano_mov = '$ano' LIMIT 1");
                    $novo_clt = 1;
                        
		}else {
                    
                    $dias = $total_dias_folha  - $dias_evento - $dias_ferias ;                   
                }
          
            
		if($dias < 0) { $dias = 0; }
		$Trab     -> calculo_proporcional($salario_limpo, $dias);
		$valor_dia = $Trab -> valor_dia;
		$salario   = $Trab -> valor_proporcional;
                
                
		//////// INSALUBRIDADE  ////////
		////////////////////////////////////////////////////////
		 if ($row_clt['insalubridade'] == 1 and ($dias - $dias_faltas) > 0 and $row_curso['tipo_insalubridade'] != 0) {
                            
                            //CALCULANDO
                            $qnt_dias_insalubridade = $dias;
                            $INSALUBRIDADE_         = $CALC_NEW->Calcula_insalubridade($qnt_dias_insalubridade, $tipo_insalubr,$qnt_salInsalu);
                            $INFO_MOV               = $CALC_NEW->get_info_movimento($INSALUBRIDADE_['cod']);
                            $valor_insalubridade    = ($novo_clt == 1) ? $INSALUBRIDADE_['valor_proporcional'] : $INSALUBRIDADE_['valor_integral'];                            
                            
                            //VERIFICANDO SE FOI LANÇADO
                            $verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov IN('$INSALUBRIDADE_[id_mov]',200) AND ((mes_mov ='$mes' AND ano_mov = '$ano')   OR lancamento = 2 )  AND status = 1");
                            $row_verifica_insalu    = mysql_fetch_assoc($verifica_insalubridade);
                              
                            if(mysql_num_rows($verifica_insalubridade) == 0){   //SE não existir o movimento de insalubridade lançado, aqui  adiciona
                            
                                    mysql_query("INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg) 
                                               VALUES 
                                               ('$clt','$regiao','$projeto','$mes','$ano','{$INFO_MOV['id_mov']}','{$INFO_MOV['cod']}','{$INFO_MOV['categoria']}','{$INFO_MOV['nome']}',NOW(),'{$_COOKIE[logado]}','{$valor_insalubridade}','{$INSALUBRIDADE_['percentual']}','1','5020,5021,5023',1, 1)") or die(mysql_error());
                  
                                               } elseif($valor_insalubridade  !=  ((float)$row_verifica_insalu['valor_movimento'])){ //caso o valor calculado seja diferente do que está gravado no sistema
                                
                              mysql_query("UPDATE rh_movimentos_clt SET valor_movimento = '{$valor_insalubridade}' WHERE id_movimento = '{$row_verifica_insalu['id_movimento']}' AND id_clt = '{$clt}' LIMIT 1");
                                                         
                            }                
                      
                 } else {
                     
                       //CONDIÇÂO PARA REMOVER A INSALUBRUIDADE CASO SEJ DESMARCAR NO CADASTRO DE CLT
                        $qr_verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (56,235,200) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ") ;
                        $row_insalubridade       = mysql_fetch_assoc($qr_verifica_insalubridade);                         
                        $verifica_insalubridade  = mysql_num_rows($qr_verifica_insalubridade);

                        if($verifica_insalubridade !=0){                                        
                           mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
                        }
                 }  
                //fim INSALUBRIDADE
                 
                 
                 
		//////// periculosiadae  ////////
		//////////////////////////////////////////////////////// 
                 
              
		 if(($dias - $dias_faltas) > 0 and $row_curso['periculosidade_30'] == 1) {
                
                            $valor_periculosidade           = (($salario * 0.30)/30) * ($dias - $dias_faltas);
                            $id_mov                         = 57;
                            $cod_mov                        = 6007;
                            $verifica_periculosidade        = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov IN(57) AND mes_mov ='$mes' AND ano_mov = '$ano' AND status = 1");
                            $row_verifica_periculosidade    = mysql_fetch_assoc($verifica_periculosidade);                          
                            $INFO_MOV                       = $CALC_NEW->get_info_movimento($cod_mov); 
                            
                            if(mysql_num_rows($verifica_periculosidade) == 0){   //SE não existir o movimento de insalubridade lançado, aqui  adiciona
                            
                                mysql_query("INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg) 
                                               VALUES 
                                               ('$clt','$regiao','$projeto','$mes','$ano','{$INFO_MOV['id_mov']}','{$INFO_MOV['cod']}','{$INFO_MOV['categoria']}','{$INFO_MOV['nome']}',NOW(),'{$_COOKIE[logado]}','{$valor_periculosidade}','{$INFO_MOV['percentual']}','1','5020,5021,5023',1, 1)") or die(mysql_error());
   
                            } elseif($valor_periculosidade  != $row_verifica_periculosidade['valor_movimento']){ //caso o valor calculado seja diferente do que está gravado no sistema
                                
                                    mysql_query("UPDATE rh_movimentos_clt SET valor_movimento = '{$valor_periculosidade}' WHERE id_movimento = '{$row_verifica_periculosidade['id_movimento']}' AND id_clt = '{$clt}' LIMIT 1");
                            }                 
                      
                 } else {                     
                       ////VERIFICA SE EXISTE OU NÃO O MOVIMENTO DE INSALUBRIDADE E ADICIONA CASO NÃO TENHA
                        $qr_veri_periculosidade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (57) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ") ;
                        $row_insalubridade       = mysql_fetch_assoc($qr_veri_periculosidade);                         
                        $verifica_perculosidade  = mysql_num_rows($qr_veri_periculosidade);
                        if($verifica_perculosidade !=0){                                        
                         mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
                        }
                 }  
                ///FIM PERICULOSIDADE
               
                 
                 
                 
               
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
                       
                         $sql_mov_sempre = " SELECT * FROM rh_movimentos_clt
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
                /////////////////////////////////////////////////////////////////////
                   
		while($row_movimento = mysql_fetch_array($qr_movimentos)) {
					
                                        
		 
                     
                       if(($sinaliza_evento == true and $dias == 0)){
                            if($row_movimento['lancamento'] == 2) continue;  
                       }
                     
                 
                         /* CONdição substituida pela de cima
                          * 
                          *   if($row_evento['cod_status'] == 20){                      
                             if($row_movimento['lancamento'] == 2) continue;                      
                          }

                               //CONDIÇÂO PARA QUANDO O CLT TIVER 30  DIAS DE LICENÇA NÂO APARECER OS MOVIMENTOS DE DSR E ADICIONAL NOTURNO
                            if(($sinaliza_evento == true and $dias == 0) and
                                    ($row_movimento['id_mov'] ==  66 
                                    or $row_movimento['id_mov'] ==  61
                                    or $row_movimento['id_mov'] ==  199)){
                               continue;
                            }
                          * *
                          */
                 			
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
		                
                
             
		 ////////////////////////////////////////
		//////// CÁLCULO DO INSS ///////////////
		////////////////////////////////////////                 
                    $base_inss        += $base_inss_ferias;     
                    
                    $Calc -> MostraINSS($base_inss, $data_inicio);
                    $inss             = $Calc -> valor;
                    $percentual_inss  = (int)substr($Calc -> percentual, 2);
                    $faixa_inss       = $Calc -> percentual;   
                    $teto_inss        = $Calc->teto;

                    /* DESCONTO INSS OUTRA EMPRESA */
                    if($base_inss !=0){
                       if($row_clt['desconto_inss'] == '1' ) {                         
                                      if($row_clt['tipo_desconto_inss'] == 'isento') {
                                          $inss= 0;                             
                                      } elseif($row_clt['tipo_desconto_inss'] == 'parcial') {

                                          if(($row_clt['desconto_outra_empresa'] + $inss)  > $teto_inss){
                                              $inss =  $teto_inss  - $row_clt['desconto_outra_empresa'];
                                          }
                                      }                        
                              }
                    }
                    
		  $inss_completo = $inss;
			
         
                
                
                
		////////////////////////////////////////
		//////// CÁLCULO DO IRRF ///////////////
		////////////////////////////////////////
		$base_irrf  -= $inss;                
                if($base_irrf > 0){
                    $Calc -> MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);
                    $irrf            = $Calc -> valor;
                    $percentual_irrf = str_replace('.',',',$Calc -> percentual * 100);
                    $faixa_irrf      = $Calc -> percentual;
                    $fixo_irrf       = $Calc -> valor_fixo_ir;
                    $ddir            = $Calc -> valor_deducao_ir_total;
                    $filhos_irrf     = $Calc -> total_filhos_menor_21;
                    $deducao_irrf    = 	$ddir;	
                    $irrf_completo   = $irrf;	
                } else {
                    $base_irrf = 0;
                }
		
               
		
		
                
				
		////////////////////////////////////////
		//////// CÁLCULO DO FGTS ///////////////
		////////////////////////////////////////
		$fgts          = $base_fgts * 0.08;
		$fgts_completo = $fgts + $fgts_ferias;
		
		  
                
                
		
		///////////////////////////////////////////////////
		//////// CÁLCULO DO SALÁRIO FAMÍLIA ///////////////
		//////////////////////////////////////////////////
		
                 if( $row_evento['cod_status'] == 20 and $dias == 0){ //condição para não pagar salario familia quando estiver sob licença medica e não tiver dias trabalhados
                     $cont_salfamilia = 0;
                 } else {
                       $cont_salfamilia = 1;
                 }
     
                
                if(empty($decimo_terceiro) and $dias_ferias != 30 and $cont_salfamilia == 1) {
			
			$base_familia = ($salario_limpo + $movimentos_rendimentos) - $familia_mes_anterior;
					
			if(!empty($row_clt['id_antigo'])) {
				$referencia_familia = $row_clt['id_antigo']; 
			} else {
				$referencia_familia = $row_clt['id_clt'];
			}
			
                      
                                $Calc -> Salariofamilia($base_familia, $referencia_familia, $projeto, $data_inicio, $row_clt['tipo_contratacao']);
                                $filhos_familia = $Calc -> filhos_menores;
                                $familia        = $Calc -> valor;
                                $fixo_familia   = $Calc -> fixo;
                        
                                               
                        
			
		}
		
		
				
		///////////////////////////////////////////////////
		////////VALE TRANSPORTE (DÉBITO) /////////////////
		//////////////////////////////////////////////////
		if($row_clt['transporte'] == '1' and empty($decimo_terceiro) and $regiao != '10' and $row_clt['status'] != 20) {
		
               
		/*$qr_vale_transporte = mysql_query("SELECT vale.valor_total_func
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
               if(!empty($row_clt['rh_sindicato']) and $row_clt['ano_contribuicao'] != $ano and ($dias_evento != 30)  and !isset($ferias)) {

                    $qr_sindicato  = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$row_clt[rh_sindicato]'");
                    $row_sindicato = mysql_fetch_array($qr_sindicato);
                    
                    /* 
                     * VERIFICA O MÊS DE CONTRIBUIÇÃO E SE NÃO É O MÊS DE ADMISSÃO 
                     * CASO SEJAM ATENDIDAS,  VERIFICA SE FOI DESCONTADO A CONTRIBUIÇÃO, INCLUINDO-A CASO NÃO TENHA DESCONTADO
                    */                     
                    if( $mes_int >= $row_sindicato['mes_desconto'] and $novo_clt !=1) {

                        $verifica_ctrSindical = mysql_query("SELECT  a5019 FROM rh_folha_proc WHERE id_clt = {$clt} AND status = 3 AND ano={$ano} AND a5019 != '0.00' AND a5019 IS NOT NULL;");
                        if(mysql_num_rows($verifica_ctrSindical) == 0){
                            $sindicato = $valor_dia;
                        }
                    }
                }
           
           
           
           
		
		// Rendimentos
		$rendimentos = $movimentos_rendimentos + $valor_ferias;
				
		// Descontos
		$descontos = $movimentos_descontos + $desconto_ferias + $vale_refeicao + $vale_transporte + $sindicato ;
		
                $inss_completo = $inss + $inss_ferias;
                $irrf_completo = $irrf + $irrf_ferias;
               
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
                             
                    }elseif($dias_ferias <= 30 ) { 
                             
                             $liquido     = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia ;
                    } 
                
                }
                // Fim da verificação de 13º ou Rescisão
                
       
                //ZERANDO VARIAVEIS
                unset($novo_clt);  
}
$liquido = ($liquido<0) ? 0 : $liquido;