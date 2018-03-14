<?php
/**
 * Procedimento para geração de arquivo remessa
 * 
 * @file      remessa.class.php
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link      http://www.f71lagos.com/intranet/?class=financeiro/cnab240/remessa
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   ConfigClass
 * @access    public  
 * @version:  3.0.0000 - 13/01/2016 - Jacques - Versão Inicial 
 * @version:  3.0.9999 - 11/07/2016 - Jacques - Adicionado opção de enviar parâmetro na criação da classe para definir o nome do arquivo de configuração
 * @todo 
 * @example:  
 * 
 * 
 */

class ConfigClass {
    
    static  $setup;
    
    private $title;             
    private $key;
    private $value;
    
    public  $error;
    
    /**
     * Médodo que constroi a classe com um vetor contendo as configurações do framework
     * 
     * @access public
     * @method __construct
     * @param
     * 
     * @return $this
     */    
    public function __construct($value = 'setup.ini') {
        
        try {
            
            $this->createCoreClass();
            
            if(is_array($this->setup)) return $this;
                
            $file = ROOT_DIR.$value;    
            
            if(!file_exists($file)) $this->error->set(array(9,__METHOD__." -> {$file}"),E_FRAMEWORK_ERROR);

            $this->setup = parse_ini_file($file,true);
            
            //$this->error->set(_("# Assumindo arquivo de configuração [{$value}] para [{$_SERVER['HTTP_HOST']}]"),E_FRAMEWORK_NOTICE);    
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    public function __toString() {

        return (string)$this->value;

    }      

    public function isOk() {

        return (int)$this->value;

    }      
    
    
    
    
    /**
     * Médodo que cria outras classes acessórias a execução da classe 
     * 
     * @access private
     * @method createCoreClass
     * @param
     * 
     * @return $this
     */      
    private function createCoreClass() {
        
        if(!isset($this->error)){

            include($file['error']);

            $this->error = new ErrorClass();        

            if(!is_object($this->error)) $this->error->set(_("Não foi possível instânciar a classe error"),E_FRAMEWORK_ERROR);

        }
        
        return $this;
        
    }    
    
  
    /**
     * Define valores padrão na classe
     * 
     * @access public
     * @method setDefault
     * @param
     * 
     * @return $this
     */      
    public function setDefault() {
        
        return $this;
        
    }    
    
    /**
     * Define um vetor com as configurações do framework
     * 
     * @access public
     * @method title
     * @param
     * 
     * @return $this
     */       
    public function title($value){
        
        try {
            
            $this->title = $value;
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    /**
     * Define uma chave
     * 
     * @access public
     * @method key
     * @param
     * 
     * @return $this
     */      
    public function key($value){
        
        try {
            
            $this->key = $value;
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    /**
     * Retorna um valor de configuração
     * 
     * @access public
     * @method val
     * @param
     * 
     * @return $this
     */      
    
    public function val(){
        
        try {
            
            return "{$this->setup[$this->title][$this->key]}";
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    

}
