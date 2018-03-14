<?php
/*
 * Classe para montagem de objetos de banco de dados
 * Data Criação: 23/06/2015
 * 
 * Obs: Teste para instânciamento dinâmico futuro do framework
 *  
 * e-mail: jacques@f71.com.br
 * Versão: 0.1 (Build 00001)* 
 */

const PATH_CLASS = "/intranet/classes/"; 


class GenerateObjClass {
    
    protected $error;
    private   $db;
    private   $date;
    
    private   $table = '';
    
    private   $obj = array(
                        'column_name' => '',
                        'data_type' => '',
                        'numeric_precision' => 0
                        );
    

    function __construct() {
        

    }
    
    public function alterEspToFirstUpper($value){
        
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
    
    public function setDefault(){
        
        $this->createCoreClass();
        
    }
    
    public function setTable($value){
        
        $this->table = $value;
        
    }
    
    public function setColumnName($value){
        
        $this->obj['column_name'] = $value;
        
    }

    public function setDataType($value){
        
        $this->obj['data_type'] = $value;
        
    }

    public function setNumericPrecision($value){
        
        $this->obj['numeric_precision'] = $value;
        
    }
    
    
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
        if(!isset($this->date)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'DateClass.php');

            $this->date = new DateClass();
            
        }
        
        
    }
    
    public function select(){
        
        $this->createCoreClass();
        
        $table = $this->getTable();
        
        $this->db->setQuery(SELECT,"column_name, data_type, numeric_precision");
        $this->db->setQuery(FROM,"INFORMATION_SCHEMA.COLUMNS");                    
        $this->db->setQuery(WHERE,"table_name = '{$table}' AND table_schema = 'ispv_netsorrindo'"); 
        
        if($this->db->setRs()){
            
            return 1;
            
        }
        else {

            return 0;
            
        }        
        
        
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
        
        while ($this->getRow()){
            
            $table = $this->getTable();
            $column_name = $this->getColumnName();
            $numeric_precision = $this->getNumericPrecision();

            switch($value){
                case 'array': 
                    switch ($this->getTypeDataGeneral()) {
                        case 'time':
?>
'<?=$column_name?>' => '',
<?php
                            break;
                        case 'integer':
                        case 'real':
?>
'<?=$column_name?>' => 0,
<?php
                            break;
                        default:
?>
'<?=$column_name?>' => '',
<?php
                            break;
                    }
                        break;   
                case 'select': 
?><?=$column_name?>,
<?php                   break;   
                case 'set': 
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value) {

<?php
if($this->getTypeDataGeneral()=='integer' || $this->getTypeDataGeneral()=='real'){
?>
        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
<?php
} 
if($this->getTypeDataGeneral()=='text'){
?>
        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
<?php
} 
if($this->getTypeDataGeneral()=='time'){
?>

        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
        
<?php
} 
?>

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

error_reporting(E_ERROR);

$table = isset($_REQUEST['table']) ? $_REQUEST['table'] : exit('defina uma tabela para gerar a classe Ex: gerenateObjClass.php?table=teste'); 

echo '<pre>';


$obj = new GenerateObjClass();

$obj->setTable($table);

//echo $obj->alterEspToFirstUpper('xxxxx-xxxxx-xxxxx').chr(13);
?>
/*
 * PHO-DOC - <?=$obj->alterEspToFirstUpper($table)?>Class.php
 * 
 * Descreva a função da classe aqui
 * 
 * <?=date("d-m-Y");?> 
 *
 * @name <?=$obj->alterEspToFirstUpper($table)?> 
 * @package <?=$obj->alterEspToFirstUpper($table)?>Class
 * @access public/private/protected  
 * 
 * @version
 *
 * Versão: 3.0.0000 - <?=date("d-m-Y");?> - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */            
      
        
class <?=$obj->alterEspToFirstUpper($table)?>Class {

    private     $super_class;    
    public      $error;
    private     $date;
    private     $db; 
        
    private     $<?=$table?>_default = array(
<?php
$obj->select();$obj->printClass('array');
?>
                                    );

    private     $date_range = array(
                                'field' => '',
                                'ini' => '',
                                'fim' => '',
                                'fmt' => '',
                                'sql_fmt' => ''
                                );
                                
    private     $<?=$table?> = array();

    private     $<?=$table?>_save = array();
    
    /*
     * PHP-DOC - Set <?=$table?>
     */
    public function __construct()
    {

    }     
    
    private function createCoreClass() {
        
        
        if(!isset($this->error)){
            
            include_once('ErrorClass.php');
            
            $this->error = new ErrorClass();        
            
            if(!is_object($this->getSuperClass()->error)){
                
               $this->getSuperClass()->error = $this->error;
               
            }
            
        }
        
        if(!isset($this->db)){
            
            include_once('MySqlClass.php');

            $this->db = new MySqlClass();
            
            if(!is_object($this->getSuperClass()->db)){
                
               $this->getSuperClass()->db = $this->db;
               
            }
            
        }
        
        if(!isset($this->date)){
            
            include_once('DateClass.php');

            $this->date = new DateClass();
            
            if(!is_object($this->getSuperClass()->date)){
                
               $this->getSuperClass()->date = $this->date;
               
            }
        }
        
    }       
    
    /*
     * PHP-DOC - Define valores padrões para a classe
     */
    public function setDefault() {
        
        $this->createCoreClass();       
        
        $this-><?=$table?>_save = array();
        
        $this-><?=$table?> =  $this-><?=$table?>_default;
        
    }
    
    /*
     * PHP-DOC - Define o Handle da Super Classe
     */
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    
<?php
$obj->select();$obj->printClass('set');
?>
    public function setDateRangeField($value){

        $this->date_range['field'] = $value;
        
    }
    
    public function setDateRangeIni($value){
    
        $this->date_range['ini'] = $value;
        
    }

    public function setDateRangeFim($value){
        
        $this->date_range['fim'] = $value;

        
    }

    public function setDateRangeFmt($value){
        
        $this->date_range['fmt'] = $value;
        
        $this->setDateRangeSqlFmt($value);
        
    }
    
    private function setDateRangeSqlFmt($value){

        $this->date_range['sql_fmt'] = $this->date->getFmtDateConvSql($value);
        
    }
    
    public function setWhere($value){

        $this->db->setQuery(WHERE," {$value} AND ",$ADD);
        
    }

    /*
     * PHP-DOC - Get <?=$table?>
     */
     
    /*
     * PHP-DOC - Obtem o ponteiro da Super Classe
     */
    public function getSuperClass() {
        
        return $this->super_class;
        
    }       
    
<?php
$obj->select();$obj->printClass('get');
?>
    
    public function getDateRangeField(){
        
        return $this->date_range['field'];        
        
    }
    
    public function getDateRangeIni($value){
    
        $date = clone $this->date;
    
        return $date->set($this->date_range['ini'])->get($value);    
        
    }

    public function getDateRangeFim($value){

        $date = clone $this->date;
    
        return $date->set($this->date_range['fim'])->get($value);    
        
    }
    
    public function getDateRangeFmt($value){
        
        return $this->date_range['fmt'];        
        
    }
    
    public function getDateRangeSqlFmt($value){
        
        return $this->date_range['sql_fmt'];        
        
    }    

    public function getRow(){

        if($this->db->setRow()){

            <?php
            $obj->select();$obj->printClass('getRow');
            ?>

            return 1;

        }
        else{

            return 0;
        }

    }
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
    <?php
    $obj->select();$obj->printClass('select');
    ?>    
                            ");
        
        $this->db->setQuery(FROM," 
    <?php
    $obj->select();$obj->printClass('from');
    ?>    
                            <?=$table?> ");
            
    }

}
<?php
echo '</pre>';


?>
