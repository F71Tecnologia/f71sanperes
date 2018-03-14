<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

if(!empty($_REQUEST['del'])){
	
	$sql = "DELETE FROM fr_combustivel WHERE id_combustivel = '".$_REQUEST['del']."' LIMIT 1";
	mysql_query($sql);
	
	print "<script>
	location.href = 'frota.php?regiao=$regiao';
	</script>";
	
	exit;
}

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao' and id_master = '$master'");
$row_local = mysql_fetch_array($result_local);

//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
$master = $row_user['id_master'];
//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO

//CRIANDO UM ARRAY COM TODAS AS REGIÕES COM SEUS RESPECTIVOS ID's
$REReg = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE status = 1 and id_master = '$master'");
while ($row_regiao = mysql_fetch_array($REReg)){
	$idReg = $row_regiao['0'];
	$REGIOES[$idReg] = $row_regiao['1'];
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
</script>

<style type="text/css">
<!--
.dragme{position:relative;}

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
-->
</style>
<script language="javascript">

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
</script>

<script type="text/javascript">
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
    <td align="center" valign="top"><table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='cad' style="display:none" >
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
			  $RE_carros = mysql_query("SELECT * FROM fr_carro");
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
      
      <table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        
        <tr>
          <td width="994" height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          <br>
          <table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#000000">
              <tr class="campotexto">
                <td height="26" colspan="8" align="center" valign="middle" bgcolor="#00CC66" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36">HIST&Oacute;RICO DE SOLICITA&Ccedil;&Otilde;ES<br>
                  </div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="16%" align="center" valign="middle" bgcolor="#CCFFCC">Ve&iacute;culo</td>
                <td width="10%" align="center" valign="middle" bgcolor="#CCFFCC">Placa</td>
                <td width="20%" align="center" valign="middle" bgcolor="#CCFFCC">Destino</td>
                <td width="17%" align="center" valign="middle" bgcolor="#CCFFCC">Local de Origem</td>
                <td width="11%" align="center" valign="middle" bgcolor="#CCFFCC">Data</td>
                <td width="11%" align="center" valign="middle" bgcolor="#CCFFCC">Km Atual</td>
                <td width="8%" align="center" valign="middle" bgcolor="#CCFFCC">Status</td>
                <td width="7%" align="center" valign="middle" bgcolor="#CCFFCC">Apagar</td>
              </tr>
             <?php
              $cont = 0;
			  $RE_combustivel = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')as data, date_format(data_libe, '%d/%m/%Y')as data_libe FROM 
			  fr_combustivel WHERE user_cad = '$id_user'");
			
			  
			  
			  while($Row_comb = mysql_fetch_array($RE_combustivel)){
			  
			  $RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$Row_comb[id_carro]'");
			  $RowCarros = mysql_fetch_array($RE_carros);
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  
			  $RR = $Row_comb['id_regiao'];
			  
			  //VERIFICANDO SE O CARRO É INTERNO OU NAO
			   if($Row_comb['interno'] == 2){
				   $CaRRo = "$Row_comb[carro]";
				   $PlAca = "$Row_comb[placa]";
			   }else{
				   $CaRRo = "$RowCarros[marca] $RowCarros[modelo]";
				   $PlAca = "$RowCarros[placa]";
			   }
			  
			  
			  //VERIFICANDO QUAL STATUS ESTA O PEDIDO DE COMBUSTIVEL, E CHAMANDO A RESPECTIVA IMAGEM
			  if($Row_comb['status_reg'] == 1){
			  	$img = "<img src='../suporte/imgsuporte/respondido.png' alt='Aguardando'>";
			  }elseif($Row_comb['status_reg'] == 2){
			  	$img = "
				<a href='#' 
	onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header',headingText: 'Número do Vale' } )\" 
	class='highslide'>
				<img src='../suporte/imgsuporte/aberto.png' alt='Aprovado' border='0'> </a>";
			  }elseif($Row_comb['status_reg'] == 0){
			  	$img = "<img src='../suporte/imgsuporte/finalizado.png' alt='Recusado'>";
			  }
			  
			  $src="<a href='printcombustivel.php?regiao=$RR&com=$Row_comb[0]' target='_blanck'>";
			  
			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$src $CaRRo </a></td>
                <td align='center' valign='middle' bgcolor=$color>$PlAca</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_comb[destino]</td>
                <td align='center' valign='middle' bgcolor=$color>$REGIOES[$RR]</td>
                <td align='center' valign='middle' bgcolor=$color>$Row_comb[data]</td>
				<td align='center' valign='middle' bgcolor=$color>$Row_comb[kmatual]</td>
				<td align='center' valign='middle' bgcolor=$color>
				
				$img
				
				<div class='highslide-maincontent'>
				<table>
				<tr><td align='center'>
				Número do vale: $Row_comb[numero] &nbsp;&nbsp;&nbsp; Data de Liberação: $Row_comb[data_libe]
				</td></tr></table>
				</div>
				
				</td>
				
				<td align='center' valign='middle' bgcolor=$color>
				<a href='historico.php?del=$Row_comb[0]&regiao=$regiao' style='text-decoration:none;'> Deletar </a></td>
				
              </tr>";
			  $cont ++;
			  }
              ?>
            </table>
            <br>
            <br>
            <table width="95%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <td align="center">
                <div style="width:140px; font-family:arial; color:#999; font-size:11px">
                <img src="../suporte/imgsuporte/respondido.png" alt="" width="18" height="18" align="absmiddle"> aguardando aprova&ccedil;&atilde;o 
                </div></td>
                <td align="center">
                <div style="width:140px; font-family:arial; color:#999; font-size:11px">
                <img src="../suporte/imgsuporte/aberto.png" alt="" width="18" height="18" align="absmiddle"> aprovado 
                </div></td>
                <td align="center">
                <div style="width:140px; font-family:arial; color:#999; font-size:11px">
                <img src="../suporte/imgsuporte/finalizado.png" alt="" width="18" height="18" align="absmiddle"> recusado 
                </div></td>
              </tr>
            </table>
<br>
<br>
            <br></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
