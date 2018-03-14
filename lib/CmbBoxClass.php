<?php
/**
 * Classe para criar o objeto combobox 
 * 
 * @file                cmbBoxClass.php
 * @license		F71
 * @link		
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             cmbBoxClass
 * @access              public  
 * 
 * @version: 3.0.0000 - 16/01/2017 - Jacques - Versão Inicial 
 * 
 * @todo 
 * 
 */
include_once(__DIR__.'\const.php');

/**
 * Classe para criação e manipulação do combobox
 */
class CmbBoxClass {
    
    private $db;
    private $value;
    private $selected;
    
    public function __construct($value) {
        
        $this->db = $value;
        
        return $this;
        
    }
    
    /**
     * O procedimento retorna uma string em html formatando um <select> quando referênciado diretamente o objeto
     * 
     * @access public
     * @method toString()
     * @param
     * 
     * @return string
     */     
    public function __toString() {
        
        return (string)$this->value;

    }  
    
    /**
     * Método que define Define valores default da classe
     * 
     * @access public
     * @method setDefault
     * @param  
     * 
     * @return $this
     */     
    public function setDefault(){
        
        $this->value = 0;
        
        return $this;
        
    }    
    
    /**
     * Define valor para retorno no uso de m�todos encadeados
     * 
     * @access public
     * @method setValue
     * @param $value
     * 
     * @return $this
     */       
    public function setValue($value){

        $this->value = $value;

        return $this;

    }     
    
    /**
     * O procedimento retorna uma string em html formatando um <select> quando referênciado diretamente o objeto
     * 
     * @access public
     * @method getHtml
     * @param
     * 
     * @return string
     */     
    public function getHtml($i,$value,$id,$class,$name) {
        
        try {
            
            $this->setValue(0);
            
            $id_v = !empty($id) ? "id='{$id}'" : "";
            
            $class_v = !empty($class) ? "class='{$class}'" : "";
            
            $name_v = !empty($name) ? "name='{$name}'" : "";
            
            $fields = explode('+',$value);
            
            $html = "<select {$id_v} {$class_v} {$name_v}>";

            $array = is_array($this->db) ? $this->db : $this->db->getRs();
            
            foreach ($array as $key_val => $val) {
                
                $index = $val[$i];
                
                $option = '';
                
                foreach ($fields as $key_field => $field) {
                    
                    $option .= ($key_field > 0 ? " - " : "").$val[$field];
                    
                }
                
                $selected = (!empty($index) && $index == $this->getSelected()) ? "selected='selected'" : "";

                $html .= "<option value='{$index}' {$selected} >{$option}</option>";
                
            }    
            
            $html .= "</select>";
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

        }
        
        return $html;

    }       
    
    public function setSelected($value) {
        
        $this->selected = $value;
        
    }

    public function getSelected() {
        
        return $this->selected;
        
    }

        
} // Final da Class web


