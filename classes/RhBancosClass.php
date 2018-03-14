<?php

/*
 * PHP-DOC - RhBancosClass.php 
 * 
 * Classe para acesso a tabela bancos 
 *
 * 04-01-2016
 * 
 * @package RhBancosClass
 * @access public   
 * 
 * @version
 *  
 * Versão: 1.00.0000 - 04/01/2016 - Jacques - Versão Beta da classe de conexão
 * Versão: 1.00.0000 - 04/01/2016 - Jacques - Método select utilizando o método de montagem de query da classe MySqlClass em descontinuado. 
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */

class RhBancosClass {
  
    public function select(){

        $this->createCoreClass();  
        
        $this->db->setQuery(SELECT,$this->getFields());
        
        $this->db->setQuery(FROM," bancos ");
        
        if(is_object($this->getSuperClass())){
            
            $id_banco = $this->getSuperClass()->Clt->getBanco();           
            $id_Regiao = $this->getSuperClass()->Clt->getIdRegiao();
           
        }        
        else {
            
            $id_banco = $this->getId();
            $id_regiao = $this->getIdRegiao();
            
        }
        
        
        $this->db->setQuery(WHERE,"1=1",WHERE,false);

        $this->db->setQuery(WHERE,(!empty($id_banco) ? "AND id_banco = {$id_banco}" : ""),true);        

        $this->db->setQuery(WHERE,(!empty($id_regiao) ? "AND id_regiao = {$id_regiao}" : ""),true);        
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhBancosClass',E_FRAMEWORK_ERROR);
        
        return $this;        
        
    }     
    
    
    
}
