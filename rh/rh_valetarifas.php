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
return true;  // Enter backspace ou FN qualquer um que n�o seja alfa numerico
}
key = String.fromCharCode(whichCode);
if (strCheck.indexOf(key) == -1){
return false;  //N�O E VALIDO
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
alert(\"Data digitada � inv�lida, digite novamente!\"); 
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
//C�lculo para casas decimais setadas por parametro
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

	//Analiza se o protocolo j� do m�s j� foi cadastrado.
	$result = mysql_query("SELECT * FROM rh_vale_protocolo WHERE id_reg = '$REGIAO' AND mes='$MES' AND ano='$ANO'");
	
	//Caso a QUERY acima exista no banco de dados, a vai�vel $num_row_verifica ter� valor 0.
	$num_row_verifica = mysql_num_rows($result);
	if($num_row_verifica == 0){
		mysql_query("INSERT rh_vale_protocolo SET id_reg='$REGIAO', mes='$MES',ano='$ANO', data_ini='$data_ini_MYSQL', data_fim='$data_final_MYSQL', user='$id_user', data=CURDATE()");
	}else{		
			$result = mysql_query("SELECT * FROM ano_meses WHERE num_mes='$MES'");
			$row = mysql_fetch_array($result);
			echo "<script> alert( 'O m�s de $row[nome_mes] n�o pode ser gerado novamente!'); </script>";
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
<script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
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
          <td width="21" rowspan="3" background="../layout/esquerdo.gif">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td width="26" rowspan="3" background="../layout/direito.gif">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" bgcolor="#FFFFFF">
            <table width="95%" align="center" cellspacing="0" class="bordaescura1px">
              <tr>
                <td height="45" class="show">> TARIFAS DE VALE TRANSPORTE</td>
              </tr>
              <tr>
                <td height="30"><span class="style40">
                  <label> </label>
                  </span>
                  <label> </label>
                  <span class="style40"><strong>
                    <label></label>
                  </strong></span>
<form action="rh_vale.php?tipo_cad=2&nome=1" method="post" enctype="multipart/form-data" name='form2' id="form2" onSubmit="return validaForm()">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                      <tr>
                        <td width="19%" height="28" class="style19"><div align="right"><span class="style40"><strong>Concessionaria:</strong></span></div></td>
                        <td width="81%" height="28" colspan="5">&nbsp;&nbsp;
<select name="concessionaria" id="concessionaria">
<?php
$result_conce2 = mysql_query("SELECT * FROM rh_concessionarias where id_regiao = '$regiao'");
//echo $result_conce2;
while($row_conce2 = mysql_fetch_array($result_conce2)){
print "<option value=$row_conce2[0]>$row_conce2[nome]</option>";
}
?>
</select></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Tipo:</strong></span></div></td>
                        <td height="28" colspan="5">&nbsp;&nbsp;
                          <select name="tipo2" id="tipo2">
                            <option>PAPEL</option>
                            <option>CART&Atilde;O</option>
                        </select></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Valor:</strong></span></div></td>
                        <td height="28" colspan="5"><strong>
                          &nbsp;&nbsp;
<input name="valor" type="text" id="valor" size="10" OnKeyDown="FormataValor(this,event,17,2)" class='campotexto'
onFocus="document.all.valor.style.background='#CCFFCC'"  onBlur="document.all.valor.style.background='#FFFFFF'" 
onChange="this.value=this.value.toUpperCase()">
&nbsp;                        </strong></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Itiner&aacute;rio:</strong></span></div></td>
                        <td height="28" colspan="5"><strong>&nbsp;&nbsp;
                          <input name="intinerario" type="text" id="intinerario" size="80" 
                          onFocus="document.all.intinerario.style.background='#CCFFCC'" class='campotexto'
                      onBlur="document.all.intinerario.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()">
                        </strong></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>Descri&ccedil;&atilde;o:</strong></span></div></td>
                        <td height="28" colspan="5">&nbsp;&nbsp;
                        <input name="descriao" type="text" id="descriao" size="80" class='campotexto'
                        onFocus="document.all.descriao.style.background='#CCFFCC'" 
                      onBlur="document.all.descriao.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()"></td>
                      </tr>
                      <tr>
                        <td height="28" class="style19"><div align="right"><span class="style40"><strong>C&oacute;digo:</strong></span></div></td>
                        <td height="28" colspan="5">&nbsp;&nbsp;<strong>
                          <input name="codigo" type="text" id="codigo" size="10" class='campotexto' onFocus="document.all.codigo.style.background='#CCFFCC'"  onBlur="document.all.codigo.style.background='#FFFFFF'" 
onChange="this.value=this.codigo.toUpperCase()">
                        </strong></td>
                      </tr>
                      <tr>
                        <td height="53" colspan="6" align="center" valign="middle">
                        <input name="regiao" type="hidden" id="regiao" value="<?=$regiao?>">
                      <input type="submit" name="gerar2" id="gerar2" value="GRAVAR TARIFA"></td>
                      </tr>
                    </table>
                    <script language="javascript">
function validaForm(){
           d = document.form2;

           if (d.valor.value == ""){
                     alert("O campo Valor deve ser preenchido!");
                     d.valor.focus();
                     return false;
          }
           if (d.intinerario.value == ""){
                     alert("O campo Itiner�rio deve ser preenchido!");
                     d.intinerario.focus();
                     return false;
          }

		return true;   }
                    </script>
                  </form></td>
              </tr>
            </table>
            <br>
            <table width="95%" align="center" cellspacing="0" class="bordaescura1px">
              <tr>
                <td height="36" class="show"> > TARIFAS CADASTRADAS</td>
              </tr>
              <tr>
                <td>
                  <table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr class="novo_tr">
                      <td width="6%" height="25" align="center">COD</td>
                      <td width="10%" align="center">Tipo</td>
                      <td width="11%" align="center">Valor</td>
                      <td width="57%" align="center">Itiner&aacute;rio</td>                       
                      <td width="16%" align="center">Remover tarifa</span></td>
                    </tr>

<?php
$result_tarifa = mysql_query("SELECT * FROM rh_tarifas where id_regiao = '$regiao' and status_reg = '1'");
$cont2 = "0";
while($row_tarifa = mysql_fetch_array($result_tarifa)){

$color = ($cont2++ % 2) ? "corfundo_um" : "corfundo_dois";

$result_user_cad = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_tarifa[id_user]'");
$row_user_cad = mysql_fetch_array($result_user_cad);

$valor_f = $row_tarifa['valor'];

?>

 <tr class="novalinha <?=$color?>">
  <td align="center"><?=$row_tarifa[0]?></td>
  <td align="center"><?=$row_tarifa['tipo']?></td>
  <td align="center"><?=$valor_f?></td>
  <td><?=$row_tarifa['itinerario']?></td>
  <td align="center">
  <a href="rh_vale.php?tipo_cad=3&regiao=<?=$regiao?>&tarifa=<?=$row_tarifa[0]?>&nome=1" style="color:#F00; text-decoration:none">| remover |</a></td>
 </tr>

<?php
}
?>
                  </table></td>
              </tr>
            </table>
            <br>
            <br>
            <div align="center"><a href="javascript:window.close()" class="botao">fechar</a></div>
            <br>
            <br></td>
        </tr>
        
        <tr>
          <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
          <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        
        <tr valign="top"> 
          <td height="37" colspan="4"> <img src="../layout/baixo.gif" width="750" height="38"> 
            <div align="center" class="style6"><br>
              
<?php
include "../empresa.php";
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

   if($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo   [type] != "image/jpe") {     //aki a imagem nao corresponde com as exten��es especificadas

     print "<center>
     <hr><font size=2><b>
     Tipo de arquivo n�o permitido: $arquivo[type], os �nicos padr�es permitidos s�o (gif - jpg - jpeg - png)<br>
     $arquivo[type] <br><br>
     <a href='rh_vale.php?regiao=$regiao'>Voltar</a>
     </b></font>"; 

   exit; 

 } else {  //aqui o arquivo � realente de imagem e vai ser carregado para o servidor

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

	// Resolvendo o nome e para onde o arquivo ser� movido
    $diretorio = "logo/";

	$nome_tmp = $regiao."logo_vale".$row_id.$tipo_name;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");


} //aqui fecha o IF que verificar se o arquivo tem a exten��o especificada

}else{    //AQUI EST� SEM A LOGO
mysql_query("INSERT INTO rh_concessionarias(id_regiao,id_user,nome,tipo,data) values 
('$regiao','$id_user','$nome','$tipo','$data_cad')")or die ("<hr>Erro no insert<br><hr>".mysql_error());
}

print "
<script>
alert (\"Informa��es gravadas com sucesso\");
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
alert (\"Informa��es gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";

}elseif($tipo_cad == "3"){  //REMOVER TARIFA

$regiao = $_REQUEST['regiao'];
$tarifa = $_REQUEST['tarifa'];

mysql_query("UPDATE rh_tarifas SET status_reg = '0' where id_tarifas = '$tarifa'");

print "
<script>
alert (\"Informa��es gravadas com sucesso\");
location.href=\"rh_vale.php?regiao=$regiao\"
</script>
";

}
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conex�o */
//mysql_close($conn);

}

?>
