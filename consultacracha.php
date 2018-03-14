<?php

include "conn.php";
include "codbarras.php";

mysql_query("SET NAMES 'utf8'");
mysql_query('SET character_set_connection=utf8');
mysql_query('SET character_set_client=utf8');
mysql_query('SET character_set_results=utf8');

$curso = $_REQUEST["curso"];

$sql = "SELECT * FROM rh_clt WHERE id_projeto=3302 order by nome asc";
$qry = mysql_query($sql);


?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<table border=1 cellpadding="0" cellspacing="0">
<tr>
<td>FOTO</td>
<td>MATRICULA</td>
<td>NOME</td>
<td>FUNÇÃO</td>
<td>CODIGO DE BARRAS</td>
</tr>



<?php

while ($dados=mysql_fetch_assoc($qry))
{
	$codbarras = sprintf("%09d", $dados['matricula']);
	
	
if ($dados['id_curso'] == '1375')	{$curso = "MÉDICO CLINICO";}
if ($dados['id_curso'] == '1374')	{$curso = "MÉDICO CLINICO";}
if ($dados['id_curso'] == '1439')	{$curso = "AUXILIAR ADMINISTRATIVO";}


	

$sql2 = "SELECT * FROM curso WHERE id_curso=".$dados['id_curso'];
$qry2 = mysql_query($sql2);
$dados_curso = mysql_fetch_assoc($qry2);
	echo "<tr>";
	echo "<td><img src='./fotosclt/".$dados['id_regiao']."_".$dados['id_projeto']."_".$dados['id_clt'].".gif'></td>";
	echo "<td>".$dados['matricula']."</td>";
	echo "<td>".$dados['nome']."</td>";
	
	
	if ($dados['id_curso'] == '1375' or $dados['id_curso'] == '1374' or $dados['id_curso'] == 1439)
	{
	$valor=$curso;
	}else{	
	$valor = $dados_curso['nome'];
	}
	echo "<td>".$valor."</td>";
	echo "".fbarcode($codbarras)."";
	
	

	echo "</tr>";

	
	
}
?>
</table>
