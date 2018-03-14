<?php
/*
 * PHP-DOC - RhEmpresaClass.php
 * 
 * Classe de intragração dos movimentos
 * 
 * 21-09-2015
 * 
 * @name RhEmpresaClass
 * @package RhEmpresaClass
 * @access public  
 * 
 * @version
 *  
 * Versão: 3.0.0000 - 03/11/2015 - Jacques - Versão Inicial
 * 
 * @author jacques
 * 
 * @copyright www.f71.com.br
 *  
 */        

class RhEmpresaClass {

    public function select(){
        
        $this->createCoreClass();
        
        $this->db->setQuery(SELECT,$this->getFields());
        
        $this->db->setQuery(FROM," rhempresa ");

        $this->db->setQuery(WHERE," status = 1 ",ADD);
        
        if(is_object($this->getSuperClass()->Clt)){
            
            $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
            $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
            
        }        
        else {
            
            $id_regiao = $this->getIdRegiao();
            $id_projeto = $this->getIdProjeto();
            
        } 
        
        $id_empresa = $this->getIdEmpresa();
        
        
        if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}
        
        if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

        if(!empty($id_empresa)) {$this->db->setQuery(WHERE,"AND id_empresa = {$id_empresa}",ADD);}
        
        $this->db->setQuery(ORDER,
                            "
                            id_regiao DESC
                            ");
        
        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método select da classe RhEmpresaClass",E_FRAMEWORK_ERROR);
        
        return $this;
        
    }    

}