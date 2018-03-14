<?php

include "conn.php";

$result_fe = mysql_query("SELECT * FROM rh_clt WHERE sexo = 'F'");
$total_fe = mysql_num_rows($result_fe);

$result_ma = mysql_query("SELECT * FROM rh_clt WHERE sexo = 'M'"); 
$total_ma = mysql_num_rows($result_ma);

$soma = $total_ma + $total_fe;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>
<body>

<?php

print "
INTEGRANTES DO SEXO FEMININO <BR><BR>
<TABLE cellpadding='3' border='1'>
<TR align='center' bgcolor='green'><TD>NOME</TD><TD>CPF</TD><TD>SEXO</TD></TR>";

while($row_fe = mysql_fetch_array($result_fe)){

print "<tr><td>$row_fe[nome]</td><td>$row_fe[cpf]</td><td>$row_fe[sexo]</td></tr>";
}

print "</table><br>total feminino = $total_fe";


print "
<BR><BR>INTEGRANTES DO SEXO MASCULINO <BR><BR>
<TABLE cellpadding='3' border='1'>
<TR align='center' bgcolor='green'><TD>NOME</TD><TD>CPF</TD><TD>SEXO</TD></TR>";

while($row_ma= mysql_fetch_array($result_ma)){

print "<tr><td>$row_ma[nome]</td><td>$row_ma[cpf]</td><td>$row_ma[sexo]</td></tr>";
}

print "</table><br> total masculino = $total_ma <br><br>";

echo "A soma de $total_ma com $total_fe é $soma ! <br>"; 

?>
</body>
</html>