<?php

/**
 * Classe para manipulação do arquivo de log
 * 
 * @file                LogClass.php
 * @license		F71
 * @link		
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             LogClass
 * @access              public  
 * 
 * @version: 3.0.0000 - 06/01/2017 - Jacques - Versão Inicial 
 * 
 */

class LogClass {
    
    private $log;
    private $file = 'error.log';
    
    /**
     * Método executado na construção da classe
     * 
     * @access public
     * @method __construct
     * @param
     * 
     * @return 
     */     
    public function __construct() {

        $log_channel = vsprintf("%-20s", substr($_SERVER['SERVER_NAME'],0,20))." ".vsprintf("%04d", isset($_COOKIE['logado']) ? $_COOKIE['logado'] : '0000')." ".vsprintf("%-15s",substr($_SESSION['login'],0,15))." ";

        /**
         * O formato da data padrão é "Y-m-d H:i:s"
         */
        $dateFormat = "d-m-Y H:i:s";
        
        /**
         * O formato de saída padrão é "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
         */
        $output = "%datetime% %channel% %message%\n";

        /**
         * Crio um linha de formatação do log
         */
        $formatter = new Monolog\Formatter\LineFormatter($output, $dateFormat);        
        
        /**
         * Crio alguns Handles
         */
        $stream    = new Monolog\Handler\StreamHandler(ROOT_DIR.$this->file, Monolog\Logger::DEBUG);
        $firephp   = new Monolog\Handler\FirePHPHandler();        
        
        $stream->setFormatter($formatter);
        
        /**
         * Crio o log principal do aplicativo
         */
        $this->log['main'] = new Monolog\Logger($log_channel);
        
        $this->log['main']->pushHandler($stream);
        $this->log['main']->pushHandler($firephp);
        
        /**
         * Crio um log para a segurança com um canal diferente
         */
        /**
         * Crio o log principal do aplicativo
         */
        $this->log['security'] = new Monolog\Logger($log_channel);
        
        $this->log['security']->pushHandler($stream);
        $this->log['security']->pushHandler($firephp);
        
        
    }    
    
    /**
     * Método para definir os valores padrões da classe
     * 
     * @access public
     * @method setDefault
     * @param
     * 
     * @return 
     */     
    public function setDefault() {
        
        
    }    
    
    /**
     * Registra um log de info
     * 
     * @access public
     * @method error
     * @param
     * 
     * @return 
     */     
    public function info($value) {

        $this->log['main']->info($value);
        
    }    
    
    
    /**
     * Registra um log de warning
     * 
     * @access public
     * @method error
     * @param
     * 
     * @return 
     */     
    public function warning($value) {
        
        $this->log['main']->warning($value);
        
    }    
    
    /**
     * Registra um log de error
     * 
     * @access public
     * @method error
     * @param
     * 
     * @return 
     */     
    public function error($value) {
        
        $this->log['main']->error($value);
        
    }    
    
    
    
    /**
     * Método que lê o arquivo de log
     * 
     * @access public
     * @method read
     * @param  
     * 
     * @return 
     */     
     public function read(){
         
        $this->obj = new ArrayObject(file(ROOT_DIR.$this->file));        
        
        return $this;
        
     }    
     
    /**
     * Método que lê o arquivo de log
     * 
     * @access public
     * @method read
     * @param  
     * 
     * @return 
     */     
     public function show(){
         
         try {
             
            $this->obj->asort();             
             
            while ($this->obj->valid()) {
                
                echo $this->obj->current() . "<br/>\n";
                
                $this->obj->next();
                
            }
            
             
         } catch (Exception $ex) {

         }
        
        return $this;
        
     }    
     
     public function showLast(){
         
         try {
             
            $count = $this->obj->count();
            
            $index = $count - 20;
            

            while($index <= $count) {
                
              $buffer = sprintf("<li>%s</li>",$this->obj->offsetGet([++$index]));
              
            }     
             
            echo sprintf("<html><ul>%s</ul></html>",$buffer);
             
         } catch (Exception $ex) {

         }
        
        return $this;
        
     }    
     
    
}
