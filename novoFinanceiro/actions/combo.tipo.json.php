n<?php 
require("../../conn.php");

$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT id_entradasaida, cod, nome 
FROM  entradaesaida
WHERE grupo = '$_REQUEST[grupo]'
");

$json = array();
while($row = mysql_fetch_assoc($query)){
    
    
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
header("Content-Type: text/html; charset=ISO-8859-1",true);
?>