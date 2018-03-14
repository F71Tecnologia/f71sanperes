<?php
$qr_rescisao  = mysql_query("SELECT *,IF(motivo != 65, aviso_valor,'') as aviso_credito 
                            FROM rh_recisao 
                            WHERE id_clt = '$clt' AND status = '1' AND MONTH(data_demi) = '$mes' ");
$row_rescisao = mysql_fetch_array($qr_rescisao);
$num_rescisao = mysql_num_rows($qr_rescisao);

if(!empty($num_rescisao)) {
	
// Variáveis para Linha e Update do Participante
$dias          = $row_rescisao['dias_saldo'];
$salario       = $row_rescisao['saldo_salario'];
$base          = $row_rescisao['sal_base'];

$base_inss      = $salario;
$base_irrf      = $salario;
$base_fgts      = $salario;


if( (!empty($row_rescisao['base_inss_ss']) and $row_rescisao['base_inss_ss'] !='0.00') 
   or (!empty($row_rescisao['base_inss_13']) and $row_rescisao['base_inss_13'] !='0.00') ){
    
   $base_inss_13_rescisao = $row_rescisao['base_inss_13'];
   $base_inss             = $row_rescisao['base_inss_ss'];
   $base_irrf             = $row_rescisao['base_irrf_ss'] + $row_rescisao['base_irrf_13'];
   $base_fgts             = $row_rescisao['base_inss_ss'];
   
} else {

        $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao, B.categoria
        FROM rh_movimentos_rescisao as A 
        INNER JOIN
        rh_movimentos as B
        ON A.id_mov = B.id_mov
        WHERE A.id_clt = '$row_rescisao[id_clt]' 
        AND A.id_rescisao = '$row_rescisao[id_recisao]' 
        AND A.status = 1 AND A.incidencia = '5020,5021,5023'") or die(mysql_error());
        while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  

              $movimentos_resc[$row_movimentos['campo_rescisao']] += $row_movimentos['valor']; 

                if($row_movimentos['categoria'] == 'CREDITO'){
                    $base_inss += $row_movimentos['valor'];
                    $base_irrf += $row_movimentos['valor'];
                    $base_fgts += $row_movimentos['valor'];
                }elseif($row_movimentos['categoria'] == 'DEBITO' or $row_movimentos['categoria'] == 'DESCONTO'){
                    $base_inss -= $row_movimentos['valor'];
                    $base_irrf -= $row_movimentos['valor'];
                    $base_fgts -= $row_movimentos['valor'];
                }
        }


        $base_inss_13_rescisao   = $row_rescisao['dt_salario'];
        $base_inss_13_rescisao  += $row_rescisao['terceiro_ss'];
            
        //$base_inss      += $row_rescisao['aviso_credito'];    
        $base_inss      += $row_rescisao['insalubridade'];
        //$base_inss      += $row_rescisao['valor_lei_12_506'];


        $base_irrf      += $row_rescisao['dt_salario'] ;
        $base_irrf      += $row_rescisao['terceiro_ss'] ;
        $base_irrf      += $row_rescisao['insalubridade'];
       // $base_irrf      += $row_rescisao['aviso_credito']; 
      //  $base_irrf      += $row_rescisao['valor_lei_12_506'];


        //$base_fgts      += $row_rescisao['aviso_credito'];
         // $base_fgts      += $row_rescisao['dt_salario'] ;
        //$base_fgts      += $row_rescisao['terceiro_ss'] ;
       $base_fgts      += $row_rescisao['insalubridade'];
       // $base_fgts      += $row_rescisao['valor_lei_12_506'];	
}
	
	$inss_rescisao = $row_rescisao['previdencia_ss'] + $row_rescisao['previdencia_dt'];
	$inss_completo = $inss_rescisao;
	
	
      
	$irrf_rescisao = $row_rescisao['ir_ss'] + $row_rescisao['ir_dt'];
	$irrf_completo = $irrf_rescisao;
	
	$rendimentos   = $row_rescisao['total_rendimento'] - $row_rescisao['saldo_salario'];
	$descontos     = $row_rescisao['total_deducao'] + $row_rescisao['total_liquido'] - $inss_completo - $irrf_completo;
                
        $toDescontos = $descontos + $inss_completo + $irrf_completo;
        $toRendimentos = $salario + $rendimentos;
        
        
        
         $liquido = round($toRendimentos - $toDescontos, 2); 
            
           
        if($base_inss < 0) { $base_inss = 0;}
        if($base_irrf < 0) { $base_irrf = 0;}
        if($base_fgts < 0) { $base_fgts = 0;}      
        if($liquido <=0.01){ $liquido =   0;} 
	
	// Variáveis para Estatistica do Participante
	$Trab     -> calculo_proporcional($salario_limpo, $dias);
        $valor_dia = $Trab -> valor_dia;
	
	// Variáveis para Estatistica da Folha
	$valor_rescisao    = $rendimentos;
	$desconto_rescisao = $descontos;
        
        $rescisao_status_clt = $row_rescisao['motivo'];
unset($gratificacao,$adicional_noturno,$hora_extra,$dsr,$movimentos_resc);	
}


?>