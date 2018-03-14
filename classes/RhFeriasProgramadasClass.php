<?php
/*
 * PHO-DOC - RhFeriasProgramadasClass.php
 * 
 * Classe de manipulação dos registros das férias programadas
 * 
 * 01-10-2015
 *
 * @name RhFeriasProgramadasClass 
 * @package RhFeriasProgramadasClass
 * @access public 
 * 
 * @version
 *
 * Versão: 3.0.5055 - 16/01/2016 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 


class RhFeriasProgramadasClass {
    
    
    public function select(){
        
        try {
            
            $this->createCoreClass();
            
            $this->db->setQuery(SELECT,"
                                id_ferias_programadas,
                                id_clt,
                                inicio,
                                fim,
                                data_cad,
                                id_funcionario,
                                status
                                ");
            
            $this->db->setQuery(FROM, "rh_ferias_programadas ");    
            
            $this->setDateRangeField("DATE_FORMAT(inicio,'%Y%m')");
            $this->setDateRangeFmt('Ym');
            
            $dateRangeField = $this->getDateRangeField();
            $dateRangeIni = $this->getDateRangeIni()->val();
            $dateRangeFim = $this->getDateRangeFim()->val();

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
            
            $this->db->setQuery(WHERE, " 1 = 1 AND status");

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}
            
            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}
            
            if(!empty($dateRangeIni) && !empty($dateRangeFim)) {$this->db->setQuery(WHERE,"AND $dateRangeField BETWEEN '{$dateRangeIni}' AND '{$dateRangeFim}'",ADD);}
            
            $this->db->setQuery(ORDER, " inicio ");
            
            if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método select da classe RhFeriasProgramadasClass",E_FRAMEWORK_ERROR);
            
            $this->setValue(1);
            
        
        } catch (Exception $obj) {
            
            $this->setValue(0);
            
            $this->error->set("Uma excessão no método select da classe RhFeriasProgramadasClass impediu a execução normal do programa",E_FRAMEWORK_ERROR,$obj);
            
        }        
            
        return $this;
        
    }
   
    
}





