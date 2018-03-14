<?php  

/* 
 * PHP-DOC - ErrorClass.php
 * 
 * 04/05/2015
 * 
 * @package ErrorClass
 * @access public   
 *  
 * @version
 *  
 * Versão: 3.0.0001 - 13/10/2015 - Jacques - Implementado a possibilidade de uso de métodos encadeados e uso simplificado do set e get
 * Versão: 3.0.5251 - 30/12/2015 - Jacques - Adicionado método de controle de erro na carga das classes do framework 
 * Versão: 3.0.5408 - 07/01/2016 - Jacques - Implementação de pilha de erros com todos os parâmetros e uso dele atravês de throw new Exception
 * Versão: 3.0.6637 - 18/01/2016 - Jacques - Adicionado os métodos chkInCode e getAllMsgCode para verificação de erros e obtenção dos erros lançados na classe ErrorClass
 * Versão: 3.0.7765 - 18/01/2016 - Jacques - Adicionado padronização de mensagens de erro
 *  
 * @todo
 * ATENÇÃO: 1. O sistema deverá sempre interromper o avanço encadeado dos processos quando encontrar erros <= code = 2 (E_ERROR, E_WARNING)
 * 
 * E_ERROR             = 1      (ERROR-PHP - Erros fatais em tempo de execução. Estes indicam erros que não podem ser recuperados, como problemas de alocação de memória. A execução do script é interrompida)
 * E_WARNING           = 2      (ERROR-PHP - Avisos em tempo de execução (erros não fatais). A execução do script não é interrompida)
 * E_FRAMEWORK_ERROR   = 3      (ERROR-FWK - Erro fatal que viola alguma lógica do framework)
 * E_PARSE             = 4      (ERROR-PHP - Erro em tempo de compilação. Erros gerados pelo interpretador)
 * E_FRAMEWORK_WARNING = 5      (ERROR-FWK - Avisos em tempo de execução (erros não fatais). A execução do script do framework não é interrompida mas exige atenção)
 * E_FRAMEWORK_NOTICE  = 6      (ERROR-FWK - Notícia em tempo de execução. Indica que o script do framework encontrou alguma coisa que pode indicar um erro, mas que também possa acontecer durante a execução normal do script e gerar consequencias no processamento padrão)
 * E_NOTICE            = 8      (ERROR-PHP - Notícia em tempo de execução. Indica que o script encontrou alguma coisa que pode indicar um erro, mas que também possa acontecer durante a execução normal do script)
 * E_CORE_ERROR        = 16     (ERROR-PHP - Erro fatal que acontece durante a inicialização do PHP. Este é parecido com E_ERROR, exceto que é gerado pelo núcleo do PHP)
 * E_ALL               = 30719  (ERROR-PHP - Todos erros e avisos, como suportado, exceto de nível E_STRICT 
 * 
 * Métodos do objeto de error
 * 
 * $obj->getMessage();
 * $obj->getFile();
 * $obj->getLine();
 * $obj->getCode();
 * $obj->getPrevious();
 * $obj->getTrace();
 * $obj->getTraceAsString();
 * 
 * Ordem de chamada dos eventos e métodos em uma excessão
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
const E_FRAMEWORK_ERROR = 3;
const E_FRAMEWORK_WARNING = 5;
const E_FRAMEWORK_NOTICE = 6;

$cookie = $_COOKIE["error_reporting"];

eval('$error_reporting = '.$cookie.';');

error_reporting($error_reporting);

assert_options(ASSERT_ACTIVE, 1); 
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_BAIL, 0);
assert_options(ASSERT_QUIET_EVAL, 0);
assert_options(ASSERT_CALLBACK, 'assert_callcack'); // Função do usuário para chamar quando uma afirmação falhar

set_error_handler('handler_error');                 // Função de usuário que controla erros irrecuperáveis
set_exception_handler('handler_exception');         // Função de usuário que controla excessões

register_shutdown_function('exception_shutdown');     // Primeira função a ser executada ao final de um erro ou excessão


/*
 * PHP-DOC 
 * 
 * @name assert_callcack
 * 
 * @public -  Função do usuário para chamar quando uma afirmação falhar
 * 
 */
function assert_callcack($file, $line, $message) {
    
    $error = new ErrorClass;

    $error->set('exception_shutdown = '.$message, E_FRAMEWORK_ERROR, $file, $line);
    
}

/*
 * PHP-DOC 
 * 
 * @name shutdown_handler
 * 
 * @public -  Primeira função a ser executada ao final de um erro ou excessão
 * 
 */
function exception_shutdown() {
    
//    if (null !== $error_get_last = error_get_last()) {
//        
//        $error = new ErrorClass;
//
//        $error->set('exception_shutdown = '.$error_get_last['message'], $error_get_last['type']);
//        
//    }
    
}


/*
 * PHP-DOC 
 * 
 * @name error_handler
 * 
 * @public -  Função que controla erros irrecuparáveis
 * 
 */
function handler_error($code, $message, $file, $line, $vars) {
    
    if ((error_reporting() & $code) || $code == E_ERROR || $code == E_FRAMEWORK_ERROR) {

        $error = new ErrorClass;

        $error->set('handler_error = '.$message, $code);

    }
   
}

/*
 * PHP-DOC 
 * 
 * @name exception_handler
 * 
 * @public -  Função que controla excessões e manipula a classe Exception
 * 
 */
function handler_exception(Exception $e) {

    if ((error_reporting() & $e->getCode()) || $e->getCode() == E_ERROR || $e->getCode() == E_FRAMEWORK_ERROR) {
        
        $error = new ErrorClass;
        
        $error->set('handler_exception = '.$e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine());

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
    
    public function __construct($message = null, $code = null, $previous = null) {
        
        if ($code === null) {
            
            parent::__construct($message);
            
        } else {
            
            parent::__construct($message, $code, $previous);
            
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
    
    private $tipo_erro = array(
                               1  => 'E_ERROR', 
                               2  => 'E_WARNING', 
                               4  => 'E_PARSE', 
                               8  => 'E_NOTICE', 
                               3  => 'E_FRAMEWORK_ERROR',
                               5  => 'E_FRAMEWORK_WARNING',
                               9  => 'E_FRAMEWORK_NOTICE',
                               16 => 'E_CORE_ERROR',
                            );

    
    
    private $array_default = array('error' => 
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
    
    private $msg_erro = array(
                               0  => 'Mensagem de erro padrão não definida',
                               1  => 'Uma exceção em <class> impediu a finalização do processo', 
                               2  => 'Houve um erro ao selecionar os registros da classe <class>', 
                               3  => 'Tipo do parâmetro do método <class> inválido',
                               4  => 'Pelo menos uma propriedade precisa ser definida no método <class>',
                               5  => 'Não existe nenhum parámetro definido para execução do método <class>',
                               6  => 'O método <class> possui dependência de classes sem as quais sua execução não é possível',
                               7  => 'Um vetor impressíndível para execução do método <class> está fazio',
                               8  => 'Não existe uma chave associada ao keymaster da interface',
                               9  => 'Não foi possível carregar o arquivo pelo método <class>',
                               10 => 'Não foi possível criar o core de classes pelo método <class>. Método declarado errado ou classes com versões desatualizadas',
                               11 => 'Não existe o método <class>. (Verifique a sintaxe ou se a versão das classes dependentes estão atualizadas)'
                            );
    
    private $array = array(); 
    
    private $value;
    
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
    
    public function set($value, $code = E_ERROR, Exception $previous = null){
        
        if(is_array($value)){
            
            $class = $value[1];

            $value = str_replace('<class>', $value[1],$this->getMsgDefault($value[0]));
            
        }
        else {
            
            $class = '';
            
        }
        
        $this->array['error'][] = array(
                                    'code' => $code,
                                    'message' => $value,
                                    'class' => $class,
                                    'path_class' => '',
                                    'file' => '',
                                    'line' => 0,
                                    'string' => ''
                                );
        
        $this->value .= "{$value}";
        
        /*
         * Nem todos os erros na class ErrorClass geram uma exceção, portanto faz-se necessário criar uma pilha de erros
         */
        
        if($code === E_ERROR || $code === E_FRAMEWORK_ERROR) throw new FrameWorkException($this->value, $code, $previous);
        
        return $this;
        
    }
    
    public function setError($value, $code = E_ERROR, Exception $previous = null) {

        $this->set($value,$code,$previous);
        
        return $this;
        
    }


    public function get(){
        
        return $this;

    }
    
    public function getMsgDefault($value){
        
        $this->setValue($value[0] < count($value) ? $this->msg_erro[$value] : $this->msg_erro[0]);
                
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getAll
     * 
     * @internal - Percorre todo o vetor de erros concatenando para retornar uma string. Caso haja parámetro filtra apenas erros contidos nele
     * 
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

    /*
     * PHP-DOC 
     * 
     * @name chkInCode
     * 
     * @internal - Verifica se foi gerado um determinado código de erro
     * 
     */     
    public function chkInCode($code){
        
        foreach ($this->array['error'] as $key => $value) {
            
            if($value['code']==$code) return 1;
            
        }
        
        return 0;
        
    }

    /*
     * PHP-DOC 
     * 
     * @name getAllMsgCode
     * 
     * @internal - Retorna uma string com todas mensagens de um determinado código
     * 
     */     
    public function getAllMsgCode($code){
        
        $this->value = ''; 
        
        foreach ($this->array['error'] as $key => $value) {
            
            if($value['code']==$code || empty($code)){
                
                $this->value .= $value['message']."\n";
                
            }
            
        }
        
        return $this;
        
    }
     
    /*
     * PHP-DOC 
     * 
     * @name getCodeLastError
     * 
     * @internal - Captura o erro em uma instrução Eval executada para uma classe do FrameWork
     * 
     */     
     public function getCodeLastError(){
         
        $error = error_get_last();

        return $error['type'];
        
     }
     
     
}
