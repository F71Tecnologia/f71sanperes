<?php 
require("../../conn.php");
$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT * 
FROM  prestadorservico 
WHERE id_regiao = '$_REQUEST[regiao]' AND
id_projeto = '$_REQUEST[projeto]' 
 AND status = 1 AND encerrado_em >= CURRENT_DATE()
ORDER BY c_fantasia
");


$json = array();
while($row = mysql_fetch_assoc($query)){
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
?>