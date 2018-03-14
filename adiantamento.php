<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
//
print "
<html><head><title>:: Intranet ::</title>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\"></head>
<body gbcolor=#FFFFFF>";

$id = $_REQUEST['id'];

switch ($id) {
    case 1:

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
$id_projeto = $_REQUEST['projeto'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes_pagamento'];

$data_pro = date('Y-m-d');

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);
$data_pg_f = ConverteData($data_pg);
$data_pro2 = ConverteData($data_pro);

// BOLSISTA QUE ENTROU ANTES DA DATA INICIAL E NÃO SAIU
$result1 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from bolsista$id_projeto 
where tipo_contratacao = '1' and data_entrada <= '$data_ini_f' and data_saida = '0000-00-00' and status = '1' ORDER BY nome") or die ("Erro<br><br>" . mysql_error());

// BOLSISTA QUE ENTROU DEPOIS DA DATA INICIAL E NÃO SAIU
/*
$result2 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from bolsista$id_projeto 
where tipo_contratacao = '1' and data_entrada > '$data_ini_f' and data_entrada <= '$data_fim_f' and data_saida = '0000-00-00' and status = '1' ORDER BY nome") or die ("Erro<br><br>" . mysql_error());
*/

$result_folhas_c = mysql_query("SELECT id_folha FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '2'");
$row_folhas_c = mysql_num_rows($result_folhas_c);

if($row_folhas_c == "0"){
mysql_query("INSERT INTO folhas(mes,projeto,data_pro,data_ini,data_fim,tipo_folha) VALUES ('$mes','$id_projeto','$data_pro','$data_ini_f','$data_fim_f','2')");

$result_folhas = mysql_query("SELECT * FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '2'");
$row_folhas = mysql_fetch_array($result_folhas);

//CRIANDO UMA NOVA TABELA PARA GUARDAR AS INFORMAÇÕES GERADAS

/*
RESULT1 = ENTROU ANTES E NÃO SAIU
*/

// ------------------------------   RESULT 1   ---------------------------------------------
while ($row1 = mysql_fetch_array($result1)){

$aresult1 = mysql_query("Select * from abolsista$id_projeto where id_bolsista = '$row1[0]' ");
$arow1 = mysql_fetch_array($aresult1);

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row1[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folhaad_$id_projeto(id_folhas,mes,banco,projeto,data_pro,id_bolsista,agencia,conta,tipo_pg,sit,result,status) VALUES
('$row_folhas[0]','$mes','$row1[banco]','$id_projeto','$data_pro','$row1[0]','$row1[agencia]','$row1[conta]','$arow1[tipo_pagamento]','1','1','1');") or die("Erro no Insert1<br><br>". mysql_error());
}else{
}
}
// ------------------------------   RESULT 1   ---------------------------------------------

/* ------------------------------   RESULT 2   ---------------------------------------------
while ($row2 = mysql_fetch_array($result2)){

$aresult2 = mysql_query("Select * from abolsista$id_projeto where id_bolsista = '$row1[0]' ");
$arow2 = mysql_fetch_array($aresult2);

$result_curso2 = mysql_query("Select * from curso where id_curso = '$row1[id_curso]'");
$row_curso2 = mysql_fetch_array($result_curso2);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folhaad_$id_projeto(id_folhas,mes,banco,projeto,data_pro,id_bolsista,agencia,conta,tipo_pg,sit,result,status) VALUES
('$row_folhas[0]','$mes','$row2[banco]','$id_projeto','$data_pro','$row2[0]','$row2[agencia]','$row2[conta]','$arow2[tipo_pagamento]','1','2','1');") or die("Erro no Insert2<br><br>". mysql_error());
}else{
}
}
// ------------------------------   RESULT 2   ---------------------------------------------
*/

if($row_folhas['fim'] == "0"){
mysql_query("UPDATE folhas SET fim = '1' where mes = '$mes' and projeto = '$id_projeto'");
} else {
}

print "<br><center>
<b><font color=#000000>Bolsistas<br>
Adiantamento referente ao mes: $mes  <br>
Adiantamento do dia $data_ini ao dia $data_fim<br>
Data de Processamento do Adiantamento - $data_pro2 <br><br>
<hr>

<a href='adiantamento.php?id=2&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&mes=$mes' style='TEXT-DECORATION: none;'>
<img src='imagens/continuar_ver_folha.gif' border='0' align='absmiddle'>
<font color=#FFFFFF size=3>VISUALIZAR A ADIANTAMENTO</a>
</font></b></center>
</body>
</html>";

} else {
print "<script> alert(\"Ja existe adiantamento para o mes selecionado\"); </script>";
print "<br><BR><center><h1><font color=#FFFFFF>Volte e fa�a novamente!</font></h1></center>";

}


break;

//----------------------------- VENDO O ADIANTAMENTO ----------------------------------
case 2:

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
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes'];

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);

$result = mysql_query("SELECT * FROM folhaad_$id_projeto where mes = '$mes' and projeto = '$id_projeto'")or die("erro<br>".mysql_error());

$result_folhas = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro FROM folhas where mes = '$mes' and projeto = '$id_projeto' and status = '1' and tipo_folha = '2'");
$folhas = mysql_fetch_array($result_folhas);

$result1 = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro2 FROM folha_$id_projeto where mes = '$mes'");

print "<br><center>
<b>
<table width='80%' border='0' cellpadding='0' cellspacing='0' background='layout/tab_folha_fundo.gif'>
  <tr>
    <td width='4%'><img src='layout/tab_folha_esquerda.gif' width='26' height='147' /></td>
    <td width='26%' valign='top'>
	<font color=#FFFFFF size=3><b>
	<br />
      Adiantamento do Mes:<br />
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
	<a href='acao_folha.php?id=1&id_projeto=$id_projeto&mes=$mes&regiao=$regiao&id_folha=$folhas[0]&tipo=1'
	 style='TEXT-DECORATION: none;'>
	<font color=#FFFFFF size=3><b>
	<img src='imagens/desgerar_folha.gif' border='0' align='absmiddle'>
	DESPROCESSAR ADIANTAMENTO
	</b></font>
	</a>
	</td>
	<td width='4%' align='right'>
	<img src='layout/tab_folha_direita.gif' width='26' height='147' /></td>
  </tr>
</table>
<br>
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=0&sit_2=1&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&tabela=ad' target='_blank' style='TEXT-DECORATION: none;'>
<img src='imagens/remover_bolsista.gif' border='0' align='absmiddle'>
<font color=#FFFFFF>
DESATIVAR TODOS DA FOLHA
</font>
</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=1&sit_2=0&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&tabela=ad' target='_blank' style='TEXT-DECORATION: none;'>
<img src='imagens/adicionar_bolsista.gif' border='0' align='absmiddle'>
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
<td align=center width='5%'>Cod.</td>
<td align=center width='25%'>Nome</td>
<td align=center width='10%'>Salario Bruto</td>
<td align=center width='7%'>Adicional</td>
<td align=center width='7%'>Descontos</td>
<td align=center width='10%'>Adiantamento</td>
<td align=center width='10%'>Acao</td>
</tr>";

$valor_total = "0";
$linha = "";
$cont_color = "0";
while ($row1 = mysql_fetch_array($result)){

if($cont_color % 2){ $color="linhan"; }else{ $color="linha"; }

$result2 = mysql_query("Select * from bolsista$id_projeto where id_bolsista = '$row1[7]' ");
$row2 = mysql_fetch_array($result2);

$aresult1 = mysql_query("Select * from abolsista$id_projeto where id_bolsista = '$row1[7]' ");
$arow1 = mysql_fetch_array($aresult1);

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row2[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

if($row1['sit'] == "0"){
  $imagem = "deletado";
  $mensagem = "Ativar";
 }else{
  $imagem = "ok";
  $mensagem = "Desativar";
}

// inicio do calculo 1
$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

if($row1['result'] == "1"){

$valor = "$row_curso1[valor]" * "0.4" + "$adicional_c" - "$desconto_c";
$row_dias = " ";

}else{

$result_dias = mysql_query("SELECT id_ano FROM ano where data >= '$row1[data_entrada]' and data <= '$data_fim_f' ");
$row_dias = mysql_num_rows($result_dias);

$diaria1 = "$row_curso1[valor]" / "30";    // valor diaria
$diaria2 = "$row_dias" * "$diaria1";       // diaria vezes quantidade de dias trabalhados
$valor = "$diaria2" * "0.4" + "$adicional_c" - "$desconto_c";       // dias trabalhados - 40% com adiciona e desconto

}

if($row1['sit'] == "0"){ $valor = "0"; }

$valor_for2 = number_format($valor,2,",","");
$valor_for = number_format($valor,2,",",".");
$valor_curso = number_format($row_curso1['salario'],2,",",".");

$valor_total = $valor_total + $valor;
$valor_total_f = number_format($valor_total,2,",",".");



print "
<tr onmouseover=\"this.className='table_over'\" onmouseout=\"this.className='$color'\" class='$color'>
<td><img src='imagens/$imagem.gif'></td>
<td><font color=#000000>$row2[campo3]</font></td>
<td><a href='adiantamento.php?id=3&id_projeto=$id_projeto&mes=$mes&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]' target='_blak'><font color=#000000>$row2[nome]</font></a></td>
<td><font color=#000000>R$ $valor_curso</font></td>
<td><font color=#000000>R$ $adicional</font></td>
<td><font color=#000000>R$ $desconto</font></td>
<td><font color=#000000>R$ $valor_for</font></td>
<td align='center'><a href='cadastro2.php?id_cadastro=20&zokpower=322&id_projeto=$id_projeto&mes=$mes&sit_1=$row1[sit]&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]&tipo_tabela=1' target='_blak' class=link2>$mensagem</a></td>
</tr>";
$cont_color ++;

mysql_query("UPDATE folhaad_$id_projeto SET salario = '$valor_for2' where mes = '$mes' and projeto = '$id_projeto' and id_bolsista = '$row2[id_bolsista]'");

}

print "</table><br><br>
<center><font color=#FFFFFF>$cont_color participantes</font><br><br>
<center><font color=#FFFFFF>Valor total da folha: R$ $valor_total_f</font><br><br>";

$tipo_pg_5 = mysql_query("SELECT * FROM tipopg  where id_projeto = '$id_projeto' and campo1 = '2'");
$row_tipo_pg_5 = mysql_fetch_array($tipo_pg_5);

print "<table border='0' cellspacing='0' cellpadding='0' class='tarefa' width=60%>
<tr bgcolor=#999999 height=26>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Nome do Banco</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Integrantes</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b> </td>
</tr>";

$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = $id_projeto");
$cont3 = "0";
while($row_banco = mysql_fetch_array($result_banco)){

$result_cont_banco = mysql_query("SELECT id_bolsista FROM folhaad_$id_projeto where projeto = '$id_projeto' and banco = '$row_banco[0]' and mes = '$mes' and sit = '1'"); 
$row_cont_banco = mysql_num_rows($result_cont_banco);

if($cont3 % 2){ $color3="#f0f0f0"; }else{ $color3="#dddddd"; }

print "<tr bgcolor=$color3>
<td class=border2>$row_banco[nome]</td>
<td class=border2> $row_cont_banco Bolsistas </td>
<td class=border3><a href='folha_pg.php?id=data&tipo_pg=$row_banco[0]&banco=$row_banco[0]&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto&adicional=1' target='_blank'>Pagar</a></td></tr>";

$cont3 ++;
}
$result_num_2 = mysql_query("SELECT id_bolsista FROM folhaad_$id_projeto where sit = '1' and projeto = '$id_projeto' and mes = '$mes' and tipo_pg = '$row_tipo_pg_5[0]'"); 
$num_cheque = mysql_num_rows($result_num_2);

print "
<tr bgcolor=#FFFEEF>
<td class=border2>Bolsistas que recebem em cheque</td>
<td class=border2>$num_cheque</td>
<td class=border3><a href='folha_pg.php?id=2&tipo_pg=$row_tipo_pg_5[0]&koeiurjdpll=cheque&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto&adicional=1' target='_blank'>Pagar</a></td></tr>";

$result_cont_outro = mysql_query("SELECT id_bolsista FROM folhaad_$id_projeto where projeto = '$id_projeto' 
and banco != '$row_banco[0]' and banco = '0' and tipo_pg != '$row_tipo_pg_5[0]' and mes = '$mes' and sit = '1'"); 
$row_cont_outro = mysql_num_rows($result_cont_outro);
print "
<tr bgcolor=#FFFEEE>
<td class=border2>Outros tipos de PG </td>
<td class=border2>$row_cont_outro </td>
<td class=border3><a href='folha_pg.php?id=1&tipo_pg=0&banco=0&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto&adicional=1' target='_blank'>Pagar</a></td></tr>";


print "</center></body></html>";


break;

case 3:           //-------------- JOGANDO OS ADICIONAIS OU DESCONTOS ---------------

$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['id_projeto'];
$id_bolsista = $_REQUEST['id_bolsista'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$result_bol = mysql_query("SELECT * FROM bolsista$projeto WHERE id_bolsista = '$id_bolsista'");
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$result_fol = mysql_query("SELECT * FROM folhaad_$projeto WHERE id_bolsista = '$id_bolsista' and mes = '$mes'");
$row_fol = mysql_fetch_array($result_fol);

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body bgcolor='#D7E6D5'>

<form action='adiantamento.php' method='post' name='form1' onSubmit=\"return validaForm()\">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/verbolsistas.gif'> <br> <br></div><BR></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'>Participante<br><br></td></tr>
<tr><td align='center' colspan='2'>$row_bol[nome]</td></tr>
<tr><td colspan='2' align='center'>&nbsp;</td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr>
<td align='center' colspan='2'>
<br> 
Adicional:&nbsp;&nbsp; <input name='adicional' type='text' class='campotexto' id='adicional' size='10'>&nbsp;&nbsp;
Desconto:&nbsp;&nbsp; <input name='desconto' type='text' class='campotexto' id='desconto' size='10'>
<br><br><br>
</td>
</tr>
<tr>
<td align='center' colspan=2><input type='submit' name='Submit' value='Enviar' class='campotexto'>

<input type='hidden' name='id' value='4'>
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

case 4:           //-------------- CADASTRANDO OS ADICIONAIS OU DESCONTOS ---------------

$id_regiao = $_REQUEST['id_regiao'];
$id_bolsista = $_REQUEST['id_bolsista'];
$id_projeto = $_REQUEST['projeto'];
$adicional = $_REQUEST['adicional'];
$desconto = $_REQUEST['desconto'];
$mes = $_REQUEST['mes'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$adicional = str_replace(".", "", $adicional);
$desconto = str_replace(".", "", $desconto);

$result = mysql_query ("SELECT * FROM bolsista$id_projeto WHERE id_bolsista = '$id_bolsista'") or die ("Erro<br><br>".mysql_error());
$row = mysql_fetch_array($result);

$result_cur = mysql_query ("SELECT * FROM curso WHERE id_curso = '$row[id_curso]'") or die ("Erro no SELECT 2");
$row_cur = mysql_fetch_array($result_cur);

if($row['tipo_contratacao'] == "2"){
print "<br><br><center><span class='style27'>O Código digitado pertence a um CLT e não a um BOLSISTA.<br> $row[nome]
</span><br><br><a href='ver_tudo.php?id=9&regiao=$id_regiao&id_projeto=$id_projeto'><img src='imagens/voltar.gif' border=0></a><center>";

}else{

mysql_query("UPDATE folhaad_$id_projeto SET adicional = '$adicional', desconto =  '$desconto' WHERE id_bolsista = '$id_bolsista' and mes = '$mes'") or die ("Erro no Insert<hr>".mysql_error()); 

print "
<script>
function fechar_janela(x) {
opener.location.href =
'adiantamento.php?id=2&id_projeto=$id_projeto&regiao=$id_regiao&data_ini=$data_ini&data_fim=$data_fim&mes=$mes';
return eval(x)
}
</script>
";
print "<br><br><center><span class='style27'><br> Informações gravadas com sucesso! </span><br><br><a href='javascript:fechar_janela(window.close())'><img src='imagens/voltar.gif' border=0></a><center>";
}

break;

}


}
?>