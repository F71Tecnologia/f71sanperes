<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
} else {

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$qr_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($qr_local);

if(empty($_REQUEST['nome'])) { ?>

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
return false;  // NÃO E VALIDO
}
aux = Telefone_Remove_Format(Campo.value);
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
<?php //Segunda parte do script, recebendo os dados para gerar os arquivos
$DATA_INI = $_REQUEST['data_ini'];
$DATA_FINAL = $_REQUEST['data_final'];
$MES = $_REQUEST['mes'];
$STATUS = $_REQUEST['status'];
$REGIAO = $_REQUEST['regiao'];
$ANO = date('Y');
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
<style type="text/css">
body {
	margin:0px;
}
</style>
<script type="text/javascript">
$(function() {
	$('#data_ini').datepicker({
		changeMonth: true,
	    changeYear: true
	});
	$('#data_final').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});

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
          <td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"></td>
          <td bgcolor="#FFFFFF"></td>
        </tr>
        <tr>
          <td height="24" colspan="2" bgcolor="#FFFFFF">
          <table  height="114" width="95%" align="center" cellspacing="0" class="bordaescura1px">
            <tr>
              <td style="background-color:#666; height:45px; color:#FFF; font-size:15px; font-weight:bold; text-align:center;">
                RELAT&Oacute;RIO DE PROTOCOLOS DE ENTREGA DE VALE-TRANSPORTE
              </td>
            </tr>
            <tr>
              <td align="center" bgcolor="#FFFFFF">
             
	<script language="javascript">
		function validaForm() {
			d = document.form1;
		
			if(d.nome.value == "") {
				alert("O campo Nome deve ser preenchido!");
				d.nome.focus();
				return false;
			}
		
			return true;
		}
    </script>
    
  <?
  include("../empresa.php");
  $imgCNPJ = new empresa();
  $imgCNPJ -> imagemCNPJ()
  ?>
  </p>
  
         <form action="acoes.php" name="form1" method="post">
         <table style="font-weight:bold; margin:20px;" cellpadding="2" cellspacing="2">
         	<tr>
              <td align="right">M&ecirc;s Refer&ecirc;ncia:</td>
              <td>
              	<select name="mes" id="mes">
                    <?php
					$resultMesAno = mysql_query("SELECT * FROM ano_meses");
					while($rowMes = mysql_fetch_array($resultMesAno)) { ?>
						<option value="<?=$rowMes['num_mes']?>" <?php if($rowMes['num_mes'] == date('m')) { echo 'selected'; } ?>><?=$rowMes['nome_mes']?></option>	
					<?php } ?>		
                 </select>
              </td>
            </tr>
            <tr>
              <td align="right">Data Inicial:</td>
              <td>
              	<input name="data_ini" type="text" id="data_ini" size="10" onKeyUp="mascara_data(this); pula(10, this.id, data_final.id)" maxlength="10">
              </td>
            </tr>
            <tr>
              <td align="right">Data Final:</td>
              <td>
              	<input name="data_final" type="text" id="data_final" size="10" onKeyUp="mascara_data(this)" maxlength="10">
              </td>
            </tr>
            <tr>
              <td colspan="2" align="center">
              	<input type="submit" name="status" id="status" value="Gerar Protocolo" />
              </td>
             </tr>
          </table>
          <input type="hidden" name="regiao" value="<?=$regiao?>">
          <input type="hidden" name="status" value="criar">
          </form>

	<?php $ANO = date('Y');
		  $result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS dataF FROM rh_vale_protocolo WHERE id_reg = '$REGIAO' AND status != 'IMPRESSO' AND ano = '$ANO' OR ano = '".($ANO - 1)."' ORDER BY mes ASC");
		  $num_protocolo = mysql_num_rows($result_protocolo); 
		  if(!empty($num_protocolo)) { ?>
                  
    <table width="95%" border="0" cellpadding="4" cellspacing="0" class="bordaescura1px">
       <tr>
          <td style="background-color:#666; color:#FFF; text-align:center; font-weight:bold;">PROTOCOLOS GERADOS</td>
       </tr>
       <tr>
          <td align="center">

	<table style="text-align:center; width:100%;" cellpadding="2" cellspacing="2">
	  <tr style="font-weight:bold;">
		<td width="5%">&nbsp;</td>
        <td width="20%">MÊS REFERÊNCIA</td>
        <td width="20%">GERADO POR</td>
        <td width="20%">DATA</td>
        <td width="30%">&nbsp;</td>
      </tr>
		
  <?php while($row_protocolo = mysql_fetch_array($result_protocolo)) {
		
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		$row_usuario = mysql_fetch_array($result_usuario);

		$mes = $row_protocolo['mes'];
		$result_mes = mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes = mysql_fetch_array($result_mes);
		
		$user = $row_protocolo['user'];
		$resultNOME = mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
		$rowNOME = mysql_fetch_array($resultNOME); ?>
        
      <tr>
		<td><a href="acoes.php?acao=removerprotocolo&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>"><img src="../imagens/deletar_usuario.gif" border="0" alt="Remover Arquivo" onClick="javascript:window.location.sleep(1000);javascript:window.location.reload()"></a></td>
		<td><?=$row_mes['nome_mes'];?></td>
		<td><?=$rowNOME['nome1']?></td>
		<td><?=$row_protocolo['dataF']?></td>
		<td><a href="vt/relacaodevales.php?acao=gravar&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>" target="_blank"><img src="../imagens/file.gif" width="16" height="16" border="0" alt="Gerar Arquivo" onClick="javascript:window.location.reload()"></a>
		
<?php
//////////////////////////////////////////////////////////////////////////////////////////
//VERIFICAR SE EXISTEM NOVOS FUNCIONÁRIO CADASTRADO ENTRE O PROTOCOLO PASSADO E O ATUAL//
////////////////////////////////////////////////////////////////////////////////////////
$ANO = date('Y');
$resultUltimoprotocolo = mysql_query("SELECT data FROM rh_vale_protocolo WHERE ano = '$ANO' AND id_reg = '$REGIAO' AND status ='IMPRESSO'");
$rowUltimoProtocolo01 = mysql_fetch_array($resultUltimoprotocolo);
$dataUltimoProtocolo = $rowUltimoProtocolo01['data'];

$resultEntrada=mysql_query("SELECT id_clt,nome, data_entrada, id_regiao FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_entrada >= '$dataUltimoProtocolo' and data_entrada <= CURDATE() AND status != '62'");

while ($rowEntrada = mysql_fetch_array($resultEntrada)){
	$resultVale = mysql_query("SELECT * FROM rh_vale WHERE id_regiao = '$REGIAO' AND id_clt = '$rowEntrada[id_clt]'");
	while ($rowVale = mysql_fetch_array($resultVale)){
		for ($i=1; $i<=6; $i++){
			$tarifa = $rowVale['id_tarifa'.$i];

			$resultTipo = mysql_query("SELECT id_tarifas,tipo FROM rh_tarifas WHERE id_tarifas = '$tarifa' AND tipo='CARTÃO'");
			$row = mysql_fetch_array($resultTipo);
			if ($row['id_tarifas'] != ''){
				$contador[]=$row['id_tarifas'];
			}
		}
	}
}

$cadastrosNovos = count($contador);
/////////////////////////////////////////////////////////////////////////////////
//VERIFICA SE EXISTEM FUNCIOÁRIO EXCLUIDOS ENTRE O PROTOCOLO PASSADO E O ATUAL//
///////////////////////////////////////////////////////////////////////////////
$resultSaida=mysql_query("SELECT nome, data_entrada, id_regiao FROM rh_clt WHERE id_regiao = '$REGIAO' AND data_saida >= '$dataUltimoProtocolo' AND data_entrada <= CURDATE() AND status != '10'");
$cadastrosExcluidos = mysql_affected_rows();

if(($cadastrosNovos != 0) and ($regiao == '3')) { ?>	
	<a href="vt/cadastrodevales.php?mes=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>&data=<?=$row_protocolo['data']?>" target="_blank"><img src="vt/imagens/informacao.png" width="16" height="16" border="0" alt="Aviso Importante"></a>
<?php } ?>
		</td>
	  </tr>
		
	<?php } ?>
    
                   </table>
                   </td>
                  </tr>
                </table>
   	<p>&nbsp;</p>
                
                <?php } ?>
                
 <?php $ANO = date('Y');
	   $result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS dataF FROM rh_vale_relatorio WHERE id_reg = '$REGIAO' AND status = 'GRAVADO' AND ano = '$ANO' OR ano = '".($ANO - 1)."' ORDER BY mes ASC");
	   $num_protocolo = mysql_num_rows($result_protocolo); 
	   if(!empty($num_protocolo)) { ?>
               
  <table width="95%" border="0" cellpadding="4" cellspacing="0" class="bordaescura1px">
     <tr>
       <td style="background-color:#666; color:#FFF; text-align:center; font-weight:bold;">RECIBOS GERADOS</td>
     </tr>
     <tr>
       <td align="center">
	
	<table style="text-align:center; width:100%;" cellpadding="2" cellspacing="2">
	  <tr style="font-weight:bold;">
		<td width="5%">&nbsp;</td>
        <td width="20%">MÊS REFERÊNCIA</td>
        <td width="20%">GERADO POR</td>
        <td width="20%">DATA</td>
        <td width="30%">&nbsp;</td>
      </tr>
		
	<?php 
	while($row_protocolo = @mysql_fetch_array($result_protocolo)) {
		
		$user = $row_protocolo['user'];
		$resultNOME = mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
		$rowNOME = mysql_fetch_array($resultNOME);
		
		// Exibe o nome do usuário que gerou o protocolo baseado no numero do campo user da tabela rh_vale_relatorio
		$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
		$row_usuario = mysql_fetch_array($result_usuario);

		// Exibe o nome do mes baseado no valor do campo mes da tabela rh_vale_relatorio
		$mes = $row_protocolo['mes'];
		$result_mes = mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
		$row_mes = mysql_fetch_array($result_mes); ?>
		
	  <tr>
		<td>&nbsp;</td>
		<td><?=$row_mes['nome_mes']?></td>
		<td><?=$rowNOME['nome1']?></td>
		<td><?=$row_protocolo['dataF']?></td>
		<td>
            <a href="vt/recibo_vale_geral.php?inicialrhvale=0&finalrhvale=20&acao=visualizar&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>&inicial=0" target="_blank"><img src="../imagens/search.gif" width="16" height="16" border="0" alt="Visualizar Recibo"></a>
		</td>
	  </tr>

		<?php } ?>
    
   </table>
   
      </td>
     </tr>
   </table>
                
      <p>&nbsp;</p>
      <?php } ?>
       
      <?php 
     $result_protocolo = mysql_query("SELECT *, date_format(data, '%d/%m/%Y')AS dataF FROM rh_vale_protocolo JOIN ano_meses WHERE num_mes = mes AND ano = '$ANO' AND id_reg = '$REGIAO' AND status = 'IMPRESSO' ORDER BY mes ASC");
		    $num_protocolo = mysql_num_rows($result_protocolo); 
		  
                    if(!empty($num_protocolo)) { ?>
               
      <table width="95%" cellpadding="4" cellspacing="0" class="bordaescura1px">
        <tr>
           <td style="background-color:#666; color:#FFF; text-align:center; font-weight:bold;">HIST&Oacute;RICO DE ARQUIVOS GERADOS</td>
        </tr>
         <tr>
           <td align="center">
               
	<table style="text-align:center; width:100%;" cellpadding="1" cellspacing="1">
	  <tr style="font-weight:bold;">
	<td width="5%">&nbsp;</td>
        <td width="20%">MÊS REFERÊNCIA</td>
        <td width="20%">GERADO POR</td>
        <td width="20%">DATA</td>
        <td width="30%">&nbsp;</td>
      </tr>
	
<?php while($row_protocolo= mysql_fetch_array($result_protocolo)) {
		
			$user = $row_protocolo['user'];
			$resultNOME = mysql_query("SELECT * FROM funcionario  where id_funcionario = '$user'") or die(mysql_error());
			$rowNOME = mysql_fetch_array($resultNOME);
			
			$result_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_protocolo[user]'");
			$row_usuario = mysql_fetch_array($result_usuario);
	
			$mes = $row_protocolo['mes'];
			$result_mes = mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
			$row_mes = mysql_fetch_array($result_mes); ?>			
				
	  <tr>
		<td>
            <a href="acoes.php?acao=removerarquivo&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>">
                <img src="../imagens/deletar_usuario.gif" border="0" alt="Remover Arquivo">
            </a>
		</td>
		<td><?=$row_mes['nome_mes']?></td>
		<td><?=$rowNOME['nome1']?></td>
		<td><?=$row_protocolo['dataF']?></td>
		<td align="center">
            <a href="vt/relacaodevalesview.php?acao=visualizar&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>" target="_blank"><img src="../imagens/folder_files.gif" width="16" height="16" border="0" alt="Visualizar Arquivo do Histórico"></a><br>

     
            <a href="vt/vale_express/main.php?data=<?=$row_protocolo['data']?>&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>" target="_blank"><img src="vt/imagens/valeexpress.jpg" border="0" alt="Gerar Vale Express"></a><br>
	
            <a href="vt/fetranspor/main.php?data=<?=$row_protocolo['data']?>&id_protocolo=<?=$row_protocolo['id_protocolo']?>&mes_referencia=<?=$row_mes['num_mes']?>&regiao=<?=$row_protocolo['id_reg']?>" target="_blank"><img src="vt/imagens/riocard.jpg" border="0" alt="Carregar Rio Card"></a><br>
    
		</td>
		  </tr>
		
		<?php } ?>
    
    		</table>
    
                    </td>
                  </tr>
                </table>
                
                <?php } ?>
                <br>
              </td>
            </tr>
          </table>
          
          </td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>

<?php

} else { // AKI VAI RODAR O CADASTRO

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
}
?>