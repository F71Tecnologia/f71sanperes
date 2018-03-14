<?php

/**
 * Classe para conexão a banco de dados MySql moldando a conexão a orientação a objetos 
 * 
 * @file      MySqlClass.php
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link      
 * @copyright 2015 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   MySqlClass
 * @access    public  
 * @version: 3.00.L0000 - 25/05/2016 - Jacques - Versão Inicial 
 * @version: 1.00.L0000 - 00/00/0000 - Jacques - Versão Beta da classe de conexão
 * @version: 1.01.L0000 - 00/00/0000 - Jacques - Acrescentado o método kill para evitar muitas conexões 
 * @version: 1.02.L0000 - 00/00/0000 - Jacques - Acrescentado parámetro no método getQuery($index = NULL)
 * @version: 1.03.L0000 - 00/00/0000 - Jacques - Acrescentado o vetor de private  $query_string_default
 * @version: 1.04.L0000 - 00/00/0000 - Jacques - Acrescentado o vetor collection mais métodos relacionados a ele 03-07-2015
 * @version: 1.05.L0000 - 00/00/0000 - Jacques - Acrescentado método de montagem de string de buscas 06/07/2015
 * @version: 1.06.L0000 - 00/00/0000 - Jacques - Alterado o método setSearch para montagem da query direto no método setQuery
 * @version: 1.07.L0000 - 00/00/0000 - Jacques - Adicionado novo método setQuery2 para montagem mais intuitiva da query
 * @version: 1.08.L0000 - 14/09/2015 - Jacques - setQuery recebeu a funcionalidade de setQuery2
 * @version: 1.09.L0000 - 14/09/2015 - Jacques - Criada a função de retorno de array no lugar do rs, criada também a função de retorno de json e controle de versão interno
 * @version: 1.10.L0000 - 22/09/2015 - Jacques - Adicionado verificação de elemento em um array do método getRow() e adicionado o método getQueryLast()
 * @version: 1.11.L0000 - 06/10/2015 - Jacques - Acrescentado método para controle de operações via RollBack
 * @version: 1.12.L0000 - 12/11/2015 - Jacques - Acrescentado verificação de campo nulo na query de inserção para caso algum campo possua valor vazio ou nulo então seta 0 para valores numéricos
 * @version: 1.13.L4479 - 27/11/2015 - Jacques - Aprimoramento do método setRs() com fechamento da conexão ao Banco de Dados
 * @version: 1.14.L4511 - 30/11/2015 - Jacques - Criação do método de adição de campos calculados por linha, subgrupos e geral retornados para a coleção
 * @version: 1.15.L4601 - 03/12/2015 - Jacques - Adicionado ao método setCol() a opção de adicionar campo com atribuição de valor numérico além de índices de vetor
 * @version: 1.16.L4669 - 07/12/2015 - Jacques - Adicionado ao método setCol() a opção de adicionar string para comparação
 * @version: 1.17.L4778 - 09/12/2015 - Jacques - Correção no método setCol() que estava retornando campo de registro sem adição de referência a vetor com a condição preg_match("/\"([^\"]*)\"/",$matches[0]) || preg_match("([0-9.]+)",$matches[0])
 * @version: 1.18.L4778 - 02/01/2016 - Jacques - Adicionando arquivo metodo para carregar arquivo com valores de conexão ao banco de dados
 * @version: 1.19.L5676 - 21/01/2016 - Jacques - Método setQuery adicionado aos métodos MakeField* para evitar trabalhar com retorno de valor
 * @version: 1.20.L5884 - 26/01/2016 - Jacques - Adicionado método que obtem o número de linhas afetadas em operação de INSERT, UPDATE e DELETE
 * @version: 1.20.L6068 - 29/01/2016 - Jacques - Adicionado a verificação de NULL nos métodos de montagem de query makeFieldUpdate e makeFieldInsert
 * @version: 1.20.L6460 - 16/02/2016 - Jacques - Adicionado a verificação da carga do arquivo de configuração da classe
 * @version: 1.20.L6882 - 22/02/2016 - Jacques - Adicionado o total de linhas retornadas na execução do método setRs
 * @version: 1.20.L8762 - 01/04/2016 - Jacques - Acrescentado a posibilidade de leitura do setup.ini de forma vetorizada por índice de agrupamento
 * @version: 1.20.L8762 - 20/06/2016 - Jacques - Acrescentado método de validação para execução da query.
 * @version: 1.20.F0224 - 08/09/2016 - Jacques - Incrementado o método getArray() para receber dois parâmetros e retornar chave/valor
 * @version: 1.20.F0239 - 10/10/2016 - Jacques - Adicionado o registro em log da última query executada
 * @version: 1.20.F0246 - 19/10/2016 - Jacques - Adicionado o registro de uma propriedade para controle do código de página default ou definido pelo usuário e o setQuery permitir métodos encadeados
 * @version: 1.20.F0000 - 21/02/2017 - Jacques - Fix do problema de acentuamento para o tipo de conexão ao banco de dados mysqli usando em setEnvironment self::$db->set_charset("utf8") e ajustando outros sets para mysqli
 * 
 * @todo 
 * @example:  
 *  
 * 
 *  
 * setNumRow,getNumRow
 * 
 * OBS: Implementar a op??o de getRow das classes baseadas nos campos do SELECT passados para setRs para uso din?mico
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */

include_once('const.php');

class MySqlClass {
    
    /* 
     * Passar para carga de arquivo MySqlClass.ini os valores de set de conex?o 
     */
    public static $pdo;
    public static $db;

    public    $config;
    public    $error;
    public    $crypt;

    private   $host = "";
    private   $dbname = "";
    private   $user = "";  
    private   $pass = "";      
    private   $row;
    private   $rs;
    private   $connected;
    private   $result_memory = 0;
    private   $search = '';
    private   $versao = 'tag_ver';
    private   $num_rows = 0;
    private   $value;
    private   $code_page = 'utf8';




    private   $query_string_value = array(
                             QUERY => '',
                             SELECT => 'SELECT ',
                             FROM => 'FROM ',
                             UPDATE => 'UPDATE ',
                             INSERT => 'INSERT ',
                             WHERE => 'WHERE ',
                             GROUP => 'GROUP BY',
                             HAVING => 'HAVING ',
                             ORDER => 'ORDER BY ',
                             LIMIT => 'LIMIT ',
                             CALL => 'CALL ');    

    private   $query_string_default = array(
                             QUERY => '',
                             SELECT => '',
                             FROM => '',
                             UPDATE => '',
                             INSERT => '',
                             WHERE => '',
                             GROUP => '',
                             HAVING => '',
                             ORDER => '',
                             LIMIT => '',
                             CALL => '');    
    
    private   $query_string = array();
    
    private   $last_query_string = '';
    
    private   $collection = array();
    
    private   $result = array();
    
    private   $array = array();
    

    /*
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - Executa alguns métodos na construção da classe
     */    
    public function __construct(){
        
        $this->createCoreClass();        
        $this->setDefault();

    }

    public function __toString() {
        
        return (string)$this->value;
        
    }    
        
    /**
     * Método que para ferificar o estado atual da classe
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
     * Método que define um código de pagina a ser utilizado pelo banco de dados
     * 
     * @access public
     * @method setCodePage
     * @param  
     * 
     * @return $this
     */      
    public function setCodePage($value) { 
        
        $this->code_page = $value;
        
        return $this;

    }
    
    /**
     * Método que define valor para retorno no uso de métodos encadeados
     * 
     * @access public
     * @method setValue
     * @param  type $value string
     * 
     * @return $this
     */         
    public function setValue($value){

        $this->value = $value;

        return $this;

    }    
    
    /**
     * Método que define Define valores default da classe
     * 
     * @access public
     * @method setDefault
     * @param  
     * 
     * @return $this
     */     
    public function setDefault(){
        
        //$this->setResultMemory();
        $this->row = array();
        $this->rs = array();      
        $this->array = array();
        $this->collection = array();
        $this->query_string = $this->query_string_default;
        
        $this->setConnect();
        
        return $this;
        
    }
    
    public function setHost($value) {
        
        $this->host = $value;
            
        return $this;
        
    }	

    public function setUser($value) {
        
        $this->user = $value;
        
        return $this;
            
    }	

    public function setPass ($value) {
        
        $this->pass = $value;
            
        return $this;
        
    }	

    public function setDbName($value) {
        
        $this->dbname = $value;
        
        return $this;
            
    }	
    
    /*
     * PHP-DOC 
     * 
     * @name setConnect
     * 
     * @internal - Define valores para conexão ao banco de dados
     */       
    public function setConnect(){
        
        try {
            
            if(empty($this->config->title('conn')->key('host')->val()) || empty($this->config->title('conn')->key('db_name')->val()) || empty($this->config->title('conn')->key('user')->val())) $this->error->set('Host, Db ou User não definido',E_FRAMEWORK_ERROR);
            
            $this->setHost($this->config->title('conn')->key('host')->val());

            $this->setDbName($this->config->title('conn')->key('db_name')->val());

            $this->setUser($this->config->title('conn')->key('user')->val());

            $this->setPass($this->config->title('conn')->key('pass')->val());
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }          
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setTransaction
     * 
     * @internal - Define variáveis de ambiente para execução de transação distribuída
     */    
    public function setTransaction(){
        
        mysql_query("SET AUTOCOMMIT = 0;");
        mysql_query("START TRANSACTION;");
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name commit
     * 
     * @internal - Efetiva as transações distribuídas feitas no banco de dados
     */    
    public function commit(){
        
        mysql_query("COMMIT;");
        mysql_query("SET AUTOCOMMIT = 1;");
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name RollBack
     * 
     * @internal - Desfaz as transações distribuídas feitas no banco de dados
     */    
    public function RollBack(){
        
        mysql_query("ROLLBACK;");
        mysql_query("SET AUTOCOMMIT = 1;");
        
        return $this;
        
    }

    /**
     * Seta parâmetros de uma Query para consulta ao banco de dados
     * 
     * @access public
     * @method setQuery
     * @param  $index
     * @param  $value
     * @param  $add
     * 
     * @return int
     */     
    public function setQuery($index , $value, $add = FALSE){
        
        ($add) ? $this->query_string[$index] .= " {$value} " : $this->query_string[$index] = " {$value}";
        
        return $this;
    
    }    
    
    /**
     * Verifica as condições para execução da query
     * 
     * @access public
     * @method chkQuery
     * @param
     * 
     * @return int
     */     
    public function chkQuery(){
        
        try {

            //if(empty($this->query_string[WHERE])) $this->error->set('Pelo menos uma condição deve ser especificada para execução da query',E_FRAMEWORK_ERROR,$ex);

            return 1;
            
        } 
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            return 0;
            
        }        

    }    
    
    /*
     * PHP-DOC 
     * 
     * @name setResultMemory
     * 
     * @internal - Seta se o resultado da consulta irá para memória ou ficará no ponteiro da consulta ao banco de dados
     */    
    public function setResultMemory($value = 0){
        
        $this->result_memory = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name createCoreClass
     * 
     * @internal - Cria outras classes acessórias a execução da classe MySqlClass
     */       
    private function createCoreClass() {
        
        try {

            $this->setValue(0);
            
            $file['inc'] = ROOT_LIB.'inc-projeto.php'; 
            $file['config'] = ROOT_LIB.'ConfigClass.php'; 
            $file['error'] = ROOT_LIB.'ErrorClass.php'; 

            foreach ($file as $key => $value) {

                if(!file_exists($file[$key])) exit(_("# Não foi possível carregar o arquivo do core ").$file[$key]._(" da classe ").__CLASS__);

            }

            if(!isset($this->error)){

                include_once($file['error']);

                $this->error = new ErrorClass();        

                if(!is_object($this->error)) die(_("# Não foi possível instânciar a classe error"));

            }

            if(!isset($this->config)){

                if(!include_once($file['config'])) $this->error->set(_("# Não foi possível incluir {$file['config']}"),E_FRAMEWORK_ERROR);

                if(!include($file['inc'])) $this->error->set(_("# Não foi possível incluir {$file['inc']}"),E_FRAMEWORK_ERROR);  
                
                $this->config = new ConfigClass($file_setup);        

                if(!is_object($this->config)) $this->error->set(_("# Não foi possível instânciar a classe config"),E_FRAMEWORK_ERROR);
                
            }    

            $this->setValue(1);

        } catch (Exception $ex) {

            $this->error->set(_("Não foi possível aplicar o método createCoreClass"),E_FRAMEWORK_WARNING,$ex);

            
        }

        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name setRs
     * 
     * @internal - Seta array ou ponteiro de um conjunto de registros baseados na query de consulta 
     */       
    public function setRs(){
        
        try {

    //        $this->setNumRow();

            if(!$this->connect()->isOk()) $this->error->set(_("# Não foi possível realizar a conexão ao banco de dados"),E_FRAMEWORK_ERROR);

            $query = $this->getQuery();
            
            if(!$this->chkQuery()) $this->error->set('Erro na validação da Query',E_FRAMEWORK_ERROR);
            
            /**
             * Carrega um vetor da consulta ao banco de dados
             */
            if(!$result = self::$db->query($query)) {

                $this->error->dump($this->getLastQuery());
                $this->error->set('Uma rotina de consulta ao banco de dados falhou e interrompeu o processo',E_FRAMEWORK_NOTICE);
                $this->error->set('# '.self::$db->error,E_FRAMEWORK_ERROR);

            }
            
            $this->setNumRows($result->num_rows);
            
            if($this->getResultMemory()) {
                
                while ($this->rs[] = $result->fetch_array(MYSQLI_ASSOC));
                
            }
            else {
                
                $this->rs = $result;

            }
            
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }            
        
        return $this->rs;
                
    }
    
    /*
     * PHP-DOC - Set uma linha de registros a partir de um recordset
     */
    public function setRow(){  
        
        $this->row = $this->rs->fetch_array(MYSQLI_ASSOC);
        
        if(self::$db->connect_errno) {

            $this->error->dump($this->getLastQuery());
            $this->error->set('Uma rotina de consulta ao banco de dados falhou e interrompeu o processo',E_FRAMEWORK_NOTICE);
            $this->error->set("# ".mysql_error()." - veja arquivo /framework/dump.log",E_FRAMEWORK_ERROR);
            
        }    
        
        return $this->row;

    }  
    
    /*
     * PHP-DOC - Set Cols acrescenta elementos (Colunas) ao vetor de linha de registros com op??o de campos calculados
     */
    public function setCol($value) {
        
        $callback_fields = function($matches) {
            
                                if (preg_match("/\"([^\"]*)\"/",$matches[0]) || is_numeric($matches[0])) { 
                                    
                                    return $matches[0];
                                        
                                }
                                else {
                                    
                                    return '$this->row["'.$matches[0].'"]';
                                    
                                }                                 
            
                            };        
        
        $callback_number = function($matches) {
            
                                return $matches[0];
            
                            };        
        
        $fields = explode(',',$value);
        
        foreach ($fields as $key => $value) {
            
            if(empty($value)) continue;
        
            switch (true) {
                case strpos($value,'+='):
                    $key = '+=';
                    $offset = 2;
                    break;
                case strpos($value,'-='):
                    $key = '-=';
                    $offset = 2;
                    break;
                case strpos($value,'*='):
                    $key = '*=';
                    $offset = 2;
                    break;
                case strpos($value,'/='):
                    $key = '/=';
                    $offset = 2;
                    break;
                default:
                    $offset = 1;
                    $key = '=';
                    break;
            }
            
            $arr = explode($key,$value);
            
            $idx = strpos($value,$key);
            
            $len = strlen($value);
            
            $stored = substr($value,0,$idx);
            
            $command = substr($value,$idx+$offset,$len);
            
            if(is_numeric($command)){
                
                $line = preg_replace_callback('([0-9.]+)',$callback_number,$command);
                        
            }
            else {

                $line = preg_replace_callback('([0-9a-zA-Z_"]+)',$callback_fields,$command);
                
            }
            
            if($offset==2){
                
                $command = '$this->result["'.$stored.'"]'.$key.$line.';';
                
            }
            else {
                
                $command = '$this->result = array("'.$stored.'" => '.$line.');';
                
            }
            
            eval($command);
            
            $this->row = array_merge((array)$this->row,(array)$this->result);
            
        }
        
    }
    
    public function setNumRows($value){
        
        $this->num_rows = $value;
        
    }

   
    /*
     * PHP-DOC - Monta uma cole??o de registros baseada nos valores passados por par?metros em forma de string que defininem os fields de um select
     * 
     * O vetor e carregado com tr?s ?ndex espec?ficos:
     * 
     * dados: A resultante do agrupamento de dados do select
     * sum: A resultante do somat?rio de todos os grupos e subgrupos, com ?ndice general para a soma de todos os subgrupos, e ?ndice global 
     *      para a soma de v?rias cargas de select para o mesmo tipo de agrupamento
     */
    public function setCollection($value,$value_sum_row,$value_sum_group){
        
        $cols = explode(',',$value);

        $command_rs    = '$this->array[] = $this->row;';
        
        $command_array = '$this->collection["data"]';
        $command_group = '';
        $command_value = ' = $this->getRow();';
        
        $cols_sum_group = explode(',',$value_sum_group);
        
        $command_sum_array = '$this->collection["sum"]';
        $command_sum_group = '';
        $command_sum_general = '';
        $command_sum_group_value = '';
        $command_sum_general_value = '';
        
        $command_count_array = '$this->collection["count"]';
        $command_count_group = '';
        $command_count_general = '';
        $command_count_group_value = '';
        $command_count_general_value = '';
        
        $cols_sum_row = explode(',',$value_sum_row);
        
        $this->setCol($value_sum_row);
        
        foreach ($cols as $key => $value) {
            
            $command_group .= '["'.$this->getRow($value).'"]';
                
            foreach ($cols_sum_group as $key_sum => $value_sum) {
                
                if(empty($value_sum) || empty($this->getRow($value_sum))) continue;
                
                /*
                 * Executa o somatório de campos com grupos e subgrupos
                 */
                
                $command_sum_group = '["group"]'.$command_group.'["'.$value_sum.'"]';
                
                $command_sum_general = '["general"]["'.$value_sum.'"]';
                
                $command_sum_group_value = '+='.$this->getRow($value_sum).';';

                $command_sum_general_value = '+='.$this->getRow($value_sum).';';
                
                eval($command_sum_array.$command_sum_group.$command_sum_group_value);
                
                eval($command_sum_array.$command_sum_general.$command_sum_general_value);
                
                /*
                 * Executa a contagem de campos com grupos e subgrupos
                 */
                
                $command_count_group = '["group"]'.$command_group.'["'.$value_sum.'"]';
                
                $command_count_general = '["general"]["'.$value_sum.'"]';
                
                $command_count_group_value = '+=1;';

                $command_count_general_value = '+=1;';
                
                eval($command_count_array.$command_count_group.$command_count_group_value);
                
                eval($command_count_array.$command_count_general.$command_count_general_value);
                
                
            }
            
        }
        
        eval($command_array.$command_group.$command_value);
        
        eval($command_rs);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Define a configuração de ambiente para execução das querys
     */    
    private function setEnvironment(){ 
        
        /**
         * Set de ambiente para operações via mysql_query
         */
        mysql_query("SET NAMES '{$this->getCodePage()}'");
        mysql_query("SET character_set_connection={$this->getCodePage()}");
        mysql_query("SET character_set_client={$this->getCodePage()}");
        mysql_query("SET character_set_results={$this->getCodePage()}");    
        
        /**
         * Set de ambiente para operações via mysqli_query
         */
        self::$db->set_charset($this->getCodePage());
        self::$db->query("SET TIME_ZONE = '-03:00'");
        self::$db->query("SET @@GROUP_CONCAT_MAX_LEN=1048576");
        
        return $this;
    }
    
    /*
     * PHP-DOC - M?todo para montar string de busca
     * 
     * Par?metros:
     * 
     * $value    = string para busca Ex.: Jacques Nunes 
     * $key     = campo ou fields para busca
     * $operand = Operadores l?gicos da busca
     * $inline  = Opera??es em linha
     * $add     = Indica se ? uma montagem de busca concatenada ou n?o
     */
    public function setSearch($value, $key, $operand = 'OR', $inline, $add = 0){
        
        $words = explode(' ', $value);
        $fields = explode(',',$key);
        $search = '';
        
        foreach ($words as $key_word => $word) {
                
            foreach ($fields as $key_field => $field) {
                    
                $search .= " {$operand} {$field} LIKE '%{$word}%'";
                
            }
             
        }  
        
        return $this->setQuery(WHERE," $search $inline ",$add);
        
    }

    /*
     * PHP-DOC - Executa a conex?o a um banco de dados
     */       
    public function connect(){ 
        
        try {
            
            $connect = "[db_name = {$this->getDbName()}, host = {$this->getHost()}, user = {$this->getUser()}]";

            self::$db = new mysqli("{$this->getHost()}", "{$this->getUser()}", "{$this->getPass()}", "{$this->getDbName()}");    
            
            if (self::$db->connect_errno) $this->error->set("# ".mysqli_connect_error().", verifique o arquivo framework/setup.ini {$connect}",E_FRAMEWORK_ERROR);

            $this->setEnvironment();

        } 
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }        
        
        return $this;
        
    }
    
    
    /**
     * Método de conexão ao banco de dados via PDO
     * 
     * @access public
     * @method connectPDO
     * @param  
     * 
     * @return this;
     */    
    public function connectPDO(){ 
        
        try {
            
            if (!isset(self::$pdo)) {
                self::$pdo = new PDO(
                    "mysql:host={$this->getHost()};dbname={$this->getDbName()}",
                    "{$this->getUser()}",
                    "{$this->getPass()}",
                    array(PDO::ATTR_PERSISTENT => false)
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
            }
   
            return self::$pdo;                

        } 
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }      
        
        return $this;
        
    }
      
    
    
    
    /**
     * Método que obtem o código de página definido de forma padrão ou pelo usuário
     * 
     * @access public
     * @method getCodePage
     * @param  
     * 
     * @return $this
     */      
    public function getCodePage() { 
        
        return $this->code_page;
        
    }    
    
    /**
     * Método que obtem o IP ou DNS do host
     * 
     * @access public
     * @method getHost
     * @param  
     * 
     * @return string
     */      
    public function getHost() {
        
        return $this->host;
            
    }	

    /*
     * PHP-DOC - Obtem o nome do usu?rio 
     */
    public function getUser() {
        
        return $this->user;
            
    }	

    /*
     * PHP-DOC - Obtem a senha de acesso
     */
    public function getPass() {
        
        return $this->pass;
            
    }	

    /*
     * PHP-DOC - Obtem o nome do banco de dados
     */
    public function getDbName() {
        
        return $this->dbname;
            
    }    
    
    /*
     * PHP-DOC - Obtem a vers?o da classe
     */
    private function getVersao(){
        
        return $this->versao;
        
    }     
    
    private function getResultMemory(){
        
        return $this->result_memory;
        
    }    

    public function getRs(){
        
        return $this->rs;
        
    }    
    
    public function getRow($value){
        
        if(!empty($value)) {
            
            if (!array_key_exists($value,$this->row) && empty($this->collection)) {
                
                return NULL;
                
            }
            else {
                
                return $this->row[$value];

            }
            
        }
        
        
        return $this->row;
        
    }    
    
    /*
     * PHP-DOC - Retorna a Query de consulta montada limpando o vetor de montagem no final
     */
    public function getQuery($index = NULL){
        
        $query = '';
        
        if(empty($index)) {
            
            $start = 0;
            $off_set = count($this->query_string_value)+1;
            
        }
        else {

            $start = $index;
            $off_set = $start + 1;
            
        }
        
            
        for ($i = $start; $i < $off_set; $i++) {

            $command = $this->query_string_value[$i];

            $string = $this->query_string[$i];

            if(strlen($string) > 0){

                $query .= "  $command {$string} ";
                
                $this->query_string[$i] = '';

                
            }

        }
        
        $this->last_query_string  = $query;
        
        return $query;
        
    } 
    
    /*
     * PHP-DOC - Retorna a ?ltima query executada
     */
    public function getLastQuery(){
        
        return $this->last_query_string;
    
    }    
    
    /*
     * PHP-DOC - Obtem uma coleção de registro agrupados por chaves, que podem ser somadas a partir do segundo conjunto de parâmetros
     * 
     * Parâmetros:
     * value1 = Campos a serem agrupados (Regra de agrupamento)
     * value2 = Campos calculados a partir dos campos de cada linha de registros
     * value3 = Campos com os valores totais de cada agrupamento
     * value4 = Inicializa o vetor novamente para evitar futuros agrupamentos em consultas sequenciais
     */
    public function getCollection($value1,$value2,$value3,$value4){
        
        $this->result  = array();
        
        if(isset($value4)) {
            
            $this->collection = array();
            
        }    
        
        while ($this->setRow()) {
            
            $this->setCollection($value1,$value2,$value3);
            
        }
        
        $this->rs = $this->array;
        
        return $this->collection;    
        
    }
    
    
    /*
     * PHP-DOC - Retorna o Json de um array
     */
    public function getJson(){
        
        return json_encode($this->getArray());          
        
    }
    
    public function getArray($key='',$value=''){
        
        $this->array = array();
        
        if(empty($key) && empty($value)){
            
            while ($this->setRow()) {

                $this->array[] = $this->getRow();

            }
            
        }
        else {

            while ($this->setRow()) {

                $this->array[$this->getRow($key)] = $this->getRow($key).' - '.$this->getRow($value);

            }
            
        }
        
            
        return $this->array;
        
    }
    
    public function getTotCollection($value){
        
        $cols = explode(',',$value);

        $command = '$this->collection';
        
        foreach ($cols as $key => $value) {
            
            $command .= '[$this->getRow('.$value.')]';
            
        }
        
        return eval("count{$command};");
        
    }    
    
    // Para verificar as Querys processadas
    // A ideia inicial aqui e verificar UPDATE sem WHERE
    // Evitar tamb?m inje??o SQL
    // Sintaxe com aspas duplas no meio da instru??o
    private function check(){
        
        
    }
    
    /*
     * Obtem a nova chave de registro inclu?do
     */
    public function getKey(){
        
        return self::$db->insert_id;
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name getRowAffected
     * 
     * @internal - Obtem o número de linhas afetadas para uma determinada operação
     */    
    public function getRowAffected(){
        
       $info = explode(' ',mysql_info());
       
       return (int)$info[4];
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name getRowMatched
     * 
     * @internal - Obtem o número de linhas compatíveis a execução da operação
     */    
    public function getRowMatched(){ 
        
       $info = explode(' ',mysql_info());
       
       return (int)$info[2];
        
    }
    
    
    /*
     * PHP-DOC 
     * 
     * @name setNumRow
     * 
     * @internal - Método que seta a query para criação de uma coluna com contagem de linhas
     * 
     */    
    public function setNumRow(){
        
        $this->setQuery(SELECT,',@rownum:=@rownum+1 as num_row',ADD);
        
        $this->setQuery(FROM,', (SELECT @rownum:=0) r',ADD);
        
    }
    
    
    /*
     * PHP-DOC 
     * 
     * @name getNumRows
     * 
     * @internal - Método para obter o número da linha corrente
     * 
     */    
    public function getNumRows(){
        
        return $this->num_rows;
	
    }

    public function getObj(){
		
        return ($this->getResultMemory() ?  (object)$this->getRs() : mysql_fetch_object($this->getRs()));

	
    }
    
    public function getSearch(){
        
        return $this->search;
        
    }
    
    public function kill() {

        $result = mysql_query("SHOW FULL PROCESSLIST");

        if (count($result) >= 5) {
                while ($this->row=mysql_fetch_assoc($result)) {
                $process_id=$this->row["Id"];
                        if ($this->row["Time"] > 20000 && $this->row["Command"]=="Query") {
                                $sql="KILL $process_id";
                                return mysql_query($sql);
                        }
                }
                
                //$this->error->setError(mysql_error());
        
                return 0;
                
        }
        
        return 0;
    }	
    /*
     * PHP-DOC - Constroi a uma query de atualiza??o a partir de um array
     */
    public function makeFieldUpdate($table,$array){
        
        $fields = '';
        
        foreach ($array as $key => $value) {
            
            if(gettype($value) == 'string' || is_null($value)){

                $fields .= (empty($fields) ? " $key='$value' " : ", $key='$value' ");
                
            }
            else {
                
                $fields .= (empty($fields) ? " $key=$value " : ", $key=$value ");
                
            }    
            
        }
        
        
        $string = " {$fields} ";
        
        $this->setQuery(UPDATE," {$table} SET {$string}",ADD);
        
        return $this;
        
    }
    /*
     * PHP-DOC - Constroi a uma query de inser??o a partir de um array
     */
    public function makeFieldInsert($table, $array){
        
        $fields = '';
        $values = '';
        
        foreach ($array as $key => $value) {
            
            $fields .= (empty($fields) ? " {$key} " : ", {$key} ");

            if(gettype($value) == 'string' || is_null($value)){

                $values .= (empty($values) ? " '{$value}' " : ", '{$value}' ");
                
            }
            else {
                
                /*
                 * Caso algum campo possua valor vazio ou nulo ent?o seta 0 para valores num?ricos
                 */
                if(empty($value)) {

                    $values .= (empty($values) ? " 0 " : ", 0 ");

                }
                else {
                    
                    $values .= (empty($values) ? " {$value} " : ", {$value} ");
                    
                }
                
            }    
            
        }
        
        
        $string = " ({$fields}) VALUES ({$values}) ";
        
        $this->setQuery(INSERT," {$table} {$string} ",ADD);
        
        return $this;
        
    }
    
    public function close() {
        
        self::$db->close();
        
        return $this;
        
    }
	

}