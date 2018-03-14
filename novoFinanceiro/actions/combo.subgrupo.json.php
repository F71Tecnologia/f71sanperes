<?php 
include("../../conn.php");
$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT * 
FROM  `entradaesaida_subgrupo` 
WHERE entradaesaida_grupo = '$_REQUEST[grupo]' 
ORDER BY nome
");

$json = array();
while($row = mysql_fetch_assoc($query)){
    
    //if($row['id'] == 3) continue;
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
?>