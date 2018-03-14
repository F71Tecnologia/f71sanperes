<?php
/*
 * PHO-DOC - RhstatusClass.php
 * 
 * 17-12-2015 
 * 
 * Versão: 3.0.0000 - 17-12-2015 - Jacques - Versão Inicial
 * Versão: 3.0.6466 - 16/02/2016 - Jacques - Adicionado opção de carregar valor da propriedade código da classe a partir da classe Clt no método select
 * Versão: 3.0.6545 - 16/02/2016 - Jacques - Atualização da classe para instânciamento dinâmico
 * 
 * @jacques
 *  
 */            
            
        
class RhStatusClass {

    
    /*
     * PHP-DOC - Seleciona os registros da tabela rhstatus 
     */
    public function selectExt(){
        
        try {

            $this->db->setQuery(SELECT,",LPAD(codigo,3,'0') codigo_fmt",ADD);
            
            $this->db->setQuery(FROM,"rhstatus");

            if(is_object($this->getSuperClass()->Clt) && $this->getMagneticKey()){
                
                $codigo = $this->getSuperClass()->Clt->getStatus();

            }        
            else {

                $codigo = $this->getCodigo();
            }         

            $tipo = $this->getTipo();
            $motivo = $this->getMotivo();

            $this->db->setQuery(ORDER,
                                "
                                codigo_fmt ASC
                                ");

            $this->db->setQuery(WHERE, " 1 = 1 ");

            if(!empty($codigo)) {$this->db->setQuery(WHERE,"AND codigo IN ({$codigo})",ADD);}

            if(!empty($tipo)) {$this->db->setQuery(WHERE,"AND tipo='{$tipo}'",ADD);}
            
            if(!empty($motivo)) {$this->db->setQuery(WHERE,"AND motivo='{$motivo}'",ADD);}
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;  
        
            
    }

}