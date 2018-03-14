<?php
class calculos {



public function __construct() {
	
	$id_user     = $_COOKIE['logado'];
	$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
	$row_user    = mysql_fetch_array($result_user);
	
	$this->id_userlocado   = $row_user['id_master'];
	$this->regiaologado    = $row_user['regiao'];
	$this->id_regiaologado = $row_user['id_regiao'];
	
}



function MostraINSS($base,$data) {
	
	if(strstr($data, '/')) {
		$d = explode ('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
		$data_f = implode('-', array_reverse($d));
	} elseif(strstr($data, '-')) {
		$d = explode ('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
		$data_f = $data;
	}
		
	$result_inss = mysql_query("SELECT faixa, fixo, percentual, piso, teto FROM rh_movimentos 
								WHERE cod = '5020' 
								AND v_ini <= '$base' AND v_fim >= '$base' 
								AND '$data_f' BETWEEN data_ini AND data_fim");
	$row_inss = mysql_fetch_array($result_inss);
			  
	$inss_saldo_salario = $base * $row_inss['percentual'];
	
	if($inss_saldo_salario > $row_inss['teto']) {
		$inss_saldo_salario = $row_inss['teto'];
	}
	
	$inss_saldo_salario = number_format($inss_saldo_salario, 3, '.', '');
	$inss_saldo_salario = explode('.', $inss_saldo_salario);
	$decimal            = substr($inss_saldo_salario[1], 0, 2);
		
	$valor_final = $inss_saldo_salario[0].'.'.$decimal;
	
	$this->valor	  = $valor_final;
	$this->percentual = $row_inss['percentual'];

}



function MostraIRRF($base,$idclt,$idprojeto,$data,$tipo='clt') {
	
	if(strstr($data, '/')) {
		$d = explode('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	} elseif(strstr($data, '-')) {
		$d = explode('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	
	$data_menor21 = date('Y-m-d', mktime(0,0,0, $mes, $dia, $ano - 21));
	
	$qr_dependentes = mysql_query("SELECT COUNT(*) FROM prestador_dependente WHERE id_prestador = '$id_prestador'");
	$total_filhos_menor_21 = (int) @mysql_result($qr_dependentes, 0);
	
	$menor21 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt' 
							AND data1 > '$data_menor21' AND data1 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor21 = mysql_fetch_array($menor21);

	$menor22 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt' 
							AND data2 > '$data_menor21' AND data2 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor22 = mysql_fetch_array($menor22);
		  
	$menor23 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt' 
							AND data3 > '$data_menor21' AND data3 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor23 = mysql_fetch_array($menor23);

	$menor24 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt'
							AND data4 > '$data_menor21' AND data4 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor24 = mysql_fetch_array($menor24);

	$menor25 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt'
							AND data5 > '$data_menor21' AND data5 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor25 = mysql_fetch_array($menor25);
	
	$menor26 = mysql_query("SELECT count(data1) AS cont 
							FROM dependentes 
							WHERE id_bolsista = '$idclt'
							AND data6 > '$data_menor21' AND data6 != '0000-00-00' 
							AND id_projeto = '$idprojeto'");
	$row_menor26 = mysql_fetch_array($menor26);
	   
	$total_filhos_menor_21 = $row_menor21['0'] + $row_menor22['0'] + $row_menor23['0'] + $row_menor24['0'] + $row_menor25['0'] + $row_menor26['0'];
	
	if(!empty($total_filhos_menor_21)) {
		
		$result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = '$ano'");
		$row_deducao_ir = mysql_fetch_array($result_deducao_ir);
		
		$valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];
		$base -= $valor_deducao_ir;
		
		$this->valor_deducao_ir_total = $valor_deducao_ir;
		$this->valor_deducao_ir_fixo  = $row_deducao_ir['fixo'];
		$this->total_filhos_menor_21  = $total_filhos_menor_21;
		
	} else {
		
		$this->valor_deducao_ir_total = 0;
		$this->valor_deducao_ir_fixo  = 0;
		$this->total_filhos_menor_21  = 0;
		
	}
		  

	$result_IR = mysql_query("SELECT * FROM rh_movimentos 
							  WHERE cod = '5021' 
							  AND v_ini <= '$base' AND v_fim >= '$base' 
							  AND anobase = '$ano'");
	$row_IR = mysql_fetch_array($result_IR);
	
	$valor_IR = ($base * $row_IR['percentual']) - $row_IR['fixo'];
	
	if($tipo == 'clt') {
		
		$result_recolhimentoIR = mysql_query("SELECT recolhimento_ir FROM rh_clt WHERE id_clt = '$idclt'");
		$row_recolhimentoIR    = mysql_fetch_assoc($result_recolhimentoIR);
		$recolhimento          = $row_recolhimentoIR['recolhimento_ir'];
		
		// Se o recolhimento não estiver vazio, soma o valor do IR mais o recolhimento
		if(!empty($recolhimento)) {
			$valor_IR = $valor_IR + $recolhimento;
		}
		
		// Se ainda assim o valor do IR mais o recolhimento for menor que 10 reais, atualiza o recolhimento 
		// e o valor do IR fica nulo
		if($valor_IR < 10) {
			$update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = '$valor_IR' WHERE id_clt = '$idclt'";
			$valor_IR = 0;
		
		// Se o valor do IR mais o recolhimento for maior que 10 reais e o recolhimento não estiver vazio, 
		// o recolhimento fica nulo e o valor do IR permanece
		} elseif((!empty($recolhimento)) and ($valor_IR > 10)) {
			$update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = 0 WHERE id_clt = '$idclt'";
		}

	}
	
	$this->valor		   = $valor_IR;
	$this->percentual	   = $row_IR['percentual'];
	$this->valor_fixo_ir   = $row_IR['fixo'];
	$this->base_calculo_ir = $base;
	$this->recolhimento_ir = $update_recolhimentoIR;
	
}



function Salariofamilia($base,$idclt,$idprojeto,$data,$contratacao) {
	
	if(strstr($data, '/')) {
		$d   = explode('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	} elseif(strstr($data, '-')) {
		$d   = explode('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}
	
	
	
	$data_menor14 = date('Y-m-d', mktime(0,0,0, $mes, $dia, $ano - 14));
	
	$menor1 = mysql_query("SELECT count(data1) AS cont
						   FROM dependentes
						   WHERE id_bolsista = '$idclt'
						   AND data1 > '$data_menor14' AND data1 != '0000-00-00'
						   AND id_projeto = '$idprojeto'
						   AND contratacao = '$contratacao'");
	$row_menor1 = mysql_fetch_array($menor1);

	$menor2 = mysql_query("SELECT count(data1) AS cont
						   FROM dependentes
						   WHERE id_bolsista = '$idclt'
						   AND data2 > '$data_menor14' AND data2 != '0000-00-00' 
						   AND id_projeto = '$idprojeto'
						   AND contratacao = '$contratacao'");
  	$row_menor2 = mysql_fetch_array($menor2);
		  
	$menor3 = mysql_query("SELECT count(data1) AS cont 
						   FROM dependentes 
						   WHERE id_bolsista = '$idclt' 
						   AND data3 > '$data_menor14' AND data3 != '0000-00-00' 
						   AND id_projeto = '$idprojeto' 
						   AND contratacao = '$contratacao'");
	$row_menor3 = mysql_fetch_array($menor3);

	$menor4 = mysql_query("SELECT count(data1) AS cont 
						   FROM dependentes
						   WHERE id_bolsista = '$idclt' 
						   AND data4 > '$data_menor14' AND data4 != '0000-00-00' 
						   AND id_projeto = '$idprojeto' 
						   AND contratacao = '$contratacao'");
	$row_menor4 = mysql_fetch_array($menor4);

	$menor5 = mysql_query("SELECT count(data1) AS cont 
						   FROM dependentes 
						   WHERE id_bolsista = '$idclt' 
						   AND data5 > '$data_menor14' AND data5 != '0000-00-00' 
						   AND id_projeto = '$idprojeto'
						   AND contratacao = '$contratacao'");
	$row_menor5 = mysql_fetch_array($menor5);
	
	$menor6 = mysql_query("SELECT count(data1) AS cont 
						   FROM dependentes 
						   WHERE id_bolsista = '$idclt' 
						   AND data6 > '$data_menor14' AND data6 != '0000-00-00' 
						   AND id_projeto = '$idprojeto'
						   AND contratacao = '$contratacao'");
	$row_menor6 = mysql_fetch_array($menor6);

    

	$total_menor = $row_menor1['0'] + $row_menor2['0'] + $row_menor3['0'] + $row_menor4['0'] + $row_menor5['0'] + $row_menor6['0'];

	
	/*
	if($mes == 01 and $ano == 2009) {
		$ano -= 1;
	}*/
	
	$result_familia = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5022' AND v_ini <= '$base' AND v_fim >= '$base' AND anobase = '$ano' AND '$data' BETWEEN data_ini AND data_fim");
	$row_familia = mysql_fetch_array($result_familia);
	  
	$valor_familia = $total_menor * $row_familia['fixo'];
	
	
	
	
	$this->valor		  = $valor_familia;
	$this->filhos_menores = $total_menor;
	$this->fixo			  = $row_familia['fixo'];
	
}



function adnoturno($idclt,$data) {
	
	if(empty($data)) {
		$dataexp = explode('/', date('d/m/Y'));
	} else {
		$dataexp = explode('/', $data);
	}
	
	$re_adnoturno = mysql_query("SELECT * 
								 FROM rh_movimentos_clt 
								 WHERE cod_movimento = '9000' 
								 AND id_clt = '$idclt' 
								 AND status = '5' 
	AND (lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2')");
	$row_adnoturno = mysql_fetch_array($re_adnoturno);
	
	$this->valor = $row_adnoturno['valor_movimento'];
	
}



function insalubridade($idclt,$data) {
	
	if(empty($data)) {
		$dataexp = explode('/', date('d/m/Y'));
	} else {
		$dataexp = explode('/', $data);
	}
	
	
	$re_insalubridade = mysql_query("SELECT * 
									 FROM rh_movimentos_clt 
									 WHERE (cod_movimento = '6006' OR cod_movimento = '6007')
									 AND id_clt = '$idclt' 
									 AND status != '0' 
	AND (lancamento = '1' AND mes_mov = '".$dataexp[1]."' AND ano_mov = '".$dataexp[2]."' OR lancamento = '2') ORDER BY data_movimento DESC");
	$row_insalubridade = mysql_fetch_array($re_insalubridade);
	
	$this->valor = $row_insalubridade['valor_movimento'];
	
}



function dt_data($parcela,$data_entrada,$ano_folha,$mes_folha,$salario_base,$clt) {

	list($ano_entrada,$mes_entrada,$dia_entrada) = explode('-', $data_entrada);
	
	// 2010 == 2010
	if($ano_entrada == $ano_folha) {
		
		// 12 != 12
		if($mes_entrada != $mes_folha) {
			$meses_trab = 12 - $mes_entrada;
		}
		
		if($dia_entrada <= 15) {
			$meses_trab += 1;
		}
		
	} else {
		
	    $meses_trab = 12;
		
	}
		
	// Valor Décimo Terceiro		  
	$pre_valor_dt = ($salario_base / 12) * $meses_trab;
    
	// Primeira Parcela ou Segunda Parcela
	if($parcela == 1 or $parcela == 2) {
		
		$valor_dt = $pre_valor_dt / 2;
	
	// Integral
	} else {
		
		$valor_dt = $pre_valor_dt;
		
	}
	
	// Valores Finais de Décimo Terceiro
	$this->valor	  = $valor_dt;
	$this->meses_trab = $meses_trab;
	
}



}
?>