<?php 
include("../../conn.php");
include("../../wfunction.php");

$tabela = 'entradaesaida_subgrupo';
$id_tabela = 'id';
$classe = 'EntradaESaidaSubGrupo';

$tabulacao = '&nbsp;&nbsp;&nbsp;&nbsp;';

echo "class {$classe}Class { <br><br>";

$query = mysql_query("SHOW COLUMNS FROM $tabela");
while ($coluna = mysql_fetch_assoc($query)) {
echo $tabulacao.'protected $'.$coluna[Field].';<br>';
}
echo "<br>
{$tabulacao}protected \$QUERY;<br>
{$tabulacao}protected \$SELECT = ' * ';<br>
{$tabulacao}protected \$FROM = ' $tabela ';<br>
{$tabulacao}protected \$WHERE;<br>
{$tabulacao}protected \$GROUP;<br>
{$tabulacao}protected \$HAVING;<br>
{$tabulacao}protected \$ORDER;<br>
{$tabulacao}protected \$LIMIT;<br>
{$tabulacao}protected \$rs;<br>
{$tabulacao}protected \$row;<br>
{$tabulacao}protected \$numRows;<br><br>

{$tabulacao}function __construct() { <br>{$tabulacao}{$tabulacao}<br>{$tabulacao}}<br><br>";

$query = mysql_query("SHOW COLUMNS FROM $tabela");
echo "{$tabulacao}//GET's DA CLASSE<br>";
while ($coluna = mysql_fetch_assoc($query)) {
if($coluna[Type] == 'timestamp' || $coluna[Type] == 'date'){
    echo 
    "{$tabulacao}function get".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."(\$formato = null) {<br>
    {$tabulacao}{$tabulacao}if (empty(\$this->$coluna[Field]) || \$this->$coluna[Field] == '0000-00-00') {<br>
    {$tabulacao}{$tabulacao}{$tabulacao}return '';<br>
    {$tabulacao}{$tabulacao}} else {<br>
    {$tabulacao}{$tabulacao}{$tabulacao}return (!empty(\$formato)) ? date_format(date_create(\$this->$coluna[Field]), \$formato) : \$this->$coluna[Field];<br>
    {$tabulacao}{$tabulacao}}<br>
    {$tabulacao}}<br><br>";
}else if(strripos($coluna[Field], 'cnpj') !== false || strripos($coluna[Field], 'cpf') !== false){
    echo 
    "{$tabulacao}function get".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."(\$limpo = false) {<br>
    {$tabulacao}{$tabulacao}return (\$limpo) ? str_replace(array('.','-','/'), '' ,\$this->$coluna[Field]) : \$this->$coluna[Field];<br>
    {$tabulacao}}<br><br>";
} else {
    echo 
    "{$tabulacao}function get".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."() {<br>
    {$tabulacao}{$tabulacao}return \$this->{$coluna[Field]};<br>
    {$tabulacao}}<br><br>";
    }
}

echo "{$tabulacao}//SET's DA CLASSE<br>";
$query = mysql_query("SHOW COLUMNS FROM $tabela");
while ($coluna = mysql_fetch_assoc($query)) {
echo 
"{$tabulacao}function set".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."(\${$coluna[Field]}) {<br>
{$tabulacao}{$tabulacao}\$this->{$coluna[Field]} = \${$coluna[Field]};<br>
{$tabulacao}}<br><br>";
}

echo "
{$tabulacao}protected function setQUERY(\$QUERY) {<br>
{$tabulacao}{$tabulacao}\$this->QUERY = \$QUERY;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setSELECT(\$SELECT) {<br>
{$tabulacao}{$tabulacao}\$this->SELECT = \$SELECT;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setFROM(\$FROM) {<br>
{$tabulacao}{$tabulacao}\$this->FROM = \$FROM;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setWHERE(\$WHERE) {<br>
{$tabulacao}{$tabulacao}\$this->WHERE = \$WHERE;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setGROUP(\$GROUP) {<br>
{$tabulacao}{$tabulacao}\$this->GROUP = \$GROUP;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setORDER(\$ORDER) {<br>
{$tabulacao}{$tabulacao}\$this->ORDER = \$ORDER;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setLIMIT(\$LIMIT) {<br>
{$tabulacao}{$tabulacao}\$this->LIMIT = \$LIMIT;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setHAVING(\$HAVING) {<br>
{$tabulacao}{$tabulacao}\$this->HAVING = \$HAVING;<br>
{$tabulacao}}<br><br>";

echo "{$tabulacao}//SET DEFAULT<br>
{$tabulacao}function setDefault() {<br>";
$query = mysql_query("SHOW COLUMNS FROM $tabela");
while ($coluna = mysql_fetch_assoc($query)) {
echo "{$tabulacao}{$tabulacao}\$this->{$coluna[Field]} = null;<br>";
}
echo "{$tabulacao}}<br><br>";

echo "
{$tabulacao}protected function setRs() {<br>
{$tabulacao}{$tabulacao}if (!empty(\$this->QUERY)) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$sql = \$this->QUERY;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$auxWhere = (!empty(\$this->WHERE)) ? \" WHERE \$this->WHERE \" : null;<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$auxGroup = (!empty(\$this->GROUP)) ? \" GROUP BY \$this->GROUP \" : null;<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$auxHaving = (!empty(\$this->HAVING)) ? \" HAVING \$this->HAVING \" : null;<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$auxOrder = (!empty(\$this->ORDER)) ? \" ORDER BY \$this->ORDER \" : null;<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$auxLimit = (!empty(\$this->LIMIT)) ? \" LIMIT \$this->LIMIT \" : null;<br>
<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$sql = \"SELECT \$this->SELECT FROM \$this->FROM \$auxWhere \$auxGroup \$auxHaving \$auxOrder \$auxLimit\";<br>
{$tabulacao}{$tabulacao}}<br>
<br>
{$tabulacao}{$tabulacao}\$this->rs = mysql_query(\$sql);<br>
{$tabulacao}{$tabulacao}\$this->numRows = mysql_num_rows(\$this->rs);<br>
{$tabulacao}{$tabulacao}return \$this->rs;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function limpaQuery() {<br>
{$tabulacao}{$tabulacao}\$this->setQUERY('');<br>
{$tabulacao}{$tabulacao}\$this->setSELECT(' * ');<br>
{$tabulacao}{$tabulacao}\$this->setFROM(' {$tabela} ');<br>
{$tabulacao}{$tabulacao}\$this->setWHERE('');<br>
{$tabulacao}{$tabulacao}\$this->setGROUP('');<br>
{$tabulacao}{$tabulacao}\$this->setHAVING('');<br>
{$tabulacao}{$tabulacao}\$this->setORDER('');<br>
{$tabulacao}{$tabulacao}\$this->setLIMIT('');<br>
{$tabulacao}}<br>
<br>
{$tabulacao}public function getNumRows() {<br>
{$tabulacao}{$tabulacao}return \$this->numRows;<br>
{$tabulacao}}<br>
<br>
{$tabulacao}protected function setRow(\$valor) {<br>
{$tabulacao}{$tabulacao}return \$this->row = mysql_fetch_assoc(\$valor);<br>
{$tabulacao}}<br><br>";



echo "{$tabulacao}//RECUPERANDO INFO DO BANCO<br>
{$tabulacao}public function getRow() {<br><br>
{$tabulacao}{$tabulacao}if (\$this->setRow(\$this->rs)) {<br>";
$query = mysql_query("SHOW COLUMNS FROM $tabela");
while ($coluna = mysql_fetch_assoc($query)) {
echo 
"{$tabulacao}{$tabulacao}{$tabulacao}\$this->set".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."(\$this->row['{$coluna[Field]}']);<br>";
}
echo "
{$tabulacao}{$tabulacao}{$tabulacao}return 1;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}//\$this->setError(mysql_error());<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 0;<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}}<br><br>";

echo "
{$tabulacao}private function makeCampos() {<br>
<br>
{$tabulacao}{$tabulacao}\$array = array(<br>";

$query = mysql_query("SHOW COLUMNS FROM $tabela");
while ($coluna = mysql_fetch_assoc($query)) {
if($coluna[Field] != $id_tabela){
    echo 
    "{$tabulacao}{$tabulacao}{$tabulacao}'{$coluna[Field]}' => addslashes(\$this->get".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."()),<br>";
    }
}
echo "
{$tabulacao}{$tabulacao});<br>
<br>
{$tabulacao}{$tabulacao}return \$array;<br>
{$tabulacao}}<br><br>";


echo "
{$tabulacao}public function update() {<br>
{$tabulacao}{$tabulacao}\$this->limpaQuery();<br>
{$tabulacao}{$tabulacao}\$array = \$this->makeCampos();<br>
<br>
{$tabulacao}{$tabulacao}foreach (\$array as \$key => \$value) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$camposUpdate[] = \"\$key = '\$value'\";<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}{$tabulacao}\$this->setQUERY(\"UPDATE {$tabela} SET \" . implode(\", \", (\$camposUpdate)) . \" WHERE $id_tabela = {\$this->get".str_replace(' ','',ucwords(str_replace('_',' ',$id_tabela)))."()} LIMIT 1;\");<br>
<br>
{$tabulacao}{$tabulacao}if (\$this->setRs()) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 1;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 0; //\$this->setError(mysql_error());<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}}<br>
<br>
{$tabulacao}public function insert() {<br>
{$tabulacao}{$tabulacao}\$this->limpaQuery();<br>
{$tabulacao}{$tabulacao}\$array = \$this->makeCampos();<br>
<br>
{$tabulacao}{$tabulacao}\$keys = implode(',', array_keys(\$array));<br>
{$tabulacao}{$tabulacao}\$values = implode(\"' , '\", \$array);<br>
<br>
{$tabulacao}{$tabulacao}\$this->setQUERY(\"INSERT INTO {$tabela} (\$keys) VALUES ('\$values');\");<br>
{$tabulacao}{$tabulacao}if (\$this->setRs()) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}\$this->set".str_replace(' ','',ucwords(str_replace('_',' ',$id_tabela)))."(mysql_insert_id());<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 1;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}die(mysql_error());<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}}<br>
<br>
{$tabulacao}public function inativa() {<br>
{$tabulacao}{$tabulacao}\$this->limpaQuery();<br>
<br>
{$tabulacao}{$tabulacao}\$this->setQUERY(\"UPDATE {$tabela} SET status = 0 WHERE $id_tabela = {\$this->get".str_replace(' ','',ucwords(str_replace('_',' ',$id_tabela)))."()} LIMIT 1;\");<br>
{$tabulacao}{$tabulacao}if (\$this->setRs()) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 1;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}die(mysql_error());<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}}<br>
<br>
{$tabulacao}public function deleta() {<br>
{$tabulacao}{$tabulacao}\$this->limpaQuery();<br>
<br>
{$tabulacao}{$tabulacao}\$this->setQUERY(\"DELETE FROM {$tabela} WHERE $id_tabela = {\$this->get".str_replace(' ','',ucwords(str_replace('_',' ',$id_tabela)))."()} LIMIT 1;\");<br>
{$tabulacao}{$tabulacao}if (\$this->setRs()) {<br>
{$tabulacao}{$tabulacao}{$tabulacao}return 1;<br>
{$tabulacao}{$tabulacao}} else {<br>
{$tabulacao}{$tabulacao}{$tabulacao}die(mysql_error());<br>
{$tabulacao}{$tabulacao}}<br>
{$tabulacao}}<br>";

echo '}';


//$query = mysql_query("SHOW COLUMNS FROM $tabela");
//while ($coluna = mysql_fetch_assoc($query)) {
//echo 
//"{$tabulacao}\$obj{$classe}->set".str_replace(' ','',ucwords(str_replace('_',' ',$coluna[Field])))."(\$_REQUEST['$coluna[Field]']);<br>";
//}
