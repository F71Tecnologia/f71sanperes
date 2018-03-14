<?php
/*
 * PHO-DOC - RhDocStatusClass.php
 * 
 * Classe de definição de tipos de documentos para upload
 * 
 * 26-02-2016
 *
 * @name RhDocStatusClass 
 * @package RhDocStatusClass
 * @access public 
 *  
 * @version 
 *
 * Versão: 3.0.5055 - 26/02/2016 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 
          
            
        
class RhDocStatusClass {
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_doc,
                            tipo,
                            obs,
                            obs2,
                            data_obs,
                            id_clt,
                            data,
                            id_user,
                            status_reg,
                            motivo
                            ");
        
        $this->db->setQuery(FROM,"rh_doc_status ");
        
        if(is_object($this->getSuperClass()->Clt)){

            $id_clt = $this->getSuperClass()->Clt->getIdClt();

        }        
        else {
            
            $id_clt = $this->getIdClt();
            
        }    
        
        if(is_object($this->getSuperClass()->Documentos)){

            $id_doc = $this->getSuperClass()->Documentos->getIdDoc();

        }        
        else {
            
            $id_doc = $this->getIdDoc();
            
        }    
        
        
        $id_tipo = $this->getTipo();
        $id_user = $this->getIdUser();
        $id_motivo = $this->getMotivo();
        
        $this->db->setQuery(WHERE, " status_reg ");

        if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}

        if(!empty($id_doc)) {$this->db->setQuery(WHERE,"AND id_doc = {$id_doc}",ADD);}
        
        if(!empty($id_tipo)) {$this->db->setQuery(WHERE,"AND id_tipo = {$id_tipo}",ADD);}
        
        if(!empty($id_user)) {$this->db->setQuery(WHERE,"AND id_user = {$id_user}",ADD);}
        
        if(!empty($id_motivo)) {$this->db->setQuery(WHERE,"AND id_motivo = {$id_motivo}",ADD);}
        
        $this->db->setQuery(ORDER, " data ASC ");
        
        if(empty($id_doc) && empty($id_tipo) && empty($id_user) && empty($id_motivo)) $this->db->setQuery(LIMIT,"100");

        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método RhDocStatusClass->select()",E_FRAMEWORK_ERROR);
        
//        echo $this->db->getLastQuery();
        
        return $this;
        
            
    }

}