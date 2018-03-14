<?php
$restatus = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$dispensa'");

if($valor == '0,00') {
	$Curso -> MostraCurso($idcurso);
	$salario_base = $Curso -> salario;
} else {
	$valor = str_replace(',', '.', str_replace('.', '', $valor));
	$salario_base = $valor;
}

$salario_base_limpo = $salario_base;

// Trabalhando com as Datas
$data_exp = explode('-', $data_demissao);
$data_adm = explode('-', $data_entrada);

$dia_demissao = (int)$data_exp[2];
$mes_demissao = (int)$data_exp[1];
$ano_demissao = (int)$data_exp[0];

$dia_admissao = (int)$data_adm[2];
$mes_admissao = (int)$data_adm[1];
$ano_admissao = (int)$data_adm[0];

// Verificando se o funcionário tem 1 ano de contratação
if(date('Y-m-d') >= date('Y-m-d', strtotime("$data_entrada +1 year"))) {
	$um_ano = '1';
} else {
	$um_ano = '0';
}


if($mes_demissao == 2) {	
	$qnt_dias_mes =  cal_days_in_month(CAL_GREGORIAN, $mes_demissao, $ano_demissao);	
} else {
	$qnt_dias_mes  = 30;
}





//  60 = Com Justa Causa
//  61 = Sem Justa Causa
//  62 = Por outros motivos / 81 = Óbito
//  63 = Pedido de Dispensa Antes do Prazo
//  64 = Dispensa Sem Justa Causa Antecipado Fim Cont. Empregador
//  65 = Pedido de Dispensa
//  66 = Dispensa Sem Justa Causa Fim Cont. Empregador
// 101 = Afastado para Aposentaria

//   0 = NÃO 
//   1 = SIM 
//   2 = PAGA 
//   3 = DEPOSITADO

if($dispensa == 60) {
	
	$terceiro_ssF = 0;
	$t_ss 	= 1; // SALDO SALARIO
	$t_ap 	= 0; // AVISO PREVIO
	$t_fv 	= 1; // FERIAS VENCIDAS
	$t_fp 	= 0; // FERIAS PROPORCIONAIS
	$t_fa 	= 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 0; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 0; // FGTS MULTA 40
	$t_mu   = 0; // MULTA ART 479
	$cod_mov_fgts = 'H';
	$cod_saque_fgts = '02';

} elseif($dispensa == 61) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 1; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu = 0; // MULTA ART 479
	$cod_mov_fgts = '11';
	$cod_saque_fgts = '01';
	
	if($fator == 'empregado') {
		
		$t_f4 = 2; // FGTS MULTA 40
		$cod_mov_fgts = 'J';
		
		if($aviso == 'indenizado') {
			$t_ap = 2; // AVISO PREVIO (PAGA)
		}
		
	}
	
} elseif($dispensa == 62 or $dispensa == 81) {
	
	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa 	= 1; // FERIAS 1/3 ADICIONAL
	$t_13 	= 1; // DECIMO TERCEIRO
	$t_f8   = 1; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu = 1; // MULTA ART 479
	$cod_mov_fgts = '11';
	$cod_saque_fgts = '02';

} elseif($dispensa == 63) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 0	; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';

	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}
	
} elseif($dispensa == 64) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 0; // FGTS MULTA 40

if($fator == 'empregador') {
		$t_mu   = 1; // MULTA ART 479
} else {$t_mu   = 0; // MULTA ART 479
  } 
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';

} elseif($dispensa == 65) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 2; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';
	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}

} elseif($dispensa == 66) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';
	
} elseif($dispensa == 101) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 2; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '02';
	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}

}



// Movimentos
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt
									   WHERE id_clt = '$id_clt'
									   AND tipo_movimento = 'CREDITO'
									   AND status = '1'
									   AND lancamento = '2'");
while($row_movimento = mysql_fetch_array($qr_movimentos)) {
			  
	// Acrescenta os Movimentos nas Bases de INSS e IRRF
	$incidencias = explode(',', $row_movimento['incidencia']);
				  
	foreach($incidencias as $incidencia) {
	
		if($incidencia == 5020) { // INSS
			$salario_calc_inss += $row_movimento['valor_movimento'];
		}
					  
		if($incidencia == 5021) { // IRRF
			$salario_calc_IR   += $row_movimento['valor_movimento'];
		}
		
		if($incidencia == 5023) { // FGTS
			$salario_calc_FGTS += $row_movimento['valor_movimento'];
		}
					  
	}
	
	// Novo Salário Base + Todos os Movimentos
	if($valor == '0,00') {
		$salario_base += $row_movimento['valor_movimento'];
	}
		  
	$total_rendi += $row_movimento['valor_movimento'];
		  
	$array_codigos_rendimentos[] = $row_movimento['cod_movimento'];
	$array_valores_rendimentos[] = $row_movimento['valor_movimento'];
	  
}

if($array_valores_rendimentos == '') {
	$array_valores_rendimentos[] = '0';
}
// Fim dos Movimentos



/* Vale Refeição (Débito)
$qr_refeicao = mysql_query("SELECT * FROM rh_movimentos_clt
									 WHERE id_clt = '$id_clt'
									 AND status = '1'
									 AND lancamento = '2'
									 AND cod_movimento = '8006'
									 UNION
							SELECT * FROM rh_movimentos_clt
									 WHERE id_clt = '$id_clt'
									 AND status = '1'
									 AND lancamento = '1'
									 AND mes_mov = '$mes_demissao'
									 AND ano_mov = '$ano_demissao'
									 AND cod_movimento = '8006'");
while($row_refeicao = mysql_fetch_array($qr_refeicao)) {

	$vale_refeicao = $row_refeicao['valor_movimento'];
	$debito_vale_refeicao = $vale_refeicao * 0.20;
		  
} */



// Salário Família
$Calc -> Salariofamilia($salario_base,$id_clt,$idprojeto,$data_demissao,'2');
$total_menor	   = $Calc -> filhos_menores;
$valor_sal_familia = (($Calc -> valor) /$qnt_dias_mes) * $dias_trabalhados;



// Adicional Noturno
$Calc -> adnoturno($id_clt, '');
$valor_adnoturno = (($Calc -> valor) / $qnt_dias_mes) * $dias_trabalhados;



// Insalubridade / Periculosidade
$Calc -> insalubridade($id_clt,$data_demissao);
//$valor_insalubridade = (($Calc -> valor) / $qnt_dias_mes) * $dias_trabalhados;
$valor_insalubridade = $Calc -> valor;



// Hora Extra
$qr_hora_extra = mysql_query("SELECT SUM(valor_movimento) AS valor
									FROM rh_movimentos_clt 
								   WHERE id_clt = '$id_clt'
									 AND cod_movimento = '8080' 
									 AND mes_mov = '16' 
									 AND status = '1'");
$hora_extra = mysql_result($qr_hora_extra,0);



// Saldo de Salário e Faltas
$valor_salario_dia = $salario_base  / $qnt_dias_mes;
$data_base 		   = $data_demissao;
$valor_faltas	   = $valor_salario_dia * $faltas;
$saldo_de_salario  = $valor_salario_dia * $dias_trabalhados - $valor_faltas;      /// * $dias_trabalhados - $valor_faltas;


// Calculando Previdência
$Calc -> MostraINSS(($saldo_de_salario + $hora_extra), $data_base);
$previ_ss = $Calc -> valor;

if($t_ss == 1) {

	// Calculando INSS sobre Saldo de Salários
	$Calc -> MostraINSS($saldo_de_salario,$data_exp);
	$inss_saldo_salario = $Calc -> valor;
	//
	
	$base_irrf_saldo_salarios = $saldo_de_salario - $inss_saldo_salario - $previ_ss + $valor_insalubridade;
	
	// Calculando IRRF sobre Saldo de Salários
	$Calc -> MostraIRRF($base_irrf_saldo_salarios,$id_clt,$idprojeto,$data_base);
	$irrf_saldo_salario = $Calc -> valor;
	//

	$to_saldo_salario = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
	$to_descontos 	  = $irrf_saldo_salario + $inss_saldo_salario;
	$to_rendimentos   = $saldo_de_salario + $terceiro_ss;

} else {
	
	$to_saldo_salario = 0;
	
}




// Aviso Prévio
if($aviso == 'indenizado' and $t_ap == 2) {
	
	$aviso_previo 		  = 'PAGO pelo funcionário';
	$aviso_previo_valor_d = $salario_base - $valor_insalubridade; // valor desconto
	
} elseif($aviso == 'indenizado' and $t_ap == 1) {
	
	$aviso_previo 		  = 'indenizado';
	
	///NOVA REGRA DO AVISO PRÉVIO
	$dt_demissao = mktime(0,0,0,$mes_demissao, $dia_demissao, $ano_demissao);	
	$dt_admissao = mktime(0,0,0,$mes_admissao, $dia_admissao, $ano_admissao);
	$diferenca_anos = ($dt_demissao - $dt_admissao)/31536000;
	
	for($d=1;$d <= (int)$diferenca_anos; $d++){	
		$valor_diario_3 += ($salario_base/$qnt_dias_mes) * 3;	
	}
		
	$aviso_previo_valor_a = $salario_base - $valor_insalubridade; // + $valor_diario_3 valor acréscimo padrão, sem cálculos
	

	
	
	if($dispensa == 63  or $dispensa == 66) {
		
		$pri = $data_demissao;
		$seg = date('Y-m-d', mktime(0,0,0,$mes_admissao,$dia_admissao + 45,$ano_admissao));
		
		if(date('Y-m-d') > $seg) {
			$seg = date('Y-m-d', mktime(0,0,0,$mes_admissao,$dia_admissao + 90,$ano_admissao));
		}
		
		// Verificando a quantidade de dias que faltam para terminar o Aviso Prévio
		// Ex: Foi demitido em 01/01/2009 e o fim dos 90 dias seria 10/01/2009. Então faltariam 9 dias.
		$re   = mysql_query("SELECT data FROM ano WHERE data > '$pri' AND data < '$seg'");
		$dias = mysql_num_rows($re);
		
		// Valor acréscimo
		$art_479 = ($salario_base / $qnt_dias_mes) * ($dias / 2);
		$aviso_previo_valor_a = 0;
		
	}

} elseif($aviso == 'trabalhado' and $t_ap == 1) {
	
	$dt_aviso = explode('-',$data_aviso);
	
	$aviso_previo = "trabalhado até ".date('d/m/Y', mktime(0,0,0,$dt_aviso[1],$dt_aviso[2] +29, $dt_aviso[0]));
	$aviso_previo_valor_a = $salario_base;
	
} elseif($t_ap == 0) {
	
	$aviso_previo = 'Não recebe';
	
}




$to_descontos   = $to_descontos + $aviso_previo_valor_d;
$to_rendimentos = $to_rendimentos + $aviso_previo_valor_a + $valor_insalubridade;
$total_outros_descontos = $aviso_previo_valor_d + $devolucao;




// Fim Aviso Prévio



// Décimo Terceiro (DT)
$qr_verifica_13_folha = mysql_query("SELECT a.id_clt FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = '$ano_demissao' AND a.status = '3'");
$verifica_13_folha    = mysql_num_rows($qr_verifica_13_folha);

		
///Verifica se  a pesssoa recebeu décimo terceiro em novembro
if($t_13 == 1) {
	
	if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) {
		$dia_quinze = 15;
	} else {
		$dia_quinze = 16;
	}
				// 2009 == 2009
				if($ano_admissao == $ano_demissao) {
					
				
									// 12 == 12
									if($mes_demissao == $mes_admissao) {
										
										if($dia_demissao >= $dia_quinze) {
											$meses_ativo_dt = 1;
										} else {
											
											$meses_ativo_dt = 0;
										}
					
					// 11 != 12u
					} else {
						
									if($dia_demissao >= $dia_quinze) {
										$meses_ativo_dt = $mes_demissao - $mes_admissao + 1; 
									} else {
										$meses_ativo_dt = $mes_demissao - $mes_admissao; 
									}
						
					}
	
	// 2009 != 2010
	} else {

		if($dia_demissao >= $dia_quinze) {
			$meses_ativo_dt = $mes_demissao;
		} else {
			$meses_ativo_dt = $mes_demissao - 1;
		}
		
	}

	$valor_td = ($salario_base / 12) * $meses_ativo_dt;
	
	
	
	$Calc -> MostraINSS($valor_td,$data_demissao);
	$previ_dt = $Calc -> valor;
	
	// Calculando INSS sobre DT
	$Calc -> MostraINSS($valor_td,$data_exp);
	$valor_td_inss = $Calc -> valor;
	
	// Calculando IRRF sobre DT
	$base_irrf_td = $valor_td - $valor_td_inss;
	$Calc -> MostraIRRF($base_irrf_td,$id_clt,$idprojeto,$data_demissao);
	$valor_td_irrf = $Calc -> valor;
	
	// Valor do DT
	$total_dt 		= $valor_td - $valor_td_inss - $valor_td_irrf;
	$to_descontos 	= $to_descontos + $valor_td_inss + $valor_td_irrf;
	$to_rendimentos = $to_rendimentos + $valor_td;
	

} else {
	
	$total_dt 	 = 0;
	$meses_ativo = 0;
	
}
// Fim de Décimo Terceiro (DT)






// Verificando Direito de Férias
$qr_verifica_ferias    = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY id_ferias DESC");
$verifica_ferias 	   = mysql_fetch_assoc($qr_verifica_ferias);
$total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

if(empty($total_verifica_ferias)) {
	
	$aquisitivo_ini = $data_entrada;
	$aquisitivo_end = date('Y-m-d', strtotime("".$data_entrada." +1 year"));
	
} else {
	
	$aquisitivo_ini = date('Y-m-d', strtotime("".$data_entrada." + ".$total_verifica_ferias." year"));
	$aquisitivo_end = date('Y-m-d', strtotime("".$data_entrada." + ".($total_verifica_ferias+1)." year"));
	
}

// Verificando Períodos Gozados
$qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY id_ferias ASC");
while($periodos = mysql_fetch_assoc($qr_periodos)) {
	$periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
}

// Verificando Períodos Aquisitivos, Períodos Vencidos e Período Proporcional
list($ano_data_entrada,$mes_data_entrada,$dia_data_entrada) = explode('-', $data_entrada);
$quantidade_anos = (date('Y') - $ano_data_entrada) + 1;

for($a=0; $a<$quantidade_anos; $a++) {
	
	
	
	$aquisitivo_inicio = date('Y-m-d', strtotime("$data_entrada +$a year"));
	$aquisitivo_final  = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1));
	
	if($aquisitivo_final > $data_demissao) {
		
		$periodo_aquisitivo     = $aquisitivo_inicio.'/'.$data_demissao;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$data_demissao;
		
	} else {
		
		$periodo_aquisitivo     = $aquisitivo_inicio.'/'.$aquisitivo_final;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$aquisitivo_final;
		
	}
	
	if(@!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_demissao) {
		
		$periodos_vencidos[]    = $aquisitivo_inicio.'/'.$aquisitivo_final;
		
	} elseif($aquisitivo_final >= $data_demissao and $aquisitivo_inicio < $data_demissao) {
		
		$periodo_proporcional[] = $aquisitivo_inicio.'/'.$data_demissao;
		
	}



}
	
	
	
	

// Buscando Faltas
include('faltas_rescisao.php');

// Fim da Verificação de Férias



// Férias Vencidas
if($t_fv == 1) {
	
	//print_r($periodos_vencidos);
	
	$total_periodos_vencidos = count($periodos_vencidos);
	
	if(empty($total_periodos_vencidos)) {
		
		$ferias_vencidas = 'não';
		$fv_valor_base 	 = 0;
		$fv_um_terco	 = 0;
		
	} elseif($total_periodos_vencidos == 1) {
		
		$ferias_vencidas = 'sim';
		$fv_valor_base 	 = (($salario_base - $valor_insalubridade) / $qnt_dias_mes) * $qnt_dias_fv + $valor_insalubridade;
		$fv_um_terco	 = $fv_valor_base / 3;
		$fv_total 		 = $fv_valor_base + $fv_um_terco ;
		
	} elseif($total_periodos_vencidos > 1) {
		
		$ferias_vencidas = 'sim';
		$fv_valor_base 	 = ((($salario_base - $valor_insalubridade) / $qnt_dias_mes) * $qnt_dias_fv + $valor_insalubridade) * $total_periodos_vencidos;
		$fv_um_terco	 = $fv_valor_base / 3;
		$fv_total 		 = $fv_valor_base + $fv_um_terco;
		$multa_fv		 = ((($salario_base - $valor_insalubridade) / $qnt_dias_mes) * $qnt_dias_fv) * 2;
			
	}

} else {
	
	$fv_total = 0;

}
// Fim de Férias Vencidas



// Férias Proporcionais
if($t_fp == 1) {
	

	
	
	
	list($periodo_proporcional_inicio,$periodo_proporcional_final) 					 = explode('/',$periodo_proporcional[0]);
	list($ano_proporcional_inicio,$mes_proporcional_inicio,$dia_proporcional_inicio) = explode('-',$periodo_proporcional_inicio);
	list($ano_proporcional_final,$mes_proporcional_final,$dia_proporcional_final)    = explode('-',$periodo_proporcional_final);
	
		
	



	// 2010 == 2010
	if($ano_proporcional_inicio == $ano_proporcional_final) {
	    $meses_ativo_fp = $mes_proporcional_final - $mes_proporcional_inicio;
	// 2009 != 2010
	} else {
		$meses_ativo_fp = (12 - $mes_proporcional_inicio) + $mes_proporcional_final;	
		

	}
	
	// Dia Quinze
	if(date('t', @mktime(0,0,0,$mes_proporcional_final,$dia_proporcional_final,$ano_proporcional_final)) != 31) {
		$dia_quinze = 15;
	} else { 
		$dia_quinze = 16;
	}
	
	if($dia_proporcional_final >= $dia_quinze and $dia_data_entrada < $dia_quinze) {
		$meses_ativo_fp += 1;
	}
	

	if($aviso == 'indenizado' and $fator == 'empregador' and $meses_ativo_fp != 12) {
		$meses_ativo_fp += 1;
	}
	
	$fp_valor_mes 	= ($salario_base / $qnt_dias_mes) * $qnt_dias_fp;
	$fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
	
	if($t_fa == 1) {
		
		$fp_um_terco = $fp_valor_total / 3;
		$fp_total 	 = $fp_valor_total + $fp_um_terco;
		
	} else {
		
		$fp_total = $fp_valor_total;
		
	}
	
} else {
	
	$fp_total = 0;

}
// Fim de Férias Proporcionais


// Cálculo de Férias
$ferias_total   = $fp_total + $fv_total;
$to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco;
	

/* Calculando IRRF sobre Férias
$Calc       -> MostraIRRF($ferias_total, $id_clt, $idprojeto, date('Y-m-d'));
$ferias_irrf = $Calc -> valor; */

// Calculando INSS sobre Férias
$ferias_inss = 0;

$ferias_total_final = $ferias_total - $ferias_irrf;
$to_descontos 	    = $to_descontos + $ferias_irrf;

// Fim de Férias



// Atraso de Rescisão
$data_demissao_1      = date('Y-m-d', strtotime("$data_demissao +1 days"));
$data_aviso_previo_1  = date('Y-m-d', strtotime("$data_aviso +1 days"));
$data_aviso_previo_10 = date('Y-m-d', strtotime("$data_aviso +10 days"));


if($dispensa != '63' or $dispensa != '64' or $dispensa != '66') {
	if(
	   ($fator == 'empregador' and $aviso == 'indenizado' and date('Y-m-d') > $data_aviso_previo_10) 
	  ) {
		$valor_atraso = $salario_base;
		//$valor_atraso = 0;
	}
}




//}



// Décimo Terceiro Saldo de Salário (Indenizado)
if($fator == 'empregador' and $aviso == 'indenizado') {
	$num_ss = 1;
	$terceiro_ss = $salario_base / 12;
} else {
	$num_ss = 0;
	$terceiro_ss = 0;
}



// Outros Lançamentos
$result_eventos = mysql_query("SELECT DISTINCT(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");

while($row_evento = mysql_fetch_array($result_eventos)) {
		
	$result_total_evento = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_evento[id_mov]' AND mes_mov = '16' AND cod_movimento != '8080' AND status = '1' AND id_clt = '$id_clt'");
	$row_total_evento = mysql_fetch_array($result_total_evento);
	
	$debitos_tab 	 = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
	$rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
	
	if(in_array($row_evento['cod'], $debitos_tab)) { 
		$debito     = $row_total_evento['valor'];
		$rendimento = NULL;
	} else {
		$debito     = NULL;
		$rendimento = $row_total_evento['valor'];
	}
	
	if($row_evento['cod'] == '5024') {
		$sal_familia_anterior = $row_total_evento['valor'];
	}
	
	// Somando Variáveis
	$re_tot_desconto   += $debito;
	$re_tot_rendimento += $rendimento;
	
	// Limpando Variáveis
	unset($desconto,$rendimento);
				
} 



// Outros que não são utilizados ainda
$valor_comissao		= NULL;
$valor_grativicacao	= NULL;
$valor_outro		= NULL;
	


// Totalizadores
$total_outros    = $valor_sal_familia + $valor_adnoturno + $valor_atraso + $terceiro_ss + $re_tot_rendimento + $vale_refeicao;


/* ANTES DA ALTERAÇÂO
OBS: foi retirado o valor da insalubridade
$total_outros    = $valor_sal_familia + $valor_adnoturno + $valor_insalubridade + $valor_atraso + $terceiro_ss + $re_tot_rendimento + $vale_refeicao;
*/


$total_descontos = $to_descontos + $re_tot_desconto + $previ_ss + $previ_dt + $devolucao + $debito_vale_refeicao + $art_479;
$to_rendimentos  = $to_rendimentos + $total_outros + $hora_extra;

$ajuda_custo = $re_tot_rendimento;
unset($re_tot_desconto,$re_tot_rendimento);



// FGTS 8%
if($t_f8 == 1) {
	$fgts8_total    = $fgts;
	$mensagem_fgts8 = 'Recebe';
} elseif($t_f8 == 2) {
	$fgts8_total = 0;
} elseif($t_f8 == 3) {
	$fgts8_total    = $fgts;
	$mensagem_fgts8 = 'Depositado';
}



// FGTS 40%
if($t_f4 == 1) {
	$fgts4_total = 0;
} else {
	$fgts4_total = 0;
}


///Calculando o total do FGTS
$qr_salario = mysql_query("SELECT  SUM(salbase)  FROM `rh_folha_proc` WHERE id_clt = '$id_clt' AND ano <= '$ano_demissao' AND status = 3");
$total_fgts = mysql_result($qr_salario,0);



$total_fgts = ($aviso_previo_valor_a +$total_fgts) * 0.08;

$totalizador_saldo_fgts += $total_fgts;

//multa 50%
$multa_50_fgts = ($total_fgts) * 0.5;
$totalizador_multa_50_fgts += $multa_50_fgts;





 ///////////////////////////////////outros eventos(MOVIMENTOS)         	
		
                      $result_eventos = mysql_query("SELECT DISTINCT(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");
                      while($row_evento = mysql_fetch_array($result_eventos)) {                 
                       
                        
                        $result_total_evento = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_evento[id_mov]' AND mes_mov >= '$mes_demissao' AND  ano_mov = '$ano_demissao' AND cod_movimento != '8080' AND status = '1' AND id_clt = '$id_clt' AND nome_movimento = 'DESCONTO'");
                        $row_total_evento 	 = mysql_fetch_array($result_total_evento);
                        $total_evento        = mysql_num_rows($result_total_evento);
                        
                        if(!empty($total_evento)) {
                        
                            $debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
                            $rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
                            
                            if(in_array($row_evento['cod'], $debitos_tab)) {
                                
                                $debito     = $row_total_evento['valor'];
                                $rendimento = '';
                                
                            } else {
                                
                                $debito     = '';
                                $rendimento = $row_total_evento['valor'];
                                
                            }
							
			  
						  ///Somando Variáveis
					  $re_tot_desconto   += $debito;
					  $re_tot_rendimento += $rendimento;
						
					  } else {
						
						  $re_tot_desconto   = 0;
						  $re_tot_rendimento = 0;
						
					  }
				
					  unset($desconto,$rendimento);
					
					  }
					  
///ADICIONANDO OS VALORES DE DESCONTO E RENDIMENTO DOS MOVIMENTOS NOS TOTAIS
$to_rendimentos  += $re_tot_rendimento;					  
$total_descontos += $re_tot_desconto;
///

// Totalizadores
$valor_rescisao_final = $to_rendimentos - $total_descontos;

if($valor_rescisao_final < 0) {
	$arredondamento_positivo = abs($valor_rescisao_final);
	$valor_rescisao_final 	 = NULL;
	$to_rendimentos 		 = $to_rendimentos + $arredondamento_positivo;
} else {
	$arredondamento_positivo = NULL;
	$valor_rescisao_final 	 = $to_rendimentos - $total_descontos;
}

?>