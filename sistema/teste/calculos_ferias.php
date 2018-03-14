<?php
class Ferias{
    
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
        $resultado['total_registro'] = mysql_num_rows($qr_ferias);
        while($row = mysql_fetch_assoc($qr_ferias)){
             $resultado['registros'][] = $row;
        }
        return  $resultado;        
    }

    
    /** Períodos gozados 
     * @param type $id_clt
     * @return type
     */
    public function verificaPeriodosGozados(){
          $qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '{$this->id_clt}' AND status = '1' ORDER BY id_ferias DESC ") or die(mysql_error());
           while ($periodos = mysql_fetch_assoc($qr_periodos)) {
               $periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
           }  
        $RETORNO =  $periodos_gozados;    
        return $RETORNO;
     } 
     
     
     /**
      * Periodos Aquisitivos
      * 
      * Obs: Passando os períodos gozados retorna os periodos aquisitivos que o funcionário não gozou férias
      * @param type $data_admissao
      * @param type $periodos_gozados Array 
      * @return string
      */
     public function verificaPeriodoAquisitivo($data_admissao, $periodos_gozados = NULL) {         
          
          $data_admissao   = explode('-', $data_admissao);          
          $quantidade_anos = date('Y') - $data_admissao[0];
          
          for($a = 0; $a < $quantidade_anos; $a++) {
                $aquisitivo_inicio    = date('Y-m-d', mktime('0','0','0', $data_admissao[1], $data_admissao[2], $data_admissao[0] + $a));
                $aquisitivo_final     = date('Y-m-d', mktime('0','0','0', $data_admissao[1], $data_admissao[2] - 1, $data_admissao[0] + $a + 1)); 
                $periodo_aquisitivo   = $aquisitivo_inicio.'/'.$aquisitivo_final;

                
                if($periodos_gozados != NULL){
                      if(!in_array($periodo_aquisitivo, $periodos_gozados)) {  $periodo_disponivel[] =  $periodo_aquisitivo; }
                } else {
                    $periodo_disponivel[] = $periodo_aquisitivo;
                }       
          }          
          return $periodo_disponivel;
         
     }
     
      /**Período de concessão
      * 
      * Retorna o prazo de concessão das férias, após esse prazo o valor da férias  é dobrada
      * @param type $dt_aquisitivo_termino
      * @param type $dt_dobrado Data para verificar se as férias vão ser dobradas. Preencher com a data de inicio do periodo de gozo das férias, 
      * no caso de rescisão vai ser a data da rescisão 
      * @return type
      */
     public function verificaPeriodoConcessivo($dt_aquisitivo_termino, $dt_dobrado = NULL ){
       
         $inicio_concessao  = $dt_aquisitivo_termino;
         $dt_termino        = $this->getDataCalc($dt_aquisitivo_termino, 2);         
         $inicio_concessao  =  date('Y-m-d',mktime('0', '0', '0', $dt_termino['mes'], $dt_termino['dia'] +1, $dt_termino['ano']));
         
         $termino_concessao =  mktime('0', '0', '0', $dt_termino['mes'], $dt_termino['dia'], $dt_termino['ano'] + 1);
         $termino_concessao = date('Y-m-d', $termino_concessao);
         
         if($dt_dobrado != NULL){
             if($termino_concessao < $dt_dobrado){
                 $dobrado = ($termino_concessao < $dt_dobrado) ? TRUE: FALSE;
             }             
         } 
         
         
         $resultado['periodo'] = $inicio_concessao.'/'.$termino_concessao;
         $resultado['inicio']  = $inicio_concessao;
         $resultado['fim'] = $termino_concessao;
         $resultado['dobrado'] = $dobrado;
         return  $resultado;
     }
     
     /**
      * Quantidade de faltas no período aquisitivo
      * @param type $periodo_inicio
      * @param type $periodo_fim
      */
     public function verificaFaltasNoPeriodo($periodo_inicio, $periodo_fim){
         echo '<pre>';
         $dt_inicio = substr($periodo_inicio, 0,7);
         $dt_fim    = substr($periodo_fim, 0,7);        
         
         $qr_folha = mysql_query("SELECT A.mes, A.ano,A.tipo_terceiro, A.ids_movimentos_estatisticas FROM rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE B.id_clt = {$this->id_clt} AND A.status = 3 AND B.status = 3
                                    AND DATE_FORMAT(CAST(CONCAT(A.ano,'-',A.mes,'-01') as DATE),'%Y-%m') >= '{$dt_inicio}' 
                                    AND DATE_FORMAT(CAST(CONCAT(A.ano,'-',A.mes,'-01') as DATE),'%Y-%m') <= '{$dt_fim}';") or die(mysql_error());
        while($row_folha = mysql_fetch_assoc($qr_folha)){
            
            
             
            $qr_movimento = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '{$this->id_clt}' AND id_movimento IN(".$row_folha['ids_movimentos_estatisticas'].") AND id_mov IN(".implode(',',  $this->id_movFaltas).") AND status = 5");
            while($row_mov = mysql_fetch_assoc($qr_movimento)){
                
                $movimento[$row_mov['id_movimento']]['nome']            = $row_mov['nome_movimento']; 
                $movimento[$row_mov['id_movimento']]['mes']             = $row_mov['mes_mov']; 
                $movimento[$row_mov['id_movimento']]['nome_mes']        = $this->meses[$row_mov['mes_mov']]; 
                $movimento[$row_mov['id_movimento']]['ano']             = $row_mov['ano_mov'];
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
      * @param type $faltas
      * @return int
      */
     public function verificaDiaProporcionalFaltas($faltas){
         
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
  
    
     
     
     
     
     
}



