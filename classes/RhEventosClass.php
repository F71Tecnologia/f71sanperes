<?php
/*
 * PHO-DOC - RhEventosClass.php
 * 
 * Classe de intragração dos eventos 
 * 
 * 18-09-2015
 *
 * @name RhEventosClass 
 * @package RhEventosClass 
 * @access public 
 * 
 * @version 
 *
 * Versão: 3.0.0000 - 18-09-2015 - Jacques - Versão Inicial
 * Versão: 3.0.7734 - 10-03-2016 - Jacques - Adicionado try na função select dentro dos novos padrões do framework
 * Versão: 3.0.7768 - 10-03-2016 - Jacques - Adicionado os usos de mensagens de erro padrão da classe RhErrorClass
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 

const VENCIDOS = 0;
const NA_DATA = 1;
const A_VENCER = 2;


class RhEventosClass {
    
    private     $db_seguidos;

    private     $rh_eventos_ext = array(
                                       'dias_inicio_aviso' => 0,
                                       'tipo_aviso' => 0
                                       );
    
    public function setDiasInicioAviso($value){
        
        $this->rh_eventos_ext['dias_inicio_aviso'] = $value;
        
        return $this;
        
    }
    
    public function setTipoAviso($value){
        
        $this->rh_eventos_ext['tipo_aviso'] = $value;
        
        return $this;
        
    }
    
    public function setFieldTipoAviso(){
        
        $this->db->setQuery(SELECT, 
                            ", 
                            DATEDIFF(data_retorno,NOW()) AS dias_inicio_aviso,
                            (CASE WHEN DATEDIFF(data_retorno, NOW()) < 0 THEN 0 
                                  WHEN DATEDIFF(data_retorno, NOW()) = 0 THEN 1 
                                  ELSE 2 END) AS tipo_aviso
                            ",ADD);
        
        return $this;
        
    }
    
    public function getDiasInicioAviso(){
        
        return $this->rh_eventos_ext['dias_inicio_aviso'];
        
    }
    
    public function getTipoAviso(){
        
        return $this->rh_eventos_ext['tipo_aviso'];
        
    }
    
    public function getRowTermiando(){
        
        $this->setDiasInicioAviso($this->db->getRow('dias_inicio_aviso'));
        $this->setTipoAviso($this->db->getRow('tipo_aviso'));
        
    }

    public function getSeguidoRow(){

        if($this->db_seguidos->setRow()){

            $this->setSeguidoData($this->db_seguidos->setRow('data'));
            $this->setSeguidoSoma($this->db_seguidos('soma'));

            return 1;
            
        }
        else{
            
            //$this->error->setError($this->db_seguidos->error->getError());            
            
            return 0;
        }
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name selectExt
     * 
     * @internal - Método que adiciona as condições extendidas de um select de classe
     * 
     */    
    public function selectExt() {
        
        try {

            $this->setFieldTipoAviso();

            if(is_object($this->getSuperClass()->Clt) && $this->getMagneticKey()){

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();

                $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();

                $id_clt = $this->getSuperClass()->Clt->getIdClt(); 

                $cod_status = $this->getSuperClass()->Clt->getStatus();

            }        
            else {

                $id_regiao = $this->getIdRegiao();

                $id_projeto = $this->getIdProjeto();

                $id_clt = $this->getIdClt();

                $cod_status = $this->getCodStatus();

            }    

            $id_evento = $this->getIdEvento();

            $this->db->setQuery(WHERE," AND status ",ADD);
            
            if(!empty($id_evento)) {$this->db->setQuery(WHERE,"AND id_evento = {$id_evento}",ADD);}

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt IN ({$id_clt})",ADD);}

            if(!empty($cod_status)) {$this->db->setQuery(WHERE,"AND cod_status = {$cod_status}",ADD);}
            
            $this->setValue(1);
            
        } 
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING);        
            
            $this->setValue(0);
            
        }
        
        return $this;        
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name xxxxxxxxx
     * 
     * @internal -  Método para 
     * 
     */    
    public function xxxxxxxxx() {
        
        try {
            
            
        }
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING);        
            
            $this->setValue(0);
            
        }

        return 0;
        
    }
    

    /*
     * PHP-DOC 
     * 
     * @name getEventosSeguidos
     * 
     * @internal -  Método para selecionar quantos dias o funcionário está em evento de um mesmo tipo sendo esses eventos seguidos
     * 
     */    
    public function getTotalDiasEventosSeguidos() {
        
        try {

            $total_dias = 0;

            $ant_data = '0000-00-00';
            $ant_data_retorno = '0000-00-00';

            $this->setDefault()->select();

            while ($this->getRow()->isOk()) {

                /*
                 * Faz uma verificação de consistência se existe um evento contido dentro de outro
                 * 
                 * Falta definir se 3x6=18 é diferente de 1x18 de licença
                 */
                if($this->getData() < $ant_data && $this->getDataRetorno() > $ant_data_retorno ){

                }

                if ($this->getData()->diffInDays($ant_data_retorno)->val() >= 1) {

                    $total_dias += $this->getDias();

                } else {

                    $total_dias = $this->getDias();
                }

                $ant_data = $this->getData();
                $ant_data_retorno = $this->getDataRetorno();

            }

            $this->setValue(1);
            
        }
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING);        
            
            $this->setValue(0);
            
        }
            
        
        return $total_dias;
        
    }
    
    /*
     * PHP-DOC - Método para verificar se o clt possui evento de Licença Médica (20) e Acidente de Trabalho (70) com mais de 180 dias
     */
    public function getNovaDataEventosComMaisDe180Dias() { 
        
        /*
         * Select agrupa e soma dias de eventos em um mesmo ano
         */
        $this->db->setQuery(SELECT, "
                                    MAX(data_retorno) AS data_aquisitivo_ini,
                                    CONCAT(YEAR(MAX(data_retorno))+1,'-',LPAD(MONTH(MAX(data_retorno)), 2, '0'),'-',LPAD(DAY(MAX(data_retorno))-1, 2, '0')) AS data_aquisitivo_fim,
                                    SUM(
                                        DATEDIFF(
                                            (
                                            CASE 
                                                WHEN YEAR(data) < YEAR(data_retorno) THEN CONCAT(DATE_FORMAT(data,'%Y'),DATE_FORMAT(data_retorno,'1231'))
                                                ELSE data_retorno
                                            END
                                            ),
                                            data
                                        )
                                    ) AS soma_eventos_mais_180    
                                    ");
        
        /*
         * Having seleciona apenas a soma dos eventos em um mesmo ano que forem iguais ou maiores que 180 dias
         */
        $this->db->setQuery(HAVING, "
                                    SUM(
                                        DATEDIFF(
                                            (
                                            CASE 
                                                WHEN YEAR(data) < YEAR(data_retorno) THEN CONCAT(DATE_FORMAT(data,'%Y'),DATE_FORMAT(data_retorno,'1231'))
                                                ELSE data_retorno
                                            END
                                            ),
                                            data
                                        )
                                    ) >= 180    
                                    ");
        
        /*
         * Apenas eventos de Licença Médica (20) e Acidente de Trabalho (70) devem ser selecionados
         */
        $this->db->setQuery(WHERE, "cod_status IN (20,70)");
        
        $this->db->setQuery(FROM, "rh_eventos");

        
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
        

        if(is_object($this->getSuperClass()->Ferias)){
    
            $data_aquisitivo_ini = $this->getSuperClass()->Ferias->getDataAquisitivoIni('Ymd');
            $data_aquisitivo_fim = $this->getSuperClass()->Ferias->getDataAquisitivoFim('Ymd');
            
        }   
        else {
            
            $this->error->set("Método rh->Eventos->getNovaDataEventosComMaisDe180Dias não pode ser executado porque a classe rh->Ferias não está instanciada",E_FRAMEWORK_ERROR);
            
        }

        if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt IN ({$id_clt})",ADD);}

        if(!empty($data_aquisitivo_ini) && !empty($data_aquisitivo_fim)) {$this->db->setQuery(WHERE," AND data BETWEEN '".$data_aquisitivo_ini."' AND '".$data_aquisitivo_fim."'",ADD);}

        if($this->db->setRs()){
            
            return $this->db->getArray();
            
        }
        else {

            return 0;
            
        }    

    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getCollectionVencidosVencer
     * 
     * @internal -  Método para selecionar de um ou mais clts a situação de seus eventos (vencidos, no prazo e a vencer)
     * 
     */ 
    public function getCollectionVencidosVencer() {
        
        $this->select();
        
        while ($this->getRow()->isOk()) {
            
            /*
             * Adiciona as propriedades extendidas da classe Eventos
             */
            $this->getRowTermiando();
            
            /*
             * $this->getTipoAviso():
             * 
             * VENCIDOS = 0 = Já passaram da data
             * NA_DATA  = 1 = Para os que estão no dia
             * A_VENCER = 2 = Os que estão no prazo
             */
            $this->getSuperClass()->Status->setCodigo($this->getCodStatus())->select()->getRow();
                            
            $collection[$this->getTipoAviso()][$this->getCodStatus()] = array(
                                                            "id_clt" => $this->getIdClt(),
                                                            "data" => $this->getData(),
                                                            "data_retorno" => $this->getDataRetorno(),
                                                            "id_evento" => $this->getIdEvento(),
                                                            "nome_status" => $this->getNomeStatus(),
                                                            "prorrogavel" => $this->getSuperClass()->Status->getProrrogavel(),
                                                            "pericia" => $this->getSuperClass()->Status->getPericia(),
                                                            "data_final" => $this->getDataRetornoFinal(),
                                                            "dias_restantes" => $this->getDiasInicioAviso()
                                                        );              
            
        }
        
        return $collection;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getUltimoEvento
     * 
     * @internal -  Método para selecionar o último evento dentro de um período pre-determinado
     * 
     */ 
    public function getUltimoEvento() {
        
        $this->db->setQuery(ORDER,"data DESC ");
        
        $this->select();
        
        if($this->getRow()->isOk()){
            
            return array(
                        'cod_evento' => $this->getCodStatus(),
                        'data_ini' => $this->getData(),
                        'data_fim' => $this->getDataRetorno(),
                        'dias' => $this->getData()->diffInDays($this->getDataRetorno())->val()
                        );
            
        }
        else {
            
            return 0;
            
        }
        
    }
    
}
