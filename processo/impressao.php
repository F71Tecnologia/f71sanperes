<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}
include "../conn.php";
include "../funcoes.php";
if(empty($_REQUEST['editar'])){
$regiao = $_REQUEST['regiao'];
$id_prestador = $_REQUEST['prestador'];
$id_projeto = $_REQUEST['projeto'];
/*
if(!empty($_REQUEST['parcela'])){
	$valor = $_REQUEST['valor'];
	$data = $_REQUEST['data'];
	$documento = $_REQUEST['documento'];
	$valor = str_replace(".","", $valor);
	
	/* 
	Função para converter a data
	De formato nacional para formato americano.
	Muito útil para você inserir data no mysql e visualizar depois data do mysql.
	
	
	function ConverteData($Data){
		if (strstr($Data, "/")){//verifica se tem a barra /
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
	
	$data_f = ConverteData($data);
	
	$result_cont = mysql_query("SELECT id_pg FROM prestador_pg where id_prestador = '$id_prestador'") 
	or die ("Erro<br>".mysql_error());
	
	$row_cont = mysql_num_rows($result_cont);
	
	$parcela = $row_cont + 1;
	
	mysql_query("INSERT INTO prestador_pg(id_prestador,id_regiao,valor,data,documento,parcela) 
	values 
	('$id_prestador','$regiao','$valor','$data_f','$documento','$parcela')") or die ("Erro<br>".mysql_error());
	
	print "
	<script>
	location.href=\"impressao.php?prestador=$id_prestador&id=1&regiao=$regiao\";
	</script>";
}
*/
$result_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$id_prestador'");
$row_prestador = mysql_fetch_array($result_prestador);
$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);
$data_hoje = date('d/m/Y');
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="form.css" rel="stylesheet" type="text/css">
<link href="folha.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
margin-left: 0px;
margin-top: 0px;
margin-right: 0px;
margin-bottom: 0px;
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
.style29 {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 11px;
}
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}
.style291 {
font-family: Arial, Verdana, Helvetica, sans-serif;
font-weight: bold;
}
-->
</style>
<?php
print "<script language=\"JavaScript\">
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
</script>
<style type='text/css'>
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
}
.style3 {font-size: 12px}
.style6 {color: #003300}
.style7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style37 {font-family: Arial, Helvetica, sans-serif}
.style39 {font-family: Arial, Helvetica, sans-serif; color: #003300;}
.style40 {font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
.style41 {
	color: #FFFFFF;
	font-size: 11px;
}
.style42 {font-weight: bold; color: #003300; font-family: Arial, Helvetica, sans-serif;}
.style43 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 11px; }
.style44 {font-family: Arial, Helvetica, sans-serif; color: #003300; font-size: 14px; }
.style45 {font-size: 11px}
.style46 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style47 {
	font-size: 11px;
	color: #FF0000;
}
.style48 {
	font-size: 8px;
	color: #FF0000;
}
.style49 {font-size: 9px}
-->
</style>";
?>
</head>
<body>
<div style="margin-left:650px;margin-top:20px;"> <?php include('../reportar_erro.php'); ?> </div>


<table width="101%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top"> 
<table width="750" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="4" bgcolor="#FFFFFF"><img src="../layout/topo.gif" alt="s" width="750" height="38"></td>
  </tr>
  <tr>
    <td width="20" rowspan="6" background="../layout/esquerdo.gif">&nbsp;</td>
    <td width="386" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="318" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="6" background="../layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF" class="totais"><p><span class="submit-go">GERENCIAMENTO DE PRESTADORES DE SERVI&Ccedil;O</span><br>
      <br>
      <span class="style29">(Selecione a &aacute;rea que deseja visualizar, Clique para Exibir, clique novamente para Ocultar)</span><br>
        <br>
        <br>
      </p></td>
    </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF"></td>
    </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><table width="700" border="0" align="center" class="totais" style="size:1px">
      <tr>
        <td width="61%" height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Processo:</td>
         <td width="39%" align="left" valign="middle" bgcolor="#FFFFFF"><a href="#">&nbsp;&nbsp;<img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" onClick="document.all.processo.style.display = (document.all.processo.style.display == 'none') ? '' : 'none' ;" ></a> </td>  
      </tr>
      <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Processo Avan&ccedil;ado:</td>
        <td align="left" valign="middle" bgcolor="#FFFFFF"><a href="#" onClick="javascript:document.all.avancado.style.display = (document.all.avancado.style.display == 'none') ? '' : 'none' ;">
        &nbsp;&nbsp;<img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" 
         ></a></td>
      </tr>
      <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Pagamentos:</td>
        <td align="left" valign="middle" bgcolor="#FFFFFF"><a href="#" onClick="javascript:document.all.pagamentos.style.display = (document.all.pagamentos.style.display == 'none') ? '' : 'none' ;">
        &nbsp;&nbsp;<img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" 
         ></a></td>
      </tr>
       <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Ficha Financeira</td>
        <td align="left" valign="middle" bgcolor="#FFFFFF"><a href="#" onClick="javascript:document.all.financeiro.style.display = (document.all.financeiro.style.display == 'none') ? '' : 'none' ;">
        &nbsp;&nbsp;<img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" 
         ></a></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><p align="center"><br>
    </p></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
    <td bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4"><img src="../layout/baixo.gif" alt="s" width="750" height="38"></td>
  </tr>
</table>
<table width="750" id="processo" border="0" cellpadding="0" cellspacing="0" >
  <tr> 
<td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
</tr>
<tr>
<td width="21" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
</tr>
<tr>
<td colspan="2" bgcolor="#FFFFFF"><div align="center"><strong><strong><span class="style5"><strong>
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?>
<!--<img src="../imagens/certificadosrecebidos.gif" width="105" height="75" align="left">--></strong></span></strong></strong></div>
  <br></td>
</tr>
<tr>
  <td colspan="2" bgcolor="#FFFFFF"><div align="center">
    <p><span style="font-size: 11px; font-family: verdana; color: #000000;"><strong><strong><span class="style5">
      <?php 
		print $row_prestador['c_razao'];
		?>
      </span></strong></strong></span>
      <br>
      <br>
      <span class="submit-go"><strong><strong>CONTROLE DE NOVOS PROCESSOS - 
N&Atilde;O DEIXE DE IMPRIMIR OS PROCEDIMENTOS ABAIXO</strong>:</strong></span><br>
<br>
    </p>
    <table width="700" style="size:1px">
      <tr>
        <td width="65%" height="25" align="right" bgcolor="#EBEBEB" class="style29">Procedimento de Abertura de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td width="35%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="abertura.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank"><img src="imagensprocesso/abertura.gif" alt="abertura" width="190" height="31" border="0"></a></td>
      </tr>
      
   <!--   <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Memorando de Cota&ccedil;&atilde;o de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="memocota.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">  
          <img src="imagensprocesso/memocota.gif" alt="Memorando de cota&ccedil;&otilde;es" width="190" height="31" border="0"></a></td>
        </tr> -->
     <!-- <tr>
        <td height="23" align="right" bgcolor="#EBEBEB" class="style29">Memorando interno para Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="memointerno.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
          <img src="imagensprocesso/memointerno.gif" alt="Gerar Memorando Interno" width="190" height="31" border="0"></a></td>
        </tr>-->

      <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Fechamento de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="encerramento.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
  <img src="imagensprocesso/fechamento.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
        </tr>
      </table>
    <br>
    <br>
  </div></td>
</tr>
<tr>
  <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
  <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr valign="top"> 
<td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38">           </td>
</tr>
</table>




<?php
/*
$id_user = $_COOKIE['logado'];
if($id_user == "1" or $id_user == "5" or $id_user == "89" or $id_user == "34" or $id_user == "46" or  $id_user == "51" or  $id_user == "64" or $id_user == '75' or $id_user == "36" or $id_user == "77" or  $id_user == "64" or  $id_user == "71" or $id_user == "27" or $id_user == "73" or $id_user == "65" or $id_user == "24" or $id_user == "2" or $id_user == "68" or $id_user == '92' or $id_user == '87' or $id_user == '104' or $id_user == '88'  or $id_user == '22' or $id_user == '9' ){
//  if($id_user == "1" or $id_user == "5" or $id_user == "9" or $id_user == "34" or $id_user == "46" or  $id_user == "51"){ */
?>
<table width="750" id="avancado" border="0" cellpadding="0" cellspacing="0" style="display:">
<tr>
<td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
</tr>
<tr>
<td width="20" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
<td width="386" bgcolor="#FFFFFF">&nbsp;</td>
<td width="318" bgcolor="#FFFFFF">&nbsp;</td>
<td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
</tr>
<tr>
<td colspan="2" bgcolor="#FFFFFF"><div align="center"><strong><strong><span class="style5"><strong>
<?php
$img= new empresa();
$img -> imagem();
?><!--<img src="../imagens/certificadosrecebidos.gif" alt="img" width="105" height="75" align="left">--></strong><br>
<br>
</span><strong class="submit-go">CONTROLE DE  PROCESSOS - 
GERENCIAMENTO AVAN&Ccedil;ADO DO PROCESSO</strong><br>
<br>
<span class="style5"><br>
</span><br>
</strong></strong></div></td>
</tr>
<tr>
<td colspan="2" bgcolor="#FFFFFF"><table width="700" align="center" style="size:1px">
  <tr>
    <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Procedimento de Abertura de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
    <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF">
        <?php if($id_projeto == 3331) { ?>
        <a href="contrato_new.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
            <img src="imagensprocesso/gerarcontrato.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0">
        </a>
        <?php } else {?>
            <a href="contrato.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
                <img src="imagensprocesso/gerarcontrato.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0">
            </a>
        <?php }?>
    </td>
  </tr>
    <tr>
    <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Anexo I:</td>
    <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="anexo1.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
      <img src="imagensprocesso/geraranexo.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
  </tr>
    <tr>
    <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Anexo II:</td>
    <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="anexo2.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
      <img src="imagensprocesso/geraranexo.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
  </tr>
    <tr>
    <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Anexo III:</td>
    <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="anexo3.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
      <img src="imagensprocesso/geraranexo.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
  </tr>
    <tr>
    <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Anexo IV:</td>
    <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="anexo4.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
      <img src="imagensprocesso/geraranexo.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
  </tr>
    </table>
  <p align="center"><br>
  </p></td>
</tr>
<tr>
<td bgcolor="#FFFFFF">&nbsp;</td>
<td bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr valign="top">
<td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38">            </td>
</tr>
</table>





  <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
  
  <script>
  $(function(){
	 $('.ano').click(function(){
		 
	ano = $(this).html();
	 
	 
	 if( $('.'+ano).attr("style") == 'display:none;'){
		 
		 $('.'+ano).show();
	 }else{
		 
	 	$('.'+ano).attr("style",'display:none;');
	 }
	 });
	 
	 
	 
  });
  	
  
  
  </script>

<table width="750" id="financeiro" border="0" cellpadding="0" cellspacing="0">


          <tr>
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
          </tr>
          <tr>
          <td width="20" rowspan="5" background="../layout/esquerdo.gif">&nbsp;</td>
          <td width="386" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="318" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="5" background="../layout/direito.gif">&nbsp;</td>
          </tr>
          <tr>
          <td colspan="2" bgcolor="#FFFFFF"><div align="center"><strong><strong><span class="style5"><strong>
          <?php
        $img= new empresa();
        $img -> imagem();
        ?><!--<img src="../imagens/certificadosrecebidos.gif" alt="img" width="105" height="75" align="left">--></strong><br>
          <br>
            </span><span class="submit-go">FICHA FINANCEIRA <br>
            </span><br>
          <br>
          </strong></strong></div></td>
          </tr>
         
         <td colspan="2" bgcolor="#FFFFFF">
         
         
         
         	
         <?php
		
		
		///CALCULA O VALOR TOTAL DAS SAIDAS POR MÊS E ANOS
		
		$meses = array(1 => 'Janeiro', 2=> 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
		
		for($ano = 2005;$ano<=date('Y')+1;$ano++) :
		
		
			 	foreach($meses as $chave => $mes):
			
		 			//Amrmazena  os valores  e os ids das saidas nos arrays
					 $query_pg_total = mysql_query("SELECT saida.id_saida, saida.data_vencimento, saida.valor,saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg,prestador_pg.gerado, prestador_pg.comprovante, saida.comprovante  FROM  prestador_pg INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida WHERE prestador_pg.status_reg = '1' AND prestador_pg.id_prestador = '$id_prestador' AND saida.status != '0'  AND YEAR(saida.data_pg) = '$ano' AND MONTH(saida.data_pg) = '$chave'");
						$linha = mysql_num_rows($query_pg_total);	
						while($row_pg_total = mysql_fetch_array($query_pg_total)):
						
								$valor_total = str_replace(',','.', $row_pg_total['valor']);
								$total_Pg[$ano][$mes] += $valor_total;
								$numero_saida[] = $row_pg_total['id_saida'];
								
						
						endwhile;
					
			endforeach;
		 
		 endfor;
		 
		
	///CALCULA O VALOR TOTAL DAS SAIDAS POR MÊS E ANOS	
	
	if(!empty($numero_saida)){
	
	$ids = implode(',',$numero_saida);
	?>	
	
	<table width="100%" id="tabela">
           
<?php
   //EXIBE OS VALORES                                    
  for($ano=2005;$ano<=date('Y')+1;$ano++) :
						 		
								$ano_verifica =0;
                         
                                 foreach($meses as $chave => $mes):
                                 
								 
								    if($total_Pg[$ano][$mes] != '' and $ano_verifica == 0){
                    
												$select_anos[] = $ano;
												echo '<tr>
														<td colspan="5" style="background-color:#Eff1e9;text-align:center;">
														<a href="" onclick="return false" class="ano">'.$ano.'</a>
														</td>
												</tr> ';				
												
											
												$ano_verifica = 1;
											 }
											 
											 
											 $query_pg_total = mysql_query("SELECT saida.id_saida,saida.nome, saida.especifica, saida.data_vencimento, saida.valor,saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg,prestador_pg.gerado, prestador_pg.comprovante, saida.comprovante  FROM  prestador_pg INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida WHERE saida.id_saida IN($ids)  AND YEAR(saida.data_pg) = $ano AND MONTH(saida.data_pg) = $chave") or die(mysql_error());
													$linha = mysql_num_rows($query_pg_total);
													while($row_pg_total = mysql_fetch_array($query_pg_total)):
													
													$id_saida = $row_pg_total['id_saida'];
													$data_pg  = implode('/',array_reverse(explode('-',$row_pg_total['data_pg'])));
													$valor    = $row_pg_total['valor'];
													
														?>	
															<tr class="<?php echo $ano; ?>" style="display:none;">
                                                            
                                                            <?php if($mes_anterior != $mes){ 
                                                            
																echo '<tr  class="'.$ano.'" style="display:none;"><td style="font-size:12px;background-color:#DEF;color:#000;font-weight:bold;" colspan="5">'.$mes.'</td></tr>';
																
																echo'<tr class="'.$ano.'" style="display:none; background-color:#999;">
																	
																	<td  style="background-color:#999;color:#fff;font-weight:bold;font-size:12px;text-align:center;">Saída</td>
																	<td  style="background-color:#999;color:#fff;font-weight:bold;font-size:12px;text-align:center;">Descrição</td>
																	<td  style="background-color:#999;color:#fff;font-weight:bold;font-size:12px;text-align:center;">Data de Pagamento</td>                                                        
																	<td  style="background-color:#999;color:#fff;font-weight:bold;font-size:12px;text-align:center;">Valor</td>
																	
																</tr>';
																
                                                                    
																	} 
																	

																	?>
                                                                    <tr class="<?php echo $ano; ?>"  style="display:none;">
                                                                    
																	<td style="background-color:#F4F4F4;font-size:14px;text-align:center;"><?php echo $id_saida; ?></td>
                                                                    <td style="background-color:#F4F4F4;font-size:12px;text-align:center; padding:2px;" width="280"><?php echo $row_pg_total['especifica']; ?></td>
																	<td style="background-color:#F4F4F4;font-size:14px;text-align:center;"><?php echo $data_pg; ?></td>                                                      				
																	<td style="background-color:#F4F4F4;font-size:14px;text-align:center;"><?php echo 'R$ '.number_format($valor,2,',','.'); ?></td>
																	
																</tr>													
											<?php	 
											
											$mes_anterior = $mes;
												endwhile; 
											 
										
											 
											 
                                   if($total_Pg[$ano][$mes] != ''){
                                 
                                        echo'<tr class="'.$ano.'" style="display:none;">
											<td colspan="3" style="background-color:#fff;color:#000;font-size:12px;font-weight:bold;">Total:</td>
											
											<td  style="background-color:#fff;color:#000;font-size:12px;font-weight:bold;text-align:center;"> R$ '.number_format($total_Pg[$ano][$mes],2,',','.').'</td>
											
											</tr>
											
											<tr class="'.$ano.'" style="display:none;">
												<td colspan="5">&nbsp;</td>
											</tr>
											
											';
											                                       
								   }
                                         
                                 endforeach;
                         
                      endfor;
                     
            ?>
              <form action="imprime_ficha.php" method="post">
          <tr>      
                <td  bgcolor="#FFFFFF" >&nbsp;</td>
                <td  bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Imprimir:<select name="ano">
                    
                    <option>Selecione o ano...</option>
                    
                    <?php 
						for($i=0;$i<=count($select_anos);$i++) {
					?>	
                        <option><?php echo $select_anos[$i]; ?></option>
					<?php 
						}
                    
                    ?>
                    </select> 
          		         	
                 <input type="submit" value="OK"/>
                 </td>
                 
                  <input type="hidden" name = "projeto" value="<?php echo $id_projeto; ?>"/>
                 <input type="hidden" name = "prestador" value="<?php echo $id_prestador; ?>"/>
                  <input type="hidden" name = "regiao" value="<?php echo $regiao; ?>"/>
                
                </form>
               
                
                
			</tr>        
           </table>
  <?php
  
	}
  ?>
  
  
  		</td>
  </tr>
  
  
  
   <tr>
          
          <td width="386" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="318" bgcolor="#FFFFFF">&nbsp;</td>
        
   </tr>
     <tr>
          <td colspan="2" bgcolor="#FFFFFF"><p align="center">&nbsp;&nbsp;<br>
            <span class='style29'>
              <?=$row_prestador['c_razao']?>
              </span>
           </p></td>
           </tr>
         <tr>      
          
          
  <tr valign="top">
    <td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38"> </td>
  </tr>
</table>



















<table width="750" id="pagamentos" border="0" cellpadding="0" cellspacing="0">
          <tr>
          <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
          </tr>
          <tr>
          <td width="20" rowspan="4" background="../layout/esquerdo.gif">&nbsp;</td>
          <td width="386" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="318" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="4" background="../layout/direito.gif">&nbsp;</td>
          </tr>
          <tr>
          <td colspan="2" bgcolor="#FFFFFF"><div align="center"><strong><strong><span class="style5"><strong>
          <?php
        $img= new empresa();
        $img -> imagem();
        ?><!--<img src="../imagens/certificadosrecebidos.gif" alt="img" width="105" height="75" align="left">--></strong><br>
          <br>
            </span><span class="submit-go">CONTROLE DE PAGAMENTOS <br>
            </span><br>
          <br>
          </strong></strong></div></td>
          </tr>
          <tr>
          <td colspan="2" bgcolor="#FFFFFF"><p align="center">&nbsp;&nbsp;<br>
            <span class='style29'>
              <?=$row_prestador['c_razao']?>
              </span>
            <?php
          $string = "1,3,7,9,10,15,3.3,3.5";
         
          if (strrpos($string,"3.7")) { 
            print "OK";
          } 
          ?>
            <br>
            <br>
          </p>
        <form action="" method="post" name="form1">
            <table width="700" border="0" align="center" style="size:1px;">
          <tr bgcolor="#003300" class="style29">
        <td width="144" bgcolor="#909090" class="style27"><div align="center" class="style29"><strong>VALOR</strong></div></td>
        <td width="188" bgcolor="#909090" class="style27"><div align="center" class="style29"><strong>DATA</strong></div></td>
        <td width="160" bgcolor="#909090" class="style27"><div align="center" class="style29"><strong>DOCUMENTO</strong></div></td>
        <td width="160" bgcolor="#909090" class="style27"><div align="center" class="style29"><strong>Comprovante</strong></div></td>
        <td width="160" bgcolor="#909090" class="style27"><div align="center" class="style29"><strong>LAN&Ccedil;AR</strong></div></td>
        </tr>
        <tr>
                <td bgcolor="#EBEBEB"><div align="center">
                <input name="valor" type="text" id="valor" size="20" 
                OnKeyDown="FormataValor(this,event,20,2)" 
                onFocus="document.all.valor.style.background='#CCFFCC'" 
                onBlur="document.all.valor.style.background='#FFFFFF'" 
                style="background:#FFFFFF;"/>
                </div></td>
                <td bgcolor="#EBEBEB"><div align="center">
                <input name="data" type="text" id="data" size="10" 
                OnKeyUp="mascara_data(this)" maxlength="10"
                onFocus="document.all.data.style.background='#CCFFCC'" 
                onBlur="document.all.data.style.background='#FFFFFF'" 
                style="background:#FFFFFF;">
                </div></td>
                <td bgcolor="#EBEBEB">
                <div align="center">
                <input name="documento" type="text" id="documento" size="10" 
                onFocus="document.all.documento.style.background='#CCFFCC'" 
                onBlur="document.all.documento.style.background='#FFFFFF'" 
                style="background:#FFFFFF;">
                </div></td>
                <td bgcolor="#EBEBEB">
                <div align="center">
                <input type="file" id="comprovante" size="10" >
                <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
                <script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
                <script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
                <link rel="stylesheet" type="text/css" href="../uploadfy/css/uploadify.css" />
                <script type="text/javascript">
                $().ready(function(){
                    
                         $("#comprovante").uploadify({		
                            'uploader'       : '../uploadfy/scripts/uploadify.swf',
                            'script'         : 'actions/cadastro.pg.php',
                            'folder'         : '../../fotos',
                            'buttonText'     : 'Comprovante',
                            'queueID'        : 'bar_upload',
                            'cancelImg'      : '../uploadfy/cancel.png',
                            'auto'           : false,
                            'multi'          : true,
                            'fileDesc'       : 'Formatos de imagens e arquivos PDFs',
                            'fileExt'        : '*.gif;*.jpg;*.pdf;*.GIF;*.JPG;*.PDF;',
                            'onSelect'		 : function(){
                                                    $("#bar_upload").show('fast');
                                                },
                            'onComplete'     : function(a,b,c,d){
                                                },
							'onAllComplete' : function (){
								alert("Cadastrado com sucesso!");
								 $('#bar_upload').hide('fast');
								window.location.reload();
							}
                            
                        });
                        
                        
                        $('#Enviar').click(function(){
                            
                            $.ajax({
								url : 'actions/cadastro.pg.php',
								dataType : 'json',
								data : {
                                            'regiao' 	: '<?=$regiao?>',
                                            'prestador' : '<?=$id_prestador?>',
                                            'valor'		: $("#valor").val(),
                                            'data' 		: $("#data").val(),
                                            'documento'	: $("#documento").val()	
                                        },
								success : function(response){
									//alert(response.msg);
									if($('#bar_upload').html() != ""){
										$('#comprovante').uploadifySettings('scriptData',{'id' : response.id});
										$('#comprovante').uploadifyUpload();
										$("#valor").val("");
										$("#data").val("");
										$("#documento").val("");
									}
								}
							});                    
                        });
                        
                });
                </script>
                </div></td>
                <td bgcolor="#EBEBEB">
                    <div align="center">
                    <label>
                    <input type="button" name="button" id="Enviar"  value="Enviar">
                    </label>
                    <input type="hidden" name="prestador" id="prestador" value="<?=$id_prestador?>">
                    <input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>">
                    <input name="parcela" type="hidden" id="parcela" value="1">
                    </div>
                </td>
                </tr>
                <tr>
                <td colspan="5">
                    <div id="bar_upload" style="width:250px; margin-top:0px; margin-left:auto; margin-right:auto; margin-bottom:0px;"></div>
                    <img src="imagensprocesso/36-1.gif" style="display:none" id="loagind"/>
                </td>
                </tr>
                </table>
  </form>

<!--  
Relação com o financeiro
maikom 15/10/2010
-->

<br>
<table width="700" border="0" align="center" style="size:1px">
  <tr class="submit-go">
    <td height="26" colspan="8" bgcolor="#909090" class="style27"><div align="center" class="submit-go"><strong>HIST&Oacute;RICO DE LAN&Ccedil;AMENTOS / SAIDAS</strong></div></td>
</tr>
<tr class="style29">
	<td width="10%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>SAIDA</strong></span></span></td>
	<td width="21%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>DATA VENCIMENTO</strong></span></span></td>
    <td width="19%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>VALOR</strong></span></span></td>
    <td width="10%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>IMPRIMIR ANEXO I</strong></span></span></td>
    <td width="10%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>STATUS</strong></span></span></td>
    <td width="10%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>NF</strong></span></span></td>
    <td width="29%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>NF PG</strong></span></span></td>
    <td width="11%" align="center" bgcolor="#EBEBEB" class="style27"><span class="style291"><span class="style7"><strong>STATUS SAIDA</strong></span></span></td>
</tr>
<?php 
$query_pg = mysql_query("SELECT saida.id_saida, saida.data_vencimento, saida.valor,saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg,prestador_pg.gerado, prestador_pg.comprovante, saida.comprovante  FROM
 prestador_pg INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida WHERE prestador_pg.status_reg = '1' AND prestador_pg.id_prestador = '$id_prestador' AND saida.status != '0'");
 
 
while($row_pg = mysql_fetch_array($query_pg)):
/*$result_pg = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM prestador_pg WHERE id_saida = NULL AND  id_prestador = '$id_prestador' and status_reg = '1' ORDER BY year(data), month(data), day(data) ASC") */
$link_encryptado = encrypt('ID='.$row_pg[0].'&tipo=0');
$link_encryptado_pg = encrypt('ID='.$row_pg[0].'&tipo=1');
?>
<tr class="style29">
	<td><?=$row_pg[0]?></td>
	<td align="center"><?=implode('/',array_reverse(explode('-',$row_pg[1])))?></td>
    <td>R$ <?=$row_pg[2];?></td>
    <td align="center"><div align='center' class='style291'> <a href='<?="anexo1.php?regiao=$regiao&prestador=$id_prestador&pg=$row_pg[id_pg]"?>' target='_blank'> <img src='imagensprocesso/geraranexo1.gif' alt='Gerar Anexo I' width='190' height='31' border='0' align='abslute'> </a> </div></td>
    <td align="center">
      <div align='center' class='style291'>
        
        <?php 
	if($row_pg['gerado'] == "2"){
		$impresso = "<font color='blue'>Ja foi impresso</font>";
	}else{
		$impresso = "<font color='red'>N&atilde;o foi impresso</font>";
	}

	  echo $impresso;?>
        </div>
    </td>
    <?php 
	$query_comprovante = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_pg[0]'");
	$num_comprovante = mysql_num_rows($query_comprovante);
	if(!empty($num_comprovante) or $row_pg['comprovante'] = '1'){
		$comprovante = '<a target="_blank" href="../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'">Ver</a>';
	}
	$query_comprovante2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_pg[0]'");
	$num_comprovante2 = mysql_num_rows($query_comprovante2);
	if(!empty($num_comprovante2)){
		$comprovante2 = '<a target="_blank" href="../novoFinanceiro/view/comprovantes.php?'.$link_encryptado_pg.'">Ver</a>';
	}
	?>
    <td align="center"><?=$comprovante?></td>
    <td  align="center"><?=$comprovante2?></td>
    <td align="center">
    	<?php
		if($row_pg['status'] == '1'){
			echo "Não Pago";
		}else{
			echo "Pago em ".implode('/',array_reverse(explode('-',$row_pg['data_pg'])));
		}
		 ?>
    </td>
</tr>
<?php 
unset($comprovante,$comprovante2);
endwhile;
?>
</table>




<p><br>
</p>
<table width="700" border="0" align="center" style="size:1px">
  <tr>
    <td height="26" colspan="9" bgcolor="#909090" class="style27"><div align="center" class="submit-go"><strong>HIST&Oacute;RICO DE LAN&Ccedil;AMENTOS</strong></div></td>
  </tr>
  <tr bgcolor="#003300" class="style29">
    <td width="51" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>N&Uacute;MERO</strong></span></div></td>
    <td width="70" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>VALOR</strong></span></div></td>
    <td width="56" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>DATA</strong></span></div></td>
    <td width="84" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>DOCUMENTO</strong></span></div></td>
    <td width="123" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>IMPRIMIR ANEXO I</strong></span></div></td>
    <td width="74" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>STATUS</strong></span></div></td>
    <td width="82" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>COMPROVANTE</strong></span></div></td>
    <td width="82" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>EDITAR</strong></span></div></td>
    <td width="78" bgcolor="#EBEBEB" class="style27"><div align="center" class="style291"><span class="style7"><strong>DELETAR</strong></span></div></td>
  </tr>
  <?php 
$result_pg = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM prestador_pg where id_saida IS NULL AND id_prestador = '$id_prestador' and status_reg = '1' ORDER BY year(data), month(data), day(data) ASC") 
or die ("Erro".mysql_error());
while($row_pg = mysql_fetch_array($result_pg)){
$valor = str_replace(",",".",$row_pg['valor']);
$valor_f = number_format($valor,2,",",".");
if($row_pg['gerado'] == "2"){
$impresso = "<font color='blue'>Ja foi impresso</font>";
}else{
$impresso = "<font color='red'>N&atilde;o foi impresso</font>";
}
$valor_total_lancamentos += $valor;
?>

  <tr class="style29">
    <td><div align='center' class='style291'>
      <?=$row_pg['parcela']?>
    </div></td>
    <td><div align='center' class='style291'>
      <?=$valor_f?>
    </div></td>
    <td><div align='center' class='style291'>
      <?=$row_pg['data']?>
    </div></td>
    <td><div align='center' class='style291'>
      <?=$row_pg['documento']?>
    </div></td>
    <td valign='middle'><div align='center' class='style291'> <a href='<?="anexo1.php?regiao=$regiao&prestador=$id_prestador&pg=$row_pg[0]"?>' target='_blank'> <img src='imagensprocesso/geraranexo1.gif' alt='Gerar Anexo I' width='190' height='31' border='0' align='abslute'> </a> </div></td>
    <td><div align='center' class='style291'>
          <?=$impresso?>
        </div></td>
    <td><?php 
	if($row_pg['comprovante'] == 2){
		print "<div align='center' class='style29'>
						   <a href=\"viewFiles.php?pg=$row_pg[id_pg]\">
						     <img src=\"imagensprocesso/DOC.png\" />
						   </a>
				 		 </div>";
	
	}elseif(!empty($row_pg['comprovante'])){
				print "<div align='center' class='style29'>
						   <a href=\"comprovantes/$row_pg[id_pg].$row_pg[comprovante]\">
						     <img src=\"imagensprocesso/DOC.png\" />
						   </a>
				 		 </div>";
	}
	 ?></td>
    <td><div align='center' class='style291'> <a href='<?="impressao.php?editar=1&prestador=$id_prestador&regiao=$regiao&id_pg=$row_pg[0]"?>'>EDITAR</a> </div></td>
    <td><div align='center' class='style291'> <a href='<?="impressao.php?editar=3&prestador=$id_prestador&regiao=$regiao&id_pg=$row_pg[0]&parcela=$row_pg[parcela]"?>'> <img src='../imagens/deletado.gif' alt="1" width='20' height='18' border='0'> </a> </div></td>
  </tr>
  
  <?php } ?>
  
      <tr class="style29">
        <td>Total:</td>
        <td><div align='center' class='style291'><?php echo 'R$ '.number_format($valor_total_lancamentos,2,',','.'); ?></div></td>
        <td colspan="7"></td>
      </tr>
    </table>
	<p>&nbsp;</p>

    <!--  Relação com o financeiro
          maikom 15/10/2010 -->

    </td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">&nbsp;</td>
    <td bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4"><img src="../layout/baixo.gif" width="750" height="38"> </td>
  </tr>
</table>
<?php
}
?>
<br>
<div align="center"><a href="javascript:history.go(-1)"><img src="../imagens/voltar.gif" alt="" border="0"/></a></div><br>
</td>
</tr>
</table>
</body>
</html>
<?php
//}else{
$editar = $_REQUEST['editar'];
$prestador = $_REQUEST['prestador'];
$regiao = $_REQUEST['regiao'];
$id_pg = $_REQUEST['id_pg'];
$valor = $_REQUEST['valor'];
$data = $_REQUEST['data'];
$documento = $_REQUEST['documento'];
$result = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM prestador_pg where id_pg = '$id_pg'") or die ("Erro".mysql_error());
$row = mysql_fetch_array($result);
if($editar == "1"){
print "<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='../net1.css' rel='stylesheet' type='text/css'>
<script type=\"text/javascript\" src=\"../jquery/jquery-1.4.2.min.js\" ></script>
<script type=\"text/javascript\" src=\"../uploadfy/scripts/swfobject.js\"></script>
<script type=\"text/javascript\" src=\"../uploadfy/scripts/jquery.uploadify.v2.1.0.js\"></script>
<link rel=\"stylesheet\" type=\"text/css\" href=\"../uploadfy/css/uploadify.css\" />
<script type=\"text/javascript\">
$().ready(function(){
	
	     $(\"#comprovante\").uploadify({		
			'uploader'       : '../uploadfy/scripts/uploadify.swf',
			'script'         : 'actions/cadastro.pg_update.php',
			'folder'         : '../../fotos',
			'buttonText'     : 'Comprovante',
			'queueID'        : 'bar_upload',
			'cancelImg'      : '../uploadfy/cancel.png',
			'auto'           : true,
			'multi'          : false,
			'fileDesc'       : 'GIF JPG OU PDF',
			'fileExt'        : '*.gif;*.pdf;*.jpg;',
			'onSelect'		 : function(){
									$(\"#bar_upload\").show('fast');
									$('#button').attr('disabled','disabled');
								},
			'onComplete'     : function(a,b,c,d){
									alert(d);
									$('#bar_upload').hide('fast');
									$('#button').removeAttr('disabled');
								},
			'scriptData'     : 	{
									id_pg : '$id_pg'							
								}	
			
		});		
});
</script>
</head>
<body>";
print "
<form action='impressao.php' method='post' name='form1'>
<table width='674' align='center' cellpadding='0' cellspacing='2'>
<tr bgcolor='#909090'>
<td width='144' class='style27'><div align='center' class='style29'>VALOR</div></td>
<td width='188' class='style27'><div align='center' class='style29'><strong>DATA</strong></div></td>
<td width='160' class='style27'><div align='center' class='style29'>DOCUMENTO</div></td>
<td width='160' class='style27'><div align='center' class='style29'><strong>COMPROVANTE</strong></div></td>
<td width='160' class='style27'><div align='center' class='style29'><strong>ALTERAR</strong></div></td><br>
</tr>
<tr bgcolor='#FFFFFF'>
<td><div align='center'>
<input name='valor' type='text' id='valor' size='20' value='$row[valor]' class='campotexto'/>
</div></td>
<td><div align='center'>
<input name='data' type='text' id='data' size='12' value='$row[data]' class='campotexto'>
</div></td>
<td>
<div align='center'>
<input name='documento' type='text' id='documento' size='10' value='$row[documento]' class='campotexto''>
</div></td>
<td width='160' class='style27'><div align='center' class='style29'><strong>
";?>
<?php
if(empty($row['comprovante'])):
?> 
		<input type="file" id="comprovante" size="10" >
<?php else:?>
	<a href="comprovantes/<?=$row['id_pg'].'.'.$row['comprovante']; ?>">Ver</a>

<?php endif;?>
<?php 
print "
</strong></div></td>
<td><div align='center'>
<label>
<input type='submit' name='button' id='button' value='Enviar'>
</label>
<input type='hidden' name='id_pg' id='id_pg' value='$id_pg'>
<input type='hidden' name='prestador' id='prestador' value='$prestador'>
<input type='hidden' name='regiao' id='regiao' value='$regiao'>
<input type='hidden' name='editar' id='regiao' value='2'>
</div></td>
</tr>
</table>
<br>
<div id=\"bar_upload\" style=\"text-align:center; width=\"200px\" margin: 0px auto; \"></div>
";

print "</body></html>";
}elseif($editar == "2"){
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
 return "";
 }
}
$data2 = ConverteData($data);
mysql_query("UPDATE prestador_pg SET valor = '$valor',data = '$data2', documento = '$documento' WHERE id_pg = '$id_pg'");
print "<script>
alert(\"Informações alteradas com sucesso!\");
location.href=\"impressao.php?prestador=$prestador&regiao=$regiao\"
</script>";
}elseif($editar == "3"){
//impressao.php?editar=3&prestador=$id_prestador&regiao=$regiao&id_pg=$row_pg[0
$prestador = $_REQUEST['prestador'];
$regiao = $_REQUEST['regiao'];
$id_pg = $_REQUEST['id_pg'];
$parcela = $_REQUEST['parcela'];
$result1 = mysql_query("SELECT * FROM prestador_pg WHERE id_prestador = '$prestador' and parcela > '$parcela' and status_reg = '1'");
$row_cont1 = mysql_num_rows($result1);
mysql_query("UPDATE prestador_pg SET status_reg = '0' WHERE id_pg = '$id_pg' LIMIT 1");
$i = $parcela+1;
while($row = mysql_fetch_array($result1)){
	$i_2 = $i - 1;
	//echo "ESTOU MUDANDO A PARCELA  ".$i."  PARA  ".$i_2." ONDE O ID_PG É: ".$row[0]."<br>";
	mysql_query("UPDATE prestador_pg SET parcela = '$i_2' WHERE id_pg = '$row[0]' LIMIT 1");
	$i++;
}
print "<script>
alert(\"DELETADO!\");
location.href=\"impressao.php?prestador=$prestador&regiao=$regiao\"
</script>";
}
//}
?>