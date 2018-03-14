<?php
/*
 * PHP-DOC - RhMovimentosCltClass.php
 * 
 * Classe de intragração dos movimentos
 * 
 * 21-09-2015
 * 
 * @name Movimentos
 * @package RhMovimentosCltClass  
 * @access public  
 * 
 * @version
 *  
 * Versão: 3.0.0000 - 21/09/2015 - Jacques - Versão Inicial
 * Versão: 3.0.4917 - 14/12/2015 - Jacques - Adicionado o código 50521 Insalubridade 40% no método getCalcTotInsalubridadePericulosidade para a Lagos 
 * Versão: 3.0.4920 - 14/12/2015 - Jacques - Foi alterado ao invéz de valor fixo no retorno do método getCalcTotInsalubridadePericulosidade para o último movimento lançado
 * Versão: 3.0.5112 - 18/12/2015 - Jacques - O método getCalcTotInsalubridadePericulosidade chamava o método setMakeSelectSalarioVariavel que usava período aquisitivo
 *                                           como referência, o que não deveria pois pode não pega valores de insalubridade e periculosidade atualizados.
 *                                           Foi então atribuido ao método getCalcTotInsalubridadePericulosidade montagem de query específica e não montada
 *                                           pelo método setMakeSelectSalarioVariavel que constroi uma consulta que envolve período pegando movimentos da folha.
 *                                           Retirado também para o método getCalcValorPensao a consulta por período e o uso de período que retorna os
 *                                           movimentos lançados na folha pois pode não pegar valores atualizados e não lançados ainda na folha.
 *                                           Ambos os métodos não devem usar os IDs de movimento da folha conforme sugestão de Sinésio.
 *                                           O método getCalcTotInsalubridadePericulosidade passou a pegar apenas os movimentos que compreende os mes_mov entre 1 é 12 
 * Versão: 3.0.5456 - 11/01/2016 - Jacques - Alterado no método getCalcTotInsalubridadePericulosidade o calculo da insalubridade baseada no valor do salário mínimo de 2016 provisoriamente
 * 
 * 
 * @author jacques
 * 
 * @copyright www.f71.com.br
 *  
 */

class RhMovimentosCltClass {
    
    private $rh_movimentos_clt_ext = array(
                                'incidencia_inss' => 0,
                                'incidencia_irrf' => 0,
                                'incidencia_fgts' => 0
                                );
    
    public function setIncidenciaInss($value) {

        $this->$rh_movimentos_clt_ext['incidencia_inss'] = $value;
        
        return $this;

    }      
    
    public function setIncidenciaIrrf($value) {

        $this->$rh_movimentos_clt_ext['incidencia_irrf'] = $value;
        
        return $this;

    }      
    
    public function setIncidenciaFgts($value) {

        $this->$rh_movimentos_clt_ext['incidencia_fgts'] = $value;
        
        return $this;

    }      
    
    /*
     * PHP-DOC 
     * 
     * @name setFieldIncidencia
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe com base nas incidência de rh_movimentos
     * 
     */    
    public function setFieldIncidencia(){
        
        $this->db->setQuery(SELECT,",
                            (
                            SELECT incidencia_inss
                            FROM rh_movimentos m
                            WHERE m.id_mov=a.id_mov
                            ) AS incidencia_inss
                            ",ADD);
            
        $this->db->setQuery(SELECT,",
                            (
                            SELECT incidencia_irrf
                            FROM rh_movimentos m
                            WHERE m.id_mov=a.id_mov
                            ) AS incidencia_irrf
                            ",ADD);
        
        $this->db->setQuery(SELECT,",
                            (
                            SELECT incidencia_fgts
                            FROM rh_movimentos m
                            WHERE m.id_mov=a.id_mov
                            ) AS incidencia_fgts
                            ",ADD);
        
        return $this;    
        
    }
    
    public function selectExt($collection){
        
        try{
            
            /*
             * Adição de campos extra a cláusula select
             */
            $this->setFieldIncidencia();
            

            /*
             * Para selecionar movimentos é imprescindível que a classe folha esteja instânciada e setIdsMovimentosEstatisticas definido, mais range
             * Caso contrário os registros do movimento poderão ser orfãos.
             * 
             * Existe alguns registros com status zero e regiao zero entrando nos movimentos antigos por estarem setados na folha
             */
            if(is_object($this->getSuperClass()->Folha)){

                if(is_object($this->getSuperClass()->Clt)){

                    $this->setIdClt($this->getSuperClass()->Clt->getIdClt());

                }   
                
                $this->setDateRangeIni($this->getSuperClass()->Folha->getDateRangeIni());
                $this->setDateRangeEnd($this->getSuperClass()->Folha->getDateRangeEnd());
                
                $this->setTipoMovimento($this->getSuperClass()->Folha->getIdsMovimentosEstatisticas());

            }   
            else {

                if(is_object($this->getSuperClass()->Clt)){

                    $this->setIdRegiao($this->getSuperClass()->Clt->getIdRegiao());
                    $this->setIdClt($this->getSuperClass()->Clt->getIdClt());

                }        

                $this->setTipoMovimento($this->getSuperClass()->Folha->getIdsMovimentosEstatisticas());

            }
           
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0); 
            
        }
        
        return $this;
        
    }
    
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcTotInsalubridadePericulosidade()
     * 
     * @internal - Obtem o valor dos movimentos no período de Insalubridade ou Periculosidade 
     * 
     */     
    public function getCalcTotInsalubridadePericulosidade(){
        
        try {
            
            $valor = 0;

            if(!$cod_movimento = $this->getSuperClass()->getKeyMaster(3)) $this->error->set(array(8,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(is_object($this->getSuperClass()->Clt) && $this->getMagneticKey()){

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
                $id_clt = $this->getSuperClass()->Clt->getIdClt();

            }        
            else {

                $id_regiao = $this->getIdRegiao();
                $id_projeto = $this->getIdProjeto();
                $id_clt = $this->getIdClt();

            }        
            
            $this->setDefault();
            
            $this->setIdRegiao($id_regiao);
            
            $this->setIdProjeto($id_projeto);
            
            $this->setIdClt($id_clt);
            
            $this->setTipoMovimento('CREDITO');
            
            $this->setCodMovimento($cod_movimento);
            

            /*
             * Pega todos os movimentos que não foram excluídos
             */
            $this->setStatusReg(1);

            /*
             * Pega todos os movimento finalizados que recebem o status 5 que indica que foram efetivados
             */
            $this->setStatus(5);

            /*
             * Pegar apenas os valores maiores que zero para evitar pegar lixo e os ids de movimentos da folha
             */

            $this->db->setQuery(WHERE," valor_movimento > 0 AND mes_mov >= 1 AND mes_mov <= 12 AND ",ADD);

            $this->db->setQuery(ORDER,'ano_mov DESC,mes_mov DESC');

            $this->db->setQuery(LIMIT,'1');
            
            if(!$this->select()->isOk()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->getRow();

            /*
             * Verificação de inconsistência nos registros:
             * 
             * Executa uma verificação se existe alguma definição de Insalubridade no Clt ou Periculosidade em Cursos
             * e não existe movimento definido para o Clt ou vive-versa.
             */

            if(($this->getSuperClass()->Clt->getInsalubridade() || $this->getSuperClass()->Curso->getPericulosidade30()) &&  empty($this->db->getRow('cod_movimento'))){

                $this->error->set('Erro na verificação de Consistência: Existe insalubridade/periculosidade definida para o Clt ou Curso mas não existe movimento',E_FRAMEWORK_NOTICE);            


            }elseif((!$this->getSuperClass()->Clt->getInsalubridade() && !$this->getSuperClass()->Curso->getPericulosidade30()) &&  $this->db->getRow('cod_movimento')){

                $this->error->set('Erro na verificação de Consistência: Existe insalubridade/periculosidade lançada em movimento para o Clt mas não existe referência a ele definida no cadastro ou função',E_FRAMEWORK_NOTICE);  

            }

            $this->getSuperClass()->Curso->getPericulosidade30();

            $cod_movimento_array = explode(',',$cod_movimento);

            /*
             * Se insalubridade 20% atribui valor fixo de 176,00
             */
            if($this->db->getRow('cod_movimento')==$cod_movimento_array[0]){

                $valor = 880.00*0.20; //$this->db->getRow('valor_movimento'); 

            }
            /*
             * Se insalubridade 40% atribui valor fixo de 352,00
             */
            elseif($this->db->getRow('cod_movimento')==$cod_movimento_array[1]){

                $valor = 880.00*0.40; //$this->db->getRow('valor_movimento'); 

            }    
            /*
             * Se Periculosidade atribui valor do movimento
             */
            elseif($this->db->getRow('cod_movimento')==$cod_movimento_array[2]){

                $valor = $this->db->getRow('valor_movimento');

            }    
            /*
             * Caso contrário seta valor zerado
             */
            else {

                $valor = 0;

            }

        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return  $valor;
        
    }
    
    
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcValorPensaoXXX()
     * 
     * @internal - Obtem o valor da Pensão Alimentícia caso exista e verifica a consistência para o valor obtido
     * 
     *             O calculo depende da ordem judicial segundo Donato
     */     
    public function getCalcValorPensao($value){

        try {

            if(!is_object($this->getSuperClass()->Ferias)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            if(is_object($this->getSuperClass()->Clt)){

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
                $id_clt = $this->getSuperClass()->Clt->getIdClt();

            }        
            else {

                $id_regiao = $this->getIdRegiao();
                $id_projeto = $this->getIdProjeto();
                $id_clt = $this->getIdClt();

            }
            
            $this->setDefault();
            
            $this->setIdRegiao($id_regiao);
            $this->setIdProjeto($id_projeto);
            $this->setIdClt($id_clt);
            
            $this->setStatusFerias(1);

            /* 
             * getKeyMaster(4) obtem as chaves referentes a pensão alimentícias
             */

            $this->setCodMovimento($this->getSuperClass()->getKeyMaster(4));

            $this->setTipoMovimento('DEBITO');

            /*
             * Pega todos os movimento finalizados que recebem o status 5
             */
            $this->setStatus(5);

            $this->db->setQuery(ORDER, "id_movimento DESC");

            if(!$this->select()->isOk()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR); 
            
            if($this->getRow()->isOk()){
                
                $ps = $this->getSuperClass()->Movimentos->setDefault()->setCod($this->getCodMovimento())->select()->getRow('percentual');
                
                $pensao_alimenticia = round((($this->getSuperClass()->Ferias->getSalario() + $this->getSuperClass()->Ferias->getSalarioVariavel() + $this->getSuperClass()->Ferias->getInsalubridadePericulosidade() + $this->getSuperClass()->Ferias->getUmTerco()) - ($this->getSuperClass()->Ferias->getInss() + $this->getSuperClass()->Ferias->getIr())) * $ps,2);

                if(empty($value)){

                     return $pensao_alimenticia;

                }
                else {

                     $array_format = explode('|',$value);

                     $moeda = $array_format[0];
                     $separador_unidades = $array_format[1];
                     $separador_fracao = $array_format[2];
                     $casas_decimais = $array_format[3];

                     return $moeda.number_format($pensao_alimenticia, $casas_decimais, $separador_fracao, $separador_unidades);      

                }              


            }
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            return 0;
            
        }
        
    }
    
    
    
    public function getValorTotal(){
        
        try {

            $this->db->setQuery(SELECT,"
                                IF(ISNULL(SUM(valor_movimento)),0,SUM(valor_movimento)) AS total
                                "
                                 );

            $this->db->setQuery(FROM, "rh_movimentos_clt");      
            $this->db->setQuery(WHERE, "status = 1");

            if(is_object($this->getSuperClass()->Clt)){

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
                $id_clt = $this->getSuperClass()->Clt->getIdClt();

            }        
            else {

                $id_regiao = $this->getIdRegiao();
                $id_projeto = $this->getIdProjeto();
                $id_clt = $this->getIdClt();

            }

            if(is_object($this->getSuperClass()->Folha)){

                $dateRangeFmt = $this->getSuperClass()->Folha->getDateRangeFmt();
                $dateRangeIni = $this->getSuperClass()->Folha->getDateRangeIni($dateRangeFmt);
                $dateRangeFim = $this->getSuperClass()->Folha->getDateRangeFim($dateRangeFmt);
                $dateRangeSqlFmt = $this->getSuperClass()->Folha->getDateRangeSqlFmt($dateRangeFmt);

                $ids_movimentos_estatisticas = $this->getSuperClass()->Folha->getIdsMovimentosEstatisticas();

            }   
            else {

                $dateRangeFmt = $this->getDateRangeFmt();
                $dateRangeIni = $this->getDateRangeIni($dateRangeFmt);
                $dateRangeFim = $this->getDateRangeFim($dateRangeFmt);
                $dateRangeSqlFmt = $this->getDateRangeSqlFmt($dateRangeFmt);

            }

            $dateRangeField = $this->getDateRangeField();
            $tipo_movimento = $this->getTipoMovimento();
            $status_ferias = $this->getStatusFerias();
            $lancamento = $this->getLancamento();

            if(empty($id_regiao) && empty($id_projeto) && empty($id_clt) && empty($tipo_movimento) && empty($status_ferias)) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}

            if(!empty($tipo_movimento)) {$this->db->setQuery(WHERE,"AND tipo_movimento = '{$tipo_movimento}'",ADD);}

            if(!empty($status_ferias)) {$this->db->setQuery(WHERE,"AND status_ferias = {$status_ferias}",ADD);}

            if(!empty($lancamento)) {$this->db->setQuery(WHERE,"AND lancamento = {$lancamento}",ADD);}

            if(!empty($dateRangeIni) && !empty($dateRangeFim)) {$this->db->setQuery(WHERE,"AND $dateRangeField BETWEEN '{$dateRangeIni}' AND '{$dateRangeFim}'",ADD);}

            if(!empty($ids_movimentos_estatisticas)) {$this->db->setQuery(WHERE,"AND ids_movimentos_estatisticas = {$ids_movimentos_estatisticas}",ADD);}

            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

            $this->db->setRow();
            
            return $this->db->getRow('total');
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
            
        return 0;
        
    }
        
}