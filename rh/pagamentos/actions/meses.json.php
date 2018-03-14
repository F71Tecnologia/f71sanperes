<?php 
include "../../../conn.php";
$charset = mysql_set_charset('utf8');
$query = mysql_query("SELECT * FROM ano_meses 
				ORDER BY num_mes
				");
			
while($row = mysql_fetch_assoc($query)){
	$array[] = $row;
}

echo json_encode($array);
?>