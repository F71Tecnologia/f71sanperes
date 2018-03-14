<?php

/*
 * PHO-DOC - constructClass.php
 * 
 * 30-11-2015 
 * 
 * Classe para montagem de objetos de banco de dados dinamicamente     
 * 
 * Versão: 3.0.0000 - 15/01/2016 - Jacques - Versão Inicial
 * Versao: 3.0.6994 - 25/02/2016 - Jacques - Adicionado o método getRowExt para execução de adição de colunas adicionais a um select
 * Versao: 3.0.6994 - 26/02/2016 - Jacques - Adicionado a classe mãe file
 * Versao: 3.0.7132 - 29/02/2016 - Jacques - Adicionado a classe de bibliotecas para automatização do framework
 * Versao: 3.0.7222 - 02/03/2016 - Jacques - Adicionado o método GetFields para uso no método select 
 * Versao: 3.0.7322 - 04/03/2016 - Jacques - Adicionado o método isOk para verificar estado do objeto apôs a execução de um método
 * Versão: 3.0.7474 - 08/03/2016 - Jacques - Adicionado o método getRowExt para inclusão de propriedades extendidas da classe
 * Versão: 3.0.7773 - 10/03/2016 - Jacques - Adicionado o método para verificação de uso de chaves magnéticas na junção de classes
 * Versão: 3.0.8166 - 16/03/2016 - Jacques - Adicionado o método padrão select e update com carga de métodos extendidos para refino da operação
 * Versão: 3.0.8288 - 18/03/2016 - Jacques - Adicionado o método para salvar o estado padrão de uma classe e restaura-la
 * Versão: 3.0.8738 - 31/03/2016 - Jacques - Adicionado o método setDateRangeQuery que monta a condição de busca por intervalo de forma padrão
 * Versão: 3.0.8738 - 31/03/2016 - Jacques - Adicionado o método setWhereQuery que monta as condições do select de acordo com as propriedades definidas na classe
 *   
 * @jacques
 *  
 */

const PATH_CLASS = "/intranet/classes/";   


class ConstructClass {
    
    private   $error;
    private   $db;
    private   $date;
    private   $file;
    private   $lib;
    private   $names = array(
                            'aliquota_rat' => 'Aliquota_Rat'
                            );
    
    private   $table = '';
    
    private   $obj = array(
                            'column_name' => '',
                            'data_type' => '',
                            'numeric_precision' => 0
                            );
    

    public function __construct() {
        

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
    
    
    
    private function createCoreClass() {
        
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
        
        return $this;
        
    }
    
    public function select(){

        $this->createCoreClass();

        $schema = $this->db->getDbName();

        $table = $this->getTable();

        $this->db->setQuery(SELECT,"column_name, data_type, numeric_precision");
        $this->db->setQuery(FROM,"INFORMATION_SCHEMA.COLUMNS");                    
        $this->db->setQuery(WHERE,"table_schema = '{$schema}' AND table_name = '{$table}'"); 
        
        if(!$this->db->setRs()) $this->error->set("Houve um erro na query de consulta do método select da classe constructClass",E_FRAMEWORK_ERROR);
            
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
        
        $str_where .= strpos($this->get<?=$this->alterEspToFirstUpper($column_name)?>(),",") ? " IN ({$this->get<?=$this->alterEspToFirstUpper($column_name)?>()})" : " = '{$this->get<?=$this->alterEspToFirstUpper($column_name)?>()}'";
        
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
        if(!empty($this->get<?=$this->alterEspToFirstUpper($column_name)?>())) {$this->db->setQuery(WHERE,"AND {$str_where} ",ADD);}
<?php        
}
                        break;
                case 'chkFields': 
?>
        $ok = 0;
        $ok = $ok || !empty($this->get<?=$this->alterEspToFirstUpper($column_name)?>());
        
<?php
                        break;
                case 'set': 
?>
<?php
if($this->getTypeDataGeneral()=='integer' || $this->getTypeDataGeneral()=='real'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = 0) {
    
        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
<?php
} 
if($this->getTypeDataGeneral()=='text'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = '') {
    
        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
<?php
} 
if($this->getTypeDataGeneral()=='time'){
?>
    public function set<?=$this->alterEspToFirstUpper($column_name)?>($value = '') {
    

        $this-><?=$table?>_save['<?=$column_name?>'] = ($this-><?=$table?>['<?=$column_name?>'] = $value);
        
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
 * Início da classe instânciada dinâmicamente
 * 
 * Versão: 3.0.6530 - 15/01/2016 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 *  
 */

error_reporting(E_ERROR);

$table = isset($_REQUEST['table']) ? strtolower($_REQUEST['table']) : exit('defina uma tabela para gerar a classe Ex: gerenateObjClass.php?table=teste'); 

$obj = new ConstructClass();

$obj->setTable($table);

//echo $obj->alterEspToFirstUpper('xxxxx-xxxxx-xxxxx').chr(13);
?>
            
private     $super_class;  
private     $class;
private     $value;
private     $magnetic_key;
public      $error;
public      $date;
public      $db; 
public      $file;
public      $lib;



protected   $<?=$table?>_default = array(
                                <?php
                                $obj->select();$obj->printClass('array');
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

/*
 * PHP-DOC - Set <?=$table?>
 */
public function __construct()
{

    $this->createCoreClass();

}     

public function __toString() {

    return (string)$this->value;

}      

public function isOk() {

    return (int)$this->value;

}    

public function __call($name, $arguments){

    try {
    
        if (strpos($name,'upper') !== false) {

            return strtoupper($arguments[0]);

        }
    
        if(!method_exists($this,$name)) $this->error->set(array(11,"<?=$table?>->{$name}"),E_FRAMEWORK_ERROR);
        
        $this->setValue(1);
        
    }
    catch (Exception $ex) {

        $this->setValue(0);
        
        $this->error->set("A impossibilidade de executar o método {$name} impediu a finalização do processo",E_FRAMEWORK_WARNING,$ex);
        
        exit('<pre>'.$this->error->getAllMsgCode().'</pre>');

    }
    
    return $this;
      
} 
 
public static function __callStatic($name, $arguments){

    try {
    
        if (strpos($name,'upper') !== false) {

            return strtoupper($arguments[0]);

        }
        
        if(!method_exists($this,$name)) $this->error->set(array(11,"<?=$table?>->{$name}"),E_FRAMEWORK_ERROR);
        
        $this->setValue(1);

    }
    catch (Exception $ex) {

        $this->setValue(0);
        
        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        //exit('<pre>'.$this->getAllMsgCode().'</pre>');

    }
    
    return $this;
      
}

public function __get($key) {

    
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
 * PHP-DOC - Define valores padrões para a classe
 */
public function setDefault() {

    $this-><?=$table?>_save = array();

    $this-><?=$table?> =  $this-><?=$table?>_default;
    
    $this->date_range =  $this->date_range_default;
    
    $this->setMagneticKey(1);

    return $this;

}

/*
 * PHP-DOC - Define o Handle da Super Classe
 */
public function setSuperClass($value) {

    $this->super_class = $value;

    return $this;

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
 * @name setMagneticKey
 * 
 * @internal - Define se a classe irá usar chaves magnéticas para vinculo de classes
 */
public function setMagneticKey($value){

    $this->magnetic_key = $value;

    return $this;

} 


<?php
$obj->select();$obj->printClass('set');
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

public function setDateRangeQuery(){

    if(!empty($this->getDateRangeField())) $this->db->setQuery(WHERE,"AND {$this->getDateRangeField()} BETWEEN '{$this->getDateRangeIni($this->getDateRangeFmt())}' AND '{$this->getDateRangeEnd($this->getDateRangeFmt())}'",ADD);

}

public function setSelectQuery($value) {

    $this->db->setQuery(SELECT,empty($value) ? $this->getFields() : $value,ADD);
    
    return $this;

}

public function setFromQuery($value) {

    $this->db->setQuery(FROM,empty($value) ? "<?=$table?> a" : $value,ADD);
    
    return $this;

}

public function setWhereQuery(){

    $this->db->setQuery(WHERE," 1 = 1 ",ADD);

    <?php
    $obj->select();$obj->printClass("where");
    ?>
    
    return $this;

}

public function chkFields(){

    <?php
    $obj->select();$obj->printClass("chkFields");
    ?>
    
    $this->setValue($ok);
    
    return $this;

}

  



/*
 * PHP-DOC - Get <?=$table?>
 */

/*
 * PHP-DOC
 * 
 * @name getMagneticKey
 * 
 * @internal - Obtem o ponteiro da Super Classe
 */ 
public function getSuperClass() {

    return $this->super_class;

}    

/*
 * PHP-DOC
 * 
 * @name getMagneticKey
 * 
 * @internal - Obtem a informação se a classe irá usar chaves magnéticas para vinculo de classes
 */
public function getMagneticKey($value){

    return $this->magnetic_key;

}

/*
 * PHP-DOC
 * 
 * @name getFields
 * 
 * @internal - Obtem os campos da tabela da classe
 */
public function getFields() {

    return "<?=$obj->select();$obj->printClass('select');?>";

}       


<?php
$obj->select();$obj->printClass('get');
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

/*
 * PHP-DOC 
 * 
 * @name getClone
 * 
 * @internal - Esse método retorna o close de uma classe
 * 
 */
public function getCloneClass() {

    try {

        return clone $this;


    } catch (Exception $ex) {

        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

    }  

    return $this;

}    

/*
 * PHP-DOC 
 * 
 * @name saveClass
 * 
 * @internal - Esse método salva o estado de uma classe
 * 
 */
public function saveClass() {

    try {

        $this->class = clone $this;


    } catch (Exception $ex) {

        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

    }  

    return $this;

}    

/*
 * PHP-DOC 
 * 
 * @name restoreClass
 * 
 * @internal - Esse método restaura o estado de uma classe
 * 
 */
public function getRestoreClass() {

    try {
    
        return $this->class;
        

    } catch (Exception $ex) {

        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

    }  

    return $this;

}    
    


/*
 * PHP-DOC 
 * 
 * @name select
 * 
 * @internal - Método de seleção padrão de registros da classe
 * 
 */    
public function select($value = ''){

    try {
    
        $this->setSelectQuery($value)->setFromQuery()->setWhereQuery()->setDateRangeQuery();
        
        if(method_exists($this,'selectExt')) {

            $this->selectExt();

        }

        //if('<?=$table?>'=='rh_clt') echo $this->db->getQuery();
        
        //echo '<?=$table?> <br>';
        
        //if(!$this->chkFields()->isOk()) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);
        
        if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

        $this->setValue(1);

    } 
    catch (Exception $ex) {

        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        $this->setValue(0);

    }


    return $this; 

}

/*
 * PHP-DOC 
 * 
 * @name getRow
 * 
 * @internal - Método de carga padrão de registros para a propriedade da classe
 * 
 */    
public function getRow($value){

    try {

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
            
    
        }
        else{

            $this->setValue(0);

        }
        
    } 
    catch (Exception $ex) {

        $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

        $this->setValue(0);

    }
        
    
    return $this;

}

/*
 * PHP-DOC 
 * 
 * @name update
 * 
 * @internal - Método de atualização padrão de registros da classe
 * 
 */    
public function update(){

    try {

        $this->db->makeFieldUpdate('<?=$table?>',$this-><?=$table?>_save);
    
        if(empty($this-><?=$table?>_save)) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);
        
        $this->setWhereQuery()->setDateRangeQuery();
        
        if(method_exists($this,'updateExt')) {

            $this->updateExt();

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

   
