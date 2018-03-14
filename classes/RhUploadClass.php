<?php
/*
 * PHO-DOC - RhUploadClass.php
 * 
 * Classe de definição de tipos de documentos para upload
 * 
 * 26-02-2016
 *
 * @name RhUploadClass 
 * @package RhUploadClass
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
          
            
        
class RhUploadClass {
	
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_upload,
                            arquivo,
                            descricao,
                            descricao
                            ");
        
        $this->db->setQuery(FROM,"upload ");
        
        $this->db->setQuery(WHERE, " 1 = 1 ");

        if(!empty($id_doc)) {$this->db->setQuery(WHERE,"AND id_doc = {$id_doc}",ADD);}

        $this->db->setQuery(ORDER, " ordem ASC ");
        
        if(empty($id_doc)) $this->db->setQuery(LIMIT,"200");

        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método RhUploadClass->select()",E_FRAMEWORK_ERROR);
        
        return $this;
        
            
    }


}