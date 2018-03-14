<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{


include "conn.php";

$id = $_REQUEST['id'];

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net2.css\" rel=\"stylesheet\" type=\"text/css\">
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

switch ($id){

case 1:									//DESGERAR FOLHA
if(empty($_REQUEST['confirmacao'])){

$id_folha = $_REQUEST['id_folha'];
$projeto = $_REQUEST['id_projeto'];
$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];
$tipo = $_REQUEST['tipo'];

if($tipo == "1"){
$referente = "Adicional";
}else{
$referente = "Pagamento";
}

print "
<center>
<font color=#FFFFFF size=3>
$referente referente ao mês: $mes<br><br>
Deseja realmente apagar esta folha de $referente?
<br><BR>
<a href='acao_folha.php?id=1&id_projeto=$projeto&mes=$mes&regiao=$regiao&id_folha=$id_folha&confirmacao=1&tipo=$tipo' 
style='TEXT-DECORATION: none;'>
SIM</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='javascript:history.go(-1)' style='TEXT-DECORATION: none;'>
NÃO
</a>
</font>
</center>
";

}else{

$id_folha = $_REQUEST['id_folha'];
$projeto = $_REQUEST['id_projeto'];
$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];
$tipo = $_REQUEST['tipo'];

if($tipo == "1"){
$tabela = "folhaad_$projeto";
}else{
$tabela = "folha_$projeto";
}

mysql_query("DELETE FROM $tabela WHERE id_folhas = '$id_folha'") or die ("1 - O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br>$projeto - $id_folha<br>".mysql_error());
mysql_query("DELETE FROM folhas WHERE id_folha = '$id_folha'") or die ("2 - O servidor não respondeu conforme deveria, tente novamente mais tarde, Obrigado!<br><br>".mysql_error());

print "Folha apagada<br><Br><a href='javascript:window.close()'>Fechar</a>";

}
break;

case 2:




break;
}

}   // FECHANDO O IF QUE VERIFICA O LOGIN
?>