<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

//PEGANDO O ID DO CADASTRO
$id = 1;
$id_bol = $_REQUEST['bol'];
$tabela = $_REQUEST['tab'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['reg'];

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data, date_format(data_saida, '%d/%m/%Y')as data_saida2 FROM $tabela where id_bolsista = $id_bol ", $conn);
$row = mysql_fetch_array($result);

$result_tab = mysql_query(" SELECT * FROM projeto where id_projeto = $id_pro ", $conn);
$row_tab = mysql_fetch_array($result_tab);

$result_ban = mysql_query(" SELECT * FROM bancos where id_regiao = $id_reg ", $conn);

if ($row['tipo_contratacao'] == "1"){
$tipo_contra = "
<center>
<a href='alter_bolsista.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro' class='link'><img src='imagens/editar_bolsista.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='contrato.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/gerar_contrato.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='distrato.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/gerar_distrato.gif' border=0></a>
<BR>
<a href='tvsorrindo.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro' class='link' target='_blak'><img src='imagens/tvsorrindo.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='matricula.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/matricula.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href=declararenda.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/declara_renda.gif' border=0></a>
</center>";
}else{
$tipo_contra = "
<center>
<a href='alter_bolsista.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro' class='link'><img src='imagens/editar_bolsista.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='tvsorrindo.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro' class='link' target='_blak'><img src='imagens/tvsorrindo.gif' border=0></a>
&nbsp;&nbsp;&nbsp;
<a href='contrato_clt.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/cadastrofuncionario.gif' border=0></a>
<br>
<a href=declararenda.php?bol=$row[id_bolsista]&tab=bolsista$id_pro&pro=$id_pro&id_reg=$id_reg' class='link' target='_blak'><img src='imagens/declara_renda.gif' border=0></a>

</center>";
}

if($row['status'] =="0"){
$texto = "<font color=red>Data de saída: $row[data_saida2]</font>";
}else{
$texto = "";
}

// INICIO DO PÁGINA QUE RODA EM TODOS OS TIPOS DE CADASTRO
print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<style type=\"text/css\">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}
.style5 {color: #FF0000}
.style6 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style11 {font-weight: bold}
.style13 {font-weight: bold}
.style15 {font-weight: bold}
.style17 {font-weight: bold}
.style19 {font-weight: bold}
.style23 {font-weight: bold}
body {
	background-color: #5C7E59;
}
.style24 {
	font-size: 10px;
	font-weight: bold;
	color: #003300;
}
.style25 {color: #003300}
.style26 {
	color: #FFFFFF;
	font-size: 10px;
}
.style27 {color: #FFFFFF; }
-->
</style>
</head>";
print "
<script language=\"JavaScript\">
<!--
limite=250;
function soma() {
	var mais_um=eval(document.form.caracteres.value.length-1);
	mais_um++;
	if (document.form.caracteres.value.length>limite) {
		document.form.caracteres.value='';
		document.form.caracteres.value=valor_limite;
		alert(\"Limite de \"+limite+\" caracteres\");
	}else{
		  document.form.exibe.value='';
		  document.form.exibe.value=eval(mais_um);
		  valor_limite=document.form.caracteres.value;
		  document.form.exibe2.value='';
          document.form.exibe2.value=(limite-mais_um);
	}
	document.form.caracteres.focus();
}
function mostra_tamanho(){
	document.form.exibe2.value=limite;
	document.span.exibe2.value=limite;
}
function Contar(Campo){
document.getElementById(\"Qtd\").innerText = 250-Campo.value.length
if((250-Campo.value.length)==0)
alert('Atenção, você atingiu o limite máximo de caracteres!');
}
//-->
</script>";

print "
<body bgcolor='#D7E6D5'>
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/verbolsistas.gif'> <br> <br></div><BR></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'>Bolsista</td></tr>
<tr><td align='center' colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2' align='center'><font size=3> $row[nome]</font></td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr>
<td align='center' colspan='2'>Data de Cadastro: $row[nova_data]  $texto<br>Projeto:&nbsp;&nbsp; $row_tab[nome]</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td>&nbsp;&nbsp; &nbsp;</td>
</tr>
<tr>
<td align='center' colspan=2> &nbsp;</td>
</tr>
</table>
<br>
$tipo_contra

<form action='declarabancos.php' method='post' name='form1' target='_blanc'>
<br><br><center><font color=#ffffff><b>Escolha o Banco:</b></font>&nbsp;&nbsp;&nbsp;<select name=banco id=banco>";

while($row_ban = mysql_fetch_array($result_ban)){
  print "<option value=$row_ban[id_banco]>$row_ban[nome]</option>";
  };

print "<input type='hidden' name='bolsista' id='bolsista' value=$id_bol><input type='hidden' name='tabela' id='tabela' value=$tabela><input type='hidden' name='regiao' id='regiao' value=$id_reg>";
print "</select>&nbsp;&nbsp;&nbsp;<input type=submit value='Gerar Encaminhamento de Conta'></center></form>
<br><a href='bolsista.php?projeto=$id_pro&regiao=$id_reg' class='link'><img src='imagens/voltar.gif' border=0></a>";

print "
</body>
</html>";


}
?>