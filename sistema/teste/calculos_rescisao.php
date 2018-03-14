<?php
include '../conn.php';

class Calculo_rescisao extends calculos{

    /**
     * Depende da classe 'calculos', arquivo calculos.php
     */
   
    private $id_clt;
    public $fator;
    public $motivo_rescisao;
    public $tipo_aviso;
    public $config;
    /**
     * Id do clt
     * @param type $id_clt
     */
    public function setClt($id_clt){     
        $this->id_clt = $id_clt;            
    }
    
    /**
     *  
     * @param type $fator
     */
    public function setFator($fator){
        $this->fator = $fator;
    }
    
    /**
     * Código do Motivo de rescisao
     * Ver a tabela rhstatus
     * @param type $tipo
     */
    public function setMotivoRescisao($tipo){     
        $this->motivo_rescisao = $tipo;
    }
    
    /**
     * Tipo de aviso prévio( se houver)
     * @param type $aviso trabalhado ou indenizado
     */
    public function setTipoAviso($aviso){     
        $this->tipo_aviso = $aviso;
    }
    
    /**
     * @param type $data
     * @param type $tipo 1- segundos, 2- dia, mes, ano
     * @return type
     */
    public Function getData($data, $tipo){
        
        list($ano, $mes, $dia) = explode('-', $data);                 
        switch($tipo){
            case 1:   $retorno = mktime(0, 0, 0, $mes, $dia, $ano);
                break;
            case 2: $retorno['ano'] = $ano;
                    $retorno['mes'] = $mes;
                    $retorno['dia'] = $dia;
                break;
        }        
        return $retorno;
    }
    
    /**
     * @param type $salario_base
     * @return type
     */
    public function getValorsalarioDia($salario_base){
        return $salario_base/30;
    }

    /**
     * Retorna as informações do clt para a rescisão
     * @return type
     */
    public function getDadosClt (){        
         $qr_clt = mysql_query("SELECT A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso,
                        DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaF, 
                        DATE_FORMAT(data_demi, '%d/%m/%Y') as data_demiF, 
                        A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, 
                        A.salario_outra_empresa, A.desconto_outra_empresa,  
                        IF(DATEDIFF(data_demi, data_entrada) >= 365, 1, 0) as um_ano,                        
                        B.salario, B.nome as nome_funcao, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                        
                        /*MESES TRABALHADOS*/
                       ( SELECT IF( PERIOD_DIFF(demissao, admissao) >= 12, 12, PERIOD_DIFF(demissao, admissao)) as meses
                              FROM 
                                (SELECT CONCAT(YEAR(data_entrada),SUBSTR(data_entrada,6,2)) as admissao,
                                CONCAT(YEAR(data_demi), SUBSTR(data_demi,6,2) ) as demissao,
                                data_entrada, data_demi
                                FROM rh_clt WHERE id_clt = '{$this->id_clt}' ) as folha
                        ) as qnt_meses_media
                        
                        FROM rh_clt as A 
                        INNER JOIN curso as B
                        ON B.id_curso = A.id_curso
                        WHERE id_clt = '{$this->id_clt}' ") or die(mysql_error());
         return   $row_clt = mysql_fetch_assoc($qr_clt);
    }
    
    
    /**
     * Calcula diferença dos meses e ano
     * @param type $dt_admissao
     * @param type $dt_demissao
     * @return type
     */
    public function getPeriodoTrabalhado($dt_admissao, $dt_demissao){
        
        $demissao                     = $this->getData($dt_demissao, 2);    
        $dt_inicio                    = $demissao['ano'].'-'.$demissao['mes'].'-01';
        $diferencias_dias             = $this->getData($dt_demissao,1) - $this->getData($dt_inicio,1) ;    
        $retorno['dias_trabalhados']  = floor($diferencias_dias/(60*60*24)) + 1;        
        
        $diferenca                     = $this->getData($dt_demissao,1) - $this->getData($dt_admissao,1);      
        $retorno['anos_trabalhados']   = floor($diferenca/(365*60*60*24));
        
        return $retorno;
    }
 
    /**
     * 
     * @param type $um_ano
     * @return type
     */
    public function getRescisaoConfig($um_ano){         
         $um_ano = ($this->motivo_rescisao == 63 or $this->motivo_rescisao == 64 or $this->motivo_rescisao == 66) ? 2 : $um_ano;
         
         $restatus = mysql_query("SELECT A.especifica, A.codigo_saque, B.* FROM rhstatus as A
                                INNER JOIN rescisao_config as B 
                                ON A.codigo = B.tipo
                                WHERE A.codigo = '{$this->motivo_rescisao}' AND ano = '$um_ano'");
        $this->config = mysql_fetch_assoc($restatus);
    }
    
    
    public function getInss($base_calc, $data, $desconto_inss = null, $salario_outra_empresa = null, $desconto_outra_empresa = null){
        
      
        
            $this->MostraINSS($base_calc, $data);  
            $resultado['valor_inss'] = $this->valor;        
            if($desconto_inss == 1){
                  if ($desconto_outra_empresa + $resultado['valor_inss'] > $this->teto) {
                      $resultado['valor_inss'] = ($this->teto - $desconto_outra_empresa);
                  }
            } 
        $resultado['desconto_inss']          = $desconto_inss;
        $resultado['salario_outra_empresa']  = $salario_outra_empresa;
        $resultado['desconto_outra_empresa'] = $desconto_outra_empresa;
        $resultado['percentual']             = $this->percentual;
        return $resultado;
    }
    
    public function getIrrf($base_calc, $id_projeto, $data){
      
        $this->MostraIRRF($base_calc, $this->id_clt, $id_projeto, $data);
        $resultado['valor_irrf'] = $this->valor;

           if ($resultado['valor_irrf'] > 0) {
               $resultado['percentual']    = $this->percentual;
               $resultado['total_menor']   = $this->total_filhos_menor_21;
               $resultado['valor_ddir']    = $this->valor_deducao_ir_total;
               $resultado['valor_parcela'] = $this->valor_fixo_ir;
           } 
           
        return $resultado;   
    }
    
    
    
    /**
     * SALDO DE SALÁRIO
     * @param type $salario_base
     * @param type $dias_trabalhados
     */     
    public function getSaldoSalario( $salario_base, $dias_trabalhados){     
        if($this->config['saldodesalario'] == 1){
            $saldo_salario = $this->getValorsalarioDia($salario_base) * $dias_trabalhados;
            return $saldo_salario;
        } else {
            return 0;
        }
    }
    
    
    /**
     * Calcula as médias de movimentos
     */
    public function getMediaMovimentos(){
          $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes, A.ano
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '{$this->id_clt}'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");
                        
        while ($row_folha = mysql_fetch_assoc($qr_folha)) {            
            if (!empty($row_folha[ids_movimentos_estatisticas])) {
                $qr_movimentos = mysql_query("SELECT *
                                            FROM rh_movimentos_clt
                                            WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  
                                            AND tipo_movimento = 'CREDITO' AND id_clt = {$this->id_clt} AND id_mov NOT IN(56,200)");
                while ($mov = mysql_fetch_assoc($qr_movimentos)) {                     
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['nome']       = $mov['nome_movimento'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['id_mov']     = $mov['id_mov'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['mes']        = $row_folha['mes'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['ano']        = $row_folha['ano'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['codigo']     = $mov['cod_movimento'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['categoria']  = $mov['tipo_movimento'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['lancamento'] = $mov['lancamento'];
                    $resultado['movimentos'][$row_folha['ano']][$row_folha['mes']][$mov['id_movimento']]['valor']      = $mov['valor_movimento'];   
                    $total_movimento += $mov['valor_movimento'];
                }
            }
        }

        if ($total_movimento != 0) {
            $media = $total_movimento / 12;
        } else {
            $media = 0;
        }
        
      
        $resultado['total_movimentos'] = $total_movimento;
        $resultado['total_media']      = $media;       
        return $resultado;
    }
    

    /**
     * Movimentos da rescisão
     */
    public  function getMovimentosRescisao(){           
           $qr_mov = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$this->id_clt}' AND mes_mov = 16 AND status = 1");
           while($mov = mysql_fetch_assoc($qr_mov)){               
               $resultado['movimentos'][$mov['id_movimento']]['nome']      = $mov['nome_movimento'];
               $resultado['movimentos'][$mov['id_movimento']]['id_mov']    = $mov['id_mov'];
               $resultado['movimentos'][$mov['id_movimento']]['codigo']    = $mov['cod_movimento'];
               $resultado['movimentos'][$mov['id_movimento']]['categoria'] = $mov['tipo_movimento'];
               $resultado['movimentos'][$mov['id_movimento']]['valor']     = $mov['valor_movimento'];
               $resultado['movimentos'][$mov['id_movimento']]['incidencia'] = $mov['incidencia'];
               
               $incidencias = explode(',',$mov['incidencia']);
              
               if(in_array(5020, $incidencias) ){
                   if($mov['tipo_movimento'] == 'CREDITO'){   
                       $base_inss += $mov['valor_movimento'];
                    } else {
                        $base_inss -= $mov['valor_movimento'];
                     }
               }
               if(in_array(5021, $incidencias) ){
                   if($mov['tipo_movimento'] == 'CREDITO'){ 
                      $base_irrf += $mov['valor_movimento'];
                    } else {
                      $base_irrf -= $mov['valor_movimento'];
                    }
               }
               if(in_array(5023, $incidencias) ){
                   if($mov['tipo_movimento'] == 'CREDITO'){  
                      $base_fgts += $mov['valor_movimento'];
                    } else {
                       $base_fgts -= $mov['valor_movimento'];
                    }
               }             
           }
            $resultado['base_inss'] = $base_inss;
            $resultado['base_irrf'] = $base_irrf;
            $resultado['base_fgts'] = $base_fgts;               
            return $resultado;
       } 
    
    
    
    
   /**
    * Décimo Térceiro
    * @param type $salario_base_13  Salário + Insalubridade Integral
    * @param type $dt_admissao
    * @param type $dt_demissao
    * @return int
    */ 
   public function getDecimoTerceiroProporcional($base_calc_13, $dt_admissao, $dt_demissao){     
       
        if ($this->config['13salario'] == 1) {
            
            $data_inicio    = $this->getData($dt_admissao, 2);
            $data_fim       = $this->getData($dt_demissao, 2);
            $valor_mes      = ($base_calc_13) / 12;
            
             //Quantidade de meses
            $valor_13_folha   = $this->verifica13();            
            $dt_inicio_calc   = (($data_inicio['ano'] == $data_fim['ano']) or $valor_13_folha == 0 ) 
                                ?  $data_fim['ano'] .'-01-01'
                                : $dt_admissao;
            
            $avos_13          =  $this->calculaQntAvos13($dt_inicio_calc, $dt_demissao);  
            $decimoIndenizado = $this->getDecimoTerceiroIndenizado($valor_mes);
            $valor_td         = $valor_mes * $avos_13;
            $baseCalc_inss    = $valor_td + $decimoIndenizado['valor'];
            
            // Calculando INSS sobre DT
            $this->MostraINSS($baseCalc_inss, $dt_demissao);
            $valor_inss   = $this->valor;    
            $percentual_inss = $this->percentual;

            // Calculando IRRF sobre DT
            $baseCalc_irrf = $baseCalc_inss - $valor_inss;
            $irrf         = $this->getIrrf($baseCalc_irrf, $this->id_clt, $idprojeto, $dt_demissao);
            $valor_irrf = $this->valor;
            if ($valor_irrf > 0) {
                $percentual_irrf      = $this->percentual;
                $qnt_dependentes_irrf = $this->total_filhos_menor_21;
                $valor_irrf           = $this->valor_deducao_ir_total;
                $parcela_irrf         = $this->valor_fixo_ir;
            } else {
                $baseCalc_irrf = 0;
            }
            
            $resultado['avos_13']              = $avos_13;            
            $resultado['valor_13']             = $valor_td;            
            $resultado['avos_13_indenizado']   = $decimoIndenizado['avos_13'];            
            $resultado['valor_13_indenizado']  = $decimoIndenizado['valor'];            
            $resultado['base_inss']            = $baseCalc_inss;            
            $resultado['inss']                 = $valor_inss;            
            $resultado['percentual_inss']      = $percentual_inss;            
            $resultado['base_irrf']            = $baseCalc_irrf;            
            $resultado['percentual_irrf']      = $percentual_irrf;            
            $resultado['valor_irrf']           = $valor_irrf;            
            $resultado['qnt_dependentes_irrf'] = $qnt_dependentes_irrf;            
            $resultado['parcela_irrf']         = $parcela_irrf;
            
            return  $resultado;
        } else {
           return 0;
        }
   }
  
   
   /**
    * Décimo terceiro Indenizado
    * @param type $valor_mes
    * @param type $avos_13
    * @return type
    */
   public function getDecimoTerceiroIndenizado($valor_mes, $avos_13 = 1){
        
            if ($this->tipo_aviso == 'indenizado') {               
                $avos_13_indenizado = 1;   
                if ($this->motivo_rescisao == 65) {
                        $avos_13_indenizado   = 0;
                        $valor_13_indenizado = 0;
                } else {                 
                        $valor_13_indenizado = $valor_mes * $avos_13_indenizado;
                }
            }
            
            $resultado['avos_13'] = $avos_13_indenizado;
            $resultado['valor']   = $valor_13_indenizado;
            return $resultado;
   }
  
   
   public function verifica13(){       
       ///Verifica se  a pesssoa recebeu décimo terceiro no ano
            $qr_verifica_13_folha = mysql_query("SELECT a.id_clt,SUM(a.salliquido) as sal_liquido,b.data_fim,tipo_terceiro
                                                FROM rh_folha_proc a
                                                INNER JOIN rh_folha b ON a.id_folha = b.id_folha
                                                WHERE a.id_clt = {$this->id_clt} AND a.ano = " . date('Y') . " AND a.status = '3' AND b.terceiro = 1
                                                ORDER BY b.tipo_terceiro DESC") or die(mysql_error());
            $row_veri_decimo = mysql_fetch_assoc($qr_verifica_13_folha);
            $verifica_13_folha = mysql_num_rows($qr_verifica_13_folha);            
            $valor_decimo_folha = ($row_veri_decimo['tipo_terceiro'] == 1)?$row_veri_decimo['sal_liquido'] : 0;
            return $valor_decimo_folha;
   }



#calcula a quantidade de meses de 13 e férias na rescisao
function  calculaQntAvos13($dt_inicial, $dt_final){
  
            $dt_inicio         = $this->getData($dt_inicial, 2);
            $dt_fim            = $this->getData($dt_final, 2);            
            $dt_inicio_seg     = $this->getData($dt_inicial, 1);
            $dt_fim_seg        = $this->getData($dt_final, 1);            
            $diferenca_meses   =  floor(($dt_fim_seg - $dt_inicio_seg)/(30*60*60*24));    
          
            for($i=0;$i<=$diferenca_meses;$i++){                  
                
                $primeiro_dia   = ($i == 0) ? $dt_inicio['dia'] : 1;
                $mes_calc       = $dt_inicio['mes'] + $i;                
                $data_1         =  mktime(0,0,0, $mes_calc,$primeiro_dia, $dt_inicio['ano'] );                
                $ultimo_dia_mes = cal_days_in_month(CAL_GREGORIAN, date('m',$data_1), date('y',$data_1));   
                $dia_metade_mes = round($ultimo_dia_mes/2);
                
                $data_2         =  mktime(0,0,0, $mes_calc , $ultimo_dia_mes, $dt_inicio['ano'] );
                $data_2         = ($data_2 >= $dt_fim_seg) ? $dt_fim_seg : $data_2;

                $dias_trab      = (($data_2 - $data_1)/86400) + 1;
                if($dias_trab >=15){    $meses_ativos +=1; }   
                $debug[] =  date('d/m/Y',$data_1).' - '.date('d/m/Y',$data_2).' = '.round($dias_trab).' dias.  MEIO: '.$dia_metade_mes.'<br>';                     
               
            }           
            return $meses_ativos;
}

    

    /**
     * AVISO Prévio
     * @param type $base_aviso  Salário contratual + insalubridade integral
     */
    public function getAvisoPrevio( $base_aviso, $qnt_anos){ 
        
         if ($this->config['avisoprevio'] == 1){
             if($this->tipo_aviso == 'indenizado') {

                if ($this->motivo_rescisao == 65) {
                    $aviso = "PAGO pelo funcionário";
                    $debito = $base_aviso;
                } else {
                    $credito = $base_aviso;
                } 
             }     
             
          $lei_12506 = $this->getLei12506($base_aviso, $qnt_anos);
         } 
          $resultado['dias_lei_12506']  = $lei_12506['dias'];
          $resultado['valor_lei_12506'] = $lei_12506['valor'];
          $resultado['aviso']           = $aviso;
          $resultado['aviso_credito']   = $credito;
          $resultado['aviso_debito']    = $debito;   
          return $resultado;
    }
            
    /**
     * Lei 12506 
     * Usado no método getAvisoPrevio()
     * 
     * @param type $base_aviso
     * @param type $qnt_anos
     */
    public function getLei12506($base_aviso, $qnt_anos){
        
        $valor_dia = $base_aviso / 30;
        
        for ($d = 1; $d <= (int) $qnt_anos; $d++) {
            $lei_12_506 +=  $valor_dia * 3;
            $dias       += $dias + 3;
        }
        
        $resultado['dias']   = $dias;
        $resultado['valor']  = $lei_12_506;
        return $resultado;        
    }
    
    
    
    public function getFeriasVencidas(){  }
    public function getFeriasProporcionais(){  }
    
    
    
    
    
    public function getArt477($salario_base, $data_demissao){ 
    
        $data_hoje  = date('Y-m-d');
        $data1      = date('Y-m-d', strtotime("$data_demissao +1 days"));
        $data10     = date('Y-m-d', strtotime("$data_demissao +10 days"));

        if($this->config['avisoprevio']){
           if ( ($data_hoje >= $data1 and $this->motivo_rescisao == 66) or
                (  $data_hoje >= $data10 and $this->motivo_rescisao == 65 and $this->tipo_aviso == 'indenizado') or 
                ( $data_hoje >= $data10 and $t_ap == 1 )
             ) {
                $resultado = $salario_base;
            }
        }        
        return $resultado; 
    }
        
    
    /**
     * Calcula a quantidade de dias restantes para o término do período de experiência,
     * somente para rescisão antecipada.
     * @param type $data_admissao
     * @param type $data_demissao
     * @return type
     */
    public function getDiasExperienciaRestantes($data_admissao, $data_demissao){
        
        $data45         = strtotime("+ 44 days", strtotime($data_admissao));
        $data90         = strtotime("+ 89 days", strtotime($data_admissao));
        $data_demissao  = $this->getData($data_demissao, 1);
        
        if($data_demissao <= $data45) {
            $resultado['periodo_dias'] = 45;
            $resultado['dias_restantes'] = ($data45 - $data_demissao)/(60 * 60 * 24);
        }else
        if($data_demissao <= $data90){
            $resultado['periodo_dias'] = 90;
            $resultado['dias_restantes'] = ($data90 - $data_demissao)/(60 * 60 * 24);
        }
        
       return $resultado;
    }
    
    /**
     * Artigo 479
     * 
     * @param type $salario_base
     * @param type $data_admissao
     * @param type $data_demissao
     * @return type
     */
    public function getArt479($salario_base, $data_admissao, $data_demissao){
        
        if($this->config['indenizacao479']){  
            
            $calcDiasExp = $this->getDiasExperienciaRestantes($data_admissao, $data_demissao);            
            $resultado['valor']      = ($salario_base/30) * ($calcDiasExp['dias_restantes'] /2);
            $resultado['periodo_dias']   = $calcDiasExp['periodo_dias'];
            $resultado['dias_restantes'] = $calcDiasExp['dias_restantes'];
        }        
        return $resultado;
    }   
    
    
    /**
     * Artigo 480
     * 
     * @param type $salario_base
     * @param type $data_admissao
     * @param type $data_demissao
     * @return type
     */
    public function getArt480($salario_base, $data_admissao, $data_demissao){
        
        if($this->config['indenizacao480']){  
            
            $calcDiasExp = $this->getDiasExperienciaRestantes($data_admissao, $data_demissao);            
            $resultado['valor']      = ($salario_base/30) * ($calcDiasExp['dias_restantes'] /2);
            $resultado['periodo_dias']   = $calcDiasExp['periodo_dias'];
            $resultado['dias_restantes'] = $calcDiasExp['dias_restantes'];
        }        
        return $resultado;
    }   
    
    /**
     * 
     * @param type $salario_base
     * @param type $dias_trabalhados
     * @param type $id_projeto
     * @param type $data_demissao
     * @return type
     */
    public function getSalarioFamilia($salario_base,$dias_trabalhados, $id_projeto,$data_demissao){ 
        
        if($this->config['salariofamilia']){
            $this->Salariofamilia($salario_base, $this->id_clt, $id_projeto, $data_demissao, '2');
            $valor_sal_familia = (($this->valor) / 30) * $dias_trabalhados;            
          
            if ($valor_sal_familia > 0) {

                $resultado['valor']         = $valor_sal_familia;
                $resultado['total_filhos']  = $this->filhos_menores;
            }
        }        
        return $resultado;
    }
    
}
?>