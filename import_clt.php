<?php

include "conn.php";

//for ($i = 15; $i <= 16; $i++) {

//$projeto = $i;

//$projeto = "1";

//$result = mysql_query("SELECT * FROM bolsista$projeto WHERE status = '1' ORDER BY nome");

//$result = mysql_query("SELECT * FROM bolsista$projeto where tipo_contratacao = '2' ORDER BY nome ASC ");

//$result = mysql_query("SELECT * FROM bolsista$projeto where tipo_contratacao != '2' ORDER BY nome ASC ");

$result = mysql_query("SELECT * FROM dependentes where id_projeto = '1' and data1 != '0000-00-00' and contratacao = '1' order by nome");

$contador = "1";

print "
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
<head><title>:: Intranet ::</title>
</head>
<body bgcolor='ffffff'>

<table width='100%' border=1 bgcolor=#CCCCCC><tr>
<td>QUANT</td>
<td>IDBOLSISTA</td>
<td>NOME</td>
<td>DATAFILHO</td>
<td>FILHO1</td>
<td>-</td>
</tr>";

while($row = mysql_fetch_array($result)){

$result2 = mysql_query("SELECT * FROM bolsista1 where nome = '$row[nome]' and id_projeto = '$row[id_projeto]'");
$row_cont2 = mysql_num_rows($result2);
$row2 = mysql_fetch_array($result2);

/*
$result_bolsista = mysql_query("Select * from abolsista$projeto where id_bolsista = '$row[0]'");
$rowa= mysql_fetch_array($result_bolsista);
*/
print "
<tr>
<td>$contador</td>
<td>$row[id_bolsista]</td>
<td>$row[nome]</td>
<td>$row[data1]</td>
<td>$row[nome1]</td>
<td>$row_cont2</td>
</tr>";

$contador ++;

/*
if($row['status'] == "0"){
	$status_atual = "0";
}else{
	$status_atual = "1";
}

mysql_query ("insert into autonomo
(id_projeto,id_bolsista,id_regiao,localpagamento,locacao,nome,sexo,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,
tel_rec,data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,titulo,zona,secao,pai,mae,estuda,
data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,id_curso,apolice,status,data_entrada,
campo1,campo2,campo3,data_exame,reservista,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,
distrato,outros,pis,dada_pis,data_ctps,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,dataalter,useralter) 
VALUES
('$row[id_projeto]','$row[0]','$row[regiao]','$row[localpagamento]','$row[locacao]','$row[nome]','$row[sexo]',
'$row[endereco]',
'$row[bairro]','$row[cidade]','$row[uf]','$row[cep]','$row[tel_fixo]','$row[tel_cel]','$row[tel_rec]',
'$row[data_nasci]','$row[naturalidade]','$row[nacionalidade]','$row[civil]','$row[rg]','$row[orgao]','$row[data_rg]',
'$row[cpf]','$row[titulo]','$row[zona]','$row[secao]','$row[pai]','$row[mae]','$row[estuda]','$row[data_escola]',
'$row[escolaridade]','$row[instituicao]','$row[curso]','$row[tipo_contratacao]','$row[banco]','$row[agencia]',
'$row[conta]','$row[id_curso]','$row[apolice]','$status_atual','$row[data_entrada]','$row[campo1]','$row[campo2]','$row[campo3]',
'$row[data_exame]','$row[reservista]','$row[cabelos]','$row[altura]','$row[olhos]','$row[peso]','$row[defeito]',
'$row[cipa]','$row[ad_noturno]','$row[plano]','$row[assinatura]','$row[distrato]','$row[outros]',
'$rowa[pis]','$rowa[dada_pis]','$rowa[data_ctps]','$rowa[fgts]','$rowa[insalubridade]','$rowa[transporte]',
'$rowa[medica]','$rowa[tipo_pagamento]','$rowa[nomebanco]','$rowa[num_filhos]','$rowa[observacao]',
'$rowa[impressos]','$row[sis_user]','$row[sis_data_cadastro]','$rowa[foto]','$rowa[dataalter]','$rowa[useralter]')") 
or die ("Erro de digitação na query" . mysql_error());

//mysql_query ("UPDATE autonomo SET data_saida = '$row[data_saida]', id_psicologia = '$row[id_psicologia]', psicologia = '$row[psicologia]', obs = '$row[obs]' WHERE id_bolsista = '$row[0]' and id_projeto = '$projeto' LIMIT 1") or die ("Erro de digitação na query<br><br>" . mysql_error());
*/
}

print "</table>";
//}

print "</body> </html>";
?>