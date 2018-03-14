<?php 
require("../../conn.php");
$charset = mysql_set_charset('utf8');
$query = mysql_query("
SELECT valor,data,comprovante,id_pg,documento FROM prestador_pg
WHERE
id_prestador = '$_REQUEST[id_prestador]'
AND status_reg = '1'
AND id_saida IS NULL
");

$json = array();
while($row = mysql_fetch_assoc($query)){
	if(!empty($row['comprovante'])){
		$row['comprovante'] =  "<a target=\"_blank\" href=\"../../processo/comprovantes/$row[id_pg].$row[comprovante]\"><img src=\"../../processo/imagensprocesso/DOC.png\" /></a>";
	}else{
		$row['comprovante'] = '';
	}
	$row['data']  = implode('/',array_reverse(explode('-',$row['data'])));
	$row['valor'] = 'R$ '. $row['valor'];
	$json[] = $row;
	//json_encode($json);
}
echo json_encode($json);
?>