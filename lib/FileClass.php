<?php 
/*
 * PHO-DOC - FileClass.php
 * 
 * Classe de defini��o de tipos de documentos para upload
 * 
 * 26-02-2016
 *
 * @name FileClass 
 * @package FileClass
 * @access public 
 *  
 * @version 
 *
 * Vers�o: 3.0.5055 - 26/02/2016 - Jacques - Vers�o Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 
          
            
        
class FileClass {
    
    private $collection;
    
    private $file = array(
                         'domain' => '',
                         'path' => '',
                         'name' => '',
                         'ext' => '',
                         'size' => 0,
                         'create' => '',
                         'update' => ''
                         );
    
    public function setDefault(){
        
        
    }
    
    public function setDomain($value) {
        
        $this->file['domain'] = $value;
        
        return $this;
        
    }
    
    public function setPath($value) {
        
        $this->file['path'] = $value;
        
        return $this;
        
    }
    
    public function setName($value) {
        
        $this->file['name'] = $value;
        
        return $this;
        
    }
    
    public function setExt($value) {
        
        $this->file['ext'] = $value;
        
        return $this;
        
    }
    
    public function getLocation(){
        
        return $this->getDomain().$this->getPath().$this->getPath().$this->getExt();
        
    }
    
    public function exists(){
        
        return file_exists($this->getLocation());
        
    }
    
    public function unlink(){
        
        return unlink($this->getLocation());
        
    }
    

}
