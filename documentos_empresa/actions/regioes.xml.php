<?php
require("../../conn.php");

$sql = "SELECT regiao, id_regiao FROM regioes WHERE status = 1;";
$query = mysql_query($sql);
header("content-type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
$xml .= "\r<dados>\n";
while($row = mysql_fetch_assoc($query)){
	$xml .= "\r<regioes>\n";
	$xml .= "\r<id_regiao>".$row['id_regiao']."</id_regiao>\n";
	$xml .= "\r<regiao>".$row['regiao']."</regiao>\n";
	$xml .= "\r</regioes>\n";
}
$xml .= "\r</dados>\n";
echo $xml;

?>