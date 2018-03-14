<?php 

$pj_darf_irrf 			= 1; 
$pj_darf_irrf_cod		= '1708';
$pj_darf_csll 			= 4.65; 
$pj_darf_csll_cod   	= '5952';
$pj_gps 				= 11;

$pjcoop_darf_irrf 		= 1; 
$pjcoop_darf_irrf_cod 	= '3280';
$pjcoop_darf_csll 		= 0; // 
$pjcoop_darf_csll_cod	= '';
$pjcoop_gps 			= 15;

$pjpres_darf_irrf 		= 1.5;
$pjpres_darf_irrf_cod 	= '1708';
$pjpres_darf_csll 		= 4.65;
$pjpres_darf_csll_cod 	= '5952';
$pjpres_gps 			= 11;

$pjadm_darf_irrf 		= 1.5;
$pjadm_darf_irrf_cod 	= '5944';
$pjadm_darf_csll 		= 4.65;
$pjadm_darf_csll_cod 	= '5952';
$pjadm_gps 				= 11;


$pjpub_darf_irrf 		= 1.5;
$pjpub_darf_irrf_cod 	= '8045';
$pjpub_darf_csll 		= 4.65;
$pjpub_darf_csll_cod 	= '5952';
$pjpub_gps 				= 11;

$pf_gps 			= 20;



/*	global $pj_darf_irrf;
	global $pj_darf_csll;
	global $pj_gps;
	
	global $pjcoop_darf_irrf;
	global $pjcoop_darf_csll; 
	global $pjcoop_gps;
	
	global $pjpres_darf_irrf;
	global $pjpres_darf_csll;
	global $pjpres_gps;
	
	global $pf_gps;
*/


function getStatus($id_prestador, $tipo, $mes = '', $ano = ''){
	
	$mes = (empty($mes)) ? date('m') : $mes;
	$ano = (empty($ano)) ? date('Y') : $ano;
	
	$qr_status = mysql_query("SELECT * FROM prestador_pagamento WHERE 
	mes = '$mes' 
	AND ano = '$ano' 
	AND tipo = '$tipo' 
	AND id_prestador = '$id_prestador'");
	
}

function getTotalSaida ($id_prestador,$mes,$ano){
	
	$qr_total = mysql_query("SELECT SUM(saida.valor_bruto) AS TOTAL FROM 
	(prestadorservico INNER JOIN prestador_pg USING(id_prestador) )
	INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida
	WHERE prestadorservico.id_prestador = '$id_prestador' 
	AND MONTH(saida.data_pg) = '$mes' 
	AND YEAR(saida.data_pg) = '$ano'
	");
	return (float) @mysql_result($qr_total,0);
	
}

// parametros antigos $base,$idclt,$idprojeto,$data,$tipo='clt'


function MostraIRRF($base,$id_prestador) {
	
	$return = array();
	
	
	$dia = date('d');
	$mes = date('m');
	$ano = date('Y');
	// SEPARANDO A DATA
	/*if(strstr($data, '/')) {
		$d = explode('/', $data);
		$dia = $d[0];
		$mes = $d[1];
		$ano = $d[2];
	} elseif(strstr($data, '-')) {
		$d = explode('-', $data);
		$dia = $d[2];
		$mes = $d[1];
		$ano = $d[0];
	}*/
	// CALCULANDO A DATA DE 21 ANOS ATRAZ
	$data_menor21 = date('Y-m-d', mktime(0,0,0, $mes, $dia, $ano - 21));
	// BUSCANDO OS FILHOS MENORES DE 21
	$qr_dependentes = mysql_query("SELECT COUNT(*) FROM prestador_dependente WHERE prestador_id = '$id_prestador' AND prestador_dep_status = '1'");
	// TOTALIZANDO A QUANTIDADE DE FILHOS 
	$total_filhos_menor_21 = (int) @mysql_result($qr_dependentes,0);
	if(!empty($total_filhos_menor_21)) {
		
		$result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049' AND anobase = '$ano'");
		$row_deducao_ir = mysql_fetch_array($result_deducao_ir);
		
		$valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];
		$base -= $valor_deducao_ir;
		
		$return['valor_deducao_ir_total'] = $valor_deducao_ir;
		$return['valor_deducao_ir_fixo']  = $row_deducao_ir['fixo'];
		$return['total_filhos_menor_21']  = $total_filhos_menor_21;
		
	} else {
		
		$return['valor_deducao_ir_total'] = 0;
		$return['valor_deducao_ir_fixo']  = 0;
		$return['total_filhos_menor_21']  = 0;
		
	}
		  

	$result_IR = mysql_query("SELECT * FROM rh_movimentos 
							  WHERE cod = '5021' 
							  AND v_ini <= '$base' AND v_fim >= '$base' 
							  AND anobase = '$ano'");
	$row_IR = mysql_fetch_array($result_IR);
	
	$valor_IR = $base * $row_IR['percentual'] - $row_IR['fixo'];
	
	$return['valor']	   		= $valor_IR;
	$return['percentual']	   	= $row_IR['percentual'];
	$return['valor_fixo_ir']   	= $row_IR['fixo'];
	$return['base_calculo_ir'] 	= $base;
	$return['recolhimento_ir'] 	= $update_recolhimentoIR;
	
	return $return;
	
}


function darf_irrf($valor,$tipo_prestador){
	
	global $pj_darf_irrf;
	global $pjcoop_darf_irrf;
	global $pjpres_darf_irrf;
	global $pjadm_darf_irrf;
	global $pjpub_darf_irrf;
	
	
	switch ($tipo_prestador):
		case 1:
			$resultado = (($valor * $pj_darf_irrf)/100);
			break;
		case 2: 
			$resultado = (($valor * $pjcoop_darf_irrf)/100);
			break;
		case 4: 
			$resultado = (($valor * $pjpres_darf_irrf)/100);
			break;
		case 5:
			$resultado = (($valor * $pjadm_darf_irrf)/100);
			break;
		case 6:
			$resultado = (($valor * $pjpub_darf_irrf)/100);
			break;
	endswitch;
	
	return $resultado;
	
	
}


function darf_csll($valor,$tipo_prestador){
	
	global $pj_darf_csll;
	global $pjcoop_darf_csll; 
	global $pjpres_darf_csll;
	global $pjadm_darf_csll;
	global $pjpub_darf_csll;
	
	
	
	switch ($tipo_prestador):
		case 1:
			$resultado = (($valor * $pj_darf_csll)/100);
			break;
		case 2: 
			$resultado = (($valor * $pjcoop_darf_csll)/100);
			break;
		case 4: 
			$resultado = (($valor * $pjpres_darf_csll)/100);
			break;
		case 5:
			$resultado = (($valor * $pjadm_darf_csll)/100);
			break;
		case 6:
			$resultado = (($valor * $pjpub_darf_csll)/100);
			break;
	endswitch;
	
	return $resultado;

	
}


function gps($valor,$tipo_prestador){
	global $pj_gps;	
	global $pjcoop_gps;
	global $pjpres_gps;
	global $pf_gps;
	global $pjadm_gps;
	global $pjpub_gps;
	
	
	
	switch ($tipo_prestador):
		case 1:
			$resultado = (($valor * $pj_gps)/100);
			break;
		case 2: 
			$resultado = (($valor * $pjcoop_gps)/100);
			break;
		case 3:
			$resultado = (($valor * $pf_gps)/100);
			break;
		case 4: 
			$resultado = (($valor * $pjpres_gps)/100);
			break;
		case 5:
			$resultado = (($valor * $pjadm_gps)/100);
			break;
		case 6:
			$resultado = (($valor * $pjpub_gps)/100);
			break;		
	endswitch;
	
	return $resultado;
}


function getCod($tipo_prestador,$tipo_darf = 'irrf'){
	global $pj_darf_irrf_cod;
	global $pj_darf_csll_cod;
	
	global $pjcoop_darf_irrf_cod;
	global $pjcoop_darf_csll_cod;	
	
	global $pjpres_darf_irrf_cod;
	global $pjpres_darf_csll_cod;
	
	global $pjadm_darf_irrf_cod;
	global $pjadm_darf_csll_cod;
	
	global $pjpub_darf_irrf_cod;
	global $pjpub_darf_csll_cod;

	
	
	switch ($tipo_prestador):
		case 1:
			
			$return = ($tipo_darf == 'csll') ? $pj_darf_csll_cod : $pj_darf_irrf_cod;
			break;
		case 2: 
			$return = ($tipo_darf == 'csll') ? $pjcoop_darf_csll_cod : $pjcoop_darf_irrf_cod;
			break;
		case 4:
			$return = ($tipo_darf == 'csll') ? $pjpres_darf_csll_cod : $pjpres_darf_irrf_cod;
			break;
		case 5:
			$return = ($tipo_darf == 'csll') ? $pjadm_darf_csll_cod : $pjadm_darf_irrf_cod;
			break;
		case 6:
			$return = ($tipo_darf == 'csll') ? $pjpub_darf_csll_cod : $pjpub_darf_irrf_cod;
			break;
		
		default :
			$return = '';
			break;		
	endswitch;
	
	return $return;
	
}

function getTaxa($tipo_prestador,$tipo_darf = 'irrf'){
	
	global $pj_darf_irrf;
	global $pjcoop_darf_irrf;
	global $pjpres_darf_irrf;
	global $pjadm_darf_irrf;
	global $pjpub_darf_irrf;
	
	global $pj_darf_csll;
	global $pjcoop_darf_csll; 
	global $pjpres_darf_csll;
	global $pjadm_darf_csll;
	global $pjpub_darf_csll;
	
	global $pj_gps;
	global $pjcoop_gps;
	global $pjpres_gps;
	global $pjadm_gps;
	global $pjpub_gps;
	

	
	global $pf_gps;
	
	
	switch ($tipo_prestador):
		case 1:
			if($tipo_darf == 'csll'){
				$return = $pj_darf_csll;
			}elseif($tipo_darf == 'irrf'){
				$return = $pj_darf_irrf;
			}else{
				$return = $pj_gps;
			}
			break;
		case 2: 
		
			if($tipo_darf == 'csll'){
				$return = $pjcoop_darf_csll;
			}elseif($tipo_darf == 'irrf'){
				$return = $pjcoop_darf_irrf;
			}else{
				$return = $pjcoop_gps;
			}
			break;
		case 3:
			
			$return = $pf_gps;
			break;
		case 4:
			if($tipo_darf == 'csll'){
				$return = $pjpres_darf_csll;
			}elseif($tipo_darf == 'irrf'){
				$return = $pjpres_darf_irrf;
			}else{
				$return = $pjpres_gps;
			}
			break;
		case 5:
			if($tipo_darf == 'csll'){
				$return = $pjadm_darf_csll;
			}elseif($tipo_darf == 'irrf'){
				$return = $pjadm_darf_irrf;
			}else{
				$return = $pjadm_gps;
			}
			break;
		case 6:
			if($tipo_darf == 'csll'){
				$return = $pjpub_darf_csll;
			}elseif($tipo_darf == 'irrf'){
				$return = $pjpub_darf_irrf;
			}else{
				$return = $pjpub_gps;
			}
			break;
		
		default :
			$return = '';
			break;		
	endswitch;
	
	return $return ;
	


	
	
}
?>