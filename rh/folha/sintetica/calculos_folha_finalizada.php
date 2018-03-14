<?php

settype($ids_movimentos_estatisticas,'array');

// Definindo Classes
$Calc = new calculos();
$Trab = new proporcional();

$clt = $row_participante['id_clt'];


// Consulta de Dados do Participante
$qr_clt  = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($qr_clt);
		  
// Buscando a Atividade do Participante e o Salário Limpo
$qr_curso       = mysql_query("SELECT salario, nome FROM curso WHERE id_curso = '$row_clt[id_curso]'");
@$salario_limpo = mysql_result($qr_curso, 0, 0);




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

// Entrada
include('entrada.php');
		  
// Férias
include('ferias.php');

// Faltas
$qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND cod_movimento = '8000' AND mes_mov = '$mes' AND ano_mov = '$ano'");
while($faltas = mysql_fetch_assoc($qr_faltas)) {
	$ids_movimentos_estatisticas[] = $faltas['id_movimento'];
	$ids_movimentos_update_geral[] = $faltas['id_movimento'];
	$dias_faltas	              += $faltas['qnt'];
}

// Dias Trabalhados, Valor por Dia e Salário
$dias = 30 - $dias_entrada - $dias_evento - $dias_ferias - $dias_faltas;
if($dias < 0) { $dias = 0; }
$Trab     -> calculo_proporcional($salario_limpo, $dias);
$valor_dia = $Trab -> valor_dia;
$salario   = $Trab -> valor_proporcional;
		  
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
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt
									   WHERE id_clt = '$clt'
									   AND status = '1'
									   AND lancamento = '2'
									   UNION
							  SELECT * FROM rh_movimentos_clt
									   WHERE id_clt = '$clt'
									   AND status = '5'
									   AND lancamento = '1'
									   AND mes_mov = '$mes'
									   AND ano_mov = '$ano'
									   AND cod_movimento != '8000'");
									   
									   
while($row_movimento = mysql_fetch_array($qr_movimentos)) {
	
	// Criando Array para Update em Movimentos
	if($row_movimento['lancamento'] == 1) {
		$ids_movimentos_update_geral[]  = $row_movimento['id_movimento'];
	}
	$ids_movimentos_estatisticas[]      = $row_movimento['id_movimento'];
	$ids_movimentos_parcial[]           = $row_movimento['id_movimento'];
	$ids_movimentos_update_individual[] = $row_movimento['id_movimento'];
	
	// Acrescenta os Movimentos de Crédito nos Rendimentos e no Salário Base
	if($row_movimento['tipo_movimento'] == 'CREDITO') {
		if(!in_array($row_movimento['cod_movimento'], $movimentos_base)) {
			$base += $row_movimento['valor_movimento'];
		}
		$movimentos_rendimentos += $row_movimento['valor_movimento'];
		
				  
	// Acrescenta os Movimentos de Débito nos Descontos e no Salário Base
	} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
		if(!in_array($row_movimento['cod_movimento'], $movimentos_base)) {
			$base -= $row_movimento['valor_movimento'];
		}
		$movimentos_descontos += $row_movimento['valor_movimento'];
	}
			  
	// Acrescenta os Movimentos nas Bases de INSS e IRRF
	$incidencias = explode(',', $row_movimento['incidencia']);
				  
	foreach($incidencias as $incidencia) {
	
		if($incidencia == 5020) { // INSS
			if($row_movimento['tipo_movimento'] == 'CREDITO') {
				$base_inss += $row_movimento['valor_movimento'];
			} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
				$base_inss -= $row_movimento['valor_movimento'];
			}
		}
					  
		if($incidencia == 5021) { // IRRF
			if($row_movimento['tipo_movimento'] == 'CREDITO') {
				$base_irrf += $row_movimento['valor_movimento'];
			} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
				$base_irrf -= $row_movimento['valor_movimento'];
			}
		}
		
		if($incidencia == 5023) { // FGTS
			if($row_movimento['tipo_movimento'] == 'CREDITO') {
				$base_fgts += $row_movimento['valor_movimento'];
			} elseif($row_movimento['tipo_movimento'] == 'DEBITO') {
				$base_fgts -= $row_movimento['valor_movimento'];
			}
		}
					  
	}
			  
	// Vale Refeição (Débito)
	if($row_movimento['cod_movimento'] == '8006' and empty($decimo_terceiro)) {
		$base_refeicao = $row_movimento['valor_movimento'];
	    $vale_refeicao = $base_refeicao * 0.20;
	}
	
	// Salário Família Mês Anterior
	if($row_movimento['cod_movimento'] == '50220') {
		$familia_mes_anterior = $row_movimento['valor_movimento'];
	}
		  
} // Fim dos Movimentos



// Salário Maternidade
if($row_evento['cod_status'] == 50) {
	$salario_maternidade     = $valor_dia * $dias_evento;
	$movimentos_rendimentos += $salario_maternidade;
	$base_inss 				+= $salario_maternidade;
	$base_irrf 				+= $salario_maternidade;
	$base_fgts 				+= $salario_maternidade;
}

	

// INSS
if($row_clt['desconto_inss'] == '1') {
	
	if($row_clt['tipo_desconto_inss'] == 'isento') {
		$base_inss = 0;
		$inss	   = 0;
	} elseif($row_clt['tipo_desconto_inss'] == 'parcial') {
		//$base_inss = abs($row_clt['salario_outra_empresa'] - $salario) * 0.11;
		$inss 	   = $row_clt['valor_desconto_inss'];
	}
	
} else {
		
	$Calc -> MostraINSS($base_inss, $data_inicio);
	$inss            = $Calc -> valor;
	$percentual_inss = (int)substr($Calc -> percentual, 2);
	$faixa_inss      = $Calc -> percentual;

}

//$inss_completo = $inss + $inss_ferias;
$inss_completo = $inss;
	
		
// IRRF
$base_irrf  -= $inss;
		  
$Calc -> MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);
$irrf            = $Calc -> valor;
$percentual_irrf = str_replace('.',',',$Calc -> percentual * 100);
//$percentual_irrf = (int)substr($Calc -> percentual, 2);
$faixa_irrf      = $Calc -> percentual;
$fixo_irrf       = $Calc -> valor_fixo_ir;
$ddir            = $Calc -> valor_deducao_ir_total;
$filhos_irrf     = $Calc -> total_filhos_menor_21;
		
$irrf_completo   = $irrf + $irrf_ferias;

if(empty($irrf_completo)) {
	$base_irrf = NULL;
	$ddir	   = NULL;
}


		
// FGTS
$fgts          = $base_fgts * 0.08;
$fgts_completo = $fgts + $fgts_ferias;



// Salário Familia
if(empty($decimo_terceiro) and $dias_ferias != 30) {
	
	$base_familia = $base - $familia_mes_anterior;
			
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

	
		
// Vale Transporte (Débito)
if($row_clt['transporte'] == '1' and empty($decimo_terceiro) and $regiao != '10') {

	$qr_vale_transporte = mysql_query("SELECT vale.valor_total_func
									     FROM rh_vale_r_relatorio vale 
								   INNER JOIN rh_vale_protocolo protocolo 
										   ON vale.id_protocolo = protocolo.id_protocolo
									    WHERE vale.id_func = '$clt'
									      AND vale.valor_total_func != ''
									      AND protocolo.mes = '$mes'
									      AND protocolo.ano = '$ano'");
   @$vale_transporte = mysql_result($qr_vale_transporte,0);
	
	$limite_transporte = $salario_limpo * 0.06;
	
	if($vale_transporte > $limite_transporte) {
		$vale_transporte = $limite_transporte;
	}
	    
}
	


// Contruibuição Sindical
$qr_sindicato  = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$row_clt[rh_sindicato]'");
$row_sindicato = mysql_fetch_array($qr_sindicato);

if($row_sindicato['mes_desconto'] == $mes_int) {
	$sindicato = $valor_dia;
}



// Rendimentos
$rendimentos = $movimentos_rendimentos + $valor_ferias;
		
// Descontos
$descontos = $movimentos_descontos + $desconto_ferias + $vale_refeicao + $vale_transporte + $sindicato;

// Salário Liquido
$liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;



// Fim da verificação de 13º ou Rescisão
}
?>