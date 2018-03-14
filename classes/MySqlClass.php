<?php
/*
 * PHP-DOC - MySqlClass.php 
 * 
 * Classe para conexão a banco de dados MySql moldando a conexão a orientação a objetos  
 *
 * 02-06-2015
 * 
 * @package MySqlClass
 * @access public   
 *  
 * @version
 *  
 * Versão: 1.00.0000 - 00/00/0000 - Jacques - Versão Beta da classe de conexão
 * Versão: 1.01.0000 - 00/00/0000 - Jacques - Acrescentado o método kill para evitar muitas conexões 
 * Versão: 1.02.0000 - 00/00/0000 - Jacques - Acrescentado parámetro no método getQuery($index = NULL)
 * Versão: 1.03.0000 - 00/00/0000 - Jacques - Acrescentado o vetor de private  $query_string_default
 * Versão: 1.04.0000 - 00/00/0000 - Jacques - Acrescentado o vetor collection mais métodos relacionados a ele 03-07-2015
 * Versão: 1.05.0000 - 00/00/0000 - Jacques - Acrescentado método de montagem de string de buscas 06/07/2015
 * Versão: 1.06.0000 - 00/00/0000 - Jacques - Alterado o método setSearch para montagem da query direto no método setQuery
 * Versão: 1.07.0000 - 00/00/0000 - Jacques - Adicionado novo método setQuery2 para montagem mais intuitiva da query
 * Versão: 1.08.0000 - 14/09/2015 - Jacques - setQuery recebeu a funcionalidade de setQuery2
 * Versão: 1.09.0000 - 14/09/2015 - Jacques - Criada a função de retorno de array no lugar do rs, criada também a função de retorno de json e controle de versão interno
 * Versão: 1.10.0000 - 22/09/2015 - Jacques - Adicionado verificação de elemento em um array do método getRow() e adicionado o método getQueryLast()
 * Versão: 1.11.0000 - 06/10/2015 - Jacques - Acrescentado método para controle de operações via RollBack
 * Versão: 1.12.0000 - 12/11/2015 - Jacques - Acrescentado verificação de campo nulo na query de inserção para caso algum campo possua valor vazio ou nulo então seta 0 para valores numéricos
 * Versão: 1.13.4479 - 27/11/2015 - Jacques - Aprimoramento do método setRs() com fechamento da conexão ao Banco de Dados
 * Versão: 1.14.4511 - 30/11/2015 - Jacques - Criação do método de adição de campos calculados por linha, subgrupos e geral retornados para a coleção
 * Versão: 1.15.4601 - 03/12/2015 - Jacques - Adicionado ao método setCol() a opção de adicionar campo com atribuição de valor numérico além de índices de vetor
 * Versão: 1.16.4669 - 07/12/2015 - Jacques - Adicionado ao método setCol() a opção de adicionar string para comparação
 * Versão: 1.17.4778 - 09/12/2015 - Jacques - Correção no método setCol() que estava retornando campo de registro sem adição de referência a vetor com a condição preg_match("/\"([^\"]*)\"/",$matches[0]) || preg_match("([0-9.]+)",$matches[0])
 * Versão: 1.18.4778 - 02/01/2016 - Jacques - Adicionando arquivo metodo para carregar arquivo com valores de conexão ao banco de dados
 * Versão: 1.19.5676 - 21/01/2016 - Jacques - Método setQuery adicionado aos métodos MakeField* para evitar trabalhar com retorno de valor
 * Versão: 1.20.5884 - 26/01/2016 - Jacques - Adicionado método que obtem o número de linhas afetadas em operação de INSERT, UPDATE e DELETE
 * Versão: 1.20.6068 - 29/01/2016 - Jacques - Adicionado a verificação de NULL nos métodos de montagem de query makeFieldUpdate e makeFieldInsert
 * Versão: 1.20.6460 - 16/02/2016 - Jacques - Adicionado a verificação da carga do arquivo de configuração da classe
 * Versão: 1.20.6882 - 22/02/2016 - Jacques - Adicionado o total de linhas retornadas na execução do método setRs
 * Versão: 1.20.8762 - 01/04/2016 - Jacques - Acrescentado a posibilidade de leitura do setup.ini de forma vetorizada por índice de agrupamento
 *  
 * setNumRow,getNumRow
 * 
 * OBS: Implementar a op??o de getRow das classes baseadas nos campos do SELECT passados para setRs para uso din?mico
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */

const QUERY  = 0;
const SELECT = 1;
const FROM   = 2;
const UPDATE = 3;
const INSERT = 4;
const WHERE  = 5;
const SEARCH = 6;
const GROUP  = 7;
const HAVING = 8;
const ORDER  = 9;
const LIMIT  = 10;

const ADD = 1;

class MySqlClass {
    
    /* 
     * Passar para carga de arquivo MySqlClass.ini os valores de set de conex?o 
     */

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




    private   $query_string_value = array(
                             QUERY => '',
                             SELECT => 'SELECT ',
                             FROM => 'FROM ',
                             UPDATE => 'UPDATE ',
                             INSERT => 'INSERT ',
                             WHERE => 'WHERE ',
                             GROUP => 'GROUP ',
                             HAVING => 'HAVING ',
                             ORDER => 'ORDER BY ',
                             LIMIT => 'LIMIT ');    

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
                             LIMIT => '');    
    
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
        
        //$this->setQuery("LIMIT 100", LIMIT); 
        $this->setDefault();

    }

    public function __toString() {
        
        return (string)$this->value;
        
    }    
        
    /*
     * PHP-DOC 
     * 
     * @name __destruct
     * 
     * @internal - Executa alguns métodos ao destruir à classe
     */    
    public function __destruct() { 
        
        $this->close();

    }
    
    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Define valores default da classe
     */    
    public function setDefault(){
        
        //$this->setResultMemory();
        $this->row = array();
        $this->rs = array();      
        $this->collection = array();
        $this->query_string = $this->query_string_default;
        
        $this->createCoreClass();        
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
            
            $file = dirname(__FILE__).'/setup.ini';    
            
            if(!file_exists($file)) $this->error->set(array(9,__METHOD__." -> {$file}"),E_FRAMEWORK_ERROR);
            
            if($setup = parse_ini_file($file,true)){
                
                if(isset($setup['conn'])){
                    
                    $this->setHost($setup['conn']['host']);

                    $this->setDbName($setup['conn']['db_name']);

                    $this->setUser($setup['conn']['user']);

                    $this->setPass($setup['conn']['pass']);
                    
                }
                else {
                    
                    if($setup = parse_ini_file($file)){

                        $this->setHost($setup['host']);

                        $this->setDbName($setup['db_name']);

                        $this->setUser($setup['user']);

                        $this->setPass($setup['pass']);
                        
                    }
                    else {
                        
                        $this->error->set(array(8,__METHOD__),E_FRAMEWORK_ERROR);
                        
                    }
                    
                }
                
            }
            else {
                
                $this->error->set(array(8,__METHOD__),E_FRAMEWORK_ERROR);
                
            }

            
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

    /*
     * PHP-DOC 
     * 
     * @name setQuery
     * 
     * @internal - Seta parâmetros de uma Query para consulta ao banco de dados
     */    
    public function setQuery($index , $value, $add = FALSE){

        return ($add) ? $this->query_string[$index] .= " {$value} " : $this->query_string[$index] = " {$value}";
    
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
        
        if(!isset($this->error)){
            
            include_once('ErrorClass.php');
            
            $this->error = new ErrorClass();        
            
        }
        
        return $this;
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name setRs
     * 
     * @internal - Seta array ou ponteiro de um conjunto de registros baseados na query de consulta 
     */       
    public function setRs(){
            
//        $this->setNumRow();

        $this->connect();

        if($this->getResultMemory()) {

            /*
             * Carrega um vetor da consulta ao banco de dados
             */
            while ($this->rs[] = mysql_fetch_assoc(mysql_query($this->getQuery())));

            $this->setNumRows(count($this->getRs()));
            
        }
        else {

            /*
             * Seta o ponteiro da conexão de consulta ao banco de dados
             */

            $this->rs = mysql_query($this->getQuery());
            
            $this->setNumRows(mysql_num_rows($this->getRs()));

        }

        if(mysql_error()) $this->error->set(mysql_error(),E_FRAMEWORK_ERROR);

        return $this->rs;
        
                
    }
    
    /*
     * PHP-DOC - Set uma linha de registros a partir de um recordset
     */
    public function setRow(){  
        
        return $this->row = mysql_fetch_assoc($this->rs);

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
        
        $command_array = '$this->collection["dados"]';
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
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Define a configura??o de ambiente para execu??o das querys
     */    
    private function setEnvironment(){ 
        
        //mysql_query("SET NAMES 'utf8'");
        //mysql_query('SET character_set_connection=utf8');
        //mysql_query('SET character_set_client=utf8');	
        mysql_query("SET TIME_ZONE = '-03:00'");	
        mysql_query("SET @@GROUP_CONCAT_MAX_LEN=1048576");	 
        
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
        
            $this->createCoreClass();
            
            mysql_close();
            
            if(!mysql_connect($this->getHost(),$this->getUser(),$this->getPass())) $this->error->set("Não foi possível conectar ao host",E_FRAMEWORK_ERROR);
            
            $this->setEnvironment();

            if(!mysql_select_db($this->getDbName())) $this->error->set("Não foi possível conectar ao banco de dados ao banco de dados",E_FRAMEWORK_ERROR);
            
        } 
        catch (Exception $ex) {
            
            echo "<pre>";
            echo $this->error->set($ex)->getAllMsgCode();
            echo "</pre>";

        }        
        
    }
    
    /*
     * PHP-DOC - Obtem o IP ou DNS do host
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
        
        return $this->collection;    
        
    }
    
    
    /*
     * PHP-DOC - Retorna o Json de um array
     */
    public function getJson(){
        
        
        return json_encode($this->getArray());          
        
    }
    
    public function getArray(){
        
        /*
         * Executa-se o m?todo para poder limpar a query
         */
        $this->getQuery();
        
        $this->array = array();
        
        while ($this->setRow()) {
            
            $this->array[] = $this->getRow();
            
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
        
        return mysql_insert_id();
        
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
        
        if(mysql_close($this->connected)){
            
            return 1;
            
        }
        else {

            return 0;

        }
        
    }
	

}