<?php

include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/valor_proporcional.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

ini_set('display_errors', 0);




if(isset($_POST['ajax'])){

$Calc = new calculos();
$Trab = new proporcional();
extract($_POST);






$row_folha = mysql_fetch_assoc(mysql_query("SELECT * FROM rh_folha_proc
											INNER JOIN rh_folha
											ON rh_folha.id_folha = rh_folha_proc.id_folha
											WHERE rh_folha_proc.id_folha = '$id_folha' AND rh_folha_proc.id_clt = '$clt'")) or die(mysql_error());
$mes 		  			 = $row_folha['mes'];
$mes_int     			 = (int)$mes;
$ano 		 			 = $row_folha['ano'];
$data_inicio 			 = $row_folha['data_inicio'];
$data_fim    			 = $row_folha['data_fim'];
$id_folha_proc 			 = $row_folha['id_folha_proc'];



///QNT DE FERIADOS NO MÊS
$qnt_feriados  = mysql_num_rows(mysql_query("SELECT * FROM rhferiados WHERE MONTH(data) = '$mes' AND  (id_regiao = '$row_folha[id_regiao]' OR id_regiao = 0)"));

/////////PEGANDO A QUANTO DADE DE DIAS ÚTEIS
$total_dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

for($i=0; $i < $total_dias_mes; $i++){
    $dia = strtotime($ano.'-'.$mes.'-'.$i); 
	if(date('w',$dia) >=1 and date('w',$dia) <=5){ $dias_uteis =  $dias_uteis+1;}
	if(date('w',$dia) == 6){ $qnt_semanas = $qnt_semanas + 1;}
}

	 
// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}


// Percentual RAT
$percentual_fap = mysql_result(mysql_query("SELECT percentual FROM rh_movimentos WHERE cod = '9991'"),0);

if($ano >= 2011) {
	$percentual_rat = $percentual_fap;
} else {
	$percentual_rat = '0.03';
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



///CALCULANDO O SALÁRIO DE ACORDO COM AS HORAS TRABALHADAS
//$valor_dia 			= $salario_atividade/30;
//$valor_hora 		= $salario_atividade/$total_horas;	
//$valor_proporcional = $valor_hora * $horas;


include('sintetica/calculos_folha_teste.php');




	
$JSON = array( "dias" 				=> (!empty($decimo_terceiro)) ? $meses :  $dias,
			   "base"	 			=> (!empty($decimo_terceiro)) ?  formato_real($decimo_terceiro_credito) :  formato_real($salario),
			   "total_xml_base"     => formato_real($base),
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
			"ddir" 					=> formato_real($ddir),
			"adicional_noturno_mes" => formato_real($valor_adicional_noturno),
			"DSR"					=> formato_real($DSR)
			)	;	




$row_participante[0] = $row_folha['id_folha_proc'];




include('sintetica/update_participante_teste.php');


mysql_query($update_participantes) or die(mysql_error());








$xml = new DOMDocument();//
$xml->load("xml_horista/$id_folha.xml") or die("Error");

$root = $xml->getElementsByTagName('clt');////Pegando o elemento pai
$total_participantes = $root->length;


for($i = 0; $i < $total_participantes; $i++) {
	
			
			  if( $xml->getElementsByTagName('id_clt')->item($i)->nodeValue != $clt )	continue;
			  	
			  
  
			  
			  ///Pegando 
			  
			  $cod 			= $xml->getElementsByTagName('cod')->item($i);
			  $new_cod		= $xml->createElement('cod', $row_clt['campo3']); 
			  $cod->parentNode->replaceChild($new_cod, $cod); 			  
		
			  
			  $status_clt 		= $xml->getElementsByTagName('status_clt')->item($i);
			  $new_status_clt	 	= $xml->createElement('status_clt', $row_clt['status']); 
			  $status_clt->parentNode->replaceChild($new_status_clt, $status_clt); 
			  
			  
			  $id_banco 		= $xml->getElementsByTagName('id_banco')->item($i);
			  $new_id_banco 	= $xml->createElement('id_banco', $row_clt['banco']); 
			  $id_banco->parentNode->replaceChild($new_id_banco, $id_banco); 
			  
			  $agencia 		= $xml->getElementsByTagName('agencia')->item($i);
			  $new_agencia 	= $xml->createElement('agencia', $row_clt['agencia']); 
			  $agencia->parentNode->replaceChild($new_agencia, $agencia); 
			  
			  $conta 		= $xml->getElementsByTagName('conta')->item($i);
			  $new_conta 	= $xml->createElement('conta', $row_clt['conta']); 
			  $conta->parentNode->replaceChild($new_conta, $conta); 
			  
			  
			  $cpf 		= $xml->getElementsByTagName('cpf')->item($i);
			  $new_cpf 	= $xml->createElement('cpf', $row_clt['cpf']); 
			  $cpf->parentNode->replaceChild($new_cpf, $cpf); 
			  
			  
			  $dias_trab 		= $xml->getElementsByTagName('dias_trab')->item($i);
			  $new_dias_trab 	= $xml->createElement('dias_trab', $dias); 
			  $dias_trab->parentNode->replaceChild($new_dias_trab, $dias_trab); 
			  
			  
			  
			  $meses 		= $xml->getElementsByTagName('meses')->item($i);
			  $new_meses 	= $xml->createElement('meses', 'CERTO'); 
			  $meses->parentNode->replaceChild($new_meses, $meses); 
			  
			  
			  $salbase 		= $xml->getElementsByTagName('salbase')->item($i);
			  $new_salbase 	= $xml->createElement('salbase', formato_banco($base)); 
			  $salbase->parentNode->replaceChild($new_salbase, $salbase); 
			  
			  
			  $sallimpo 		= $xml->getElementsByTagName('sallimpo')->item($i);
			  $new_sallimpo 	= $xml->createElement('sallimpo', formato_banco($salario_limpo)); 
			  $sallimpo->parentNode->replaceChild($new_sallimpo, $sallimpo); 
			  
			  
			  $sallimpo_real 		= $xml->getElementsByTagName('sallimpo_real')->item($i);
			  $new_sallimpo_real 	= $xml->createElement('sallimpo_real',formato_banco($salario)); 
			  $sallimpo_real->parentNode->replaceChild($new_sallimpo_real, $sallimpo_real);
			  
			  
			  $rend 		= $xml->getElementsByTagName('rend')->item($i);
			  $new_rend 	= $xml->createElement('rend', formato_banco($rendimentos)); 
			  $rend->parentNode->replaceChild($new_rend, $rend); 
			  
			  
			  $desco 		= $xml->getElementsByTagName('desco')->item($i);
			  $new_desco 	= $xml->createElement('desco', formato_banco($descontos)); 
			  $desco->parentNode->replaceChild($new_desco, $desco); 
			  
			  $inss 		= $xml->getElementsByTagName('inss')->item($i);
			  $new_inss 	= $xml->createElement('inss', formato_banco($inss)); 
			  $inss->parentNode->replaceChild($new_inss, $inss); 
			  
			  $t_inss 		= $xml->getElementsByTagName('t_inss')->item($i);
			  $new_t_inss 	= $xml->createElement('t_inss', $faixa_inss); 
			  $t_inss->parentNode->replaceChild($new_t_inss, $t_inss); 
			  
			  $imprenda 		= $xml->getElementsByTagName('imprenda')->item($i);
			  $new_imprenda 	= $xml->createElement('imprenda', formato_banco($irrf)); 
			  $imprenda->parentNode->replaceChild($new_imprenda, $imprenda); 
			  
			  $t_imprenda 		= $xml->getElementsByTagName('t_imprenda')->item($i);
			  $new_t_imprenda 	= $xml->createElement('t_imprenda', $faixa_irrf); 
			  $t_imprenda->parentNode->replaceChild($new_t_imprenda, $t_imprenda); 
			  
			  $d_imprenda 		= $xml->getElementsByTagName('d_imprenda')->item($i);
			  $new_d_imprenda 	= $xml->createElement('d_imprenda', $fixo_irrf); 
			  $d_imprenda->parentNode->replaceChild($new_d_imprenda, $d_imprenda); 
			  
			  $fgts 		= $xml->getElementsByTagName('fgts')->item($i);
			  $new_fgts 	= $xml->createElement('fgts', formato_banco($fgts)); 
			  $fgts->parentNode->replaceChild($new_fgts, $fgts); 
			  			  
		
			  
			  $base_irrf 		= $xml->getElementsByTagName('base_irrf')->item($i);
			  $new_base_irrf 	= $xml->createElement('base_irrf', formato_banco($base_irrf)); 
			  $base_irrf->parentNode->replaceChild($new_base_irrf, $base_irrf); 
			  
			  
			  $salfamilia 		= $xml->getElementsByTagName('salfamilia')->item($i);
			  $new_salfamilia 	= $xml->createElement('salfamilia',formato_banco($familia)); 
			  $salfamilia->parentNode->replaceChild($new_salfamilia, $salfamilia); 
			  
			  $salliquido 		= $xml->getElementsByTagName('salliquido')->item($i);
			  $new_salliquido		= $xml->createElement('salliquido', formato_banco($liquido)); 
			  $salliquido->parentNode->replaceChild($new_salliquido, $salliquido); 
			  
			 /* 
			  $a4001 		= $xml->getElementsByTagName('a4001')->item($i);
			  $new_a4001		= $xml->createElement('a4001', 'CERTO'); 
			  $a4001->parentNode->replaceChild($new_a4001, $a4001); 
			  
			  
			  
			  $a4002 		= $xml->getElementsByTagName('a4002')->item($i);
			  $new_a4002	= $xml->createElement('a4002', 'CERTO'); 
			  $a4002->parentNode->replaceChild($new_a4002, $a4002); 
			  
			  $a4003 		= $xml->getElementsByTagName('a4003')->item($i);
			  $new_a4003	= $xml->createElement('a4003', 'CERTO'); 
			  $a4003->parentNode->replaceChild($new_a4003, $a4003); 
			  
			  $a4004 		= $xml->getElementsByTagName('a4004')->item($i);
			  $new_a4004	= $xml->createElement('a4004', 'CERTO'); 
			  $a4004->parentNode->replaceChild($new_a4004, $a4004); 
			  
			  $a4005 		= $xml->getElementsByTagName('a4005')->item($i);
			  $new_a4005	= $xml->createElement('a4005', 'CERTO'); 
			  $a4005->parentNode->replaceChild($new_a4005, $a4005); 
			  
			  
			  $a4006 		= $xml->getElementsByTagName('a4006')->item($i);
			  $new_a4006	= $xml->createElement('a4006', 'CERTO'); 
			  $a4006->parentNode->replaceChild($new_a4006, $a4006); 
			  
			  $a4007 		= $xml->getElementsByTagName('a4007')->item($i);
			  $new_a4007	= $xml->createElement('a4007', 'CERTO'); 
			  $a4007->parentNode->replaceChild($new_a4007, $a4007); 
			  
			  
			  $a5001 		= $xml->getElementsByTagName('a5001')->item($i);
			  $new_a5001	= $xml->createElement('a5001', 'CERTO'); 
			  $a5001->parentNode->replaceChild($new_a5001, $a5001); 
			  
			  $a5002 		= $xml->getElementsByTagName('a5002')->item($i);
			  $new_a5002	= $xml->createElement('a5002', 'CERTO'); 
			  $a5002->parentNode->replaceChild($new_a5002, $a5002); 
			  
			  $a5003 		= $xml->getElementsByTagName('a5003')->item($i);
			  $new_a5003	= $xml->createElement('a5003', 'CERTO'); 
			  $a5003->parentNode->replaceChild($new_a5003, $a5003); 
			  
			  $a5004 		= $xml->getElementsByTagName('a5004')->item($i);
			  $new_a5004	= $xml->createElement('a5004', 'CERTO'); 
			  $a5004->parentNode->replaceChild($new_a5004, $a5004); 
			  
			  $a5010 		= $xml->getElementsByTagName('a5010')->item($i);
			  $new_a5010	= $xml->createElement('a5010', 'CERTO'); 
			  $a5010->parentNode->replaceChild($new_a5010, $a5010); 
			  
			  $a5011 		= $xml->getElementsByTagName('a5011')->item($i);
			  $new_a5011	= $xml->createElement('a5011', 'CERTO'); 
			  $a5011->parentNode->replaceChild($new_a5011, $a5011); 
			  
			  $a5012 		= $xml->getElementsByTagName('a5012')->item($i);
			  $new_a5012	= $xml->createElement('a5012', 'CERTO'); 
			  $a5012->parentNode->replaceChild($new_a5012, $a5012); 
			  
			  $a5013 		= $xml->getElementsByTagName('a5013')->item($i);
			  $new_a5013	= $xml->createElement('a5013', 'CERTO'); 
			  $a5013->parentNode->replaceChild($new_a5013, $a5013); 
			  
			  $a5014 		= $xml->getElementsByTagName('a5014')->item($i);
			  $new_a5014	= $xml->createElement('a5014', 'CERTO'); 
			  $a5014->parentNode->replaceChild($new_a5014, $a5014); 
			  
			  $a5015 		= $xml->getElementsByTagName('a5015')->item($i);
			  $new_a5015	= $xml->createElement('a5015', 'CERTO'); 
			  $a5015->parentNode->replaceChild($new_a5015, $a5015); 
			  
			  $a5016 		= $xml->getElementsByTagName('a5016')->item($i);
			  $new_a5016	= $xml->createElement('a5016', 'CERTO'); 
			  $a5016->parentNode->replaceChild($new_a5016, $a5016); 
			  
			  $a5017 		= $xml->getElementsByTagName('a5017')->item($i);
			  $new_a5017	= $xml->createElement('a5017', 'CERTO'); 
			  $a5017->parentNode->replaceChild($new_a5017, $a5017); 
			  
			  $a5018 		= $xml->getElementsByTagName('a5018')->item($i);
			  $new_a5018	= $xml->createElement('a5018', 'CERTO'); 
			  $a5018->parentNode->replaceChild($new_a5018, $a5018);*/ 
			  
			  $a5019 		= $xml->getElementsByTagName('a5019')->item($i);
			  $new_a5019	= $xml->createElement('a5019',formato_banco($sindicato)); 
			  $a5019->parentNode->replaceChild($new_a5019, $a5019); 
			  
			  $a5020 		= $xml->getElementsByTagName('a5020')->item($i);
			  $new_a5020	= $xml->createElement('a5020', formato_banco($inss)); 
			  $a5020->parentNode->replaceChild($new_a5020, $a5020); 
			  
			  
			  $a5021 		= $xml->getElementsByTagName('a5021')->item($i);
			  $new_a5021	= $xml->createElement('a5021', formato_banco($irrf)); 
			  $a5021->parentNode->replaceChild($new_a5021, $a5021); 
			  
			  $a5022 		= $xml->getElementsByTagName('a5022')->item($i);
			  $new_a5022	= $xml->createElement('a5022',formato_banco($familia)); 
			  $a5022->parentNode->replaceChild($new_a5022, $a5022); 
			  
			  
			  /*
			  $a5023 		= $xml->getElementsByTagName('a5023')->item($i);
			  $new_a5023	= $xml->createElement('a5023', 'CERTO'); 
			  $a5023->parentNode->replaceChild($new_a5023, $a5023); 
			  
			  $a5024 		= $xml->getElementsByTagName('a5024')->item($i);
			  $new_a5024	= $xml->createElement('a5024', 'CERTO'); 
			  $a5024->parentNode->replaceChild($new_a5024, $a5024); 
			  
			  $a5025 		= $xml->getElementsByTagName('a5025')->item($i);
			  $new_a5025	= $xml->createElement('a5025', 'CERTO'); 
			  $a5025->parentNode->replaceChild($new_a5025, $a5025); 
			  
			  $a5026 		= $xml->getElementsByTagName('a5026')->item($i);
			  $new_a5026	= $xml->createElement('a5026', 'CERTO'); 
			  $a5026->parentNode->replaceChild($new_a5026, $a5026); 
			  
			  $a5027 		= $xml->getElementsByTagName('a5027')->item($i);
			  $new_a5027	= $xml->createElement('a5027', 'CERTO'); 
			  $a5027->parentNode->replaceChild($new_a5027, $a5027); 
			  
			  $a5028 		= $xml->getElementsByTagName('a5028')->item($i);
			  $new_a5028	= $xml->createElement('a5028', 'CERTO'); 
			  $a5028->parentNode->replaceChild($new_a5028, $a5028); */
			  
			  $a5029 		= $xml->getElementsByTagName('a5029')->item($i);
			  $new_a5029	= $xml->createElement('a5029',formato_banco($decimo_terceiro_credito)); 
			  $a5029->parentNode->replaceChild($new_a5029, $a5029); 
			  
			  $a5030 		= $xml->getElementsByTagName('a5030')->item($i);
			  $new_a5030	= $xml->createElement('a5030', formato_banco($irrf_dt)); 
			  $a5030->parentNode->replaceChild($new_a5030, $a5030); 
			  
			  $a5031 		= $xml->getElementsByTagName('a5031')->item($i);
			  $new_a5031	= $xml->createElement('a5031', formato_banco($inss_dt)); 
			  $a5031->parentNode->replaceChild($new_a5031, $a5031); 
			  /*
			  $a5032 		= $xml->getElementsByTagName('a5032')->item($i);
			  $new_a5032	= $xml->createElement('a5032', 'CERTO'); 
			  $a5032->parentNode->replaceChild($new_a5032, $a5032); 
			  
			  $a5033 		= $xml->getElementsByTagName('a5033')->item($i);
			  $new_a5033	= $xml->createElement('a5033', 'CERTO'); 
			  $a5033->parentNode->replaceChild($new_a5033, $a5033); 
			  
			  $a5034 		= $xml->getElementsByTagName('a5034')->item($i);
			  $new_a5034	= $xml->createElement('a5034', 'CERTO'); 
			  $a5034->parentNode->replaceChild($new_a5034, $a5034); */
			  
			  $a5035 		= $xml->getElementsByTagName('a5035')->item($i);
			  $new_a5035	= $xml->createElement('a5035', formato_banco($inss_ferias)); 
			  $a5035->parentNode->replaceChild($new_a5035, $a5035); 
			  
			  $a5036 		= $xml->getElementsByTagName('a5036')->item($i);
			  $new_a5036	= $xml->createElement('a5036', formato_banco($irrf_ferias)); 
			  $a5036->parentNode->replaceChild($new_a5036, $a5036); 
			  
			  $a5037 		= $xml->getElementsByTagName('a5037')->item($i);
			  $new_a5037	= $xml->createElement('a5037', formato_banco($valor_ferias)); 
			  $a5037->parentNode->replaceChild($new_a5037, $a5037); 
			  
			  
			  
			  
			  
			 /*
			  $a5038 		= $xml->getElementsByTagName('a5038')->item($i);
			  $new_a5038	= $xml->createElement('a5038', 'CERTO'); 
			  $a5038->parentNode->replaceChild($new_a5038, $a5038); 
			  
			  $a5039 		= $xml->getElementsByTagName('a5039')->item($i);
			  $new_a5039	= $xml->createElement('a5039', 'CERTO'); 
			  $a5039->parentNode->replaceChild($new_a5039, $a5039); 
			  
			  $a5040 		= $xml->getElementsByTagName('a5040')->item($i);
			  $new_a5040	= $xml->createElement('a5040', 'CERTO'); 
			  $a5040->parentNode->replaceChild($new_a5040, $a5040); 
			  
			  $a5041 		= $xml->getElementsByTagName('a5041')->item($i);
			  $new_a5041	= $xml->createElement('a5041', 'CERTO'); 
			  $a5041->parentNode->replaceChild($new_a5041, $a5041); 
			  
			  $a5042 		= $xml->getElementsByTagName('a5042')->item($i);
			  $new_a5042	= $xml->createElement('a5042', 'CERTO'); 
			  $a5042->parentNode->replaceChild($new_a5042, $a5042); 
			  
			  $a5043 		= $xml->getElementsByTagName('a5043')->item($i);
			  $new_a5043	= $xml->createElement('a5043', 'CERTO'); 
			  $a5043->parentNode->replaceChild($new_a5043, $a5043); */
			  
			  $a5044 		= $xml->getElementsByTagName('a5044')->item($i);
			  $new_a5044	= $xml->createElement('a5044', formato_banco($fgts_ferias)); 
			  $a5044->parentNode->replaceChild($new_a5044, $a5044); 
			  
			  /*
			  $a5045 		= $xml->getElementsByTagName('a5045')->item($i);
			  $new_a5045	= $xml->createElement('a5045', 'CERTO'); 
			  $a5045->parentNode->replaceChild($new_a5045, $a5045); 
			  
			  $a5046 		= $xml->getElementsByTagName('a5046')->item($i);
			  $new_a5046	= $xml->createElement('a5046', 'CERTO'); 
			  $a5046->parentNode->replaceChild($new_a5046, $a5046); 
			  
			  
			  $a5047 		= $xml->getElementsByTagName('a5047')->item($i);
			  $new_a5047	= $xml->createElement('a5047', 'CERTO'); 
			  $a5047->parentNode->replaceChild($new_a5047, $a5047); 
			  
			  $a5048 		= $xml->getElementsByTagName('a5048')->item($i);
			  $new_a5048	= $xml->createElement('a5048', 'CERTO'); 
			  $a5048->parentNode->replaceChild($new_a5048, $a5048); 
			  */
			  $a5049 		= $xml->getElementsByTagName('a5049')->item($i);
			  $new_a5049	= $xml->createElement('a5049', formato_banco($ddir)); 
			  $a5049->parentNode->replaceChild($new_a5049, $a5049); 
			  
			  /*
			  $a6000 		= $xml->getElementsByTagName('a6000')->item($i);
			  $new_a6000	= $xml->createElement('a6000', 'CERTO'); 
			  $a6000->parentNode->replaceChild($new_a6000, $a6000); 
			  
			  $a6001 		= $xml->getElementsByTagName('a6001')->item($i);
			  $new_a6001	= $xml->createElement('a6001', 'CERTO'); 
			  $a6001->parentNode->replaceChild($new_a6001, $a6001); 
			  
			  $a6003 		= $xml->getElementsByTagName('a6003')->item($i);
			  $new_a6003	= $xml->createElement('a6003', 'CERTO'); 
			  $a6003->parentNode->replaceChild($new_a6003, $a6003); 
			  
			  $a6004 		= $xml->getElementsByTagName('a6004')->item($i);
			  $new_a6004	= $xml->createElement('a6004', 'CERTO'); 
			  $a6004->parentNode->replaceChild($new_a6004, $a6004); 
			  */
			  
			  $a6005 		= $xml->getElementsByTagName('a6005')->item($i);
			  $new_a6005	= $xml->createElement('a6005',formato_banco($salario_maternidade)); 
			  $a6005->parentNode->replaceChild($new_a6005, $a6005); 
			  
			  /*
			  $a6006 		= $xml->getElementsByTagName('a6006')->item($i);
			  $new_a6006	= $xml->createElement('a6006', 'CERTO'); 
			  $a6006->parentNode->replaceChild($new_a6006, $a6006); 
			  
			  $a7000 		= $xml->getElementsByTagName('a7000')->item($i);
			  $new_a7000	= $xml->createElement('a7000', 'CERTO'); 
			  $a7000->parentNode->replaceChild($new_a7000, $a7000); 
			  */
			  
			  $a7001 		= $xml->getElementsByTagName('a7001')->item($i);
			  $new_a7001	= $xml->createElement('a7001', formato_banco($vale_transporte)); 
			  $a7001->parentNode->replaceChild($new_a7001, $a7001); 
			  
			  /*
			  $a7003 		= $xml->getElementsByTagName('a7003')->item($i);
			  $new_a7003	= $xml->createElement('a7003', 'CERTO'); 
			  $a7003->parentNode->replaceChild($new_a7003, $a7003); 
			  
			  $a7004 		= $xml->getElementsByTagName('a7004')->item($i);
			  $new_a7004	= $xml->createElement('a7004', 'CERTO'); 
			  $a7004->parentNode->replaceChild($new_a7004, $a7004); 
			  
			  
			  $a7009 		= $xml->getElementsByTagName('a7009')->item($i);
			  $new_a7009	= $xml->createElement('a7009', 'CERTO'); 
			  $a7009->parentNode->replaceChild($new_a7009, $a7009); 
			  
			  
			  $a8000 		= $xml->getElementsByTagName('a7009')->item($i);
			  $new_a8000	= $xml->createElement('a8000', 'CERTO'); 
			  $a8000->parentNode->replaceChild($new_a8000, $a8000); 
			 
			  
			  $a8002 		= $xml->getElementsByTagName('a8002')->item($i);
			  $new_a8002	= $xml->createElement('a8002', 'CERTO'); 
			  $a8002->parentNode->replaceChild($new_a8002, $a8002); 
			   */
			   
			   
			  $a8003 		= $xml->getElementsByTagName('a8003')->item($i);
			  $new_a8003	= $xml->createElement('a8003', formato_banco($vale_refeicao)); 
			  $a8003->parentNode->replaceChild($new_a8003, $a8003); 
			  
			  /*
			  $a8004 		= $xml->getElementsByTagName('a8004')->item($i);
			  $new_a8004	= $xml->createElement('a8004', 'CERTO'); 
			  $a8004->parentNode->replaceChild($new_a8004, $a8004); 
			  
			  $a8005 		= $xml->getElementsByTagName('a8005')->item($i);
			  $new_a8005	= $xml->createElement('a8005', 'CERTO'); 
			  $a8005->parentNode->replaceChild($new_a8005, $a8005); 
			  
			  $a8006 		= $xml->getElementsByTagName('a8006')->item($i);
			  $new_a8006	= $xml->createElement('a8006', 'CERTO'); 
			  $a8006->parentNode->replaceChild($new_a8006, $a8006); 
			  
			  $a8080 		= $xml->getElementsByTagName('a8080')->item($i);
			  $new_a8080	= $xml->createElement('a8080', 'CERTO'); 
			  $a8080->parentNode->replaceChild($new_a8080, $a8080); 
			  
			  $a9000 		= $xml->getElementsByTagName('a9000')->item($i);
			  $new_a9000	= $xml->createElement('a9000', 'CERTO'); 
			  $a9000->parentNode->replaceChild($new_a9000, $a9000); 
			  
			  $a9500 		= $xml->getElementsByTagName('a9500')->item($i);
			  $new_a9500	= $xml->createElement('a9500', 'CERTO'); 
			  $a9500->parentNode->replaceChild($new_a9500, $a9500); 
			  
			  $a9999 		= $xml->getElementsByTagName('a9999')->item($i);
			  $new_a9999	= $xml->createElement('a9999', 'CERTO'); 
			  $a9999->parentNode->replaceChild($new_a9999, $a9999); 
			  
			  $a50220 		= $xml->getElementsByTagName('a50220')->item($i);
			  $new_a50220	= $xml->createElement('a50220', 'CERTO'); 
			  $a50220->parentNode->replaceChild($new_a50220, $a50220); 
			  */
			  
			  $a50222 		= $xml->getElementsByTagName('a50222')->item($i);
			  $new_a50222	= $xml->createElement('a50222', $filhos_familia); 
			  $a50222->parentNode->replaceChild($new_a50222, $a50222); 
			  
			  /*
			  $a50272 		= $xml->getElementsByTagName('a50272')->item($i);
			  $new_a50272	= $xml->createElement('a50272', 'CERTO'); 
			  $a50272->parentNode->replaceChild($new_a50272, $a50272); 
			  */
			  
			  $a50292 		= $xml->getElementsByTagName('a50292')->item($i);
			  $new_a50292	= $xml->createElement('a50292', $filhos_irrf); 
			  $a50292->parentNode->replaceChild($new_a50292, $a50292); 
			  /*
			  $a50372 		= $xml->getElementsByTagName('a50372')->item($i);
			  $new_a50372	= $xml->createElement('a50372', 'CERTO'); 
			  $a50372->parentNode->replaceChild($new_a50372, $a50372); 
			  */
			  $a50492 		= $xml->getElementsByTagName('a50492')->item($i);
			  $new_a50492	= $xml->createElement('a50492',$filhos_irrf); 
			  $a50492->parentNode->replaceChild($new_a50492, $a50492); 
			  
			  $a80002 		= $xml->getElementsByTagName('a80002')->item($i);
			  $new_a80002	= $xml->createElement('a80002', $dias_faltas); 
			  $a80002->parentNode->replaceChild($new_a80002, $a80002); 
			  
			  /*
			  $a50111 		= $xml->getElementsByTagName('a50111')->item($i);
			  $new_a50111	= $xml->createElement('a50111', 'CERTO'); 
			  $a50111->parentNode->replaceChild($new_a50111, $a50111); 			  
			  
			  */
			  	
			 if(sizeof( $ids_movimentos_update_individual) != 0){			 	
				
				
				  $ids_movimentos 		= $xml->getElementsByTagName('ids_movimentos')->item($i);
				  $new_ids_movimentos	= $xml->createElement('ids_movimentos', $ids_movimentos_update_individual); 
				  $ids_movimentos->parentNode->replaceChild($new_ids_movimentos, $ids_movimentos); 
			  
			  }
			 /*
			  
			
				$status 		= $xml->getElementsByTagName('status')->item($i);
					  $new_status	= $xml->createElement('status', 3); 
			  $status->parentNode->replaceChild($new_status, $status);
			 */ 
			  
			  $valor_ferias 		= $xml->getElementsByTagName('valor_ferias')->item($i);
			  $new_valor_ferias	= $xml->createElement('valor_ferias', formato_banco($valor_ferias)); 
			  $valor_ferias->parentNode->replaceChild($new_valor_ferias, $valor_ferias); 
			  
			  $valor_pago_ferias 		= $xml->getElementsByTagName('valor_pago_ferias')->item($i);
			  $new_valor_pago_ferias	= $xml->createElement('valor_pago_ferias', formato_banco($desconto_ferias)); 
			  $valor_pago_ferias->parentNode->replaceChild($new_valor_pago_ferias, $valor_pago_ferias); 
			  
			  
			  $inss_ferias 		= $xml->getElementsByTagName('inss_ferias')->item($i);
			  $new_inss_ferias	= $xml->createElement('inss_ferias', formato_banco($inss_ferias)); 
			  $inss_ferias->parentNode->replaceChild($new_inss_ferias, $inss_ferias); 
			  
			  $ir_ferias 		= $xml->getElementsByTagName('ir_ferias')->item($i);
			  $new_ir_ferias	= $xml->createElement('ir_ferias', formato_banco($decimo_terceiro_credito)); 
			  $ir_ferias->parentNode->replaceChild($new_ir_ferias, $ir_ferias); 
			  
			  $fgts_ferias 		= $xml->getElementsByTagName('fgts_ferias')->item($i);
			  $new_fgts_ferias	= $xml->createElement('fgts_ferias', formato_banco($fgts_ferias)); 
			  $fgts_ferias->parentNode->replaceChild($new_fgts_ferias, $fgts_ferias); 
			  
			  
			  $valor_dt 		= $xml->getElementsByTagName('valor_dt')->item($i);
			  $new_valor_dt	= $xml->createElement('valor_dt', formato_banco($decimo_terceiro_credito)); 
			  $valor_dt->parentNode->replaceChild($new_valor_dt, $valor_dt); 
			  
			  
			  
			  
			  
			  $inss_dt 		= $xml->getElementsByTagName('inss_dt')->item($i);
			  $new_inss_dt	= $xml->createElement('inss_dt', formato_banco($inss_dt)); 
			  $inss_dt->parentNode->replaceChild($new_inss_dt, $inss_dt); 
			  
			  $ir_dt 		= $xml->getElementsByTagName('ir_dt')->item($i);
			  $new_ir_dt	= $xml->createElement('ir_dt', formato_banco($irrf_dt)); 
			  $ir_dt->parentNode->replaceChild($new_ir_dt, $ir_dt); 
			  
			  /*
			  $fgts_dt 		= $xml->getElementsByTagName('fgts_dt')->item($i);
			  $new_fgts_dt	= $xml->createElement('fgts_dt', 'CERTO'); 
			  $fgts_dt->parentNode->replaceChild($new_fgts_dt, $fgts_dt); 
			  
			  $valor_rescisao 		= $xml->getElementsByTagName('valor_rescisao')->item($i);
			  $new_valor_rescisao	= $xml->createElement('valor_rescisao', 'CERTO'); 
			  $valor_rescisao->parentNode->replaceChild($new_valor_rescisao, $valor_rescisao); 
			  */
			  
			  $valor_pago_rescisao 		= $xml->getElementsByTagName('valor_pago_rescisao')->item($i);
			  $new_valor_pago_rescisao	= $xml->createElement('valor_pago_rescisao', formato_banco($valor_rescisao)); 
			  $valor_pago_rescisao->parentNode->replaceChild($new_valor_pago_rescisao, $valor_pago_rescisao); 
			  
			  $inss_rescisao 		= $xml->getElementsByTagName('inss_rescisao')->item($i);
			  $new_inss_rescisao	= $xml->createElement('inss_rescisao', formato_banco($inss_rescisao)); 
			  $inss_rescisao->parentNode->replaceChild($new_inss_rescisao, $inss_rescisao); 
			  
			  $ir_rescisao 		= $xml->getElementsByTagName('ir_rescisao')->item($i);
			  $new_ir_rescisao	= $xml->createElement('ir_rescisao', formato_banco($irrf_rescisao)); 
			  $ir_rescisao->parentNode->replaceChild($new_ir_rescisao, $ir_rescisao); 
			  
			 
			  
			  
			  
			  
			  
			  /*
			  $fgts_rescisao 		= $xml->getElementsByTagName('fgts_rescisao')->item($i);
			  $new_fgts_rescisao	= $xml->createElement('fgts_rescisao', 'CERTO'); 
			  $fgts_rescisao->parentNode->replaceChild($new_fgts_rescisao, $fgts_rescisao); 
			  
			  $desconto_inss 		= $xml->getElementsByTagName('desconto_inss')->item($i);
			  $new_desconto_inss	= $xml->createElement('desconto_inss', 'CERTO'); 
			  $desconto_inss->parentNode->replaceChild($new_desconto_inss, $desconto_inss); 
			  
			  
			  $tipo_desconto_inss 		= $xml->getElementsByTagName('tipo_desconto_inss')->item($i);
			  $new_tipo_desconto_inss	= $xml->createElement('tipo_desconto_inss', 'CERTO'); 
			  $tipo_desconto_inss->parentNode->replaceChild($new_tipo_desconto_inss, $tipo_desconto_inss); 
			  
			 
			  $arquivo 		= $xml->getElementsByTagName('arquivo')->item($i);
			  $new_arquivo	= $xml->createElement('arquivo', 'CERTO'); 
			  $arquivo->parentNode->replaceChild($new_arquivo, $arquivo); 
			  
			  
			  $tipo_pg 		= $xml->getElementsByTagName('tipo_pg')->item($i);
			  $new_tipo_pg	= $xml->createElement('tipo_pg', 'CERTO'); 
			  $tipo_pg->parentNode->replaceChild($new_tipo_pg, $tipo_pg); 
			   
			  
			  $folha_proc_salario_outra_empresa 		= $xml->getElementsByTagName('folha_proc_salario_outra_empresa')->item($i);
			  $new_folha_proc_salario_outra_empresa	= $xml->createElement('folha_proc_salario_outra_empresa', 'CERTO'); 
			  $folha_proc_salario_outra_empresa->parentNode->replaceChild($new_folha_proc_salario_outra_empresa, $folha_proc_salario_outra_empresa); 
			  
			
			  $folha_proc_desconto_outra_empresa 		= $xml->getElementsByTagName('folha_proc_desconto_outra_empresa')->item($i);
			  $new_folha_proc_desconto_outra_empresa	= $xml->createElement('folha_proc_desconto_outra_empresa', 'CERTO'); 
			  $folha_proc_desconto_outra_empresa->parentNode->replaceChild($new_folha_proc_desconto_outra_empresa, $folha_proc_desconto_outra_empresa); 
			   
			  
			  
			  $folha_proc_diferenca_inss 		= $xml->getElementsByTagName('folha_proc_diferenca_inss')->item($i);
			  $new_folha_proc_diferenca_inss	= $xml->createElement('folha_proc_diferenca_inss', 'CERTO'); 
			  $folha_proc_diferenca_inss->parentNode->replaceChild($new_folha_proc_diferenca_inss, $folha_proc_diferenca_inss); 
			  
			   */
			  $hora_trabalhada 		= $xml->getElementsByTagName('hora_trabalhada')->item($i);
			  $new_hora_trabalhada	= $xml->createElement('hora_trabalhada', $horas); 
			  $hora_trabalhada->parentNode->replaceChild($new_hora_trabalhada, $hora_trabalhada); 
			  
			  /*
			  $hora_extra 		= $xml->getElementsByTagName('hora_extra')->item($i);
			  $new_hora_extra	= $xml->createElement('hora_extra', 'CERTO'); 
			  $hora_extra->parentNode->replaceChild($new_hora_extra, $hora_extra); 
			  
			  
			  $hora_desconto 		= $xml->getElementsByTagName('hora_desconto')->item($i);
			  $new_hora_desconto	= $xml->createElement('hora_desconto', 'CERTO'); 
			  $hora_desconto->parentNode->replaceChild($new_hora_desconto, $hora_desconto); 
			  
			  
			  $financeiro 		= $xml->getElementsByTagName('financeiro')->item($i);
			  $new_financeiro	= $xml->createElement('financeiro', 'CERTO'); 
			  $financeiro->parentNode->replaceChild($new_financeiro, $financeiro); 
			  */
				$hora_noturna		= $xml->getElementsByTagName('hora_noturna')->item($i);
				$new_hora_noturna   = $xml->createElement('hora_noturna', $horas_noturnas); 
				$hora_noturna->parentNode->replaceChild($new_hora_noturna, $hora_noturna); 
			  
				$adicional_noturno		= $xml->getElementsByTagName('adicional_noturno')->item($i);
				$new_adicional_noturno	= $xml->createElement('adicional_noturno', formato_banco($adicional_noturno_mes)); 
				$adicional_noturno->parentNode->replaceChild($new_adicional_noturno, $adicional_noturno); 
				
				
				$horas_atraso_campo		= $xml->getElementsByTagName('horas_atraso')->item($i);
				$new_horas_atraso		= $xml->createElement('horas_atraso', $horas_atraso); 
				$horas_atraso_campo->parentNode->replaceChild($new_horas_atraso, $horas_atraso_campo); 
				
				$valor_DSR 		= $xml->getElementsByTagName('dsr')->item($i);
				$new_valor_DSR	= $xml->createElement('dsr', formato_banco($DSR)); 
				$valor_DSR->parentNode->replaceChild($new_valor_DSR, $valor_DSR); 


$valor_desconto_aux_distancia 		= $xml->getElementsByTagName('desconto_auxilio_distancia')->item($i);
$new_valor_desconto_aux_distancia	= $xml->createElement('desconto_auxilio_distancia', formato_banco($desconto_aux_distancia)); 
$valor_desconto_aux_distancia->parentNode->replaceChild($new_valor_desconto_aux_distancia, $valor_desconto_aux_distancia); 


			file_put_contents ("xml_horista/$id_folha.xml", $xml->saveXML());			  
}











echo json_encode($JSON);
	
}

?>