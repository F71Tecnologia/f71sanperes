<?php 
/*
 * PHP-DOC - RhCursoClass.php
 * 
 * Classe de intragra��o dos cursos
 * 
 * 21-09-2015
 * 
 * @package RhMovimentosCltClass
 * @access public  
 * 
 * @version
 * 
 * Vers�o: 3.0.6546 - 16/02/2016 - Jacques - Atualiza��o da classe para inst�nciamento din�mico
 * 
 * @author jacques
 * 
 * @copyright www.f71.com.br 
 *  
 */

class RhCursoClass {
    
    public function getCargo(){
        
        $cargo = str_replace("CAPACITANDO EM ","CAP. EM ",$this->getCampo2());
        $cargo = str_replace("T�CNICO ","TEC. ", $cargo);
        
        return $cargo;
        
    }

    public function select(){
        
        $this->createCoreClass();  
        
        $this->db->setQuery(SELECT,$this->getFields());
        
        $this->db->setQuery(FROM, "curso");
        
        
        if(is_object($this->getSuperClass()->Clt)){

            $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
            $id_curso = $this->getSuperClass()->Clt->getIdCurso();
            
        }        
        else {
            
            $id_regiao = $this->getIdRegiao();
            $id_curso = $this->getIdCurso();
            
        }
        
        
        $this->db->setQuery(WHERE, "1 = 1");  

        if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

        if(!empty($id_curso)) {$this->db->setQuery(WHERE,"AND id_curso = {$id_curso}",ADD);}
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do m�todo select da classe RhCursoClass');
        
        return $this;
        
    }    
    
}