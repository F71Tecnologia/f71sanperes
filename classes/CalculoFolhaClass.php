<?php

class Calculo_Folha{
   
    private $valorHora;
    public $tabelaImpostos;
    private $movInsalubridade20 = array('id_mov' => 56, 'cod_mov' => 6006);
    private $movInsalubridade40 = array('id_mov' => 235, 'cod_mov' => 50251);
    private $movPericulosidade  = array('id_mov' => 57, 'cod_mov' => 6007);
    private $movAdicionalNoturno = array('id_mov' => 66, 'cod_mov' => 9000);
    private $movDsr = array('id_mov' => 199, 'cod_mov' => 9997);
    
    
    /**
     * ESSE METODO VAI VERIFICAR EM rh_sindicato
     * SE ADICIONAL NOTURNO ESTA SIM OU NAO
     */
    public function getAdNoturnoEmSindicato($clt){
        $retorno = array();
        $query = "SELECT B.*
                    FROM rh_clt AS A
                    LEFT JOIN rhsindicato AS B ON(A.rh_sindicato = B.id_sindicato)
                    WHERE A.id_clt = '{$clt}'";
        $sql = mysql_query($query) or die("Erro ao selecionar adcional noturno no sindicato");
        if(mysql_num_rows($sql) > 0){
            while($rows = mysql_fetch_assoc($sql)){
                $retorno = array(
                    "flagAdNoturno" => $rows['adNoturno'],
                    "horas_noturna" => $rows['hr_noturna'],
                    "porcenAdNoturno" => $rows['prcentagem_add_noturno'],
                    "insalubridade"  => $rows['insalubridade'],
                    "contribuicaoAssistencial" => $rows['contribuicao_assistencial'],
                    "piso" => $rows['piso'],
                    "creche" => $rows['creche'],
                    "creche_base" => $rows['creche_base'],
                    "creche_percentual" => $rows['creche_percentual'],
                    "creche_idade" => $rows['creche_idade'],
                    "mes_dissidio" => $rows['mes_dissidio'],
                );
            }             
        }
        
        return $retorno;
    }
    
    /**Método para carregar as tabelas de  impostos(INSS,IRRF,FGTS e etc)
     * É obrigatório chamar esse método no caso de calculo de impostos
     * 
     * @param type $anobase Declara ano para base de cálculo
     */
    public function CarregaTabelas($anobase){        
       $qr_impostos = mysql_query("SELECT id_mov,cod, descicao,categoria, faixa, v_ini, v_fim, percentual, fixo, piso, teto, anobase
                                    FROM rh_movimentos 
                                    WHERE cod IN(5020,5021,5022,5023,5024,5049, 50241,0001) AND anobase = '{$anobase}'") or die(mysql_error()); //$anobase
       
       while($row_mov = mysql_fetch_assoc($qr_impostos)){           
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['id_mov']     = $row_mov['id_mov'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['cod']        = $row_mov['cod'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['descicao']   = $row_mov['descicao'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['categoria']   = $row_mov['categoria'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['v_ini']      = $row_mov['v_ini'];   
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['v_fim']      = $row_mov['v_fim'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['percentual'] = $row_mov['percentual'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['fixo']       = $row_mov['fixo'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['piso']       = $row_mov['piso'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['teto']       = $row_mov['teto'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['anobase']    = $row_mov['anobase'];
       }
       $this->tabelaImpostos = $tabelaImpostos;
    }
    
    
    
    /**
     * 
     * @param type $baseCalc
     * @param type $tipoContratacao
     * @param type $descInssOutraEmpresa
     * @param type $salarioOutraEmpresa
     * @param type $valorDescInssOutraEmpresa
     */
    public function getCalcInss($baseCalc, $tipoContratacao, $descInssOutraEmpresa = 0,$tipoDescOutraEmpresa = NULL, $salarioOutraEmpresa = 0,$valorDescInssOutraEmpresa = 0){  
	
        switch($tipoContratacao){            
              case 1: $cod = 50241;
                  break;
              case 2: $cod = 5020;
                  break;
              case 3: $cod = '5024';
                  break;
              case 4: $cod = '';
                  break;
          }
           
        foreach($this->tabelaImpostos['5020'] as $chave => $linha){            
          if($linha['v_ini'] <= $baseCalc and $linha['v_fim'] >= $baseCalc){
              $percentual = $linha['percentual'];
              $teto       = $linha['teto'];
              $inss       = $baseCalc * $percentual;
              $inss       = ($inss > $teto) ? $teto : $inss;
          }
        }
       //DESCONTO EM OUTRA EMPRESA
        if($descInssOutraEmpresa == 1) {
            if( $tipoDescOutraEmpresa == 'isento') { 
                        $inss   = 0;
               } elseif( $tipoDescOutraEmpresa == 'parcial') {
                        if(($valorDescInssOutraEmpresa + $inss)  > $teto){ 
                                    $inss =  $teto  - $valorDescInssOutraEmpresa;
                        }
            }
        }  

        $resultado['valor_inss'] = number_format($inss,2,'.','');
        $resultado['percentual'] = $percentual;
        $resultado['teto'] = number_format($teto,2,'.','');
        $resultado['desconto_inss'] = $descInssOutraEmpresa;
        $resultado['tipo_desconto_outra_empresa'] = $tipoDescOutraEmpresa;
        $resultado['salario_outra_empresa']        = number_format($salarioOutraEmpresa,2,'.','');
        $resultado['valor_desconto_outra_empresa'] = number_format($valorDescInssOutraEmpresa,2,'.','');
        return $resultado;
    }
    
    
    
 
   /**
    * @param type $id_clt
    * @param type $contratacao
    * @param type $tipoDep - Tipo de dependentes: 1 - IRRF, 2 - SAL FAMILIA
    * @return int
    */
    public function getDependentes($id_clt, $contratacao,$tipoDep){
      
           switch($tipoDep){
               case 1: $idade = 21;
                   break;
               case 2: $idade = 14;
                   break;
           }    
          $qr_menor21 = mysql_query("SELECT  
                                    IF(data1 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho1,
                                    IF(data2 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho2,
                                    IF(data3 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho3,
                                    IF(data4 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho4,
                                    IF(data5 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho5,
                                    IF(data6 > DATE_SUB(CURDATE(), INTERVAL {$idade} YEAR), 1,0) as filho6,
                                     ddir_pai, ddir_mae, ddir_conjuge, portador_def1, portador_def2, portador_def3, portador_def4,  portador_def5,  portador_def6,
                                     ddir_avo_h, ddir_avo_m, ddir_bisavo_h, ddir_bisavo_m 
                                    FROM dependentes 
                                    WHERE id_bolsista = '$id_clt'  AND contratacao = $contratacao") or die(mysql_error()); 
            
            if(mysql_num_rows($qr_menor21) != 0){
                
           $row_menor = mysql_fetch_assoc($qr_menor21); 
           $total_dependentes = 0;
           if($row_menor['filho1'] == 1 or $row_menor['portador_def1'] == 1 ){ $total_dependentes++; }           
           if($row_menor['filho2'] == 1 or $row_menor['portador_def2'] == 1){ $total_dependentes++; }
           if($row_menor['filho3'] == 1 or $row_menor['portador_def3'] == 1){ $total_dependentes++; }
           if($row_menor['filho4'] == 1 or $row_menor['portador_def4'] == 1){ $total_dependentes++; }
           if($row_menor['filho5'] == 1 or $row_menor['portador_def5'] == 1){ $total_dependentes++; }
           if($row_menor['filho6'] == 1 or $row_menor['portador_def6'] == 1){ $total_dependentes++; }  	
           
           if($tipoDep == 1) {
                if($row_menor['ddir_pai'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_mae'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_conjuge'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_avo_h'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_avo_m'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_bisavo_h'] == 1 ){ $total_dependentes++; }  			
                if($row_menor['ddir_bisavo_m'] == 1 ){ $total_dependentes++; }  			
           }
           return $total_dependentes;
	}      
    }



    /**
     * CALCULA IRRF
     * @param type $base  VALOR BASE - DESCONTO DE INSS
     * @param type $id_clt 
     * @param type $contratacao  1 - AUTONOMO , 2 - CLT, 3 - COOPERADO, 4 - AUTONOMO/PJ
     * 
     */  
    function getCalcIrrf($base,$id_clt,$contratacao) {

                  ///VERIFICAÇÂO DE DEPENDENTES   
                  $qnt_dependente_irrf          = $this->getDependentes($id_clt, $contratacao, 1);

                   if(!empty($qnt_dependente_irrf)) {
                           $valor_deducao_dep_ir_fixo   =  $this->tabelaImpostos[5049][1]['fixo'];
                           $valor_deducao_dep_ir_total  =  $qnt_dependente_irrf * $valor_deducao_dep_ir_fixo;                 
                           $base                        -= $valor_deducao_dep_ir_total;
                   } else {
                           $valor_deducao_dep_ir_total = 0;
                           $valor_deducao_dep_ir_fixo  = 0;
                           $qnt_dependente_irrf        = 0;
                   }      
                    foreach($this->tabelaImpostos[5021] as $valor){

                        if($valor['v_ini'] <= $base and $valor['v_fim'] >= $base){

                                $percentual                   = $valor['percentual'];
                                $valor_parcela_deducao_irrf   = $valor['fixo'];
                                $valor_IR                     = ($base *   $percentual) - $valor_parcela_deducao_irrf;

                               if($contratacao == 2) {
                                        $result_recolhimentoIR = mysql_query("SELECT recolhimento_ir FROM rh_clt WHERE id_clt = '$id_clt'");
                                        $row_recolhimentoIR    = mysql_fetch_assoc($result_recolhimentoIR);
                                        $recolhimento          = $row_recolhimentoIR['recolhimento_ir'];

                                        // Se o recolhimento não estiver vazio, soma o valor do IR mais o recolhimento
                                        if(!empty($recolhimento)) {  $valor_IR = $valor_IR + $recolhimento;  }

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
                        }
                    }

            $RETORNO['percentual_irrf']                 = $percentual;
            $RETORNO['valor_parcela_deducao_irrf']      = $valor_parcela_deducao_irrf;
            $RETORNO['qnt_dependente_irrf']             = $qnt_dependente_irrf;
            $RETORNO['valor_deducao_dep_ir_fixo']        = $valor_deducao_dep_ir_fixo;
            $RETORNO['valor_deducao_dep_ir_total']       = $valor_deducao_dep_ir_total;
            $RETORNO['valor_irrf']                       = number_format($valor_IR,2,'.','');
            $RETORNO['base_calculo_irrf']                = number_format($base,2,'.','');
            $RETORNO['recolhimento_irrf']                = $recolhimento_irrf;
            return $RETORNO;
    }


    /**
     * Verifica a quantidade de dias trabalhados e o mes de entrada do clt
     * 
     * @param type $dt_inicioFolha
     * @param type $dt_terminoFolha
     * @param type $dt_admissao
     * @param type $diasEvento
     * @param type $diasFerias
     * @param type $dias_trabalhandos_ferias
     * @param type $total_dias_folha
     * @return int
     */
    public function getDiasTrabalhadosFolha($dt_inicioFolha, $dt_terminoFolha,$dt_admissao, $diasEvento,$diasFerias, $dias_trabalhandos_ferias,$total_dias_folha, $total_dias_em_evento, $qntDiasMes = 30){
        
        if ($dt_admissao >= $dt_inicioFolha and $dt_admissao <= $dt_terminoFolha) {

            $inicio = explode('-', $dt_admissao);
            $dia_inicio = ($inicio[1] == '02' and ($inicio[2] == 28 or $inicio[2] == 29)) ? $qntDiasMes : $inicio[2];

            $dias_entrada = ($qntDiasMes - $dia_inicio) + 1;
            $dias         = $dias_entrada - $diasEvento - $diasFerias;
            $novo_clt = 1;
            
            $ultimo_dia_mes   = cal_days_in_month(CAL_GREGORIAN, $inicio[1], $inicio[0]);
            if($ultimo_dia_mes == 31  AND $inicio[2] != '01'){
                $dias++;
            }
            
        } else {

            if ($dias_trabalhandos_ferias == 0) {
                $dias = $total_dias_folha - $diasEvento - $diasFerias;
            } else {
                $dias = $dias_trabalhandos_ferias - $diasEvento;
            }
        }
        
//        echo $dias;
        
        $dias = ($dias < 0) ? 0: $dias;  
        if($total_dias_em_evento >= $qntDiasMes){
            $resultado['dias'] = 0;
        }else{
            $resultado['dias'] = $dias;
        }
        
        $resultado['novo_clt'] = $novo_clt;
        $resultado['dias_entrada'] = $dias_entrada;
        return $resultado;
    }

    /**
     * CALCULO DE HORA EXTRA
     */
    public function getHoraExtra($id_clt, $id_hora_extra, $qnt, $tipoQnt, $info = false) {
        
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);
        
        $sqlPorcentagem = "SELECT * FROM rh_movimentos WHERE id_mov = $id_hora_extra";
        $queryPorcentagem = mysql_query($sqlPorcentagem);
        $arrPorcentagem = mysql_fetch_assoc($queryPorcentagem);
        
        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }
        
        //INSALUBRIDADE
        $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        
        //BASE PARA CALCULO DO VALOR DA HORA
        $salarioBase = $arrClt['salario'] + $periculosidade['valor_integral'] + $insalubridade['valor_integral'];
        
        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($salarioBase, $arrClt['horas_mes']);
        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($salarioBase);
            $valorBase = $valorBase['valor_diario'];
        }
        
        //CALCULO DO ACRESCIMO
        $acrescimo = $valorBase * $arrPorcentagem['percentual'];
        
        //VALOR FINAL
        $valorFinal = ($valorBase + $acrescimo) * $qnt;
        
        $valor = $valorFinal;
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt = (empty($qnt)) ? "0.00" : $qnt;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt</strong><br/>";
            $valor .= "<strong>Percentual (F): {$arrPorcentagem['percentual']}</strong><br/><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (G):</strong><br/>";
            $valor .= "<pre>$salarioBase (G) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (H):</strong><br/>";
                $valor .= "<pre>$valorBase (H) = {$salarioBase} (G) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (H):</strong><br/>";
                $valor .= "<pre>$valorBase (H) = ({$salarioBase} (G) / 30) * $qnt (D)</pre>";
            }
            $valor .= "<strong>Valor Total da Hora Extra (I):</strong><br/>";
            $valor .= "<pre>$valorFinal (I) = ($valorBase (H) + ($valorBase (H) * {$arrPorcentagem['percentual']} (F))) * $qnt (E)</pre>";
    
        }
        
        return $valor;
        
    }

    /**
     * 
     * @param type $salario
     * @param type $horas
     * @return type
     */
    public function getValorHora($salario, $horas) {
        $this->valorHora = $salario / $horas;
        $valor_hora = $this->valorHora;
        return $valor_hora;
    }

    /**
     * 
     * @param type $salario
     * @param type $dias
     * @return type
     */
    public function getValorByDias($salario, $dias = 30) {

        //VARIAVEIS GLOBAIS
        $dados = array();
        $salario_receber = ($salario / 30) * $dias;
        $valor_diario = $salario_receber / $dias;

        //TRATAMENTO
        $valorByDias = number_format($salario_receber, "2", ".", "");
        $valor_dia = number_format($valor_diario, "2", ".", "");

        //MONTANDO ARRAY
        $dados = array("salario" => $salario_receber, "valor_diario" => $valor_diario);

        return $dados;
    }

    /**
     * ADCIONAL NOTURNO
     * @param type $baseCalc  Salário + INsalubridade ou Periculosidade
     * @param type $hora_mensal
     * @param type $adic_noturno
     * @return type
     */
    public function getAdicionalNoturno($baseCalc, $hora_mensal, $hora_noturna, $diasTrab = 30, $curso) {
        
        /**
            2525 - ENFERMEIRO
            2524 - TÉCNICO DE ENFERMAGEM
            2510 - AUXILIAR DE FARMÁCIA
            2509 - AUXILIAR ADMINISTRATIVO
            2521 - AUXILIAR DE COZINHA
            2522 - AUXILIAR DE SERVIÇOS GERAIS
            2520 - AUXILAR DE LAVANDERIA
            2519 - FISCAL DE ACESSO
        **/
        
        
        
        $valor_hora = $this->getValorHora($baseCalc, $hora_mensal);
        $percentual = 0.20;
        
        if(isset($curso) && !empty($curso)){
            $curso35Porcento = array(2525,2524,2510,2509,2521,2522,2520,2519);
            if(in_array($curso, $curso35Porcento)){
                $percentual = 0.35;
            }    
        }
        
//        if($_COOKIE['logado'] == 179){
//            print_r($valor_hora . ' * ' . $hora_noturna . ' * ' . $percentual);
//            //exit();
//        }
        
        $valor_adicional = ($valor_hora * $hora_noturna) * $percentual;
        $valor_adicional = number_format($valor_adicional,2,'.','');         
        $valor_adicionalProporcional = ($valor_adicional/30) * $diasTrab;
        
        
        $resultado['id_mov']  = $this->movAdicionalNoturno['id_mov'];
        $resultado['cod_mov'] = $this->movAdicionalNoturno['cod_mov'];
        $resultado['valor_integral'] = number_format($valor_adicional,2,'.','');
        $resultado['valor_proporcional'] = number_format($valor_adicionalProporcional,2,'.','');
        $resultado['percent'] = $percentual;
        $resultado['valor_hora_adnoturno'] = $valor_hora;
        $resultado['hora_noturna_adnoturno'] = $hora_noturna;
        
        return $resultado;
    }

    /**
     * DSR(DESCANSO SEMANAL REMUNERADO)
     * @param type $baseCalc   A base da cálciulo é o valor do adicional noturno
     * @return type
     */
    public function getDsr($baseCalc, $diasTrab = 30){      
        
        $diasDsr = 5;
        $valor_dsr = ($baseCalc/30) * $diasDsr;        
        $valorProporcional = ($valor_dsr/30) * $diasTrab;
        
        $resultado['id_mov']  = $this->movDsr['id_mov'];
        $resultado['cod_mov'] = $this->movDsr['cod_mov'];
        $resultado['valor_integral'] = number_format($valor_dsr,2,'.','') ;
        $resultado['valor_proporcional'] = number_format($valorProporcional,2,'.','') ;
        $resultado['diasDsr'] = $diasDsr;
        
        return  $resultado;
    }
    
    
    
    /**
     * MÉTODO QUE RETORNA VALOR A RECEBER DE PERICULOSIDADE
     * @param type $salario
     * @return type
     */
    public function getPericulosidade($salario, $dias = 30, $meses = null, $pericProp) {
        
        if ($_COOKIE['logado'] == 299) {
            echo 'classe';
            echo '<pre>';
            print_r($pericProp);
            echo '</pre>';
            //exit();
            
        }

        if ($pericProp) {
            $id_mov     = 622;
            $cod_mov    = 80259;
            
        } else {
            $id_mov     = 57;
            $cod_mov    = 6007;
        }
        echo 'PEriculosidade proporcional';
        $valor_integral     = ($salario * 0.30);
        $valor_integral     = number_format($valor_integral, "2", ".", "");
        
        $valor_proporcional = (($salario * 0.30)/30) * $dias;
        $valor_proporcional = number_format($valor_proporcional, "2", ".", "");       
        
        $resultado['id_mov']     = $id_mov;
        $resultado['cod_mov']    = $cod_mov;
        $resultado['valor_integral']     = number_format($valor_integral, 2, '.', '');
        $resultado['valor_proporcional'] = $valor_proporcional;
        $resultado['valor_13_integral'] = number_format(($valor_proporcional / 12) * $meses, 2, '.', '');
        
        return $resultado;
    }

    
    
     
 
/**
 * @param type $dias                Quantidade de dias para calculo proporcional
 * @param type $tipo_insalubridade  1- 20%, 2 - 40%, vem da tabela 'curso'
 * @param type $qnt_salario_insalu  Quantidade de salários mínimo, 1 - 20%, 2 - 40%, vem da tabela 'curso'
 */

public function getInsalubridade($dias, $tipo_insalubridade, $qnt_salario_insalu, $ano, $meses, $insalSobreSalBase = 0,$salarioBase,$proporcional = 0){
    
    
    //CONFIGURAÇÃO PARA INSALUBRIDADE INTEGRAL OU PROPORCIONAL///
    //1 => Primeira Parcela
    //2 => Segunda Parcela
    //3 => Integral
    $tipo_terceiro = 2;
    
    $this->CarregaTabelas($ano);
    
    if($tipo_insalubridade == 1){   
        if (!$proporcional) {
            $percentual = 0.20;
            $id_mov     = 56;  
            $cod        = '6006'; 
        } else {
            $percentual = 0.20;
            $id_mov     = 620;  
            $cod        = '80257'; 
        }
               
    } else if($tipo_insalubridade == 2) {
        if (!$proporcional) {
            $percentual = 0.40;
            $id_mov     = 235;
            $cod        = '50251';
        } else {
            $percentual = 0.40;
            $id_mov     = 621;
            $cod        = '80258';
        }
    }
    
    if($insalSobreSalBase){
        $salario =  $salarioBase;
    }else{
        $salario =  $this->tabelaImpostos['0001'][1]['fixo'];
    }
    
    
//    if($_COOKIE['logado'] == 179){
//        echo "<pre>";
//            print_r($salario);
//        echo "</pre>";
//    }
    
    
    $RETORNO['id_mov']                = $id_mov;    
    $RETORNO['cod_mov']               = $cod;    
    $RETORNO['percentual']            = $percentual;    
    $RETORNO['salario_minimo']        = $salario;    
    $RETORNO['valor_integral']        = number_format(($salario * $qnt_salario_insalu) * $percentual, 2, '.', ''); 
    $RETORNO['valor_proporcional']    = ( $RETORNO['valor_integral'] /30) * $dias; 
               
    if($tipo_terceiro == 2 || $tipo_terceiro == 3){
        $RETORNO['valor_13_integral']     = number_format(($RETORNO['valor_integral'] / 12) * $meses , 2, '.', ''); 
    }else{
        $RETORNO['valor_13_integral']     = ($meses == 0) ? 0 : number_format(($RETORNO['valor_integral'] / 12) * $meses, 2, '.', ''); 
    }
    
    
//    if($_COOKIE['logado'] == 179){
//         print_r($RETORNO);
//    }
//    
    
    return  $RETORNO;    
}

    
    
    /**
     * MÉTODO PARA CONTRIBUIÇÃO SINDICAL
     */
    public function getContribuicaoSindical($salario){
        $diaria = $this->getValorByDias($salario, 30);
        return $diaria;
    }
    
    /**
     * MÉTODO MUITO IMPORTANTE, O MESMO RETORNA A DIFERENÇA DE DIAS SOBRE UM INTERVALO DE DATAS,
     * O ULTIMO DIA DO MÊS PASSADO NO PARÂMETRO, ENTRE OUTROS DADOS QUE PODE SER IMPORTANTE PARA
     * FOLHA DE PAGAMENTO -- FUNCIONARIO --
     * @param type $data
     * @return type
     */
    public function getIntervaloDatas($data){
        //DIA REFERENTE
        $dia = date("d", strtotime($data));   
        //MES REFERENTE
        $mes = date("m", strtotime($data));   
        //ANO REFERENTE
        $ano =  date("Y", strtotime($data));   
        //ULTIMO DIA NO MÊS REFERENTE
        $ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
        //DIFERENÇA DE DIAS
        $diff_dias = $ultimo_dia - $dia; 
        //DADOS
        $dados = array(
            "data" => $data,
            "dia_inicial"  => $dia, 
            "dia_final" => $ultimo_dia,
            "mes"  => $mes,
            "ano"  => $ano,
            "diff" => $diff_dias,
        );
        
        return $dados;
        
    }
    
    /**
     * MÉTODO PARA RETORNAR A QUANTIDADE DE DIAS TRABALHADOS, CALCULANDO PELA DATA DE ENTRADA NA FOLHA
     * E RETORNA TAMBÉM O VALOR A SER PAGO AO  -- FUNCIONÁRIO --
     * @param type $salario
     * @param type $data_entrada
     * @return type
     */
    public function getDiasTrabalhado($salario, $data_entrada){
        $data = $this->getIntervaloDatas($data_entrada);
        //DATA INICIO
        $iniciado_em = date("d/m/Y", str_replace("/","-",strtotime($data['data']))); 
        //DATA FINAL
        $finalizado_em  = $data['ultimo_dia'] . "/" . $data['mes'] . "/" . $data['ano']; 
        //VALOR PAGO PELA DIÁRIA
        $valor_pago = $this->getValorByDias($salario, $data['diff']);
        //DADOS
        $dados = array(
            "iniciado_em" => $iniciado_em, 
            "finalizado_em" => $finalizado_em, 
            "dias" => $data['diff'], 
            "valor_pago" => number_format($valor_pago['salario'], "2", ".", "")
        );
        
        return $dados;
    }
        
    /**
     * MÉTODO QUE RETORNA A QUANTIDADE DE DIAS PARA GERAR -- FOLHA -- 
     * @param type $data_inicio
     * @return string
     */
    public function getPeriodoFolha($data_inicio, $fotmato_data = "pt"){
        $data = $this->getIntervaloDatas($data_inicio);
        //DATA INICIAL
        $data_ref_inicial_pt = $data['dia_inicial'] . "/" . $data['mes'] . "/" . $data['ano'];
        $data_ref_inicial = $data['ano'] . "-" . $data['mes'] . "-" . $data['dia_inicial'];
        //DATA FINAL
        $data_ref_final_pt = $data['dia_final'] . "/" . $data['mes'] . "/" . $data['ano'];
        $data_ref_final = $data['ano'] . "-" . $data['mes'] . "-" . $data['dia_final'];
        //QUANTIDADE DE DIAS
        $diff_dias = $data['diff'];
        //DADOS
        $dados = array(
            "iniado_em_pt" => $data_ref_inicial_pt, 
            "iniado_em" => $data_ref_inicial, 
            "finalizado_em_pt" => $data_ref_final_pt, 
            "finalizado_em" => $data_ref_final, 
            "dias" => $diff_dias
        );
        
        return $dados;
    }
    
    
    
     /**
     * Calcula as médias de movimentos
     */
    public function getMediaMovimentos($id_clt,$mes, $ano,  $qnt_meses, $folhaDecimoTerceiro = 0,  $parcelaDecimo = 0, $calculaResidoMedia = 0){        
          $dtReferencia = $ano.'-'.$mes.'-01';
          
          $idsMovimentoDecimo  = ($folhaDecimoTerceiro == 1)? '  AND A.id_mov NOT IN(200,56,57,235) AND B.id_mov NOT IN(55) AND A.status IN(1,5)' : ' AND A.id_mov NOT IN(200)'; //Movimentos que não entrampara calculo de médias de décimo terceiro, pois os mesmos são calculados de forma integral
          
         if($folhaDecimoTerceiro == 1){
                $qr_folha = "SELECT A.* FROM rh_folha as A 
                    INNER JOIN rh_folha_proc as B
                    ON A.id_folha  = B.id_folha
                    WHERE A.ano  = {$ano} AND B.id_clt  = {$id_clt} AND A.status = 3 AND B.status = 3 AND A.terceiro !=1;";
                    //$qnt_meses =  mysql_num_rows(mysql_query($qr_folha));    
                    
         } else {
                $qr_folha = "SELECT  A.ids_movimentos_estatisticas, B.id_clt,A.mes, A.ano
                    FROM rh_folha as A
                    LEFT JOIN rh_folha_proc as B ON(A.id_folha = B.id_folha)
                    WHERE B.id_clt = '{$id_clt}' AND A.status = 3 AND A.terceiro = 2 
                    AND A.data_inicio >= DATE_SUB('{$dtReferencia}', INTERVAL '{$qnt_meses}' MONTH) ORDER BY A.ano,A.mes";
        }

        
        $sql_folha = mysql_query($qr_folha) or die("Erro de seleção de movimentos");
        $resultado = array();
          
        while ($row_folha = mysql_fetch_assoc($sql_folha)) {  
            
            if (!empty($row_folha[ids_movimentos_estatisticas])) {
//                        if($_COOKIE['logado'] == 260){
//                            echo "<pre>";
//                                print_r($row_folha);
//                            echo "</pre>";
//                            echo "SELECT *
//                                            FROM rh_movimentos_clt AS A
//                                            LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)
//                                            WHERE A.id_movimento IN($row_folha[ids_movimentos_estatisticas])
//                                            AND A.tipo_movimento = 'CREDITO' AND A.id_clt = {$id_clt} AND B.media_13 = 1 $idsMovimentoDecimo;";
//                        }
                
//                echo "SELECT *
//                        FROM rh_movimentos_clt AS A
//                        LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)
//                        WHERE A.id_movimento IN($row_folha[ids_movimentos_estatisticas])
//                        AND A.tipo_movimento = 'CREDITO' AND A.id_clt = {$id_clt} AND B.media_13 = 1 $idsMovimentoDecimo";
                
                $qr_movimentos = mysql_query("SELECT *
                                            FROM rh_movimentos_clt AS A
                                            LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)
                                            WHERE A.id_movimento IN($row_folha[ids_movimentos_estatisticas])
                                            AND A.tipo_movimento = 'CREDITO' AND A.id_clt = {$id_clt} AND B.media_13 = 1 $idsMovimentoDecimo");
                                           
//                if($_COOKIE['logado'] == 179){
//                    echo "<pre>";
//                        print_r("SELECT *
//                                        FROM rh_movimentos_clt AS A
//                                        LEFT JOIN rh_movimentos AS B ON(A.cod_movimento = B.cod)
//                                        WHERE A.id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND A.incidencia = '5020,5021,5023'  
//                                        AND A.tipo_movimento = 'CREDITO' AND A.id_clt = {$id_clt} AND B.media_13 = 1 $idsMovimentoDecimo");
//                    echo "</pre>";
//                }
                                            
                                       
                                            
                while ($mov = mysql_fetch_assoc($qr_movimentos)) { 
                    if($_COOKIE['logado'] == 179){
                        echo "<pre>";
                            echo " <br>**************MEDIAS 13*****************<br> ";
                            print_r($mov);
                            echo "<br> ********************************************** <br>";
                        echo "</pre>";
                    }
                    $ano_folha    = $row_folha['ano'];
                    $mes_folha    = $row_folha['mes'];
                    $id_movimento = $mov['id_movimento'];
                    $cod_mov      = $mov['cod_movimento'];
                    
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['nome']       = $mov['nome_movimento'];
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['id_mov']     = $mov['id_mov'];
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['mes']        = $mes_folha;
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['ano']        = $ano_folha;
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['codigo']     = $mov['cod_movimento'];
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['categoria']  = $mov['tipo_movimento'];
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['lancamento'] = $mov['lancamento'];
                    $resultado['movimentos'][$ano_folha][$mes_folha][$id_movimento]['valor']      = $mov['valor_movimento'];   
                      
                    
                    $resultado['total_somatorio'][$cod_mov]['nome']          = $mov['nome_movimento']; 
                    $resultado['total_somatorio'][$cod_mov]['id_mov']        = $mov['id_mov'];
                    $resultado['total_somatorio'][$cod_mov]['cod_mov']       = $mov['cod_movimento'];
                    $resultado['total_somatorio'][$cod_mov]['fator_media']   = $qnt_meses; 
                    $resultado['total_somatorio'][$cod_mov]['valor']        += $mov['valor_movimento'];                   
                }
            }
        }
        
        if($_COOKIE['logado'] == 179){
            echo "<pre>";
            echo "<br>Total Somatorio<br>";
                print_r($resultado);
            echo "</pre>";
        }
        
        //echo $qnt_meses; 
        //Calculando média por movimento      
        if(sizeof($resultado['total_somatorio']) > 0){
            
            foreach($resultado['total_somatorio'] as $codigo => $mov){  
                $valor_media  = ($mov['valor']/$qnt_meses);              
                $resultado['total_somatorio'][$codigo]['valor_media'] = number_format($valor_media,2,'.','');
                
                $total_media = $valor_media;    
//                if($_COOKIE['logado'] == 179){
//                    echo "<pre>";
//                        print_r($mov);
//                    echo "</pre>";
//                }
                
                if($folhaDecimoTerceiro == 1){
                  $resultado['total_somatorio'][$codigo]['valor_decimo'] = $total_media;                    
                  $total_media_13 += $this->getValorDecimoTerceiro($valor_media, $qnt_meses);       
                }
           
            }  
        }      
        $resultado['total_movimentos'] = number_format($total_movimento,2,'.','');
        $resultado['total_media']      = number_format($total_media,2,'.','');   
        $resultado['total_media_13']      = number_format($total_media_13,2,'.','');   
        
        
        if($calculaResidoMedia == 1){
            
            //QUERY DE MOVIMENTOS DE MÉDIA LANÇADO PARA 13° NO ANO ANTERIOR
            $qr_recupera_media = "SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$id_clt}' AND 
            A.mes_mov = '12' AND A.cod_movimento NOT IN(6006,6007,9996,50251,80030) AND A.ano_mov = '{$ano}' AND A.tipo_movimento = 'CREDITO' AND A.`status` = 5";
               
            $sql_recupera_media = mysql_query($qr_recupera_media) or die("Erro ao selecionar medias de 13°");
            $valor_medias = 0;
            while($rows_medias = mysql_fetch_assoc($sql_recupera_media)){
                $valor_medias += $rows_medias['valor_movimento']; 
            }
            
            $valor_medias = $valor_medias / 12;
            if($valor_medias < 0){
                $valor_medias = 0;
            }

            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    print_r("Média: " . $valor_medias);
//                    print_r("<br>");
//                echo "</pre>";
            }
        }
        
        $resultado['residuo_media'] = number_format($valor_medias,2,'.','');   
        
        
        return $resultado;
    }
    
    /**
     * 
     * @param type $id_folha
     * @return type
     * @throws Exception
     */
    public function getMediaPorClt($id_folha){
        
        $query = "SELECT id_clt,mes,ano FROM rh_folha_proc WHERE id_folha = '{$id_folha}'";
        
        try{
            $sql = mysql_query($query);
            if(mysql_num_rows($sql) > 0){ 
                while($rows_participantes  = mysql_fetch_assoc($sql)){
                    $this->mediaMovimentos($rows_participantes['id_clt'],$rows_participantes['ano']);
                }
            }else{
                throw new Exception("Nenhum participantes encontrado");
            }
        } catch (Exception $e){
            echo $e->getMessage("Bloco não foi execultado");
        }        
        
    }
    
    /**
     * 
     * @param type $clt
     * @param type $ano
     * @param type $tipo 1 => ferias, 2= rescisão, 3 = 13°
     * @return int
     */
    public function mediaMovimentos($clt, $ano, $tipo){
        
        //MOVIMENTOS QUE NÃO ENTRÃO NA MÉDIA DE 13°
        $movimentos = array(6006,50251,9996,6007);
        $criteria = "";
        
        try{
            
            $query_total_mov = "SELECT cod_movimento,nome_movimento,valor_movimento,id_clt,mes_mov,ano_mov,tipo_movimento FROM rh_movimentos_clt WHERE id_clt = '{$clt}' AND ano_mov = '{$ano}' AND status = 5";
            $sql_total = mysql_query($query_total_mov);
            $soma_movimentos = array();
            $medias_movimentos = array();
            
            
            while($row_total = mysql_fetch_assoc($sql_total)){
                if(!in_array($row_total['cod_movimento'], $movimentos) && $row_total['tipo_movimento'] == "CREDITO"  && !empty($row_total['tipo_movimento']) && $row_total['tipo_movimento'] != ",,"){
                    $soma_movimentos[$ano][$row_total['id_clt']][$row_total['cod_movimento']]["nome"] = $row_total['nome_movimento'];
                    $soma_movimentos[$ano][$row_total['id_clt']][$row_total['cod_movimento']]["valor"] += $row_total['valor_movimento'];
                }
            }
            
            $total_meses_trabalhado = $this->getQntMesesByAno($clt, $ano);
            foreach ($soma_movimentos[$ano] as $clt => $movimento){
                foreach ($movimento as $key => $dados){
                    $medias_movimentos[$clt][$key] = number_format((($dados["valor"] / $total_meses_trabalhado) / 12) * $total_meses_trabalhado, 2, ",",".");
                }
            }
            
        }catch(Exception $e){
            echo $e->getMessage("Erro ao executar bloco");
        }
        
        return $movimentos;
    }
    
    /**
     * 
     * @param type $clt
     * @param type $ano
     * @return type
     */
    public function getQntMesesByAno($clt, $ano){
        $total = 0;
        $query = "SELECT COUNT(A.id_clt) as total FROM rh_folha_proc AS A WHERE A.id_clt = '{$clt}' AND A.ano = '{$ano}' AND A.status = 3 GROUP BY A.id_clt";
        try{
            $sql = mysql_query($query);
            if(mysql_num_rows($sql) > 0){
                $result = mysql_fetch_assoc($sql);
                $total = $result['total'];
            }
        }  catch (Exception $e){
            echo $e->getMessage();
        }
        
        return $total;
        
    }
    
    /**
     * 
     * @param type $valor
     * @param type $qntMeses
     */
    public  function getValorDecimoTerceiro($valor, $qntMeses,$tipo_terceiro = null){    
        $decimo = ($valor/12)*$qntMeses;
        return number_format($decimo,2,'.','');
    }
    
    
    /**
     * 
     * @param type $inicioFolha
     * @param type $finalFolha
     * @param type $dataAdmissao
     */
    public function getDiasTrabalhados($inicioFolha, $finalFolha, $dataAdmissao){
        
        $dias_entrada = 30;
        if ($dataAdmissao >= $inicioFolha && $dataAdmissao <= $finalFolha) {
            
            $inicio = explode('-', $dataAdmissao);
            $dia_inicio = ($inicio[1] == '02' and ($inicio[2] == 28 or $inicio[2] == 29)) ? 30 : $inicio[2];

            $dias_entrada = (30 - $dia_inicio) + 1;
        }
        
        return $dias_entrada;
        
    }
    
    /**
     * @author Lucas Praxedes (05/06/2017)
     * MÉTODO CRIADO PARA CALCULAR O ADICIONAL NOTURNO NA TELA DE LANÇAMENTO DE
     * MOVIMENTOS;
     */
    public function getMovAdNoturno($id_clt, $id_ad_noturno, $qnt_noturna, $tipoQnt, $info = false) {
        
        
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);
        
        $sqlPorcentagem = "SELECT * FROM rh_movimentos WHERE id_mov = $id_ad_noturno";
        $queryPorcentagem = mysql_query($sqlPorcentagem);
        $arrPorcentagem = mysql_fetch_assoc($queryPorcentagem);
        
        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }
        
        //INSALUBRIDADE
        $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        
        //BASE PARA CALCULO DO VALOR DA HORA
        $salarioBase = $arrClt['salario'] + $periculosidade['valor_integral'] + $insalubridade['valor_integral'];
        
        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($salarioBase, $arrClt['horas_mes']);
            
        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($salarioBase);
            $valorBase = $valorBase['valor_diario'];
        }
        
        $valor_adicional = ($valorBase * $arrPorcentagem['percentual']) * $qnt_noturna;
        
        $valor = $valor_adicional;
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt_noturna = (empty($qnt_noturna)) ? "0.00" : $qnt_noturna;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt_noturna</strong><br/>";
            $valor .= "<strong>Percentual (F): {$arrPorcentagem['percentual']}</strong><br/><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (G):</strong><br/>";
            $valor .= "<pre>$salarioBase (G) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (H):</strong><br/>";
                $valor .= "<pre>$valorBase (H) = {$salarioBase} (G) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (H):</strong><br/>";
                $valor .= "<pre>$valorBase (H) = ({$salarioBase} (G) / 30) * $qnt_noturna (E)</pre>";
            }
            $valor .= "<strong>Valor Total do Adicional Noturno (I):</strong><br/>";
            $valor .= "<pre>$valor_adicional (I) = ($valorBase (H) * {$arrPorcentagem['percentual']} (F)) * $qnt_noturna (E)</pre>";
    
        }
        
        return $valor;
    }
    
    /**
     * @author Lucas Praxedes (18/07/2017)
     * MÉTODO CRIADO PARA CALCULAR O ADICIONAL DE PRONTIDÃO NA TELA DE LANÇAMENTO DE
     * MOVIMENTOS;
     */
    public function getMovAdProntidao($id_clt, $qnt, $tipoQnt, $info = false) {
        
        
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);
        
        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }
        
        //INSALUBRIDADE
        $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        
        //BASE PARA CALCULO DO VALOR DA HORA
        $salarioBase = $arrClt['salario'] + $periculosidade['valor_integral'] + $insalubridade['valor_integral'];
        
        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($salarioBase, $arrClt['horas_mes']);
            
        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($salarioBase);
            $valorBase = $valorBase['valor_diario'];
        }
        
        $valor_adicional = ($valorBase * 2) / 3;
        
        $valot_total_adicional = $valor_adicional * $qnt;
        
        $valor = $valot_total_adicional;
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt = (empty($qnt)) ? "0.00" : $qnt;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt</strong><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (F):</strong><br/>";
            $valor .= "<pre>$salarioBase (F) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = {$salarioBase} (F) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = ({$salarioBase} (F) / 30) * $qnt (E)</pre>";
            }
            $valor .= "<strong>Valor Total do Adicional de " . utf8_encode("Prontidão") . " (H):</strong><br/>";
            $valor .= "<pre>$valot_total_adicional (H) = (($valorBase (G) * 2) / 3) * $qnt (E)</pre>";
    
        }
        
        return $valor;
    }
    
    /**
     * @author Lucas Praxedes (18/07/2017)
     * MÉTODO CRIADO PARA CALCULAR O SOBREAVISO EM HORAS NA TELA DE LANÇAMENTO DE
     * MOVIMENTOS;
     */
    public function getSobreAvisoEmHoras($id_clt, $qnt, $tipoQnt, $info = false) {
        
        
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);
        
        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }
        
        //INSALUBRIDADE
        $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        
        //BASE PARA CALCULO DO VALOR DA HORA
        $salarioBase = $arrClt['salario'] + $periculosidade['valor_integral'] + $insalubridade['valor_integral'];
        
        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($salarioBase, $arrClt['horas_mes']);
            
        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($salarioBase);
            $valorBase = $valorBase['valor_diario'];
        }
        
        $valor_sobreaviso = $valorBase / 3;
        
        $valot_total_sobreaviso = $valor_sobreaviso * $qnt;
        
        $valor = $valot_total_sobreaviso;
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt = (empty($qnt)) ? "0.00" : $qnt;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt</strong><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (F):</strong><br/>";
            $valor .= "<pre>$salarioBase (F) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = {$salarioBase} (F) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = ({$salarioBase} (F) / 30) * $qnt (E)</pre>";
            }
            $valor .= "<strong>Valor Total do Sobreaviso (H):</strong><br/>";
            $valor .= "<pre>$valot_total_sobreaviso (H) = ($valorBase (G)/ 3) * $qnt (E)</pre>";
    
        }
        
        return $valor;
    }
    
    public function getMovHoraExtraAdNoturno($id_clt, $id_hora_extra, $qnt_noturna, $tipoQnt, $info = false) {
        
        
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);
        
        $sqlPorcentagem = "SELECT * FROM rh_movimentos WHERE id_mov = $id_hora_extra";
        $queryPorcentagem = mysql_query($sqlPorcentagem);
        $arrPorcentagem = mysql_fetch_assoc($queryPorcentagem);
        
        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }
        
        //INSALUBRIDADE
        $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        
        //BASE PARA CALCULO DO VALOR DA HORA
        $salarioBase = $arrClt['salario'] + $periculosidade['valor_integral'] + $insalubridade['valor_integral'];

        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($salarioBase, $arrClt['horas_mes']);

        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($salarioBase);
            $valorBase = $valorBase['valor_diario'];
        }
        
        //VALOR DO ADICIONAL NOTURNO
        $valor_adicional = $valorBase * $arrPorcentagem['percentual2'];
        
        //VALOR DA HORA EXTRA NOTURNA   
        $valorHoraNoturna = ($valor_adicional + $valorBase) + (($valor_adicional + $valorBase) * $arrPorcentagem['percentual']);
        
        $valorFinal = $valorHoraNoturna * $qnt_noturna;
        
        $valor = $valorFinal; 
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt_noturna = (empty($qnt_noturna)) ? "0.00" : $qnt_noturna;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt_noturna</strong><br/>";
            $valor .= "<strong>Percentual da Hora Extra (F): {$arrPorcentagem['percentual']}</strong><br/><br/>";
            $valor .= "<strong>Percentual do Adicional Noturno (G): {$arrPorcentagem['percentual2']}</strong><br/><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (H):</strong><br/>";
            $valor .= "<pre>$salarioBase (H) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (I):</strong><br/>";
                $valor .= "<pre>$valorBase (I) = {$salarioBase} (H) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (I):</strong><br/>";
                $valor .= "<pre>$valorBase (I) = ({$salarioBase} (H) / 30) * $qnt (D)</pre>";
            }
            $valor .= "<strong>Valor do Adicional Noturno (J):</strong><br/>";
            $valor .= "<pre>$valor_adicional (J) = $valorBase (I) * {$arrPorcentagem['percentual2']} (F)</pre>";
            
            $valor .= "<strong>Valor da Hora Extra Noturna (K):</strong><br/>";
            $valor .= "<pre>$valorHoraNoturna (K) = ($valor_adicional (J) + $valorBase (I)) + ($valor_adicional (J) + $valorBase (I)) * {$arrPorcentagem['percentual']} (F))</pre>";
            
            $valor .= "<strong>Valor Total (L):</strong><br/>";
            $valor .= "<pre>$valorFinal (L) = $valorHoraNoturna (K) * $qnt_noturna (E)</pre>";
    
        }
        
        return $valor;
    }
    
    
    /**
     * 
     * @param type $competencia
     * @param type $terminoFolha
     * @param type $clt
     * @param type $projeto
     */
    public function getStatusAtualPorCompetecia($competencia, $terminoFolha, $clt, $projeto){
        $query = "   
                    SELECT *, 
                         if(DATE_FORMAT(inicio_ferias,'%Y-%m') = '{$competencia}',1,
                             if(DATE_FORMAT(inicio_ferias,'%Y-%m') = '{$competencia}' AND DATE_FORMAT(fim_ferias,'%Y-%m') = '{$competencia}', 1, 2)) AS mes_ferias 
                         FROM (
                             SELECT 
                                     tmp1.id_curso,
                                     tmp1.rh_horario,
                                     tmp1.id_ferias,
                                     tmp1.id_recisao,
                                     tmp1.id_evento, 
                                     tmp1.id_regiao,
                                     tmp1.id_projeto,
                                     tmp1.id_clt, 
                                     tmp1.nome,
                                     B.cod_status, 
                                     B.nome_status,
                                     B.`data`,
                                     B.data_retorno,
                                     tmp1.data_ini as inicio_ferias,
                                     tmp1.data_fim as fim_ferias,
                                     tmp1.data_demi,
                                     tmp1.data_entrada,
                                     tmp1.data_ultima_atualizacao,

                                     /*VERIFICANDO SE A DATA ATUAL ESTA ENTRE ALGUM EVENTO E NAO TEM RESCISAO COM STATUS 1*/
                                     if('{$terminoFolha}' BETWEEN B.data AND B.data_retorno AND tmp1.id_recisao IS NULL,B.cod_status,
                                             /*VERIFICANDO SE O EVENTO NAO TEM DATA DE RETORNO E NAO TEM RESCISAO COM STATUS 1*/
                                             if(('{$terminoFolha}' >= B.data AND B.data_retorno = '0000-00-00') AND tmp1.id_recisao IS NULL,B.cod_status,

                                             /*VERIFANDO SE TEM RESCIS?O PARA A FOLHA*/
                                             if(tmp1.id_recisao IS NOT NULL AND DATE_FORMAT(tmp1.data_demi,'%Y-%m') = '{$competencia}',tmp1.statusRescisao,

                                             /*VERIFICANDO SE TEM FERIAS E N?O TEM RESCISAO COM STATUS 1*/
                                             if(tmp1.id_ferias IS NOT NULL AND (tmp1.id_recisao IS NOT NULL AND DATE_FORMAT(tmp1.data_demi,'%Y-%m') != '{$competencia}'),40,10)))			

                                     ) as novoStatus,

                                     /*AQUI EU VERIFICO SE O CLT VAI ENTRAR NA FOLHA, POIS ? NECESS?RIO PARTICIPAR DA MESMA OS CASOS DE RESCIS?O NO M?S DA COMPET?NCIA*/
                                     if((tmp1.id_recisao IS NOT NULL AND DATE_FORMAT(tmp1.data_demi,'%Y-%m') >= '{$competencia}'), 1, 
                                         if(tmp1.id_recisao IS NULL,1,0)) as entra_na_folha

                                     FROM (
                                             SELECT A.id_curso, A.rh_horario, A.data_entrada, C.data_demi, E.id_ferias, E.data_ini, E.data_fim, C.id_recisao, B.id_evento, A.id_clt, 
                                             D.id_regiao, D.id_projeto, D.nome as nome_projeto, A.nome, MAX(B.`data`) AS ultimoEvento,C.motivo as statusRescisao, A.status, A.data_ultima_atualizacao 
                                                 FROM rh_clt AS A 
                                                     LEFT JOIN rh_eventos AS B ON(A.id_clt = B.id_clt AND B.`status` = 1)
                                                     LEFT JOIN rh_recisao AS C ON(A.id_clt = C.id_clt AND C.`status` = 1)
                                                     LEFT JOIN projeto AS D ON(A.id_projeto = D.id_projeto)
                                                     LEFT JOIN rh_ferias AS E ON(A.id_clt = E.id_clt AND E.`status` = 1 AND (DATE_FORMAT(E.data_ini,'%Y-%m') = @competencia || DATE_FORMAT(E.data_fim,'%Y-%m') = @competencia))
                                             WHERE A.id_projeto = '{$projeto}' /*AND B.cod_status IS NOT NULL*/
                                             GROUP BY A.id_clt
                                     ) as tmp1
                                     LEFT JOIN rh_eventos AS B ON(tmp1.id_clt = B.id_clt AND tmp1.ultimoEvento = B.`data` AND B.`status` = 1 AND B.cod_status != 10 AND B.cod_status != 0)
                             ) as tmp2 WHERE id_projeto IN('{$projeto}') AND entra_na_folha = 1 AND id_clt = '{$clt}' ORDER BY nome";
            $sql = mysql_query($query);
            $status = 0;
            while($rows = mysql_fetch_assoc($sql)){
                $status = $rows['novoStatus'];
            }
            
            return $status;
    }
    
    /**
     * @author Lucas Praxedes (25/08/2017)
     * MÉTODO CRIADO PARA CALCULAR O VALOR DE FALTAS OU ATRASOS
     */
    public function getFaltasAtrasos($id_clt, $qnt, $tipoQnt, $info = false) {
        
        $base = $this->getSalarioBase($id_clt);
               
        if ($tipoQnt == 1) {
            //VALOR DA HORA
            $valorBase = $this->getValorHora($base['salario_base'], $base['horas_mes']);
            
        } else if ($tipoQnt == 2) {
            //VALOR DO DIA
            $valorBase = $this->getValorByDias($base['salario_base']);
            $valorBase = $valorBase['valor_diario'];
        }
        
        $valor = $valorBase * $qnt;
        
        if ($info) {
            
            $insalubridade['valor_integral'] = (empty($insalubridade['valor_integral'])) ? "0.00" : $insalubridade['valor_integral'];
            $periculosidade['valor_integral'] = (empty($periculosidade['valor_integral'])) ? "0.00" : $periculosidade['valor_integral'];
            $qnt_noturna = (empty($qnt_noturna)) ? "0.00" : $qnt_noturna;
                    
            $valor = "<strong> " . utf8_encode("Salário") . " (A): {$arrClt['salario']}</strong><br/>";
            $valor .= "<strong>Periculosidade (B): {$periculosidade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Insalubridade (C): {$insalubridade['valor_integral']}</strong><br/>";
            $valor .= "<strong>Horas / " . utf8_encode("Mês") . " (D): {$arrClt['horas_mes']}</strong><br/>";
            $valor .= "<strong>Quantidade (E): $qnt</strong><br/>";
            
            $valor .= "<strong> " . utf8_encode("Salário") . " Final (F):</strong><br/>";
            $valor .= "<pre>{$base['salario_base']} (F) = {$arrClt['salario']} (A) + {$periculosidade['valor_integral']} (B) + {$insalubridade['valor_integral']} (C)</pre>";
            if ($tipoQnt == 1) {
                $valor .= "<strong>Valor Hora (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = {{$base['salario_base']}} (F) / {$arrClt['horas_mes']} (D)</pre>";
            } else if ($tipoQnt == 2) {
                $valor .= "<strong>Valor Dia (G):</strong><br/>";
                $valor .= "<pre>$valorBase (G) = ({{$base['salario_base']}} (F) / 30) * $qnt (E)</pre>";
            }
            $valor .= "<strong>Valor Total da Falta/Atraso (H):</strong><br/>";
            $valor .= "<pre>$valor_adicional (H) = $valorBase (G) * $qnt (E)</pre>";
    
        }
        
        
        return $valor;
    }
    
    public function getSalarioBase($id_clt) {

        //DADOS DO CLT
        $sqlClt = " SELECT B.salario, F.horas_mes, B.tipo_insalubridade, B.qnt_salminimo_insalu, 
                    B.periculosidade_30, B.horista_plantonista, A.valor_hora, A.quantidade_horas_proporcional, 
                    A.quantidade_horas, B.funcao_professor
                    FROM rh_clt AS A
                    LEFT JOIN curso AS B ON A.id_curso = B.id_curso
                    LEFT JOIN rh_horarios AS F ON F.id_horario = A.rh_horario
                    WHERE A.id_clt = $id_clt";
        $queryClt = mysql_query($sqlClt);
        $arrClt = mysql_fetch_assoc($queryClt);

        //PERICULOSIDADE
        if ($arrClt['periculosidade_30']) {
            $periculosidade = $this->getPericulosidade($arrClt['salario'], 30, 12);
        }

        //INSALUBRIDADE
        if ($arrClt['insalubridade']) {
            $insalubridade = $this->getInsalubridade(30, $arrClt['tipo_insalubridade'], $arrClt['qnt_salminimo_insalu'], date('Y'));
        }

        $ad_transferencia = $this->getAdTransferencia($id_clt);

        $ad_tempo_servico = $this->getAdTempoServico($id_clt);

        $ad_cargo_confianca = $this->getAdCargoConfianca($id_clt);
        
        $gratificacao_funcao = $this->getGratificacaoFuncao($id_clt);
        
        $prod_perc_fixo = $this->getProdPercFixo($id_clt);
        
        if (!$arrClt['horista_plantonista']) {
            $return['salario'] = $arrClt['salario'];
        } else {
            $return['salario'] = $arrClt['valor_hora'] * $arrClt['quantidade_horas_proporcional'];
        }
        
        $salarioBase = $return['salario'] +
                $periculosidade['valor_integral'] +
                $insalubridade['valor_integral'] +
                $ad_transferencia['valor_integral'] +
                $ad_tempo_servico['valor_integral'] +
                $ad_cargo_confianca['valor_integral'] +
                $ad_unidocencia['valor_integral'] +
                $gratificacao_funcao['valor_integral'] +
                $prod_perc_fixo['valor_integral'];

        $return['horista_plantonista'] = $arrClt['horista_plantonista'];
        $return['funcao_professor'] = $arrClt['funcao_professor'];
        $return['valor_hora'] = $arrClt['valor_hora'];
        $return['periculosidade'] = $periculosidade;
        $return['insalubridade'] = $insalubridade;
        $return['ad_transferencia'] = $ad_transferencia;
        $return['ad_tempo_servico'] = $ad_tempo_servico;
        $return['ad_cargo_conafianca'] = $ad_cargo_confianca;
        $return['horas_mes'] = $arrClt['horas_mes'];
        $return['quantidade_horas'] = $arrClt['quantidade_horas'];

        if (!$arrClt['horista_plantonista']) {
            $return['salario_base'] = $salarioBase;
        } else {
            $return['salario_base'] = $return['salario'];
        }

        return $return;
    }

    public function getGratificacaoProjeto($id_clt, $qnt, $info = false) {

        $base = $this->getSalarioBase($id_clt);

//        print_array($base);
        //VALOR DA HORA
        $valorBase = $this->getValorHora($base['salario_base'], $base['horas_mes']);

        if ($base['horista_plantonista'] && $base['funcao_professor']) {
            $qnt = ($qnt / 6) * 5;
        }

//        print_array($valorBase);

        $valor_grat = $valorBase * $qnt;
        $valor = $valor_grat;

        if ($info) {
            $valor = "<strong>Valor Hora (A): {$valorBase}</strong><br/>";
            $valor .= "<strong>Quantidade de Horas Proporcionais (B): $qnt</strong><br/>";
            $valor .= "<strong>Valor Total da " . utf8_encode("Gratificação") . " (C):</strong><br/>";
            $valor .= "<pre>$valor_grat (C) = {$valorBase} (A) * $qnt (B)</pre>";
        }

        return $valor;
    }

    /**

     * @author Lucas Praxedes

     * VERIFICA SE ALGUM CLT NA FOLHA ESTÁ RESCINDIDO E REMOVE DA FOLHA PROC

     */
    public function removeCltRescindido($idClt, $idFolha) {

        $sqlRescisao = "SELECT motivo FROM rh_recisao WHERE id_clt = $idClt AND status = 1";

        $queryRescisao = mysql_query($sqlRescisao);

        $numRescisao = mysql_num_rows($queryRescisao);



        if ($numRescisao > 0) {

            $motivoRescisao = mysql_result($queryRescisao, 0);



            if ($motivoRescisao >= 60 && $motivoRescisao != 67 && $motivoRescisao != 68 && $motivoRescisao != 70 && $motivoRescisao != 200) {

                $sqlRemove = "UPDATE rh_folha_proc SET status = '0'

                              WHERE id_clt = '$idClt' AND id_folha = '$idFolha';";

                $queryRemove = mysql_query($sqlRemove);
            }
        }
    }

    public function getAdTransferencia($id_clt) {

        $resultado = array(
            'id_mov' => 0,
            'cod_mov' => 0,
            'valor_integral' => 0,
            'porcentagem' => 0,
            'ids' => [521 => 521, 708 => 708, 709 => 709]
        );

        $arr_ads_transferencia = [
            '0' => [
                'id_mov' => 521,
                'cod_mov' => 80160
            ],
            '0.25' => [
                'id_mov' => 708,
                'cod_mov' => 80345,
                'porcentagem' => 0.25
            ],
            '0.35' => [
                'id_mov' => 709,
                'cod_mov' => 80346,
                'porcentagem' => 0.35
            ]
        ];

        $sql_clt = "SELECT A.ad_transferencia_tipo, A.ad_transferencia_valor, B.salario
                    FROM rh_clt A
                    LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                    WHERE id_clt = $id_clt";
        $query_clt = mysql_query($sql_clt);
        $arr_clt = mysql_fetch_assoc($query_clt);
        $salario = $arr_clt['salario'];
        
        if ($arr_clt['ad_transferencia_tipo'] == 1) {

            $resultado['valor_integral'] = $arr_clt['ad_transferencia_valor'];
            $resultado['id_mov'] = $arr_ads_transferencia[0]['id_mov'];
            $resultado['cod_mov'] = $arr_ads_transferencia[0]['cod_mov'];
        } else if ($arr_clt['ad_transferencia_tipo'] == 2) {

            $resultado['valor_integral'] = $salario * 0.25;
            $resultado['id_mov'] = $arr_ads_transferencia['0.25']['id_mov'];
            $resultado['cod_mov'] = $arr_ads_transferencia['0.25']['cod_mov'];
            $resultado['porcentagem'] = $arr_ads_transferencia['0.25']['porcentagem'];
        } else if ($arr_clt['ad_transferencia_tipo'] == 3) {

            $resultado['valor_integral'] = $salario * 0.35;
            $resultado['id_mov'] = $arr_ads_transferencia['0.35']['id_mov'];
            $resultado['cod_mov'] = $arr_ads_transferencia['0.35']['cod_mov'];
            $resultado['porcentagem'] = $arr_ads_transferencia['0.35']['porcentagem'];
        }

        return $resultado;
    }

    public function getAdUnidocencia($id_clt) {

        $resultado = array(
            'id_mov' => 518,
            'cod_mov' => 80157,
            'valor_integral' => 0,
            'ad_unidocencia' => 0
        );

        $sql_ad = "SELECT B.ad_unidocencia, A.ad_unidocencia ad_uni_clt
                    FROM rh_clt A
                    LEFT JOIN rhsindicato B ON (A.rh_sindicato = B.id_sindicato) 
                    WHERE id_clt = $id_clt";
        $query_ad = mysql_query($sql_ad);
        $arr_ad = mysql_fetch_assoc($query_ad);

        //VALOR PREENCHIDO NO SINDICATO
        $resultado['valor_integral'] = $arr_ad['ad_unidocencia'];
        //FLAG NO CADASTRO DE CLT
        $resultado['ad_unidocencia'] = $arr_ad['ad_uni_clt'];

        return $resultado;
    }

    public function getAdTempoServico($id_clt) {

        $resultado = array(
            'id_mov' => 522,
            'cod_mov' => 80161,
            'valor_integral' => 0,
            'porcentagem' => 0
        );

        $sql_ad = "SELECT B.tempo_ad_tempo_servico, B.tipo_ad_tempo_servico, B.valor_fixo_ad_tempo_servico, B.porc_ad_tempo_servico, C.salario
                    FROM rh_clt A
                    LEFT JOIN rhsindicato B ON (A.rh_sindicato = B.id_sindicato)
                    LEFT JOIN curso C ON (A.id_curso = C.id_curso)
                    WHERE id_clt = $id_clt";
        $query_ad = mysql_query($sql_ad);
        $arr_ad = mysql_fetch_assoc($query_ad);
        
        $salario = $arr_ad['salario'];
        
        
        if ($arr_ad['tipo_ad_tempo_servico'] == 1) {
            $resultado['valor_integral'] = $arr_ad['valor_fixo_ad_tempo_servico'];
        } else if ($arr_ad['tipo_ad_tempo_servico'] == 2) {
            $resultado['valor_integral'] = $salario * $arr_ad['porc_ad_tempo_servico'];
            $resultado['porcentagem'] = $arr_ad['porc_ad_tempo_servico'];
        }

        return $resultado;
    }

    public function getGratificacaoComplexidade($id_clt) {

        $resultado = array(
            'id_mov' => 663,
            'cod_mov' => 80300,
            'valor_integral' => 0
        );

        $sql_grat = "SELECT A.gratificacao_complexidade
                    FROM rh_clt A
                    WHERE id_clt = $id_clt";
        $query_grat = mysql_query($sql_grat);
        $arr_grat = mysql_fetch_assoc($query_grat);

        $resultado['valor_integral'] = $arr_grat['gratificacao_complexidade'];

        return $resultado;
    }

    public function getAdCargoConfianca($id_clt) {

        $resultado = array(
            'id_mov' => 520,
            'cod_mov' => 80159,
            'valor_integral' => 0,
            'porcentagem' => 0
        );

        $sql = "SELECT B.salario, B.valor_ad_cargo_confianca, B.percentual_ad_cargo_confianca
                FROM rh_clt A
                LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        
        if ($arr['horista_plantonista']) {
            $salario = $arr['valor_hora'] * $arr['quantidade_horas_proporcional'];
        } else {
            $salario = $arr['salario'];
        }
        
        if ($arr['valor_ad_cargo_confianca'] > 0) {
            $resultado['valor_integral'] = $arr['valor_ad_cargo_confianca'];
        } else if ($arr['percentual_ad_cargo_confianca'] > 0) {
            $resultado['valor_integral'] = $arr['percentual_ad_cargo_confianca'] * $salario;
            $resultado['porcentagem'] = $arr['percentual_ad_cargo_confianca'];
        }

        return $resultado;
    }
    
    public function getGratificacaoFuncao($id_clt) {
        
        $resultado = array(
            'id_mov' => 256,
            'cod_mov' => 50228,
            'valor_integral' => 0
        );
        
        $sql = "SELECT B.gratificacao_funcao
                FROM rh_clt A
                LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        
        $resultado['valor_integral'] = $arr['gratificacao_funcao'];
        
        return $resultado;
        
    }
    
    public function getQuebraCaixa($id_clt) {
        
        $resultado = array(
            'id_mov' => 527,
            'cod_mov' => 80166,
            'valor_integral' => 0
        );
        
        $sql = "SELECT B.quebra_caixa
                FROM rh_clt A
                LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        
        $resultado['valor_integral'] = $arr['quebra_caixa'];
        
        return $resultado;
        
    }
    
    public function getAjudaCusto($id_clt) {
        
        $resultado = array(
            'id_mov' => 13,
            'cod_mov' => 5011,
            'valor_integral' => 0
        );
        
        $sql = "SELECT A.ajuda_custo
                FROM rh_clt A
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        
        $resultado['valor_integral'] = $arr['ajuda_custo'];
        
        return $resultado;
        
    }
    
    public function getProdPercFixo($id_clt) {
        
        $resultado = array(
            'id_mov' => 692,
            'cod_mov' => 80329,
            'valor_integral' => 0,
            'porcentagem' => 0
        );
        
        $sql = "SELECT A.produtividade_percentual_fixo, B.salario
                FROM rh_clt A
                LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        $salario = $arr['salario'];
        
        $resultado['porcentagem'] = $arr['produtividade_percentual_fixo'];
        $resultado['valor_integral'] = $salario * $arr['produtividade_percentual_fixo'];
        
        return $resultado;
        
    }
    
    public function getRiscoVida($id_clt) {
        
        $resultado = array(
            'id_mov' => 149,
            'cod_mov' => 8004,
            'valor_integral' => 0,
            'porcentagem' => '0.30'
        );
        
        $sql = "SELECT B.salario, B.risco_vida
                FROM rh_clt A
                LEFT JOIN curso B ON (A.id_curso = B.id_curso)
                WHERE id_clt = $id_clt";
        $query = mysql_query($sql);
        $arr = mysql_fetch_assoc($query);
        
        if ($arr['risco_vida']) {
            $resultado['valor_integral'] = $arr['salario'] * $resultado['porcentagem'];
        }
        
        return $resultado;
        
    }

}