<?php 
include('../conn.php');
include('../classes/calculos.php');

$Calc_ir = new calculos();


$id_folha_participante = $_GET['id_folha_participante'];


$rendimentos		 = mysql_real_escape_string($_GET['rendimentos']);
$horas_trabalhadas   = mysql_real_escape_string($_GET['horas_trab']);
$descontos 			 = mysql_real_escape_string($_GET['descontos']);
$ajuda_custo 		 = mysql_real_escape_string($_GET['ajuda_custo']);
$sal_base 			 = mysql_real_escape_string($_GET['sal_base']);
$id_cooperado  		 = mysql_real_escape_string($_GET['id_coop']);
$ano_folha 			 = mysql_real_escape_string($_GET['ano_folha']);
$mes_anterior 		 = mysql_real_escape_string($_GET['mes_anterior']);



if(isset($_GET['id_folha_participante'])) {

$update = mysql_query("UPDATE folha_cooperado SET adicional = '$rendimentos',
												   faltas = '$horas_trabalhadas',
												   desconto = '$descontos',
												   ajuda_custo = '$ajuda_custo'
												    WHERE id_folha_pro = '".$id_folha_participante."' LIMIT 1") or die(mysql_error());

}


////////////////////////////////////////////////
///////////////////// Calculando INSS  ///////
///////////////////////////////////////////////
$base_inss = $sal_base;
$base_inss += $rendimentos;
$base_inss -=  $descontos;


// Consulta de Dados do Participante
$qr_cooperado  = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_cooperado'");
$row_cooperado = mysql_fetch_array($qr_cooperado);


$qr_inss  = mysql_query("SELECT faixa, fixo, percentual, piso, teto
						   FROM rh_movimentos
						  WHERE cod = '5024'
							AND CURDATE() BETWEEN data_ini AND data_fim");
$row_inss = mysql_fetch_array($qr_inss);

$minimo_inss = $row_inss['piso'];
$maximo_inss = $row_inss['teto'];


// Verifica se o INSS é Definido ou Percentual
if($row_cooperado['tipo_inss'] == 1) { // Valor Definido
  
    $inss = $row_cooperado['inss'];
  	
} else { // Valor Percentual
  
 
    if($row_cooperado['tipo_contratacao'] == 3) {
	    $taxa_inss = $row_cooperado['inss'] / 100;
    } else {
	    $taxa_inss = 0.2;
    }
	
	
	$inss = $base_inss * $taxa_inss;
	
    if($inss > $maximo_inss) {
	    $inss = $maximo_inss;
    }
	
	
  
}
//////////////////////////////////////////////////



//////////////////////////////////////////////////
/////////////  calculo do IR     ///////////////////
///////////////////////////////////////////////////
$base_irrf = $sal_base + $rendimentos - $descontos - $inss;
$minimo_irrf = 10.00; 


// Vestígio de IRRF do mês anterior
$qr_vestigio    = mysql_query("SELECT irrf FROM folha_cooperado WHERE mes = '$mes_anterior' AND id_autonomo = '$id_cooperado' 
AND status = '3' AND irrf <= '$minimo_irrf'");
$row_vestigio   = mysql_fetch_array($qr_vestigio);
$total_vestigio = mysql_num_rows($qr_vestigio);
$valor_vestigio = $row_vestigio['irrf'];

///verifica se entra IR
$qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5021' AND anobase = '2011' AND faixa = 1 " ) or die(mysql_error());
$row_ddir = mysql_fetch_assoc($qr_ddir);




if($base_irrf > $row_ddir['v_ini']) {

		// Dependentes
		$RE_Depe = mysql_query ("SELECT * FROM dependentes WHERE id_bolsista = '$id_cooperado' ");
		$RowDepe = mysql_fetch_array($RE_Depe);
		$total_dep = mysql_num_rows($RE_Depe);
		
		
		/////Verificação de dependentes
		if( $total_dep != 0) {
			$qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5049' AND anobase = '$ano_folha'");
			$row_ddir = mysql_fetch_assoc($qr_ddir);
			
			for($i = 0; $i<$total_dep;$i++){
				$irrf = $base_irrf - $row_ddir['fixo'];
			}	
		} else {
		
			$irrf = $base_irrf;
			
		}




		///Verificando faixa dO Imposto de renda
		$qr_ddir = mysql_query("SELECT * FROM rh_movimentos WHERE cod= '5021' AND anobase = '$ano_folha' ORDER BY faixa ASC" );
		while($row_ddir = mysql_fetch_assoc($qr_ddir)):
		
		if($irrf > $row_ddir['v_ini'] and $irrf< $row_ddir['v_fim'] ) {
			
			$irrf2 = $irrf*$row_ddir['percentual'];
			$irrf2 = $irrf2 - $row_ddir['fixo'];
			
		} 
		endwhile;


} else {
	
 $irrf2 = '0.00';	
}

if($irrf2 < $minimo_irrf) {
	$resultado_irrf =  '0.00';
} else {
	$resultado_irrf = $irrf2;
}




echo json_encode( array('irrf' => $resultado_irrf , 'inss' => $inss));


