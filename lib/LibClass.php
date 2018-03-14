<?php

/*
 * PHP-DOC - Classe de bibliotecas
 *  
 * 29/02/2016
 * 
 * Classe de com biblioteca de automatiza��o do framework
 * 
 * Vers�o: 3.00.7788 - 29/02/2016 - Jacques - Vers�o Inicial
 * 
 * @Jacques
 */


class LibClass {
    
    private $value;
    
    public function __construct() 
    {
        
    }

    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Define um valor de retorno caso a refer�ncia ao objeto seja feita em um procedimento de retorno de valor
     */    
    public function __toString()
    {
        
        return (string)$this->value; 
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Define um valor padr�o de in�cio de valor para as propriedades do objeto
     */    
    public function setDefault(){
        
        $this->value = '';      

        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name - getMakeHtmlOption
     * 
     * @internal - Retorna a montagem de um option range de acordo com os par�metros que pode ser um vetor 
     *             com segundo par�metro de string definindo o �ndice dos vetores para atribui��o de valor,
     *             ou o intervalo de dois inteiros entre o valor um e dois
     */    
    public function getMakeHtmlOption($value1,$value2,$value3='<option value="&value1">&value1 - &value2</option>')
    {
        
        try {
            
            if(is_array($value1) && is_string($value2)) {
                
                $field = explode(',',$value2);

                foreach ($value1 as $key => $value) {

                    $this->value .= str_replace('&value2',$value[$field[0]],str_replace('&value1',$value[$field[1]],$value3));

                }


            }
            elseif(is_int($value1) && is_int($value2)) {

                for($i=$ini;$i<=$end;$i++){

                    $this->value .= str_replace('&value2',$i,str_replace('&value1',$i,$value3));

                }

            }
            else {
                
                $this->error->set(array(3,__METHOD__),E_FRAMEWORK_ERROR);                

            }
            
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING);                
            
        }
        
        exit($this->value);
        
        
        return $this; 
        
    }
    
    

    function array_find($string, $array)
    {

       foreach ($array as $key => $value)

       {

          if (strpos($value, $string) !== FALSE)
          {

             return $key;

             break;

          }

       }

    }    

}