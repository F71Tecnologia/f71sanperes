<?php 
require("../../conn.php");
$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT * 
FROM  `entradaesaida_nomes` 
WHERE id_entradasaida = '$_REQUEST[tipo]' 
ORDER BY nome
");

$json = array();
while($row = mysql_fetch_assoc($query)){
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
?>