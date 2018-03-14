<?php

class SqlInjection{
   
    
   /**
    * MÉTODO PARA LIMPAR CARACTERES ESPECIAIS DE STRING RETORNANDO APENAS LETRAS
    * @param type $string
    * @return type
    */ 
   public function getString($string){
      $string_tratada =  preg_replace('/[^[:alpha:]_]/', '', $string);
      return $string_tratada; 
   }
   
   /**
    * MÉTODO PARA LIMPAR CARACTERES ESPECIAIS DE STRING RETORNANDO APENAS
    * LETRAS E NÚMERO
    * @param type $string
    * @return type
    */ 
   public function getStringAndInteger($string){
      $string_tratada =  preg_replace('/[^[:alnum:]_]/', '', $string);
      return $string_tratada; 
   }
   
   /**
    * ESCAPA ASPAS
    * @param type $string
    * @return type
    */
   public function getEscapeString($string){
      $sring_tratada = addslashes($string); 
      return $sring_tratada;
   }
   
   /**
    * MÉTODO PARA RETORNA TAG HTML CONVERTIDAS EM CODIGOS
    * @param type $string
    * @return type
    */
   public function getStringNoHtml($string){
       $string_tratada = htmlspecialchars($string);
       return $string_tratada;
   }
   
   /**
    * MÉTODO QUE REMOVE HTML E PHP DA STRING
    * @param type $string
    * @return type
    */
   public function getStringNoHtmlPhp($string){
       $string_tratada = strip_tags($string);
       return $string_tratada;
   }
   
   /**
    * 
    * @param type $int
    * @return type
    */
   public function validaVarInt($int){
       $valor_tratado = filter_var($int, FILTER_VALIDATE_INT);
       return $valor_tratado;
   } 
   
   /**
    * 
    * @param type $email
    * @return type
    */
   public function validaVarEmail($email){
       $valor_tratado = filter_var($int, FILTER_VALIDATE_EMAIL);
       return $valor_tratado;
   } 
   
   /**
    * 
    * @param type $url
    * @return type
    */
   public function validaVarUrl($url){
       $valor_tratado = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
       return $valor_tratado;
   }
   
   /**
    * 
    * @param type $value
    * @param type $method
    * @return type
    */
   public function filterInteger($value, $method = "POST"){
       $type = "";
       if(isset($value) && !empty($value))
           $type = 'INPUT_POST';
       else
           $type = 'INPUT_GET';
       
       $valor_tratado = filter_input($type, $value, 'FILTER_VALIDATE_INT');
       return $valor_tratado;
   }
   
   /**
    * MÉTODO QUE VERIFICA A EXISTENCIA DE SINTEXE SQL
    * E FAZ OUTROS TRATAMENTOS
    * @param type $string
    */
   public function antiInjection($string){
        //REMOVE PALAVRAS QUE CONTENHAM SINTEXE SQL
        $string = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$string);
        //REMOVE ESPAÇOS VAZIOS NO INÍCIO E NO FINAL DO ARQUIVO
        $string = trim($string);
        //REMOVE TAG HTML E PHP
        $string = $this->getStringNoHtmlPhp($string);
        //ADICIONA BARRA IVERTIDAS EM UMAM STRING
        $string = $this->getEscapeString($string);
        
        return $string;
   }
   
   
   
}