<?php 
include "../../../conn.php";

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$charset = mysql_set_charset('utf8');
$query = mysql_query("SELECT regioes.id_regiao, regioes.regiao
						  FROM (rh_folha INNER JOIN projeto ON rh_folha.projeto = projeto.id_projeto)
						  INNER JOIN regioes ON rh_folha.regiao = regioes.id_regiao
						 WHERE 
						   rh_folha.status = '3' 
						   AND rh_folha.mes = '$mes' 
						   AND rh_folha.ano = '$ano'
						   AND regioes.id_regiao != '36'
						   ORDER BY rh_folha.id_folha DESC");
while($row = mysql_fetch_assoc($query)){
	$array[] = $row;
}

echo json_encode($array);
?>