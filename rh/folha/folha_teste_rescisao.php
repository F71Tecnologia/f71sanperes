<?php
       /////BUSCANDO A RECIS�O
                    $qr_recisao = mysql_query("SELECT *, DATE_FORMAT(data_proc, '%Y-%m-%d') as data_proc2,
                                               IF(motivo = 65, aviso_valor,'') as aviso_pg_funcionario,
                                               IF(motivo != 65, aviso_valor,'') as aviso_credito,
                                               IF(motivo = 64, a479,'') as multa_a479,
                                               IF(motivo = 63, a480,'') as multa_a480
                                               FROM rh_recisao WHERE id_clt = '$folha[id_clt]' and status = 1");
                    $rescisao  = mysql_fetch_assoc($qr_recisao);                 
                             
                    
                    
                       if(substr($rescisao['data_proc2'],0,10) >= '2013-04-04'){

                                $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao
                                FROM rh_movimentos_rescisao as A 
                                INNER JOIN
                                rh_movimentos as B
                                ON A.id_mov = B.id_mov
                                WHERE A.id_clt = '$rescisao[id_clt]' 
                                AND A.id_rescisao = '$rescisao[id_recisao]' 
                                AND A.status = 1") or die(mysql_error());
                                while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  

                                        $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];   
                                }
                                
                                
                                $gratificacao       = $movimentos[52];     
                                $adicional_noturno  = $movimentos[55];
                                $hora_extra         = $movimentos[56];
                                $dsr                = $movimentos[58];
                                $diferenca_salarial = $movimentos[80];
                                $ajuda_custo        = $movimentos[82];
                                $vale_transporte    = $movimentos[107];
                                $vale_refeicao      = $movimentos[108];
                                
                                $pensao_alimenticia    = $movimentos[100];
                                $adiantamento_salarial = $movimentos[101];
                                $desconto_vale_transporte = $movimentos[106];
                                $desconto_vale_alimentacao = $movimentos[109];
                                
                           
                            } else {
                                
                            

                                $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND mes_mov = 16 AND status = 1");
                                while($row_movimento = mysql_fetch_assoc($qr_movimento)){      
                                    switch($row_movimento['id_mov']){

                                        case 198: $vale_transporte = $row_movimento['valor_movimento'];
                                            break;
                                        case 151: $vale_refeicao        = $row_movimento['valor_movimento'];
                                            break;
                                        case 14:  $diferenca_salarial = $row_movimento['valor_movimento'];
                                        break;
                                        case 76: $outros = $row_movimento['valor_movimento'];
                                            break;
                                    }
                                }                               
                                
                               
                                $gratificacao       =  $rescisao['gratificacao'];
                                $adicional_noturno  = $rescisao['adicional_noturno'];
                                $hora_extra         = $rescisao['hora_extra'];
                                $dsr                = $rescisao['dsr'];
                                $ajuda_custo        = $rescisao['ajuda_custo'];       
                                $desconto_vale_transporte = $rescisao['desconto_vale_transporte'];
                                $pensao_alimenticia  = $rescisao['pensao_alimenticia_15'] + $rescisao['pensao_alimenticia_20'] + $rescisao['pensao_alimenticia_30'];
                            }
                                  
                                
                        $qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '");      
                       
                        $movimentos_folha[$folha['id_clt']]['CREDITO'] = array( 'SALDO DE SAL�RIO'                 => $rescisao['saldo_salario'],
                                                                                'GRATIFICA��ES'                         => $gratificacao,
                                                                                'ADICIONAL DE INSALUBRIDADE'     => $rescisao['insalubridade'],
                                                                                'ADICIONAL NOTURNO'                     => $adicional_noturno,
                                                                                'HORAS EXTRAS'                          => $hora_extra,    
                                                                                'DSR'                                   => $dsr,
                                                                                'MULTA ART. 477'                        => $rescisao['a477'],
                                                                                'MULTA ART. 479/CLT'                    => $rescisao['multa_a479'],
                                                                                'SAL�RIO FAM�LIA'                       => $rescisao['salario_familia'],
                                                                                '13� SAL�RIO PROPORCIONAL'              => $rescisao['dt_salario'],
                                                                               'F�RIAS PROPORCIONAIS'                  => $rescisao['ferias_pr'],
                                                                               'F�RIAS VENCIDAS'                       => $rescisao['ferias_vencidas'],
                                                                               '1/3 CONSTITUCIONAL <br> DE F�RIAS'     => $rescisao['umterco_fv'] + $rescisao['umterco_fp'],
                                                                               'AVISO PR�VIO'                          => $rescisao['aviso_credito'],
                                                                               '13� SAL�RIO (Aviso-Pr�vio Indenizado)' => $rescisao['terceiro_ss'],                                           
                                                                               'F�RIAS EM DOBRO'                       => $rescisao['fv_dobro'],
                                                                               '1/3 F�RIAS EM DOBRO'                   => $rescisao['um_terco_ferias_dobro'],
                                                                               'DIFEREN�A SALARIAL'                    => $diferenca_salarial,
                                                                               'AJUDA DE CUSTO'                        => $rescisao['ajuda_custo'],
                                                                               'VALE TRANSPORTE'                       => $vale_transporte,
                                                                               'VALE REFEI��O'                         => $vale_refeicao,
                                                                               'LEI 12 506'                             => $rescisao['valor_lei_12_506'],
                                                                               'AJUSTE DE SALDO DEVEDOR'               => $rescisao['arredondamento_positivo'],
                                                                               'Reembolso Vale Transporte:'            => $reembolso_vt,
                                            
                                );
                        
                        $movimentos_folha[$folha['id_clt']]['DEBITO'] = array('PENS�O ALIMENT�CA'                             => $pensao_alimenticia,
                                                                                'ADIANTAMENTO SALARIAL'                         => $adiantamento_salarial,
                                                                                'ADIANTAMENTO 13� TERCEIRO'                     => '',
                                                                                'MULTA ART.480'                                 => $rescisao['multa_a480'],
                                                                                'EMPR�STIMO EM CONSIGNA��O'                     => '',
                                                                                'VALE TRANSPORTE'                               => $rescisao['desconto_vale_transporte'],
                                                                                'VALE ALIMENTA��O'                              => $desconto_vale_alimentacao,
                                                                                'PREVID�NCIA SOCIAL <br> (INSS SALDO DE SAL�RIO)' => $rescisao['inss_ss'],
                                                                                'IRRF <br>SALDO DE SAL�RIO'                     => $rescisao['ir_ss'],
                                                                                'AVISO PR�VIO <br> PAGO PELO FUNCION�RIO'       => $rescisao['aviso_pg_funcionario'],
                                                                                'PREVID�NCIA SOCIAL 13� SAL�RIO <BR> (INSS 13� SAL�RIO)' => $rescisao['inss_dt'],
                                                                                'IRRF <br> 13� SAL�RIO'                         => $rescisao['ir_dt'],
                                                                                'DEVOLU��O DE CR�DITO INDEVIDO'                 => $rescisao['devolucao'],
                                                                                'OUTROS'                                        => $rescisao['outros'],
                                                                                'FALTAS'                                        => $faltas,
                            );
                        
                      ////C�LCULOS DAS BASES  
                      $movimentos_folha[$folha['id_clt']]['BASE INSS'] +=   $rescisao['saldo_salario'] + $rescisao['insalubridade'] +  $gratificacao 
                                                                            + $adicional_noturno + $rescisao['hora_extra'] + $dsr 
                                                                            + $rescisao['dt_salario'] + $rescisao['terceiro_ss'] +
                                                                            $rescisao['aviso_credito'] + $rescisao['ferias_pr'] +
                                                                            $rescisao['ferias_vencidas'] + $rescisao['umterco_fv'] + $rescisao['umterco_fp']
                                                                            + $rescisao['fv_dobro'] + $rescisao['um_terco_ferias_dobro'] + $rescisao['valor_lei_12_506'];// + $rescisao['aviso_credito'];
              
                     if($rescisao['ir_dt'] != 0  or $rescisao['ir_ss'] != 0){
                      $movimentos_folha[$folha['id_clt']]['BASE IRRF'] +=  $movimentos_folha[$folha['id_clt']]['BASE INSS'] - $rescisao['ir_ss'] - $rescisao['ir_dt'];
                     }
                ?>
       
                
            
