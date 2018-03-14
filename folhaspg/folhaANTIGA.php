<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

$id = $_REQUEST['id'];


switch ($id){

// ----------------- FOLHA SIMPLES - INSERIR ALGUNS DADOS NA TABELA FOLHA -------------------
case 12:

//FORMATANDO DATA
function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes_pagamento'];

$data_pro = date('Y-m-d');

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);
$data_pg_f = ConverteData($data_pg);
$data_pro2 = ConverteData($data_pro);

$result_folhas_c = mysql_query("SELECT id_folha FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '1'");
$row_folhas_c = mysql_num_rows($result_folhas_c);


if($row_folhas_c == "0"){
mysql_query("INSERT INTO folhas(mes,projeto,data_pro,data_ini,data_fim,qnt_dias,tipo_folha) VALUES ('$mes','$id_projeto','$data_pro','$data_ini_f','$data_fim_f','$qnt_dias','1')");

//where data_entrada < '2009-01-01' and data_saida = '0000-00-00' and id_projeto = '10' and status = '1'

// BOLSISTA QUE ENTROU ANTES DA DATA INICIAL E NÃO SAIU
$result1 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from autonomo where data_entrada < '$data_ini_f' and data_saida = '0000-00-00' and status = '1' and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_1 = mysql_num_rows($result1);

// BOLSISTA QUE ENTROU ANTES DA DATA INICIAL E SAÍU ANTES DE FECHAR A FOLHA
$result2 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(data_saida, '%m') as mes_tal from autonomo where tipo_contratacao = '1' and data_entrada < '$data_ini_f' and data_saida <= '$data_fim_f' and data_saida > '$data_ini_f' and status = '0'  and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_2 = mysql_num_rows($result2);

// BOLSISTA QUE ENTROU DEPOIS DA DATA INICIAL
$result3 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 from autonomo where tipo_contratacao = '1' and data_entrada >= '$data_ini_f' and data_entrada < '$data_fim_f' and data_saida = '0000-00-00' and status = '1'  and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_3 = mysql_num_rows($result3);

// BOLSISTA QUE ENTROU DEPOIS DA DATA INICIAL E SAIU ANTES DE FECHAR A FOLHA
$result4 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from autonomo where tipo_contratacao = '1' and data_entrada >= '$data_ini_f' and data_saida <= '$data_fim_f' and data_saida > '$data_ini_f' and status = '0' and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_4 = mysql_num_rows($result4);

$result_folhas = mysql_query("SELECT * FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '1'");
$row_folhas = mysql_fetch_array($result_folhas);

//CRIANDO UMA NOVA TABELA PARA GUARDAR AS INFORMAÇÕES GERADAS

/*
RESULT1 = ENTROU ANTES E NÃO SAIU
RESULT2 = ENTROU ANTES E SAIU NO MEIO DO MES QUE ESTÁ GERANDO A FOLHA
RESULT3 = ENTROU DEPOIS DA DATA INICIAL DA FOLHA
RESULT4 = ENTROU DEPOIS DA DATA INICIAL DA FOLHA E SAIU ANTES DE FECHAR O MES
*/

// ------------------------------   RESULT 1   ---------------------------------------------
while ($row1 = mysql_fetch_array($result1)){

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row1[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row1[banco]','$id_projeto','$data_pro','$data_pg_f','$row1[0]','$row1[nome]','$row1[agencia]','$row1[conta]','$row1[tipo_pagamento]','1','1','1');") or die("Erro no Insert 1");
}

}
// ------------------------------   RESULT 1   ---------------------------------------------

// ------------------------------   RESULT 2   ---------------------------------------------
while ($row2 = mysql_fetch_array($result2)){

$result_curso2 = mysql_query("Select * from curso where id_curso = '$row2[id_curso]'");
$row_curso2 = mysql_fetch_array($result_curso2);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row2[banco]','$id_projeto','$data_pro','$data_pg_f','$row2[0]','$row2[nome]','$row2[agencia]','$row2[conta]','$row2[tipo_pagamento]','1','2','1');") or die("Erro no Insert 2");
}

}
// ------------------------------   RESULT 2   ---------------------------------------------

// ------------------------------   RESULT 3   ---------------------------------------------
while ($row3 = mysql_fetch_array($result3)){

$result_curso3 = mysql_query("Select * from curso where id_curso = '$row3[id_curso]'");
$row_curso3 = mysql_fetch_array($result_curso3);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row3[banco]','$id_projeto','$data_pro','$data_pg_f','$row3[0]','$row3[nome]','$row3[agencia]','$row3[conta]','$row3[tipo_pagamento]','1','3','1');") or die("Erro no Insert 3");
}

}
// ------------------------------   RESULT 3   ---------------------------------------------

// ------------------------------   RESULT 4   ---------------------------------------------
while ($row4 = mysql_fetch_array($result4)){

$result_curso4 = mysql_query("Select * from curso where id_curso = '$row4[id_curso]'");
$row_curso4 = mysql_fetch_array($result_curso4);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row4[banco]','$id_projeto','$data_pro','$data_pg_f','$row4[0]','$row4[nome]','$row4[agencia]','$row4[conta]','$row4[tipo_pagamento]','1','4','1');") or die("Erro no Insert 4");
}

}
// ------------------------------   RESULT 4   ---------------------------------------------

if($row_folhas['fim'] == "0"){
mysql_query("UPDATE folhas SET fim = '1' where mes = '$mes' and projeto = '$id_projeto'");
} else {
}

print "
<br><center>
<b><font color=#000000>Participantes<br>
Folha referente ao mes: $mes  <br>
Folha do dia $data_ini até o dia $data_fim<br>
Data de Processamento da folha - $data_pro2 <br><br>
<hr>

<a href='ver_tudo.php?id=13&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&qnt_dias=$qnt_dias&mes=$mes' style='TEXT-DECORATION: none;'>
<img src='../imagens/continuar_ver_folha.gif' border='0' align='absmiddle'>
<font color=#FFFFFF size=3>VISUALIZAR A FOLHA</a>
</font></b><br><hr><br>

PARTICIPANTES: ENTROU ANTES E NÃO SAIU $contagem_re_1<br>
PARTICIPANTES: ENTROU ANTES E SAIU NO MEIO DO MES QUE ESTÁ GERANDO A FOLHA $contagem_re_2<br>
PARTICIPANTES: ENTROU DEPOIS DA DATA INICIAL DA FOLHA $contagem_re_3<br>
PARTICIPANTES: ENTROU DEPOIS DA DATA INICIAL DA FOLHA E SAIU ANTES DE FECHAR O MES $contagem_re_4<br>

</center>
</body>
</html>";

} else {

print "<script> alert(\"Ja existe uma folha para o mes selecionado\"); </script>";
print "<br><BR><center><h1><font color=#FFFFFF>Volte e faça novamente!</font></h1></center>";
}


break;

case 13:				//MOSTRANDO A FOLHA COM O CALCULO


//FORMATANDO DATA
function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
 $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes'];

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);

$resultf = mysql_query("SELECT * FROM folha_$id_projeto where mes = '$mes' and projeto = '$id_projeto'");
$rowf = mysql_fetch_array($resultf);

$result_folhas = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro FROM folhas where mes = '$mes' and projeto = '$id_projeto' and status = '1' and tipo_folha = '1'");
$folhas = mysql_fetch_array($result_folhas);

$result1 = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro2 FROM folha_$id_projeto where mes = '$mes' order by nome ASC");

print "<br><center>
<b>
<table width='80%' border='0' cellpadding='0' cellspacing='0' background='layout/tab_folha_fundo.gif'>
  <tr>
    <td width='4%'><img src='layout/tab_folha_esquerda.gif' width='26' height='147' /></td>
    <td width='26%' valign='top'>
	<font color=#FFFFFF size=3><b>
	<br />
      Folha Referente ao Mês:<br />
      Data Processamento:<br />
      <br />
      Data Inicio:<br />
    Data Fim:</td>
	</b></font>
    <td width='22%' valign='top'>
	<font color=#FFFFFF size=3><b>
	<br />
      $mes<br />
      $folhas[data_pro]<br />
    <br />
    $data_ini<br />
    $data_fim</td>
    </b></font>
	<td width='44%' align='center' valign='middle'>
	
	<a href='acao_folha.php?id=1&id_projeto=$id_projeto&mes=$mes&regiao=$regiao&id_folha=$folhas[0]&tipo=2'
	 style='TEXT-DECORATION: none;'>
	<font color=#FFFFFF size=3><b>
	<img src='../imagens/desgerar_folha.gif' border='0' align='absmiddle'>
	DESPROCESSAR FOLHA</b></font>
	</a>
	
	<br><br>
	
	<a href='javascript:window.location.reload()' style='TEXT-DECORATION: none;'>
	<font color=#FFFFFF size=3><b>
	<img src='../imagens/atualizar_pg.gif' border='0' align='absmiddle'>
	ATUALIZAR FOLHA	</b></font>
	</a>
	</td>
	</b></font>
    <td width='4%' align='right'>
	<img src='layout/tab_folha_direita.gif' width='26' height='147' /></td>
  </tr>
</table>
<br>
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=0&sit_2=1&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao' target='_blank' style='TEXT-DECORATION: none;'>
<img src='../imagens/remover_bolsista.gif' border='0' align='absmiddle'>
<font color=#FFFFFF>
DESATIVAR TODOS DA FOLHA
</font>
</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=1&sit_2=0&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao' target='_blank' style='TEXT-DECORATION: none;'>
<img src='../imagens/adicionar_bolsista.gif' border='0' align='absmiddle'>
<font color=#FFFFFF>
ATIVAR TODOS DA FOLHA
</font>
</a>
<br>
<hr>
</font></b></center>


<table bgcolor=#FFFFFF align='center' width='97%'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center width='3%'> </td>
<td align=center width='5%'>Cód.</td>
<td align=center width='25%'>Nome</td>
<td align=center width='10%'>Salário Bruto</td>
<td align=center width='4%'>Faltas</td>
<td align=center width='4%'>Dias Trab</td>
<td align=center width='7%'>Adicional</td>
<td align=center width='7%'>Descontos</td>
<td align=center width='7%'>13º </td>
<td align=center width='7%'>Valor Diária</td>
<td align=center           >Adiantamento</td>
<td align=center width='10%'>Salário Liquido</td>
<td align=center width='10%'>Ação</td>
</tr>";

$valor_total = "0";
$linha = "";
$cont_color = "0";

while ($row1 = mysql_fetch_array($result1)){

if($cont_color % 2){ $color=""; }else{ $color="#ECF2EC"; }

$result2 = mysql_query("Select * from autonomo where id_autonomo = '$row1[7]' ");
$row2 = mysql_fetch_array($result2);

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row2[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

$result_con_ad = mysql_query("Select id_folha from folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '2' ");
$cont_adianta = mysql_num_rows($result_con_ad);

if($row1['sit'] == "0"){
  $imagem = "deletado";
  $mensagem = "Ativar";
 }else if($row1['sit'] == "1" and $row1['status'] == "2"){
  $imagem = "pago";
  $mensagem = "PAGO";
 }else if($row1['sit'] == "1"){
  $imagem = "ok";
  $mensagem = "Desativar";
}

if($row1['status'] == "2"){
  $imagem_pg = "pago";
  $link_pg = "<font color=#000000>$row1[nome]</font>";
 }else{
  $imagem_pg = "pago_n";
//href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class='link2'target='_blak'
  $link_pg = "<a href='ver_tudo.php?id=14&id_projeto=$id_projeto&mes=$mes&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><font color=#000000>$row1[nome]</font></a>";
}


$result_teste = $row1['result'];

if($cont_adianta == "1"){                // PEGANDO O ADIANTAMENTO SE HOUVER-------------------------------

$result_adianta = mysql_query("Select * from folhaad_$id_projeto where id_bolsista = $row1[id_bolsista]");
$row_adianta = mysql_fetch_array($result_adianta);

$adianta = $row_adianta['salario'];

}else{
$adianta = "0";
}

switch ($result_teste){

case 1:

$diaria = "$row_curso1[valor]" / "30";

$dias_trabalhados = "$qnt_dias" - "$row1[faltas]";
$diaria_f = number_format($diaria,2,",",".");

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";


break;
case 2:

$diaria = "$row_curso1[valor]" / "30";

$result_dias = mysql_query("SELECT COUNT(*) FROM ano where data > '$data_ini_f' and data <= '$row2[data_saida]' ");
$row_dias = mysql_fetch_array($result_dias);

$dias_trabalhados = $row_dias['0'] - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

$dias_trab = "$qnt_dias" - "$row1[faltas]";
$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

break;

case 3:

$diaria = "$row_curso1[valor]" / "30";

$result_dias_t = mysql_query("SELECT data FROM ano where data >= '$row2[data_entrada]' and data <= '$data_fim_f'");
$row_dias = mysql_num_rows($result_dias_t);

$dias_trabalhados = $row_dias - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

break;

case 4:

$diaria = "$row_curso1[valor]" / "30";

$result_dias_t = mysql_query("SELECT COUNT(*) FROM ano where data >= '$row2[data_entrada]' and data <= '$row2[data_saida]'");
$row_dias = mysql_fetch_array($result_dias_t);

$dias_trabalhados = $row_dias['0'] - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

break;


}

$valor13 = $row1['valor_13'];

if($row1['sit'] == "0"){
$valor = "0";

 }else{

}

$valor = $valor - $adianta;

$adianta = number_format($adianta,2,",",".");
$valor_for2 = number_format($valor,2,",","");
$valor_for = number_format($valor,2,",",".");
$valor_13 = number_format($valor13,2,",",".");
$valor_curso = number_format($row_curso1['valor'],2,",",".");

$valor_total = $valor_total + $valor;
$valor_total_f = number_format($valor_total,2,",",".");
print "<tr class='linha'>

<td bgcolor=$color><img src='../imagens/$imagem.gif'></td>
<td bgcolor=$color><font color=#000000>$row2[campo3]</font></td>
<td bgcolor=$color>$link_pg</td>
<td bgcolor=$color><font color=#000000>R$ $valor_curso</font></td>
<td bgcolor=$color><font color=#000000>$row1[faltas]</font></td>
<td bgcolor=$color><font color=#000000>$dias_trabalhados</font></td>
<td bgcolor=$color><font color=#000000>R$ $adicional</font></td>
<td bgcolor=$color><font color=#000000>R$ $desconto</font></td>
<td bgcolor=$color><font color=#000000>R$ $valor_13</font></td>
<td bgcolor=$color><font color=#000000>R$ $diaria_f</font></td>
<td bgcolor=$color><font color=#000000>R$ $adianta</font></td>
<td bgcolor=$color><font color=#000000>R$ $valor_for</font></td>
<td bgcolor='$color' align='center'><a href='cadastro2.php?id_cadastro=20&zokpower=323&id_projeto=$id_projeto&mes=$mes&sit_1=$row1[sit]&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]' target='_blak' class=link2>$mensagem</a></td>
</tr>";
$cont_color ++;

mysql_query("UPDATE folha_$id_projeto SET salario = '$valor_for2' where mes = '$mes' and projeto = '$id_projeto' and id_bolsista = '$row2[0]'");

}

print "</table><br><br>
<center><font color=#FFFFFF>Valor total da folha: R$ $valor_total_f</font><br><br>";

$tipo_pg_5 = mysql_query("SELECT * FROM tipopg  where id_projeto = '$id_projeto' and campo1 = '2'");
$row_tipo_pg_5 = mysql_fetch_array($tipo_pg_5);

$result_num_2 = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where sit = '1' and projeto = '$id_projeto' and mes = '$mes' and tipo_pg ='$row_tipo_pg_5[0]'"); 
$num_cheque = mysql_fetch_array($result_num_2);

print "<table border='0' cellspacing='0' cellpadding='0' class='tarefa' width=60%>
<tr bgcolor=#999999 height=26>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Nome do Banco</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Integrantes</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b> </td>
</tr>";

$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = $id_projeto");
$cont3 = "0";
while($row_banco = mysql_fetch_array($result_banco)){

$result_cont_banco = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where projeto = '$id_projeto' and banco = '$row_banco[0]' and mes = '$mes' and sit = '1'"); 

$row_cont_banco = mysql_fetch_array($result_cont_banco);

if($cont3 % 2){ $color3="#f0f0f0"; }else{ $color3="#dddddd"; }

print "<tr bgcolor=$color3>
<td class=border2>$row_banco[nome]</td>
<td class=border2> $row_cont_banco[0] Participantes </td>
<td class=border3><a href='folha_pg.php?id=data&tipo_pg=$row_banco[0]&banco=$row_banco[0]&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";

$cont3 ++;
}
print "
<tr bgcolor=#FFFEEF>
<td class=border2>Participantes que recebem em cheque</td>
<td class=border2>$num_cheque[0]</td>
<td class=border3><a href='folha_pg.php?id=2&tipo_pg=$row_pg[0]&koeiurjdpll=cheque&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";

$result_cont_outro = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where projeto = '$id_projeto' and banco = '9999' and mes = '$mes' and sit = '1'");

$row_cont_outro = mysql_fetch_array($result_cont_outro);
print "
<tr bgcolor=#FFFEEE>
<td class=border2>Outros tipos de PG </td>
<td class=border2>$row_cont_outro[0] </td>
<td class=border3><a href='folha_pg.php?id=1&tipo_pg=0&banco=0&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";


print "</center></body></html>";
break;

case 14:                       //TELA PARA CADASTRAR AS FALTAS

$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['id_projeto'];
$id_bolsista = $_REQUEST['id_bolsista'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$result_bol = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_bolsista'");
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$result_fol = mysql_query("SELECT * FROM folha_$projeto WHERE id_bolsista = '$id_bolsista' and mes = '$mes'");
$row_fol = mysql_fetch_array($result_fol);

$ver_terceiro = $row_fol['terceiro'];

if($ver_terceiro == "0"){
$mensagem = "";
}else{
$mensagem = "Este funcionário ja está recebendo o seu 13º desde o mes: $row_fol[ini_13]";
}

print "
<form action='cadastro2.php' method='post' name='form1' onSubmit=\"return validaForm()\">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='../imagens/verbolsistas.gif'> <br> <br></div><BR></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'>Projeto</td></tr>
<tr><td align='center' colspan='2'>$row_bol[nome]</td></tr>
<tr><td colspan='2' align='center'>&nbsp;</td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr>
<td align='center' colspan='2'>
<br> Faltas:&nbsp;&nbsp; 

<input name='faltas' type='text' class='campotexto' id='faltas' size='5' value='$row_fol[faltas]'>
Adicional:&nbsp;&nbsp; 

<input name='adicional' type='text' class='campotexto' id='adicional' size='10' value='$row_fol[adicional]'>&nbsp;&nbsp;
Desconto:&nbsp;&nbsp; 

<input name='desconto' type='text' class='campotexto' id='desconto' size='10' value='$row_fol[desconto]'><br><br>
<font color=red>$mensagem</font><br><br>
Pagar 13º: <input type='checkbox' name='terceiro' value='1'>&nbsp;&nbsp;
Número de Parcelas:&nbsp;&nbsp; <select name='parcelas' class='campotexto' id='parcelas'>
<option value='1'>1</option>
<option value='2'>2</option>
</select>
<br>
Selecionar mês de ínicio do pagamento: &nbsp;&nbsp;<select name='mes_pagamento' class='campotexto' id='mes_pagamento'>
<option value='01'>Janeiro</option>
<option value='02'>Fevereiro</option>
<option value='03'>Março</option>
<option value='04'>Abril</option>
<option value='05'>Maio</option>
<option value='06'>Junho</option>
<option value='07'>Julho</option>
<option value='08'>Agosto</option>
<option value='09'>Setembro</option>
<option value='10'>Outubro</option>
<option value='11'>Novembro</option>
<option value='12'>Dezembro</option>
</select><br>
</td>
</tr>
<tr>
<td align='center' colspan=2><input type='submit' name='Submit' value='Enviar' class='campotexto'>

<input type='hidden' name='id_cadastro' value='18'>
<input type='hidden' name='id_bolsista' value='$id_bolsista'>
<input type='hidden' name='projeto' value='$projeto'>
<input type='hidden' name='mes' value='$mes'>
<input type='hidden' name='id_regiao' value='$regiao'>
<input type='hidden' name='data_ini' value='$data_ini'>
<input type='hidden' name='data_fim' value='$data_fim'>
<input type='hidden' name='qnt_dias' value='$qnt_dias'>

</form>

</td>
</tr>
<tr>
<td align='center' colspan=2> &nbsp;</td>
</tr>
</table>";
break;
}
?>