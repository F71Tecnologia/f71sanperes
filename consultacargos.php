<?php

include "conn.php";

$curso = $_REQUEST["curso"];

$sql = "SELECT * FROM rh_clt WHERE id_projeto=3302 order by id_curso asc";
$qry = mysql_query($sql);
?>

<table border=1 cellpadding="0" cellspacing="0">
<tr>

<td>NOME</td>
<td>IDENTIDADE</td>
<td>ORGÃO</td>
<td>DATA EMISSAO</td>
<td>PIS</td>
<td>NASCIMENTO</td>
<td>FUNCAO</td>

<!--
<td>NOME</td>
<td>PAI</td>
<td>SERIE_CTPS</td>
<td>DATA_CTPS</td>
<td>RG</td>
<td>ORGAO</td>
<td>DATA_RG</td>
<td>TITULO</td>
<td>ZONA</td>
<td>SECAO</td>
<td>CIVIL</td>
<td>FUNCAO</td>


<td>IDENTIDADE</td>
<td>ORGAO</td>
<td>CPF</td>
<td>NASCIMENTO</td>
<td>FUNÇÃO</td>
-->

</tr>

<?php

while ($dados=mysql_fetch_assoc($qry))
{
	
$sql2 = "SELECT * FROM curso WHERE id_curso=".$dados['id_curso'];
$qry2 = mysql_query($sql2);
$dados2 = mysql_fetch_assoc($qry2);
	
	echo "<tr>";
	echo "<td>".$dados['nome']."</td>";
	echo "<td>".$dados['rg']."</td>";
	echo "<td>".$dados['orgao']."</td>";
	echo "<td>".$dados['data_rg']."</td>";
	echo "<td>".$dados['pis']."</td>";
	echo "<td>".$dados['data_nasci']."</td>";
	echo "<td>".$dados2['nome']."</td>";

	/*
	echo "<td>".$dados['nome']."</td>";
	echo "<td>".$dados['pai']."</td>";
	echo "<td>".$dados['serie_ctps']."</td>";
	echo "<td>".$dados['data_ctps']."</td>";
	echo "<td>".$dados['rg']."</td>";
	echo "<td>".$dados['orgao']."</td>";
	echo "<td>".$dados['data_rg']."</td>";
	echo "<td>".$dados['titulo']."</td>";
	echo "<td>".$dados['zona']."</td>";
	echo "<td>".$dados['secao']."</td>";
	echo "<td>".$dados['civil']."</td>";	
	echo "<td>".$dados2['nome']."</td>";
	*/
	echo "</tr>";

	
	
}
?>
</table>
