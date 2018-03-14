<?php 

require("../../conn.php");
$sql = "SELECT fun.id_funcionario, reg.regiao, fun.nome FROM funcionario AS fun, regioes AS reg WHERE reg.id_regiao = fun.id_regiao AND fun.nome LIKE '%".$_REQUEST['funcionario']."%' OR fun.login = '%".$_REQUEST['funcionario']."%';";
$query = mysql_query($sql); 

header("content-type: text/xml");

$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
$xml .= "\r<dados>\n";

while($row = mysql_fetch_assoc($query)){
	$xml .= "\r<funcionario>\n";
	$xml .= "\r<id_funcionario>".$row['id_funcionario']."</id_funcionario>\n";
	$xml .= "\r<nome>".$row['nome']."</nome>\n";
	$xml .= "\r<regiao>".$row['regiao']."</regiao>\n";
	$xml .= "\r</funcionario>\n";
}

$xml .= "\r</dados>\n";

echo $xml;

?>