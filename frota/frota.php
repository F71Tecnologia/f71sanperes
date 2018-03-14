<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../classes/funcionario.php');

$FUNCIONARIO = new funcionario();

$id      = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao  = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao' and id_master = '$master'");
$row_local    = mysql_fetch_array($result_local);

// SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$result_user   = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);
$master        = $row_user['id_master'];
//

// CRIANDO UM ARRAY COM TODAS AS REGIÕES COM SEUS RESPECTIVOS ID's
$REReg = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE status = 1 and id_master = '$master'");
while ($row_regiao = mysql_fetch_array($REReg)){
	$idReg = $row_regiao['0'];
	$REGIOES[$idReg] = $row_regiao['1'];
	$regioes_master[] = $row_regiao['0'];
	
}
$regioes_master = implode(',',$regioes_master);

/*
PAULO		= 64
SILVANIA 	= 27
FABRICIO	= 65
EUGENIO		= 28
*/
if($id_user == 1 
or $id_user == 9 
or $id_user == 64 
or $id_user == 65 
or $id_user == 68 
or $id_user == 5 
or $id_user == 27 
or $id_user == 28 
or $id_user == 80 
or $id_user == 85 
or $id_user == 71 
or  $id_user == 87)

{
	$displayrel="display:";
}else{
	$displayrel="display:none";
}

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';

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

if (d.data_nasc.value == "" ){

alert("O campo Data de Nascimento deve ser preenchido!");

d.data_nasc.focus();

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

if (d.tipo_admicao.value == "" ){

alert("O campo Local do tipo de admição deve ser preenchido!");

d.tipo_admicao.focus();

return false;

}

return true;

}
</script>
<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<style type="text/css">
<!--
.dragme{position:relative;}

body {
	margin: 0px;
}
.style35 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style36 {
	font-size: 14px;
	font-family: Verdana, Geneva, sans-serif;
}
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
}
.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}
.style50 {font-family: Geneva, Arial, Helvetica, sans-serif; font-size: 10; font-weight: bold; color: #FFFFFF; }
.style51 {
	font-family: arial, verdana, "ms sans serif";
	font-weight: bold;
}
.style52 {font-family: arial, verdana, "ms sans serif"}
.style53 {font-family: Arial, Verdana, Helvetica, sans-serif}
.style55 {font-size: 10}
.style56 {font-family: Arial, Verdana, Helvetica, sans-serif; font-weight: bold; }
.style12 {	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style291 {color: #000000}
.style31 {font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 14px;
	color: #FF0000;
}
-->
</style>
<script language="javascript">
$(function() {
	$('#inicio').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	$('#fim').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
});

function TelefoneFormat(Campo, e) {
	
	var key = '';
	var len = 0;
	var strCheck = '0123456789';
	var aux = '';
	var whichCode = (window.Event) ? e.which : e.keyCode;
	if (whichCode == 13 || whichCode == 8 || whichCode == 0){
		return true;  // Enter backspace ou FN qualquer um que não seja alfa numerico
	}
	
	key = String.fromCharCode(whichCode);
	if (strCheck.indexOf(key) == -1){
		return false;  //NÃO E VALIDO
	}
	aux =  Telefone_Remove_Format(Campo.value);
	len = aux.length;
	if(len>=10){
		return false;	//impede de digitar um telefone maior que 10
	}
	aux += key;
	Campo.value = Telefone_Mont_Format(aux);

	return false;
}

function  Telefone_Mont_Format(Telefone){
	
	var aux = len = '';
	len = Telefone.length;
	if(len<=9){
		tmp = 5;
	}else{
		tmp = 6;
	}
	
	aux = '';
	for(i = 0; i < len; i++){
		if(i==0){
			aux = '(';
		}
		
		aux += Telefone.charAt(i);
		if(i+1==2){
			aux += ')';
		}
		
		if(i+1==tmp){
			aux += '-';
		}
	}

	return aux ;
}

function  Telefone_Remove_Format(Telefone){
	
	var strCheck = '0123456789';
	var len = i = aux = '';
	len = Telefone.length;
	for(i = 0; i < len; i++){
		if (strCheck.indexOf(Telefone.charAt(i))!=-1){
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


function verifica_data(d) {  
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


function FormataValor(objeto,teclapres,tammax,decimais) {
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

function CorFundo(campo,cor){
	var d = document;
	if(cor == 1){
		var color = "#CCFFCC";
	}else{
		var color = "#FFFFFF";
	}

	d.getElementById(campo).style.background=color;
	
}

<!-- This script and many more are available free online at -->
<!-- Created by: elouai.com -->
<!-- Início

var ie=document.all;
var nn6=document.getElementById&&!document.all;
var isdrag=false;
var x,y;
var dobj;

function movemouse(e)
{
  if (isdrag)
  {
    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y;
    return false;
  }
}

function selectmouse(e)
{
  var fobj       = nn6 ? e.target : event.srcElement;
  var topelement = nn6 ? "HTML" : "BODY";
  while (fobj.tagName != topelement && fobj.className != "dragme")
  {
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

  if (fobj.className=="dragme")
  {
    isdrag = true;
    dobj = fobj;
    tx = parseInt(dobj.style.left+0);
    ty = parseInt(dobj.style.top+0);
    x = nn6 ? e.clientX : event.clientX;
    y = nn6 ? e.clientY : event.clientY;
    document.onmousemove=movemouse;
    return false;
  }

}

document.onmousedown=selectmouse;
document.onmouseup=new Function("isdrag=false");
//  Fim -->

</script>

</head>

<body bgcolor="#FFFFFF">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr> 
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="6" background="../layout/esquerdo.gif">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="26" rowspan="6" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><span style="float:right; margin-right:15px"> <?php include('../reportar_erro.php'); ?> </span>
    <span style="clear:right"></span></td>
       
        </tr>
        <tr>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif"><table  height="191" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="45" bgcolor="#003300"><div align="right" class="style35">
                <div align="center" class="style27 style36">GERENCIAMENTO DE VE&Iacute;CULOS<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td height="144" align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>

<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr class="campotexto">
    <td height="112" align="center" valign="middle"><img src="../imagens/c1.gif" width="88" height="60" border="0" style="cursor:pointer"
      onClick="document.all.cad.style.display = (document.all.cad.style.display == 'none') ? '' : 'none' ;"><br>
      CADASTRO DE VE&Iacute;CULO</td>
    <td align="center" valign="middle"><img src="../imagens/c2.gif" width="88" height="60" border="0" style="cursor:pointer"
      onClick="document.all.comb.style.display = (document.all.comb.style.display == 'none') ? '' : 'none' ;"><br>
      SOLICITA&Ccedil;&Atilde;O DE COMBUST&Iacute;VEL</td>
    <td align="center" valign="middle"><img src="../imagens/c3.gif" width="88" height="60" border="0" style="cursor:pointer"
      onClick="document.all.reti.style.display = (document.all.reti.style.display == 'none') ? '' : 'none' ;"><br>
      RETIRADAS E ENTREGAS DE VE&Iacute;CULOS</td>
    <td align="center" valign="middle"><img src="../imagens/c4.gif" alt="img" width="88" height="60" border="0" style="cursor:pointer"
      onClick="document.all.multa.style.display = (document.all.multa.style.display == 'none') ? '' : 'none' ;"><br> 
      CADASTRAMENTO DE MULTAS</td>
  </tr>
  <tr class="campotexto">
    <td align="center" valign="baseline"><div style="background-color:#09F; width:50; height:2;">&nbsp;</div></td>
    <td align="center" valign="baseline"><div style="background-color:#0C6; width:50; height:2;">&nbsp;</div></td>
    <td align="center" valign="middle"><div style="background-color:#FC9; width:50; height:2;">&nbsp;</div></td>
    <td align="center" valign="middle"><div style="background-color:#C00; width:50; height:2;">&nbsp;</div></td>
  </tr>
  </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><div align="center"></div></td>
        </tr>
        <tr>
          <td height="47" colspan="2">
          <table  height="191" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333" style=" <?=$displayrel?> ">
            <tr>
              <td height="45" bgcolor="#003300"><div align="right" class="style35">
                <div align="center" class="style27 style36">RELAT&Oacute;RIOS DE ABASTECIMENTO<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td height="144" align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                <form action="../financeiro/abastecimento.php" method="post" name="formabas" target="_parent">
                  <p align="center"><span class="style12 style29">
                    <label>Marque para ver relat&oacute;rio anual:&nbsp;
                      <input type="checkbox" name="anotodo" id="anotodo" value="1" onClick="document.formabas.mes.style.display = (document.formabas.mes.style.display == 'none') ? '' : 'none' ;">
                    </label>
                    <br>
                    <br>
                    &nbsp;
                    <select name="mes" id="mes" class='textarea2'>
                      <option value="01">Janeiro</option>
                      <option value="02">Fevereiro</option>
                      <option value="03">Mar&ccedil;o</option>
                      <option value="04">Abril</option>
                      <option value="05">Maio</option>
                      <option value="06">Junho</option>
                      <option value="07">Julho</option>
                      <option value="08">Agosto</option>
                      <option value="09">Setembro</option>
                      <option value="10">Outubro</option>
                      <option value="11">Novembro</option>
                      <option value="12">Dezembro</option>
                    </select>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <select name="ano" id="ano" class='textarea2'>
                      <option>2005</option>
                      <option>2006</option>
                      <option>2007</option>
                      <option>2008</option>
                      <option>2009</option>
                      <option>2010</option>
                      <option>2011</option>
                      <option selected>2012</option>
                      <option>2013</option>
                      <option>2014</option>
                    </select>
                    <br>
                    <br>
                    </span>
                    <input type="submit" value="Visualizar Relat&oacute;rio de Abastecimento">
                  </p>
                </form>
                <p>&nbsp;</p>
    
    <form action="../financeiro/abastecimento.php" method="post">
      <span class="style12 style29">
      	<label>Relat&oacute;rio por per&iacute;odos</label>
      </span>
      <br>
      <br>
	  <input type="text" name="inicio" id="inicio" value="<?=date('d/m/Y', strtotime('-1 month'))?>" class='textarea2' style="width:70px; text-align:center;" /> à
      <input type="text" name="fim"    id="fim"    value="<?=date('d/m/Y')?>" class='textarea2' style="width:70px; text-align:center;" />
      <br>
      <br>
      <input type="hidden" name="periodos" value="1">
      <input type="submit" value="Visualizar Relat&oacute;rio de Abastecimento">
    </form>

                </td>
            </tr>
          </table></td>
        </tr>
        
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4" bgcolor="#E2E2E2"> <img src="../layout/baixo.gif" width="750" height="38"> 
            <div align="center" class="style6"><br>
          </div></td>
        </tr>
      </table>
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='cad' style="display:none" >
        <tr>
          <td colspan="4"><img src="../layout/topo.gif" alt="img" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          <form action="frotacad.php" method="post" name="form1" enctype='multipart/form-data' onSubmit="return validaForm1()">
          <table  height="159" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="28" bgcolor="#0099FF"><div align="right" class="style35">
                <div align="center" class="style27 style36">CADASTRO DE VE&Iacute;CULO<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                
                <table border="0" cellspacing="3" cellpadding="0">
                  <tr class="campotexto">
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Marca:</td>
                    <td height="28" colspan="5" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;
                    <input name='marca' type='text' id='marca' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                    </tr>
                  <tr class="campotexto">
                    <td width="62" height="28" align="center" valign="middle" bgcolor="#CCFFCC">Modelo:</td>
                    <td height="28" colspan="5" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;
                    <input name='modelo' type='text' id='modelo' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                    </tr>
                  <tr class="campotexto">
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Ano:</td>
                    <td width="80" height="28" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;
                    <select name="ano" id="ano" class="campotexto">
                      <option value="07">2007</option>
                      <option value="08">2008</option>
                      <option value="09">2009</option>
                      <option value="10">2010</option>
                    </select></td>
                    <td width="215" height="28" align="center" valign="middle" bgcolor="#CCFFCC">Fabrica&ccedil;&atilde;o:</td>
                    <td width="84" height="28" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;
                    <select name="fab" id="fab" class="campotexto">
                      <option value="07">2007</option>
                      <option value="08">2008</option>
                      <option value="09">2009</option>
                      <option value="10">2010</option>
                    </select></td>
                    <td width="63" height="28" align="center" valign="middle" bgcolor="#CCFFCC">Placa:</td>
                    <td width="137" height="28" align="left" valign="middle" bgcolor="#CCCCCC">
                    
                    &nbsp;
                    <input name='placa' type='text' id='placa' size='15' class='campotexto' 
                    onKeyUp="pula(8, this.id, apolice.id)" 
                    onKeyPress="formatar('###-####',this)"
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)">
                    
                    </td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="2" align="center" valign="middle" bgcolor="#CCFFCC">Ap&oacute;lice de Seguro:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">
                    
                    &nbsp;
                    <input name='apolice' type='text' id='apolice' size='20' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)">
                    
                    </td>
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Telefone Seguro:</td>
                    <td height="28" colspan="2" valign="middle" bgcolor="#CCCCCC">
                    
                    &nbsp;
                    <input name='telefone' type='text' id='telefone' size='15' class='campotexto' 
                    onKeyPress="return(TelefoneFormat(this,event))" 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="2" align="center" valign="middle" bgcolor="#CCFFCC">Local de Origem:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;
                    <select name='regiao' class='campotexto' id='regiao'>
                    <?php
					$REReg = mysql_query("SELECT * FROM regioes WHERE status = 1 and id_master = '$master'");
                    while ($row_regiao = mysql_fetch_array($REReg)){
                    	
						if($row_regiao['0'] == $regiao){
							print "<option value=$row_regiao[0] selected>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}else{
							print "<option value=$row_regiao[0]>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}
                    
                    }
                    ?>
                    </select></td>
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Foto:</td>
                    <td height="28" colspan="2" align="left" valign="middle" bgcolor="#CCCCCC"><label>
                      &nbsp;
                      <input name="foto" type="file" id="foto" size="15" class="campotexto"
                      onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)" >
                    </label></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="35" colspan="6" align="center" valign="middle" bgcolor="#CCFFCC">
                    <input type="hidden" name="id" id="id" value="1">
                    <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$regiao?>">
                      <input type="submit" name="enviar" id="enviar" value="Gravar">
                      
                      <script language="javascript">
                      function validaForm1(){
                        d = document.form1;

                        if (d.marca.value == ""){
                        	alert("O campo Marca deve ser preenchido!");
                        	d.marca.focus();
                            return false;
                        }
						if (d.modelo.value == ""){
                        	alert("O campo Modelo deve ser preenchido!");
                        	d.modelo.focus();
                            return false;
                        }
						if (d.placa.value == ""){
                        	alert("O campo Placa deve ser preenchido!");
                        	d.placa.focus();
                            return false;
                        }

                        return true;   
					  }

                      </script>
                      
                      
                    </td>
                    </tr>
                </table></td>
            </tr>
          </table>
          
          </form>
            <br>
            <br>
            <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="9" align="center" valign="middle" bgcolor="#0099FF" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36">VE&Iacute;CULOS CADASTRADOS<br>
                  </div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="14%" align="center" valign="middle" bgcolor="#CCFFCC">Modelo</td>
                <td width="8%" align="center" valign="middle" bgcolor="#CCFFCC">Ano</td>
                <td width="14%" align="center" valign="middle" bgcolor="#CCFFCC">Placa</td>
                <td width="16%" align="center" valign="middle" bgcolor="#CCFFCC">Ap&oacute;lice</td>
                <td width="19%" align="center" valign="middle" bgcolor="#CCFFCC">Tel. Seguro</td>
                <td width="16%" align="center" valign="middle" bgcolor="#CCFFCC">Imagem</td>
                <td width="13%" align="center" valign="middle" bgcolor="#CCFFCC">Multas</td>
              </tr>
              <?php
              $cont = 0;
			  $RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_regiao IN ($regioes_master)");
			  while($Row_carros = mysql_fetch_array($RE_carros)){
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
			  if($Row_carros['foto'] != '0'){
				  $img = "<img src='fotos/carro$Row_carros[0]"."$Row_carros[foto]' width='25' height='25'>";
			  }else{
				  $img = "S/Imagem";
			  }
			  
			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$Row_carros[modelo]</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_carros[ano]</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_carros[placa]</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_carros[apolice]</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_carros[telefone]</td>
                <td align='center' valign='middle' bgcolor=$color>$img</td>
                <td align='center' valign='middle' bgcolor=$color>&nbsp;</td>
              </tr>";
			  $cont ++;
			  }
              ?>  
			  
            </table>
            <br>
            <br></td>
        </tr>
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4"  bgcolor="#E2E2E2"><img src="../layout/baixo.gif" alt="img" width="750" height="38">
            <div align="center" class="style6"></div></td>
        </tr>
      </table>
      
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="comb" style="display:">
        <tr>
          <td colspan="4"><img src="../layout/topo.gif" alt="img" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          <form action="frotacad.php" method="post" name="form2" id="form2" onSubmit="return validaForm1()">
          <table  height="159" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="28" bgcolor="#00CC66"><div align="right" class="style35">
                <div align="center" class="style27 style36">SOLICITA&Ccedil;&Atilde;O DE COMBUST&Iacute;VEL<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                
                <table width="100%" border="0" cellpadding="0" cellspacing="3">
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Interno:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <label>
                    <input type="radio" name="interno" id="interno" value="1"  
                    onClick="document.all.spa1.style.display = 'none'; document.form2.veiculo.style.display = '';"> Sim                    </label>
                    &nbsp;&nbsp;&nbsp;
                    <label>
                    <input type="radio" name="interno" id="interno" value="2"
                     onClick="document.form2.veiculo.style.display = 'none'; document.all.spa1.style.display = '';"> Não                    </label>                    </td>
                  </tr>
                  <tr class="campotexto">
                    <td width="121" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='veiculo' class='campotexto' id='veiculo' style="display:none">
                        <?php
					  
					  $RE_carros2 = mysql_query("SELECT * FROM fr_carro WHERE id_regiao = '$regiao' AND status = '1'");
					  
					  while($Row_carros2 = mysql_fetch_array($RE_carros2)){
						  print "<option value=$Row_carros2[0]>$Row_carros2[0] - $Row_carros2[marca] $Row_carros2[modelo] - $Row_carros2[placa]</option>";
					  }
                    
                      ?>
                      </select><span id="spa1" style="display:none">
                      <input name="veiculo2" type="text" class="campotexto" id="veiculo2" size="25"
                       onChange="this.value=this.value.toUpperCase()" onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)">
                      &nbsp;&nbsp;&nbsp;Placa:&nbsp;
                      <input name="placa" type="text" class="campotexto" id="placa" size="12" maxlength="8"
                    onKeyPress="formatar('###-####',this)"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2); this.value=this.value.toUpperCase()">
                      </span>                      </td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Funcion&aacute;rio:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <label>
                    <input type="radio" name="funcionario" id="funcionario" value="1"  
                    onClick="document.all.spa2.style.display = 'none'; document.form2.user.style.display = '';"> Sim                    </label>
                    &nbsp;&nbsp;&nbsp;
                    <label>
                    <input type="radio" name="funcionario" id="funcionario" value="2"
                     onClick="document.form2.user.style.display = 'none'; document.all.spa2.style.display = '';"> Não                    </label> </td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Nome:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <select name="user" class="campotexto" id="user" style="display:none">
                    <?php
                     $FUNCIONARIO->preenche_select_nome($id_user,$master,$regiao);  
                      ?>
                      </select>
                      <span id="spa2" style="display:none">
                    <input name="nome" type="text" class="campotexto" id="nome" size="25" onChange="this.value=this.value.toUpperCase()">
                    &nbsp;&nbsp;&nbsp; RG:&nbsp;
                    <input name="rg" type="text" class="campotexto" id="rg" size="12">
                    </span>                    </td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Km Atual:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                        <input name='kmatual' type='text' id='kmatual' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Destino:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='destino' type='text' id='destino' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Local de Origem:&nbsp;</td>
                    <td width="284" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='regiao' class='campotexto' id='regiao'>
                        <?php
					$REReg = mysql_query("SELECT * FROM regioes WHERE status = 1 and id_master = '$master'");
                    while ($row_regiao = mysql_fetch_array($REReg)){
                    	
						if($row_regiao['0'] == $regiao){
							print "<option value=$row_regiao[0] selected>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}else{
							print "<option value=$row_regiao[0]>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}
                    
                    }
                    ?>
                        </select></td>
                    <td width="61" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Data:&nbsp;</td>
                    <td width="181" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='dataT' type='text' id='dataT' size='12' class='campotexto' value='<?=$data?>'
                    onKeyUp="mascara_data(this)"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC"><label>
                      <input type="hidden" name="id" id="id" value="2">
                      <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$regiao?>">
                      <input type="submit" name="enviar2" id="enviar2" value="Gravar">
                    </label></td>
                  </tr>
                </table></td>
            </tr>
          </table>
          </form>
            <br>
            <br>
            <div align="center">
              <a href="historico.php?id=1&regiao=<?=$regiao?>" 
              style="text-decoration:none; font-weight:bold; color:#000">VISUALIZAR HIST&Oacute;RICO DE SOLICITA&Ccedil;&Otilde;ES</a></div>
            <br>
            <br>
            </td>
        </tr>
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        <tr valign="top">
          <td colspan="4" bgcolor="#E2E2E2"><img src="../layout/baixo.gif" alt="img" width="750" height="38">
            </td>
        </tr>
      </table>
     
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="reti" style="display:none">
        <tr>
          <td colspan="4"><img src="../layout/topo.gif" alt="img" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          <form action="frotacad.php" method="post" name="form1" onSubmit="return validaForm1()">
          <table  height="159" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="28" bgcolor="#FFCC99"><div align="right" class="style35">
                <div align="center" class="style27 style36" style="color:#000">DEFINI&Ccedil;&Atilde;O DE ROTA<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                
                <table width="100%" border="0" cellpadding="0" cellspacing="3">
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='veiculo' class='campotexto' id='veiculo'>
                      <?php
					  
					  $RE_carros2 = mysql_query("SELECT * FROM fr_carro");
					  
					  while($Row_carros2 = mysql_fetch_array($RE_carros2)){
						  print "<option value=$Row_carros2[0]>$Row_carros2[0] - $Row_carros2[marca] $Row_carros2[modelo] - $Row_carros2[placa]</option>";
					  }
                    
                      ?>
                      </select></td>
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Km atual:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <input name='km' type='text' id='km' size='17' class='campotexto' 
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                    </tr>
                  <tr class="campotexto">
                    <td width="109" height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Destino:</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='destino3' type='text' class='campotexto' id='destino3'
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)" 
                    onChange="this.value=this.value.toUpperCase()" size='40' ></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">Local de Origem:</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='regiao3' class='campotexto' id='regiao3'>
                        <?php
					$REReg = mysql_query("SELECT * FROM regioes WHERE status = 1 and id_master = '$master'");
                    while ($row_regiao = mysql_fetch_array($REReg)){
                    	
						if($row_regiao['0'] == $regiao){
							print "<option value=$row_regiao[0] selected>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}else{
							print "<option value=$row_regiao[0]>$row_regiao[0] - $row_regiao[regiao] - $row_regiao[sigla]</option>";
						}
                    
                    }
                    ?>
                      </select></td>
                    </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Respons&aacute;vel:</td>
                    <td width="306" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
  						<select name='responsavel' class='campotexto' id='responsavel'>
    					<?php
					  
					  $REFunc = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
					  
					  while($RowFunc = mysql_fetch_array($REFunc)){
						  print "<option value=$RowFunc[0]>$RowFunc[nome1]</option>";
					  }
                    
                      ?>
  					</select></td>
                    <td width="75" height="28" align="center" valign="middle" bgcolor="#CCFFCC">Data:</td>
                    <td width="157" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='data3' type='text' id='data3' size='12' class='campotexto' value='<?=$data?>'
                    onKeyUp="mascara_data(this)"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC"><label>
                      <input type="hidden" name="id" id="id" value="3">
                      <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$regiao?>">
                      <input type="submit" name="enviar3" id="enviar3" value="Gravar">
                    </label></td>
                  </tr>
                </table></td>
            </tr>
          </table>
          </form>
            <br>
            <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="7" align="center" valign="middle" bgcolor="#FFCC99" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36" style="color:#000">HIST&Oacute;RICO DE RETIRADAS</div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="12%" align="center" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo</td>
                <td width="14%" align="center" valign="middle" bgcolor="#CCFFCC">Placa</td>
                <td width="22%" align="center" valign="middle" bgcolor="#CCFFCC">Local de Origem</td>
                <td width="19%" align="center" valign="middle" bgcolor="#CCFFCC">Destino</td>
                <td width="9%" align="center" valign="middle" bgcolor="#CCFFCC">Data</td>
                <td width="14%" align="center" valign="middle" bgcolor="#CCFFCC">KM</td>
                <td width="10%" align="center" valign="middle" bgcolor="#CCFFCC">Entregar</td>
              </tr>
              <?php
              $cont = 0;
			  $RE_rota = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')as data FROM fr_rota WHERE id_user = '$id_user' and status_reg='1'");
			  while($RowRota = mysql_fetch_array($RE_rota)){
			  
			  $RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$RowRota[id_carro]'");
			  $RowCarros = mysql_fetch_array($RE_carros);
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
			  $RR2 = $RowRota['id_regiao'];

			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[modelo]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[placa]</td>
                <td align='center' valign='middle' bgcolor=$color>$REGIOES[$RR2]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRota[destino]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRota[data]</td>
				<td align='center' valign='middle' bgcolor=$color>$RowRota[kmini]</td>
				<td align='center' valign='middle' bgcolor=$color><a href='frotacad.php?id=4&rota=$RowRota[0]'>OK</a></td>
              </tr>";
			  $cont ++;
			  }
              ?>
            </table>
            <br>
            <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="7" align="center" valign="middle" bgcolor="#FFCC99" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36" style="color:#000">HIST&Oacute;RICO DE ENTREGA</div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="12%" align="center" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo</td>
                <td width="14%" align="center" valign="middle" bgcolor="#CCFFCC">Placa</td>
                <td width="22%" align="center" valign="middle" bgcolor="#CCFFCC">Local de Origem</td>
                <td width="19%" align="center" valign="middle" bgcolor="#CCFFCC">Destino</td>
                <td width="9%" align="center" valign="middle" bgcolor="#CCFFCC">Data</td>
                <td width="11%" align="center" valign="middle" bgcolor="#CCFFCC">KM</td>
                <td width="13%" align="center" valign="middle" bgcolor="#CCFFCC">KM entrega</td>
              </tr>
              <?php
              $cont = 0;
			  $RE_rota = mysql_query("SELECT *, date_format(data_ent, '%d/%m/%Y')as data_ent FROM fr_rota WHERE id_user = '$id_user' 
				and status_reg='2'");
			  while($RowRota = mysql_fetch_array($RE_rota)){
			  
			  $RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$RowRota[id_carro]'");
			  $RowCarros = mysql_fetch_array($RE_carros);
			  
			  $RE_re = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$RowRota[id_regiao]' and id_master = '$master'");
			  $RowRE = mysql_fetch_array($RE_re);
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[modelo]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[placa]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRE[regiao]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRota[destino]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRota[data_ent]</td>
				<td align='center' valign='middle' bgcolor=$color>$RowRota[kmini]</td>
				<td align='center' valign='middle' bgcolor=$color>$RowRota[kmfim]</td>
              </tr>";
			  $cont ++;
			  }
              ?>
            </table>
           <br> 
           <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="7" align="center" valign="middle" bgcolor="#FFCC99" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36" style="color:#000">RELATÓRIO DE USO IMPRESSO</div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="12%" align="center" valign="middle" bgcolor="#CCFFCC"> <a href="relatorio1.php" target="_blank"> IMPRIMIR </a></td>
                            </tr>
            
            </table>
            <p><br>
            </p></td>
        </tr>
        <tr>
          <td width="155" height="22"></td>
          <td width="549"></td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4"  bgcolor="#E2E2E2"><img src="../layout/baixo.gif" alt="img" width="750" height="38">
            <div align="center" class="style6"></div></td>
        </tr>
      </table>
      
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="multa" style="display:none">
        <tr>
          <td colspan="4"><img src="../layout/topo.gif" alt="img" width="750" height="38"></td>
        </tr>
        <tr>
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          
          <form action="frotacad.php" method="post" name="form4">
          <table  height="159" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="28" bgcolor="#CC0000"><div align="right" class="style35">
                <div align="center" class="style27 style36">CADASTRAMENTO DE MULTAS<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                
                <table border="0" cellspacing="3" cellpadding="0">
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo:</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
<select name='veiculo2' class='campotexto' id='veiculo2'>
  <?php
					  
					  $RE_carros2 = mysql_query("SELECT * FROM fr_carro");
					  
					  while($Row_carros2 = mysql_fetch_array($RE_carros2)){
						  print "<option value=$Row_carros2[0]>$Row_carros2[0] - $Row_carros2[marca] $Row_carros2[modelo] - $Row_carros2[placa]</option>";
					  }
                    
                      ?>
</select></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Rota:</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='rota' class='campotexto' id='rota'>
                        <?php
					  
					  $RERota2 = mysql_query("SELECT * FROM fr_rota");
					  
					  while($RowROTA = mysql_fetch_array($RERota2)){
						  $ORI = $RowROTA['id_regiao'];
						  print "<option value=$RowROTA[0]>$RowROTA[0] - $REGIOES[$ORI] - $RowROTA[destino]</option>";
					  }
                    
                      ?>
                      </select></td>
                  </tr>
                  <tr class="campotexto">
                    <td width="105" height="28" align="right"  valign="middle" bgcolor="#CCFFCC">Tipo da Multa:</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='tipo' type='text' id='tipo' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Local da Multa:</td>
                    <td width="301" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <input name='local' type='text' id='local' size='40' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                    <td width="73" height="28" align="center" valign="middle" bgcolor="#CCFFCC">Data:</td>
                    <td width="168" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <input name='data4' type='text' id='data4' size='12' class='campotexto' 
                    onKeyUp="mascara_data(this)"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Real Infrator:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name='infrator' class='campotexto' id='infrator'>
                        <?php
					  
					  $REFunc = mysql_query("SELECT * FROM funcionario WHERE status_reg = '1' ORDER BY nome1" );
					  
					  while($RowFunc = mysql_fetch_array($REFunc)){
						  print "<option value=$RowFunc[0]>$RowFunc[nome1]</option>";
					  }
                    
                      ?>
                      </select></td>
                    <td height="28" align="center" valign="middle" bgcolor="#CCFFCC">CNH:</td>
                    <td height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name='cnh' type='text' id='cnh' size='20' class='campotexto' 
                    onChange="this.value=this.value.toUpperCase()"
                    onFocus="CorFundo(this.id,1)" onBlur="CorFundo(this.id,2)"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC"><label>
                      
                      <input type="hidden" name="id" id="id" value="5">
                      <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$regiao?>">
                      <input type="submit" name="enviar4" id="enviar4" value="Gravar">
                    </label></td>
                  </tr>
                </table></td>
            </tr>
          </table>
          </form>
            <br>
            <br>
            <table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="5" align="center" valign="middle" bgcolor="#CC0000" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36">HIST&Oacute;RICO DE MULTAS<br>
                  </div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td align="center" valign="middle" bgcolor="#CCFFCC">Modelo</td>
                <td align="center" valign="middle" bgcolor="#CCFFCC">Placa</td>
                <td align="center" valign="middle" bgcolor="#CCFFCC">Tipo Infra&ccedil;&atilde;o</td>
                <td align="center" valign="middle" bgcolor="#CCFFCC">Localiza&ccedil;&atilde;o da Infra&ccedil;&atilde;o</td>
                <td align="center" valign="middle" bgcolor="#CCFFCC">Real Infrator</td>
              </tr>
               <?php
              $cont = 0;
			  $REMultas = mysql_query("SELECT * FROM fr_multa WHERE id_user = '$id_user'");
			  while($RowMulta = mysql_fetch_array($REMultas)){
			  
			  $RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$RowMulta[id_carro]'");
			  $RowCarros = mysql_fetch_array($RE_carros);
			  
			  $REFun = mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$RowMulta[id_user]'");
			  $RowFun = mysql_fetch_array($REFun);
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[marca] $RowCarros[modelo]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowCarros[placa]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowMulta[tipo]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowMulta[local]</td>
				<td align='center' valign='middle' bgcolor=$color>$RowFun[nome1]</td>
              </tr>";
			  $cont ++;
			  }
              ?>
            </table>
            
            <br>
            <br></td>
        </tr>
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="../layout/baixo.gif" alt="img" width="750" height="38">
            <div align="center" class="style6"></div></td>
        </tr>
      </table>
<p>&nbsp;</p></td>
  </tr>
</table>
</body>
</html>
