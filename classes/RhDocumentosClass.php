<?php
/*
 * PHO-DOC - RhDocumentosClass.php
 * 
 * Classe de definição de tipos de documentos para upload
 * 
 * 26-02-2016
 *
 * @name RhDocumentosClass 
 * @package RhDocumentosClass
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
          
            
        
class RhDocumentosClass {
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_doc,
                            documento,
                            tipo_contratacao,
                            descricao
                            ");
        
        $this->db->setQuery(FROM,"rh_documentos ");
        
        $id_doc = $this->getIdDoc();
        
        $this->db->setQuery(WHERE, " 1 = 1 ");

        if(!empty($id_doc)) {$this->db->setQuery(WHERE,"AND id_doc = {$id_doc}",ADD);}

        $this->db->setQuery(ORDER, " documento ASC ");
        
        if(empty($id_doc)) $this->db->setQuery(LIMIT,"100");

        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método RhDocumentosClass->select()",E_FRAMEWORK_ERROR);
        
        return $this;
        
            
    }

}