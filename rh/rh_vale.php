<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

if(empty($_REQUEST['nome'])){


echo "<script language=\"JavaScript\">
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
situacao = \"\";  
// verifica o dia valido para cada mes  
if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
situacao = \"falsa\";  
}  
// verifica se o mes e valido  
if (mes < 01 || mes > 12 ) {  
situacao = \"falsa\";  
}  
// verifica se e ano bissexto  
if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
situacao = \"falsa\";  
}  
if (d.value == \"\") {  
situacao = \"falsa\";  
}  
if (situacao == \"falsa\") {  
alert(\"Data digitada é inválida, digite novamente!\"); 
d.value = \"\";  
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
vr    = vr.replace( \"/\", \"\" );
vr    = vr.replace( \"/\", \"\" );
vr    = vr.replace( \",\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
vr    = vr.replace( \".\", \"\" );
tam    = vr.length;
if (tam < tammax && tecla != 8)
{
tam = vr.length + 1 ;
}
if ((tecla == 8) && (tam > 1))
{
tam = tam - 1 ;
vr = objeto.value;
vr = vr.replace( \"/\", \"\" );
vr = vr.replace( \"/\", \"\" );
vr = vr.replace( \",\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
vr = vr.replace( \".\", \"\" );
}
//Cálculo para casas decimais setadas por parametro
if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 )
{
if (decimais > 0)
{
if ( (tam <= decimais) )
{ 
objeto.value = (\"0,\" + vr) ;
}
if( (tam == (decimais + 1)) && (tecla == 8))
{
objeto.value = vr.substr( 0, (tam - decimais)) + ',' + vr.substr( tam - (decimais), tam ) ;    
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) == \"0\"))
{
objeto.value = vr.substr( 1, (tam - (decimais+1))) + ',' + vr.substr( tam - (decimais), tam ) ;
}
if ( (tam > (decimais + 1)) && (tam <= (decimais + 3)) &&  ((vr.substr(0,1)) != \"0\"))
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
</script>";
?>
<? //Segunda parte do script, recebendo os dados para gerar os arquivos

//$RELATORIO = $_REQUEST['relatorio'];

$DATA_INI = $_REQUEST['data_ini'];
$DATA_FINAL = $_REQUEST['data_final'];
$MES = $_REQUEST['mes'];
$STATUS = $_REQUEST['status'];
$REGIAO = $_REQUEST['regiao'];
/*
if($STATUS == 'CRIAR'){

	$data_ini=explode("/",$DATA_INI);
	$d_ini = $data_ini[0];
    $m_ini = $data_ini[1];
	$a_ini = $data_ini[2];
	
	$data_ini_MYSQL = $a_ini.'-'.$m_ini.'-'.$d_ini; 
	
	$data_final=explode("/",$DATA_FINAL);
	$d_final = $data_final[0];
    $m_final = $data_final[1];
	$a_final = $data_final[2];
	
	$data_final_MYSQL = $a_final.'-'.$m_final.'-'.$d_final; 
	
	$ANO = date('Y');

	//Analiza se o protocolo já do mês já foi cadastrado.
	$result = mysql_query("SELECT * FROM rh_vale_protocolo WHERE id_reg = '$REGIAO' AND mes='$MES' AND ano='$ANO'");
	
	//Caso a QUERY acima exista no banco de dados, a vaiável $num_row_verifica terá valor 0.
	$num_row_verifica = mysql_num_rows($result);
	if($num_row_verifica == 0){
		mysql_query("INSERT rh_vale_protocolo SET id_reg='$REGIAO', mes='$MES',ano='$ANO', data_ini='$data_ini_MYSQL', data_fim='$data_final_MYSQL', user='$id_user', data=CURDATE()");
	}else{		
			$result = mysql_query("SELECT * FROM ano_meses WHERE num_mes='$MES'");
			$row = mysql_fetch_array($result);
			echo "<script> alert( 'O mês de $row[nome_mes] não pode ser gerado novamente!'); </script>";
	}
}
*/
$ANO = date('Y');
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

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
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
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <td height="24" colspan="2" background="imagens/fundo_cima.gif" bgcolor="#FFFFFF"><table  height="114" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="45" bgcolor="#333333"><div align="right" class="style35">
                <div align="center" class="style27 style36">RELAT&Oacute;RIOS<br>
                </div>
              </div></td>
            </tr>
            <tr>
              <td align="center" bgcolor="#FFFFFF"><span class="style40">
                <label> </label>
                </span>
                <label> </label>
                <span class="style40"><strong>
                  <label></label>
                  </strong></span>
<script language="javascript">
function validaForm(){
           d = document.form1;

           if (d.nome.value == ""){
                     alert("O campo Nome deve ser preenchido!");
                     d.nome.focus();
                     return false;
          }

		return true;   }

            </script>
  <?
  include("../empresa.php");
  $imgCNPJ = new empresa();
  $imgCNPJ -> imagemCNPJ()
  ?>
  <br>
  <span class="style2">Gerar Protocolo de Entrega de Vales - Transporte</span></p>
         <form action="acoes.php" name="form1" method="post">
                <input type="hidden" name="regiao" value="<?=$regiao?>">
                <p class="linha">Selecione o m&ecirc;s de refer&ecirc;ncia para emiss&atilde;o do relat&oacute;rio
                  <label>
                    <select name="mes" id="mes">
                      <option value="01" selected>JANEIRO</option>
                      <option value="02">FEVEREIRO</option>
                      <option value="03">MAR&Ccedil;O</option>
                      <option value="04">ABRIL</option>
                      <option value="05">MAIO</option>
                      <option value="06">JUNHO</option>
                      <option value="07">JULHO</option>
                      <option value="08">AGOSTO</option>
                      <option value="09">SETEMBRO</option>
                      <option value="10">OUTUBRO</option>
                      <option value="11">NOVEMBRO</option>
                      <option value="12">DEZEMBRO</option>
                    </select>
                  </label>
                </p>
                <p class="linha">Data Inicial:
                  <label>
                    <input name='data_ini' type='text' id='data_ini' size='10' class='campotexto' onKeyUp="mascara_data(this); pula(10, this.id, data_final.id)" maxlength='10' onFocus="document.all.data_ini.style.background='#CCFFCC'" onBlur="document.all.data_ini.style.background='#FFFFFF'" style="background:#FFFFFF">
                  </label>
                   Data Final:
                   <input name='data_final' type='text' id='data_final' size='10' class='campotexto' onKeyUp="mascara_data(this)" maxlength='10' onFocus="document.all.data_final.style.background='#CCFFCC'" onBlur="document.all.data_final.style.background='#FFFFFF'" style="background:#FFFFFF">
                </p>
<p class="linha">
  <label>
    <input type="submit" name="status" id="status" value="CRIAR"/>
    </label>
</form>
                  <br />
                  </p>
                <table width="95%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td align="center" bgcolor="#666666"><strong class="style1">PROTOCOLO GERADOS</strong></td>
                  </tr>
                  <tr>
                   <td align="center" valign="middle" class="campotexto"><span class="style2">
                     <? 

	$ANO = date('Y');
	
	$result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')AS dataF FROM rh_vale_protocolo WHERE ano='$ANO' AND id_reg='$REGIAO' AND status !='IMPRESSO' ORDER BY id_protocolo DESC");
		
		echo '<div style="width=100%">';
		echo '<div style="float:left; width=5%" align=center class=linha></div>';
		echo '<div style="float:left; width=20% " align=center class=linha>MÊS REFERÊNCIA</div>';
		echo '<div style="float:left; width=20% " align=center class=linha>GERADO POR</div>';
		echo '<div style="float:left; width=20%" align=center class=linha>DATA</div>';
		echo '<div style="float:left; width=20%" align=center class=linha></div>';		
		echo '</div>';
		
	while($row_protocolo= mysql_fetch_array($result_protocolo)){
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		$row_usuario= mysql_fetch_array($result_usuario);

		$mes = $row_protocolo['mes'];
		$result_mes=mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes=mysql_fetch_array($result_mes);
		
		$user = $row_protocolo['user'];
		$resultNOME=mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
		$rowNOME=mysql_fetch_array($resultNOME);
		
		echo '<div style="width=100%">';

		echo '<div style="float:left; width=5%; height:40px; line-height:40px" align=left>';
		echo " <a href='acoes.php?acao=removerprotocolo&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]'> <img src='../imagens/deletar_usuario.gif' border='0' alt='Remover Arquivo' onclick = 'javascript:window.location.sleep(1000);javascript:window.location.reload()'> </a>";
		echo '</div>';	


		echo '<div style="float:left; width=20%;height:40px; line-height:40px " align=center>';
		echo $row_mes['nome_mes'];
		echo '</div>';
		
		echo '<div style="float:left; width=20%;height:40px; line-height:40px " align=center>';
		echo $rowNOME['nome1'];
		echo '</div>';		
		
		echo '<div style="float:left; width=20%;height:40px; line-height:40px " align=center>';
		echo $row_protocolo['dataF']; 
		echo '</div>';
		
		
		echo '<div style="float:left; width=20%; height:40px; line-height:40px " align=center>';
		
		echo "<a href='vt/relacaodevales.php?acao=gravar&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]' target='_blank'> <img src='../imagens/file.gif' width='16' height='16' border='0' alt='Gerar Recibo' onclick = 'javascript:window.location.reload()'></a>";

//////////////////////////////////////////////////////////////////////////////////////////
//VERIFICAR SE EXISTEM NOVOS FUNCIONÁRIO CADASTRADO ENTRE O PROTOCOLO PASSADO E O ATUAL//
////////////////////////////////////////////////////////////////////////////////////////
$mes = $MES;
$mes = $mes-01;
$mes = sprintf("%02d", $mes);

$resultDataUltimoProtocolo = mysql_query("SELECT data,id_reg,mes FROM rh_vale_relatorio WHERE id_reg = '$REGIAO' and mes <= '$MES' and mes >= '$mes'");
$rowUltimoProtocolo = mysql_fetch_array($resultDataUltimoProtocolo);
$dataUltimoProtocolo =  $rowUltimoProtocolo['data'];

$resultEntrada=mysql_query("SELECT nome, data_entrada, id_regiao FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_entrada >= '$dataUltimoProtocolo' and data_entrada <= CURDATE() AND transporte != '1';");

$cadastrosNovos = mysql_affected_rows();
/////////////////////////////////////////////////////////////////////////////////
//VERIFICA SE EXISTEM FUNCIOÁRIO EXCLUIDOS ENTRE O PROTOCOLO PASSADO E O ATUAL//
///////////////////////////////////////////////////////////////////////////////
$resultSaida=mysql_query("SELECT nome, data_entrada, id_regiao FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_saida >= '$dataUltimoProtocolo' and data_entrada <= CURDATE() AND transporte = '1';");

$cadastrosExcluidos = mysql_affected_rows();


if (($cadastrosNovos!=0)or($cadastrosExcluidos!=0)){
	echo "<a href='vt/cadastrodevales.php?mes=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]&data=$row_protocolo[data]' target='_blank'> <img src='vt/imagens/informacao.png' width='16' height='16' border='0' alt='Aviso Importante' onclick = 'javascript:window.location.reload()'></a>";	
}
		echo '</div>';
		
	}
	
	
?>
                   </span></td>
                  </tr>
                </table>
                <table width="95%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td align="center" bgcolor="#666666"><strong class="style1">RECIBOS GERADOS</strong></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" class="campotexto"><span class="style2">
                      <? 

	$ANO = date('Y');
	
	$result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')AS dataF FROM rh_vale_relatorio WHERE ano='$ANO' AND id_reg='$REGIAO' AND status='GRAVADO' ORDER BY id_protocolo DESC");
		echo '<div style="width=100%">';		
		echo '<div style="float:left; width=5%" align=center class=linha></div>';
		echo '<div style="float:left; width=20% " align=center class=linha>MÊS REFERÊNCIA</div>';
		echo '<div style="float:left; width=20% " align=center class=linha>GERADO POR</div>';
		echo '<div style="float:left; width=20%" align=center class=linha>DATA</div>';
		echo '<div style="float:left; width=20%" align=center class=linha></div>';		
		echo '</div>';
		
	while($row_protocolo= @mysql_fetch_array($result_protocolo)){
		$user = $row_protocolo['user'];
		$resultNOME=mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
		$rowNOME=mysql_fetch_array($resultNOME);
		//Exibe o nome do usuário que gerou o protocolo baseado no numero do campo user da tabela rh_vale_relatorio
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		
		$row_usuario= mysql_fetch_array($result_usuario);
		//echo '<div style="float:left; width=100%">';
		//Exibe o nome do mes baseado no valor do campo mes da tabela rh_vale_relatorio

		$mes = $row_protocolo['mes'];
		$result_mes=mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes=mysql_fetch_array($result_mes);
		
		echo '<div style="width=100%">';
		echo '<div style="float:left; width=5%;height:40px; line-height:40px " align=center></div>';
		echo '<div style="float:left; width=20%;height:40px; line-height:40px " align=center>';
		echo $row_mes['nome_mes'];
		echo '</div>';
		
		echo '<div style="float:left; width=20%;height:40px; line-height:40px " align=center>';
		echo $rowNOME['nome1'];
		echo '</div>';		
		
		echo '<div style="float:left; width=20%;height:40px; line-height:40px" align=center>';
		echo $row_protocolo['dataF']; 
		echo '</div>';
		
		
		echo '<div style="float:left; width=20%;height:40px; line-height:40px" align=center>';
		echo "<a href='vt/recibo_vale_geral.php?inicialrhvale=0&finalrhvale=20&acao=visualizar&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]&inicial=0' target='_blank'> <img src='../imagens/search.gif' width='16' height='16' border='0' alt='Visualizar Recibo'> </a>";
		echo '</div>';
		echo '</div>';
	}
	
	
?>
                    </span></td>
                  </tr>
                </table>
                <p>&nbsp;</p>
                <p class="linha">&nbsp;</p>
                <table width="95%" border="1" cellpadding="4" cellspacing="0" bordercolor="#CCCCCC">
                  <tr>
                    <td align="center" bgcolor="#666666"><strong class="style1">HIST&Oacute;RIOS DE ARQUIVOS GERADOS</strong></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" class="campotexto"><span class="style2"><!--&lt;data_gerado&gt;
                      <label>
                        <input type="checkbox" name="gerar4" id="gerar4">
                      </label>
                      <label>
                        <input type="submit" name="gerarnovamente" id="gerarnovamente" value="Gerar Novamente" align="absmiddle" />
                      </label>
                    -->
                        <? 
	$result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')AS dataF FROM rh_vale_protocolo JOIN ano_meses WHERE num_mes=mes AND ano='$ANO' AND id_reg=$REGIAO AND status ='IMPRESSO'");
	
	echo '<div style="width=100%">';		
		echo '<div style="float:left; width=5%;  " align=center class=linha></div>';
		echo '<div style="float:left; width=20%; " align=center class=linha>MÊS REFERÊNCIA</div>';
		echo '<div style="float:left; width=20%; " align=center class=linha>GERADO POR</div>';
		echo '<div style="float:left; width=20%; " align=center class=linha>DATA</div>';
		echo '<div style="float:left; width=20%; " align=center class=linha></div>';
	echo '</div>';
	
	while($row_protocolo= mysql_fetch_array($result_protocolo)){
		
		$user = $row_protocolo['user'];
		$resultNOME=mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
		
		$rowNOME=mysql_fetch_array($resultNOME);
		
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		$row_usuario= mysql_fetch_array($result_usuario);
echo '<div style="width=100%">';

		$mes = $row_protocolo['mes'];
		$result_mes=mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes=mysql_fetch_array($result_mes);				
				
		echo '<div style="float:left; width=5%; height:40px; line-height:40px " align=left>';
		echo " <a href='acoes.php?acao=removerarquivo&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]'> <img src='../imagens/deletar_usuario.gif' border='0' alt='Remover Arquivo'> </a> &nbsp;&nbsp;";
		echo '</div>';	

		echo '<div style="float:left; width=20%; height:40px; line-height:40px " align=center>';
		echo $row_mes['nome_mes'];
		echo '</div>';

		echo '<div style="float:left; width=20%; height:40px;  line-height:40px" align=center>';
		echo $rowNOME['nome1']; 
		echo '</div>';

		echo '<div style="float:left; width=20%; height:40px; line-height:40px " align=center>';
		echo $row_protocolo['dataF']; 
		echo '</div>';
		
		
		echo '<div style="float:left; width=30%; height:40px; line-height:40px " align=center>';
		echo "<a href='vt/relacaodevalesview.php?acao=visualizar&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]' target='_blank'> <img src='../imagens/folder_files.gif' width='16' height='16' border='0' alt='Visualizar Arquivo do Histórico' ></a> ";
		
//		echo " <a href='acoes.php?acao=removerarquivo&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]'> <img src='../imagens/deletar_usuario.gif' border='0' alt='Remover Arquivo'> </a>";
		
		//LINK TEMPORÁRIO PARA ATIVAR A DO EMISSÃO DO VALE EXPRESS
//		echo " &nbsp; &nbsp;<a href='vt/vale_express/main.php?acao=gerarvale&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]'>&Omega;</a>";
		switch($regiao){
			case '4';
			echo "&nbsp; &nbsp;<a href='vt/vale_express/main.php?data=$row_protocolo[data]&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]' target='_blank'> <img src='vt/imagens/valeexpress.jpg' border='0' alt='Gerar Vale Express'> </a>";
			break;
			
			case '3';
			echo "&nbsp; &nbsp;<a href='vt/fetranspor/main.php?data=$row_protocolo[data]&id_protocolo=$row_protocolo[id_protocolo]&mes_referencia=$row_mes[num_mes]&regiao=$row_protocolo[id_reg]' target='_blank'> <img src='vt/imagens/riocard.jpg' border='0' alt='Carregar Rio Card'> </a>";
			break;
			
		}
		echo '</div>';
		
	}
	
	
?>
                    </span></td>
                  </tr>
                </table>
                <br></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF"><div align="center"></div></td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#FFFFFF">
          
          <br>
            <table  height="135" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
            <tr>
              <td height="45" bgcolor="#666666"><div align="right" class="style35">
                <div align="center" class="style27 style36">CONCESSION&Aacute;RIAS<br>
                </div>
              </div></td>
              </tr>
            
            <tr>
              <td height="88"><span class="style40">
                <label>                </label>
              </span>                
                <label>                </label>
                <span class="style40"><strong>
                <label></label>
                </strong></span>
<form action="rh_vale.php" name="form1" method="post" enctype='multipart/form-data' 
id="form1" onSubmit="return validaForm1()">
  <table width="100%" border="0" cellpadding="0" cellspacing="1">
    <tr>
      <td width="16%" class="style19"><div align="right"><span class="style40"><strong>Nome</strong>:</span> </div></td>
      <td width="84%" colspan="5"><strong>
        &nbsp;&nbsp;
        <input name="nome" type="text" id="nome" size="80" onFocus="document.all.nome.style.background='#CCFFCC'" 
                      onBlur="document.all.nome.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" 
                      class='campotexto'>
        </strong></td>
    </tr>
                    <tr>
                      <td height="28" class="style19"><div align="right"><span class="style40"><strong><span class="style35">Logo marca</span>:</strong></span></div></td>
                      <td colspan="5"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="16%" class="style35"><label> &nbsp;&nbsp;
                            <input name="logo" type="checkbox" id="foto" onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/>
                            Sim</label></td>
                          <td width="84%">
                            </td>
                          </tr>
                        </table></td>
                    </tr>
                    </table>
<br><div align="center">
  <input type="hidden" value="<?=$regiao?>" name="regiao">
                    <input type="hidden" value="1" name="tipo_cad">
                    <input type="submit" name="gerar" id="gerar" value="GRAVAR">
</div>
                </form>
                
<script language="javascript">
function validaForm1(){
           d = document.form1;

           if (d.nome.value == ""){
                     alert("O campo Nome deve ser preenchido!");
                     d.nome.focus();
                     return false;
          }

		return true;   }
</script>
                
                
                </td>
              </tr>
          </table>
            <br>
            <table  height="68" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
              <tr>
                <td height="45" bgcolor="#666666"><div align="right" class="style35">
                    <div align="center" class="style27 style36">CONCESSION&Aacute;RIAS CADASTRADAS</div>
                </div></td>
              </tr>
              
              <tr>
                <td height="21"><span class="style40">
                  <label> </label>
                  </span>
                    <label> </label>
                    <span class="style40"><strong>
                    <label></label>
                    </strong></span>
<table width="100%" border="0" cellpadding="0" cellspacing="1">
<tr>
<td width="8%" bgcolor="#999999" class="style19"><div align="right" class="style50 style53">
<div align="center">COD</div>
</div></td>
<td width="20%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Nome</strong></div></td>
<td width="20%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Data Cadastro</strong></div></td>
<td width="17%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Cadastrado por</strong></div></td>
<td width="23%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Logo</strong></div></td>
                    </tr>
<?php
$result_conce = mysql_query("SELECT *, date_format(data, '%d-%m-%Y')AS data FROM rh_concessionarias where id_regiao = '$regiao'");
$cont = "0";
while($row_conce = mysql_fetch_array($result_conce)){

if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user_cad = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_conce[id_user]'");
$row_user_cad = mysql_fetch_array($result_user_cad);

if($row_conce['logo'] != ""){
$caminho_logo = "../logo/".$regiao."logo_vale".$row_conce[0].$row_conce['logo'];
$imagem = "<img src='$caminho_logo' width='50' height='50'>";
}else{
$imagem = "Sem LOGO";
}
print"
<tr bgcolor=$color>
<td><div align='center' class='style3'><strong>$row_conce[0]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_conce[nome]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_conce[data]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_user_cad[0]</strong></div></td>
<td><div align='center' class='style3'>$imagem</div></td>
</tr>";
$cont ++;
}

?>
                      </table>
                      
                </td>
              </tr>
            </table>
            <hr color="#003300">
            <table  height="114" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
              <tr>
                <td height="45" bgcolor="#666666"><div align="right" class="style35">
                  <div align="center" class="style27 style36">TARIFAS DE VALE TRANSPORTE<br>
                  </div>
                </div></td>
              </tr>
              <tr>
                <td><span class="style40">
                  <label> </label>
                  </span>
                  <label> </label>
                  <span class="style40"><strong>
                    <label></label>
                  </strong></span>
<form action="rh_vale.php?tipo_cad=2&nome=1" method="post" enctype="multipart/form-data" name='form2' id="form2" onSubmit="return validaForm()">
                    <table width="100%" border="0" cellpadding="0" cellspacing="1">
                      <tr>
                        <td width="15%" class="style19"><div align="right"><span class="style40"><strong>Concessionaria:</strong></span></div></td>
                        <td width="85%" colspan="5">&nbsp;&nbsp;
<select name="concessionaria" id="concessionaria">
<?php
$result_conce2 = mysql_query("SELECT * FROM rh_concessionarias where id_regiao = '$regiao'");
while($row_conce2 = mysql_fetch_array($result_conce2)){
print "<option value=$row_conce2[0]>$row_conce2[nome]</option>";
}
?>
</select></td>
                      </tr>
                      <tr>
                        <td class="style19"><div align="right"><span class="style40"><strong>Tipo:</strong></span></div></td>
                        <td colspan="5">&nbsp;&nbsp;
                          <select name="tipo2" id="tipo2">
                            <option>PAPEL</option>
                            <option>CART&Atilde;O</option>
                        </select></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Valor:</strong></span></div></td>
                        <td colspan="5"><strong>
                          &nbsp;&nbsp;
<input name="valor" type="text" id="valor" size="10" OnKeyDown="FormataValor(this,event,17,2)" class='campotexto'
onFocus="document.all.valor.style.background='#CCFFCC'"  onBlur="document.all.valor.style.background='#FFFFFF'" 
onChange="this.value=this.value.toUpperCase()">
&nbsp;                        </strong></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Itiner&aacute;rio:</strong></span></div></td>
                        <td colspan="5"><strong>&nbsp;&nbsp;
                          <input name="intinerario" type="text" id="intinerario" size="80" 
                          onFocus="document.all.intinerario.style.background='#CCFFCC'" class='campotexto'
                      onBlur="document.all.intinerario.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">
                        </strong></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Descri&ccedil;&atilde;o:</strong></span></div></td>
                        <td colspan="5">&nbsp;&nbsp;
                        <input name="descriao" type="text" id="descriao" size="80" class='campotexto'
                        onFocus="document.all.descriao.style.background='#CCFFCC'" 
                      onBlur="document.all.descriao.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()"></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>C&oacute;digo:</strong></span></div></td>
                        <td colspan="5">&nbsp;&nbsp;<strong>
                          <input name="codigo" type="text" id="codigo" size="10" class='campotexto' onFocus="document.all.codigo.style.background='#CCFFCC'"  onBlur="document.all.codigo.style.background='#FFFFFF'" 
onChange="this.value=this.codigo.toUpperCase()">
                        </strong></td>
                      </tr>
                    </table>
                    <br>
                    <div align="center">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo2">
                        <tr>
                          <td width="15%" align="right"><span class="style19">SELECIONE:</span></td>
                          <td width="85%"><span class="style19"> &nbsp;&nbsp;
                            <input name="arquivo2" type="file" id="arquivo2" size="60" />
                          </span></td>
                        </tr>
                      </table>
                      <input name="regiao" type="hidden" id="regiao" value="<?=$regiao?>">
                      <input type="submit" name="gerar2" id="gerar2" value="GRAVAR TARIFA">
                    </div>
                  </form>
<script language="javascript">
function validaForm(){
           d = document.form2;

           if (d.valor.value == ""){
                     alert("O campo Valor deve ser preenchido!");
                     d.valor.focus();
                     return false;
          }
           if (d.intinerario.value == ""){
                     alert("O campo Itinerário deve ser preenchido!");
                     d.intinerario.focus();
                     return false;
          }

		return true;   }
</script>
                  
                  </td>
              </tr>
            </table>
            <br>
            <table  height="68" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
              <tr>
                <td height="45" bgcolor="#666666"><div align="right" class="style35">
                  <div align="center" class="style27 style36">TARIFAS CADASTRADAS</div>
                </div></td>
              </tr>
              <tr>
                <td height="21"><span class="style40">
                  <label> </label>
                  </span>
                  <label> </label>
                  <span class="style40"><strong>
                    <label></label>
                  </strong></span>
                  <table width="100%" border="0" cellpadding="0" cellspacing="1">
                    <tr>
                      <td width="9%" bgcolor="#999999" class="style19"><div align="right" class="style50 style53">
                        <div align="center">COD</div>
                      </div></td>
                      <td width="10%" bgcolor="#999999" class="style19"><div align="center" class="style53 style27"><strong>Tipo</strong></div></td>
                      <td width="10%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Valor</strong></div></td>
                      <td width="20%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>Itiner&aacute;rio</strong></div></td>
                      <td width="29%" bgcolor="#999999"><div align="center" class="style53 style27"><strong><span class="style53">Descri&ccedil;&atilde;o</span></strong></div>
                        <div align="center"></div></td>
                      <td width="7%" bgcolor="#999999"><div align="center" class="style53 style27"><strong>C&oacute;digo</strong></div>
                        <div align="center"></div></td>                        
                      <td width="15%" bgcolor="#999999"><div align="center" class="style53 style27"><strong><span class="style55">Remover tarifa</span></strong></div></td>
                    </tr>

<?php
$result_tarifa = mysql_query("SELECT * FROM rh_tarifas where id_regiao = '$regiao' and status_reg = '1'");
$cont2 = "0";
while($row_tarifa = mysql_fetch_array($result_tarifa)){

if($cont2 % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$result_user_cad = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_tarifa[id_user]'");
$row_user_cad = mysql_fetch_array($result_user_cad);

//$valor_f = number_format($row_tarifa['valor'],2,",",".");
$valor_f = $row_tarifa['valor'];

print"
<tr bgcolor=$color>
<td><div align='center' class='style3'><strong>$row_tarifa[0]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_tarifa[tipo]</strong></div></td>
<td><div align='center' class='style3'><strong>$valor_f</strong></div></td>
<td><div align='center' class='style3'><strong>$row_tarifa[itinerario]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_tarifa[descricao]</strong></div></td>
<td><div align='center' class='style3'><strong>$row_tarifa[codigo]</strong></div></td>
<td><div align='center' class='style3'><strong><a href='rh_vale.php?tipo_cad=3&regiao=$regiao&tarifa=$row_tarifa[0]&nome=1'>Remover</strong></div></td>
</tr>";
$cont2 ++;
}

?>
                  </table></td>
              </tr>
            </table>
          <p>&nbsp; </p></td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38"> 
            <div align="center" class="style6"><br>
              
<?php
$rod = new empresa();
$rod -> rodape();
?>
            </div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>

<?php

}else{ // AKI VAI RODAR O CADASTRO

$tipo_cad = $_REQUEST['tipo_cad'];

if($tipo_cad == "1"){                  //CADASTRANDO CONCESSIONARIAS

$regiao = $_REQUEST['regiao'];

$nome = $_REQUEST['nome'];
$logo = $_REQUEST['logo'];

$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

if($logo == "1"){    // ----------------- AQUI TEM ARQUIVO -------------------

$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

   if($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo   [type] != "image/jpe") {     //aki a imagem nao corresponde com as extenções especificadas

     print "<center>
     <hr><font size=2><b>
     Tipo de arquivo não permitido: $arquivo[type], os únicos padrões permitidos são (gif - jpg - jpeg - png)<br>
     $arquivo[type] <br><br>
     <a href='rh_vale.php?regiao=$regiao'>Voltar</a>
     </b></font>"; 

   exit; 

 } else {  //aqui o arquivo é realente de imagem e vai ser carregado para o servidor

  $arr_basename = explode(".",$arquivo['name']); 
  $file_type = $arr_basename[1]; 
   
   if($file_type == "gif"){
      $tipo_name =".gif"; 
    }  if($file_type == "jpg" or $arquivo[type] == "jpeg"){
      $tipo_name =".jpg"; 
    }  if($file_type == "png") { 
      $tipo_name =".png"; 
  } 

$logo = $tipo_name;

mysql_query("INSERT INTO rh_concessionarias(id_regiao,id_user,nome,logo,data) values 
('$regiao','$id_user','$nome','$logo','$data_cad')")or die ("<hr>Erro no insert<br><hr>".mysql_error());

$row_id = mysql_insert_id();

	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "logo/";

	$nome_tmp = $regiao."logo_vale".$row_id.$tipo_name;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");


} //aqui fecha o IF que verificar se o arquivo tem a extenção especificada

}else{    //AQUI ESTÁ SEM A LOGO
mysql_query("INSERT INTO rh_concessionarias(id_regiao,id_user,nome,tipo,data) values 
('$regiao','$id_user','$nome','$tipo','$data_cad')")or die ("<hr>Erro no insert<br><hr>".mysql_error());
}

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";


}elseif($tipo_cad == "2"){                  //CADASTRADO VALOR DE VALE

$regiao = $_REQUEST['regiao'];

$concessionaria = $_REQUEST['concessionaria'];
$tipo2 = $_REQUEST['tipo2'];
$valor = $_REQUEST['valor'];
$intinerario = $_REQUEST['intinerario'];
$descriao = $_REQUEST['descriao'];
$codigo = $_REQUEST['codigo'];

$valor = str_replace(".","",$valor);

$id_user = $_COOKIE['logado'];
$data_cad = date('Y-m-d');

mysql_query("INSERT INTO rh_tarifas(tipo,valor,itinerario,descricao,id_concessionaria,id_user,data,id_regiao,codigo) 
values 
('$tipo2','$valor','$intinerario','$descriao','$concessionaria','$id_user','$data_cad','$regiao','$codigo')")or die 
("<hr>Erro no insert<br><hr>".mysql_error());

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";

}elseif($tipo_cad == "3"){  //REMOVER TARIFA

$regiao = $_REQUEST['regiao'];
$tarifa = $_REQUEST['tarifa'];

mysql_query("UPDATE rh_tarifas SET status_reg = '0' where id_tarifas = '$tarifa'");

print "
<script>
alert (\"Informações gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";

}
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);

}

?>
