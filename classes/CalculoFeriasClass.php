<?php
class Calculo_Ferias{
    
    private $id_clt;
    private $id_movFaltas = array(232,62);
    private $tipo_quantidade = array(1 => 'HORAS', 2 => 'DIAS');
    private $meses = array("1" => "Janeiro", "2" => "Fevereiro", "3" => "Março", "4" => "Abril", "5" => "Maio", "6" => "Junho","7" => "Julho", "8" => "Agosto", "9" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
    
    public function setIdClt($id_clt){
        $this->id_clt = $id_clt;
    }
    
    
    public function getIdClt($id_clt){
        $this->id_clt = $id_clt;
    }
    
    public function getClt(){
       return $this->id_clt;
    }
    
    public function setPeriodosNegado($periodos){
        $this->periodosNegados = $periodos;
    }
    
    public function getPeriodosNegados(){
        return $this->periodosNegados;
    }
    
    /** 
     * @param type $data
     * @param type $tipo  1- segundos, 2- dia, mes, ano
     * @return type
     */
    
    public function getDataCalc($data, $tipo){
        
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
    
    public  function getFeriasPorClt(){        
        $qr_ferias = mysql_query("SELECT A.*,B.nome as nome_usuario,
                                        DATE_FORMAT(A.data_aquisitivo_ini,'%d/%m/%Y') as data_aquisitivo_iniBR,
                                        DATE_FORMAT(A.data_aquisitivo_fim,'%d/%m/%Y') as data_aquisitivo_fimBR,
                                        DATE_FORMAT(A.data_ini,'%d/%m/%Y') as data_iniBR,
                                        DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fimBR,
                                        DATE_FORMAT(A.data_proc,'%d/%m/%Y') as data_procBR                                
                                FROM rh_ferias as A
                                LEFT JOIN funcionario as B ON(A.user =  B.id_funcionario)
                                WHERE A.id_clt = '{$this->id_clt}' AND A.status = '1' ORDER BY A.data_fim DESC");
       $total  = mysql_num_rows($qr_ferias);
        while($row = mysql_fetch_assoc($qr_ferias)){
             $resultado['registros'][] = $row;
        }
        $resultado['total_registro'] = $total;
        
        return  $resultado;        
    }

    
    /** Períodos gozados 
     * @param type $id_clt
     * @return type
     */
    public function getPeriodosGozados(){
        $i =0;       
        $qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '{$this->id_clt}' AND status = '1' ORDER BY id_ferias DESC ") or die(mysql_error());
        while ($periodos = mysql_fetch_assoc($qr_periodos)) {               
            $i = $i++;
            $periodos_gozados[$i]['inicio'] = $periodos[data_aquisitivo_ini];
            $periodos_gozados[$i]['fim']    = $periodos[data_aquisitivo_fim];

        }  
                      
        $RETORNO =  $periodos_gozados;    
        return $RETORNO;
     } 
     
     /** Períodos gozados 
     * @param type $id_clt
     * @return type
     */
    public function getPeriodosGozados2(){
        $array_per_gozados = array();
        $i =0;
          $qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '{$this->id_clt}' AND status = '1' ORDER BY id_ferias ") or die(mysql_error());
           while ($periodos = mysql_fetch_assoc($qr_periodos)) {               
               $i = $i++;
               $periodos_gozados[$i]['inicio'] = date('Y', str_replace("/","-",strtotime($periodos[data_aquisitivo_ini])));
               $periodos_gozados[$i]['fim']    = date('Y', str_replace("/","-",strtotime($periodos[data_aquisitivo_fim])));
               
               $array_per_gozados[] = date('d/m/Y', str_replace("/","-",strtotime($periodos[data_aquisitivo_ini]))) . " - " . date('d/m/Y', str_replace("/","-",strtotime($periodos[data_aquisitivo_fim])));
               
           }  

        return $array_per_gozados;
     } 
     
     
    /**
     * Periodos Aquisitivos
     * 
     * Obs: Passando os períodos gozados retorna os periodos aquisitivos disponiveis do funcionário
     * @param type $data_admissao
     * @param type $periodos_gozados Array 
     * @param type $tipo  Tipo de calculo:  1 - Ferias, 2 - Rescisao 
     * @return string
     */
    public function getPeriodoAquisitivo($data_admissao, $periodos_gozados = NULL, $tipo = 1) {     
        
        
       $periodos_gozados2 = $this->getPeriodosGozados2();
       $data_admissao   = explode('-', $data_admissao);          
       $quantidade_anos = date('Y') - $data_admissao[0];
       $quantidade_anos = ($tipo == 2)? $quantidade_anos + 1: $quantidade_anos ;
       $array_total_periodos = array();
       $periodos = array();
       $periodosN = array();
       $clt = $this->getClt();

       for($a = 0; $a < $quantidade_anos; $a++) {
             $aquisitivo_inicio    = date('Y-m-d', mktime('0','0','0', $data_admissao[1], $data_admissao[2], $data_admissao[0] + $a));
             $aquisitivo_final     = date('Y-m-d', mktime('0','0','0', $data_admissao[1], $data_admissao[2] - 1, $data_admissao[0] + $a + 1)); 
             $array_total_periodos[] = date('d/m/Y', str_replace("/","-",strtotime($aquisitivo_inicio))) ." - ". date('d/m/Y', str_replace("/","-",strtotime($aquisitivo_final)));
       }         

       $result = array_diff($array_total_periodos, $periodos_gozados2);
       
       /**
        * CRIADO POR: SINÉSIO LUIZ
        * 14/07/2015
        */
       foreach($result as $datas){
            /**
             * EXPLODINDO DATAS PARA VERIFICAÇÃO.
             */
            $explode  = str_replace(" ", "", explode("-",$datas));

            /**
             * EXPLODINDO PRIMEIRA DATA DA FORMA MAIS NOJENTA POSSIVEL
             */
            $d_ini = explode("/",$explode[0]);
            $data_ini = $d_ini[2] . "-" . $d_ini[1] . "-" . $d_ini[0]; 

            /**
             * EXPLODINDO SEGUNDA DATA DA FORMA MAIS NOJENTA POSSIVEL
             */
            $d_fim = explode("/",$explode[1]);
            $data_fim = $d_fim[2] . "-" . $d_fim[1] . "-" . $d_fim[0]; 

            /**
             * VEFIFICAÇÃO
             */
            $dias = $this->verificaDiasEventoNoPeriodo($clt, $data_ini, $data_fim);
            if(!empty($dias) && $dias != 0){
                $periodosN[] = array(
                    "periodos" => date('d/m/Y', str_replace("-","/",strtotime($data_ini))) . " à " . date('d/m/Y', str_replace("-","/",strtotime($data_fim))),
                    "dias" => "{$dias} Dias de Licença Dentro do Periodo."
                );
            }
            
//            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    print_r($periodosN);
//                echo "</pre>";
//            }
            
            $this->setPeriodosNegado($periodosN);
            
            if($dias <= 180){
                $periodos[] = $datas;
            }
        }
           
        
        return $periodos;
    }   
     
    /**
      * MÉTODO PARA VERIFICAR A QUANTIDADE DE DIAS DE EVENTOS DENTRO DO PERIODO AQUISITIVO;
      * @param type $clt
      * @param type $ini_periodo
      * @param type $fim_periodo
      */
     public function verificaDiasEventoNoPeriodo($clt,$ini_periodo,$fim_periodo){
         
         $dias = 0;
         $query = " SELECT SUM(dias) AS total FROM(
                        SELECT A.id_clt,A.nome_status,A.cod_status,
                            if('{$ini_periodo}' > A.data, '{$ini_periodo}', A.data) AS data,	
                            if('{$fim_periodo}' < A.data_retorno, '{$fim_periodo}', A.data_retorno) AS data_retorno,
                            if(
                                DATEDIFF(if('{$fim_periodo}' < A.data_retorno,'{$fim_periodo}',A.data_retorno), if('{$ini_periodo}' > A.data, '{$ini_periodo}', A.data)) > 0,
                                DATEDIFF(if('{$fim_periodo}' < A.data_retorno,'{$fim_periodo}',A.data_retorno), if('{$ini_periodo}' > A.data, '{$ini_periodo}', A.data)),0
                            ) AS dias
                        FROM rh_eventos AS A
                        WHERE ((A.data BETWEEN '{$ini_periodo}' AND '{$fim_periodo}') OR (A.data_retorno BETWEEN '{$ini_periodo}' AND '{$fim_periodo}')) AND A.id_clt = '{$clt}' AND A.status = 1 AND A.cod_status  NOT IN(10,40,50)
                    ) AS tmp";
        
//        if($_COOKIE['logado'] == 179){
//            echo "<pre>" . $query . "</pre>";
//        }                
                        
        $sql = mysql_query($query) or die("Erro ao verificar dias de evento no periodo");    
        while($rows = mysql_fetch_assoc($sql)){
            $dias = $rows['total'];
        }
        
        return $dias;
     }
    
    
    /**Período de concessão
    * 
    * Retorna o prazo de concessão das férias, após esse prazo o valor da férias  é dobrada
    * @param type $dt_aquisitivo_termino
    * @param type $dt_dobrado Data para verificar se as férias vão ser dobradas. Preencher com a data de inicio do periodo de gozo das férias, 
    * no caso de rescisão vai ser a data da rescisão 
    * @return type
    */
    public function getPeriodoConcessivo($dt_aquisitivo_termino, $dt_dobrado = NULL ){  
        $inicio_concessao  = $dt_aquisitivo_termino;
        $dt_termino        = $this->getDataCalc($dt_aquisitivo_termino, 2);         
        $inicio_concessao  =  date('Y-m-d',mktime('0', '0', '0', $dt_termino['mes'], $dt_termino['dia'] +1, $dt_termino['ano']));

        $termino_concessao =  mktime('0', '0', '0', $dt_termino['mes'], $dt_termino['dia'], $dt_termino['ano'] + 1);
        $termino_concessao = date('Y-m-d', $termino_concessao);

        if($dt_dobrado != NULL){
                $dobrado = ($termino_concessao < $dt_dobrado) ? TRUE: FALSE;
        }          

        $resultado['inicio']  = $inicio_concessao;
        $resultado['fim']     = $termino_concessao;
        $resultado['dobrado'] = $dobrado;
        return  $resultado;
    }

    /**
     * Quantidade de faltas no período aquisitivo
     * @param type $periodo_inicio
     * @param type $periodo_fim
     */
     public function getFaltasNoPeriodo($periodo_inicio, $periodo_fim){
     
         $dt_inicio = substr($periodo_inicio, 0,7);
         $dt_fim    = substr($periodo_fim, 0,7);        
         
        
           
         $qr_folha = mysql_query("SELECT A.mes, A.ano,A.tipo_terceiro, A.ids_movimentos_estatisticas FROM rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE B.id_clt = {$this->id_clt} AND A.status = 3 AND B.status = 3
                                    AND DATE_FORMAT(CAST(CONCAT(A.ano,'-',A.mes,'-01') as DATE),'%Y-%m') >= '{$dt_inicio}' 
                                    AND DATE_FORMAT(CAST(CONCAT(A.ano,'-',A.mes,'-01') as DATE),'%Y-%m') <= '{$dt_fim}'
                                    ORDER BY A.ano, A.mes    ;") or die(mysql_error());
        while($row_folha = mysql_fetch_assoc($qr_folha)){
            
           $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$this->id_clt}' AND id_movimento IN(".$row_folha['ids_movimentos_estatisticas'].") AND id_mov IN(".implode(',',  $this->id_movFaltas).") AND status = 5");
            while($row_mov = mysql_fetch_assoc($qr_movimento)){
                
                $movimento[$row_mov['id_movimento']]['nome']            = $row_mov['nome_movimento']; 
                $movimento[$row_mov['id_movimento']]['mes']             = $row_mov['mes_mov']; 
                $movimento[$row_mov['id_movimento']]['nome_mes']        = $this->meses[$row_mov['mes_mov']]; 
                $movimento[$row_mov['id_movimento']]['ano']             = $row_mov['ano_mov'];
                $movimento[$row_mov['id_movimento']]['valor']           = $row_mov['valor'];
                $movimento[$row_mov['id_movimento']]['tipo_quantidade'] = $this->tipo_quantidade[$row_mov['tipo_qnt']];
                $movimento[$row_mov['id_movimento']]['quantidade']      = $row_mov['qnt'];                
                $total_faltas += $row_mov['qnt'];               
              
            }            
        } 
        
        $resultado['movimentos']   = $movimento;
        $resultado['total_faltas'] = $total_faltas;
        return $resultado;
     }
     
     
     /**
      * Se o funcionário tiver faltas, esse método calcula o dias que o clt vai ter
      * de remuneração de férias de acordo com as faltas
      * 
      * Usada na geração das férias
      * @param type $faltas
      * @return int
      */
     public function getDiaProporcionalFaltas($faltas){
         
            if ($faltas <= 5) {
                $qnt_dias = 30;
            } elseif ($faltas >= 6 and $faltas <= 14) {
                $qnt_dias = 24;
            } elseif ($faltas >= 15 and $faltas <= 23) {
                $qnt_dias = 18;
            } elseif ($faltas >= 24 and $faltas <= 32) {
                $qnt_dias = 12;
            } elseif ($faltas > 32) {
                $qnt_dias = 0;
            }
      
            return $qnt_dias;
     }
     
     
     
     /**não é usado a partir daqui **/
     
     /**
      * Formata os período par um array
      * @param type $periodos
      * @return type
      */
     public function formataPeriodo($periodos){
            if(sizeof($periodos) >0){
                  foreach ($periodos as $valor){
                           list($periodo['inicio'], $periodo['fim']) = explode('/', $valor);
                           $resultado[] = $periodo;
                   }
               return $resultado;
          }
     }
     
     /**
      *Método para pegar os períodos aquisitivos, períodos vencidos,periodo gozado, períodos não gozados
      * 
      * @param type $id_clt
      * @param type $data_admissao
      * @param type $data_final  Ex: data atual, data de demissão
      * @return type
      */
     public function getPeriodos($id_clt, $data_admissao,   $data_final){

                       $dt_admissao_calc = $this->getDataCalc($data_admissao, 2);
                       $quantidade_anos  = (date('Y') - $dt_admissao_calc['ano']) ;
                       $periodos_gozados =  $this->verificaPeriodosGozados($id_clt);

                       for ($a = 0; $a < $quantidade_anos; $a++) {

                           $aquisitivo_inicio      = date('Y-m-d', strtotime("$data_admissao + $a year"));
                           $aquisitivo_final       = date('Y-m-d', mktime('0', '0', '0', $dt_admissao_calc['mes'], $dt_admissao_calc['dia'] - 1, $dt_admissao_calc['ano'] + $a + 1));
                          
                           $periodo_aquisitivo     = $aquisitivo_inicio . '/' . $aquisitivo_final;
                           $periodos_aquisitivos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;                        
                           
                           $this->getPeriodoConcessivo($aquisitivo_final);
                            
                           
                          if (@!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_final) {
                              
                               $periodos_vencidos[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                               
                           } elseif(@!in_array($periodo_aquisitivo, $periodos_gozados)) {
                               
                               $periodo_nao_gozados[] = $aquisitivo_inicio . '/' . $aquisitivo_final;
                           }
                       }      
                       
                       $resultado['periodos_aquisitivos'] = $this->formataPeriodo($periodos_aquisitivos);                   
                       $resultado['periodos_gozados']      = $this->formataPeriodo($periodos_gozados);
                       $resultado['periodos_vencidos']     = $this->formataPeriodo($periodos_vencidos);
                       $resultado['periodos_nao_gozados'] = $this->formataPeriodo($periodo_nao_gozados);  
                      return $resultado;
     }
  
    
     
     public function getPeriodoFeriasRescisao($id_clt, $data_admissao, $data_fim){
         
      $this->setIdClt($id_clt);  
      $periodosGozado = $this->getPeriodosGozados(); 
      $periodosAquisitivoDisponiveis  = $this->getPeriodoAquisitivo($data_admissao, $periodosGozado);
      
      
      foreach($periodosAquisitivoDisponiveis as $chave =>$periodos){
          
         // $periodoConcessivo = $this->getPeriodoConcessivo($periodos['fim'], $data_fim);
          
          if($periodos['fim'] > $data_fim){
              $periodosProporcional['inicio'] = $periodos['inicio'];
              $periodosProporcional['fim']    = $data_fim;
              break; //Para o loop ao encontrar a data procional pois daqui para frente não será necessário contar
          } else{              
              $periodosVencidos[$chave]['inicio'] = $periodos['inicio'];
              $periodosVencidos[$chave]['fim']    = $periodos['fim'];
          }   
          
      }
      
      $resultado['periodo_disponivel']   = $periodosAquisitivoDisponiveis;
      $resultado['periodo_gozados']      = $periodosGozado;
      $resultado['periodo_proporcional'] = $periodosProporcional;
      $resultado['periodos_vencido']     = $periodosVencidos;
      return $resultado;          
     }
        
    
     
     
}



