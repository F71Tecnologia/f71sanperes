<?php
//RENDIMENTOS
$movimentos_folha[$folha['id_clt']]['CREDITO']['SALÁRIO FAMILIA'] = $folha['a5022'];

if($folha['status_clt'] == 50){  
  $movimentos_folha[$folha['id_clt']]['CREDITO']['SALÁRIO MATERNIDADE'] = $folha['a6005']; 
  $movimentos_folha[$folha['id_clt']]['CREDITO']['SALÁRIO BASE'] = $folha['sallimpo_real'];
  $base = $folha['a6005']+$folha['sallimpo_real'];
}else {
    
    if($folha['mes_admissao'] == $folha['mes'] or $folha['status_clt'] == 40){      
       $base = $folha['sallimpo_real'];
    } else {        
        $base = $folha['sallimpo'];
    }    
    $movimentos_folha[$folha['id_clt']]['CREDITO']['SALÁRIO BASE'] = $base;
}


$qr_ferias = mysql_query("SELECT *, MONTH(data_ini) as mes1,DAYOFMONTH(data_ini) as dias_mes1,YEAR(data_ini) as ano1, MONTH(data_fim) as mes2
                           FROM rh_ferias 
                           WHERE id_clt = '$folha[id_clt]' AND status = 1");
if(mysql_num_rows($qr_ferias) != 0){
    
  
    $row_ferias = mysql_fetch_assoc($qr_ferias);
    
   
    
    //FÉRIAS NO MESMO MÊS
    if($row_ferias['mes1'] == $row_ferias['mes2'] and $row_ferias['mes1'] == $folha['mes']){   
        
            $valor_total_ferias       = $row_ferias['valor_total_ferias'];
            $acrescimo_constitucional = $row_ferias['umterco'];
            $total_remuneracoes       = $row_ferias['total_remuneracoes'];
            $valor_inss_ferias                = $row_ferias['inss'];
            $valor_irrf_ferias                = $row_ferias['ir'];
            $pensao_alimenticia        = $row_ferias['pensao_alimenticia'];
            $abono_pecuniario         = $row_ferias['abono_pecuniario'];
            $umterco_abono_pecuniario = $row_ferias['umterco_abono_pecuniario'];
            $nome_valor_ferias        = $row_ferias['dias_ferias'].' DIAS A R$ '.$row_ferias['valor_dias_ferias'];
            $total_liquido_ferias     = $row_ferias['total_liquido'];
          
    } else {


        $diasmes      = cal_days_in_month(CAL_GREGORIAN, $row_ferias['mes1'], $row_ferias['ano1']);
        $dias_ferias  = $diasmes - $row_ferias['dias_mes1'] + 1;
        $dias_ferias2 = $row_ferias['dias_ferias'] - $dias_ferias;
       
        echo $row_ferias['mes1'].' - '. $folha['mes'];
        if($row_ferias['mes1'] == $folha['mes']){

            // Periodo 1
            $valor_total_ferias       = $row_ferias['valor_total_ferias1'];
            $acrescimo_constitucional = $row_ferias['acrescimo_constitucional1'];
            $total_remuneracoes       = $ferias['total_remuneracoes1'];
            $valor_inss_ferias                = $row_ferias['inss'];
            $valor_irrf_ferias               = $row_ferias['ir'];
            $pensao_alimenticia        = $row_ferias['pensao_alimenticia'];
            $nome_valor_ferias = $dias_ferias.' DIAS A R$ '.$row_ferias['valor_dias_ferias'];
            $abono_pecuniario         = $row_ferias['abono_pecuniario'];
            $umterco_abono_pecuniario = $row_ferias['umterco_abono_pecuniario'];
            

        } elseif($row_ferias['mes2'] == $folha['mes']){

            // Periodo 2
            $valor_total_ferias       = $row_ferias['valor_total_ferias2'];
            $acrescimo_constitucional = $row_ferias['acrescimo_constitucional2'];
            $total_remuneracoes       = $row_ferias['total_remuneracoes2'];
            $valor_inss_ferias               = $total_remuneracoes;
            $nome_valor_ferias        = $dias_ferias2.' DIAS A R$ '.$row_ferias['valor_dias_ferias'];
        }
    }




    
    
    ////RENDIMENTO
    $movimentos_folha[$folha['id_clt']]['CREDITO']['ABONO PECUNIÁRIO']             = $abono_pecuniario;
    $movimentos_folha[$folha['id_clt']]['CREDITO']['1/3 SOBRE ABONO PECUNIÁRIO']   = $umterco_abono_pecuniario;
    $movimentos_folha[$folha['id_clt']]['CREDITO']['ACRÉSCIMO CONSTITUCIONAL 1/3'] = $acrescimo_constitucional;
    $movimentos_folha[$folha['id_clt']]['CREDITO'][$nome_valor_ferias]             = $valor_total_ferias;
  
   
   
$movimentos_folha[$folha['id_clt']]['BASE INSS'] +=  $acrescimo_constitucional +$valor_total_ferias;

    //DESCONTOS
    $movimentos_folha[$folha['id_clt']]['CREDITO']['PENSÃO ALIMENTÍCIA'] = $pensao_alimenticia;
    $movimentos_folha[$folha['id_clt']]['DEBITO']['INSS FÉRIAS'] = $valor_inss;
    $movimentos_folha[$folha['id_clt']]['DEBITO']['IRRF FÉRIAS'] = $valor_irrf;
    $movimentos_folha[$folha['id_clt']]['DEBITO']['TOTAL LÍQUIDO PAGO EM FÉRIAS'] = $total_liquido_ferias;

      unset($valor_total_ferias1,$acrescimo_constitucional1,$total_remuneracoes1,$valor_inss,$valor_irrf,$pensao_alimenticia,
                  $abono_pecuniario, $umterco_abono_pecuniario,$total_liquido_ferias,$valor_total_ferias2,$acrescimo_constitucional2,
                 $total_remuneracoes2, $valor_inss2);
  
} 


//DESCONTOS 
$movimentos_folha[$folha['id_clt']]['DEBITO']['INSS'] = $folha['a5020'];
$movimentos_folha[$folha['id_clt']]['DEBITO']['IRRF'] = $folha['a5021'];
$movimentos_folha[$folha['id_clt']]['DEBITO']['DESCONTO VALE TRANSPORTE'] = $folha['a7001'];


///BASES 
$movimentos_folha[$folha['id_clt']]['BASE INSS'] += $base - $movimentos_folha[$folha['id_clt']]['DEBITO']['FALTA'] ;


$movimentos_folha[$folha['id_clt']]['BASE IRRF'] +=  $base;
$movimentos_folha[$folha['id_clt']]['BASE IRRF'] -=  $folha['a5020'];  ///RETIRANDO O VALOR DO INSS
$movimentos_folha[$folha['id_clt']]['BASE FGTS'] +=  $base;


if($folha['status_clt'] == 10) {
        $base_inss_folha_normal +=$movimentos_folha[$folha['id_clt']]['BASE INSS']; 
}


?> 

