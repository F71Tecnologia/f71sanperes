<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

// SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
$sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
$result = mysql_query($sql, $conn);

//PEGANDO O ID DO CADASTRO
$id = $_REQUEST['id'];



// INICIO DO PÁGINA QUE RODA EM TODOS OS TIPOS DE CADASTRO
/*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<script src='SpryAssets/SpryAccordion.js' type='text/javascript'></script>
<link href='SpryAssets/SpryAccordion.css' rel='stylesheet' type='text/css' />
*/
?>
<html>
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script src='ajax.js' type='text/javascript'></script>
<link href='autocomp/css.css' type='text/css' rel='stylesheet'>
<link href="net2.css" rel="stylesheet" type="text/css">
<style type="text/css">
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

<script language="JavaScript">
function TelefoneFormat(Campo, e) {
var key = '';
var len = 0;
var strCheck = '0123456789';
var aux = '';
var whichCode = (window.Event) ? e.which : e.keyCode;
if (whichCode == 13 || whichCode == 8 || whichCode == 0)
{
return true;  // Enter backspace ou FN qualquer um que não seja alfa numerico
}
key = String.fromCharCode(whichCode);
if (strCheck.indexOf(key) == -1){
return false;  //NÃO E VALIDO
}
aux =  Telefone_Remove_Format(Campo.value);
len = aux.length;
if(len>=10)
{
return false;	//impede de digitar um telefone maior que 10
}
aux += key;
Campo.value = Telefone_Mont_Format(aux);
return false;
}
function  Telefone_Mont_Format(Telefone)
{
var aux = len = '';
len = Telefone.length;
if(len<=9)
{
tmp = 5;
}
else
{
tmp = 6;
}
aux = '';
for(i = 0; i < len; i++)
{
if(i==0)
{
aux = '(';
}
aux += Telefone.charAt(i);
if(i+1==2)
{
aux += ')';
}
if(i+1==tmp)
{
aux += '-';
}
}
return aux ;
}
function  Telefone_Remove_Format(Telefone)
{
var strCheck = '0123456789';
var len = i = aux = '';
len = Telefone.length;
for(i = 0; i < len; i++)
{
if (strCheck.indexOf(Telefone.charAt(i))!=-1)
{
aux += Telefone.charAt(i);
}
}
return aux;
}
function formatar(mascara, documento){ 
var i = documento.value.length; 
var saida = mascara.substring(0,1); 
var texto = mascara.substring(i) 
if (texto.substring(0,1) != saida){ 
documento.value += texto.substring(0,1); 
} 
} 
function pula(maxlength, id, proximo){ 
if(document.getElementById(id).value.length >= maxlength){ 
document.getElementById(proximo).focus();
}
} 
function mascara_data(d){  
var mydata = '';  
data = d.value;  
mydata = mydata + data;  
if (mydata.length == 2){  
mydata = mydata + '/';  
d.value = mydata;  
}  
if (mydata.length == 5){  
mydata = mydata + '/';  
d.value = mydata;  
}  
if (mydata.length == 10){  
verifica_data(d);  
}  
} 
function verifica_data (d) {  
dia = (d.value.substring(0,2));  
mes = (d.value.substring(3,5));  
ano = (d.value.substring(6,10));  
situacao = "";  
// verifica o dia valido para cada mes  
if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
situacao = "falsa";  
}  
// verifica se o mes e valido  
if (mes < 01 || mes > 12 ) {  
situacao = "falsa";  
}  
// verifica se e ano bissexto  
if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
situacao = "falsa";  
}  
if (d.value == "") {  
situacao = "falsa";  
}  
if (situacao == "falsa") {  
alert("Data digitada é inválida, digite novamente!"); 
d.value = "";  
d.focus();  
}  
}
function FormataValor(objeto,teclapres,tammax,decimais) 
{
var tecla            = teclapres.keyCode;
var tamanhoObjeto    = objeto.value.length;
if ((tecla == 8) && (tamanhoObjeto == tammax))
{
tamanhoObjeto = tamanhoObjeto - 1 ;
}
if (( tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ) && ((tamanhoObjeto+1) <= tammax))
{
vr    = objeto.value;
vr    = vr.replace( "/", "" );
vr    = vr.replace( "/", "" );
vr    = vr.replace( ",", "" );
vr    = vr.replace( ".", "" );
vr    = vr.replace( ".", "" );
vr    = vr.replace( ".", "" );
vr    = vr.replace( ".", "" );
tam    = vr.length;
if (tam < tammax && tecla != 8)
{
tam = vr.length + 1 ;
}
if ((tecla == 8) && (tam > 1))
{
tam = tam - 1 ;
vr = objeto.value;
vr = vr.replace( "/", "" );
vr = vr.replace( "/", "" );
vr = vr.replace( ",", "" );
vr = vr.replace( ".", "" );
vr = vr.replace( ".", "" );
vr = vr.replace( ".", "" );
vr = vr.replace( ".", "" );
}
//Cálculo para casas decimais setadas por parametro
if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
{
if (decimais > 0)
{
if ( (tam <= decimais) )
{ 
objeto.value = ("0," + vr) ;
}
if( (tam == (decimais + 1)) && (tecla == 8))
{
objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == "0"))
{
objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != "0"))
{
objeto.value = vr.substr( 0, tam - decimais ) + ',' + vr.substr( tam - decimais, tam ) ; 
}
if ( (tam >= (decimais + 4)) && (tam <= (decimais + 6)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 3) ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 7)) && (tam <= (decimais + 9)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 6) ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 10)) && (tam <= (decimais + 12)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 9) ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
if ( (tam >= (decimais + 13)) && (tam <= (decimais + 15)) )
{
objeto.value = vr.substr( 0, tam - (decimais + 12) ) + '.' + vr.substr( tam - (decimais + 12), 3 ) + '.' + vr.substr( tam - (decimais + 9), 3 ) + '.' + vr.substr( tam - (decimais + 6), 3 ) + '.' + vr.substr( tam - (decimais + 3), 3 ) + ',' + vr.substr( tam - decimais, tam ) ;
}
}
else if(decimais == 0)
{
if ( tam <= 3 )
{ 
objeto.value = vr ;
}
if ( (tam >= 4) && (tam <= 6) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 7) && (tam <= 9) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 6 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 10) && (tam <= 12) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 9 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ); 
}
if ( (tam >= 13) && (tam <= 15) )
{
if(tecla == 8)
{
objeto.value = vr.substr(0, tam);
window.event.cancelBubble = true;
window.event.returnValue = false;
}
objeto.value = vr.substr( 0, tam - 12 ) + '.' + vr.substr( tam - 12, 3 ) + '.' + vr.substr( tam - 9, 3 ) + '.' + vr.substr( tam - 6, 3 ) + '.' + vr.substr( tam - 3, 3 ) ;
}            
}
}
}
else if((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
{
window.event.cancelBubble = true;
window.event.returnValue = false;
}
} 
</script>
</head>


<?PHP

switch ($id) {					//SELEÇÃO DE CASOS

case 1:						//CASO O ID SEJA 1 ELE VAI RODAR O - CADASTRO DE PROJETO -

$sql_projeto = "SELECT COUNT(id_projeto) FROM projeto";
$resulto_projeto = mysql_query($sql_projeto);
$row_projeto = mysql_fetch_array($resulto_projeto);
$id_projeto = $row_projeto[0] + 1 ;
$id_regiao = $_REQUEST['regiao'];
$id_user = $_REQUEST['user'];

?>

<body bgcolor='#D7E6D5'>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
<tr><td colspan='4' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrodeprojetos.gif'> <br>  Cadastramento de Projetos</div><BR></td></tr>
<tr><td width='73' align="left" valign="top"><img src='imagens/arre_cima1.gif' width='21' height='18' /></td><td width='128'>&nbsp;</td>
  <td width='349'>&nbsp;</td>
  <td width='21' align="right" valign="top"><img src='imagens/arre_cima2.gif' alt='' width='18' height='21' /></td>
</tr>
<tr><td width='73'>&nbsp;</td><td width='128'>&nbsp;</td>
  <td width='349'>&nbsp;</td>
  <td width='21'>&nbsp;</td>
</tr>
<tr><td height="25" align='right' class="style17">&nbsp;</td>
<td height="25">&nbsp;&nbsp;<span class="style17">Nome do Projeto:</span></td>
<td><input name='nome2' type='text' class='campotexto' id='nome2' size='20'></td>
<td>&nbsp;</td>
</tr>
<tr><td height="25" align='right' class="style17">&nbsp;</td>
<td height="25">&nbsp;&nbsp;<span class="style17">Tema:</span></td>
<td><input name='tema' type='text' class='campotexto' id='tema' size='35'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td height="25" align='right' class="style17">&nbsp;</td>
<td height="25">&nbsp;&nbsp;<span class="style17">&Aacute;rea:</span></td>
<td><input name='area2' type='text' class='campotexto' id='area2' size='25'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td height="25" align='right' class="style17">&nbsp;</td>
<td height="25">&nbsp;&nbsp;<span class="style17">Local:</span></td>
<td><input name='local2' type='text' class='campotexto' id='local2' size='25'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td height="25" align='right' class="style17">&nbsp;</td>
<td height="25">&nbsp;&nbsp;<span class="style17">Regi&atilde;o:</span></td>
<td><select name='id_regiao2' class='campotexto' id='id_regiao'>

  <?PHP
while ($row = mysql_fetch_array($result)){

$row_regiao = "$row[id_regiao]";

if ($id_regiao == "$row_regiao"){
print "<option value=$row[id_regiao] selected>$row[0] - $row[regiao] - $row[sigla]</option>";
} else {
print "<option value=$row[id_regiao]>$row[0] - $row[regiao] - $row[sigla]</option>";
}
}
?>
</select></td>
<td>&nbsp;</td>
</tr>
<tr>
<td height="25" align='right' class="style17">&nbsp;</td>
<td height="25"><span class="style17">Inicio:</span></td>
<td><table width='241' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='66'>&nbsp;&nbsp;
      <select name='ini_dia' class='campotexto' id='ini_dia'>
        <option>01</option>
        <option>02</option>
        <option>03</option>
        <option>04</option>
        <option>05</option>
        <option>06</option>
        <option>07</option>
        <option>08</option>
        <option>09</option>
        <option>10</option>
        <option>11</option>
        <option>12</option>
        <option>13</option>
        <option>14</option>
        <option>15</option>
        <option>16</option>
        <option>17</option>
        <option>18</option>
        <option>19</option>
        <option>20</option>
        <option>21</option>
        <option>22</option>
        <option>23</option>
        <option>24</option>
        <option>25</option>
        <option>26</option>
        <option>27</option>
        <option>28</option>
        <option>29</option>
        <option>30</option>
        <option>31</option>
      </select></td>
    <td width='104'><select name='ini_mes' class='campotexto' id='ini_mes'>
      <option value='01'>Janeiro</option>
      <option value='02'>Fevereiro</option>
      <option value='03'>Mar&ccedil;o</option>
      <option value='04'>Abril</option>
      <option value='05'>Maio</option>
      <option value='06'>Junho</option>
      <option value='07'>Julho</option>
      <option value='08'>Agosto</option>
      <option value='09'>Setembro</option>
      <option value='10'>Outubro</option>
      <option value='11'>Novembro</option>
      <option value='12'>Dezembro</option>
    </select></td>
    <td width='71'><select name='ini_ano' class='campotexto' id='ini_ano'>
      <option value='2007'>2007</option>
      <option value='2008'>2008</option>
      <option value='2009'>2009</option>
      <option value='2010'>2010</option>
      <option value='2011'>2011</option>
      <option value='2012'>2012</option>
      <option value='2013'>2013</option>
      <option value='2014'>2014</option>
      <option value='2015'>2015</option>
    </select></td>
  </tr>
</table></td>
<td>&nbsp;</td>
</tr>
<tr>
<td height="25" align='right' class="style17">&nbsp;</td>
<td height="25"><span class="style17">Previs&atilde;o de T&eacute;rmino:</span></td>
<td><table width='241' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td width='66'>&nbsp;&nbsp;
      <select name='ter_dia' class='campotexto' id='ter_dia'>
        <option>01</option>
        <option>02</option>
        <option>03</option>
        <option>04</option>
        <option>05</option>
        <option>06</option>
        <option>07</option>
        <option>08</option>
        <option>09</option>
        <option>10</option>
        <option>11</option>
        <option>12</option>
        <option>13</option>
        <option>14</option>
        <option>15</option>
        <option>16</option>
        <option>17</option>
        <option>18</option>
        <option>19</option>
        <option>20</option>
        <option>21</option>
        <option>22</option>
        <option>23</option>
        <option>24</option>
        <option>25</option>
        <option>26</option>
        <option>27</option>
        <option>28</option>
        <option>29</option>
        <option>30</option>
        <option>31</option>
      </select></td>
    <td width='104'><select name='ter_mes' class='campotexto' id='ter_mes'>
      <option value='01'>Janeiro</option>
      <option value='02'>Fevereiro</option>
      <option value='03'>Mar&ccedil;o</option>
      <option value='04'>Abril</option>
      <option value='05'>Maio</option>
      <option value='06'>Junho</option>
      <option value='07'>Julho</option>
      <option value='08'>Agosto</option>
      <option value='09'>Setembro</option>
      <option value='10'>Outubro</option>
      <option value='11'>Novembro</option>
      <option value='12'>Dezembro</option>
    </select></td>
    <td width='71'><select name='ter_ano' class='campotexto' id='ter_ano'>
      <option value='2007'>2007</option>
      <option value='2008'>2008</option>
      <option value='2009'>2009</option>
      <option value='2010'>2010</option>
      <option value='2011'>2011</option>
      <option value='2012'>2012</option>
      <option value='2013'>2013</option>
      <option value='2014'>2014</option>
      <option value='2015'>2015</option>
    </select></td>
  </tr>
</table></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right' valign='top' class="style17">&nbsp;</td>
<td>&nbsp;&nbsp;
  <span class="style17">Descri&ccedil;&atilde;o</span>
<td><TEXTAREA id=textarea name=caracteres cols='30' rows='7' class='campotexto' onKeyPress='soma(this.value)' onKeyUp='soma(this.value) ; Contar(this)'></textarea>
  <br>
  &nbsp;<font size='1' color='#CCCCCC'>(<span id='Qtd'>250</span> caracteres restantes)</font>
<td>
</tr>
<tr>
<td align='right' class="style23">&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right' class="style23">&nbsp;</td>
<td><span class="style23">Valor de Reserva Inicial:</span></td>
<td>&nbsp;&nbsp; R$:
  <input name='valor_ini' type='text'  class='campotexto' id='reserva' size='5'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right' class="style23">&nbsp;</td>
<td><span class="style23">Total de Participantes:</span></td>
<td>&nbsp;&nbsp;
  <input name='bolsista' type='text'  class='campotexto' id='bolsista' size='3' onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='4' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='1'>
<input type='hidden' name='id_projeto' value='$id_projeto'>
<input type='hidden' name='user' value='$id_user'>
</td>
</tr>
<tr>
<td align='left' valign="bottom"><img src='imagens/arre_baixo1.gif' alt='' width='18' height='21' /></td>
<td align='center'>&nbsp;</td>
<td align='center'>&nbsp;</td>
<td align='right' valign="bottom"><img src='imagens/arre_baixo2.gif' alt='' width='21' height='18' /></td>
</tr>
</table>
</form>
<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>


<script>function validaForm(){
d = document.form1;
if (d.nome.value == ""){
alert("O campo Nome do Projeto deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.tema.value == ""){
alert("O campo Tema deve ser preenchido!");
d.tema.focus();
return false;
}
if (d.area.value == ""){
alert("O campo Área deve ser preenchido!");
d.area.focus();
return false;
}
if (d.local.value == ""){
alert("O campo Local deve ser preenchido!");
d.local.focus();
return false;
}
if (d.reserva.value == ""){
alert("O campo Valor de Reserva Inicial deve ser preenchido!");
d.reserva.focus();
return false;
}
if (d.bolsista.value == ""){
alert("O campo Total de Participantes deve ser preenchido!");
d.bolsista.focus();
return false;
}
return true;   }
</script> 
<?php

break;

case 2:  							// CASO O ID SEJA 2 ELE VAI RODAR O - REGIÕES -


?>

<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<div align='left' class='style1'> <img src='imagens/cadastroderegioes.gif'><BR>Cadastro de Regi&otilde;es</div><BR>

<table width='70%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
  <tr>
    <td width='3%' valign='top'><img src='imagens/arre_cima1.gif' width='21' height='18' /></td>
    <td width='94%'>&nbsp;</td>
    <td width='3%' align='right' valign='top'><img src='imagens/arre_cima2.gif' alt='' width='18' height='21' /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	
	
<table width='100%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td align='right'>Nome da Regi&atilde;o:</td>
<td>&nbsp;&nbsp; 
<input name='regiao' type='text' class='campotexto' id='regiao' size='20' style='background:#FFFFFF;'
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'">

</td>
</tr>
<tr>
<td align='right'>Sigla:</td>
<td>&nbsp;&nbsp; 
<input name='sigla' type='text' class='campotexto' id='sigla' size='2' maxlength='2' style='background:#FFFFFF;'
onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'>

<input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR><BR>

</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='2'>

<td>&nbsp;</td>
  </tr>
  <tr>
    <td valign='bottom'><img src='imagens/arre_baixo1.gif' alt='' width='18' height='21' /></td>
    <td>&nbsp;</td>
    <td valign='bottom' align='right'><img src='imagens/arre_baixo2.gif' alt='' width='21' height='18' /></td>
  </tr>
</table>




</td>
</tr>
</table>
</form>
<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.regiao.value == ""){
alert("O campo Nome da Região deve ser preenchido!");
d.regiao.focus();
return false;
}
if (d.sigla.value == "" ){
alert("O campo Sigla deve ser preenchido!");
d.sigla.focus();
return false;
}
return true;   }
</script>

<?php
break;

case 3:			//CASO O ID SEJA 3 ELE VAI RODAR O - CADASTRO DE FUNCIONÁRIO/USUÁRIO -

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'", $conn);
$row_user = mysql_fetch_array($result_user);

?>

<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()" enctype='multipart/form-data'>
<table width='660' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan=4 bgcolor='#5C7E59'><div align=left class='style1'><img src='imagens/cadastrodeusuarios.gif'> <BR>Cadastro de Usu&aacute;rio para acesso a Intranet</div><BR></td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td width='15%' align=right>Região:</td>
<td width='38%'>&nbsp;&nbsp; <select name='id_regiao' class='campotexto' id='regiao'>
<?php

while ($row = mysql_fetch_array($result)){
$regiao_atual = $row_user[id_regiao];
$regiao_atual2 = $row[id_regiao];
if ($regiao_atual == $regiao_atual2){
print "<option value='$row[id_regiao]' selected>$row[0] - $row[regiao] - $row[sigla]</option>";
}else{
print "<option value='$row[id_regiao]'>$row[regiao] - $row[sigla]</option>";
}
}
?>
</select></td>
<td width='11%' align=right></td>
<td width='36%'></td>
</tr>
<tr>
<td width='15%' align=right>Função:</td>
<td width='38%'>&nbsp;&nbsp; <input name='funcao' type='text' class='campotexto' id='funcao' size='30' 
onFocus="document.all.funcao.style.background='#CCFFCC'"
onBlur="document.all.funcao.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
<td width='12%' align=right>Lotação:</td>
<td width='35%'>&nbsp;&nbsp; <input name='locacao' type='text' class='campotexto' id='locacao' size='20'
onFocus="document.all.locacao.style.background='#CCFFCC'"
onBlur="document.all.locacao.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
</tr>
<tr>
<td width='15%' align=right>Grupo: </td>
<td width='38%' colspan=3>&nbsp;&nbsp; <select name='grupo_usuario' class='campotexto'>
<?php
$result_grupo = mysql_query("SELECT * FROM grupo", $conn);
while ($row_grupo = mysql_fetch_array($result_grupo)){
print "<option value=$row_grupo[id_grupo]>$row_grupo[nome]</option>";
}
?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp; Salário: R$&nbsp;&nbsp;<input name='salario' type='text' class='campotexto' id='salario' size='10'
onFocus="document.all.salario.style.background='#CCFFCC'"
onBlur="document.all.salario.style.background='#FFFFFF'" 
style="background:#FFFFFF"> <font color=#999999 size=1>Somente números</font>
</td>
</tr>
<tr>
<td width='15%' align=right>Nome Completo:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='nome' type='text' class='campotexto' id='nome' size='35' onFocus="document.all.nome.style.background='#CCFFCC'"
onBlur="document.all.nome.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp;Nome para exibição: <input name='nome1' type='text' class='campotexto' id='nome1' size='15' onFocus="document.all.nome1.style.background='#CCFFCC'"
onBlur="document.all.nome1.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr>
<td width='15%' align=right>Endereco:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='endereco' type='text' class='campotexto' id='endereco' size='75' onFocus="document.all.endereco.style.background='#CCFFCC'"
onBlur="document.all.endereco.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
</tr>
<tr>
<td width='15%' align=right>Bairro:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
onFocus="document.all.bairro.style.background='#CCFFCC'"
onBlur="document.all.bairro.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; Cidade:&nbsp;&nbsp; <input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
onFocus="document.all.cidade.style.background='#CCFFCC'"
onBlur="document.all.cidade.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; UF:&nbsp;&nbsp; <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
onFocus="document.all.uf.style.background='#CCFFCC'"
onBlur="document.all.uf.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()" onKeyUp="pula(2,this.id,cep.id)" >
&nbsp;&nbsp; CEP:&nbsp;&nbsp; <input name='cep' type='text' class='campotexto' id='cep' size='12' 
onFocus="document.all.cep.style.background='#CCFFCC'"
onBlur="document.all.cep.style.background='#FFFFFF'" 
style="background:#FFFFFF" onKeyUp="pula(9,this.id,tel_fixo.id)" 
OnKeyPress="formatar('#####-###', this)" >
</td>
</tr>
<tr>
<td width='15%' align=right>Telefones:</td>
<td width='38%' colspan=3>&nbsp; Fixo:&nbsp;&nbsp; 
<input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' size='13'
onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,tel_cel.id)" 
onFocus="document.all.tel_fixo.style.background='#CCFFCC'"
onBlur="document.all.tel_fixo.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp; Cel:&nbsp;&nbsp; 
<input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='13' 
onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,tel_rec.id)" 
onFocus="document.all.tel_cel.style.background='#CCFFCC'"
onBlur="document.all.tel_cel.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp; Recado:&nbsp;&nbsp; 
<input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='13' 
onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,data_nasci.id)" 
onFocus="document.all.tel_rec.style.background='#CCFFCC'"
onBlur="document.all.tel_rec.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr>
<td align=right>Data de </td>
<td colspan=3>&nbsp; Nascimento:
&nbsp; 
<input name='data_nasci' type='text' id='data_nasci' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,naturalidade.id)" maxlength='10' 
onFocus="document.all.data_nasci.style.background='#CCFFCC'" 
onBlur="document.all.data_nasci.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp;&nbsp;&nbsp;
Naturalidade:
&nbsp;&nbsp;<input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='14' onFocus="document.all.naturalidade.style.background='#CCFFCC'"
onBlur="document.all.naturalidade.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
</td>
</tr>
<tr>
<td width='15%' align=right>Nacionalidade:</td>
<td width='38%' colspan=3>&nbsp;&nbsp;
<input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='12' 
onFocus="document.all.nacionalidade.style.background='#CCFFCC'"
onBlur="document.all.nacionalidade.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; 
Estado Civil:
&nbsp;&nbsp; <select name='civil' class='campotexto' id='civil' >
<option>Solteiro</option>
<option>Casado</option>
<option>Viúvo</option>
<option>Sep. Judicialmente</option>
<option>Divorciado</option>
</select>
&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td width='15%' align=right>CTPS:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='ctps' type='text' class='campotexto' id='ctps' size='10' 
onFocus="document.all.ctps.style.background='#CCFFCC'"
onBlur="document.all.ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; &nbsp;&nbsp; 
Série:
&nbsp;&nbsp; 
<input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='8' 
onFocus="document.all.serie_ctps.style.background='#CCFFCC'"
onBlur="document.all.serie_ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; &nbsp;&nbsp; 
UF:
&nbsp;&nbsp; 
<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' 
onFocus="document.all.uf_ctps.style.background='#CCFFCC'"
onBlur="document.all.uf_ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF" 
onChange="this.value=this.value.toUpperCase()" onKeyUp="pula(2,this.id,pis.id)" >
&nbsp;&nbsp; &nbsp;&nbsp; 
PIS:
&nbsp;&nbsp; 
<input name='pis' type='text' class='campotexto' id='pis' size='15' 
onFocus="document.all.pis.style.background='#CCFFCC'"
onBlur="document.all.pis.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td width='15%' align=right>Nº do RG:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='rg' type='text' class='campotexto' id='rg' 
onFocus="document.all.rg.style.background='#CCFFCC'"
onBlur="document.all.rg.style.background='#FFFFFF'" 
style="background:#FFFFFF" size='12' maxlength=13 OnKeyPress="formatar('##.###.###-#', this)">
&nbsp;&nbsp; Orgão Expedidor:&nbsp;&nbsp; 
<input name='orgao' type='text' class='campotexto' id='orgao' size='8' 
onFocus="document.all.orgao.style.background='#CCFFCC'"
onBlur="document.all.orgao.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; Data:&nbsp;&nbsp; 
<input name='data_rg' type='text' id='data_ctps' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,cpf.id)" maxlength='10' 
onFocus="document.all.data_rg.style.background='#CCFFCC'" 
onBlur="document.all.data_rg.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr>
<td width='15%' align=right>CPF:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='cpf' type='text' class='campotexto' id='cpf' size='14' maxlength='14'
OnKeyPress="formatar('###.###.###-##', this)" onKeyUp="pula(14,this.id,n_titulo.id)" 
onFocus="document.all.cpf.style.background='#CCFFCC'" 
onBlur="document.all.cpf.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; Nº Título de Eleitor:&nbsp;&nbsp; 
<input name='titulo' type='text' class='campotexto' id='n_titulo' size='10'
onFocus="document.all.n_titulo.style.background='#CCFFCC'" 
onBlur="document.all.n_titulo.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; Zona:&nbsp;&nbsp; 
<input name='zona' type='text' class='campotexto' id='n_zona' size='3' 
onFocus="document.all.n_zona.style.background='#CCFFCC'" 
onBlur="document.all.n_zona.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; Seção:&nbsp;&nbsp; 
<input name='secao' type='text' class='campotexto' id='secao' size='3' 
onFocus="document.all.secao.style.background='#CCFFCC'" 
onBlur="document.all.secao.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr>
<td width='15%' align=right>Filiação - Pai:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='pai' type='text' class='campotexto' id='pai' size='75' 
onFocus="document.all.pai.style.background='#CCFFCC'" 
onBlur="document.all.pai.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
</td>
</tr>
<tr>
<td width='15%' align=right>Filiação - Mãe:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='mae' type='text' class='campotexto' id='mae' size='75' 
onFocus="document.all.mae.style.background='#CCFFCC'" 
onBlur="document.all.mae.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
</tr>
<tr>
<tr>
<td width='15%' align=right>Estuda Atualmente:</td>
<td width='38%' colspan=3 >&nbsp;&nbsp; <input type='radio' checked name='estuda' value='sim' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? 'none' : 'none' ;"> Sim&nbsp;&nbsp;<input type='radio' name='estuda' value='nao' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? '' : '' ;"> Não
</td>
</tr>
<tr id='linha_termino' style='display:none'>
<td width='15%' align=right>Término em:</td>
<td>&nbsp;&nbsp; <input name='escola_dia' type='text' class='campotexto' value='30' size='2' maxlength=2 > / <input name='escola_mes' type='text' class='campotexto' size='2' maxlength=2 value='11'> / <input name='escola_ano' type='text' class='campotexto' size='4' maxlength=4>
</td>
</tr>
<td width='15%' align=right>Escolaridade:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='escolaridade' type='text' class='campotexto' id='escolaridade' size='15' 
onFocus="document.all.escolaridade.style.background='#CCFFCC'" 
onBlur="document.all.escolaridade.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; Instituíção:&nbsp;&nbsp; 
<input name='instituicao' type='text' class='campotexto' id='titulo' size='20' 
onFocus="document.all.instituicao.style.background='#CCFFCC'" 
onBlur="document.all.instituicao.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; Curso:&nbsp;&nbsp; 
<input name='curso' type='text' class='campotexto' id='zona' size='10' 
onFocus="document.all.curso.style.background='#CCFFCC'" 
onBlur="document.all.curso.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
</td>
</tr>
<tr>
<td width='15%' align=right>Foto:</td>
<td colspan=3>
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='linha'>
<tr>
<td width='8%'> &nbsp;&nbsp;
<label>
<input name='foto' type='checkbox' id='foto' onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/>Sim</label></td>
<td width="77%">
<span style='display:none' id='logomarca'> &nbsp;&nbsp;&nbsp;&nbsp;
selecione:
<input type='file' name='arquivo' id='arquivo' class='campotexto'>
<font size='1' color='#999999'>(.jpg, .png, .gif, .jpeg)</font>                  
</span></td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td colspan=4><div align=center class='style2'>Informações Bancárias</div></td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td width='15%' align=right>Banco:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='banco' type='text' class='campotexto' id='banco' size='15' onFocus="document.all.banco.style.background='#CCFFCC'" 
onBlur="document.all.banco.style.background='#FFFFFF'" 
style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
&nbsp;&nbsp; Agência:&nbsp;&nbsp; 
<input name='agencia' type='text' class='campotexto' id='agencia' size='7' 
onFocus="document.all.agencia.style.background='#CCFFCC'" 
onBlur="document.all.agencia.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; nº da Conta:&nbsp;&nbsp; 
<input name='conta' type='text' class='campotexto' id='conta' size='15'
onFocus="document.all.conta.style.background='#CCFFCC'" 
onBlur="document.all.conta.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td colspan=4><div align=center class='style2'>Informações de Login</div></td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td width='15%' align=right>Login:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='login' type='text' class='campotexto' id='login' size='10' 
onFocus="document.all.login.style.background='#CCFFCC'" 
onBlur="document.all.login.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; Senha padrão:&nbsp;&nbsp; 
<input name='senha' type='text' class='campotexto' id='senha' size='7' value='123456'
onFocus="document.all.senha.style.background='#CCFFCC'" 
onBlur="document.all.senha.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; Tipo de Conta:&nbsp;&nbsp; <select name='tipo_usuario' class='campotexto'>
<option value=0>Básico</option>
<option value=1>Diretor</option>
<option value=2>Financeiro</option>
<option value=3>Administrador</option>
<option value=4>Psicólogo</option>
</select>
</tr>
<tr>
<td width='15%' align=right></td>
<td width='38%' colspan=3><br>
<font color='red'> Atenção: <BR> - O usuário modificará a senha no seu 1º logon <BR> - Verifique todos os dados acima atentamente, após verificação clique em CADASTRAR</font>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='4' align='center'><table width='208' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td width="85" align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'> <BR><BR>
</td>
<td width="123" align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'><BR> <BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='3'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.funcao.value == ""){
alert("O campo Função deve ser preenchido!");
d.funcao.focus();
return false;
}
if (d.locacao.value == ""){
alert("O campo Lotação deve ser preenchido!");
d.locacao.focus();
return false;
}
if (d.salario.value == ""){
alert("O campo Salário deve ser preenchido!");
d.salario.focus();
return false;
}
if (d.nome.value == "" ){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.nome1.value == "" ){
alert("O campo Nome para Exibição deve ser preenchido!");
d.nome1.focus();
return false;
}
if (d.login.value == "" ){
alert("O campo Login deve ser preenchido!");
d.login.focus();
return false;
}
return true;   
}
</script>

<?php
break;


case 4:								//CASO O ID SEJA 4 ELE VAI RODAR O - CADASTRO DE PARTICIPANTES -

$projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['regiao'];

$sql_pro = "SELECT * FROM projeto where id_projeto = $projeto";
$result_pro = mysql_query($sql_pro, $conn);
$row = mysql_fetch_array($result_pro);
$result_grupo = mysql_query("SELECT * FROM curso where id_regiao = '$id_regiao' and tipo = '1' ORDER BY nome", $conn);

// PEGANDO O MAIOR NUMERO
$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3 , 
MAX(campo3) 
FROM autonomo 
WHERE id_regiao= '$id_regiao' 
AND id_projeto ='$projeto' 
AND campo3 != 'INSERIR' 
GROUP BY campo3 DESC 
LIMIT 0,1");
$row_maior = mysql_fetch_array ($resut_maior); 

$codigo = $row_maior[0] + 1;

?>

<form action='cadastro2.php' method='post' name='form1' enctype='multipart/form-data' onSubmit="return validaForm()">
<table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#5C7E59' class='linha' align='center'>
<tr>
<td colspan=4><div align=center class='style7'>
<div align='left'><img src='imagens/cadastrabolsista.gif' width='440' height='20' /><br />
Cadastramento de Integrante de acordo com o Projeto Selecionado <BR><BR> </div>
</div></td>
</tr>


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='2' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS DO PROJETO</div></td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style6'>
Código:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;

<input name='codigo' type='text' class='campotexto' id='codigo' size='10' value='$codigo' disabled 
onFocus="document.all.codigo.style.background='#CCFFCC'"
onBlur="document.all.codigo.style.background='#FFFFFF'" 
style='background:#FFFFFF;' /></td>
</tr>

<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Tipo Contratação:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;
<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='1' checked/> Autônomo</label><br>
&nbsp;
<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='3' /> Cooperado</label>
</td>
</tr>



<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Projeto:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'> <span class='style6 style37'>&nbsp; &nbsp;$row[id_projeto] - $row[nome] &nbsp;&nbsp;/ <span class='style37'>&nbsp;&nbsp;Região: $row[id_regiao] - $row[regiao]
</span></span></td>
</tr>
<tr>
<td height='30' height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Curso:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='idcurso' id='idcurso' class='campotexto'>";
$result_grupo = mysql_query("SELECT * FROM curso where id_regiao = $row[id_regiao] and 
campo3 = '$projeto' ORDER BY campo2", $conn);
while ($row_grupo = mysql_fetch_array($result_grupo)){
print "
<option value='$row_grupo[id_curso]'>$row_grupo[id_curso] - $row_grupo[campo2] / Valor: $row_grupo[valor] - $row_grupo[campo1]</option>";
}
print "
</select>
</span></td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Unidade:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='locacao' id='locacao' class='campotexto'>
<?php
$result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = $row[id_regiao] and 
campo1 = '$projeto' ORDER BY unidade", $conn);
while ($row_unidade = mysql_fetch_array($result_unidade)){
print "<option>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
}
?>
</select>
</span></td>
</tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='8' bgcolor='#003300' class='style1'><div align='center' class='style6 style3 style40 style42'>
<div align='center' class='style41'>DADOS CADASTRAIS</div>
</div></td>
</tr>
<tr height='30'>
<td width='13%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Nome:&nbsp;</span></div>
</div></td>
<td width='87%' colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='nome' type='text' class='campotexto' id='nome' size='75'
onFocus="document.all.nome.style.background='#CCFFCC'"
onBlur="document.all.nome.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Endereco:&nbsp;</span></div>
</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='endereco' type='text' class='campotexto' id='endereco' size='75' 
onFocus="document.all.endereco.style.background='#CCFFCC'" 
onBlur="document.all.endereco.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Bairro:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
onFocus="document.all.bairro.style.background='#CCFFCC'" 
onBlur="document.all.bairro.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'> Cidade:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
onFocus="document.all.cidade.style.background='#CCFFCC'" 
onBlur="document.all.cidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>UF:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
onFocus="document.all.uf.style.background='#CCFFCC'" 
onBlur="document.all.uf.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"
onkeyup="pula(2,this.id,cep.id)" />
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>CEP:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cep' type='text' class='campotexto' id='cep' size='10' maxlength='9' 
style='background:#FFFFFF; text-transform:uppercase;'
onFocus="document.all.cep.style.background='#CCFFCC'" 
onBlur="document.all.cep.style.background='#FFFFFF'"
OnKeyPress="formatar('#####-###', this)" 
onKeyUp="pula(9,this.id,tel_fixo.id)" />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Telefones:&nbsp;</span></div>
</div></td>
<td colspan='2' bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='center'><span class='style37'>Fixo:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_fixo' type='text' id='tel_fixo' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_cel.id)" 
onFocus="document.all.tel_fixo.style.background='#CCFFCC'" 
onBlur="document.all.tel_fixo.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'> <div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Cel:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='14' onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_rec.id)" 
onFocus="document.all.tel_cel.style.background='#CCFFCC'" 
onBlur="document.all.tel_cel.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
&nbsp;</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Recado:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='14' onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,data_nasci.id)" 
onFocus="document.all.tel_rec.style.background='#CCFFCC'" 
onBlur="document.all.tel_rec.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Data de Nascimento:&nbsp;</span></div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;
<input name='data_nasci' type='text' id='data_nasci' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,naturalidade.id)"
onFocus="document.all.data_nasci.style.background='#CCFFCC'" 
onBlur="document.all.data_nasci.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>
</span> <span class='style6 style37'>&nbsp;</span></td>
<td bgcolor='#CCFFCC' class='style1'>
<div align='right' class='style39'>Naturalidade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10'  
onFocus="document.all.naturalidade.style.background='#CCFFCC'" 
onBlur="document.all.naturalidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nacionalidade:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' 
onFocus="document.all.nacionalidade.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estado Civil:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<select name='civil' class='campotexto' id='civil'>
<option>Solteiro</option>
<option>Casado</option>
<option>Viúvo</option>
<option>Sep. Judicialmente</option>
<option>Divorciado</option>
</select>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right'  class='style39'>Sexo:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
<table align='left'>
<tr height='30'>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>
<input type='radio' name='sexo' value='M' checked='checked' /> Masculino </label></span></td>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>		
<input type='radio' name='sexo' value='F' />Feminino</label></span></td>
</tr>
</table></td>
</tr>
<tr>
<td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>DADOS DA FAMÍLIA E EDUCACIONAIS</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Pai:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='pai' type='text' class='campotexto' id='pai' size='75' 
onFocus="document.all.pai.style.background='#CCFFCC'" 
onBlur="document.all.pai.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Pai:</span>&nbsp;&nbsp;
	
<input name='nacionalidade_pai' type='text' class='campotexto' id='nacionalidade_pai' size='15' 
onFocus="document.all.nacionalidade_pai.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade_pai.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	

</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Mãe:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='mae' type='text' class='campotexto' id='mae' size='75' 
onFocus="document.all.mae.style.background='#CCFFCC'" 
onBlur="document.all.mae.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Mãe:</span>&nbsp;&nbsp;
	
<input name='nacionalidade_mae' type='text' class='campotexto' id='nacionalidade_mae' size='15' 
onFocus="document.all.nacionalidade_mae.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade_mae.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	



</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estuda Atualmente?&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><table align='left'>
<tr height='30'>
<td class='style39'><span class='style37'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='sim' checked='checked' />
SIM</span></td>
<td class='style39'><span class='style37'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='não' />
NÃO</span></td>
</tr>
</table></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Término em:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='data_escola' type='text' id='data_escola' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,escolaridade.id)" maxlength='10' 
onFocus="document.all.data_escola.style.background='#CCFFCC'" 
onBlur="document.all.data_escola.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Escolaridade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;&nbsp;
<input name='escolaridade' type='text' class='campotexto' id='escolaridade' size='15' 
onFocus="document.all.escolaridade.style.background='#CCFFCC'" 
onBlur="document.all.escolaridade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Instituíção:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;
<input name='instituicao' type='text' class='campotexto' id='titulo' size='20' 
onFocus="document.all.instituicao.style.background='#CCFFCC'" 
onBlur="document.all.instituicao.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Curso:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='curso' type='text' class='campotexto' id='zona' size='10' 
onFocus="document.all.curso.style.background='#CCFFCC'" 
onBlur="document.all.curso.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Número de Filhos:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;&nbsp;
<input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' 
onFocus="document.all.filhos.style.background='#CCFFCC'" 
onBlur="document.all.filhos.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
<div align='right'></div>    <div align='right'></div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;&nbsp;
<input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' 
onFocus="document.all.filho_1.style.background='#CCFFCC'" 
onBlur="document.all.filho_1.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_1'
onFocus="document.all.data_filho_1.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_1.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' 
onFocus="document.all.filho_2.style.background='#CCFFCC'" 
onBlur="document.all.filho_2.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_2'
onFocus="document.all.data_filho_2.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_2.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' 
onFocus="document.all.filho_3.style.background='#CCFFCC'" 
onBlur="document.all.filho_3.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_3'
onFocus="document.all.data_filho_3.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_3.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' 
onFocus="document.all.filho_4.style.background='#CCFFCC'" 
onBlur="document.all.filho_4.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_4'
onFocus="document.all.data_filho_4.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_4.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;&nbsp;
<input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' 
onFocus="document.all.filho_5.style.background='#CCFFCC'" 
onBlur="document.all.filho_5.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_5'
onFocus="document.all.data_filho_5.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_5.style.background='#FFFFFF'" 
onkeyup="mascara_data(this)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr>
<td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>APARÊNCIA</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'>
<div align='right' class='style39'>Cabelos:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;<select name='cabelos' id='cabelos'>
<option>Loiro</option>
<option>Castanho Claro</option>
<option>Castanho Escuro</option>
<option>Ruivo</option>
<option>Pretos</option>
</select>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Olhos:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>&nbsp;&nbsp;
<select name='olhos' id='olhos'>
<option>Castanho Claro</option>
<option>Castanho Escuro</option>
<option>Verde</option>
<option>Azul</option>
<option>Mel</option>
<option>Preto</option>
</select>
</span></span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Peso:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='peso' type='text' class='campotexto' id='peso' size='5' 
onFocus="document.all.peso.style.background='#CCFFCC'" 
onBlur="document.all.peso.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</span></span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Altura:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='altura' type='text' class='campotexto' id='altura' size='5' 
onFocus="document.all.altura.style.background='#CCFFCC'" 
onBlur="document.all.altura.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
&nbsp;&nbsp; </span></span></td>
<td colspan='3' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Marcas ou Cicatriz aparente:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='defeito' type='text' class='campotexto' id='defeito' size='18' 
onFocus="document.all.defeito.style.background='#CCFFCC'" 
onBlur="document.all.defeito.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr>
<td colspan='8' bgcolor='#FFFFFF' class='style1'>&nbsp;</td>
</tr>
<tr height='30'>
<td colspan='8' bgcolor='#FFFFFF' class='style1'>
<div align='center' class='style39'>
Enviar Foto:
<input name='foto' type='checkbox' id='foto' onClick="document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;" value='1'/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name='arquivo' type='file' id='arquivo' size='60' style='display:none'/>
</div></td>
</tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='8' bgcolor='#003300' class='style1'><div align='center' class='style43'>DOCUMENTAÇÃO</div></td>
  </tr>
  <tr height='30'>
    <td width='16%' bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Nº do RG:&nbsp;</div></td>
    <td width='12%' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
	<input name='rg' type='text' id='rg' size='13' maxlength='14' class='campotexto'
                OnKeyPress="formatar('##.###.###-#', this)" 
                onFocus="document.all.rg.style.background='#CCFFCC'" 
                onBlur="document.all.rg.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(12,this.id,orgao.id)">
    </td>
    <td width='15%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Orgão Expedidor:&nbsp;</div></td>
    <td width='9%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='orgao' type='text' class='campotexto' id='orgao' size='8'
onFocus="document.all.orgao.style.background='#CCFFCC'" 
onBlur="document.all.orgao.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span> </td>
    <td width='5%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td width='7%' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='uf_rg' type='text' class='campotexto' id='uf_rg' size='2' maxlength='2' 
                onfocus="document.all.uf_rg.style.background='#CCFFCC'" 
                onblur="document.all.uf_rg.style.background='#FFFFFF'"
				onKeyUp="pula(2,this.id,data_rg.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td width='18%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Expedição:&nbsp;</div></td>
    <td width='18%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='data_rg' type='text' class='campotexto' size='12' maxlength='10'
		id='data_rg'
        onFocus="document.all.data_rg.style.background='#CCFFCC'" 
        onBlur="document.all.data_rg.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,cpf.id)"
        style='background:#FFFFFF;'/>
		
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CPF:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='cpf' type='text' class='campotexto' id='cpf' size='17' maxlength='14'
                OnKeyPress="formatar('###.###.###-##', this)" 
                onFocus="document.all.cpf.style.background='#CCFFCC'" 
                onBlur="document.all.cpf.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(14,this.id,reservista.id)"/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Certificado de Reservista:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
      <input name='reservista' type='text' class='campotexto' id='reservista' 
	  size='18'
                onFocus="document.all.reservista.style.background='#CCFFCC'" 
                onBlur="document.all.reservista.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Carteira de Trabalho:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='trabalho' type='text' class='campotexto' id='trabalho' size='15'
                onFocus="document.all.trabalho.style.background='#CCFFCC'" 
                onBlur="document.all.trabalho.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Série:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
     <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='10'
        onfocus="document.all.serie_ctps.style.background='#CCFFCC'"
        onblur="document.all.serie_ctps.style.background='#FFFFFF'" style='background:#FFFFFF;'/>
          </span>
		  
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
	<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' 
                onfocus="document.all.uf_ctps.style.background='#CCFFCC'" 
                onblur="document.all.uf_ctps.style.background='#FFFFFF'" 
				onKeyUp="pula(2,this.id,data_ctps.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data carteira de Trabalho:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
      &nbsp;&nbsp;
      
      <input name='data_ctps' type='text' class='campotexto' size='12' maxlength='10' id='data_ctps'
        onFocus="document.all.data_ctps.style.background='#CCFFCC'" 
        onBlur="document.all.data_ctps.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)"
        style='background:#FFFFFF;'/>
      
    </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Título de Eleitor:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='titulo' type='text' class='campotexto' id='titulo2' size='10'
                onFocus="document.all.titulo2.style.background='#CCFFCC'" 
                onBlur="document.all.titulo2.style.background='#FFFFFF'" 
                style='background:#FFFFFF;' />
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'> Zona:&nbsp;</span></div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='zona' type='text' class='campotexto' id='zona2' size='3'
                onFocus="document.all.zona2.style.background='#CCFFCC'" 
                onBlur="document.all.zona2.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Seção:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='secao' type='text' class='campotexto' id='secao' size='3'
                onFocus="document.all.secao.style.background='#CCFFCC'" 
                onBlur="document.all.secao.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style28'><span class='style39'>PIS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='pis' type='text' class='campotexto' id='pis' size='12'
                onFocus="document.all.pis.style.background='#CCFFCC'" 
                onBlur="document.all.pis.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Pis:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;

    <input name='data_pis' type='text' class='campotexto' size='12' maxlength='10' id='data_pis'
        onFocus="document.all.data_pis.style.background='#CCFFCC'" 
        onBlur="document.all.data_pis.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,fgts.id)"
        style='background:#FFFFFF;'/>
	
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>FGTS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='fgts' type='text' class='campotexto' id='fgts' size='10'
                onFocus="document.all.fgts.style.background='#CCFFCC'" 
                onBlur="document.all.fgts.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>BENEFÍCIOS</div></td>
  </tr>
  <tr height='30'>
    <td width='19%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Assistência Médica:&nbsp;</div>	</td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>
	
	<table width='100%' class=linha>
<tr> 
<td width='74'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='1'>Sim</label></span></td><td width='255'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='0' checked>Não</label></span></td>
</tr>
</table>	</td>
    <td width='19%' bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Tipo de Plano:&nbsp;</div></td>
    <td width='19%' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
<select name='plano_medico' class='campotexto' id='plano_medico'>

<option value=1 >Familiar</option>
<option value=2 selected>Individual</option>
</select>   </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Seguro, Apólice:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'><span class='style39'>&nbsp;&nbsp;
          <select name='apolice' class='campotexto' id='apolice'>
<option value='0'>Não Possui</option>";

$result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $id_regiao", $conn);
while ($row_ap = mysql_fetch_array($result_ap)){

  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";

}


print "
</select>
        </select>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Dependente:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='dependente' type='text' class='campotexto' id='dependente' size='20'
onFocus="document.all.dependente.style.background='#CCFFCC'" 
onBlur="document.all.dependente.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Insalubridade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>&nbsp;&nbsp;
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1'/></td>
    
	<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Vale Transporte:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
    &nbsp;<input name='transporte' type='checkbox' id='transporte2' value='1'/>    </td>
  </tr>
  
  
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo de Vale:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='5'><span class='style39'>
      &nbsp;&nbsp;
      <select name='tipo_vale' class='campotexto'>
            <option value='1'>Cartão</option>
            <option value='2'>Papel</option>
			<option value='3'>Ambos</option>
          </select>
    </span></td>
  </tr>
  
  
  
  
  
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cartão 1:&nbsp;</div></td>
    <td width='15%' bgcolor='#FFFFFF' class='style1'><span class='style39'>
      &nbsp;
      <input name='num_cartao' type='text' class='campotexto' id='num_cartao' size='12'
onfocus="document.all.num_cartao.style.background='#CCFFCC'" 
onblur="document.all.num_cartao.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
    </span></td>
    <td width='15%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor Total 1:&nbsp;</div></td>
    <td width='13%' bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='valor_cartao' type='text' class='campotexto' id='valor_cartao' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.valor_cartao.style.background='#CCFFCC'" 
              onblur="document.all.valor_cartao.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Cartão 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo_cartao_1' type='text' class='campotexto' id='tipo_cartao_1' size='12' 
	   onChange="this.value=this.value.toUpperCase()"
              onfocus="document.all.tipo_cartao_1.style.background='#CCFFCC'" 
              onblur="document.all.tipo_cartao_1.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cartão 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
      &nbsp;
      <input name='num_cartao2' type='text' class='campotexto' id='num_cartao2' size='12' 
onfocus="document.all.num_cartao2.style.background='#CCFFCC'" 
onblur="document.all.num_cartao2.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor Total 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='valor_cartao2' type='text' class='campotexto' id='valor_cartao2' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.valor_cartao2.style.background='#CCFFCC'" 
              onblur="document.all.valor_cartao2.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Cartão 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo_cartao_2' type='text' class='campotexto' id='tipo_cartao_2' size='12' 
              onChange="this.value=this.value.toUpperCase()" 
              onfocus="document.all.tipo_cartao_2.style.background='#CCFFCC'" 
              onblur="document.all.tipo_cartao_2.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  
   
  
  <tr height='30'>
    <td  bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Papel: &nbsp;&nbsp;Quantidade 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_qnt_1' type='text' class='campotexto' id='vale_qnt_1' size='3'
onFocus="document.all.vale_qnt_1.style.background='#CCFFCC'" 
onBlur="document.all.vale_qnt_1.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_1' type='text' class='campotexto' id='vale_valor_1' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.vale_valor_1.style.background='#CCFFCC'" 
              onblur="document.all.vale_valor_1.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo1' type='text' class='campotexto' id='tipo1' size='12' 
              onChange="this.value=this.value.toUpperCase()"
              onfocus="document.all.tipo1.style.background='#CCFFCC'" 
              onblur="document.all.tipo1.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
      <input name='vale_qnt_2' type='text' class='campotexto' id='vale_qnt_2' size='3' 
onFocus="document.all.vale_qnt_2.style.background='#CCFFCC'" 
onBlur="document.all.vale_qnt_2.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_2' type='text' class='campotexto' id='vale_valor_2' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.vale_valor_2.style.background='#CCFFCC'" 
              onblur="document.all.vale_valor_2.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo2' type='text' class='campotexto' id='tipo2' size='12' 
              onChange="this.value=this.value.toUpperCase()"
              onfocus="document.all.tipo2.style.background='#CCFFCC'" 
              onblur="document.all.tipo2.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
      <input name='vale_qnt_3' type='text' class='campotexto' id='vale_qnt_3' size='3' 
onFocus="document.all.vale_qnt_3.style.background='#CCFFCC'" 
onBlur="document.all.vale_qnt_3.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_3' type='text' class='campotexto' id='vale_valor_3' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.vale_valor_3.style.background='#CCFFCC'" 
              onblur="document.all.vale_valor_3.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo3' type='text' class='campotexto' id='tipo3' size='12' 
               onChange="this.value=this.value.toUpperCase()"
              onfocus="document.all.tipo3.style.background='#CCFFCC'" 
              onblur="document.all.tipo3.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_qnt_4' type='text' class='campotexto' id='vale_qnt_4' size='3' 
onFocus="document.all.vale_qnt_4.style.background='#CCFFCC'" 
onBlur="document.all.vale_qnt_4.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_4' type='text' class='campotexto' id='vale_valor_4' size='12' 
              onkeydown="FormataValor(this,event,20,2)" 
              onfocus="document.all.vale_valor_4.style.background='#CCFFCC'" 
              onblur="document.all.vale_valor_4.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo4' type='text' class='campotexto' id='tipo4' size='12' 
               onChange="this.value=this.value.toUpperCase()"
              onfocus="document.all.tipo4.style.background='#CCFFCC'" 
              onblur="document.all.tipo4.style.background='#FFFFFF'" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Adicional Noturno:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>
	
<table class='linha'>
<tr> 
<td width='98'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='1'>Sim</label></td>
<td width='86'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='0' checked>Não</label></td>
</tr>
</table>	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Integrante do CIPA:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
	
<table class='linha'>
<tr> 
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='cipa' value='1' >Sim</label></td>
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='cipa' value='0' checked>Não</label></td>
</tr>
</table>	</td>
  </tr>
</table>




<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS BANCÁRIOS</div></td>
</tr>
<tr height='30'>
<td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Banco:&nbsp;</div></td>
<td width='31%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
<select name='banco' class='campotexto' id='banco'>";
$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = '$projeto'");
while ($row_banco = mysql_fetch_array($result_banco)){
print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
}
print "
</select>
</span></td>
<td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Agência:&nbsp;</div></td>
<td width='35%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='agencia' type='text' class='campotexto' id='agencia' size='12' 
onFocus="document.all.agencia.style.background='#CCFFCC'" 
onBlur="document.all.agencia.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Conta:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class='style39'>
<input name='conta' type='text' class='campotexto' id='conta' size='12' 
onFocus="document.all.conta.style.background='#CCFFCC'" 
onBlur="document.all.conta.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome do Banco:&nbsp;<br /> 
<span class='style49'>(caso não esteja na lista acima)&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='nomebanco' type='text' class='campotexto' id='nomebanco' size='50' 
onFocus="document.all.nomebanco.style.background='#CCFFCC'" 
onBlur="document.all.nomebanco.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
</table>
<span class='style1'><br />
</span>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS FINANCEIROS E DE CONTRATO</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Data de Entrada:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='data_entrada' type='text' class='campotexto' size='12' maxlength='10' id='data_entrada'
onFocus="document.all.data_entrada.style.background='#CCFFCC'" 
onBlur="document.all.data_entrada.style.background='#FFFFFF'" 
onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)"
style='background:#FFFFFF;'/>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Data do Exame Admissional:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;
<input name='data_exame' type='text' class='campotexto' size='12' maxlength='10' id='data_exame'
onFocus="document.all.data_exame.style.background='#CCFFCC'" 
onBlur="document.all.data_exame.style.background='#FFFFFF'" 
onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)"
style='background:#FFFFFF;'/>
</td>
</tr>
<tr height='30'>
<td width='23%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Local de Pagamento:&nbsp;</div></td>
<td width='77%' colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
<input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25'  
onFocus="document.all.localpagamento.style.background='#CCFFCC'" 
onBlur="document.all.localpagamento.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Tipo de Pagamento:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<select name='tipopg' class='campotexto' id='tipopg'>";
$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$projeto'");
while ($row_pg = mysql_fetch_array($result_pg)){
print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
}
print "
</select>
&nbsp;</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Observações:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'  
onFocus="document.all.observacoes.style.background='#CCFFCC'" 
onBlur="document.all.observacoes.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"></textarea></td>
</tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td width='254%' colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style39'>FINALIZAÇÃO DO CADASTRAMENTO</div></td>
</tr>
<tr height='30'>
<td colspan='4' bgcolor='#FFFFCC' class='style1'>
<div align='center' class='style39'>
<p><br> O contrato foi ASSINADO?
&nbsp;&nbsp;
<input name='impressos2' type='checkbox' id='impressos2' value='1' />
</p>
<br>
O Distrato foi ASSINADO?
&nbsp;&nbsp;
<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='1' $selected_ass_sim2> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='0' $selected_ass_nao2> Não</label></td>
</tr></table>
<br>
Outros documentos foram ASSINADO?
&nbsp;&nbsp;
<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='1' $selected_ass_sim3> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='0' $selected_ass_nao3> Não</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mensagem_ass</td>
</tr></table>

<br>
<p>

<span class='style47'>NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span>

<br />
</p>
<table width='200' border='0' align='center' cellpadding='0' cellspacing='0'>
<tr height='30'>
<td align='center' class='style7'>-</td>
<td align='center' valign='middle' class='style7'><input type='submit' name='Submit' value='CADASTRAR' class='campotexto' />
<br /></td>
</tr>
</table>
<br />
<div align='center'><span class='style7'>


</span><br />
</div>
</div></td>
</tr>
</table>
<span class='style7'>

<input type='hidden' name='regiao' value='$id_regiao'/>
<input type='hidden' name='id_cadastro' value='4'>
<input type='hidden' name='id_projeto' value='$projeto'>
<input type='hidden' name='user' value='$id_user'>

</span></td>
</tr>
</table>
</form><br><a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0></a>
<script>
function validaForm(){
d = document.form1;
if (d.nome.value == "" ){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.endereco.value == "" ){
alert("O campo Endereço deve ser preenchido!");
d.endereco.focus();
return false;
}
if (d.data_nasci.value == "" ){
alert("O campo Data de Nascimento deve ser preenchido!");
d.data_nasci.focus();
return false;
}
if (d.rg.value == "" ){
alert("O campo RG deve ser preenchido!");
d.rg.focus();
return false;
}
if (d.cpf.value == "" ){
alert("O campo CPF deve ser preenchido!");
d.cpf.focus();
return false;
}
if (d.localpagamento.value == "" ){
alert("O campo Local de Pagamento deve ser preenchido!");
d.localpagamento.focus();
return false;
}
return true;   }
</script>
<?php
break;

case 5:  							//CASO O ID SEJA 2 ELE VAI RODAR O - CADASTRO DE APÓLICES -

$id_regiao = $_REQUEST['regiao'];
?>

<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='4' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrodeapolices.gif'><BR>Cadastro de Apólice</div><BR></td>
</tr>
<tr>
<td width='7%' align="left" valign="top"><img src='imagens/arre_cima1.gif' width='21' height='18' /></td>
<td width='30%'>&nbsp;</td>
<td width='56%'>&nbsp;</td>
<td width='7%' align="right" valign="top"><img src='imagens/arre_cima2.gif' alt='' width='18' height='21' /></td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td height="25" align="right">&nbsp;&nbsp;Nome do Banco:</td>
<td height="25">&nbsp;
  <input name='banco2' type='text' class='campotexto' id='banco2' size='20'>
  &nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td height="25" align="right">&nbsp;&nbsp;Raz&atilde;o Social do Banco:&nbsp;</td>
<td height="25">&nbsp;  <input name='razao' type='text' class='campotexto' id='razao' size='30'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td height="25" align="right">&nbsp;&nbsp;Ap&oacute;lice:</td>
<td height="25">&nbsp;  <input name='apolice2' type='text' class='campotexto' id='apolice2' size='10'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td height="25" align="right">&nbsp;&nbsp;Contrato:</td>
<td height="25">&nbsp;  <input name='contrato' type='text' class='campotexto' id='contrato' size='20'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td align='right'>&nbsp;</td>
<td height="25" align="right">Telefone:</td>
<td height="25">&nbsp;
  <input name='tel3' type='text' class='campotexto' id='tel3' size='10' value='(  )'>
  &nbsp;&nbsp;&nbsp;&nbsp; Gerente:&nbsp;&nbsp;
  <input name='gerente2' type='text' class='campotexto' id='gerente2' size='10'></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='4' align='center'><input type='submit' name='Submit3' value='CADASTRAR' class='campotexto'>
<input type='hidden' name='id_cadastro' value='8'>
<input type='hidden' name='regiao' value='$id_regiao'></td>
</tr>
<tr>
  <td align='left' valign="bottom"><img src='imagens/arre_baixo1.gif' alt='' width='18' height='21' /></td>
  <td align='center'>&nbsp;</td>
  <td align='center'>&nbsp;</td>
  <td align='right' valign="bottom"><img src='imagens/arre_baixo2.gif' alt='' width='21' height='18' /></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.banco.value == ""){
alert("O campo Nome do Banco deve ser preenchido!");
d.banco.focus();
return false;
}
if (d.razao.value == "" ){
alert("O campo Razão Social do Banco deve ser preenchido!");
d.razao.focus();
return false;
}
if (d.conta.value == ""){
alert("O campo Conta deve ser preenchido!");
d.conta.focus();
return false;
}
if (d.agencia.value == "" ){
alert("O campo Agencia deve ser preenchido!");
d.agencia.focus();
return false;
}
if (d.contrato.value == ""){
alert("O campo Contrato deve ser preenchido!");
d.contrato.focus();
return false;
}
if (d.gerente.value == "" ){
alert("O campo Gerente deve ser preenchido!");
d.gerente.focus();
return false;
}
return true;   }
</script>
<?php
break;

case 6:  							//CASO O ID SEJA 6 ELE VAI RODAR O - CADASTRO DE BANCOS -

$id_regiao = $_REQUEST['regiao'];

?>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='600' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrodebancos.gif'><BR>Cadastro de Bancos</div><BR></td>
</tr>
<tr>
<td width='30%'>&nbsp;</td>
<td width='70%'>&nbsp;</td>
</tr>
<tr>
<td width='30%' align='right'>Projeto:</td>
<td width='70%'>&nbsp;&nbsp; <select name='projeto' class='campotexto'>
<?php

$result_pro1 = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao'");
while ($row_pro1 = mysql_fetch_array($result_pro1)){
print "<option value=$row_pro1[0]>$row_pro1[0] - $row_pro1[nome]</option>";
}
?>

</select></td>
</tr>
<tr>
<td align='right'>Selecione o Banco:</td>
<td>&nbsp;&nbsp; <select name='banco' type='text' class='campotexto' id='banco'>
<?php
$result_banco = mysql_query("SELECT * FROM listabancos ORDER BY banco");
while ($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[2]</option>";
}
?>
</select>
</td>
</tr>
<tr>
<td align='right'>Nome para Exibição:</td>
<td>&nbsp;&nbsp; <input name='nom_banco' type='text' class='campotexto' id='nom_banco' size='31'>
&nbsp;&nbsp;&nbsp;&nbsp;<font size=1 color=#999999>( Ex: Real - Educação )</font>
</td>
</tr>
<tr>
<td align='right'>Localidade:</td>
<td>&nbsp;&nbsp; <input name='localidade' type='text' class='campotexto' id='localidade' size='31'>
&nbsp;&nbsp;&nbsp;&nbsp;<font size=1 color=#999999>( Ex: Mauá, Itaboraí )</font>
</td>
</tr>
<tr>
<td align='right'>Conta Corrente:</td>
<td>
&nbsp;&nbsp; <input name='conta' type='text' class='campotexto' id='conta' size='10'>
&nbsp;&nbsp; Agência:&nbsp;&nbsp;<input name='agencia' type='text' class='campotexto' id='agencia' size='5'>
</td>
</tr>
<tr>
<td align='right'>Endereço:</td>
<td>&nbsp;&nbsp; <input name='endereco' type='text' class='campotexto' id='endereco' size='31'></td>
</tr>
<tr>
<td align='right'>Telefone:</td>
<td>
&nbsp;&nbsp; <input name='tel' type='text' class='campotexto' id='tel' size='10' value='(  )'>
&nbsp;&nbsp; Gerente:&nbsp;&nbsp;<input name='gerente' type='text' class='campotexto' id='gerente' size='10'>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'> <BR><BR>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR><BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='9'>
<input type='hidden' name='regiao' value='$id_regiao'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.banco.value == ""){
alert("O campo Nome do Banco deve ser preenchido!");
d.banco.focus();
return false;
}
if (d.razao.value == "" ){
alert("O campo Razão Social do Banco deve ser preenchido!");
d.razao.focus();
return false;
}
if (d.conta.value == ""){
alert("O campo Conta deve ser preenchido!");
d.conta.focus();
return false;
}
if (d.agencia.value == "" ){
alert("O campo Agência deve ser preenchido!");
d.agencia.focus();
return false;
}
if (d.endereco.value == ""){
alert("O campo Endereço deve ser preenchido!");
d.endereco.focus();
return false;
}
if (d.gerente.value == "" ){
alert("O campo Gerente deve ser preenchido!");
d.gerente.focus();
return false;
}
return true;   }
</script>
<?php
break;

case 7:  							//CASO O ID SEJA 7 ELE VAI RODAR O - CADASTRO DE CURSOS/ATIVIDADES -

$id_regiao = $_REQUEST['regiao'];

?>

<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<div align='left' class='style1'> <img src='imagens/cadastrodecursos.gif'><BR>Cadastro de Cursos</div><BR>

<br>

<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS DO CURSO</div></td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style17'><div align='right' class='Texto10'>
Projeto:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1' colspan='3'>
&nbsp;&nbsp; <select name='projeto' class='campotexto'>
<?php

$result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao'");
while ($row_pro = mysql_fetch_array($result_pro)){
print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
}

?></select>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style17'><div align='right' class='Texto10'>
Tipo Contratação:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style17'>

<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='1' 
onClick="document.all.tabelaclt.style.display = 'none'; document.all.tabelaoutros.style.display = ''; "/> 
  <span class="Texto10">Participante</span></label>
<br>

<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='2' 
onClick="document.all.tabelaclt.style.display = ''; document.all.tabelaoutros.style.display = 'none'; "/> 
  <span class="Texto10">CLT</span></label>
<br>

<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='3' 
onClick="document.all.tabelaclt.style.display = 'none'; document.all.tabelaoutros.style.display = ''; "/> 
  <span class="Texto10">Cooperado</span></label>

</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style17'><div align='right' class='Texto10'>
Nome da Atividade:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style17'>
&nbsp;&nbsp; 
<input name='atividade' type='text' class='campotexto' id='atividade' size='50' 
onFocus="document.all.atividade.style.background='#CCFFCC'"
onBlur="document.all.atividade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" />
</td>
</tr>
</table>


<br><br>


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' id='tabelaclt' style='display:none'>
<tr>
<td colspan='2' bgcolor='#003300' class='style1'><div align='center' class='style43'>CBO</div></td>
</tr>
<tr>
<td  width='15%' height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='Texto10'>

CBO:&nbsp;</div></td>

<td width='85%' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 

<input type='text' name='pesquisa_usuario' SIZE='30' id='pesquisa_usuario' autocomplete='off' 
onFocus="document.all.pesquisa_usuario.style.background='#CCFFCC'"
onBlur="document.all.pesquisa_usuario.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()" >

&nbsp;&nbsp;

<a href='#' onClick="searchSuggest();"><span class='style39'>Procurar</span></a>

<input type='hidden' name='id_cbo' id='id_cbo' maxlength='6' >

</tr>
<tr>
<td colspan='2'><div id='ajax'></div></td>
</tr>

<tr>
<td colspan='2' bgcolor='#003300' class='style1'><div align='center' class='style43'>SALÁRIO</div></td>
</tr>
<tr>
<td width='15%' height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='Texto10'>
Salário:&nbsp;</div></td>
<td width='85%' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='salario' type='text' class='campotexto' id='salario' size='20' 
onFocus="document.all.salario.style.background='#CCFFCC'"
onBlur="document.all.salario.style.background='#FFFFFF'" 
style='background:#FFFFFF;' OnKeyDown="FormataValor(this,event,17,2)"/>
&nbsp;&nbsp; 
<a href='#' onClick="calc();"><span class='style39'>Calcular</span></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span class='Texto10' id='resultado'></span>
</tr>

<tr>
  <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='Texto10'>Mês Abono:&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
   
    <select name='mes_abono' id='mes_abono' class='campotexto'>
      <option value='01'>Janeiro</option>
      <option value='02'>Fevereiro</option>
      <option value='03'>Março</option>
      <option value='04' selected>Abril</option>
      <option value='05'>Maio</option>
      <option value='06'>Junho</option>
      <option value='07'>Julho</option>
      <option value='08'>Agosto</option>
      <option value='09'>Setembro</option>
      <option value='10'>Outubro</option>
      <option value='11'>Novembro</option>
      <option value='12'>Dezembro</option>
    </select>
	&nbsp;&nbsp;
	<input type='hidden' name='enquadramento' id='enquadramento' class='campotexto'>
	</td>
</tr>

</table>



<br><Br>



<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' id='tabelaoutros'  style='display:none'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS COMPLEMENTARES DO CURSO</div></td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Projeto:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1' colspan='3'>
&nbsp;&nbsp; <select id=tipo name=tipo class='campotexto'>
<option value=SOE>SOE</option>
<option value=LATINO>LATINO</option>
</select>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Nome do Curso:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='nome' type='text' class='campotexto' id='nome' size='50' 
onFocus="document.all.nome.style.background='#CCFFCC'"
onBlur="document.all.nome.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Área:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='area' type='text' class='campotexto' id='area' size='40' 
onFocus="document.all.area.style.background='#CCFFCC'"
onBlur="document.all.area.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Local:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='local' type='text' class='campotexto' id='local' size='40' 
onFocus="document.all.local.style.background='#CCFFCC'"
onBlur="document.all.local.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Inicio:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='ini' type='text' id='ini' size='12' class='campotexto' maxlength='10'
onKeyUp="mascara_data(this); pula(10,this.id,fim.id)"
onFocus="document.all.ini.style.background='#CCFFCC'" 
onBlur="document.all.ini.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Final:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='fim' type='text' id='fim' size='12' class='campotexto' maxlength='10'
onKeyUp="mascara_data(this); pula(10,this.id,nome.id)"
onFocus="document.all.fim.style.background='#CCFFCC'" 
onBlur="document.all.fim.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Valor:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
<div class='style39'>&nbsp;&nbsp; 
<input name='valor' type='text' id='valor' size='11' class='campotexto' maxlength='13'
onFocus="document.all.valor.style.background='#CCFFCC'" 
onBlur="document.all.valor.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>
&nbsp;&nbsp;&nbsp;&nbsp;
<font size='2' font='Arial' color='#666666'>ex: 12000,00</font>
&nbsp;&nbsp;&nbsp;&nbsp;
Parcelas:&nbsp;&nbsp;
<input name='parcelas' type='text' id='parcelas' size='10' class='campotexto' maxlength='13'
onFocus="document.all.parcelas.style.background='#CCFFCC'" 
onBlur="document.all.parcelas.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>  </div>
</td>
</tr>
<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Descrição:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<textarea name='descricao' cols='35' rows='5' class='campotexto'  id='descricao'
onFocus="document.all.descricao.style.background='#CCFFCC'" 
onBlur="document.all.descricao.style.background='#FFFFFF'" 
style='background:#FFFFFF;'></textarea>
</td>
</tr>
</table>

<br>

<center>
<input type='submit' name='Submit' value='CADASTRAR' class='campotexto'>
</center>
<input type='hidden' name='id_cadastro' value='12'>
<input type='hidden' name='regiao' value='$id_regiao'>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>

function calc(){

var total = "0"
var valor = document.form1.salario.value
valor = valor.replace( ".", "" );
valor = valor.replace( ".", "" );
valor = valor.replace( ",", "." );
total = valor*12;

if (total >= 15764.28){

msg = "Declarante de IR, pois o salário anual é: "+total+"!";
document.all.enquadramento.value = '1';

} else {

msg = "NÃO Declarante de IR, pois o salário anual é: "+total+"!";
document.all.enquadramento.value = '0';

}

document.getElementById('resultado').innerText=msg;

}


function validaForm(){

d = document.form1;

if (document.form1.contratacao[1].checked && d.enquadramento.value == ""){
alert("ATENÇÃO, se o tipo de contratação for CLT é nescessário calcular o SALÁRIO!");
d.salario.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.atividade.value == ""){
alert("O campo Atividade deve ser preenchido!");
d.atividade.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.nome.value == ""){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.area.value == "" ){
alert("O campo Área deve ser preenchido!");
d.area.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.local.value == ""){
alert("O campo Local deve ser preenchido!");
d.local.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.ini.value == ""){
alert("O campo Inicio deve ser preenchido!");
d.ini.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.fim.value == ""){
alert("O campo Término deve ser preenchido!");
d.fim.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.valor.value == "" ){
alert("O campo Valor deve ser preenchido!");
d.valor.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.parcela.value == "" ){
alert("O campo Parcela deve ser preenchido!");
d.parcela.focus();
return false;
}
if (!document.form1.contratacao[1].checked && d.descricao.value == "" ){
alert("O campo Descrição deve ser preenchido!");
d.descricao.focus();
return false;
}

return true;   }
</script>

<?php
break;

case 9:

$user = $_REQUEST['id_user'];
$regi_atu = $_REQUEST['regi_atu'];

?>
<body bgcolor='#D7E6D5'>
<form action='cadastro2.php' method='post' name='form1'>
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrodecursos.gif'><BR>Alterando Região</div><BR></td>
</tr>
<tr>
<td width='30%'>&nbsp;</td>
<td width='70%'>&nbsp;</td>
</tr>
<tr>
<td align='right'>Alterar para a Região:</td>
<td>&nbsp;&nbsp; <select name='regiao' class='campotexto' id='regiao'>
<?php
while ($row = mysql_fetch_array($result)){
$row_regiao = "$row[id_regiao]";
if ($id_regiao == "$row_regiao"){
print "<option value=$row[id_regiao] selected>$row[regiao] - $row[sigla]</option>";
} else {
print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
}
}
?>
</select></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> 
</td>
<td align='center' valign='middle'> 
<input type='submit' name='Submit' value='ALTERAR' class='campotexto'> <BR><BR>
</td>
</tr>
</table>
<input type='hidden' name='regiao_de' value='$regi_atu'>
<input type='hidden' name='id_cadastro' value='13'>
<input type='hidden' name='user' value='$user'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>
<?php

break;

case 10:                                      //MARCAÇÃO DO PONTO

$id_user = $_REQUEST['id_user'];
$id_reg = $_REQUEST['id_reg'];
$data = date('d/m/Y');
$data2 = date('Y/m/d');
$hora = date('H:i');

$consulta = mysql_query("SELECT entrada1,saida1,entrada2,saida2 FROM ponto where id_funcionario = '$id_user' and id_regiao = '$id_reg' and data = '$data2'", $conn) or die("Erro no sql");
$row = mysql_fetch_array($consulta);

if ($row['0'] == ""){ 
$entrada1 = "<input name='radiobutton' type='radio' value='1'>";
$saida1 = "<font color=#FFFF00>Marque a Entrada</font>";
$entrada2 = "<font color=#FFFF00>Marque a Saída</font>";
$saida2 = "<font color=#FFFF00>Marque a Volta</font>";
$bt = "<input type='submit' name='Enviar' value='Enviar'>";
$justificativa = "1";
}elseif ($row['1'] == "00:00:00"){
$entrada1 = "<font size=3><b>$row[0]</b></font>";
$saida1 = "<input name='radiobutton' type='radio' value='2'>";
$entrada2 = "<font color=#FFFF00>Marque a Saída</font>";
$saida2 = "<font color=#FFFF00>Marque a Volta</font>";
$bt = "<input type='submit' name='Enviar' value='Enviar'>";
$justificativa = "2";
}elseif ($row['2'] == "00:00:00"){
$entrada1 = "<font size=3><b>$row[0]</b></font>";
$saida1 = "<font size=3><b>$row[1]</b></font>";
$entrada2 = "<input name='radiobutton' type='radio' value='3'>";
$saida2 = "<font color=#FFFF00>Marque a Volta</font>";
$bt = "<input type='submit' name='Enviar' value='Enviar'>";
$justificativa = "3";
}elseif ($row['3'] == "00:00:00"){
$entrada1 = "<font size=3><b>$row[0]</b></font>";
$saida1 = "<font size=3><b>$row[1]</b></font>";
$entrada2 = "<font size=3><b>$row[2]</b></font>";
$saida2 = "<input name='radiobutton' type='radio' value='4'>";
$bt = "<input type='submit' name='Enviar' value='Enviar'>";
$justificativa = "4";
}else{
$entrada1 = "<font size=3><b>$row[0]</b></font>";
$saida1 = "<font size=3><b>$row[1]</b></font>";
$entrada2 = "<font size=3><b>$row[2]</b></font>";
$saida2 = "<font size=3><b>$row[3]</b></font>";
$bt = "";
$justificativa = "0";
}

?>
<body bgcolor='#D7E6D5'>
<form action='cadastro2.php' method='post' name='form1' onSubmit='return valida()'>
<table width='400' border='0' cellspacing='0' cellpadding='0' align='center'>
<tr>
<td colspan='4'><div align='left' class='style1'> <img src='imagens/ponto.gif'><BR></div><BR></td>
</tr>
<tr>
<td colspan='4'>&nbsp;</td>
</tr>
<tr>
<td colspan='4'><div align='center'><font size=4><b>Data: $data <br> Hora: $hora</b></font></td>
</tr>
<tr>
<td colspan='4'>&nbsp;</td>
</tr>
<tr>
<td><div align='center' class='style1'>Entrada</div></td>
<td><div align='center' class='style1'>Sa&iacute;da Almo&ccedil;o </div></td>
<td><div align='center' class='style1'>Volta Almo&ccedil;o </div></td>
<td><div align='center' class='style1'>Sa&iacute;da</div></td>
</tr>
<tr>
<td><div align='center'>
$entrada1
</div></td>
<td><div align='center'>
$saida1 
</div></td>
<td><div align='center'>
$entrada2 
</div></td>
<td><div align='center'>
$saida2
</div></td>
</tr>
<tr>
<td colspan='4' align='center' class='style1'><br><br>Justificativa:<br>
<textarea name=justifica<?=$justificativa?> id=justifica<?=$justificativa?> cols=35 rows=5></textarea></td>
</tr>
<tr>
<td height='35' colspan='4' align='center' valign='middle'><br><br>$bt</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='14'>
<input type='hidden' name='regiao' value='$id_reg'>
<input type='hidden' name='user' value='$id_user'></td>
</form><center><br><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";
print "<script>
<!--
function valida(){
if (!document.form1.radiobutton[0].checked && !document.form1.radiobutton[1].checked && !document.form1.radiobutton[2].checked && !document.form1.radiobutton[3].checked) {
alert ("Escolha uma marcação de Ponto");
return false;
}
}
//-->
</script>
<?php

break;

//											Cadastro de Atividades

case 11:    
$id_regiao = $_REQUEST['regiao'];

?>

<body bgcolor='#D7E6D5'>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastroativi.gif'><BR>Cadastro de Atividades</div><BR></td>
</tr>
<tr>
<td width='30%'>&nbsp;</td>
<td width='70%'>&nbsp;</td>
</tr>
<tr>
<td align='right'></td>
<td>&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td align='right'>Nome:</td>
<td>&nbsp;&nbsp; <input name='nome' type='text' class='campotexto' id='nome' size='20'></td>
</tr>
<tr>
<td align='right'>Área:</td>
<td>&nbsp;&nbsp; <input name='area' type='text' class='campotexto' id='area' size='31'></td>
</tr>
<tr>
<td align='right'>Região:</td>
<td>&nbsp;&nbsp; <select name='id_regiao' class='campotexto' id='regiao'>
<?php
while ($row = mysql_fetch_array($result)){
$row_regiao = "$row[id_regiao]";
if ($id_regiao == "$row_regiao"){
print "<option value=$row[id_regiao] selected>$row[regiao] - $row[sigla]</option>";
} else {
print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
}
}
?>
</select></td>
</tr>
<tr>
<td align='right'>Descrição:</td>
<td>&nbsp;&nbsp; <textarea name='descricao' cols='35' rows='5' class='campotexto'></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'> <BR><BR>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR><BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='15'>
<input type='hidden' name='regiao' value='$id_regiao'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.nome.value == ""){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.area.value == "" ){
alert("O campo Área deve ser preenchido!");
d.area.focus();
return false;
}
return true;   }
</script>
<?php
break;

case 12:                            //EDITANDO FUNCIONARIOS

$id_user = $_REQUEST['user'];
$pag = $_REQUEST['pag'];

$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'", $conn);
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['master'])){
$mostrar = "style='display:none'";
}else{
$mostrar = "";
}

$link_foto = $row_user['id_regiao']."funcionario".$row_user['0'].$row_user['foto'];

if($row_user['foto'] != "0"){
$link = "<img src='fotos/$link_foto' border=1 width='100' height='130'>";
$foto = "Deseja remover a foto? <label><input name='foto' type='checkbox' id='foto' value='3'/> Sim</label>";
}else{
$link = "<img src='fotos/semimagem.gif' border=1 width='100' height='130'>";
$foto = "Foto: <input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;\">";
}

?>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()"  enctype='multipart/form-data'>
<table width='660' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan=4 bgcolor='#5C7E59'><div align=left class='style1'><img src='imagens/cadastrodeusuarios.gif'> <BR>Cadastro de Usu&aacute;rio para acesso a Intranet</div><BR>
</td>
</tr>
<tr>
<td colspan=4 align=right>$link</td>
</tr>
<tr>
<td width='15%' align=right>Região:</td>
<td width='38%'>&nbsp;&nbsp; <select name='id_regiao' class='campotexto' id='regiao'>
<?php

while ($row = mysql_fetch_array($result)){

$regiao_atual = $row_user[id_regiao];
$regiao_atual2 = $row[id_regiao];

if ($regiao_atual == $regiao_atual2){
print "<option value='$row[id_regiao]' selected>$row[regiao] - $row[sigla]</option>";
}else{
print "<option value='$row[id_regiao]'>$row[regiao] - $row[sigla]</option>";
}
}

?>
</select></td>
<td width='11%' align=right></td>
<td width='36%'></td>
</tr>
<tr>
<td width='15%' align=right>Função:</td>
<td width='38%'>&nbsp;&nbsp; <input name='funcao' type='text' class='campotexto' id='funcao' size='30' value='$row_user[funcao]'>
<td width='12%' align=right>Lotação:</td>
<td width='35%'>&nbsp;&nbsp; <input name='locacao' type='text' class='campotexto' id='locacao' size='20' value='$row_user[locacao]'></td>
</tr>
<tr $mostrar>
<td width='15%' align=right>Grupo: </td>
<td width='38%' colspan=3>&nbsp;&nbsp; <select name='grupo_usuario' class='campotexto'>
<?php

$result_grupo = mysql_query("SELECT * FROM grupo", $conn);

while ($row_grupo = mysql_fetch_array($result_grupo)){
	
$r_grupo = "$row_grupo[id_grupo]";

if ($row_user['grupo_usuario'] == "$r_grupo"){
print "<option value=$row_grupo[id_grupo] selected>$row_grupo[nome]</option>";
}else{
print "<option value=$row_grupo[id_grupo]>$row_grupo[nome]</option>";
}
}

?>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp; Salário: R$&nbsp;&nbsp;<input name='salario' type='text' class='campotexto' id='salario' size='10' value='$row_user[salario]'> <font color=#999999 size=1>Somente números</font>
</td>
</tr>
<tr>
<td width='15%' align=right>Nome Completo:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='nome' type='text' class='campotexto' id='nome' size='35' value='$row_user[nome]'>
&nbsp;&nbsp;Nome para exibição: <input name='nome1' type='text' class='campotexto' id='nome1' size='15' value='$row_user[nome1]'>
</td>
</tr>
<tr>
<td width='15%' align=right>Endereco:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='endereco' type='text' class='campotexto' id='endereco' size='75' value='$row_user[endereco]'></td>
</tr>
<tr>
<td width='15%' align=right>Bairro:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='bairro' type='text' class='campotexto' id='bairro' size='15' value='$row_user[bairro]'>
&nbsp;&nbsp; Cidade:&nbsp;&nbsp; <input name='cidade' type='text' class='campotexto' id='cidade' size='12' value='$row_user[cidade]'>
&nbsp;&nbsp; UF:&nbsp;&nbsp; <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' value='$row_user[uf]'>
&nbsp;&nbsp; CEP:&nbsp;&nbsp; <input name='cep' type='text' class='campotexto' id='cep' size='12' value='$row_user[cep]'>
</td>
</tr>
<tr>
<td width='15%' align=right>Telefones:</td>
<td width='38%' colspan=3>&nbsp; Fixo:&nbsp;&nbsp; <input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' size='12' maxlength='14' value='$row_user[tel_fixo]'>
&nbsp; Cel:&nbsp;&nbsp; <input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='12' maxlength='14' value='$row_user[tel_cel]'>
&nbsp; Recado:&nbsp;&nbsp; <input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='12' maxlength='14' value='$row_user[tel_rec]'>
</td>
</tr>
<tr>
<td align=right>Data de </td>
<td colspan=3>&nbsp; Nascimento:
&nbsp; <input name='nasc_dia' type='text' class='campotexto' size='10' maxlength=10 value='$row_user[data_nasci]'> Ano / mes / dia
</td>
</tr>
<tr>
<td width='15%' align=right>Naturalidade:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10' value='$row_user[naturalidade]'>
&nbsp;&nbsp; Nacionalidade:&nbsp;&nbsp; <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' value='$row_user[nacionalidade]'>
&nbsp;&nbsp; Estado Civil:&nbsp;&nbsp; <input type='text' name='civil' class='campotexto' id='civil' value='$row_user[civil]'>
</td>
</tr>
<tr>
<td width='15%' align=right>CTPS:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; 
<input name='ctps' type='text' class='campotexto' id='ctps' size='10' value='$row_user[ctps]'
onFocus="document.all.ctps.style.background='#CCFFCC'"
onBlur="document.all.ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; &nbsp;&nbsp; 
Série:
&nbsp;&nbsp; 
<input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='8' value='$row_user[serie_ctps]'
onFocus="document.all.serie_ctps.style.background='#CCFFCC'"
onBlur="document.all.serie_ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; &nbsp;&nbsp; 
UF:
&nbsp;&nbsp; 
<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' value='$row_user[uf_ctps]'
onFocus="document.all.uf_ctps.style.background='#CCFFCC'"
onBlur="document.all.uf_ctps.style.background='#FFFFFF'" 
style="background:#FFFFFF" 
onChange="this.value=this.value.toUpperCase()" >
&nbsp;&nbsp; &nbsp;&nbsp; 
PIS:
&nbsp;&nbsp; 
<input name='pis' type='text' class='campotexto' id='pis' size='15' value='$row_user[pis]'
onFocus="document.all.pis.style.background='#CCFFCC'"
onBlur="document.all.pis.style.background='#FFFFFF'" 
style="background:#FFFFFF">
&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td width='15%' align=right>Nº do RG:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='rg' type='text' class='campotexto' id='rg' size='12' value='$row_user[rg]'>
&nbsp;&nbsp; Orgão Expedidor:&nbsp;&nbsp; <input name='orgao' type='text' class='campotexto' id='orgao' size='8' value='$row_user[orgao]'>
&nbsp;&nbsp; Data:&nbsp;&nbsp; <input name='data_rg' type='text' class='campotexto' size='10' maxlength=10 value='$row_user[data_rg]'> Ano / mes / dia
</td>
</tr>
<tr>
<td width='15%' align=right>CPF:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='cpf' type='text' class='campotexto' id='cpf' size='12' value='$row_user[cpf]'>
&nbsp;&nbsp; Nº Título de Eleitor:&nbsp;&nbsp; <input name='titulo' type='text' class='campotexto' id='titulo' size='10' value='$row_user[titulo]'>
&nbsp;&nbsp; Zona:&nbsp;&nbsp; <input name='zona' type='text' class='campotexto' id='zona' size='3' value='$row_user[zona]'>
&nbsp;&nbsp; Seção:&nbsp;&nbsp; <input name='secao' type='text' class='campotexto' id='secao' size='3' value='$row_user[secao]'>
</td>
</tr>
<tr>
<td width='15%' align=right>Filiação - Pai:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='pai' type='text' class='campotexto' id='pai' size='75' value='$row_user[pai]'></td>
</tr>
<tr>
<td width='15%' align=right>Filiação - Mãe:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='mae' type='text' class='campotexto' id='mae' size='75' value='$row_user[mae]'></td>
</tr>
<tr>
<tr>
<td width='15%' align=right>Estuda Atualmente:</td>
<td width='38%' colspan=3 >&nbsp;&nbsp; <input type='radio' checked name='estuda' value='sim' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? 'none' : 'none' ;"> Sim&nbsp;&nbsp;<input type='radio' name='estuda' value='nao' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? '' : '' ;"> Não
</td>
</tr>
<tr id='linha_termino' style='display:none'>
<td width='15%' align=right>Término em:</td>
<td>&nbsp;&nbsp; <input name='escola_dia' type='text' class='campotexto' value='30' size='2' maxlength=2 > / <input name='escola_mes' type='text' class='campotexto' size='2' maxlength=2 value='11'> / <input name='escola_ano' type='text' class='campotexto' size='4' maxlength=4>
</td>
</tr>
<td width='15%' align=right>Escolaridade:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='escolaridade' type='text' class='campotexto' id='escolaridade' size='15' value='$row_user[escolaridade]'>
&nbsp;&nbsp; Instituíção:&nbsp;&nbsp; <input name='instituicao' type='text' class='campotexto' id='instituicao' size='20' value='$row_user[instituicao]'>
&nbsp;&nbsp; Curso:&nbsp;&nbsp; <input name='curso' type='text' class='campotexto' id='curso' size='10' value='$row_user[curso]'>
</td>
</tr>
<tr>
<td width='15%' align=right></td>
<td colspan=3>
<table width='100%' border='0' cellspacing='0' cellpadding='0' class='linha'>
<tr>
<td>
<?=$foto?>
</td>
<td style='display:none' id='tablearquivo'>
<input type='file' name='arquivo' id='arquivo' class='campotexto' >
<font size='1' color='#999999'>(.jpg, .png, .gif, .jpeg)</font>                  
</span>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td colspan=4><div align=center class='style2'>Informações Bancárias</div></td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td width='15%' align=right>Banco:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; <input name='banco' type='text' class='campotexto' id='banco' size='15' value='$row_user[banco]'>
&nbsp;&nbsp; Agêmcia:&nbsp;&nbsp; <input name='agencia' type='text' class='campotexto' id='agencia' size='7' value='$row_user[agencia]'>
&nbsp;&nbsp; nº da Conta:&nbsp;&nbsp; <input name='conta' type='text' class='campotexto' id='conta' size='15' value='$row_user[conta]'>
</td>
</tr>
<tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td colspan=4><div align=center class='style2'>Informações de Login</div></td>
</tr>
<tr>
<td colspan=4>&nbsp;</td>
</tr>
<tr>
<td width='15%' align=right>Login:</td>
<td width='38%' colspan=3>&nbsp;&nbsp; $row_user[login]
&nbsp;&nbsp; Senha padrão:&nbsp;&nbsp; ******
&nbsp;&nbsp; 
</tr>
<tr $mostrar>
<td width='15%' align=right>Tipo de Conta:</td>
<td width='38%' colspan=3>
&nbsp;&nbsp; 
<select name='tipo_usuario' class='campotexto'>
<?php
$result_tipo_user = mysql_query("SELECT * FROM grupo where tipo = '2'");
while($row_tipo_user = mysql_fetch_array($result_tipo_user)){
	
	if($row_tipo_user['id_tipo'] == $row_user['tipo_usuario']){
	    print "<option value=$row_tipo_user[id_tipo] selected>$row_tipo_user[nome]</option>";
	}else{
		print "<option value=$row_tipo_user[id_tipo]>$row_tipo_user[nome]</option>";
	}

}
?></select>
</tr>
<tr>
<td width='15%' align=right></td>
<td width='38%' colspan=3><br>
<font color='red'> Atenção: <BR> - Verifique o TIPO DE CONTA antes de ATUALIZAR</font>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='4' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'><BR><BR>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='ATUALIZAR' class='campotexto'><BR> <BR>
</td>
</tr>
</table>
<input type='hidden' name='pag' value='$pag'>
<input type='hidden' name='id_cadastro' value='16'>
<input type='hidden' name='id_funcionario' value='$row_user[id_funcionario]'>
</td>
</tr>
</table>
</form><br><a href='ver_tudo.php?id=19' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.funcao.value == ""){
alert("O campo Função deve ser preenchido!");
d.funcao.focus();
return false;
}
if (d.locacao.value == ""){
alert("O campo Lotação deve ser preenchido!");
d.locacao.focus();
return false;
}
if (d.salario.value == ""){
alert("O campo Salário deve ser preenchido!");
d.salario.focus();
return false;
}
if (d.nome.value == "" ){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.nome1.value == "" ){
alert("O campo Nome para Exibição deve ser preenchido!");
d.nome1.focus();
return false;
}
if (d.login.value == "" ){
alert("O campo Login deve ser preenchido!");
d.login.focus();
return false;
}
if (d.nome.value == "" ){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
return true;   }
</script>
<?php

break;

case 13:								//CADASTRO DE UNIDADES
$id_regiao = $_REQUEST['regiao'];

?>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrounidades.gif'><BR>Cadastro de Unidades</div><BR></td>
</tr>
<tr>
<td align='right'></td>
<td>&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td width='30%' align='right'>Projeto:</td>
<td width='70%'>&nbsp;&nbsp; <select name='projeto' class='campotexto'>
<?php

$result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao'");

while ($row_pro = mysql_fetch_array($result_pro)){
print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
}
?></select></td>
</tr>
<tr>
<td align='right'>Nome:</td>
<td>&nbsp;&nbsp; <input name='nome' type='text' class='campotexto' id='nome' size='30'></td>
</tr>
<tr>
<td align='right'>Local:</td>
<td>&nbsp;&nbsp; <input name='local' type='text' class='campotexto' id='local' size='20'></td>
</tr>
<tr>
<td align='right'>Telefone:</td>
<td>&nbsp;&nbsp; <input name='tel' type='text' class='campotexto' id='tel' size='12' value='(  )' maxlength='14'></td>
</tr>
<tr>
<td align='right'>Telefone Recado:</td>
<td>&nbsp;&nbsp; <input name='tel2' type='text' class='campotexto' id='tel2' size='12' value='(  )' maxlength='14'></td>
</tr>
<tr>
<td align='right'>Responsável:</td>
<td>&nbsp;&nbsp; <input name='responsavel' type='text' class='campotexto' id='responsavel' size='20'></td>
</tr>
<tr>
<td align='right'>Celular do Responsável:</td>
<td>&nbsp;&nbsp; <input name='cel' type='text' class='campotexto' id='cel' size='12' value='(  )' maxlength='14'></td>
</tr>
<tr>
<td align='right'>E-mail do Responsável:</td>
<td>&nbsp;&nbsp; <input name='email' type='text' class='campotexto' id='email' size='20'></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'> <BR><BR>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR><BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='17'>
<input type='hidden' name='regiao' value='$id_regiao'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.nome.value == ""){
alert("O campo Nome deve ser preenchido!");
d.nome.focus();
return false;
}
if (d.local.value == "" ){
alert("O campo Local deve ser preenchido!");
d.local.focus();
return false;
}
return true;   }
</script>
<?php
break;

case 14:								//CADASTRO DE TIPOS DE PAGAMENTOS

$id_regiao = $_REQUEST['regiao'];

?>
<form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr>
<td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/cadastrodetipodepagmento.gif'><BR>Cadastro de Tipos de Pagamentos</div><BR></td>
</tr>
<tr>
<td align='right'></td>
<td>&nbsp;&nbsp; 
</td>
</tr>
<tr>
<td width='30%' align='right'>Projeto:</td>
<td width='70%'>&nbsp;&nbsp; <select name='projeto' class='campotexto'>";
$result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao'", $conn);
while ($row_pro = mysql_fetch_array($result_pro)){
print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
}
print "</select></td>
</tr>
<tr>
<td align='right'>Tipo Pagamento:</td>
<td>&nbsp;&nbsp; <input name='tipopg' type='text' class='campotexto' id='tipopg' size='30'></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='2' align='center'><table width='200' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td align='center'> <input type='reset' name='Submit2' value='Limpar' class='campotexto'> <BR><BR>
</td>
<td align='center' valign='middle'> <input type='submit' name='Submit' value='CADASTRAR' class='campotexto'> <BR><BR>
</td>
</tr>
</table>
<input type='hidden' name='id_cadastro' value='19'>
<input type='hidden' name='regiao' value='$id_regiao'></td>
</tr>
</table>
</form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

<script>function validaForm(){
d = document.form1;
if (d.tipopg.value == ""){
alert("O campo Tipo de Pagamento deve ser preenchido!");
d.tipopg.focus();
return false;
}
return true;   }
</script>
<?php
break;
}
/*
<script type="text/javascript">
<!--
var Accordion1 = new Spry.Widget.Accordion("Accordion1");
var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgRight:"../SpryAssets/SpryMenuBarRightHover.gif"});
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1");
var sprytooltip1 = new Spry.Widget.Tooltip("sprytooltip1", "#sprytrigger1", {useEffect:"blind"});
//-->
</script>
*/
?>
</body>
</html>

<?php
}
?>