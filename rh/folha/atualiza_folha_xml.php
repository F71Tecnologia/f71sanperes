<?php

include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/valor_proporcional.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

ini_set('display_errors', 1);




if(isset($_POST['ajax'])){

$Calc = new calculos();
$Trab = new proporcional();
extract($_POST);


$row_folha = mysql_fetch_assoc(mysql_query("SELECT * FROM rh_folha_proc
											INNER JOIN rh_folha
											ON rh_folha.id_folha = rh_folha_proc.id_folha
											WHERE rh_folha_proc.id_folha = '$id_folha' AND rh_folha_proc.id_clt = '$clt'")) or die(mysql_error());
$mes 		  = $row_folha['mes'];
$mes_int     = (int)$mes;
$ano 		 = $row_folha['ano'];
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$id_folha_proc = $row_folha['id_folha_proc'];

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}






$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_folha[id_projeto]' ");
$row_projeto = mysql_fetch_assoc($qr_projeto);

$projeto_tipo_folha = $row_projeto['tipo_folha'];





$row_curso = mysql_fetch_assoc(mysql_query("SELECT * FROM rh_clt
											 INNER JOIN curso
											 ON rh_clt.id_curso = curso. id_curso
											 WHERE rh_clt.id_clt = '$clt'  ")) or die(mysql_error());
											 
											 
						 
$salario_atividade  =  $row_curso['salario'];	
$total_horas 		=  $row_curso['hora_mes'];

if($total_horas == 0){

echo json_encode(array('erro' => 1, 'nome' => $row_folha['nome']) );	
exit;

} else {  $JSON['erro'] = 0;
}



///CALCULANDO O SALÁRIO DE ACORDO COM AS HORAS TRABALHADAS
//$valor_dia 			= $salario_atividade/30;
//$valor_hora 		= $salario_atividade/$total_horas;	
//$valor_proporcional = $valor_hora * $horas;


include('sintetica/calculos_folha_teste.php');

	

	
$JSON = array( "dias" 				=> (!empty($decimo_terceiro)) ? $meses :  $dias,
			   "base"	 			=> (!empty($decimo_terceiro)) ?  formato_real($decimo_terceiro_credito) :  formato_real($salario),
			   "rendimentos" 		=> formato_real($rendimentos),
			   "descontos"   		=> formato_real($descontos),
			   "inss_completo"   => formato_real($inss_completo),
			   "irrf_completo"   => formato_real($irrf_completo),
			   "liquido"   			=> formato_real($liquido),
			  "salario" 			=> formato_real($salario), 					
			  "decimo_terceiro" 		=> formato_real($decimo_terceiro_credito),
			  "ferias" 				=> formato_real($valor_ferias), 			
			  "desconto_ferias" 		=> formato_real($desconto_ferias), 		
			  "rescisao" 				=> formato_real($valor_rescisao) , 			
			  "desconto_rescisao"	 	=> formato_real($desconto_rescisao), 		
			  "inss" 					=> formato_real($inss), 					
			  "inss_dt" 				=> formato_real($inss_dt), 				
			  "inss_ferias" 			=> formato_real($inss_ferias), 			
			  "inss_rescisao" 		=> formato_real($inss_rescisao),	 		
			  "irrf" 					=> formato_real( $irrf),				 
			  "irrf_dt" 				=> formato_real($irrf_dt), 				
			"irrf_ferias" 			=> formato_real($irrf_ferias) , 			
			"irrf_rescisao" 		=> formato_real($irrf_rescisao), 			
			"fgts" 					=> formato_real($fgts), 					
			"fgts_dt" 				=> formato_real($fgts_dt), 				
			"fgts_ferias" 			=> formato_real($fgts_ferias), 			
			"fgts_rescisao" 		=> formato_real( $fgts_rescisao), 			
			"fgts_completo" 		=> formato_real($fgts_completo), 			
			"vale_refeicao" 		=> formato_real($vale_refeicao), 			
			"familia" 				=> formato_real($familia), 				
			"salario_maternidade" 	=> formato_real($salario_maternidade), 	
			"vale_transporte" 		=> formato_real($vale_transporte) , 		
			"sindicato" 			=> formato_real($sindicato) , 				
			"base_inss" 			=> formato_real($base_inss) ,				
			"base_inss_empresa" 	=> formato_real(($base_inss * 0.2)), 		
			"base_inss_rat" 		=> formato_real(($base_inss * $percentual_rat)),
			"base_inss_terceiros"  	=> formato_real(($base_inss * 0.058)), 	
			"base_irrf" 			=> formato_real(($base_irrf - $ddir)),		
			"base_fgts" 			=> formato_real($base_fgts) , 				
			"base_fgts_ferias" 		=> formato_real($base_fgts_ferias), 		
			"ddir" 					=> formato_real($ddir));	




$row_participante[0] = $row_folha['id_folha_proc'];

//include('sintetica/update_participante.php');









//mysql_query($update_participantes) or die(mysql_error());





echo json_encode($JSON);
	
}

?>