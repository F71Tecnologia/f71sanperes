<?php   

/**
 *  
 * @version: 3.0.0001L - 04/05/2015 - Jacques - Versão Inicial
 * @version: 3.0.0001L - 13/10/2015 - Jacques - Implementado a possibilidade de uso de m�todos encadeados e uso simplificado do set e get
 * @version: 3.0.5251L - 30/12/2015 - Jacques - Adicionado m�todo de controle de erro na carga das classes do framework 
 * @version: 3.0.5408L - 07/01/2016 - Jacques - Implementação de pilha de erros com todos os parâmetros e uso dele atrav�s de throw new Exception
 * @version: 3.0.6637L - 18/01/2016 - Jacques - Adicionado os métodos chkInCode e getAllMsgCode para verificação de erros e obtenção dos erros lançados na classe ErrorClass
 * @version: 3.0.7765L - 18/01/2016 - Jacques - Adicionado padronização de mensagens de erro
 * @version: 3.0.7765L - 01/06/2016 - Jacques - Adicinado método getAllMsgCodeJson para retorno de lista de error no formato Json.
 * @version: 3.0.0186F - 18/07/2016 - Jacques - Comentado o código: header("Content-Type: text/html; charset=UTF-8",true); Pois estava gerando problema de codificação no uso do framework no sistema legado quando 
 *                                              utilizado de forma híbrida. Importante verificar as consequências desse código comentado.
 * @version: 3.0.0233F - 30/09/2016 - Jacques - Adicionado o método para gerar log dos erros do framework com nova opção de uso de E_FRAMEWORK_LOG para título apenas de registro em log de alguma operação  
 * @version: 3.0.0237F - 07/10/2016 - Jacques - Adicionado a identificação do SERVER_NAME no log de error
 * @version: 3.0.0237F - 04/01/2017 - Jacques - Adicionado value na mensagem default do vetor index 8
 * @version: 3.0.0000F - 13/02/2017 - Jacques - Adicionado o registro no log do nome de usuário
 * 
 * @todo
 * ATEN��O: 1. O sistema dever� sempre interromper o avanão encadeado dos processos quando encontrar erros <= code = 2 (E_ERROR, E_WARNING)
 * 
 * E_LOG               = 0      (ERROR-FWK - Não é código de erro e sim instrução para registro em log)
 * E_ERROR             = 1      (ERROR-PHP - Erros fatais em tempo de execução. Estes indicam erros que não podem ser recuperados, como problemas de aloca��o de mem�ria. A execução do script � interrompida)
 * E_WARNING           = 2      (ERROR-PHP - Avisos em tempo de execução (erros não fatais). A execução do script não � interrompida)
 * E_FRAMEWORK_ERROR   = 3      (ERROR-FWK - Erro fatal que viola alguma l�gica do framework)
 * E_PARSE             = 4      (ERROR-PHP - Erro em tempo de compila��o. Erros gerados pelo interpretador)
 * E_FRAMEWORK_WARNING = 5      (ERROR-FWK - Avisos em tempo de execução (erros não fatais). A execução do script do framework não � interrompida mas exige atenção)
 * E_FRAMEWORK_NOTICE  = 6      (ERROR-FWK - Not�cia em tempo de execução. Indica que o script do framework encontrou alguma coisa que pode indicar um erro, mas que tamb�m possa acontecer durante a execução normal do script e gerar consequencias no processamento padr�o)
 * E_NOTICE            = 8      (ERROR-PHP - Not�cia em tempo de execução. Indica que o script encontrou alguma coisa que pode indicar um erro, mas que tamb�m possa acontecer durante a execução normal do script)
 * E_CORE_ERROR        = 16     (ERROR-PHP - Erro fatal que acontece durante a inicializa��o do PHP. Este � parecido com E_ERROR, exceto que � gerado pelo n�cleo do PHP)
 * E_ALL               = 30719  (ERROR-PHP - Todos erros e avisos, como suportado, exceto de n�vel E_STRICT 
 * 
 * M�todos do objeto de error
 * 
 * $obj->getMessage();
 * $obj->getFile();
 * $obj->getLine();
 * $obj->getCode();
 * $obj->getPrevious();
 * $obj->getTrace();
 * $obj->getTraceAsString();
 * 
 * Ordem de chamada dos eventos e m�todos em uma excess�o
 * 
 * shutdown_handler
 * FrameWorkException
 * FrameWorkException->__construct
 * exception_handler
 *  
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 * 
 */
//header("Content-Type: text/html; charset=UTF-8",true); 

$cookie = $_COOKIE["error_reporting"];

eval('$error_reporting = '.$cookie.';');

error_reporting($error_reporting);

assert_options(ASSERT_ACTIVE, 1); 
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_BAIL, 0);
assert_options(ASSERT_QUIET_EVAL, 0);
assert_options(ASSERT_CALLBACK, 'assert_callcack'); // Fun��o do usu�rio para chamar quando uma afirma��o falhar

set_error_handler('error_handler');                 // Fun��o de usu�rio que controla erros irrecuper�veis
set_exception_handler('exception_handler');         // Fun��o de usu�rio que controla excess�es

register_shutdown_function('shutdown_handler');     // Primeira fun��o a ser executada ao final de um erro ou excess�o



/*
 * PHP-DOC 
 * 
 * @name assert_callcack
 * 
 * @public -  Fun��o do usu�rio para chamar quando uma afirma��o falhar
 * 
 */
function assert_callcack($file, $line, $message) {
    
    throw new FrameWorkException($message, E_FRAMEWORK_ERROR, $file, $line);
    
}

/*
 * PHP-DOC 
 * 
 * @name shutdown_handler
 * 
 * @public -  Primeira função a ser executada ao final de um erro ou excessão
 * 
 */
function shutdown_handler() {
    
    if (null !== $error = error_get_last()) {

        $err_no   = $error["type"];
        $err_file = $error["file"];
        $err_line = $error["line"];
        $err_str  = $error["message"];
        
    }            
    else {
        
        $err_file = "unknown file";
        $err_str  = "shutdown";
        $err_no   = E_CORE_ERROR;
        $err_line = 0;
        
    }

    //throw new FrameWorkException($err_str, $err_no, $err_file, $err_line);
        
}



/*
 * PHP-DOC 
 * 
 * @name error_handler
 * 
 * @public -  Função que controla erros irrecupar�veis
 * 
 */
function error_handler($code, $error, $file, $line, $vars) {
    
    //throw new FrameWorkException($error, $code, $file, $line);
   
}

/*
 * PHP-DOC 
 * 
 * @name exception_handler
 * 
 * @public -  Função que controla excessões e manipula a classe Exception
 * 
 */
function exception_handler(FrameWorkException $e) {
    
    if ((error_reporting() & $e->getCode()) || $e->getCode() == E_ERROR || $e->getCode() == E_FRAMEWORK_ERROR) {
        
        echo 'exception_handler = '.$e->getMessage();

    }
    
    
}


/*
 * PHP-DOC 
 * 
 * @name FrameWorkException
 * 
 * @public -  Classe de exception extendida para controle de erros
 * 
 */
class FrameWorkException extends Exception {
    
    public function __construct($message = null, $code = null, $file = null, $line = null) {
        
        if ($code === null) {
            
            parent::__construct($message);
            
        } else {
            
            parent::__construct($message, $code);
            
        }
        
        if ($file !== null) {
            
            $this->file = $file;
            
        }
        
        if ($line !== null) {
            
            $this->line = $line;
            
        }
        
    }
    
}

class FileException extends FrameWorkException {};

class SystemException extends FrameWorkException {};

class IOError extends FrameWorkException {}; 

class AccessControl extends FrameWorkException {}; 


class ErrorClass { 
    
    private static  $tipo_erro = array(
                               1  => 'E_ERROR', 
                               2  => 'E_WARNING', 
                               4  => 'E_PARSE', 
                               8  => 'E_NOTICE', 
                               3  => 'E_FRAMEWORK_ERROR',
                               5  => 'E_FRAMEWORK_WARNING',
                               9  => 'E_FRAMEWORK_NOTICE',
                               16 => 'E_CORE_ERROR',
                            );

    
    
    private static  $array_default = array('error' => 
                                            array(
                                                'code' => 0,
                                                'message' => 'Unknown exception',
                                                'class' => '',
                                                'path_class' => '',
                                                'file' => '',
                                                'line' => 0,
                                                'string' => ''
                                            )    
                            );
    
    private $mail = array('header' => '',
                          'to' => 'jacques@f71.com.br',
                          'from' => 'postmaster@f71.com.br',
                          'subtitle' => 'LOG de erros do Framework',
                          'message' => ''
                          );
    
    private $msg_erro = array();
    
    private $value;
    
    private $log;
    
    public function __construct() {
        
        global $error_array;
        
        $this->msg_erro[0] = _("# Mensagem de erro");
        $this->msg_erro[1] = _("# Uma exceção em <value> impediu a finalização do processo");
        $this->msg_erro[2] = _("# Houve um erro ao selecionar os registros da classe <value>");
        $this->msg_erro[3] = _("# Tipo do parâmetro do método <value> inválido");
        $this->msg_erro[4] = _("# Pelo menos uma propriedade precisa ser definida no método <value>");
        $this->msg_erro[5] = _("# Não existe nenhum parâmetro definido para execução do método <value>");
        $this->msg_erro[6] = _("# O método <value> possui dependência de classes sem as quais sua execução não é possível");
        $this->msg_erro[7] = _("# Um vetor impressindível para execução do método <value> está fazio ou sua chave não tem correspondência");
        $this->msg_erro[8] = _("# Não existe uma chave associada ao keymaster da interface em <value>");
        $this->msg_erro[9] = _("# Não foi possível carregar o arquivo pelo método <value>");
        $this->msg_erro[10]= _("# O método <value> não existe");
        $this->msg_erro[11]= _("# A classe <value> não possui esse método declarado");
        $this->msg_erro[12]= _("# A classe <value> não está definida na tabela de instânciamento dinâmico");
        
        if(!include_once(ROOT_LIB.'LogClass.php')) die(_("Não foi possível incluir LogClass.php a partir de ErrorClass.php"));         

        $this->log = new LogClass();

        if(!is_object($this->log)) $this->error->set(_("Não foi possível instânciar a classe log"),E_FRAMEWORK_ERROR);

        
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
    
    public function set($value, $code = E_ERROR, FrameWorkException $previous = null){
        
        global $error_array;
        
        if(is_array($value)){
            
            $class = $value[1];

            $value = str_replace('<value>', $value[1],$this->getMsgDefault($value[0]));
            
        }
        else {
            
            $class = '';
            
        }
        
        if(count($error_array)==0) $this->log->info(vsprintf("%02d", $code)." SESSION  - # {$this->getServerSnapShotString()}");
        
        $error_array[] = array(
                                'code' => $code,
                                'message' => $value,
                                'class' => $class,
                                'path_class' => '',
                                'file' => '',
                                'line' => 0,
                                'string' => ''
                                );
        
        if($code==E_ERROR || $code==E_FRAMEWORK_ERROR){
            
            $arr = debug_backtrace(2);
            
            $trace = array_reverse($arr);
            
            foreach ($trace as $key => $error) {
                
                $stack = sprintf("%03d", $key);
                
                $this->log->info(vsprintf("%02d", 0)." INFO     - # [tracert:{$stack}] {$error['class']}->{$error['function']}() call in {$error['file']}:{$error['line']}");
                
            }
            
        }
        
        switch ($code) {
            case E_ERROR:
            case E_FRAMEWORK_ERROR:
                $this->log->error(vsprintf("%02d", $code)." ERROR    - {$value}");
                break;
            case E_WARNING:
            case E_FRAMEWORK_WARNING:
                $this->log->warning(vsprintf("%02d", $code)." WARNING  - {$value}");
                break;
            case E_NOTICE:
            case E_FRAMEWORK_NOTICE:
                $this->log->warning(vsprintf("%02d", $code)." NOTICE   - {$value}");
                break;
            default:
                $this->log->info(vsprintf("%02d", $code)." INFO     - {$value}");
                break;
        }
        
        /**
         * Nem todos os erros na class ErrorClass geram uma exceção, portanto faz-se necessário criar uma pilha de erros
         */
        
        if($code === E_ERROR || $code === E_FRAMEWORK_ERROR) throw new FrameWorkException($this->value, $code, $previous);
        
        return $this;
        
    }
    
    public function setError($value, $code = E_ERROR, FrameWorkException $previous = null) {

        $this->set($value,$code,$previous);
        
        return $this;
        
    }
    
    public function getServerSnapShotArray(){
        
        $srv['s_name'] = $_SERVER['SERVER_NAME'];
        
        $srv['u_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $srv['q_string'] = $_SERVER['QUERY_STRING'];
        
        $srv['s_url'] = $_SERVER['REQUEST_URI'];
        
        $srv['php_version'] = phpversion();

        $srv['session'] = session_id();

        $srv['login'] = $_SESSION['login'];

        $srv['logado'] = $_COOKIE['logado'];
        
        $args = '';

        foreach ($_REQUEST  as $key => $value) {

            $args.= "{$key}={$value};"; 

        }
        
        $srv['args'] = $args;
        
        return $srv;
        
    }    
    
    public function getServerSnapShotString(){
        
        $srv = $this->getServerSnapShotArray();

        return "[{$srv['session']}] {$srv['u_agent']} PHP/{$srv['php_version']} [{$srv['args']}]";
        
    }


    public function get(){
        
        return $this;

    }
    
    public function getMsgDefault($value){
        
        $this->setValue($value[0] < count($value) ? $this->msg_erro[$value] : $this->msg_erro[0]);
                
        return $this;
        
    }
    
   /**
    * Percorre todo o vetor de erros concatenando para retornar uma string. Caso haja par�metro filtra apenas erros contidos nele
    * 
    * @access public
    * @method getAll
    * @param 
    * 
    * @return $this
    */       
    public function getAll(){
        
        $this->get();
        
        return $this;

    }
     
    public function getError(){

        $this->get();
        
        return $this;

     }
     
    public function getArr(){
         
        return exception_handler();
         
    }

   /**
    * Verifica se foi gerado um determinado c�digo de erro
    * 
    * @access public
    * @method chkInCode
    * @param int
    * 
    * @return $this
    */       
    public function chkInCode($code){
        
        global $error_array;
        
        foreach($error_array as $key => $value) {        
            
            if($value['code']==$code) return 1;
            
        }
        
        return 0;
        
    }

   /**
    * Retorna uma string com todas mensagens de um determinado código
    * 30/06/2016 - Adicionado tags de marcação de erro que não deve ser apresentado para o usuário final
    * 
    * @access protected
    * @method getAllMsgCode
    * @param int
    * 
    * @return $this
    */     
    public function getAllMsgCode($code, $break = "\n"){
        
        global $error_array;
        
        $sendMail = 0;
        
        $error_reporting = $_COOKIE["error_reporting"];
        
        $this->value = ''; 
        
        foreach($error_array as $key => $value) {                
            
            $message .= $value['message'].$break;
            
            if(($value['code']==$code && $code > 0) || strpos($value['message'],'#')===false || ($error_reporting=='E_ALL')){
                
                $this->value .= $value['message'].$break;
                
            }
            
            $sendMail = ($sendMail || $value['code']==E_ERROR || $value['code']==E_FRAMEWORK_ERROR);
            
        }
        
        if($sendMail) $this->setMailSuport($message)->sendMailSuport();
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getAllMsgCodeJson
     * 
     * @internal - Retorna um JSON com todos os erros reportados na classe error ou definido como error code
     * 
     */     
    public function getAllMsgCodeJson($code){
        
        global $error_array;
        
        $sendMail = 0;
        
        $error_reporting = $_COOKIE["error_reporting"];
        
        foreach($error_array as $key => $value) {                
            
            if(($value['code']==$code && $code > 0) || strpos($value['message'],'#')===false || ($error_reporting=='E_ALL')){
                
                $return[] = array('code' => $value['code'],'message' => utf8_encode($value['message']));
                
            }
            
        }        
        
        return json_encode($return);
        
    }
    
     
    /**
     * Captura o erro em uma instrução Eval executada para uma classe do FrameWork
     * 
     * @access public
     * @method getCodeLastError
     * @param  
     * 
     * @return array
     */     
     public function getCodeLastError(){
         
        $error = error_get_last();

        return $error['type'];
        
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
//     public function log($msg){
//         
//        $file = ROOT_DIR.'error.log';
//         
//        if (!$handle = fopen($file, "a+"))  exit("Erro abrindo arquivo ({$file})");
//        
//        chmod($file, 0777);        
//        
//        if (!fwrite($handle, $msg)) exit("Erro escrevendo no arquivo ({$filename2})");
//
//        fclose($handle);         
//        
//     }
     
     
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


