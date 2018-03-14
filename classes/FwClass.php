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
 * Padroniza��o de acesso as classes do Framework. De um modo geral esses s�o os procedimentos padr�es de acesso as classes e manipula��o do framework
 *
 * fw->setDefault()                         Define valores Padr�es para inicar todas as classes do framework
 *  
 * fw->obj->setDefault()                    Define valores Padr�es para iniciar opera��es na classe
 * fw->obj->set[nome do m�todo]()           Define valores em elementos da classe, nunca uma opera��o de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
 * fw->obj->setCalc[nome do m�todo]()       Calcula e define valores em elementos da classe
 * fw->obj->setField[nome do campo]()       Define a inclus�o de um campo extra em um m�todo select da classe
 * 
 * fw->obj->select()                        Seleciona um conjunto de registros de uma classe que ser� consultada com getRow() 
 * fw->obj->selectExt()                     Seleciona um conjunto de registros de acordo com as condi��es definidas nesse m�todo extendido da classe
 * fw->obj->select[nome do m�todo]()        Seleciona um conjunto de registros de um m�todo de forma agrupada em conjunto de dados ou array de dados
 * 
 * fw->obj->getRow()                        Carrega os valores de registros de uma classe que foi selecionada com select() ou select[nome do m�todo]
 * fw->obj->getRowExt()                     Carrega os valores de campos extendidos criados para as propriedades da classe
 * fw->obj->get[nome do m�todo]()           Obtem o valor de um elemento da classe ou array, nunca uma opera��o de calculos ou procedimentos. Usar apenas para obter valores prim�rios.
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
 *    ir�o levar essa chave em considera��o ao serem executados seus m�todos (ex: rh->Ferias->setCalcInssFgtsIrrf()).
 * 2. Evite o uso de vari�veis dentro das classes, procurando sempre usar a propriedade da classe para evitar inconsist�ncia de informa��o e centraliza��o
 *    dos valores.
 * 3. O deploy dever� sempre propagar as atualiza��es de classes para todos os clientes
 * 
 * Vers�o: 3.0.0000 - 04/03/2016 - Jacques - Vers�o Inicial
 * Vers�o: 3.0.0000 - 17/03/2016 - Jacques - Adicionado buffer de sess�o global para includes de classes din�micas 
 *                                           e est�ticas do framework a fim de dar celeridade a execu��o do c�digo
 * Vers�o: 3.0.0000 - 22/03/2016 - Jacques - Adicionado m�todo __toString e isOk para o controle da classe do framework
 * Vers�o: 3.0.8864 - 08/04/2016 - Jacques - Adicionado rotinas de serializa��o das classes e controle de versionamento do framework
 * Vers�o: 3.0.9006 - 20/04/2016 - Jacques - Implementado controle de erro mais detalhado desde concentrador de classes do framework
 * Vers�o: 3.0.9065 - 29/04/2016 - Jacques - Adicionado carga de arquivo de configura��o com acesso por m�todos
 *  
 * @author: Jacques
 * 
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
    public      $file;              // Objeto que carrega as sess�o de manipula��o de arquivos no Framework
    public      $lib;               // Objeto que carrega as libs de manipula��o padr�o do Framework

    private     $action = '';       // Vari�vel que ativa a todas as classes  
    private     $id_master = 0;     // Vari�vel que define o cliente que est� instanciando a classe
    private     $build = 'tag_ver'; // Vari�vel que controla o versionamento do framework
    private     $setup;             // Vari�vel que armazeza informa��es de configura��o do framework
    private     $title;             
    private     $key;
    private     $field;




    protected   $uri = '';
    protected   $path_root = ''; 



//    public function __call($method,$args)
//    {
//
//        echo '__call';
//        
//        foreach($this as $ext)
//        {
//            if(property_exists($ext,$varname))
//            return $ext->$varname;
//        }
//
//    }    
//    
//    public function __get($varname)
//    {
//        
//        echo '__get';
//        
//        foreach($this as $ext)
//        {
//            if(method_exists($ext,$method))
//            return call_user_method_array($method,$ext,$args);
//        }
//        throw new Exception("Este Metodo {$method} nao existe!");
//
//    }    
    
    /*
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - M�todo construtor da classe do framework. Nele se define o dom�nio master para o modelo de neg�cio do framework.
     */
    public function __construct() {
        
        $this->createCoreClass();
        
        $this->setMaster($this->master[$this->getDominio()]);
        
        $this->setBuild(isset($_REQUEST['build']) ? $_REQUEST['build'] : 'tag_ver');

        
    }
    
    public function __toString() {

        return (string)$this->value;

    }      

    public function isOk() {

        return (int)$this->value;

    }      
    
    public function createCoreClass() {

        if(!isset($this->error)){

            include_once('ErrorClass.php');

            $this->error = new ErrorClass();        

        }

        if(!isset($this->db)){

            include_once('MySqlClass.php');

            $this->db = new MySqlClass();

        }

        if(!isset($this->date)){

            include_once('DateClass.php');

            $this->date = new DateClass();

        }

        if(!isset($this->file)){

            include_once('FileClass.php');

            $this->file = new FileClass();

        }

        if(!isset($this->lib)){

            include_once('LibClass.php');

            $this->lib = new LibClass();

        }
        
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
                         
                        $this->error->set("M�todo setDefault n�o declarado na classe {$key}",E_FRAMEWORK_ERROR);
                        
                     }

                } 

            }

       } 
       catch (Exception $ex) {
           
            $this->error->set('Uma excess�o na MACRO setDefault do framework impediu a execu��o do m�todo',E_FRAMEWORK_ERROR,$ex);
            
       } 
       
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
     * @name getRow
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
    
    /*
     * PHP-DOC 
     * 
     * @name getPathRoot
     * 
     * @internal - Obtem a localiza��o raiz do arquivo do framework
     * 
     */     
    public function getPathRoot(){
        
        return $_SERVER['DOCUMENT_ROOT'];
        
    }
    
    public function getAction(){
        
        return $this->action;
        
    }
    
    public function getMaster(){
        
        return $this->id_master;
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getKeyMaster
     * 
     * @internal - M�todo para obter a correspond�ncia de c�digo para o master em execu��o
     * 
     */    
    public function getKeyMaster($value){
        
        try {
            
            $file = 'keymaster.ini';
            
            $keymaster = parse_ini_file($file,true);
            
            if(!$keymaster) $this->error->set("N�o foi poss�vel carregar o arquivo {$file} de configura��o da classe",E_FRAMEWORK_ERROR);
            
            $key = $keymaster[$value][$this->getMaster()]; 
            
            if(!$key) $this->error->set("N�o existe chave [{$value}] associada ao master [{$this->getMaster()}]",E_FRAMEWORK_ERROR);
                
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
     * @internal - M�todo construtor de classe din�mica. Esse m�todo � respons�vel por carregar a classe m�e de todas as classes do framework.
     * 
     */    
    public function constructClass($value){
        
        try {
            
            $dominio = $this->getDominio();

            $uri = $this->getUriExt();

            $url = "http://{$dominio}{$uri}constructClass.php";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);

            $dados = array(
                "table" => $value, 
                "status" => 1
            );    

            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);

            $response = curl_exec($ch);

            $error_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            $error_msg = curl_error($ch);
            
            if(!empty($error_msg)) $this->error->set("{$url} retornou {$error_msg}",E_FRAMEWORK_ERROR); 
            
            switch ($error_code) {
                case 200:
                    break;
                case 400:
                   $this->error->set("O servidor n�o cosseguiu processar a requisi��o do arquivo {$url}",E_FRAMEWORK_ERROR); 
                   break;
                case 403:
                   $this->error->set("Acesso negado na execu��o do arquivo {$url}",E_FRAMEWORK_ERROR); 
                   break;
                case 404:
                   $this->error->set("N�o foi localizado o arquivo em {$url}",E_FRAMEWORK_ERROR); 
                   break;
                case 500: 
                   $this->error->set("Erro no servidor ao executar o arquivo em {$url}",E_FRAMEWORK_ERROR); 
                   break;
                default:
                   $this->error->set("Erro n�o documentado na execu��o do arquivo {$url}",E_FRAMEWORK_ERROR); 
                   break;
            }

            
            
    //        print_array($error);

    //        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //        $response = preg_replace('/\r?\n|\r/','<br>', $response);

            curl_close ($ch);

            return $response;


        } catch (Exception $ex) {

            $this->error->set("A n�o possibilidade de carregar o arquivo {$file} gerou uma excess�o no framework",E_FRAMEWORK_WARNING,$ex); 
            
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
     * @name getRow
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
    
    /*
     * PHP-DOC 
     * 
     * @name chkInCode
     * 
     * @internal - Verifica se foi gerado um determinado c�digo de erro em todas as classes do framework
     * 
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

    /*
     * PHP-DOC 
     * 
     * @name getAllMsgCode
     * 
     * @internal - Retorna uma string com todas mensagens de todas as classes de um determinado c�digo
     * 
     */     
    public function getAllMsgCode($code){ 
        
        $str = $this->error->getAllMsgCode($code);
        
        foreach ($this as $key => $value)
        {

            if(is_object($value)) {

                 if(method_exists($value->error,'getAllMsgCode')) {
                     
                    $str .= $this->$key->error->getAllMsgCode($code);
                     
                 }

            } 

        }        
        
        return $str;
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name addClassExt
     * 
     * @internal - Esse m�todo adiciona uma classe ao framework e extende a mesma a uma classe m�e din�mica.
     * 
     */    
    public function addClassExt($class) {
        
        try {
            
            $obj = $this->addClassChildren($class);
            
            if(!is_object($obj)) $this->error->set("N�o foi poss�vel criar a classe {$class}",E_FRAMEWORK_ERROR);

            $this->$class = $obj;

            $this->$class->setSuperClass($this);

        }    
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            exit('<pre>'.$this->getAllMsgCode().'</pre>');
            
        }
        

        return $this->$class;
        
    }
    

    
    /*
     * PHP-DOC 
     * 
     * @name getRow
     * 
     * @internal - Adiciona classes filhas a super classe rh que podem ser extendidas ou n�o
     * 
     */
    public function addClassChildren($class) {
        
        try {
            
            /*
             * Verifica se existe os m�todos extendidos para execu��o do m�todo
             */
            if(!method_exists($this,'getUriExt')) $this->error->set("M�todo extendido getUriExt inexistente! Favor Adiciona-lo em sua classe extendida de FwClass.php",E_FRAMEWORK_ERROR);
            
            if(!method_exists($this,'getFileNameExt')) $this->error->set("M�todo extendido getFileNameExt inexistente! Favor Adiciona-lo em sua classe extendida de FwClass.php",E_FRAMEWORK_ERROR);
            
            $this->createCoreClass();

            $table = $this->getTable($class);

            $new_class = (empty($table) ? $this->getFileNameExt($class) : $this->getFileNameExt($class)).'Ext';
            
            $file = dirname(__FILE__).'/no_cache';    
            
            if(!isset($_SESSION['framework']["code"]["{$class}"]) || $_SESSION['framework']["{$class}_build"]!==$this->getBuild() || file_exists($file) || 1==1) {
                
                $_SESSION['framework']["{$class}_build"] = $this->getBuild();
                
                $code1 = $this->constructClass($table);
                
                if(empty($code1)) $this->error->set("Nenhum resultado retornado para a constru��o da classe {$new_class} a partir de {$table}",E_FRAMEWORK_ERROR);

                $file = dirname(__FILE__).'/'.$this->getFileNameExt($class).'.php';
                
                if(!is_readable($file)) $this->error->set("A n�o possibilidade de carregar o arquivo {$file} gerou uma excess�o no framework",E_FRAMEWORK_ERROR);

                $code2 = file($file);

                $total_linhas = count($code2); 

                $linha_offset = array_find("class ",$code2);  

                /*
                 *  Exclui as linhas que precedem a declara��o da classe e a pr�pria declara��o
                 */
                for ($i = 0; $i <= $linha_offset; $i++) {

                    unset($code2[$i]);                 

                }

                $code2 = array_values($code2);

                $code2 = array_reverse($code2);

                $linha_offset = array_find('}', $code2);


                /*
                 *  Exclui as linhas que suscedem a chave de fechamento e as linhas seguintes da classe            
                 */
                for ($i = 0; $i <= $linha_offset; $i++) {

                    unset($code2[$i]);                 

                }

                $code2 = array_reverse($code2);

                /*
                 * Adiciona conte�do do arquivo para cria��o da classe
                 */
                $code2 = implode("",$code2);  

                $code = empty($table) ? "class $new_class  { {$code2} }" : "class Rh{$class}Class { {$code1} } class {$new_class} extends Rh{$class}Class { {$code2} }";
                
                $_SESSION['framework']["code"]["{$class}"] = $code;           
                
            }
            
            if($class=='Clt' && 1==2) {
                echo '<pre>';
                echo $_SESSION['framework']["code"]["{$class}"];
                echo '</pre>';
            }
            
            return $this->newClass($new_class,$_SESSION['framework']["code"]["{$class}"],$class); 

        } 
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            return 0;
            
        }
        
    }

    /*
     * PHP-DOC 
     * 
     * @name newClass
     * 
     * @internal - Esse m�todo inst�ncia o c�digo passado por par�metro
     * 
     */
    private function newClass($new_class,$code,$class) {

        try {
            
            $this->error = new ErrorClass();    
        
//            if($nome_nova_classe=='RhCltClassEx') echo "<pre>{$code}</pre>"; 
            
            eval($code);
            
            if(error_get_last() <= E_FRAMEWORK_ERROR) $this->error->set("O m�todo fw->newClass do Framework n�o conseguiu executar a inst�ncia da classe {$class}",E_FRAMEWORK_ERROR);
            
            if(isset($_SESSION['framework']["serialize"]["{$class}"])){
                
                $obj = unserialize($_SESSION['framework']["serialize"]["{$class}"]);
                
            }
            else {
                
                $obj = new $new_class();
                
            }
            
            return $obj;         


        }
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            return 0;
            
        }
       
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name config
     * 
     * @internal - Define um vetor com as configura��es do framework
     */       
    public function config(){
        
        try {
            
            if(is_array($this->setup)) return $this;
            
            $file = dirname(__FILE__).'/setup.ini';    
            
            if(!file_exists($file)) $this->error->set(array(9,__METHOD__." -> {$file}"),E_FRAMEWORK_ERROR);
            
            $this->setup = parse_ini_file($file,true);
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    /*
     * PHP-DOC 
     * 
     * @name title
     * 
     * @internal - Define um vetor com as configura��es do framework
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
    
    /*
     * PHP-DOC 
     * 
     * @name key
     * 
     * @internal - Define uma chave
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
    
    /*
     * PHP-DOC 
     * 
     * @name val
     * 
     * @internal - Retorna um valor de configura��o
     */       
    public function val(){
        
        try {
            
            return $this->setup[$this->title][$this->key];
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
            
    }
    
    
}

