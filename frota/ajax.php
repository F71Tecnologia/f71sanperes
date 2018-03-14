<?php

include "../conn.php";
mysql_query ('SET character_set_client=utf8');
mysql_query ('SET character_set_connection=utf8');
mysql_query ('SET character_set_results=utf8');

	//EXECUTANDO O AJAX CARREGANDO OS BANCO DE ACORDO COM A REGIÃO SELECIONADA
	
	
	$regiao = $_REQUEST['regiao'];
	
	$result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao'");
	print "<select name='banco'>";
	while($row_banco = mysql_fetch_array($result_banco)){
		print "<option value=$row_banco[0]>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
	}

print "</select>";


?>