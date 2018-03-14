<?php
/*
 * PHO-DOC - RhFolhaProcClass.php
 * 
 * 01-12-2015 
 * 
 * Vers�o: 3.0.0000 - 01-12-2015 - Jacques - Vers�o Inicial
 * Vers�o: 3.0.7847 - 14/03/2015 - Jacques - Atualiza��o da classe para inst�nciamento din�mico
 * 
 * @jacques
 *  
 */            
            
        
class RhFolhaProcClass { 
    
    public function selectExt(){
        
        try {

            $this->db->setQuery(ORDER,
                                "
                                ano DESC,
                                mes DESC,
                                nome ASC 
                                ");
        
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;  
        
            
    }

}