<?php   

/**
 * 
 * Classe de manipulação de email
 *  
 * @file                MailClass.php
 * @license		F71
 * @link		
 * @copyright           2017 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             MailClass
 * @access              public  
 * 
 * @copyright www.f71.com.br 
 * 
 */

class MailClass { 
    
    private $value;
    
    public function __construct() {
        
        
    }
    
    public function __toString() {
        
        return (string)$this->value;
        
    }
    
    public function setDefault($value){
        
        $this->value = '';
        
        return $this;
        
    }
    
    public function setValue($value){
        
        $this->value = $value;
        
        return $this;
        
    }
    
    public function set(){
        
        return $this;
        
    }
    

    public function get(){
        
        return $this;

    }
    
     
    /**
     * Define todos os parâmetros para envio de email de suport
     * 
     * @access public
     * @method setMailSuport
     * @param  
     * 
     * @return $this;
     */     
     public function setMailSuport($value){
         
        $break = "\n";
        
        $cols = 50;
         
        $srv = $this->getServerSnapShotArray();
        
        $this->mail['subtitle'] .= " - {$srv['login']}";
        
        $this->mail['message'] = str_repeat("-=", $cols).$break
                                .'Data/Hora..: '.date('d-m-Y H:i:s ').$break
                                .'Domínio....: '.vsprintf("%-20s", substr($srv['s_name'],0,20)).$break
                                .'Usuário....: '.vsprintf("%04d", $srv['logado']).' - '.vsprintf("%-15s",substr($srv['login'],0,15)).' '.$break
                                .'User Agent.: '.$srv['u_agent'].$break
                                .'PHP/Versão.: '.$srv['php_version'].$break
                                .'URL........: '.'http://'.$srv['s_name'].$srv['s_url'].$break
                                .str_repeat("-=", $cols).$break
                                .$break
                                .$value.$break;
        
        return $this;
         
     }
     
    /**
     * Informa ao suporte sobre lote de erros
     * 
     * @access public
     * @method sendMamilSuport
     * @param  
     * 
     * @return $this
     */     
     public function sendMailSuport(){
         
        try {
             
            $this->mail['header']  = "MIME-Version: 1.1\n"
                                   . "Content-type: text/plain; charset=UTF-8\n"
                                   . "From: {$this->mail['to']}\n"
                                   . "Return-Path: {$this->mail['to']}\n";            
            
            if(mail("{$this->mail['to']}", "{$this->mail['subtitle']}", "{$this->mail['message']}", "{$this->mail['header']}", "-f{$this->mail['to']}")){
            
                $this->set("# Notificação de erro enviada para {$this->mail['to']}",E_FRAMEWORK_LOG);            
            
            }
            else{
                
                $this->set("# Não foi possível enviar email de notificação de erro para {$this->mail['to']}",E_FRAMEWORK_LOG);                
                
            }    
             
        } catch (Exception $ex) {
             
             $this->set("# Falhou geral da notificação por email",E_FRAMEWORK_WARNING,$ex);

        }
        
        return $this;
         
        
    }
     
    /**
     * Método que registra um log de erros do sistema
     * 
     * @access public
     * @method log
     * @param  
     * 
     * @return 
     */     
     public function log($msg){
         
        $file = ROOT_DIR.'error.log';
         
        if (!$handle = fopen($file, "a+"))  exit("Erro abrindo arquivo ({$file})");
        
        chmod($file, 0777);        
        
        if (!fwrite($handle, $msg)) exit("Erro escrevendo no arquivo ({$filename2})");

        fclose($handle);         
        
     }
     
     
    /**
     * Método que salva um dump de arquivo 
     * 
     * @access public
     * @method dump
     * @param  string
     * 
     * @return 
     */     
     public function dump($code){
         
        $file = ROOT_DIR.'dump.log';
         
        if (!$handle = fopen($file, "w"))  exit("Erro abrindo arquivo ({$file})");
        
        chmod($file, 0777);        
        
        if (!fwrite($handle, $code)) exit("Erro escrevendo no arquivo ({$filename2})");

        fclose($handle);         
        
     }     
     
}


