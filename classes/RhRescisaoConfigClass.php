<?php
/*
 * PHO-DOC - RhRescisaoConfigClass.php
 * 
 * Classe para manipulação da tabela rh_recisao_config orientada a objetos
 *
 * 16-03-2016
 * 
 * @package RhRescisaoConfigClass
 * @access public   
 * 
 * @version
 *  
 * Versão: 3.0.4385 - 24/11/2015 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */
        
class RhRescisaoConfigClass { 
    
    /*
     * PHP-DOC 
     * 
     * @name select
     * 
     * @internal - Método de seleção padrão de registros da classe
     * 
     */    
    public function selectExt(){
        
        try {
            
            if(is_object($this->getSuperClass()->Clt) && $this->getMagneticKey()){

                $tipo = $this->getSuperClass()->Clt->getStatus();

            }        
            else {

                $tipo = $this->getTipo();

            } 

            if(!empty($id_config)) {$this->db->setQuery(WHERE,"AND id_config IN ({$id_config})",ADD);}

            if(!empty($tipo)) {$this->db->setQuery(WHERE,"AND tipo IN ({$tipo})",ADD);}
            
            if(!empty($ano)) {$this->db->setQuery(WHERE,"AND ano IN ({$ano})",ADD);}
            
            $this->setValue(1);
            
        } 
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        
        return $this; 
         
            
    }

    /*
     * PHP-DOC 
     * 
     * @name getRowExt
     * 
     * @internal - Método extendido da classe dinâmica para carregar propriedades extendidaas do método select
     *             como um campo calculado.
     * 
     */    
    public function getRowExt(){
        
        return $this;
        
    }
    
}