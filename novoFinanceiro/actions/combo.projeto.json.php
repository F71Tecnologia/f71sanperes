<?php 
require("../../conn.php");
$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT id_projeto, nome 
FROM  projeto 
WHERE id_regiao = '$_REQUEST[regiao]'
");

$json = array();
while($row = mysql_fetch_assoc($query)){
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
?>