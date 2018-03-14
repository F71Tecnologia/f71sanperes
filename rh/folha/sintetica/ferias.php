<?php
//$qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$clt' AND status = '1' AND ano = '$ano' ORDER BY id_ferias DESC LIMIT 1");
$qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$clt' AND status = '1'  ORDER BY id_ferias DESC LIMIT 1"); // REMOVIDO O ANO PARA PROCESSAR FERIAS QUE COMEÇA EM UM ANO E TERMINA EM OUTRO
$row_ferias = mysql_fetch_array($qr_ferias);
$num_ferias = mysql_num_rows($qr_ferias);

if(!empty($num_ferias)) {

  
    
// Início das Férias entre o Início e Fim da Folha
if($row_ferias['data_ini'] >= $row_folha['data_inicio'] and $row_ferias['data_ini'] <= $row_folha['data_fim']) {

	$inicio = $row_ferias['data_ini'];
	
       
        
	// Se o Fim das Férias for antes do Fim da Folha
	if($row_ferias['data_retorno'] < $row_folha['data_fim']) {
		$fim = $row_ferias['data_retorno'];
	// Fim das Férias depois do Fim da Folha
	} else {
		$fim = date('Y-m-d', strtotime("$row_folha[data_fim] + 1 day"));
	}
	
	$ferias = true;
        
  
        

// Fim das Férias entre o Início e Fim da Folha
} elseif($row_ferias['data_fim'] >= $row_folha['data_inicio'] and $row_ferias['data_fim'] <= $row_folha['data_fim']) {
	
	// Se o Início das Férias for depois do Início da Folha
	if($row_ferias['data_ini'] > $row_folha['data_inicio']) {
		$inicio = $row_ferias['data_ini'];
	// Início das Férias antes do Início da Folha
	} else {
		$inicio = $row_folha['data_inicio'];
	}
	
	$fim = $row_ferias['data_retorno'];
	
	$ferias = true;
		
        
    
        
}



// Tem Férias
if(isset($ferias)) {
	
	// Calcula a diferença de dias
	$dias_ferias = abs((int)floor((strtotime($inicio) - strtotime($fim)) / 86400));
        $dias_ferias = ($dias_ferias == 31)? 30: $dias_ferias;
       
        
	list($nulo, $mes_inicio, $nulo) = explode('-', $row_ferias['data_ini']);
	list($nulo, $mes_fim,    $nulo) = explode('-', $row_ferias['data_fim']);
	
        
         $inss_porcentagem = $row_ferias['inss_porcentagem']/100;     
	
	
	$fgts_ferias = $base_fgts_ferias * 0.08;      
	
	 //Definindo INSS, IRRF e FGTS referente ao mês das férias
	if((int)$mes_inicio == (int)$mes_fim) {               
                $base_inss_ferias   = $row_ferias['total_remuneracoes'];
                $base_fgts_ferias   = $row_ferias['total_remuneracoes'];
		$inss_ferias        = $row_ferias['inss'];
		$irrf_ferias        = $row_ferias['ir'];
		$fgts_ferias        = $row_ferias['fgts'];
                $valor_ferias       = $row_ferias['total_remuneracoes'] ;
                $desconto_ferias    = $row_ferias['total_liquido'];
               
	} else {
            
            if($row_folha['mes'] == $mes_inicio){
            
            
                $base_inss_ferias = $row_ferias['total_remuneracoes1'];
                $base_fgts_ferias = $row_ferias['total_remuneracoes1'] ;
               // $inss_ferias      = ($row_ferias['inss'] );
		$irrf_ferias      = ($row_ferias['ir'] );
		$fgts_ferias      = ($row_ferias['fgts']);
                $valor_ferias     = $row_ferias['total_remuneracoes1'] ;
                $desconto_ferias  = $row_ferias['total_remuneracoes1']  - $irrf_ferias;
                
            }else {
                
                $base_inss_ferias  = $row_ferias['total_remuneracoes2'];
                $base_fgts_ferias  = $row_ferias['total_remuneracoes2'] ;
               // $inss_ferias       = ($row_ferias['inss'] );
		//$irrf_ferias       = ($row_ferias['ir']  );
		//$fgts_ferias       = ($row_ferias['fgts'] );
                $valor_ferias      = $row_ferias['total_remuneracoes2'];
                $desconto_ferias = $row_ferias['total_remuneracoes2'] ;
                
            }
	}    
        
      }


}           
?>