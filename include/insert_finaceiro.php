<?php 

require("../conn.php");

	mysql_query("INSERT INTO saida	(id_regiao,id_projeto,id_banco,id_user,nome,especifica,tipo,adicional,valor,data_proc,data_vencimento,comprovante,tipo_arquivo) VALUES
('','','','','','','','','','','','','')");
	
	$query = mysql_query("SELECT MAX(id_saida) FROM saida");
	$ultimo = mysql_result($query,0);
	
	echo $ultimo;
?>