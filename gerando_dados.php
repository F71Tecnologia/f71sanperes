<?php

include "conn.php";


$result = mysql_query("SELECT nome,endereco,bairro,cidade,data_nasci,tel_fixo,cpf,rg,locacao,id_curso,date_format(data_entrada, '%d/%m/%Y') as data_entrada FROM bolsista14 WHERE status = '1' ORDER BY unidade,nome");

print "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<head><title>:: Intranet ::</title>
</head>
<body bgcolor='ffffff'>

<table><tr>
<td>Nome</td>
<td>Endereço</td>
<td>Data de Nascimento</td>
<td>Telefone</td>
<td>CPF</td>
<td>RG</td>
<td>Locação</td>
<td>Salário</td>
<td>Data de Entrada</td>
<td>Data Formatada</td>
</tr>";

while($row = mysql_fetch_array($result)){

$result_curso = mysql_query("Select * from curso where id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$d = explode("/", $row['data_entrada']);

$dia = $d[0];
$mes = $d[1];
$ano = $d[2];

switch ($mes) {
case 1:
$mes = "Janeiro";
break;
case 2:
$mes = "Fevereiro";
break;
case 3:
$mes = "Março";
break;
case 4:
$mes = "Abril";
break;
case 5:
$mes = "Maio";
break;
case 6:
$mes = "Junho";
break;
case 7:
$mes = "Julho";
break;
case 8:
$mes = "Agosto";
break;
case 9:
$mes = "Setembro";
break;
case 10:
$mes = "Outubro";
break;
case 11:
$mes = "Novembro";
break;
case 12:
$mes = "Dezembro";
break;
}


print "
<tr>
<td>$row[nome]</td>
<td>$row[endereco], $row[bairro] - $row[cidade]</td>
<td>$row[data_nasci]</td>
<td>$row[tel_fixo]</td>
<td>$row[cpf]</td>
<td>$row[rg]</td>
<td>$row[locacao]</td>
<td>$row_curso[valor]</td>
<td>$row[data_entrada]</td>
<td>Bom Jardim, $dia de $mes de $ano</td>
</tr>";

}

print "</table> </body> </html>";

?>