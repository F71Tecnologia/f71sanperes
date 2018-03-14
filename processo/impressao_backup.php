<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";

if(empty($_REQUEST['editar'])){

$regiao = $_REQUEST['regiao'];
$id_prestador = $_REQUEST['prestador'];

if(!empty($_REQUEST['parcela'])){

$valor = $_REQUEST['valor'];
$data = $_REQUEST['data'];
$documento = $_REQUEST['documento'];
$valor = str_replace(".","", $valor);

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
<link href="../net1.css" rel="stylesheet" type="text/css">
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
	font-size: 16px;
}
.style42 {font-weight: bold; color: #003300; font-family: Arial, Helvetica, sans-serif;}
.style43 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 14px; }
.style44 {font-family: Arial, Helvetica, sans-serif; color: #003300; font-size: 14px; }
.style45 {font-size: 14px}
.style46 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.style47 {
	font-size: 16px;
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
    <td colspan="2" align="center" bgcolor="#FFFFFF"><span class="style29">GERENCIAMENTO DE PRESTADORES DE SERVI&Ccedil;O</span><br>
      <span class="calibri">(Selecione a &aacute;rea que deseja visualizar, Clique para Exibir, clique novamente para Ocultar)<br>
      </span></td>
    </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF"></td>
    </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><table width="90%" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
      <tr>
        <td width="53%" height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Processo:</td>
         <td width="47%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="#"><img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" onClick="document.all.processo.style.display = (document.all.processo.style.display == 'none') ? '' : 'none' ;" ></a> </td>  
      </tr>
      <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Processo Avan&ccedil;ado:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="#" onClick="javascript:document.all.avancado.style.display = (document.all.avancado.style.display == 'none') ? '' : 'none' ;">
        <img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" 
         ></a></td>
      </tr>
      <tr>
        <td height="25" align="right" bgcolor="#EBEBEB" class="style29">Gerenciamento de Pagamentos:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="#" onClick="javascript:document.all.pagamentos.style.display = (document.all.pagamentos.style.display == 'none') ? '' : 'none' ;">
        <img src="../imagens/ver_relatorio.gif" width="90" height="22" border="0" 
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
    <p><span style="font-size: 13pt; font-family: Arial; color: #000000;"><strong><strong><span class="style5">
      <?php 
print "$row_prestador[c_razao]"
?>
      </span></strong></strong></span>
      </p>
    <table width="90%" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
      <tr>
        <td height="25" colspan="2" align="center" bgcolor="#666666" class="style7"><strong><strong>CONTROLE DE NOVOS PROCESSOS<br>
        N&Atilde;O DEIXE DE IMPRIMIR OS PROCEDIMENTOS ABAIXO</strong>:</strong></td>
        </tr>
      <tr>
        <td width="65%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Procedimento de Abertura de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td width="35%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="abertura.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank"><img src="imagensprocesso/abertura.gif" alt="abertura" width="190" height="31" border="0"></a></td>
        </tr>
      <tr>
        <td height="25" align="center" bgcolor="#EBEBEB" class="style29">Memorando de Cota&ccedil;&atilde;o de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="memocota.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">  
          <img src="imagensprocesso/memocota.gif" alt="Memorando de cota&ccedil;&otilde;es" width="190" height="31" border="0"></a></td>
        </tr>
      <tr>
        <td height="23" align="center" bgcolor="#EBEBEB" class="style29">Memorando interno para Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
        <td align="center" valign="middle" bgcolor="#FFFFFF"><a href="memointerno.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
          <img src="imagensprocesso/memointerno.gif" alt="Gerar Memorando Interno" width="190" height="31" border="0"></a></td>
        </tr>
      <tr>
        <td height="25" align="center" bgcolor="#EBEBEB" class="style29">Fechamento de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os</td>
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
$id_user = $_COOKIE['logado'];
if($id_user == "1" or $id_user == "5" or $id_user == "9" or $id_user == "34" or $id_user == "46" or  $id_user == "51" or  $id_user == "64" or $id_user == '75'){
//  if($id_user == "1" or $id_user == "5" or $id_user == "9" or $id_user == "34" or $id_user == "46" or  $id_user == "51"){
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
</span><br>
</strong></strong></div></td>
</tr>
<tr>
<td colspan="2" bgcolor="#FFFFFF"><table width="90%" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
  <tr>
      <td height="25" colspan="2" align="center" bgcolor="#666666" class="style6"><strong><strong><span class="style7">CONTROLE DE  PROCESSOS<br>
GERENCIAMENTO AVAN&Ccedil;ADO DO PROCESSO</span></strong></strong></td>
    </tr>
    <tr>
      <td width="66%" height="25" align="center" bgcolor="#EBEBEB" class="style29">Procedimento de Abertura de Processo de Presta&ccedil;&atilde;o de Servi&ccedil;os:</td>
      <td width="34%" align="center" valign="middle" bgcolor="#FFFFFF"><a href="contrato.php?regiao=<?=$regiao?>&prestador=<?=$id_prestador?>" target="_blank">
        <img src="imagensprocesso/gerarcontrato.gif" alt="Gerar Contrato para Prestador de Servi&ccedil;os" width="190" height="31" border="0"></a></td>
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
  <td colspan="2" bgcolor="#FFFFFF"><div align="center"><span style="font-size: 13pt; font-family: Arial; color: #000000;"><strong><strong><span class="style5"><strong>
  <?php
$img= new empresa();
$img -> imagem();
?><!--<img src="../imagens/certificadosrecebidos.gif" alt="img" width="105" height="75" align="left">--></strong><br>
    CONTROLE DE PAGAMENTOS</span><br>
  <br>
  </strong></strong></span></div></td>
  </tr>
  <tr>
  <td colspan="2" bgcolor="#FFFFFF"><p align="center">&nbsp;&nbsp;<br>
    <span class='style29'>
      <?=$row_prestador['c_razao']?>
      </span>
    <?php
  $string = "1,3,7,9,10,15,3.3,3.5";
 
  if (strrpos($string,"3.7")) { 
    print "Beleza.. Funcionou";
  } 
  ?>
  <br>
  </p>
  <form action="" method="post" name="form1">
    <table width="90%" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
  <tr bgcolor="#003300" class="style7">
<td width="144" bgcolor="#666666" class="style27"><div align="center" class="style29">VALOR</div></td>
<td width="188" bgcolor="#666666" class="style27"><div align="center" class="style29"><strong>DATA</strong></div></td>
<td width="160" bgcolor="#666666" class="style27"><div align="center" class="style29">DOCUMENTO</div></td>
<td width="160" bgcolor="#666666" class="style27"><div align="center" class="style29"><strong>LAN&Ccedil;AR</strong></div></td>
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
<td bgcolor="#EBEBEB"><div align="center">
<label>
<input type="submit" name="button" id="button" value="Enviar">
</label>
<input type="hidden" name="prestador" id="prestador" value="<?=$id_prestador?>">
<input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>">
<input name="parcela" type="hidden" id="parcela" value="1">
</div></td>
</tr>
</table>
  </form>
  <br>
  <table width="90%" align="center" style="border-bottom-color:#fff; border-left-color:#fff; border-right-color:#fff; border-top-color:#fff; border-color:#fff; border: solid; size:1px">
<tr>
<td height="26" colspan="8" bgcolor="#666666" class="style27"><div align="center" class="style29"><span class="style7"><strong>HIST&Oacute;RICO DE LAN&Ccedil;AMENTOS</strong></span></div></td>
</tr>
<tr bgcolor="#003300">
<td width="51" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>N&Uacute;MERO</strong></span></div></td>
<td width="70" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>VALOR</strong></span></div></td>
<td width="56" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>DATA</strong></span></div></td>
<td width="84" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>DOCUMENTO</strong></span></div></td>
<td width="123" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>IMPRIMIR ANEXO I</strong></span></div></td>
<td width="74" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>STATUS</strong></span></div></td>
<td width="82" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>EDITAR</strong></span></div></td>
<td width="78" bgcolor="#999999" class="style27"><div align="center" class="style29"><span class="style7"><strong>DELETAR</strong></span></div></td>
</tr>
<?php 

$result_pg = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM prestador_pg where id_prestador = '$id_prestador' and status_reg = '1' ORDER BY year(data), month(data), day(data) ASC") 
or die ("Erro".mysql_error());

while($row_pg = mysql_fetch_array($result_pg)){

$valor = str_replace(",",".",$row_pg['valor']);
$valor_f = number_format($valor,2,",",".");

if($row_pg['gerado'] == "2"){
$impresso = "<font color='blue'>Ja foi impresso</font>";
}else{
$impresso = "<font color='red'>Não foi impresso</font>";
}

print "
<tr>
<td><div align='center' class='style29'>$row_pg[parcela]</div></td>
<td><div align='center' class='style29'>$valor_f</div></td>
<td><div align='center' class='style29'>$row_pg[data]</div></td>
<td><div align='center' class='style29'>$row_pg[documento]</div></td>
<td valign='middle'><div align='center' class='style29'><a href='anexo1.php?regiao=$regiao&prestador=$id_prestador&pg=$row_pg[0]' target='_blank'> <img src='imagensprocesso/geraranexo1.gif' alt='Gerar Anexo I' width='174' height='23' border='0' align='abslute'></a></td>
<td><div align='center' class='style29'>$impresso</div></td>
<td><div align='center' class='style29'><a href='impressao.php?editar=1&prestador=$id_prestador&regiao=$regiao&id_pg=$row_pg[0]'>EDITAR</a></div></td>
<td><div align='center' class='style29'><a href='impressao.php?editar=3&prestador=$id_prestador&regiao=$regiao&id_pg=$row_pg[0]&parcela=$row_pg[parcela]'><img src='../imagens/deletado.gif' width='20' height='18' border='0'></a></div></td>

</tr>";
}


?>
</table>
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

}else{

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
</head>
<body>";


print "
<form action='impressao.php' method='post' name='form1'>
<table width='674' border='2' align='center' cellpadding='0' cellspacing='2' bordercolor='#000000'>
<tr bgcolor='#003300'>
<td width='144' class='style27'><div align='center' class='style29'>VALOR</div></td>
<td width='188' class='style27'><div align='center' class='style29'><strong>DATA</strong></div></td>
<td width='160' class='style27'><div align='center' class='style29'>DOCUMENTO</div></td>
<td width='160' class='style27'><div align='center' class='style29'><strong>ALTERAR</strong></div></td>
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
</table>";

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
}
?>
