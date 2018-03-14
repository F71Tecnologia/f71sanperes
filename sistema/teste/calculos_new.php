<?php              


class  Calculos_new{
    
    private $RH_MOVIMENTOS;
    /**
     *@param type $ano Necessário para pegar as faixas do ano
     */
    public function __construct($ano ) { 
        $this->Carrega_movimentos($ano);
        
    }
    
    
    /**
     * 
     * @param type $anobase Declara ano para base de cálculo
     */
    public function Carrega_movimentos($anobase){
        
       $qr_impostos = mysql_query("SELECT id_mov,cod, descicao,categoria, faixa, v_ini, v_fim, percentual, fixo, piso, teto, anobase
                                    FROM rh_movimentos 
                                    WHERE (cod IN(0001,5020,5021,5022,5049, 50241, 6007) AND anobase = '$anobase') OR anobase = 0 ;") or die(mysql_error());
       while($row_mov = mysql_fetch_assoc($qr_impostos)){           
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['id_mov']     = $row_mov['id_mov'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['cod']        = $row_mov['cod'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['descicao']   = $row_mov['descicao'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['categoria']   = $row_mov['categoria'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['v_ini']      = $row_mov['v_ini'];   
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['v_fim']      = $row_mov['v_fim'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['percentual'] = $row_mov['percentual'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['fixo']       = $row_mov['fixo'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['piso']       = $row_mov['piso'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['teto']       = $row_mov['teto'];
           $RH_MOVIMENTOS[$row_mov['cod']][$row_mov['faixa']]['anobase']    = $row_mov['anobase'];
       }
       
       $this->RH_MOVIMENTOS = $RH_MOVIMENTOS; 
       
    }
    
    
   /**CARREGA OS DEPENDENTE DE IRRF 
    * 
    * @param type $id_clt
    * @param type $contratacao 1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
    * @return type
    */
    public function Carrega_dependentes_irrf($id_clt, $contratacao ){
      
          $qr_menor21 = mysql_query("SELECT  
                                    IF(data1 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho1,
                                    IF(data2 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho2,
                                    IF(data3 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho3,
                                    IF(data4 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho4,
                                    IF(data5 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho5,
                                    IF(data6 > DATE_SUB(CURDATE(), INTERVAL 21 YEAR), 1,0) as filho6,
                                     ddir_pai, ddir_mae, ddir_conjuge, portador_def1, portador_def2, portador_def3, portador_def4,  portador_def5,  portador_def6,
                                     ddir_avo_h, ddir_avo_m, ddir_bisavo_h, ddir_bisavo_m 
                                    FROM dependentes 
                                    WHERE id_bolsista = '$id_clt'  AND contratacao = $contratacao") or die(mysql_error()); 
            
            if(mysql_num_rows($qr_menor21) != 0){
                
           $row_menor = mysql_fetch_assoc($qr_menor21); 
           $total_filhos_menor_21 = 0;

           if($row_menor['filho1'] == 1 or $row_menor['portador_def1'] == 1 ){ $total_filhos_menor_21++; }           
           if($row_menor['filho2'] == 1 or $row_menor['portador_def2'] == 1){ $total_filhos_menor_21++; }
           if($row_menor['filho3'] == 1 or $row_menor['portador_def3'] == 1){ $total_filhos_menor_21++; }
           if($row_menor['filho4'] == 1 or $row_menor['portador_def4'] == 1){ $total_filhos_menor_21++; }
           if($row_menor['filho5'] == 1 or $row_menor['portador_def5'] == 1){ $total_filhos_menor_21++; }
           if($row_menor['filho6'] == 1 or $row_menor['portador_def6'] == 1){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_pai'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_mae'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_conjuge'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_avo_h'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_avo_m'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_bisavo_h'] == 1 ){ $total_filhos_menor_21++; }  			
           if($row_menor['ddir_bisavo_m'] == 1 ){ $total_filhos_menor_21++; }  			
	 
      
           return $total_filhos_menor_21;
	}      
    }
    /**CARREGA OS DEPENDENTES DO SALARIO FAMILIA
     * 
     * @param type $id_clt
     * @param type $contratacao  1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
     * @return int
     */
    public function Carrega_dependentes_sal_familia($id_clt, $contratacao){
        
          $qr_menor = mysql_query("SELECT  
                                        IF(data1 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho1,
                                        IF(data2 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho2,
                                        IF(data3 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho3,
                                        IF(data4 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho4,
                                        IF(data5 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho5,
                                        IF(data6 >= DATE_SUB(CURDATE(), INTERVAL 14 YEAR), 1,0) as filho6
                                    FROM dependentes 
                                    WHERE id_bolsista = '$id_clt' AND contratacao = '$contratacao'") or die(mysql_error()); 

        if(mysql_num_rows($qr_menor) != 0){
                    $row_menor = mysql_fetch_assoc($qr_menor);           
                    if($row_menor['filho1'] == 1){ $total_menor++; }
                    if($row_menor['filho2'] == 1){ $total_menor++; }
                    if($row_menor['filho3'] == 1){ $total_menor++; }
                    if($row_menor['filho4'] == 1){ $total_menor++; }
                    if($row_menor['filho5'] == 1){ $total_menor++; }
                    if($row_menor['filho6'] == 1){ $total_menor++; } 
        } else {
            $total_menor = 0;
        }
        
        return $total_menor;
    }
    
    
    
/** CALCULA SALARIO FAMILIA
 * @param type $base
 * @param type $id_clt
 * @param type $contratacao 1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
 * O cálculo do salário fampilia vai ser proporcional  somente no mês de admissão ou demissão do clt
 */ 
function Calcula_salariofamilia($base,$id_clt,$contratacao) {
	
    
        foreach($this->RH_MOVIMENTOS['5022'] as $valor ){           
            
            
          if( $valor['v_ini'] <= $base and $valor['v_fim'] >= $base){             
                            
                        $qnt_dependente_famila = $this->Carrega_dependentes_sal_familia($id_clt, $contratacao);
                        
                        if($qnt_dependente_famila != 0){
                            
                                    $fixo_familia               = $valor['fixo'];   
                                    ///$valor_proporcional_familia = $fixo_familia/30;
                                    $valor_sal_familia          = $qnt_dependente_famila * $fixo_familia;                                          
                          } 
                          
        $RETORNO['qnt_dependente']        = $qnt_dependente_famila;
        $RETORNO['valor_sal_familia']     = $valor_sal_familia;
        $RETORNO['fixo_familia']          = $fixo_familia;
        return $RETORNO;
        } 
      }   

}

/**
 * 
 * @param type                                $base Base de INSS
 * @param type                                $contratacao  1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
 * @param type $desc_inss_outra_empresa       1 - Possui desconto em outra empresa
 * @param type $tipo_desc_inss_outra_empresa   'isento' ou 'parcial'
 * @param type $salario_outra_empresa         Valor do salário na outra empresa
 * @param type $valor_desc_outra_empresa      Valor do desconto de inss em outra empresa
 */
function Calcula_INSS($base, $contratacao, $desc_inss_outra_empresa = NULL, $tipo_desc_inss_outra_empresa = NULL, $salario_outra_empresa = NULL, $valor_desc_inss_outra_empresa = NULL ) {

     
                switch($contratacao){            
                       case 1: $cod = 50241;
                           break;
                       case 2: $cod = 5020;
                           break;
                       case 3: $cod = '5024';
                           break;
                       case 4: $cod = '';
                           break;
                   }


                 foreach($this->RH_MOVIMENTOS[$cod] as $valor){

                     if($valor['v_ini'] <= $base and $valor['v_fim'] >= $base){

                       $percentual_inss        = $valor['percentual'];
                       $teto_inss              = $valor['teto'];
                       $valor_inss             = $base * $percentual_inss;
                       $valor_inss             = ($valor_inss> $teto_inss)? $teto_inss : $valor_inss;
                       
                       
                       //DESCONTO EM OUTRA EMPRESA
                       if($desc_inss_outra_empresa == '1') {			

                           if( $tipo_desc_inss_outra_empresa == 'isento') {  

                                       $valor_inss   = 0;

                              } elseif( $tipo_desc_inss_outra_empresa == 'parcial') {

                                       if(($valor_desc_inss_outra_empresa + $valor_inss)  > $teto_inss){                 

                                                   $valor_inss =  $teto_inss  - $valor_desc_inss_outra_empresa;
                                       }
                           }
                       }  

                       $RETORNO['valor_inss']                       = $valor_inss;
                       $RETORNO['percentual_inss']                  = $percentual_inss;
                       $RETORNO['teto_inss']                        = $teto_inss;
                       $RETORNO['desconto_inss_outra_empresa']      = $desc_inss_outra_empresa;
                       $RETORNO['tipo_desc_inss_outra_empresa']     = $tipo_desc_inss_outra_empresa;
                       $RETORNO['salario_outra_empresa']            = $salario_outra_empresa;
                       $RETORNO['valor_desc_inss_outra_empresa']   = $valor_desc_inss_outra_empresa;
                       
                       return $RETORNO;
                     }
                 }
      
      
}




/**
 * CALCULA IRRF
 * @param type $base  VALOR BASE - DESCONTO DE INSS
 * @param type $id_clt 
 * @param type $contratacao  1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
 * 
 */  
function Calcula_IRRF($base,$id_clt,$contratacao) {
    
              ///VERIFICAÇÂO DE DEPENDENTES   
              $qnt_dependente_irrf          = $this->Carrega_dependentes_irrf($id_clt, $contratacao);
                               
               if(!empty($qnt_dependente_irrf)) {

                       $valor_deducao_dep_ir_fixo   =  $this->RH_MOVIMENTOS[5049][1]['fixo'];
                       $valor_deducao_dep_ir_total  =  $qnt_dependente_irrf * $valor_deducao_dep_ir_fixo;                 
                       $base                        -= $valor_deducao_dep_ir_total;

               } else {
                       $valor_deducao_dep_ir_total = 0;
                       $valor_deducao_dep_ir_fixo  = 0;
                       $qnt_dependente_irrf        = 0;
               }
               
          
                foreach($this->RH_MOVIMENTOS[5021] as $valor){

                    if($valor['v_ini'] <= $base and $valor['v_fim'] >= $base){

                            $percentual_irrf              = $valor['percentual'];
                            $valor_parcela_deducao_irrf   = $valor['fixo'];
                            $valor_IR                     = ($base *   $percentual_irrf) - $valor_parcela_deducao_irrf;
                           
                            if($contratacao == 2) {

                                    $result_recolhimentoIR = mysql_query("SELECT recolhimento_ir FROM rh_clt WHERE id_clt = '$id_clt'");
                                    $row_recolhimentoIR    = mysql_fetch_assoc($result_recolhimentoIR);
                                    $recolhimento          = $row_recolhimentoIR['recolhimento_ir'];

                                    // Se o recolhimento não estiver vazio, soma o valor do IR mais o recolhimento
                                    if(!empty($recolhimento)) {  $valor_IR = $valor_IR + $recolhimento;   }

                                    // Se ainda assim o valor do IR mais o recolhimento for menor que 10 reais, atualiza o recolhimento 
                                    // e o valor do IR fica nulo
                                    if($valor_IR < 10) {
                                            $update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = '$valor_IR' WHERE id_clt = '$id_clt'";
                                            $valor_IR = 0;

                                    // Se o valor do IR mais o recolhimento for maior que 10 reais e o recolhimento não estiver vazio, 
                                    // o recolhimento fica nulo e o valor do IR permanece
                                    } elseif((!empty($recolhimento)) and ($valor_IR > 10)) {
                                           $update_recolhimentoIR = "UPDATE rh_clt SET recolhimento_ir = 0 WHERE id_clt = '$id_clt'";
                                    }
                            }

                            $RETORNO['percentual_irrf']                 = $percentual_irrf;
                            $RETORNO['valor_parcela_deducao_irrf']      = $valor_parcela_deducao_irrf;
                            $RETORNO['qnt_dependente_irrf']             = $qnt_dependente_irrf;
                            $RETORNO['valor_deducao_dep_ir_fixo']        = $valor_deducao_dep_ir_fixo;
                            $RETORNO['valor_deducao_dep_ir_total']       = $valor_deducao_dep_ir_total;
                            $RETORNO['valor_irrf']                       = $valor_IR;
                            $RETORNO['base_calculo_irrf']                = $base_calculo_irrf;
                            $RETORNO['recolhimento_irrf']                = $recolhimento_irrf;
                            
                            return $RETORNO;
                    }
                }
        
       
}

/**
 * @param type $dias                Quantidade de dias para calculo proporcional
 * @param type $tipo_insalubridade  1- 20%, 2 - 40%, vem da tabela 'curso'
 * @param type $qnt_salario_insalu  Quantidade de salários mínimo, 1 - 20%, 2 - 40%, vem da tabela 'curso'
 */

public function Calcula_insalubridade($dias, $tipo_insalubridade, $qnt_salario_insalu){
    
    $salario =  $this->RH_MOVIMENTOS['0001'][1]['fixo'];
  
    if($tipo_insalubridade == 1){
        
        $percentual = 0.20;
        $id_mov     = 56;  
        $cod        = '6006';
        
    } elseif($tipo_insalubridade == 2) {
        $percentual = 0.40;
        $id_mov     = 235;
        $cod        = '50251';
    }
    
    $RETORNO['id_mov']                = $id_mov;    
    $RETORNO['cod']                   = $cod;    
    $RETORNO['percentual']            = $percentual;    
    $RETORNO['valor_integral']        = ($salario * $qnt_salario_insalu) * $percentual;
    $RETORNO['valor_proporcional']    =  ( $RETORNO['valor_integral'] /30) * $dias;   
    return  $RETORNO;
    
}



/**ESSE Método retorna os dados do movimento como nome e tipo
 * 
 * @param type $cod Código do movimento. Campo 'cod' da tabela rh_movimentos
 */
public function get_info_movimento($cod){
    
    
    $RETORNO['id_mov']      = $this->RH_MOVIMENTOS[$cod][1]['id_mov'];
    $RETORNO['cod']         = $this->RH_MOVIMENTOS[$cod][1]['cod'];
    $RETORNO['nome']        = $this->RH_MOVIMENTOS[$cod][1]['descicao'];
    $RETORNO['categoria']   = $this->RH_MOVIMENTOS[$cod][1]['categoria'];
    
    return $RETORNO;
    
}

}

?>