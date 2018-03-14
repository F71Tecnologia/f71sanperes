<?php 

// Totalizadores
$salario_total           += $salario;
$rendimentos_total       += $rendimentos;
$descontos_total         += $descontos;
$liquido_total           += $liquido;
		
$decimo_terceiro_total   += $decimo_terceiro_credito;
$ferias_total            += $valor_ferias;
$ferias_desconto_total   += $desconto_ferias;
$rescisao_total          += $valor_rescisao;
$rescisao_desconto_total += $desconto_rescisao;
		
$inss_total              += $inss;
$inss_dt_total		     += $inss_dt;
$inss_ferias_total	     += $inss_ferias;
$inss_rescisao_total     += $inss_rescisao;
$inss_completo_total     += $inss_completo;
		
$irrf_total              += $irrf;
$irrf_dt_total		     += $irrf_dt;
$irrf_ferias_total	     += $irrf_ferias;
$irrf_rescisao_total     += $irrf_rescisao;
$irrf_completo_total     += $irrf_completo;

$fgts_total              += $fgts;
$fgts_dt_total		     += $fgts_dt;
$fgts_ferias_total	     += $fgts_ferias;
$fgts_rescisao_total     += $fgts_rescisao;
$fgts_completo_total     += $fgts_completo;
		
$vale_refeicao_total     += $vale_refeicao;
$familia_total 		     += $familia;
$maternidade_total 		 += $salario_maternidade;
$vale_transporte_total   += $vale_transporte;
$sindicato_total         += $sindicato;
		
$base_total		 += $base;
$base_inss_total	 += $base_inss + $base_inss_13_rescisao;
$base_inss_empresa       += (($base_inss + $base_inss_13_rescisao) * 0.2);
$base_inss_rat           += (($base_inss + $base_inss_13_rescisao) * $percentual_rat);
$base_inss_terceiros     += (($base_inss + $base_inss_13_rescisao) * 0.058);
$base_irrf_total	     += ($base_irrf - $ddir);
$base_fgts_total	     += $base_fgts;
$base_fgts_ferias_total	 += $base_fgts_ferias;
$ddir_total	             += $ddir;
$total_valor_adicional_noturno += $valor_adicional_noturno;	
$total_DSR += $DSR;	
$total_aux_distancia     += $desconto_aux_distancia;	
$total_valor_insalubridade_proporcional += $valor_insalubridade_proporcional;
$total_base_inss_nao_descontado += $base_inss_nao_descontado;


			
		
// Resetando Valores
unset($salario,
	  $salario_limpo,
	  $ferias,
	  $dias,
	  $dias_entrada,
	  $dias_evento,
	  $dias_rescisao,
	  $meses_evento,
	  $sinaliza_evento,
	  $dias_ferias,
	  $dias_faltas,
	  $meses,
	  $movimentos_rendimentos_dt,
	  $movimentos_descontos_dt,
	  $movimentos_rendimentos,
	  $movimentos_descontos,
	  $vale_transporte,
	  $vale_refeicao,
	  $rendimentos,
	  $descontos,
	  $inss,
	  $inss_dt,
	  $inss_ferias,
	  $inss_rescisao,
	  $inss_completo,
	  $base_inss,
            $base_inss_ferias,
	  $base_irrf,
	  $base_fgts,
	  $base_fgts_ferias,
	  $base_inss_dt,
	  $base_irrf_dt,
	  $ddir,
	  $irrf,
	  $irrf_dt,
	  $irrf_ferias,
	  $irrf_rescisao,
	  $irrf_completo,
	  $fgts,
	  $fgts_dt,
	  $fgts_ferias,
	  $fgts_rescisao,
	  $fgts_completo,
	  $familia,
	  $familia_mes_anterior,
	  $filhos_familia,
	  $salario_maternidade,
	  $sindicato,
	  $decimo_terceiro_credito,
	  $decimo_terceiro_debito,
	  $valor_ferias,
	  $desconto_ferias,
	  $valor_rescisao,
	  $desconto_rescisao,
	  $ids_movimentos_update_individual,
	  $update_movimentos_individual, 
	  $adicional_noturno_mes,
	  $desconto_aux_distancia, 
	  $DSR,
	  $insalubridade,
	  $valor_insalubridade_proporcional,
	  $base_inss_nao_descontado,
        $insalubridade_20,
        $insalubridade_40,
        $base_inss_13_rescisao);
?>