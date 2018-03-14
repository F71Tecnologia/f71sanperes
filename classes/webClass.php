<?php
/*
 * PHP-DOC
 * 
 * 16-10-2015
 * 
 * Classe para controle e interface web
 * 
 * Funcionalidade
 * 
 * 1. A classe webClass funciona basicamente com uma chamada a ação como ex: $webFerias->action(); na classe web[NomeDaTela]Class extendida
 * 2. Deverá também ser setado para a classe extendida dentro do método action métodos de funcionalidade ex:                 
 *      
 *      $this->setMethod('showPage')->exeMethodExt('telaForm');        
 * 
 *      onde $this->setMethod('NomeDoMetodo') ? o método que deseja executar, e exeMethodExt('telaForm') ? a chamada para ação do método com parámetros ou não
 * 
 * 3. Poderé também ser adicionado métodos extendidos pré-definido para adição de funcionalidade e definição de propriedades em:
 *      
 *      setTitle()
 *      setPageTitle()
 *      setCssExt()
 *      setJavaScriptExt()
 *      setBreadCrumb()
 * 
 *      Métodos extendidos sem pré-definidos para serem usados em setMethod e exeMethodExt ex:
 * 
 *      telaForm()
 *      telaModalCalculaFerias()
 *      chkFaltasAbonoPecuniario()
 *      
 * 4. Os métodos podem ser desde exibição de conte?do para páginas como execução de chamada ajax.
 * 
 * Versão: 3.0.0000 - 16/10/2015 - Jacques - Versão Inicial
 * Versão: 3.0.4506 - 24/11/2015 - Jacques - Adicionado ao CSS default os valores de html, body, nav e footer. Também alterada a referência indireta de links para direta a partir da raiz
 * Versão: 3.0.5047 - 24/11/2015 - Jacques - Adicionado a tag 231 na inclusão de arquivo javascript e css para obrigar a atualização do arquivo pelo cache
 * Versão: 3.0.5166 - 24/11/2015 - Jacques - Adicionado set e get da propriedade user para a classe interna da webClass
 * Versão: 3.0.5297 - 04/01/2016 - Jacques - Adicionado método get para obter o domínio corrente do site. 
 * Versão: 3.0.5508 - 12/01/2016 - Jacques - Alterado no método getAlertHtml um if que verificava por empty para strlen por motivo de falso verdadeiro
 * Versão: 3.0.7364 - 04/03/2016 - Jacques - Adiciona a substituição de string \n por <br> no método getAlertHtml
 * Versão: 3.0.8131 - 04/03/2016 - Jacques - Adiciona método para obter o nome do domínio corrente
 * Versão: 3.0.8506 - 04/03/2016 - Jacques - Adicionado a verificação de parámetro do logado além do cookie para submissões curl
 * Versão: 3.0.8710 - 29/03/2016 - Jacques - Adicionado o método createCoreClass com as respectivas propriedades de classes
 * Versão: 3.0.8710 - 20/04/2016 - Jacques - Adicionado controle de erro na classe
 * 
 * @Jacques
 */


/*
 * PHP-DOC - Classe para controle da várias tela de lançamento de férias
 */

class webClass {
    
    private $value;
    private $user;
    private $build = 'tag_ver';
    private $method = '';
    private $page_title = '';
    private $is_updating = 0;
    private $html = array(
                        'header' => array('value' => '',
                                          'meta' => '',
                                          'title' => '',
                                          'link' => '',
                                          'script' => '',
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
    public  $lib;    
    
    public function __construct() {
        
        try {

            if(!$this->createCoreClass()->isOk()) $this->error->set(array(10,__METHOD__),E_FRAMEWORK_ERROR);
            
    
        }    
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            exit('<pre>'.$this->getAllMsgCode().'</pre>');
            
        }

        
    }   
    
    public function __toString() {

        return (string)$this->value;

    }      

    public function isOk() {

        return (int)$this->value;

    }     
    
    /*
     * PHP-DOC 
     * 
     * @name setValue
     * 
     * @internal - Define valor para retorno no uso de métodos encadeados
     */
    public function setValue($value){

        $this->value = $value;

        return $this;

    } 
    
    
   /*
    * PHP-DOC 
    * 
    * @name createCoreClass
    * 
    * @internal - Método para criar e instânciar as classes mães do core
    * 
    */     
    public function createCoreClass() {
        
        try {
            
            $path = $_SERVER['DOCUMENT_ROOT'].PATH_CLASS;
            
            $file['error'] = $path.'ErrorClass.php'; 
            $file['mysql'] = $path.'MySqlClass.php'; 
            $file['date'] = $path.'DateClass.php'; 
            $file['file'] = $path.'FileClass.php'; 
            $file['lib'] = $path.'LibClass.php'; 
            
            foreach ($file as $key => $value) {
                
                if(!file_exists($file[$key])) exit("Não foi possível carregar o arquivo do core {$file[$key]}");
                
            }
            
            

            if(!isset($this->error)){

                include_once($file['error']);

                $this->error = new ErrorClass();        

            }

            if(!isset($this->db)){

                include_once($file['mysql']);

                $this->db = new MySqlClass();

            }

            if(!isset($this->date)){

                include_once($file['date']);

                $this->date = new DateClass();

            }

            if(!isset($this->file)){

                include_once($file['file']);

                $this->file = new FileClass();

            }

            if(!isset($this->lib)){

                include_once($file['lib']);

                $this->lib = new LibClass();

            }
            
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->error->set("Houve um erro no método update da classe classe RhCltClass",E_FRAMEWORK_WARNING);
            
            $this->setValue(0);
            
        }

        return $this;


    }    
    
    /*
     * PHP-DOC - Define o método a ser executado na classe
     */
    protected function setMethodExt($value){
        
        $this->method = $value;
        
        return $this;
        
    }
    
    protected function setBuild($value){
        
        $this->build = $value;
        
        return $this;
        
    }


    protected function setUser($value) {
        
        $this->user = $value;
        
    }
    
    
    protected function setTitle($value){
        
        $this->html['header']['title'] = $value;
        
        return $this;
        
    }
    
    protected function setPageTitle($value){
        
        $this->page_title = $value;
        
        return $this;
        
    }

    /*
     * PHP-DOC - Carrega os Css responsáveis pelo layout da página padrão e extendida
     */
    private function setCss($value){

        ob_start(); 
        
        ?>

        <!-- Bootstrap -->
        <link href="/intranet/resources/css/bootstrap.css?231" rel="stylesheet" media="all">
        <link href="/intranet/resources/css/bootstrap-theme.css?231" rel="stylesheet" media="all">
        <link href="/intranet/resources/css/bootstrap-note.css?231" rel="stylesheet" media="screen">
        <link href="/intranet/resources/css/main.css?231" rel="stylesheet" media="all">
        <link href="/intranet/resources/css/font-awesome.css?231" rel="stylesheet" media="all">
        <link href="/intranet/resources/css/bootstrap-dialog.min.css?231" rel="stylesheet" type="text/css">
        <link href="/intranet/css/validationEngine.jquery.css?231" rel="stylesheet" type="text/css" >
        <link href="/intranet/css/progress.css?231" rel="stylesheet" type="text/css">

        <!-- Estilo para Widget de calendário no padrão Boostrap -->
        <link href="/intranet/js/bootstrap-datepicker-1.4.0-dist/css/bootstrap-datepicker.css?231" rel="stylesheet" media="all">
        <link href="/intranet/js/bootstrap-datepicker-1.4.0-dist/css/bootstrap-datepicker.min.css?231" rel="stylesheet" media="all">


        <link href="/intranet/js/jquery.ui.theme.css?231" rel="stylesheet" type="text/css" />
        <link href="/intranet/js/highslide.css?231" rel="stylesheet" type="text/css" />
        
        <style>
            html, body {
                height:100%;
            }        

            nav {
                position: relative;
                min-height: 50px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                margin-right: auto;
                margin-left: auto;                      
            }                    
            
            footer {
                position: relative;
                min-height: 50px;
                margin-bottom: 20px;
                border: 1px solid transparent;
                width: 1170px;
                padding-right: 0px;
                padding-left: 29px;
                margin-right: auto;
                margin-left: auto;                        
            }                    
        </style>
        
                
        <?php
        
        /*
         * Caso exista o método extendido na extendidos da classe, então executa ele
         */
        if(method_exists($this,$value)) {
            
            $this->$value();
            
        }         

        $this->html['header']['css'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
                
                
    }
    
    /*
     * PHP-DOC - Carrega os JavaScripts padrões e extendidos
     */    
    private function setJavaScriptHeader($value){
        
        ob_start(); 
        
        ?>
                

        <?php
        
        /*
         * Caso exista o método extendido na extensão da classe, então executa ele
         */
        if(method_exists($this,$value)) {
            
            $this->$value();
            
        }         

        $this->html['header']['script'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
                
    }
    
    /*
     * PHP-DOC - Carrega os JavaScripts padrões e extendidos
     */    
    private function setJavaScriptFooter($value){
        
        ob_start(); 
        
        ?>
                
        <!-- JavaScriptFooter -->

        <!-- Código JQuery -->
        <script type="text/javascript" src="/intranet/js/jquery-1.3.2.js?231"></script>
        <script type="text/javascript" src="/intranet/js/jquery-1.11.1.min.js?231"></script>
        <script type="text/javascript" src="/intranet/js/jquery-ui-1.9.2.custom.min.js?231"></script>


        <!-- Código Bootstrap -->
        <script type="text/javascript" src="/intranet/resources/js/bootstrap.min.js?231"></script>
        <script type="text/javascript" src="/intranet/resources/js/tooltip.js?231"></script>
        <script type="text/javascript" src="/intranet/resources/js/bootstrap-dialog.min.js?231"></script>


        <!-- Código de Widget para exibição de calendário -->
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?231"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?231"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?231"></script>

        <!-- Código de validação -->
        <script type="text/javascript" src="/intranet/js/jquery.validationEngine_2.6.2.js?231" ></script>
        <script type="text/javascript" src="/intranet/js/jquery.validationEngine-pt_BR-2.6.js?231"></script>
        
        <!-- Código variádos -->
        <script type="text/javascript" src="/intranet/js/global.js?231"></script>
        <script type="text/javascript" src="/intranet/js/ramon.js?231"></script>
        <script type="text/javascript" src="/intranet/js/highslide-with-html.js?231"></script>
        <script type="text/javascript" src="/intranet/resources/js/main.js?231"></script>

        
        <?php
        
        /*
         * Caso exista o método extendido na extensão da classe, então executa
         */
        if(method_exists($this,$value)) {
            
            $this->$value();
            
        }         

        $this->html['script'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
                
    }
    
    private function setHeader() {

        ob_start(); 
        
        ?>
        
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title><?=$this->getTitle();?></title>
            <link href="/intranet/favicon.png" rel="shortcut icon" />

            <?=$this->setCss('setCssExt')->getCss();?>
            <?=$this->setJavaScriptHeader('setJavaScriptExtHeader')->getJavaScriptHeader();?>

        </head>        
        
        <?php

        $this->html['header']['value'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
    }
    
    private function setBody($show_tela) {

        ob_start(); 
        
        ?>
        
        <body>
            <nav>
                <?php 
                $value = 'setBreadCrumb';

                if(method_exists($this,$value)) {

                    $this->$value();

                }              

                ?>
            </nav>
            <article>  
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="page-header box-rh-header"><?=$this->getPageTitle()?></div>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="avisos">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?=$this->$show_tela();?>
                                        </div><!-- .col-lg-12 -->
                                    </div><!-- .row -->
                                </div><!-- #relatorio -->
                            </div><!-- tab-content -->
                        </div><!-- .col-lg-12 -->
                    </div> <!-- row -->

                </div><!-- /.container -->
            <article>  
            <footer>
                <?=$this->setFooter()->getFooter();?>
            </footer>
            <?=$this->setJavaScriptFooter('setJavaScriptExtFooter')->getJavaScriptFooter();?>
        </body>
        
        <?php
        
        $this->html['body']['value'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
    }
    
    protected function setFooter($value){ 
        
        ob_start(); 
        
        ?>
        <div class="note">
            Pay All Fast 3.0 build <?=$this->getBuild()?> - <?=date('d/m/Y - H:i')?></br>
            Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.
        </div>
        <?php
        
        /*
         * Caso exista o método extendido na extensão da classe, então executa
         */
        if(method_exists($this,$value)) {
            
            $this->$value();
            
        }          

        $this->html['body']['footer'] = ob_get_contents();

        ob_end_clean(); 

        return $this;
        
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getdomain
     * 
     * @internal - Obtem o nome do domínio corrente
     * 
     */    
    protected function getdomain(){

        return $_SERVER['HTTP_HOST'];    

    }
    /*
     * PHP-DOC - Obtem o método a ser executado na classe
     */
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
    
    /*
     * PHP-DOC 
     * 
     * @name isUpdate
     * 
     * @internal - Verifica se o sistema está em atualização e aguarda a liberação atravéz de redirecionamento
     *             com mensagem e armazenamento de todos parámetros passados para processeguir ao liberar
     * 
     */
    protected function isUpdate(){
        
        
    }


    /*
     * PHP-DOC - Executa o método da classe extendida
     */
    protected function exeMethodExt($value1, $value2, $value3, $value4, $value5){
        
        $this->isUpdate();
        
        $dominio = $_SERVER['HTTP_HOST'];

        if (!isset($_COOKIE['logado']) && !isset($_REQUEST['logado'])) {
            header("Location: http://{$dominio}/intranet/login.php?entre=true"); 
            exit;
        }        
        
        /*
         * Inicia o buffer de saída
         */
        ob_start(); 
        
        $value = $this->getMethodExt();
        
        if(method_exists($this,$value)) {
            
            $this->$value($value1, $value2, $value3, $value4, $value5);
            
        }      
        else {
            
            exit("Não existe o método this->{$value} extendido definido para a classe webClass");
            
        }
        
        ob_end_flush();     
        
        return $this;
        
    }

    /*
     * PHP-DOC - Carrega uma página espec?fica
     */      
    protected function showPage($show_tela){ 
        
        header('Content-type: text/html; charset=ISO-8859-1');         
        
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">         
        <html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br" xml:lang="pt-br">
            <?=$this->setHeader()->getHeader()?>
            <?=$this->setBody($show_tela)->getBody()?>
        </html>

        <?php
        
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


