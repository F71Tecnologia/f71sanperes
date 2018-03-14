<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

if(empty($_REQUEST['id'])){

// Convertendo Data
function ConverteData($Data) {

   if(strstr($Data, '/')) {
	   $data = implode('-', array_reverse(explode('/', substr($Data,0,10)))).substr($Data,10,9);
   } elseif(strstr($Data, '-')) {
	   $data = implode('/', array_reverse(explode('-', substr($Data,0,10)))).substr($Data,10,9);
   }
   
   return $data;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];

//aqui vamos cadsatrar o pedido de reembolso
if(!empty($_REQUEST['valor'])){
	
	$regiao = $_REQUEST['regiao'];
	$funcionario = $_REQUEST['funcionario'];
	$user = $_REQUEST['user'];
	$nome = $_REQUEST['nome'];
	$valor = $_REQUEST['valor'];
	$descricao = $_REQUEST['descricao'];
	$agencia = $_REQUEST['agencia'];
	$conta = $_REQUEST['conta'];
	$nomefavo = $_REQUEST['nomefavo'];
	$cpf = $_REQUEST['cpf'];
	$banco = $_REQUEST['banco'];
	$dataT = ConverteData($_REQUEST['data']);
	
	$data_cad = date('Y-m-d');
	
	$valorF = str_replace(".","",$valor);
	$valorF = str_replace(",",".",$valorF);
	
	mysql_query("INSERT INTO fr_reembolso (id_regiao,id_usercad,data_cad,funcionario,id_user,nome,valor,data,descricao,banco,agencia,conta,favorecido,cpf)
	VALUES 
	('$regiao','$id_user','$data_cad', '$funcionario', '$user','$nome', '$valorF', '$dataT', '$descricao', '$banco', '$agencia', '$conta', '$nomefavo', '$cpf')");
	
	$reembolso = mysql_insert_id();
	
	print "<script>
	location.href = 'reembolsoprint.php?id=$reembolso';
	</script>";
	
exit;
}

$data = date('d/m/Y H:i:s');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao' and id_master = '$master'");
$row_local = mysql_fetch_array($result_local);

//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
$master = $row_user['id_master'];
$regiao = $row_user['id_regiao'];
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
<title>:: Intranet :: Reembolso</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">

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
function ajaxFunction(a){
var xmlHttp;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function() {
    document.getElementById('mostraki').innerHTML="Aguarde...";
	if(xmlHttp.readyState==4){
	      //document.all.ttdiv.value=xmlHttp.responseText;
		  document.getElementById('mostraki').innerHTML=xmlHttp.responseText;
     }
  }
  
  if(a==1){
	  var enviando = escape(document.getElementById('user').value);
  }else{
	  var enviando = "";
  }
  xmlHttp.open("GET",'reembolso.php?funcionario=' + enviando + '&id=1',true);
  xmlHttp.send(null);
  
  }
 
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
          <td width="21" rowspan="2" background="../layout/esquerdo.gif">&nbsp;</td>
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif">
          
          
          <table  height="141" width="95%" align="center" cellspacing="0" class="bordaescura1px">
            <tr>
            
          <td ><span style="float:right; margin-right:15px"> <?php include('../reportar_erro.php'); ?> </span>
    <span style="clear:right"></span></td>
       
        </tr>
              <td height="23" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">REEMBOLSO<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td height="116" align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>

<table width="90%" border="0" cellspacing="0" cellpadding="0">
  <tr class="campotexto">
    <td width="46%" height="112" align="center" valign="middle"><img src="../imagens/reembolso.gif" border="0" style="cursor:pointer"
      onClick="document.all.cad.style.display = (document.all.cad.style.display == 'none') ? '' : 'none' ;"><br>
      SOLICITAR REEMBOLSO</td>
    <td width="54%" align="center" valign="middle"><img src="../imagens/reembolso2.gif" border="0" style="cursor:pointer"
      onClick="document.all.ver.style.display = (document.all.ver.style.display == 'none') ? '' : 'none' ;"><br>
      ACOMPANHAMENTO DE REEMBOLSO</td>
  </tr>
  </table></td>
            </tr>
          </table></td>
          <td width="26" rowspan="2" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="../layout/baixo.gif" width="750" height="38">
            <div align="center" class="style6"><br>
          </div></td>
        </tr>
      </table>
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='cad' style="display:" >
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
          <form action="reembolso.php" method="post" name="form1" onSubmit="return validaForm1()">
          <table  height="303" width="95%" align="center" cellspacing="0" class="bordaescura1px">
            <tr>
              <td height="28" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">SOLICITA&Ccedil;&Atilde;O DE REEMBOLSO</div>
              </div></td>
            </tr>
            <tr>
              <td height="273" align="center"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
                
                <table width="95%" border="0" cellpadding="0" cellspacing="1" bgcolor="#999999" class="bordaescura1px">
                  <tr class="campotexto">
                    <td width="150" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Funcion&aacute;rio:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">
                    &nbsp;&nbsp;
                    <label>
                    <input type="radio" name="funcionario" id="funcionario" value="1"  
                    onClick="document.all.nome.style.display = 'none'; document.all.user.style.display = '';"> Sim
                    </label>
                    &nbsp;&nbsp;&nbsp;
                    <label>
                    <input type="radio" name="funcionario" id="funcionario" value="2"
                     onClick="document.all.user.style.display = 'none'; document.all.nome.style.display = ''; ajaxFunction(2)"> Não
                    </label>
                    </td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Nome / Raz&atilde;o:&nbsp;</td>
                    <td height="28" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <select name="user" class="campotexto" id="user" onChange="ajaxFunction(1)" style="display:none">
                      <?php
                      $REFunc = mysql_query("SELECT id_funcionario,nome1 FROM funcionario WHERE status_reg = '1' ORDER BY nome1");
					  print "<option>Selecione</option>";
					  while($RowFunc = mysql_fetch_array($REFunc)){
						  print "<option value='$RowFunc[0]'>$RowFunc[nome1]</option>";
					  }
                      ?>
                      </select><input name="nome" type="text" class="campotexto" id="nome" size="25" style="display:none"></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="28" align="right" valign="middle" bgcolor="#CCFFCC">Valor:&nbsp;</td>
                    <td width="204" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name="valor" type="text" class="campotexto" id="valor" size="13" OnKeyDown="FormataValor(this,event,17,2)"></td>
                    <td width="98" height="28" align="right" valign="middle" bgcolor="#CCFFCC">Data:&nbsp;</td>
                    <td width="172" height="28" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                      <input name="data" type="text" class="campotexto" id="data3" size="20"  value="<?=$data?>"
                      onKeyUp="mascara_data(this); pula(10, this.id, descricao.id)" maxlength="19"
                       ></td>
                  </tr>
                  <tr class="campotexto">
                    <td height="82" align="right" valign="middle" bgcolor="#CCFFCC">Descri&ccedil;&atilde;o
                      :&nbsp;</td>
                    <td height="82" colspan="3" align="left" valign="middle" bgcolor="#CCCCCC">&nbsp;&nbsp;
                    <textarea name="descricao" cols="40" rows="4" id="descricao" onChange="this.value=this.value.toUpperCase()"></textarea>
                    
                    </td>
                    </tr>
                  <tr class="campotexto">
                    <td height="28" colspan="4" align="center" valign="middle" bgcolor="#666666"><div align="right" class="style35">
                        <div align="center" class="style27 style36">Dados banc&aacute;rios para o Dep&oacute;sito</div>
                      </div></td>
                    </tr>
                  <tr class="campotexto">
                    <td height="13" colspan="4" align="center" valign="top" bgcolor="#FFFFFF">
                    <div id="mostraki"></div></td>
                    </tr>
                  <tr class="campotexto">
                    <td height="35" colspan="4" align="center" valign="middle" bgcolor="#CCFFCC">
                    <input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>">
                    <input type="submit" name="enviar" id="enviar" value="Gravar">
                      
                      <script language="javascript">
                      function validaForm1(){
                        d = document.form1;

						if (d.funcionario[1].checked == true && d.nome.value == ""){
							alert("O campo Nome deve ser preenchido!");
                        	d.nome.focus();
                            return false;
						}
                        if (d.valor.value == ""){
                        	alert("O campo Valor deve ser preenchido!");
                        	d.valor.focus();
                            return false;
                        }
						if (d.descricao.value == ""){
                        	alert("O campo Descrição deve ser preenchido!");
                        	d.descricao.focus();
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
      
      <table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="ver" style="display:">
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
          <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif"><table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" class="bordaescura1px">
              <tr class="campotexto">
                <td height="26" colspan="6" align="center" valign="middle" bgcolor="#666666" class="style7"><div align="right" class="style35">
                  <div align="center" class="style27 style36">ACOMPANHAMENTO DE REEMBOLSO<br>
                  </div>
                </div></td>
              </tr>
              <tr class="campotexto">
                <td width="10%" align="center" valign="middle" bgcolor="#CCFFCC">COD</td>
                <td width="46%" align="center" valign="middle" bgcolor="#CCFFCC">Nome</td>
                <td width="21%" align="center" valign="middle" bgcolor="#CCFFCC">Valor</td>
                <td width="13%" align="center" valign="middle" bgcolor="#CCFFCC">Data</td>
                <td width="10%" align="center" valign="middle" bgcolor="#CCFFCC">Status</td>
              </tr>
             <?php
              $cont = 0;
			  $RE_ree = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') as data FROM fr_reembolso WHERE id_usercad = '$id_user'");
			  while($RowRee = mysql_fetch_array($RE_ree)){
			  
			  $codigo = sprintf("%05d",$RowRee['0']);
			  
			  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
			  		  
			  if($RowRee['status'] == 1){
			  	$img = "<img src='../suporte/imgsuporte/respondido.png' alt='Aguardando'>";
				$cod = "<a href='reembolsoprint.php?id=$RowRee[0]' target='_blank'>$codigo</a>";
			  }elseif($RowRee['status'] == 2){
			  	$img = "<img src='../suporte/imgsuporte/aberto.png' alt='Aprovado' border='0'>";
				$cod = "$codigo";
			  }elseif($RowRee['status'] == 0){
			  	$img = "<img src='../suporte/imgsuporte/finalizado.png' alt='Recusado'>";
				$cod = "$codigo";
			  }
			  
			  if($RowRee['funcionario'] == "1"){
				  $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowRee[id_user]'");
				  $row_user = mysql_fetch_array($result_user);
				  $NOME = $row_user['nome1'];  
			  }else{
				  $NOME = $RowRee['nome']; 
			  }
			  
			  print"
              <tr class='campotexto'>
                <td align='center' valign='middle' bgcolor=$color>$cod</td>
                <td align='center' valign='middle' bgcolor=$color>$NOME</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRee[valor]</td>
                <td align='center' valign='middle' bgcolor=$color>$RowRee[data]</td>
				<td align='center' valign='middle' bgcolor=$color>$img</td>
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
        <tr>
          <td width="155">&nbsp;</td>
          <td width="549">&nbsp;</td>
        </tr>
        <tr valign="top">
          <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="../layout/baixo.gif" alt="img" width="750" height="38">
          <div align="center" class="style6"></div></td>
        </tr>
      </table>
    <?php
	}else{
	include "../conn.php";
    
	mysql_query ('SET character_set_client=utf8');
	mysql_query ('SET character_set_connection=utf8');
	mysql_query ('SET character_set_results=utf8');
	
	$funcionario = $_REQUEST['funcionario'];
    
	$REAuto = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$funcionario'");
	$RowAut = mysql_fetch_array($REAuto);
	
    print "
    <table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#FFFFFF'>
      <tr bgcolor='#999999' class='campotexto'>
        <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Banco:&nbsp;</td>
        <td height='28' colspan='3' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
          <input name='banco' type='text' class='campotexto' id='banco' size='25' value='$RowAut[banco]' 
                      onChange='this.value=this.value.toUpperCase()'></td>
      </tr>
      <tr bgcolor='#999999' class='campotexto'>
        <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Agencia:&nbsp;</td>
        <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
          <input name='agencia' type='text' class='campotexto' id='agencia' size='7' value='$RowAut[agencia]'></td>
        <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Conta:&nbsp;</td>
        <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
          <input name='conta' type='text' class='campotexto' id='conta' size='12' value='$RowAut[conta]'></td>
      </tr>
      <tr bgcolor='#999999' class='campotexto'>
        <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>Nome Favorecido:&nbsp;</td>
        <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
          <input name='nomefavo' type='text' class='campotexto' id='nomefavo' size='25' value='$RowAut[nome]' 
          onChange='this.value=this.value.toUpperCase()'></td>
        <td height='28' align='right' valign='middle' bgcolor='#CCFFCC'>CPF / CNPJ:&nbsp;</td>
        <td height='28' align='left' valign='middle' bgcolor='#CCCCCC'>&nbsp;&nbsp;
          <input name='cpf' type='text' class='campotexto' id='cpf' size='15' value='$RowAut[cpf]'></td>
      </tr>
    </table>";
    
	}
	?>
    </td>
  </tr>
</table>
</body>
</html>
