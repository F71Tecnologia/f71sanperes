<?php

include "conn.php";

$projeto = $_REQUEST['projeto'];

//$result = mysql_query("SELECT * FROM bolsista$projeto WHERE status = '1' ORDER BY nome");

$result = mysql_query("SELECT * FROM bolsista$projeto where status = '1' ORDER BY locacao ASC");

print "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<head><title>:: Intranet ::</title>
</head>
<body bgcolor='ffffff'>

<table><tr>
<td>Nome</td>
<td>CÓDIGO</td>
<td>Locacao</td>
</tr>";

while($row = mysql_fetch_array($result)){

$result_bolsista = mysql_query("Select * from abolsista$projeto where id_bolsista = '$row[0]'");
$rowa = mysql_fetch_array($result_bolsista);

/*
$result_tv = mysql_query("Select * from tvsorrindo where id_bolsista = '$row[0]'");
$row_tv = mysql_fetch_array($result_tv);
*/

print "
<tr>
<td>$row[nome]</td>
<td>$row[campo3]</td>
<td>$row[locacao]</td>
</tr>";

}

print "</table> </body> </html>";

?>