<?PHP
include "conn.php";

$regiao = $_REQUEST['regiao'];

print "<table border=1>";
$result_saidas = mysql_query("SELECT * FROM saida WHERE id_regiao = '$regiao'");
while($row_saidas = mysql_fetch_array($result_saidas)){
 $valor = number_format($row_saidas['valor'],2,",",".");
 print "<tr><td>$row_saidas[nome]</td><td>$valor</td><td>$row_saidas[adicional]</td></tr>";
 }
?>