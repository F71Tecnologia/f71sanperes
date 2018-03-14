<?php
if(!empty($decimo_terceiro)) {

// Parcela do Décimo Terceiro
switch($tipo_terceiro) {
	case 1:
	$mes_dt = '13';
	break;
	case 2:
	$mes_dt = '14';
	break;
	case 3:
	$mes_dt = '15';
	break;
}



// Movimentos de Décimo Terceiro
$qr_movimentos_dt = mysql_query("SELECT * FROM rh_movimentos_clt
								          WHERE id_clt = '$clt'
								          AND status = '1'
								          AND mes_mov = '$mes_dt'
								          AND ano_mov = '$ano'");
    while($row_movimento_dt = mysql_fetch_array($qr_movimentos_dt)) {
		  
		// Criando Array para Update em Movimentos
		$ids_movimentos_update_geral[] = $row_movimento_dt['id_movimento'];
		$ids_movimentos_estatisticas[] = $row_movimento_dt['id_movimento'];
		$ids_movimentos_update_individual[] = $row_movimento_dt['id_movimento'];
			  
		// Acrescenta os Movimentos de Crédito nos Rendimentos de DT
		if($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
			$movimentos_rendimentos += $row_movimento_dt['valor_movimento'];
			  
		// Acrescenta os Movimentos de Débito nos Descontos de DT
		} elseif($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
			$movimentos_descontos += $row_movimento_dt['valor_movimento'];
	    }
			  
	    // Acrescenta os Movimentos nas Bases de INSS e IRRF
	    $incidencias = explode(',', $row_movimento_dt['incidencia']);
			  
		foreach($incidencias as $incidencia) {
				  
			if($incidencia == 5020) { // INSS
				if($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
					$base_inss += $row_movimento_dt['valor_movimento'];
				} elseif($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
					$base_inss -= $row_movimento_dt['valor_movimento'];
				}
			}
				  
			if($incidencia == 5021) { // IRRF
				if($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
					$base_irrf += $row_movimento_dt['valor_movimento'];
				} elseif($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
					$base_irrf -= $row_movimento_dt['valor_movimento'];
				}
		    }
				  
	    }
		  
} // Fim dos Movimentos



// Calculando Décimo Terceiro
$Calc -> dt_data($tipo_terceiro, $row_clt['data_entrada'], $ano, $mes, $salario_limpo, $clt);
$meses                   = $Calc -> meses_trab;
$decimo_terceiro_credito = $Calc -> valor;

if($tipo_terceiro != 1) {
				  
	// INSS sobre DT
	if($row_clt['desconto_inss'] == '1') {
		$base_inss = 0;
	} else {
		if($tipo_terceiro == 2) {
			$base_inss += ($decimo_terceiro_credito * 2);
		} else {
			$base_inss += $decimo_terceiro_credito;
		}
	}
	
	$Calc   -> MostraINSS($base_inss, $data_inicio);
	$inss_dt = $Calc -> valor;
	$percentual_inss = (int)substr($Calc -> percentual, 2);
				
	// IRRF sobre DT
	if($tipo_terceiro == 2) {
		$base_irrf = ($decimo_terceiro_credito * 2) - $inss_dt;
	} else {
		$base_irrf = $decimo_terceiro_credito - $inss_dt;
	}
	
	$Calc   -> MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);
	$irrf_dt         = $Calc -> valor;
	$percentual_irrf = (int)substr($Calc -> percentual, 2);
	$faixa_irrf      = $Calc -> percentual;
	$fixo_irrf       = $Calc -> valor_fixo_ir;
	$ddir            = $Calc -> valor_deducao_ir_total;
	$filhos_irrf     = $Calc -> total_filhos_menor_21;
	
	// FGTS sobre DT
	$base_fgts = $base_inss;
	$fgts      = $base_fgts * 0.08;
		  
}



// Variáveis para Linha do Participante
$inss_completo = $inss_dt;
$irrf_completo = $irrf_dt;
$rendimentos   = $movimentos_rendimentos;
$descontos     = $movimentos_descontos;
$liquido       = $decimo_terceiro_credito + $rendimentos - $descontos - $inss_completo - $irrf_completo;

// Mais Variáveis para Update do Participante
$base          = $decimo_terceiro_credito;
$fgts_completo = $fgts;

// Mais Variáveis para Estatistica do Participante
$valor_mes          = $salario_limpo / 12;
$valor_proporcional = round($valor_mes * $meses, 2);

}
?>