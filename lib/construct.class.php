<?php
/*
 * PHO-DOC
 * 
 * @brief Classe para montagem de objetos de banco de dados dinamicamente 
 * 
 * @code
 * 
 * @endcode
 * 
 * @file                constructClass.php
 * @license		http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link		http://www.f71lagos.com/intranet/?class=construct
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             constructClass
 * @access              public  
 * 
 * @version: 3.0.0000L - 30-11-2015 - Jacques - Versão Inicial
 * @version: 3.0.6994L - 25/02/2016 - Jacques - Adicionado o método getRowExt para execução de adição de colunas adicionais a um select
 * @version: 3.0.6994L - 26/02/2016 - Jacques - Adicionado a classe mãe file
 * @version: 3.0.7132L - 29/02/2016 - Jacques - Adicionado a classe de bibliotecas para automatização do framework
 * @version: 3.0.7222L - 02/03/2016 - Jacques - Adicionado o mmétodotodo GetFields para uso no método select 
 * @version: 3.0.7322L - 04/03/2016 - Jacques - Adicionado o mmétodotodo isOk para verificar estado do objeto apôs a execução de um método
 * @version: 3.0.7474L - 08/03/2016 - Jacques - Adicionado o método getRowExt para inclusão de propriedades extendidas da classe
 * @version: 3.0.7773L - 10/03/2016 - Jacques - Adicionado o método para verificação de uso de chaves magnéticas na jun��o de classes
 * @version: 3.0.8166L - 16/03/2016 - Jacques - Adicionado o método padrão select e update com carga de métodos extendidos para refino da operação
 * @version: 3.0.8288L - 18/03/2016 - Jacques - Adicionado o método para salvar o estado padrão de uma classe e restaura-la
 * @version: 3.0.8738L - 31/03/2016 - Jacques - Adicionado o método setDateRangeQuery que monta a condição de busca por intervalo de forma padrão
 * @version: 3.0.8738L - 31/03/2016 - Jacques - Adicionado o método setWhereQuery que monta as condições do select de acordo com as propriedades definidas na classe
 * @version: 3.0.9398L - 09/05/2016 - Jacques - Adicionado a possibilidade de conversão do tipo de colação do banco para o charset utilizado nas páginas
 * @version: 3.0.0208F - 10/05/2016 - Jacques - Adicionado o método setDefaultExt para setar valores padrão para as propriedades extendidas da classe
 * @version: 3.0.0209F - 12/08/2016 - Jacques - Adicionado o método where que controla o vetor que irá fornecer os campos de condições da query
 * @version: 3.0.0212F - 19/08/2016 - Jacques - Adicionado o método o método setDefault das classes dinâmicas no instante da criação da classe
 * @version: 3.0.0212F - 16/01/2017 - Jacques - Adicionado a variável cmbBox classe para uso na classe dinâmica
 * @version: 3.0.0212F - 03/02/2017 - Jacques - Adicionado setDefault no início do método getRow() da classe dinâmica pois quando o retorno é vazio estava ficando registrado lixo da consulta nas propriedades da classe
 * 
 * @todo iconv('<?=$this->getCollationNamexCharset()?>','<?=$this->config->title('framework')->key('charset')->val()?>',$value));	
 */

include_once('const.php');

class ConstructClass {
    
    public    $core;
    public    $error;
    public    $db;
    public    $date;
    public    $file;
    public    $log;
    public    $lib;
    public    $config;
    
    private   $names = array(
                            'aliquota_rat' => 'Aliquota_Rat'
                            );
    
    private   $table = '';
    
    private   $charset = array(
                            'latin1_swedich_ci' => 'ISO-8859-1',
                            'latin1_general_ci' => 'ISO-8859-1',
                            'utf8_general_ci' => 'UTF-8'
                            );
    
    private   $obj = array(
                            'column_name' => '',
                            'data_type' => '',
                            'numeric_precision' => 0,
                            'collation_name' => ''
                            );
    

    public function __construct() {
        
        $this->createCoreClass();
        
        return $this;

    }
    
    public function __toString() {

        return (string)$this->value;

    }      

    public function isOk() {

        return (int)$this->value;

    }      
    
    public function alterEspToFirstUpper($value){
        
        if(array_key_exists($value,$this->names)){
            
            return $this->names["{$value}"];
            
        }
        
        $array = str_split($value);
        
        $string = '';
        
        $upper = 0;
        
        foreach ($array as $key => $value) {

            if($upper || $key==0){
                $array[$key] = ucfirst($array[$key]);
                $upper = 0;
            }

            switch ($value) {
                case '-':
                case '_':
                    unset($array[$key]);
                    $upper = 1;
                    break;
                default:
                    $string .= $array[$key];
                    break;
            }
            
        }
                
        return $string;
        
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
    
    public function setDefault(){
        
        $this->createCoreClass();
        
        return $this;
        
    }
    
    public function setTable($value){
        
        $this->table = $value;
        
        return $this;
        
    }
    
    public function setColumnName($value){
        
        $this->obj['column_name'] = $value;

        return $this;
        
    }

    public function setDataType($value){
        
        $this->obj['data_type'] = $value;

        return $this;
        
    }

    public function setNumericPrecision($value){
        
        $this->obj['numeric_precision'] = $value;
        
        return $this;
        
    }
    
    public function setCollationName($value){
        
        $this->obj['collation_name'] = $value;
        
        return $this;
        
    }
    
    
    public function createCoreClass() {

        include('inc-create-core.php'); 
        
        return $this;
        
    }
    
    public function select(){

        $schema = $this->db->getDbName();

        $table = $this->getTable();

        $this->db->setQuery(SELECT,"column_name, data_type, numeric_precision, collation_name");
        $this->db->setQuery(FROM,"INFORMATION_SCHEMA.COLUMNS");                    
        $this->db->setQuery(WHERE,"table_schema = '{$schema}' AND table_name = '{$table}'"); 
        
        if(!$this->db->setRs()) $this->error->set("# Houve um erro na query de consulta do m�todo select da classe constructClass",E_FRAMEWORK_ERROR);
            
        if(!$this->db->getNumRows()) $this->error->set("# A tabela [{$table}] não foi encontrada",E_FRAMEWORK_WARNING);
       
        return $this;
        
    }
    
    public function getColumnName(){
        
        return $this->obj['column_name'];
        
    }
    
    public function getDataType(){
        
        return $this->obj['data_type'];
        
    }

    public function getNumericPrecision(){
        
        return $this->obj['numeric_precision'];
        
    }
    
    public function getCollationName(){
        
        return $this->obj['collation_name'];
        
    }
    
    public function getCollationNamexCharset(){
        
        return $this->charset[$this->getCollationName()];
        
    }
    
    
    private function getTypeDataGeneral(){
        
        switch($this->getDataType()){
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'bit':
                 return 'integer';
                 break;
            case 'float':
            case 'double':
            case 'decimal':
                 return 'real';
                 break;
            case 'varchar': 
            case 'text':
            case 'char':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
                 return 'text';
                 break;
            case 'binary':
            case 'varbinary':
            case 'tinyblob': 
            case 'blob':
            case 'mediumblob':
            case 'longblob':
                 return 'binary';
                 break;
            case 'date':
            case 'time':
            case 'year':
            case 'datetime':
            case 'timestamp':
                 return 'time';
                 break;
            case 'point':
            case 'linestring':
            case 'polygon':
            case 'geometry':
            case 'multipont':
            case 'multilinestring':
            case 'multipolygon':
            case 'geometrycollection':
                 return 'geometry';
                 break;
            case 'enum';
            case 'set':
                 return 'other';
                 break;
            default :
                 return 'void';
                 break;
        }
            
    }
    
    public function getTable(){
        
        return $this->table;
        
    }
    

    public function getRow(){
        
        if($this->db->setRow()){
            
            $this->setColumnName($this->db->getRow('column_name'));
            $this->setDataType($this->db->getRow('data_type'));
            $this->setNumericPrecision($this->db->getRow('numeric_precision'));

            return 1;


        }
        else{

            return 0;
        }
        
    }    
    
    public function printClass($value){
        
        $countCol = 0; 
        
        while ($this->getRow()){
            
            $countCol++;
            
            $table = $this->getTable();
            $column_name = $this->getColumnName();
            $numeric_precision = $this->getNumericPrecision();

            switch($value){
                case 'array': 
                    switch ($this->getTypeDataGeneral()) {
                        case 'time':
?>
'<?=$column_name?>' => ''<?=($this->db->getNumRows() > $countCol) ? ',' : '';?>  
<?php
                            break;
                        case 'integer':
                        case 'real':
?>
'<?=$column_name?>' => 0<?=($this->db->getNumRows() > $countCol) ? ',' : '';?>  
<?php
                            break;
                        default:
?>
'<?=$column_name?>' => ''<?=($this->db->getNumRows() > $countCol) ? ',' : '';?>  
<?php
                            break;
                    }
                    break;   
                case 'select': 
?><?=$column_name?><?=($this->db->getNumRows() > $countCol) ? ',' : '';?>
<?php
                    break;
                case 'where': 
                    
?>
        $str_where = " <?=$column_name?> ";
<?php        
if($this->getTypeDataGeneral()=='integer' || $this->getTypeDataGeneral()=='real'){
?>

        $str_where .= strpos($this->get<?=$this->alterEspToFirstUpper($column_name)?>(),",") ? " IN ({$this->get<?=$this->alterEspToFirstUpper($column_name)?>()})" : " = {$this->get<?=$this->alterEspToFirstUpper($column_name)?>()}";
        
<?php
} 
if($this->getTypeDataGeneral()=='text'){
?>
        $string = '';

        $array = explode(',',$this->get<?=$this->alterEspToFirstUpper($column_name)?>());
        
        foreach ($array as $key => $value) {
        
            $string = (empty($string) ? '' : ',')."'{$value}'";
        
        }
        
        $str_where .= empty($array) ? " = '{<?=$this->alterEspToFirstUpper($column_name)?>()}'" : " IN ({$string})";
        
<?php
} 
if($this->getTypeDataGeneral()=='time'){
?>
        
        $str_where .= strpos($this->get<?=$this->alterEspToFirstUpper($column_name)?>()->val(),",") ? " IN ({$this->get<?=$this->alterEspToFirstUpper($column_name)?>()->val()})" : " = '{$this->get<?=$this->alterEspToFirstUpper($column_name)?>()->val()}'";
        
        if(!empty($this->get<?=$this->alterEspToFirstUpper($column_name)?>()->val())) {$this->db->setQuery(WHERE,"AND {$str_where} ",ADD);}
        
<?php        
}
else {
?>
        if(!empty($this->get<?=$this->alterEspToFirstUpper($column_name)?>()) && isset($this->ptr_array['<?=$column_name?>'])) {$this->db->setQuery(WHERE,"AND {$str_where} ",ADD);}
<?php        
}
                        break;
                case 'chkFields': 
?>
        $ok = $ok || !empty($this->get<?=$this->alterEspToFirstUpper($column_name)?>());
        
<?php
                        break;
                case 'set': 
?>
<?php
if($this->getTypeDataGeneral()=='integer' || $this->getTypeDataGeneral()=='real'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = 0) {
    
        //$this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);        
        
        $this-><?=$table?>['<?=$column_name?>'] = $value;
        
        $this->ptr_array['<?=$column_name?>'] = $value;
        
<?php
} 
if($this->getTypeDataGeneral()=='text'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = '') {
    
        //$this-><?=$table?>['<?=$column_name?>'] = iconv('<?=$this->getCollationNamexCharset()?>','<?=$this->config->title('framework')->key('charset')->val()?>',$value);
        
        //$this-><?=$table?>_save['<?=$column_name?>'] = iconv('<?=$this->config->title('framework')->key('charset')->val()?>','<?=$this->getCollationNamexCharset()?>',$this-><?=$table?>['<?=$column_name?>']);        
        
        //$this-><?=$table?>_save['<?=$column_name?>'] = $this-><?=$table?>['<?=$column_name?>'] = $value;
        
        $this-><?=$table?>['<?=$column_name?>'] = $value;
        
        $this->ptr_array['<?=$column_name?>'] = $value;
        
    
<?php
} 
if($this->getTypeDataGeneral()=='time'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = '', $where=0) {
    

        //$this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
        
        $this-><?=$table?>['<?=$column_name?>'] = $value;        
        
        $this->ptr_array['<?=$column_name?>'] = $value;
        
        
<?php
} 
?>
        return $this;

    }

<?php
                        break;
                case 'get': 
                    
?>

<?php
if($this->getTypeDataGeneral()=='integer' || $this->getTypeDataGeneral()=='real'){
?>
    public function get<?=$this->alterEspToFirstUpper($column_name)?>($value) {

        if(empty($value)){
            
            return $this-><?=$table?>['<?=$column_name?>'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this-><?=$table?>['<?=$column_name?>'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    
<?php
}
if($this->getTypeDataGeneral()=='text'){
?>
    public function get<?=$this->alterEspToFirstUpper($column_name)?>() {

        return $this-><?=$table?>['<?=$column_name?>'];

    }    
<?php
}
if($this->getTypeDataGeneral()=='time'){
?>
    public function get<?=$this->alterEspToFirstUpper($column_name)?>($value) {
    
        $date = clone $this->date;
    
        return $date->set($this-><?=$table?>['<?=$column_name?>'])->get($value);    
        
    } 
<?php    
}
                     break;
                case 'getRow': 
?>
            $this->set<?=$this->alterEspToFirstUpper($column_name)?>($this->db->getRow('<?=$column_name?>'));
<?php    
    
                     break;
                case 'function': 
                     break;
                
            }        
        }
        
    }    
   
}

/*
 *
 * In�cio da classe inst�nciada din�micamente
 * 
 * @version: 3.0.6530 - 15/01/2016 - Jacques - @version Inicial
 * 
 * @author jacques@f71.com.br
 *  
 */

try {
    ob_end_clean(); 
    ob_start(); 
    
    
    $new_class = 'ConstructClass';

    error_reporting(E_ERROR);

    $table = isset($_REQUEST['table']) || isset($argv[1]) ? strtolower($_REQUEST['table'].$argv[1]) : json_encode(array('code' => 1,'message' => 'ERROR: Defina uma tabela para gerar a classe Ex: construct.class.php?table=teste'));
    
    if(class_exists($new_class)) {

        $obj = new $new_class();

    }    
    else {

        die("ERROR: A classe {$new_class} n�o existe. Verifique se o arquivo construct.class.php est� no diret�rio ".__FILE__);

    }

    $obj->setTable($table);

    //echo $obj->alterEspToFirstUpper('xxxxx-xxxxx-xxxxx').chr(13);
    
    ?>

    private     $super_class;  
    private     $class;
    private     $value;
    private     $magnetic_key;
    private     $ptr_array;
    public      $fw;
    public      $error;
    public      $date;
    public      $db; 
    public      $file;
    public      $cmbBox;
    public      $log;
    public      $lib;
    public      $config;


    protected   $<?=$table?>_default = array(
                                    <?php
                                    $obj->select()->printClass('array');
                                    ?>
                                    );

    protected   $date_range_default = array(
                                'field' => '',
                                'ini' => '',
                                'end' => '',
                                'fmt' => '',
                                'sql_fmt' => ''
                                );

    protected   $date_range = array();

    protected   $<?=$table?> = array();

    protected   $<?=$table?>_save = array();
    
    protected   $<?=$table?>_where = array();

    /*
     * PHP-DOC - Set <?=$table?>
     */
    public function __construct() {

        $this->createCoreClass();
        $this->setDefault();

    }     

    public function __toString() {

        return (string)$this->value;

    }   

    /*
     * PHP-DOC 
     * 
     * @name __call
     * 
     * @internal - É disparado ao invocar métodos inacessíveis em um contexto de objeto.
     * 
     */   
    public function __call($name, $arguments){

        try { 

            if (strpos($name,'upper') !== false) return strtoupper($arguments[0]);

            $class = array_search('<?=$table?>', $this->fw->table);

            if(!method_exists($this,$name)) $this->error->set(array(11,"{$class}->{$name}"),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

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

            if (strpos($name,'upper') !== false) return strtoupper($arguments[0]);
            
            $class = array_search('<?=$table?>', $this->fw->table);

            if(!method_exists($this,$name)) $this->error->set(array(11,"$class->{$name}"),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        }
        catch (Exception $ex) {

            $this->setValue(0);

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

        $this->$name = $value;

        $this->error->set("Propriedade Inacessível Setting '$name' to '$value' via construct.class.php, experimente declará-la como proctect ou public",E_FRAMEWORK_ERROR); 

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
        echo "Getting '$name'\n";

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();

        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);

        return null;
    }


    /**
     * PHP-DOC 
     * 
     * @name createCoreClass
     * 
     * @internal - Método para criar e inst�nciar as classes mães do core
     * 
     */     
    public function createCoreClass() {

        if(!include('inc-create-core.php')) die ('Não foi possível incluir inc-create-core.php na classe dinâmica da tabela <?=$table?>'); 

        return $this;

    }

    /**
     * PHP-DOC - Define valores padr�es para a classe
     */
    public function setDefault() {

        $this-><?=$table?>_save = array();

        $this-><?=$table?> =  $this-><?=$table?>_default;

        $this->date_range =  $this->date_range_default;
        
        $this->ptr_array = &$this-><?=$table?>_save;

        $this->setMagneticKey(1);
        
        if(method_exists($this,'setDefaultExt')) $this->setDefaultExt();

        return $this;

    }

    public function isOk() {

        return (int)$this->value;

    }      


    /**
     * PHP-DOC - Define o Handle da Super Classe
     */
    public function setSuperClass($value) {

        $this->fw = $this->super_class = $value;

        return $this;

    }

    /**
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
     
    /**
     * Define se a classe irão usar chaves magnéticas para vinculo de classes
     * 
     * @access public
     * @method setMagneticKey
     * @param
     * 
     * @return 
     */       
    public function setMagneticKey($value){

        $this->magnetic_key = $value;

        return $this;

    } 
    
    /**
     * Define condição para uma operação de atualização (update)
     * 
     * @access public
     * @method where
     * @param
     * 
     * @return 
     */       
    public function where()    {

        $this->ptr_array = &$<?=$table?>_where;

        return $this;

    } 

    <?php
    $obj->select()->printClass('set');
    ?>

    public function setDateRangeField($value){

        $this->date_range['field'] = $value;

        return $this;

    }

    public function setDateRangeIni($value = ''){

        $this->date_range['ini'] = $value;

        return $this;

    }

    public function setDateRangeFim($value = ''){

        $this->setDateRangeEnd($value);

        return $this;

    }

    public function setDateRangeEnd($value = ''){

        $this->date_range['end'] = $value;

        return $this;

    }

    public function setDateRangeFmt($value = 'Ymd'){

        $this->date_range['fmt'] = $value;

        $this->setDateRangeSqlFmt($value);

        return $this;

    }

    private function setDateRangeSqlFmt($value = 'Ymd'){

        $this->date_range['sql_fmt'] = $this->date->getFmtDateConvSql($value);

        return $this;

    }

    private function setDateRangeQuery(){

        if(!empty($this->getDateRangeField())) $this->db->setQuery(WHERE,"AND {$this->getDateRangeField()} BETWEEN '{$this->getDateRangeIni($this->getDateRangeFmt())}' AND '{$this->getDateRangeEnd($this->getDateRangeFmt())}'",ADD);
        
        return $this;

    }

    private function setSelectQuery($value) {

        $this->db->setQuery(SELECT,empty($value) ? $this->getFields() : $value,ADD);

        return $this;

    }

    private function setFromQuery($value) {

        $this->db->setQuery(FROM,empty($value) ? "<?=$table?> a" : $value,ADD);

        return $this;

    }

    public function setWhereQuery($value){
    
        $this->db->setQuery(WHERE," 1 = 1 ",ADD);

        <?php
        $obj->select()->printClass("where");
        ?>

        //if(!empty($this->getDateRangeField())) $this->db->setQuery(WHERE," 1 = 1 ",ADD);
        
        return $this;

    }

   /**
    * Ferifica os campos da classe
    * 
    * @access public
    * @method chkFields
    * @param
    * 
    * @return $this
    */       
    public function chkFields(){
    
        $ok = 0;
        
        <?php
        $obj->select()->printClass("chkFields");
        ?>

        $this->setValue($ok);

        return $this;

    }





    /*
     * PHP-DOC - Get <?=$table?>
     */

    
   /**
    * Obtem o ponteiro da Super Classe
    * 
    * @access public
    * @method getSuperClass
    * @param
    * 
    * @return $this
    */       
    public function getSuperClass() {

        return $this->super_class;

    }    

   /**
    * Obtem a informa��o se a classe irão usar chaves magnéticas para vinculo de classes
    * 
    * @access public
    * @method getMagneticKey
    * @param
    * 
    * @return $this
    */       
    public function getMagneticKey($value){

        return $this->magnetic_key;

    }

   /**
    * Médodo para clonar uma classe
    * 
    * @access public
    * @method getFields
    * @param
    * 
    * @return $this
    */         
    public function getFields() {

        return "<?=$obj->select()->printClass('select');?>";

    }       


    <?php
    $obj->select()->printClass('get');
    ?>

    public function getDateRangeField(){

        return $this->date_range['field'];        

    }

    public function getDateRangeFmt(){

        return $this->date_range['fmt'];        

    }


    public function getDateRangeIni($value){

        $date = clone $this->date;

        return $date->set($this->date_range['ini'])->get($value);    

    }

    public function getDateRangeFim($value){

        return $this->getDateRangeEnd($value);

    }

    public function getDateRangeEnd($value){

        $date = clone $this->date;

        return $date->set($this->date_range['end'])->get($value);    

    }

    public function getDateRangeSqlFmt(){

        return $this->date_range['sql_fmt'];        

    }    

   /**
    * Médodo para clonar uma classe
    * 
    * @access public
    * @method getCloneClass
    * @param
    * 
    * @return $this
    */      
    public function getCloneClass() {

        try {

            return clone $this;


        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        }  

        return $this;

    }    

   /**
    * Médodo para salvar o estado da classe
    * 
    * @access public
    * @method saveClass
    * @param
    * 
    * @return $this
    */   
    public function saveClass() {

        try {

            $this->class = clone $this;


        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        }  

        return $this;

    }    

   /**
    * Médodo para restaurar o estado da classe
    * 
    * @access public
    * @method getRestoreClass
    * @param
    * 
    * @return $this
    */         
    public function getRestoreClass() {

        try {

            return $this->class;


        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        }  

        return $this;

    }    


   /**
    * Médodo da classe para seleção de registros
    * 
    * @access public
    * @method select
    * @param
    * 
    * @return $this
    */         
    public function select($value = ''){

        try {
        
            $this->setSelectQuery($value)->setFromQuery()->setWhereQuery()->setDateRangeQuery();
            
            if(method_exists($this,'selectExt') && !$this->fw->config->title('clt')->key('nao_selectExt')->val()) {

                $this->selectExt();

            }

            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

            $this->setValue(1);

        } 
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);

        }


        return $this; 

    }

   /**
    * Médodo da classe para load dos registros na classe
    * 
    * @access public
    * @method getRow
    * @param
    * 
    * @return $this
    */        
    public function getRow($value){

        try {
        
            $this->setValue(0);

            $this->setDefault();

            if($this->db->setRow()){
            
                if(empty($value)){
                
                    <?php
                    $obj->select();$obj->printClass("getRow");
                    ?>

                    $this->setValue(1);

                    if(method_exists($this,'getRowExt')) {

                        $this->getRowExt();

                    }

                }
                else {

                    return $this->db->getRow($value);

                }
                
                $this->setValue(1);

            }

        } 
        catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);


        }


        return $this;

    }
    
   /**
    * Médodo da classe para inclusão de registros
    * 
    * @access public
    * @method insert
    * @param
    * 
    * @return $this
    */     
    public function insert(){

        try {
        
            $this->db->makeFieldInsert('<?=$table?>',$this-><?=$table?>_save);

            if(empty($this-><?=$table?>_save)) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);

            if(method_exists($this,'insertExt')) {

                $this->insertExt();

            }

            if(!$this->chkFields()->isOk()) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);

            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

            $this->setValue(1);


        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);

        }

        return $this;


    }
    


   /**
    * Médodo da classe para atualização de registros
    * 
    * @access public
    * @method update
    * @param
    * 
    * @return $this
    */        
    public function update(){

        try {

            $this->db->makeFieldUpdate('<?=$table?>',$this-><?=$table?>_save);

            if(empty($this-><?=$table?>_save)) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);

            $this->setWhereQuery()->setDateRangeQuery();

            if(method_exists($this,'updateExt')) {

                $this->updateExt();

            }
            
            //echo $this->db->getQuery();
            
            if(!$this->chkFields()->isOk()) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

            $this->setValue(1);


        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);

        }

        return $this;


    }

    <?php
    
    
    $html = ob_get_contents(); 
    
    ob_end_clean(); 
    
    echo $html;
    
} catch (Exception $ex) {
    
    $e = json_decode($obj->error->getAllMsgCodeJson(), true);
    
    foreach ($e as $key => $error) {
        
        $obj->error->set($error['message'],E_FRAMEWORK_WARNING,$ex);
        
    }
    
    $obj->error->set(array(1,get_class($obj)),E_FRAMEWORK_WARNING,$ex);

    ob_end_clean(); 
    
    echo $obj->error->getAllMsgCodeJson();
    
    
}

   
