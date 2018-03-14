<?php
/**
 * Classe para controle e interface web
 * 
 * @file                webClass.php
 * @license		F71
 * @link		
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             webClass
 * @access              public  
 * 
 * @version: 3.0.0000 - 16/10/2015 - Jacques - Vers�o Inicial 
 * @version: 3.0.4506 - 24/11/2015 - Jacques - Adicionado ao CSS default os valores de html, body, nav e footer. Tamb�m alterada a refer�ncia indireta de links para direta a partir da raiz
 * @version: 3.0.5047 - 24/11/2015 - Jacques - Adicionado a tag tag_rev na inclus�o de arquivo javascript e css para obrigar a atualiza��o do arquivo pelo cache
 * @version: 3.0.5166 - 24/11/2015 - Jacques - Adicionado set e get da propriedade user para a classe interna da webClass
 * @version: 3.0.5297 - 04/01/2016 - Jacques - Adicionado m�todo get para obter o dom�nio corrente do site. 
 * @version: 3.0.5508 - 12/01/2016 - Jacques - Alterado no m�todo getAlertHtml um if que verificava por empty para strlen por motivo de falso verdadeiro
 * @version: 3.0.7364 - 04/03/2016 - Jacques - Adiciona a substitui��o de string \n por <br> no m�todo getAlertHtml
 * @version: 3.0.8131 - 04/03/2016 - Jacques - Adiciona m�todo para obter o nome do dom�nio corrente
 * @version: 3.0.8506 - 04/03/2016 - Jacques - Adicionado a verifica��o de par�metro do logado al�m do cookie para submiss�es curl
 * @version: 3.0.8710 - 29/03/2016 - Jacques - Adicionado o m�todo createCoreClass com as respectivas propriedades de classes
 * @version: 3.0.8710 - 20/04/2016 - Jacques - Adicionado controle de erro na classe
 * @version: 3.0.9307 - 04/05/2016 - Jacques - Adi��o de templates e constantes globais
 * @version: 3.0.9307 - 04/05/2016 - Jacques - Adi��o da chamada do action internamente
 * @version: 3.0.0000 - 11/12/2016 - Jacques - Extract transforma o vetor de requisi��o em um conjunto de vari�veis carregadas
 * @version: 3.0.0000 - 11/12/2016 - Jacques - Adicionado o método setMeta e opção de exeução de setMetaExt para inclusão de informações extendidas
 * @version: 3.0.0000 - 16/01/2017 - Jacques - Adicionado a variável cmbBox para uso pela classe
 * 
 * @todo 
 * 
 * Funcionalidade
 * 
 * 1. A classe webClass funciona basicamente com uma chamada a a��o como ex: $webFerias->action(); na classe web[NomeDaTela]Class extendida
 * 2. Dever� tamb�m ser setado para a classe extendida dentro do m�todo action m�todos de funcionalidade ex:                 
 *      
 *      $this->setMethod('showPage')->exeMethodExt('telaForm');        
 * 
 *      onde $this->setMethod('NomeDoMetodo') ? o m�todo que deseja executar, e exeMethodExt('telaForm') ? a chamada para a��o do m�todo com par�metros ou n�o
 * 
 * 3. Poder� tamb�m ser adicionado m�todos extendidos pr�-definido para adi��o de funcionalidade e defini��o de propriedades em:
 *      
 *      setTitle()
 *      setPageTitle()
 *      setCssExt()
 *      setJavaScriptExt()
 *      setBreadCrumb()
 * 
 *      M�todos extendidos sem pr�-definidos para serem usados em setMethod e exeMethodExt ex:
 * 
 *      telaForm()
 *      telaModalCalculaFerias()
 *      chkFaltasAbonoPecuniario()
 *      
 * 4. Os m�todos podem ser desde exibi��o de conte?do para p�ginas como execu��o de chamada ajax.
 * 
 */
include_once(__DIR__.'\const.php');


/**
 * Classe para controle da v�rias tela de lan�amento de f�rias
 */
class webClass {
    
    private $value;
    private $user;
    private $build = 'tag_ver';
    private $method = '';
    private $page_title = '';
    private $is_updating = 0;
    private $show_page = 'index';
    private $html = array(
                        'header' => array('value' => '',
                                          'meta' => '',
                                          'title' => '',
                                          'link' => '',
                                          'script' => '',
                                          'css' => ''
                                         ),
                        'body' => array('value' => '',
                                        'footer' => ''
                                        ),
                        'script' => array('value' => '')
                        );
    
    public  $error;
    public  $date;
    public  $db; 
    public  $file;
    public  $cmbBox;
    public  $log;
    public  $lib;  
    public  $config;
    
    
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
        
        try {
            
            if(!$this->createCoreClass()->isOk()) $this->error->set(array(10,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->action();
            
    
        }    
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            exit('<pre>'.$this->getAllMsgCode().'</pre>');
            
        }

        
    }   
    
    /**
     * Método executado quando o objeto e referênciado diretamente para retornar uma string
     * 
     * @access public
     * @method __toString
     * @param
     * 
     * @return string
     */     
    public function __toString() {
        
        return (string)$this->value;

    }      
    
    /**
     * Mètodo disparado ao invocar métodos inacessíveis em um contexto de objeto.
     * 
     * @access public
     * @method __call
     * @param ($name,$arguments)
     * 
     * @return $this
     */      
    public function __call($name, $arguments){

        try {
            
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

    /**
     * Método disparado quando invocando métodos inacessíveis em um contexto estático.
     * 
     * @access public
     * @method __callStatic
     * @param ($name,$arguments)
     * 
     * @return $this
     */       
    public static function __callStatic($name, $arguments){
        
        try {
            
            $class = __CLASS__;

            if(!method_exists($this,$name)) $this->error->set(array(10,"{$class}->{$name}"),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,array(1,$class)),E_FRAMEWORK_WARNING,$ex);


        }

        return $this;

    }

    /**
     * Método executado ao escrever dados em propriedades inacessíveis.
     * 
     * @access public
     * @method __set
     * @param ($name,$value)
     * 
     * @return $this
     */   
    public function __set($name, $value) {
        
        try {
            
            $class = __CLASS__;

            if(!isset($this->table[$name])) $this->error->set(array(11,"{$class}->{$name}"),E_FRAMEWORK_ERROR);

            $this->$name = $value;
        
            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

            $this->error->set(array(1,array(1,$name)),E_FRAMEWORK_WARNING,$ex);


        }

        return $this;

    }

    /**
     * Método utilizado para ler dados de propriedades inacessíveis.
     * 
     * @access public
     * @method __get
     * @param ($name)
     * 
     * @return $this
     */       
    public function __get($name)
    {
        
        try {
            
            $class = __CLASS__;
            
            if(!isset($this->$name)) $this->error->set(array(10,"{$class}->{$name}"),E_FRAMEWORK_ERROR);

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
        
        return $name;        

    } 
    
    /**
     * Método utilizado para verificar o estado apôs execução de cada método
     * 
     * @access public
     * @method isOk
     * @param 
     * 
     * @return int
     */       
    public function isOk() {

        return (int)$this->value;

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
     * Método utilizado para criar as classes de core a serem utilizadas por essa classe
     * 
     * @access public
     * @method createCoreClass
     * @param 
     * 
     * @return $this
     */       
    public function createCoreClass() {

        include_once('inc-create-core.php'); 
        
        return $this;
        
    }
    
    /**
     * Método que define o nome de complemento de exibição de página
     * 
     * @access public
     * @method setShowPage
     * @param 
     * 
     * @return $this
     */       
    public function setShowPage($value){

        $this->show_page = $value;

        return $this;

    } 
    
    /**
     * Método que define o procedimento a ser executado pela classe
     * 
     * @access protected
     * @method setMethodExt
     * @param 
     * 
     * @return $this
     */       
    protected function setMethodExt($value){
        
        $this->method = $value;
        
        return $this;
        
    }
    
    /**
     * Método que define a revisão da classe
     * 
     * @access protected
     * @method setBuild
     * @param 
     * 
     * @return $this
     */       
    protected function setBuild($value){
        
        $this->build = $value;
        
        return $this;
        
    }

    /**
     * Método que a conta de usuário ativa 
     * 
     * @access protected
     * @method setUser
     * @param 
     * 
     * @return $this
     */       
    protected function setUser($value) {
        
        $this->user = $value;
        
        return $this;
        
    }
    
    /**
     * Método que define o título do arquivo HTML
     * 
     * @access protected
     * @method setTitle
     * @param 
     * 
     * @return $this
     */       
    protected function setTitle($value){
        
        $this->html['header']['title'] = $value;
        
        return $this;
        
    }
    
    /**
     * Método que define o título da barra de navegação
     * 
     * @access protected
     * @method setPageTitle
     * @param 
     * 
     * @return $this
     */       
    protected function setPageTitle($value){
        
        $this->page_title = $value;
        
        return $this;
        
    }

    /**
     * Carrega o Css responsável pelo layout da página padrão e extendida
     * 
     * @access private
     * @method setCss
     * @param 
     * 
     * @return $this
     */       
    private function setCss($value){

        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/css.php'); 
        
        /*
         * Caso exista o m�todo extendido na extendidos da classe, ent�o executa ele
         */
        if(method_exists($this,$value)) $this->$value();

        $this->html['header']['css'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
                
                
    }
    
    /**
     * Carrega os JavaScripts padrões e extendidos na parte superior do arquivo
     * 
     * @access private
     * @method setJavaScriptHeader
     * @param 
     * 
     * @return $this
     */       
    private function setJavaScriptHeader($value){
        
        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/java_header.php'); 
        
        
        /*
         * Caso exista o m�todo extendido na extens�o da classe, ent�o executa ele
         */
        if(method_exists($this,$value)) $this->$value();

        $this->html['header']['script'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
                
    }
    
    /**
     * Carrega os JavaScripts padrões e extendidos na parte inferior do arquivo
     * 
     * @access private
     * @method setJavaScriptFooter
     * @param 
     * 
     * @return $this
     */       
    private function setJavaScriptFooter($value){
        
        ob_start(); 
        
        if(!include_once(ROOT_TEMPLATE.'web_class/java_footer.php')) die ('Não foi possível incluir '.ROOT_TEMPLATE.'web_class/java_footer.php');          
        
        /*
         * Caso exista o m�todo extendido na extens�o da classe, ent�o executa
         */
        if(method_exists($this,$value)) $this->$value();
        
        $this->html['script'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
                
    }
    
    /**
     * Método para definir as tags de template do cabeçário da página
     * 
     * @access private
     * @method setHeader
     * @param 
     * 
     * @return $this
     */       
    private function setHeader() {

        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/header.php'); 

        $this->html['header']['value'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
    }
    
    /**
     * Método para definir as informações de meta tag da página
     * 
     * @access private
     * @method setHeader
     * @param 
     * 
     * @return $this
     */       
    private function setMeta() {

        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/meta.php'); 

        $this->html['header']['meta'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
    }
    
    /**
     * Método para definir o corpo do documento
     * 
     * @access private
     * @method setBody
     * @param 
     * 
     * @return $this
     */       
    private function setBody() {

        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/body.php'); 
        
        $this->html['body']['value'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
    }
    
    /**
     * Método para defini o rodapé do documento
     * 
     * @access private
     * @method setFooter
     * @param 
     * 
     * @return $this
     */       
    private function setFooter($value){ 
        
        ob_start(); 
        
        include_once(ROOT_TEMPLATE.'web_class/footer.php'); 
        
        /**
         * Caso exista o método extendido na extensão da classe, então executa
         */
        if(method_exists($this,$value)) {
            
            $this->$value();
            
        }          

        $this->html['body']['footer'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
        
    }
    
    /**
     * Método utilizado para retornar o domínio
     * 
     * @access protected
     * @method getdomain
     * @param 
     * 
     * @return $this
     */       
    protected function getdomain(){

        return $_SERVER['HTTP_HOST'];    

    }
    
    public function getShowPage($value){

        return $this->show_page;

    } 
    
    protected function getMethodExt(){
        
        return $this->method;
        
    }

    protected function getBuild(){
        
        return $this->build;
        
    }
    
    protected function getUser($value){
        
        return isset($value) ? $this->user[$value] : $this->user;
                
    }
    
    protected function getTitle(){
        
        return $this->html['header']['title'];
        
    }
    
    protected function getPageTitle(){
        
        return $this->page_title;
        
    }
    
    private function getHeader() {
        
        return $this->html['header']['value'];

    }
    
    private function getMeta() {
        
        return $this->html['header']['meta'];

    }
    
    private function getCss() {
        
        return $this->html['header']['css'];
        
    }
            
    private function getJavaScriptHeader() {
        
        return $this->html['header']['script'];
        
    }

    private function getJavaScriptFooter() {
        
        return $this->html['script'];
        
    }
    
    private function getBody() {
        
        return $this->html['body']['value'];
        
    }
    
    protected function getFooter(){
        
        return $this->html['body']['footer'];
        
    }
    
    protected function getDominio(){
        
        return $_SERVER['HTTP_HOST'];
        
    }
    
    /** 
     * Verifica se o sistema está em atualização e aguarda a liberação atravéz de redirecionamento com mensagem e armazenamento de todos parâmetros passados para processeguir ao liberar
     * 
     * @access protected
     * @method isUpdate
     * @param
     * 
     * @return 
     */       
    protected function isUpdate(){
        
        
    }

    protected function exeMethodExt($value1, $value2, $value3, $value4, $value5){
        
        try {
            
            $this->isUpdate();

            $dominio = $_SERVER['HTTP_HOST'];

    //        if (!isset($_COOKIE['logado']) && !isset($_REQUEST['logado'])) {
    //            header("Location: http://{$dominio}/intranet/login.php?entre=true"); 
    //            exit;
    //        }        

            /**
             * Inicia o buffer de saída
             */
            ob_start(); 

            $value = $this->getMethodExt();

            if(!method_exists($this,$value)) $this->error->set(array(10,__CLASS__."->{$value}"),E_FRAMEWORK_ERROR);
                    
            $this->$value($value1, $value2, $value3, $value4, $value5);
            
            ob_end_flush();     
            
        } catch (Exception $ex) {
            
            echo $this->error->getAllMsgCode();
            
        }
        
        
        return $this;
        
    }
 
    protected function showPage(){ 
        
        include_once(ROOT_TEMPLATE.'web_class/page.php'); 
        
    }    
   
    
    /** 
     * Executa a ação assim que a classe é construída
     * 
     * @access protected
     * @method action
     * @param
     * 
     * @return 
     */       
    protected function action(){
        
        try {
            
            header ("Content-type: text/html; charset={$this->config->title('framework')->key('charset')->val()}");            

            $this->setBuild('tag_rev');
            
            foreach ($_REQUEST as $k => $v) {
                
                if(isset($$k)) $this->error->set("Não é possível declarar a variável {$k} como global pois já existe",E_FRAMEWORK_ERROR);
                
                global $$k;
                
                $$k = $v;                
                
            } 
            
            $this->setMethodExt($method); 
            
            if(method_exists($this,'actionExt')) $this->actionExt();
            
            empty($this->getMethodExt()) ? $this->showPage() : $this->exeMethodExt($value);
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            echo $this->error->getAllMsgCode();
            
        }
        
    }    
    
    /*
     * PHP-DOC - Verifica se existe mensagem passada por par?metro e caso exista retorna o mesmo formatado com mensagem do tipo pre-definido
     */
    protected function getAlertHtml($msg,$type){
        
        if(strlen($msg)){
            
            $msg = str_replace("\n", "<br>", $msg);

            return  "
                    <div class='alert alert-{$type}' role='alert'>
                      $msg
                    </div>
                    ";
            
        } 
        else {
            
            return '';
            
        }
        
        
    }    

} // Final da Class web


