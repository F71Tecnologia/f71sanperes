<?php 
/*  
 * PHP-DOC - Framework RH 
 * 
 * 04/03/2016
 * 
 * M�dulo Main de agrupamento do conjunto de classes de RhClt orientado ao FrameWork do sistema da F71 
 * 
 * Arquivos que Fazem parte do framework FwClass (Classes M�e)
 * 
 *  -> MySqlClass.php
 *  -> ErrorClass.php
 *  -> DateClass.php
 *  -> WebClass.php
 *  -> EncryptClass.php
 *  -> LibClass.php
 *  -> ConstructClass.php
 *  -> FileClass.php
 * 
 * @tutorial
 * 
 * Padronização de acesso as classes do Framework. De um modo geral esses s�o os procedimentos padr�es de acesso as classes e manipulação do framework
 *
 * fw->setDefault()                         Define valores Padr�es para inicar todas as classes do framework
 *  
 * fw->obj->setDefault()                    Define valores Padr�es para iniciar opera��es na classe
 * fw->obj->set[nome do m�todo]()           Define valores em elementos da classe, nunca uma operação de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
 * fw->obj->setCalc[nome do m�todo]()       Calcula e define valores em elementos da classe
 * fw->obj->setField[nome do campo]()       Define a inclus�o de um campo extra em um m�todo select da classe
 * 
 * fw->obj->select()                        Seleciona um conjunto de registros de uma classe que ser� consultada com getRow() 
 * fw->obj->selectExt()                     Seleciona um conjunto de registros de acordo com as condi��es definidas nesse m�todo extendido da classe
 * fw->obj->select[nome do m�todo]()        Seleciona um conjunto de registros de um m�todo de forma agrupada em conjunto de dados ou array de dados
 * 
 * fw->obj->getRow()                        Carrega os valores de registros de uma classe que foi selecionada com select() ou select[nome do m�todo]
 * fw->obj->getRowExt()                     Carrega os valores de campos extendidos criados para as propriedades da classe
 * fw->obj->get[nome do m�todo]()           Obtem o valor de um elemento da classe ou array, nunca uma operação de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
 * fw->obj->getCalc[nome do m�todo]()       Calcula e retorna um valor de resultado ou array
 * 
 * fw->obj->onUpdate()                      Gerador de evento na classe e todos registros relacionas (insert, update, delete)
 * 
 * fw->obj->chk[nome do m�todo]()           Verifica alguma coisa e retorna verdadeiro ou falso
 * 
 * fw->obj->isOk()                          Verifica o status de execu��o do �ltimo m�todo executado na classe
 *  
 * $this->db->
 * 
 * $this->error->
 * 
 * 1. Ao sefinir um elementro chave de uma consulta (ex: rh->Clt->setIdClt(5009)) todas as classes que possuem chave estrangeira relacionada a ele
 *    ir�o levar essa chave em consideração ao serem executados seus m�todos (ex: rh->Ferias->setCalcInssFgtsIrrf()).
 * 2. Evite o uso de vari�veis dentro das classes, procurando sempre usar a propriedade da classe para evitar inconsist�ncia de informação e centralização
 *    dos valores.
 * 3. O deploy dever� sempre propagar as atualiza��es de classes para todos os clientes
 * 
 * @version: 3.0.0000L - 04/03/2016 - Jacques - Vers�o Inicial
 * @version: 3.0.0000L - 17/03/2016 - Jacques - Adicionado buffer de sess�o global para includes de classes dinâmicas 
 *                                           e est�ticas do framework a fim de dar celeridade a execu��o do c�digo
 * @version: 3.0.0000L - 22/03/2016 - Jacques - Adicionado m�todo __toString e isOk para o controle da classe do framework
 * @version: 3.0.8864L - 08/04/2016 - Jacques - Adicionado rotinas de serialização das classes e controle de versionamento do framework
 * @version: 3.0.9006L - 20/04/2016 - Jacques - Implementado controle de erro mais detalhado desde concentrador de classes do framework
 * @version: 3.0.0248F - 20/10/2016 - Jacques - Adicionado informação da classe parent no evento de erro dos métodos mágicos
 * @version: 3.0.0251F - 21/10/2016 - Jacques - Implementado a opção de instanciamento de classe por alias opcional
 * 
 * @author: Jacques
 * 
 */

include_once(ROOT_LIB.'const.php');

/*
 * Está função deverá entrar na classe de bibliotecas
 */

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


class FwClass {
    
    public      $value;             
    public      $error;             // Objeto que carrega as sess�o de erros em todas as inst�ncias do Framework
    public      $date;              // Objeto que carrega as sess�o de date em todas as inst�ncias do Framework
    public      $db;                // Objeto que carrega as sess�o de conex�es com banco de dados em todas as inst�ncias do Framework
    public      $file;              // Objeto que carrega as sess�o de manipulação de arquivos no Framework
    public      $cmbBox;            // Objeto que carrega os valores retornado pela classe em um select formatado em html <select>
    public      $log;               // Objeto que carrega a classe de error
    public      $lib;               // Objeto que carrega as libs de manipulação padr�o do Framework
    public      $config;            // Objeto que carrega as configurações do Framework

    private     $action = '';       // Variável que ativa a todas as classes  
    private     $id_master = 0;     // Variável que define o cliente que est� instanciando a classe
    private     $build = 'tag_ver'; // Variável que controla o versionamento do framework
    private     $obj;
    
//    protected   $master;            // Vetor que registrar os domínios que irão trabalhar com o framework
//    protected   $table;             // Vetor que define as tabelas que irão ser instânciadas dinâmicamente
    
    
    /**
     * PHP-DOC 
     * 
     * @name __call
     * 
     * @internal - É disparado ao invocar métodos inacessíveis em um contexto de objeto.
     * 
     */   
    public function __call($name, $arguments){

        try {
            
            if (strpos($name,'upper') !== false) {

                return strtoupper($arguments[0]);

            }
            
            $trace = debug_backtrace(2);

            $class = __CLASS__;

            if(!method_exists($this,$name)) $this->error->set(array(10,"{$class}->{$name}"),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,$class),E_FRAMEWORK_WARNING,$ex);

        }

        return $this;

    } 

    /*
     * PHP-DOC 
     * 
     * @name __callStatic
     * 
     * @internal - É disparado quando invocando métodos inacessíveis em um contexto estático.
     * 
     */   
    public static function __callStatic($name, $arguments){

        try {
            
            if (strpos($name,'upper') !== false) {

                return strtoupper($arguments[0]);

            }

            $class = __CLASS__;

            if(!method_exists($this,$name)) $this->error->set(array(10,"{$class}->{$name}($arguments))"),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,array(1,$class)),E_FRAMEWORK_WARNING,$ex);


        }

        return $this;

    }

    /*
     * PHP-DOC 
     * 
     * @name __set
     * 
     * @internal - É executado ao escrever dados em propriedades inacessíveis.
     * 
     */  
    public function __set($name, $value) {
        
        try {
            
            
            if (strpos($name,'upper') !== false) {

                return strtoupper($arguments[0]);

            }
            
            $class = __CLASS__;

            if(!isset($this->table[$name])) $this->error->set(array(12,"{$class}->{$name}($value)"),E_FRAMEWORK_ERROR);

            $this->$name = $value;
        
            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,array(1,$name)),E_FRAMEWORK_WARNING,$ex);


        }

        return $this;

    }

    /*
     * PHP-DOC 
     * 
     * @name __get
     * 
     * @internal - É utilizado para ler dados de propriedades inacessíveis.
     * 
     */  
    public function __get($name)
    {
        
        try {
            
            $class = __CLASS__;

            $parent = get_parent_class($class);
            
            if(!isset($this->$name)) {
                
                $this->AddClassExt($name);
                
                if(!is_object($this->$name)) $this->error->set(array(12,"{$parent}::{$class}->{$name}"),E_FRAMEWORK_ERROR);
                
                $this->$name->setDefault();
                
            }    
                
            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,__CLASS__),E_FRAMEWORK_WARNING,$ex);


        }


//        $trace = debug_backtrace();
//
//        trigger_error(
//            'Undefined property via __get(): ' . $name .
//            ' in ' . $trace[0]['file'] .
//            ' on line ' . $trace[0]['line'],
//            E_USER_NOTICE);
        
        return $this->$name;        

    }  
    
    /**
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - M�todo construtor da classe do framework. Nele se define o dom�nio master para o modelo de neg�cio do framework.
     */
    public function __construct() {
        
        try {
            
            $this->createCoreClass();
            
            //if(!array_key_exists($this->getDominio(),$this->master)) $this->error->set("Domínio [{$this->getDominio()}] não definido para execução no framework",E_FRAMEWORK_ERROR);

            $this->setMaster($this->master[$this->getDominio()]);

            //$this->chkBuild();
        
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            die(str_replace("\n",'<br>',$this->getAllMsgCode()));
            
        }

        
    }
    
    public function __toString() {

        return (string)$this->value;

    }      

    public function isOk() {

        return (int)$this->value;

    }      
    
    public function createCoreClass() {

        include('inc-create-core.php'); 
        
        return $this;
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name setValue
     * 
     * @internal - Define valor para retorno no uso de m�todos encadeados
     */
    public function setValue($value){

        $this->value = $value;

        return $this;

    }    
    
    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Seta valores default em todos os objetos inst�nciados no framework
     */
    function setDefault() {
        
        try {
           
            foreach ($this as $key => $value)
            {

                if(is_object($value)) {

                     if(method_exists($value,'setDefault')) {

                         $this->$key->setDefault();
                         
                     }
                     else {
                         
                        $this->error->set(_("Método setDefault não declarado na classe {$key}"),E_FRAMEWORK_ERROR);
                        
                     }

                } 

            }

       } 
       catch (Exception $ex) {
           
            $this->error->set(_("Uma excessão na MACRO setDefault do framework impediu a execução do método"),E_FRAMEWORK_ERROR,$ex);
            
       } 
       
       return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcBuild
     * 
     * @internal - Obtem o número de revisão atual do framework
     * 
     * http://consello.com.br/svn/lagos/?p=2000
     * 
     * svn info http://consello.com.br/svn/lagos/ --xml
     * 
     */     
    public function chkBuild(){

        $url = $this->config->title('deploy')->key('url')->val();
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);

        $error_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $error_msg = curl_error($ch);

        $this->setBuild($response);

        return $this;
    
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setBuild
     * 
     * @internal - Serializa todas as classes do framework em uma sess�o de usu�rio
     * 
     */     
    public function setBuild($value){
        
        $this->build = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setSaveAllClass
     * 
     * @internal - Serializa todas as classes do framework em uma sess�o de usu�rio
     * 
     */     
    public function setSaveAllClass($code){
        
        $str = ''; 
        
        foreach ($this as $key => $value)
        {

            if(is_object($value)) {
                
                $_SESSION['framework']["serialize"]["{$key}"] = serialize($value);
                
            } 

        }        
        
        return $this;
        
    }    
    
    
    /*
     * PHP-DOC 
     * 
     * @name select
     * 
     * @internal - Seleciona todos os registros de classe do framework de acordo com a ordem de carregamento das classes
     * 
     */
    function select() {
        
       foreach ($this as $key => $value) 
       {
           
           if(is_object($value)) {
               
                if(method_exists($value,'select')) {
                    
                    $this->$key->select();
                    
                }
            
           } 
           
       }
       
       return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getBuild
     * 
     * @internal - Obtem o n�mero do versionamento do framework
     * 
     */     
    public function getBuild(){
        
        return $this->build;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getUri
     * 
     * @internal - Obtem a URI do framework
     * 
     */     
    public function getUri($value){
        
        return PATH_CLASS;        
            
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getRow
     * 
     * @internal - Carrega o primeiro registro do RS inst�nciado nas classes do framework
     * 
     */
    function getRow() {
        
       foreach ($this as $key => $value)
       {
           
           if(is_object($value)) {
               
                if(method_exists($value,'getRow')) {
                    
                    $this->$key->getRow();
                    
                }
            
           } 
           
       }
       
       return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setMaster
     * 
     * @internal - Identifica o dom�nio que est� acessando a classe para poder definir o id_master do cliente e permitir execu��es espec�ficas na rotinas de classe
     * 
     */
    private function setMaster($value){
        
        $this->id_master = $value;

        return $this;
        
    }
            
    
    /*
     * PHP-DOC 
     * 
     * @name setAction
     * 
     * @internal - 
     * 
     */
    public function setAction($value){
        
        $this->action = $value;
        
        return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setError
     * 
     * @internal - 
     * 
     */
    public function setError($value){
        
        $this->error = $value;
        
        return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setDb
     * 
     * @internal - 
     * 
     */
    public function setDb($value){
        
        $this->db = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getDominio
     * 
     * @internal - M�todo para obter o dom�nio atual
     * 
     */    
    public function getDominio(){
        
        return $_SERVER['HTTP_HOST'];        
        
    }
    
    public function getAction(){
        
        return $this->action;
        
    }
    
    public function getMaster(){
        
        return $this->id_master;
        
    }    
    
    /**
     * Médodo para obter a correspondência de código para o master em execução
     * 
     * @access protected
     * @method getKeyMaster
     * @param
     * 
     * @return $this
     */    
    public function getKeyMaster($value){
        
        try {
            
            $file = ROOT_DIR.'keymaster.ini';
            
            $keymaster = parse_ini_file($file,true);
            
            if(!$keymaster) $this->error->set(_("# Não foi possível carregar o arquivo {$file} de configuração da classe"),E_FRAMEWORK_ERROR);
            
            $key = $keymaster[$value][$this->getMaster()]; 
            
            if(!$key) $this->error->set(_("# Não existe chave [{$value}] associada ao master [{$this->getMaster()}]"),E_FRAMEWORK_ERROR);
                
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
        
        }

        return $key;
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name constructClass
     * 
     * @internal - M�todo construtor de classe dinâmica. Esse m�todo � respons�vel por carregar a classe m�e de todas as classes do framework.
     * 
     */    
    public function constructClass($value){
        
        try {
            
            $dominio = $this->getDominio();
            
            $uri = $this->getUri();

            $url = "http://{$dominio}/intranet/";
            
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        // Define o tipo de transferência (Padrão: 1)
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, 1);                  // Habilita o protocolo POST
            //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt'); // Imita o comportamento patrão dos navegadores: manipular cookies
            //curl_setopt ($ch, CURLOPT_POSTFIELDS, 'usuario=fulano&senha=12345'); // Define os parâmetros que serão enviados (usuário e senha por exemplo)
            //curl_setopt($ch, CURLOPT_URL, 'http://www.site.com/minha_conta.php'); // Define a URL original (do formulário de login)
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);                // Define a URL original (do formulário de login)
            
            // Define uma nova URL para ser chamada (após o login)

            $dados = array(
                "class" => "construct",
                "table" => $value, 
                "status" => 1
            );    
            
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);

            $response = curl_exec($ch); // Executa a requisição
            
            $error_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $error_msg = curl_error($ch);
            
            curl_close ($ch);
            
            if(!empty($error_msg)) $this->error->set("# [curl] {$url} retornou {$error_msg}",E_FRAMEWORK_ERROR); 
            
            switch ($error_code) {
                case 200:
                    
                    $e = json_decode($response,true);
                    
                    //if(json_last_error()) $this->error->set(_("# Erro no json de retorno de {$url}?class=construct&table={$value}"),E_FRAMEWORK_ERROR); 

                    foreach ($e as $key => $error) {

                        $this->error->set("# [curl] {$error['message']}",E_FRAMEWORK_WARNING);

                    }   

                    if(is_array($error)) $this->error->set(_("# Erros foram retornados de {$url}?class=construct&table={$value}"),E_FRAMEWORK_ERROR); 
                        
                    break;
                case 400:
                   $this->error->set(_("# [curl] O servidor não cosseguiu processar a requisição do arquivo {$url}"),E_FRAMEWORK_ERROR); 
                   break;
                case 403:
                   $this->error->set(_("# [curl] Acesso negado na execução do arquivo {$url}"),E_FRAMEWORK_ERROR); 
                   break;
                case 404:
                   $this->error->set(_("# [curl] Não foi localizado o arquivo em {$url}"),E_FRAMEWORK_ERROR); 
                   break;
                case 500: 
                   $this->error->set(_("# [curl] Erro no servidor ao executar o arquivo em {$url}"),E_FRAMEWORK_ERROR); 
                   break;
                default:
                   $this->error->set(_("# [curl] Erro não documentado na execução do arquivo {$url}"),E_FRAMEWORK_ERROR); 
                   break;
            }

            
            
    //        print_array($error);

    //        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //        $response = preg_replace('/\r?\n|\r/','<br>', $response);

            return $response;

        }
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        
    }
    
        /*
     * PHP-DOC 
     * 
     * @name constructClass
     * 
     * @internal - M�todo construtor de classe dinâmica. Esse m�todo � respons�vel por carregar a classe m�e de todas as classes do framework.
     * 
     */    
    public function constructClassCmd($value){
        
        try {
            
            $batch = 'c:\xampp\php\php.exe '.ROOT_LIB.'constructClass.php '.$value;
            
            $output = `{$batch}`;
            
            echo "<pre>$output</pre>";
            
            
            if(!empty($output))$this->error->set($output,E_FRAMEWORK_ERROR); 

            return $response;

            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            return 0;
            
        }
        
        
    }


    /*
     * PHP-DOC 
     * 
     * @name getTable
     * 
     * @internal - Obtem a tabela correspondente ao alias
     * 
     */
    function getTable($value){
        
        return $this->table[$value];
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getError
     * 
     * @internal - Verifica se houve algum erro em alguma classe do Framework
     * 
     */
    function getError() {
        
        $msg = '';
        
        foreach ($this as $key => $value)
        {

            if(is_object($value)) {

                 if(method_exists($value->error,'getAll') && !empty($this->$key->error->getAll())) {

                     $msg .= $this->$key->error->getAll(); 

                 }

            } 

        }
       
       return $msg;
       
    }
    

   /**
    * Retorna uma string com todas mensagens de todas as classes de um determinado código
    * 
    * @access public
    * @method chkInCode
    * @param
    * 
    * @return $this
    */        
    public function chkInCode($code){
        
        foreach ($this as $key => $value)
        {

            if(is_object($value)) {

                 if(method_exists($value->error,'chkInCode')) {
                     
                    if($this->$key->error->chkInCode($code)) return 1;
                     
                 }

            } 

        }        
        
        return 0;
        
    }    
   
   /**
    * Retorna uma string com todas mensagens de todas as classes de um determinado código
    * 
    * @access public
    * @method getAllMsgCode
    * @param
    * 
    * @return $this
    */      
    public function getAllMsgCode($code = 0,$break = "\n"){ 
        
        $str = $this->error->getAllMsgCode($code);

        return $str;
        
    } 
 
   /**
    * Esse método adiciona uma classe ao framework e extende a mesma a uma classe mãe dinâmica.
    * 
    * @access public
    * @method addClassExt
    * @param
    * 
    * @return $this
    */      
    public function addClassExt($class) {
        
        try {
            
            $this->setValue(0);
            
            if(!$this->addClassChildren($class)->isOk()) $this->error->set(_("# Não foi possível instânciar e adicionar a classe {$class} no framework"),E_FRAMEWORK_ERROR);
            
            $this->setValue(1);
           
        }    
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_ERROR);
            
        }
        

        return $this;
        
    }
    

    
   /**
    * Adiciona classes filhas a super classe rh que podem ser extendidas ou não
    * 
    * @access public
    * @method addClassChildren
    * @param
    * 
    * @return $this
    */      
    private function addClassChildren($class) {
        
        try {
            
            $this->setValue(0);
            
            $table = $this->getTable($class);
            
            $new_class = $this->getClassName($class).(empty($table) ? "" : "Ex");
            
            $file = ROOT_DIR.'no_cache';
            
            if(!isset($_SESSION['framework']["code"]["{$class}"]) || $_SESSION['framework']["{$class}_build"]!==$this->getBuild() || file_exists($file)) {
                
                $_SESSION['framework']["{$class}_build"] = $this->getBuild();
                
                if(!empty($table)) {

                    $code['din'] = $this->constructClass($table);

                    if(empty($code['din'])) $this->error->set(_("# Nenhum resultado retornado para a construção da classe {$new_class} a partir da tabela [{$table}]"),E_FRAMEWORK_ERROR);
                    
                }                

                $file = ROOT_CLASS.$this->getFileName($class);
                
                if(is_readable($file)) {

                    $code['file'] = file($file);

                    $total_linhas = count($code['file']); 

                    $linha_offset = array_find("class ",$code['file']);  

                    /*
                     *  Exclui as linhas que precedem a declaração da classe e a própria declaração
                     */
                    for ($i = 0; $i <= $linha_offset; $i++) {

                        unset($code['file'][$i]);                 

                    }

                    $code['file'] = array_values($code['file']);

                    $code['file'] = array_reverse($code['file']);

                    $linha_offset = array_find('}', $code['file']);


                    /*
                     *  Exclui as linhas que suscedem a chave de fechamento e as linhas seguintes da classe            
                     */
                    for ($i = 0; $i <= $linha_offset; $i++) {

                        unset($code['file'][$i]);                 

                    }

                    $code['file'] = array_reverse($code['file']);

                    /**
                     * Adiciona conteúdo do arquivo para criação da classe
                     */
                    $code['file'] = implode("",$code['file']);  
                    
                }
                else {
                    
                    //$this->error->set(_("# O arquivo {$file} da classe {$class} não existe. (Isso não é necessariamente um erro)"),E_FRAMEWORK_LOG);                    
                    
                }
                
                if(empty($code['din']) && empty($code['file'])) $this->error->set(_("# Não foi possível obter uma declaração de código para a classe {$class}"),E_FRAMEWORK_WARNING); 
                
                if(!empty($code['din']) && empty($code['file'])) $code['merge'] = "class {$this->getClassName($class)} { {$code['din']} } class {$new_class} extends {$this->getClassName($class)} {  }";
                
                if(empty($code['din']) && !empty($code['file'])) $code['merge'] = "class {$this->getClassName($class)} { {$code['file']} } class {$new_class} extends {$this->getClassName($class)} {  }";
                
                if(!empty($code['din']) && !empty($code['file'])) $code['merge'] = "class {$this->getClassName($class)} { {$code['din']} } class {$new_class} extends {$this->getClassName($class)} { {$code['file']} }";
                        
                $_SESSION['framework']["code"]["{$class}"] = $code['merge'];           
                
            }
            
            if($class=='Acoes' && 1==2) {
                echo '<pre>';
                echo $_SESSION['framework']["code"]["{$class}"];
                echo '</pre>';
            }

            $obj = $this->newClass($new_class,$_SESSION['framework']["code"]["{$class}"],$class); 
            
            if(!is_object($obj)) $this->error->set(_("# Não foi possível criar a classe {$class}"),E_FRAMEWORK_ERROR);
            
            if(!method_exists($obj,'setSuperClass')) $this->error->set(_("# Método {$class}->setSuperclass não definido para a classe"),E_FRAMEWORK_ERROR);
            
            $obj->setSuperClass($this);
            
            $this->$class = $obj;
            
            $this->setValue(1);
            
            
        } 
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_ERROR);
            
        }
        
        return $this;
        
    }

   /**
    * Esse método instância o código passado por parâmetro
    * 
    * @access private
    * @method newClass
    * @param
    * 
    * @return $this
    */     
    private function newClass($new_class,$code,$class) {

        try {
            
            $this->error = new ErrorClass();    
        
            $return = @eval($code);
                    
            if($return === false && ($error = error_get_last())) {
                
                $this->error->dump($code."\n".serialize($error));
                
                $this->error->set(_("# O método fw->newClass do Framework não conseguiu executar a instância da classe {$class} - veja arquivo /framework/dump.log: Linha {$error['line']} ({$error['message']})"),E_ERROR);
                
            }    
            
            if(isset($_SESSION['framework']["serialize"]["{$class}"])){
                
                return unserialize($_SESSION['framework']["serialize"]["{$class}"]);
                
            }
            else {
                
                if(class_exists($new_class)) {
                    
                    $obj = new $new_class();
                    
                    return $obj;

                }    
                else {
                    
                    $this->error->set("# Não foi possível instânciar a classe {$new_class}",E_FRAMEWORK_ERROR);                    
                    
                }
                
                
            }
            

        }
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_ERROR);
            
        }
        
        return 0;         

       
    }    
    
    
    
 
}

