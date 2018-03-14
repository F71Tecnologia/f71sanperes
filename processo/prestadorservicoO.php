<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{
include "../conn.php";
$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
switch($id){

case 1:

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
margin-left: 0px;
margin-top: 0px;
margin-right: 0px;
margin-bottom: 0px;
}
.style35 {
font-family: Geneva, Arial, Helvetica, sans-serif;
font-weight: bold;
}
.style36 {font-size: 14px}
.style38 {
font-size: 16px;
font-weight: bold;
font-family: Geneva, Arial, Helvetica, sans-serif;
color: #FFFFFF;
}
a:link {
color: #006600;
}
a:visited {
color: #006600;
}
a:hover {
color: #006600;
}
a:active {
color: #006600;
}.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style41 {
font-family: Geneva, Arial, Helvetica, sans-serif;
color: #FFFFFF;
font-weight: bold;
}
.style43 {font-family: Arial, Helvetica, sans-serif}
.style45 {font-size: 14px; font-family: Arial, Helvetica, sans-serif; }
-->
</style>
<script language='javascript'>
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
<body bgcolor="#FFFFFF">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top"> 
<table width="750" border="0" cellpadding="0" cellspacing="0">
<tr> 
<td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
</tr>
<tr>
<td width="21" rowspan="6" background="../layout/esquerdo.gif">&nbsp;</td>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td width="26" rowspan="6" background="../layout/direito.gif">&nbsp;</td>
</tr>

<tr>
<td colspan="2" background="../imagens/fundo_cima.gif"><div align="center"><span class="style38"><br>
CADASTRO DE PRESTADORES DE SERVI&Ccedil;O</span><br>
<br>
</div></td>
</tr>
<tr>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td bgcolor="#FFFFFF"><div align="center"></div></td>
</tr>
<tr>
  <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr>
<td colspan="2" bgcolor="#FFFFFF"><br>
 <table width="92%" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
<tr>
<td colspan="6" bgcolor="#666666"><div align="center" class="style7">EMPRESAS CADASTRADAS</div></td>
</tr>
<tr class="style35">
<td width="8%" bgcolor="#CCCCCC"><div align="center">N.</div>
<td width="21%" bgcolor="#CCCCCC"><div align="center">NUMERO PROCESSO</div></td>
<td width="38%" bgcolor="#CCCCCC"><div align="center">RAZ&Atilde;O SOCIAL</div></td>
<td width="24%" bgcolor="#CCCCCC"><div align="center">RESPONS&Aacute;VEL</div></td>
<td width="17%" bgcolor="#CCCCCC"><div align="center">STATUS</div></td>
<td width="17%" bgcolor="#CCCCCC"><div align="center">EDITAR</div></td>
</tr>
<?php
$result_empresas = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$regiao' ORDER BY numero");
while($row_empresas = mysql_fetch_array($result_empresas)){
if($row_empresas['acompanhamento'] == "1"){
$status = "Aberto";
}else if($row_empresas['acompanhamento'] == "2"){
$status = "Aguardando Aprovação";
}else if($row_empresas['acompanhamento'] == "3"){
$status = "Aprovado";
}else if($row_empresas['acompanhamento'] == "4"){
$status = "Finalizado";
}else if($row_empresas['acompanhamento'] == "5"){
$status = "Não Aprovado";
}
print "
<tr class='campotexto'>
<td><div align='center'>$row_empresas[id_prestador]</div></td>
<td align='center'><a href='impressao.php?prestador=$row_empresas[0]&id=1&regiao=$regiao'>$row_empresas[numero]</a></td>
<td>$row_empresas[c_razao]</td>
<td>$row_empresas[c_responsavel]</td>
<td>$status</td>
<td><a href=prestadorservico.php?id=3&regiao=$regiao&prestador=$row_empresas[0]>Editar</a></td>
</tr>";
}
?>
 </table>
<div align="center"><br>
<a href="relatorioprestadores.php?regiao=<?=$regiao?>" target="_blank">
<img src="../imagens/verbolsista.gif" alt="abertura" width="190" height="31" border="0"></a>
</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="#"><img src="../imagens/castrobolsista.gif" width="190" height="31" border="0" onClick="document.all.cadastro.style.display = (document.all.cadastro.style.display == 'none') ? '' : 'none' ;" ></a> <br>
</div>
<form action="prestadorservico.php" name="form1" method="post" onSubmit="return validaForm()">
<br>
<table id="cadastro"  height="1045" width="95%" border="1" align="center" cellspacing="0" bordercolor="#CCFF99" style="display:none">

<tr>
<td height="21" colspan="6" bgcolor="#CCFF99"><div align="right" class="style35">
<div align="center" class="style35">DADOS DO PROJETO</div>
</div></td>
</tr>
<tr>
<td width="19%" height="26"><div align="right" class="style40 style35"><strong>Projeto:</strong></div></td>
<td width="81%" height="26" colspan="5">
<?php 
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value='$row_projeto[0]'>$row_projeto[nome]</option>";
}
print "</select>";
?></td>
</tr>

<br>
<br>
<div align="center" class="style35">
<tr>
<td height="31" colspan="6" bgcolor="#CCFF99"><div align="right" class="style35">
<div align="center" class="style35">DADOS DO CONTRATANTE</div>
</div></td>
</tr>
<tr>
<td height="35"><div align="right" class="style40 style35"><strong>Contratante:</strong></div></td>
<td height="35" colspan="5"><input name="contratante" type="text" id="contratante" value="<? 
include "../empresa.php";
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?>" size="90" disabled="disabled" /></td>
</tr>
<tr>
<td height="35"><div align="right" class="style40 style35"><strong>Endere&ccedil;o:</strong></div></td>
<td height="35" colspan="5"><input name="endereco" type="text" id="endereco" size="90" 
onfocus="document.all.endereco.style.background='#CCFFCC'" onBlur="document.all.endereco.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" /></td>
</tr>
<tr>
<td height="35"><div align="right" class="style35">CNPJ:</div></td>
<td height="35" colspan="5"><input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
onfocus="document.all.cnpj.style.background='#CCFFCC'" 
onblur="document.all.cnpj.style.background='#FFFFFF'"
onkeypress="formatar('##.###.###/####-##', this)" 
onkeyup="pula(18,this.id,c_fantasia.id)" size="20" maxlength="18" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;Responsavel:
<input name="responsavel" type="text" id="responsavel" value="Luiz Carlos Mandia" size="40" disabled="disabled" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name="civil" type="text" id="civil" value="Casado" size="20" disabled="disabled" /></td>
</tr>
<tr>
<td height="35" colspan="6">Nacionalidade:
<input name="nacionalidade" type="text" id="nacionalidade" value="Brasileira" size="40" disabled="disabled" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Forma&ccedil;&atilde;o: <span class="style35 style40">
<input name="formacao" type="text" id="formacao" value="Administrador" size="20" disabled="disabled" />
</span></td>
</tr>
<tr>
<td height="35" colspan="6"><span class="style35 style40">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:
<input name="rg" type="text" id="rg" size="20" maxlength="14" value="3.531.222-1" disabled="disabled" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CPF:
<input name="cpf" type="text" id="cpf" value="570.072.418-91" size="20" disabled="disabled" />
</span></td>
</tr>
<tr>
<td height="31" colspan="6" bgcolor="#CCFF99"><div align="right" class="style35">
<div align="center" class="style35">DADOS DA EMPRESA CONTRATADA</div>
</div></td>
</tr>
<tr>
<td height="35" colspan="6"><strong>Nome Fantasia:</strong>
<input name="c_fantasia" type="text" id="c_fantasia" style="background:#FFFFFF;" 
onfocus="document.all.c_fantasia.style.background='#CCFFCC'" 
onblur="document.all.c_fantasia.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" size="90" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;Raz&atilde;o Social:
<input name="c_razao" type="text" id="c_razao" size="90" 
onfocus="document.all.c_razao.style.background='#CCFFCC'" 
onblur="document.all.c_razao.style.background='#FFFFFF'" 
style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Endere&ccedil;o:
<input name="c_endereco" type="text" id="c_endereco" size="90" 
onfocus="document.all.c_endereco.style.background='#CCFFCC'" 
onblur="document.all.c_endereco.style.background='#FFFFFF'" 
style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CNPJ:
<input name="c_cnpj" type="text" id="c_cnpj" 
style="background:#FFFFFF; text-transform:uppercase;"
onfocus="document.all.c_cnpj.style.background='#CCFFCC'" 
onblur="document.all.c_cnpj.style.background='#FFFFFF'" 
onkeyup="pula(18,this.id,c_ie.id)"
onkeypress="formatar('##.###.###/####-##', this)" size="18" maxlength="18" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE:
<input name="c_ie" type="text" id="c_ie" size="15" onFocus="document.all.c_ie.style.background='#CCFFCC'" onBlur="document.all.c_ie.style.background='#FFFFFF'" style="background:#FFFFFF;" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CCM:
<input name="c_im" type="text" id="c_im" size="15" onFocus="document.all.c_im.style.background='#CCFFCC'" onBlur="document.all.c_im.style.background='#FFFFFF'" style="background:#FFFFFF;" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Telefone:
<input name='c_tel' type='text' id='c_tel' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,c_fax.id)" 
onfocus="document.all.c_tel.style.background='#CCFFCC'" 
onblur="document.all.c_tel.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:
<input name="c_fax" type="text" id="c_fax" size="12" 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,c_email.id)" 
onfocus="document.all.c_fax.style.background='#CCFFCC'" 
onblur="document.all.c_fax.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E-mail: <span class="style35 style40">
<input name="c_email" type="text" id="c_email" size="25" 
onfocus="document.all.c_email.style.background='#CCFFCC'" 
onblur="document.all.c_email.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" />
</span> </td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;Responsavel:
<input name="c_responsavel" type="text" id="c_responsavel" size="40"
style="background:#FFFFFF;" 
onfocus="document.all.c_responsavel.style.background='#CCFFCC'" 
onblur="document.all.c_responsavel.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name="c_civil" type="text" id="c_civil" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.c_civil.style.background='#CCFFCC'" 
onblur="document.all.c_civil.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" /></td>
</tr>
<tr>
<td height="35" colspan="6">Nacionalidade:
<input name="c_nacionalidade" type="text" id="c_nacionalidade" size="40" 
style="background:#FFFFFF;" 
onfocus="document.all.c_nacionalidade.style.background='#CCFFCC'" 
onblur="document.all.c_nacionalidade.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Forma&ccedil;&atilde;o: <span class="style35 style40">
<input name="c_formacao" type="text" id="c_formacao" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.c_formacao.style.background='#CCFFCC'" 
onblur="document.all.c_formacao.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</span></td>
</tr>
<tr>
<td height="35" colspan="6"><span class="style35 style40">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:
<input name="c_rg" type="text" id="c_rg" 
onkeypress="formatar('##.###.###-##', this)" size="20" maxlength="14" 
onfocus="document.all.c_rg.style.background='#CCFFCC'" 
onblur="document.all.c_rg.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CPF:
<input name="c_cpf" type="text" id="c_cpf" 
onkeypress="formatar('###.###.###-##', this)" size="20" maxlength="14" 
onkeyup="pula(14,this.id,c_email2.id)" 
onfocus="document.all.c_cpf.style.background='#CCFFCC'" 
onblur="document.all.c_cpf.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
</span></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E-mail: <span class="style35 style40">
<input name="c_email2" type="text" id="c_email2" size="30" 
onfocus="document.all.c_email2.style.background='#CCFFCC'" 
onblur="document.all.c_email2.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site: <span class="style35 style40">
<input name="c_site" type="text" id="c_site" size="38" 
onfocus="document.all.c_site.style.background='#CCFFCC'" 
onblur="document.all.c_site.style.background='#FFFFFF'"
style="background:#FFFFFF; text-transform:lowercase;" />
</span></td>
</tr>
<tr>
<td colspan="6" bgcolor="#CCFF99"><div align="center">DADOS DA PESSOA DE  CONTATO NA CONTRATADA </div></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;Nome Completo:
<input name="co_responsavel" type="text" id="co_responsavel" size="27"
style="background:#FFFFFF;" 
onfocus="document.all.co_responsavel.style.background='#CCFFCC'" 
onblur="document.all.co_responsavel.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Telefone:
<input name='co_tel' type='text' id='co_tel' size='12' 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_fax.id)" 
onfocus="document.all.co_tel.style.background='#CCFFCC'" 
onblur="document.all.co_tel.style.background='#FFFFFF'" 
style="background:#FFFFFF;" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:
<input name="co_fax" type="text" id="co_fax" size="12" 
onkeypress="return(TelefoneFormat(this,event))" 
onkeyup="pula(13,this.id,co_civil.id)" 
onfocus="document.all.co_fax.style.background='#CCFFCC'" 
onblur="document.all.co_fax.style.background='#FFFFFF'" 
style="background:#FFFFFF;" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name="co_civil" type="text" id="co_civil" size="20"
style="background:#FFFFFF;" 
onfocus="document.all.co_civil.style.background='#CCFFCC'" 
onblur="document.all.co_civil.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nacionalidade:
<input name="co_nacionalidade" type="text" id="co_nacionalidade" size="27" 
style="background:#FFFFFF;" 
onfocus="document.all.co_nacionalidade.style.background='#CCFFCC'" 
onblur="document.all.co_nacionalidade.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" /></td>
</tr>
<tr>
<td height="35" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: <span class="style35 style40">
<input name="co_email" type="text" id="co_email" size="30" 
onfocus="document.all.co_email.style.background='#CCFFCC'" 
onblur="document.all.co_email.style.background='#FFFFFF'" 
style="background:#FFFFFF; text-transform:lowercase;" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
</tr>
<tr>
<td colspan="6" bgcolor="#CCFF99"><div align="center" class="style35">OBJETO DO CONTRATO</div></td>
</tr>
<tr>
<td height="44" colspan="6">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:<span class="style35 style40">
<input name="co_municipio" type="text" id="co_municipio" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.co_municipio.style.background='#CCFFCC'" 
onblur="document.all.co_municipio.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</span></td>
</tr>
<tr>
<td height="44" colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Assunto:<span class="style35 style40">
<input name="assunto" type="text" id="assunto" size="20" 
style="background:#FFFFFF;" 
onfocus="document.all.assunto.style.background='#CCFFCC'" 
onblur="document.all.assunto.style.background='#FFFFFF'" 
onchange="this.value=this.value.toUpperCase()" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data do Processo:
<input name="data_proc" type="text" id="data_proc" size="10" 
onkeyup="mascara_data(this)" maxlength="10"
onfocus="document.all.data_proc.style.background='#CCFFCC'" 
onblur="document.all.data_proc.style.background='#FFFFFF'" 
style="background:#FFFFFF;" /></td>
</tr>
<tr>
<td height="102" colspan="6"><div align="center">
<label>
<textarea name="objeto" id="objeto" cols="45" rows="5" 
onfocus="document.all.objeto.style.background='#CCFFCC'" 
onblur="document.all.objeto.style.background='#FFFFFF'" 
style="background:#FFFFFF;"
onchange="this.value=this.value.toUpperCase()"></textarea>
</label>
</div></td>
</tr>
<tr>
<td colspan="6" bgcolor="#CCFF99"><div align="center" class="style35">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</div></td>
</tr>
<tr>
<td height="102" colspan="6"><div align="center">
<label>
<textarea name="especificacao" id="especificacao" cols="45" rows="5" 
onfocus="document.all.especificacao.style.background='#CCFFCC'" 
onblur="document.all.especificacao.style.background='#FFFFFF'" 
style="background:#FFFFFF;"
onchange="this.value=this.value.toUpperCase()"></textarea>
</label>
</div></td>
</tr>
<tr style="display:none">
<td height="46" colspan="6" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ANEXO I&nbsp;&nbsp;&nbsp; &ndash;  &nbsp;&nbsp;&nbsp;VALOR R$
<input name="valor" type="text" id="valor" size="20" 
onkeydown="FormataValor(this,event,20,2)" 
onfocus="document.all.valor.style.background='#CCFFCC'" 
onblur="document.all.valor.style.background='#FFFFFF'" 
style="background:#FFFFFF;"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DATA: &nbsp;
<input name="data_inicio" type="text" id="data_inicio" size="10" 
onkeyup="mascara_data(this)" maxlength="10"
onfocus="document.all.data_inicio.style.background='#CCFFCC'" 
onblur="document.all.data_inicio.style.background='#FFFFFF'" 
style="background:#FFFFFF;" /></td>
</tr>
<tr>
  <td height="46" colspan="6" align="center" valign="middle" ><br>
<input type="hidden" name="id" value="2">
<input type="hidden" name="regiao" value="<?=$regiao?>">
<br>
<label>
<input type="submit" name="Submit" id="button" value="Cadastrar">
</label></td>
</tr>
</table>


<br>
</form>
<script language="javascript">
function validaForm(){
d = document.form1;
if (d.endereco.value == ""){
alert("O campo Endereço deve ser preenchido!");
d.endereco.focus();
return false;
}
if (d.cnpj.value == ""){
alert("O campo CNPJ deve ser preenchido!");
d.cnpj.focus();
return false;
}
if (d.c_fantasia.value == ""){
alert("O campo Nome Fantasia deve ser preenchido!");
d.c_fantasia.focus();
return false;
}
if (d.c_razao.value == ""){
alert("O campo Razão Social deve ser preenchido!");
d.c_razao.focus();
return false;
}
if (d.c_endereco.value == ""){
alert("O campo Endereço deve ser preenchido!");
d.c_endereco.focus();
return false;
}
if (d.c_cnpj.value == ""){
alert("O campo CNPJ deve ser preenchido!");
d.c_cnpj.focus();
return false;
}
if (d.c_ie.value == ""){
alert("O campo  IE deve ser preenchido!");
d.c_ie.focus();
return false;
}
if (d.c_im.value == ""){
alert("O campo IM deve ser preenchido!");
d.c_im.focus();
return false;
}
if (d.c_responsavel.value == ""){
alert("O campo Responsavel deve ser preenchido!");
d.c_responsavel.focus();
return false;
}
if (d.c_rg.value == ""){
alert("O campo RG deve ser preenchido!");
d.c_rg.focus();
return false;
}
if (d.c_cpf.value == ""){
alert("O campo CPF deve ser preenchido!");
d.c_cpf.focus();
return false;
}
if (d.co_responsavel.value == ""){
alert("O campo Responsavel deve ser preenchido!");
d.co_responsavel.focus();
return false;
}
if (d.co_tel.value == ""){
alert("O campo Telefone deve ser preenchido!");
d.co_tel.focus();
return false;
}
if (d.co_municipio.value == ""){
alert("O campo Municipio deve ser preenchido!");
d.co_municipio.focus();
return false;
}
if (d.assunto.value == ""){
alert("O campo Assunto deve ser preenchido!");
d.assunto.focus();
return false;
}
if (d.data_proc.value == ""){
alert("O campo Data do Processo deve ser preenchido!");
d.data_proc.focus();
return false;
}
if (d.objeto.value == ""){
alert("O campo Objeto deve ser preenchido!");
d.objeto.focus();
return false;
}
if (d.especificacao.value == ""){
alert("O campo Especificação deve ser preenchido!");
d.especificacao.focus();
return false;
}
return true;   }
</script>
<br>          </td>
</tr>
<tr>
<td width="155" bgcolor="#FFFFFF">&nbsp;</td>
<td width="549" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr valign="top"> 
<td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38">
<?php
$rod = new empresa();
$rod -> rodape();
?></td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
<?php
break;
case 2:  //INSERINDO AS INFORMAÇÕES
$id_projeto = $_REQUEST['projeto'];
$id_user = $_COOKIE['logado'];
$aberto_em = date('Y-m-d');
$regiao = $_REQUEST['regiao'];
$aberto_por = $_REQUEST['aberto_por'];
$contratante = "INSTITUTO SORRINDO PARA A VIDA";
$endereco = $_REQUEST['endereco'];
$cnpj = $_REQUEST['cnpj'];
$responsavel = "Luiz Carlos Mandia";
$civil = "Casado";
$nacionalidade = "Brasileira";
$formacao = "Administrador";
$rg = "3.531.222-1";
$cpf = "570.072.418-91";
$c_fantasia = $_REQUEST['c_fantasia'];
$c_razao = $_REQUEST['c_razao'];
$c_endereco = $_REQUEST['c_endereco'];
$c_cnpj = $_REQUEST['c_cnpj'];
$c_ie = $_REQUEST['c_ie'];
$c_im = $_REQUEST['c_im'];
$c_tel = $_REQUEST['c_tel'];
$c_fax = $_REQUEST['c_fax'];
$c_email = $_REQUEST['c_email'];
$c_responsavel = $_REQUEST['c_responsavel'];
$c_civil = $_REQUEST['c_civil'];
$c_nacionalidade = $_REQUEST['c_nacionalidade'];
$c_formacao = $_REQUEST['c_formacao'];
$c_rg = $_REQUEST['c_rg'];
$c_cpf = $_REQUEST['c_cpf'];
$c_email2 = $_REQUEST['c_email2'];
$c_site = $_REQUEST['c_site'];
$co_responsavel = $_REQUEST['co_responsavel'];
$co_tel = $_REQUEST['co_tel'];
$co_fax = $_REQUEST['co_fax'];
$co_civil = $_REQUEST['co_civil'];
$co_nacionalidade = $_REQUEST['co_nacionalidade'];
$co_email = $_REQUEST['co_email'];
$co_municipio = $_REQUEST['co_municipio'];
$assunto = $_REQUEST['assunto'];
$objeto = $_REQUEST['objeto'];
$especificacao = $_REQUEST['especificacao'];
$valor = $_REQUEST['valor'];
$data_inicio = $_REQUEST['data_inicio'];
$data_proc = $_REQUEST['data_proc'];
$valor = str_replace(".","",$valor);
/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/
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
$data_inicio_f = ConverteData($data_inicio);
$data_proc_f = ConverteData($data_proc);
// GERANDO A NÚMERAÇÃO DO PROCESSO
$ano_proc1 = explode("-", $data_proc_f);
$ano_proc2 = "$ano_proc1[0]"; 
$ano_proc3 = str_split($ano_proc2, 2);
$ano_proc = "$ano_proc3[1]";
$num_reg = sprintf("%03s", $regiao);
$result_cont = mysql_query("SELECT * FROM prestadorservico where id_regiao = '$regiao'");
$row_cont = mysql_num_rows($result_cont);
$row_cont = $row_cont + 1;
$num_id = sprintf("%04s", $row_cont);
$num_ano = sprintf("%0s", $row_cont);
$numero = $num_reg.".".$num_id."/".$ano_proc;
//----------------
mysql_query("INSERT INTO prestadorservico
(id_regiao, id_projeto, aberto_por, aberto_em, contratante, numero, endereco, cnpj, responsavel, civil, nacionalidade, formacao, rg, cpf, c_fantasia, c_razao, c_endereco, c_cnpj, c_ie, c_im, c_tel, c_fax, c_email, c_responsavel, c_civil, c_nacionalidade, c_formacao, c_rg, c_cpf, c_email2, c_site, co_responsavel, co_tel, co_fax, co_civil, co_nacionalidade, co_email, co_municipio, assunto, objeto, especificacao, valor, data, data_proc, acompanhamento) 
VALUES 
('$regiao','$id_projeto','$id_user','$aberto_em','$contratante','$numero', '$endereco','$cnpj','$responsavel',
'$civil','$nacionalidade','$formacao','$rg','$cpf','$c_fantasia','$c_razao','$c_endereco','$c_cnpj',
'$c_ie','$c_im','$c_tel','$c_fax','$c_email','$c_responsavel','$c_civil','$c_nacionalidade','$c_formacao',
'$c_rg','$c_cpf','$c_email2','$c_site','$co_responsavel','$co_tel','$co_fax','$co_civil',
'$co_nacionalidade','$co_email','$co_municipio','$assunto','$objeto','$especificacao','$valor','$data_inicio_f'
,'$data_proc_f','1')") or die ("Erro <br>".mysql_error());
print "
<script>
alert (\"$numero - Dasos cadastrados!\"); 
location.href=\"prestadorservico.php?id=1&regiao=$regiao\"
</script>";
break;

case 3:  //MOTRANDO TODOS OS DADOS DA EMPRESA

$id_prestador = $_REQUEST['prestador'];
$result_prestador = mysql_query("SELECT *,date_format(aberto_em, '%d/%m/%Y')as aberto_em 
,date_format(contratado_em, '%d/%m/%Y')as contratado_em ,date_format(encerrado_em, '%d/%m/%Y')as 
encerrado_em FROM prestadorservico WHERE id_prestador = '$id_prestador'") or die ("Erro no SELECT<BR>".mysql_error());
$row = mysql_fetch_array($result_prestador);
print"
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='../net2.css' rel='stylesheet' type='text/css'>
<style type='text/css'>
<!--
body {
margin-left: 0px;
margin-top: 0px;
margin-right: 0px;
margin-bottom: 0px;
}
.style35 {
font-family: Geneva, Arial, Helvetica, sans-serif;
font-weight: bold;
}
.style36 {font-size: 14px}
.style38 {
font-size: 16px;
font-weight: bold;
font-family: Geneva, Arial, Helvetica, sans-serif;
color: #FFFFFF;
}
a:link {
color: #006600;
}
a:visited {
color: #006600;
}
a:hover {
color: #006600;
}
a:active {
color: #006600;
}.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style41 {
font-family: Geneva, Arial, Helvetica, sans-serif;
color: #FFFFFF;
font-weight: bold;
}
.style43 {font-family: Arial, Helvetica, sans-serif}
.style45 {font-size: 14px; font-family: Arial, Helvetica, sans-serif; }
-->
</style>
</head>
<body>
";
print"
<form action='prestadorservico.php' method='post' name='form1'>
<table width='780' border='1' align='center' cellspacing='0' bordercolor='#CCFF99' bgcolor='#FFFFFF'>
<tr>
<td height='31' colspan='6' bgcolor='#CCFF99'><div align='right' class='style35'>
<div align='center' class='style35'>DADOS DO CONTRATANTE</div>
</div></td>
</tr>
<tr>
<td height='35'><div align='right' class='style40 style35'><strong>Contratante:</strong></div></td>
<td height='35' colspan='5'><input name='contratante' type='text' id='contratante' value='INSTITUTO SORRINDO PARA A VIDA' size='90' disabled='disabled' />
</td>
</tr>
<tr>
<td height='35'><div align='right' class='style40 style35'><strong>Endere&ccedil;o:</strong></div></td>
<td height='35' colspan='5'>
<input name='endereco' type='text' id='endereco' size='90' value='$row[endereco]'
onfocus=\"document.all.endereco.style.background='#CCFFCC'\" onblur=\"document.all.endereco.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>
<tr>
<td height='35'><div align='right' class='style35'>CNPJ:</div></td>
<td height='35' colspan='5'><span class='style35'>
<input name='cnpj' type='text' id='cnpj' value='$row[cnpj]'
style='background:#FFFFFF; text-transform:uppercase;'
onfocus=\"document.all.cnpj.style.background='#CCFFCC'\" 
onblur=\"document.all.cnpj.style.background='#FFFFFF'\"
onkeypress=\"formatar('##.###.###/####-##', this)\" 
onkeyup=\"pula(18,this.id,c_fantasia.id)\" size=\"20\" maxlength=\"18\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Responsavel:
<input name=\"responsavel\" type=\"text\" id=\"responsavel\" value=\"Luiz Carlos Mandia\" size=\"40\" disabled=\"disabled\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name=\"civil\" type=\"text\" id=\"civil\" value=\"Casado\" size=\"20\" disabled=\"disabled\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Nacionalidade:
<input name=\"nacionalidade\" type=\"text\" id=\"nacionalidade\" value=\"Brasileira\" size=\"40\" disabled=\"disabled\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Forma&ccedil;&atilde;o: <span class=\"style35 style40\">
<input name=\"formacao\" type=\"text\" id=\"formacao\" value=\"Administrador\" size=\"20\" disabled=\"disabled\" />
</span></span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35 style40\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:<span class=\"style35\">
<input name=\"rg\" type=\"text\" id=\"rg\" size=\"20\" maxlength=\"14\" value=\"3.531.222-1\" disabled=\"disabled\" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CPF: <span class=\"style35\">
<input name=\"cpf\" type=\"text\" id=\"cpf\" value=\"570.072.418-91\" size=\"20\" disabled=\"disabled\" />
</span> </span></td>
</tr>
<tr>
<td height=\"31\" colspan=\"6\" bgcolor=\"#CCFF99\"><div align=\"right\" class=\"style35\">
<div align=\"center\" class=\"style35\">DADOS DA EMPRESA CONTRATADA</div>
</div></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\"><strong>Nome Fantasia:</strong></span>
<input name=\"c_fantasia\" type=\"text\" id=\"c_fantasia\" value='$row[c_fantasia]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_fantasia.style.background='#CCFFCC'\" 
onblur=\"document.all.c_fantasia.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" size=\"90\" /></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;Raz&atilde;o Social:
<input name=\"c_razao\" type=\"text\" id=\"c_razao\" size=\"90\" value='$row[c_razao]'
onfocus=\"document.all.c_razao.style.background='#CCFFCC'\" 
onblur=\"document.all.c_razao.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Endere&ccedil;o:
<input name=\"c_endereco\" type=\"text\" id=\"c_endereco\" size=\"90\" value='$row[c_endereco]'
onfocus=\"document.all.c_endereco.style.background='#CCFFCC'\" 
onblur=\"document.all.c_endereco.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CNPJ:
<input name=\"c_cnpj\" type=\"text\" id=\"c_cnpj\" value='$row[c_cnpj]'
style=\"background:#FFFFFF; text-transform:uppercase;\"
onfocus=\"document.all.c_cnpj.style.background='#CCFFCC'\" 
onblur=\"document.all.c_cnpj.style.background='#FFFFFF'\" 
onkeyup=\"pula(18,this.id,c_ie.id)\"
onkeypress=\"formatar('##.###.###/####-##', this)\" size=\"18\" maxlength=\"18\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IE:
<input name=\"c_ie\" type=\"text\" id=\"c_ie\" size=\"15\" value='$row[c_ie]'
onfocus=\"document.all.c_ie.style.background='#CCFFCC'\" onblur=\"document.all.c_ie.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CCM:
<input name=\"c_im\" type=\"text\" id=\"c_im\" size=\"15\" value='$row[c_im]'
onfocus=\"document.all.c_im.style.background='#CCFFCC'\" onblur=\"document.all.c_im.style.background='#FFFFFF'\" style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Telefone:
<input name='c_tel' type='text' id='c_tel' size='12' value='$row[c_tel]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,c_fax.id)\" 
onfocus=\"document.all.c_tel.style.background='#CCFFCC'\" 
onblur=\"document.all.c_tel.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:
<input name=\"c_fax\" type=\"text\" id=\"c_fax\" size=\"12\" value='$row[c_fax]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,c_email.id)\" 
onfocus=\"document.all.c_fax.style.background='#CCFFCC'\" 
onblur=\"document.all.c_fax.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E-mail: <span class=\"style35 style40\">
<input name=\"c_email\" type=\"text\" id=\"c_email\" size=\"25\" value='$row[c_email]'
onfocus=\"document.all.c_email.style.background='#CCFFCC'\" 
onblur=\"document.all.c_email.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span> </span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Responsavel:
<input name=\"c_responsavel\" type=\"text\" id=\"c_responsavel\" size=\"40\" value='$row[c_responsavel]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_responsavel.style.background='#CCFFCC'\" 
onblur=\"document.all.c_responsavel.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name=\"c_civil\" type=\"text\" id=\"c_civil\" size=\"20\" value='$row[c_civil]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_civil.style.background='#CCFFCC'\" 
onblur=\"document.all.c_civil.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">Nacionalidade:
<input name=\"c_nacionalidade\" type=\"text\" id=\"c_nacionalidade\" size=\"40\" value='$row[c_nacionalidade]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_nacionalidade.style.background='#CCFFCC'\" 
onblur=\"document.all.c_nacionalidade.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Forma&ccedil;&atilde;o: <span class=\"style35 style40\">
<input name=\"c_formacao\" type=\"text\" id=\"c_formacao\" size=\"20\" value='$row[c_formacao]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.c_formacao.style.background='#CCFFCC'\" 
onblur=\"document.all.c_formacao.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35 style40\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:<span class=\"style35\">
<input name=\"c_rg\" type=\"text\" id=\"c_rg\" value='$row[c_rg]'
onkeypress=\"formatar('##.###.###-##', this)\" size=\"20\" maxlength=\"14\" 
onfocus=\"document.all.c_rg.style.background='#CCFFCC'\" 
onblur=\"document.all.c_rg.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CPF: <span class=\"style35\">
<input name=\"c_cpf\" type=\"text\" id=\"c_cpf\" value='$row[c_cpf]'
onkeypress=\"formatar('###.###.###-##', this)\" size=\"20\" maxlength=\"14\" 
onkeyup=\"pula(14,this.id,c_email2.id)\" 
onfocus=\"document.all.c_cpf.style.background='#CCFFCC'\" 
onblur=\"document.all.c_cpf.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span> </span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;E-mail: <span class=\"style35 style40\">
<input name=\"c_email2\" type=\"text\" id=\"c_email2\" size=\"30\" value='$row[c_email2]'
onfocus=\"document.all.c_email2.style.background='#CCFFCC'\" 
onblur=\"document.all.c_email2.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Site: <span class=\"style35 style40\">
<input name=\"c_site\" type=\"text\" id=\"c_site\" size=\"38\" value='$row[c_site]'
onfocus=\"document.all.c_site.style.background='#CCFFCC'\" 
onblur=\"document.all.c_site.style.background='#FFFFFF'\"
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span></span></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCFF99\"><div align=\"center\"><span class=\"style35\">DADOS DA PESSOA DE  CONTATO NA CONTRATADA</span> </div></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;Nome Completo:
<input name=\"co_responsavel\" type=\"text\" id=\"co_responsavel\" size=\"27\" value='$row[co_responsavel]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_responsavel.style.background='#CCFFCC'\" 
onblur=\"document.all.co_responsavel.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Telefone:
<input name='co_tel' type='text' id='co_tel' size='12' value='$row[co_tel]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,co_fax.id)\" 
onfocus=\"document.all.co_tel.style.background='#CCFFCC'\" 
onblur=\"document.all.co_tel.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fax:
<input name=\"co_fax\" type=\"text\" id=\"co_fax\" size=\"12\" value='$row[co_fax]'
onkeypress=\"return(TelefoneFormat(this,event))\" 
onkeyup=\"pula(13,this.id,co_civil.id)\" 
onfocus=\"document.all.co_fax.style.background='#CCFFCC'\" 
onblur=\"document.all.co_fax.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Estado civil:
<input name=\"co_civil\" type=\"text\" id=\"co_civil\" size=\"20\" value='$row[co_civil]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_civil.style.background='#CCFFCC'\" 
onblur=\"document.all.co_civil.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nacionalidade:
<input name=\"co_nacionalidade\" type=\"text\" id=\"co_nacionalidade\" size=\"27\" value='$row[co_nacionalidade]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_nacionalidade.style.background='#CCFFCC'\" 
onblur=\"document.all.co_nacionalidade.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></td>
</tr>
<tr>
<td height=\"35\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Email: <span class=\"style35 style40\">
<input name=\"co_email\" type=\"text\" id=\"co_email\" size=\"30\" value='$row[co_email]'
onfocus=\"document.all.co_email.style.background='#CCFFCC'\" 
onblur=\"document.all.co_email.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF; text-transform:lowercase;\" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCFF99\"><div align=\"center\" class=\"style35\">OBJETO DO CONTRATO</div></td>
</tr>
<tr>
<td height=\"44\" colspan=\"6\"><span class=\"style35\">Munic&iacute;pio onde ser&aacute; executado o servi&ccedil;o:<span class=\"style35 style40\">
<input name=\"co_municipio\" type=\"text\" id=\"co_municipio\" size=\"20\" value='$row[co_municipio]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.co_municipio.style.background='#CCFFCC'\" 
onblur=\"document.all.co_municipio.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span></span></td>
</tr>
<tr>
<td height=\"44\" colspan=\"6\"><span class=\"style35\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Assunto:<span class=\"style35 style40\">
<input name=\"assunto\" type=\"text\" id=\"assunto\" size=\"20\" value='$row[assunto]'
style=\"background:#FFFFFF;\" 
onfocus=\"document.all.assunto.style.background='#CCFFCC'\" 
onblur=\"document.all.assunto.style.background='#FFFFFF'\" 
onchange=\"this.value=this.value.toUpperCase()\" />
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data do Processo:
<input name=\"data_proc\" type=\"text\" id=\"data_proc\" size=\"10\" value='$row[data_proc]'
onkeyup=\"mascara_data(this)\" maxlength=\"10\"
onfocus=\"document.all.data_proc.style.background='#CCFFCC'\" 
onblur=\"document.all.data_proc.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span></td>
</tr>
<tr>
<td height=\"102\" colspan=\"6\"><div align=\"center\">
<label>
<textarea name=\"objeto\" id=\"objeto\" cols=\"45\" rows=\"5\"
onfocus=\"document.all.objeto.style.background='#CCFFCC'\" 
onblur=\"document.all.objeto.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"
onchange=\"this.value=this.value.toUpperCase()\">$row[objeto]</textarea>
</label>
</div></td>
</tr>
<tr>
<td colspan=\"6\" bgcolor=\"#CCFF99\"><div align=\"center\" class=\"style35\">ESPECIFICA&Ccedil;&Atilde;O DO TIPO DE SERVI&Ccedil;O A SER PRESTADO</div></td>
</tr>
<tr>
<td height=\"102\" colspan=\"6\"><div align=\"center\">
<label>
<textarea name=\"especificacao\" id=\"especificacao\" cols=\"45\" rows=\"5\" 
onfocus=\"document.all.especificacao.style.background='#CCFFCC'\" 
onblur=\"document.all.especificacao.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"
onchange=\"this.value=this.value.toUpperCase()\">$row[especificacao]</textarea>
</label>
</div></td>
</tr>
<tr style=\"display:none\">
<td height=\"46\" colspan=\"6\" ><span class=\"style35\"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ANEXO I&nbsp;&nbsp;&nbsp; &ndash;  &nbsp;&nbsp;&nbsp;VALOR R$
<input name=\"valor\" type=\"text\" id=\"valor\" size=\"20\" 
onkeydown=\"FormataValor(this,event,20,2)\" 
onfocus=\"document.all.valor.style.background='#CCFFCC'\" 
onblur=\"document.all.valor.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\"/>
</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"style35\">DATA: &nbsp;
<input name=\"data_inicio\" type=\"text\" id=\"data_inicio\" size=\"10\" 
onkeyup=\"mascara_data(this)\" maxlength=\"10\"
onfocus=\"document.all.data_inicio.style.background='#CCFFCC'\" 
onblur=\"document.all.data_inicio.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF;\" />
</span></td>
</tr>
</table>
<center>
<br>
<input type='hidden' name='id' value='4'>
<input type='hidden' name='regiao' value='$regiao'>
<input type='hidden' name='id_prestador' value='$id_prestador'>
<br>
<input type='submit' name='Submit' id='button' value='Atualizar'>
</center>
</form>
";
break;

case 4:

$id_prestador = $_REQUEST['id_prestador'];
$id_projeto = $_REQUEST['projeto'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$endereco = $_REQUEST['endereco'];
$cnpj = $_REQUEST['cnpj'];
$c_fantasia = $_REQUEST['c_fantasia'];
$c_razao = $_REQUEST['c_razao'];
$c_endereco = $_REQUEST['c_endereco'];
$c_cnpj = $_REQUEST['c_cnpj'];
$c_ie = $_REQUEST['c_ie'];
$c_im = $_REQUEST['c_im'];
$c_tel = $_REQUEST['c_tel'];
$c_fax = $_REQUEST['c_fax'];
$c_email = $_REQUEST['c_email'];
$c_responsavel = $_REQUEST['c_responsavel'];
$c_civil = $_REQUEST['c_civil'];
$c_nacionalidade = $_REQUEST['c_nacionalidade'];
$c_formacao = $_REQUEST['c_formacao'];
$c_rg = $_REQUEST['c_rg'];
$c_cpf = $_REQUEST['c_cpf'];
$c_email2 = $_REQUEST['c_email2'];
$c_site = $_REQUEST['c_site'];
$co_responsavel = $_REQUEST['co_responsavel'];
$co_tel = $_REQUEST['co_tel'];
$co_fax = $_REQUEST['co_fax'];
$co_civil = $_REQUEST['co_civil'];
$co_nacionalidade = $_REQUEST['co_nacionalidade'];
$co_email = $_REQUEST['co_email'];
$co_municipio = $_REQUEST['co_municipio'];
$assunto = $_REQUEST['assunto'];
$objeto = $_REQUEST['objeto'];
$especificacao = $_REQUEST['especificacao'];
/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/
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

$data_inicio_f = ConverteData($data_inicio);
$data_proc_f = ConverteData($data_proc);

mysql_query("UPDATE prestadorservico SET 
endereco = '$endereco', 
cnpj = '$cnpj', 
c_fantasia = '$c_fantasia', 
c_razao = '$c_razao', 
c_endereco = '$c_endereco', 
c_cnpj = '$c_cnpj', 
c_ie = '$c_ie', 
c_im = '$c_im', 
c_tel = '$c_tel', 
c_fax = '$c_fax', 
c_email = '$c_email', 
c_responsavel = '$c_responsavel', 
c_civil = '$c_civil', 
c_nacionalidade = '$c_nacionalidade', 
c_formacao = '$c_formacao', 
c_rg = '$c_rg', 
c_cpf = '$c_cpf', 
c_email2 = '$c_email2', 
c_site = '$c_site', 
co_responsavel = '$co_responsavel', 
co_tel = '$co_tel', 
co_fax = '$co_fax', 
co_civil = '$co_civil', 
co_nacionalidade = '$co_nacionalidade', 
co_email = '$co_email', 
co_municipio = '$co_municipio', 
assunto = '$assunto', 
objeto = '$objeto', 
especificacao = '$especificacao' WHERE id_prestador = '$id_prestador' ") or die ("Erro<br>".mysql_error());

print "
<script>
alert (\"$id_prestado - Dasos Atualizados!\"); 
location.href=\"prestadorservico.php?id=1&regiao=$regiao\"
</script>";

} // FECHANDO O   CASE
}
/* Liberando o resultado */
//mysql_free_result($result);
/* Fechando a conexão */
//mysql_close($conn);

?>