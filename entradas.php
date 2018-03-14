<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro - Entradas</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style14 {font-size: 14px}
.style16 {font-size: 12px; font-weight: bold; }
.style17 {font-size: 10px; font-weight: bold; }
-->
</style></head>
<?php
print "
<script>
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
</script></head>";

?>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="6" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center" class="style6">
      <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/entradas.gif" alt="entrdas" width="25" height="25" align="absmiddle" />&nbsp;<span class="style9">CADASTRAR ENTRADA</span> </span></font></div>
    </div></td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="6" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="16" colspan="2" align="center" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style13">DIGITE OS DADOS RELATIVOS A ENTRADA </span></span></div></td>
  </tr>
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#CCFFCC">
    <form action="cadastro2.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
      <label>
      <div align="right"><span class="style2"><br />
      </span><span class="style2"><strong>PROJETO:</strong></span>
      <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[id_projeto] - $row_projeto[nome] </option>";
}

print "</select>";

?>
&nbsp;&nbsp;&nbsp;<br />
<br />
<span class="style16">CONTA PARA CR&Eacute;DITO :</span>
<?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao'");
print "<select name='banco'>";
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[id_banco] - $row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
}

print "</select>";

?>
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
        <span class="style2"><br />
        <strong>NOME:</strong></span> 
        <input name="nome" type="text" size="80" id="nome" />
&nbsp;&nbsp;&nbsp;&nbsp; </div>
      <label><br />
      <div align="right"><span class="style16">DESCRI&Ccedil;&Atilde;O:</span>
        <input name="especifica" type="text" size="80" id="especifica" />
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
<span class="style16">TIPO:</span>
<?php
$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE tipo='1' ORDER BY nome");
print "<select name='tipo'>";
while($row_tipo = mysql_fetch_array($result_tipo)){
print "<option value=$row_tipo[0] title='$row_tipo[descricao]'>$row_tipo[0] - $row_tipo[nome]</option>";
}

print "</select>";

?>
<span class="style16">&nbsp;&nbsp;CUSTO ADICIONAL: R$ </span>
<input name="adicional" type="text" size="20" id="adicional" />
<span class="style17">(SE NECESS&Aacute;RIO)</span>&nbsp;&nbsp;&nbsp;&nbsp;<br />
      <br />
      <span class="style16">VALOR: R$ </span>
      <input name="valor" type="text" size="20" id="valor" />
      &nbsp;<span class="style16">(ex 15000,00) </span>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<span class="style16">DATA PARA CR&Eacute;DITO:</span>
      <input name="data_credito" type="text" id="data_credito" size="10" onkeyup="mascara_data(this)" maxlength="10" />
      &nbsp;&nbsp;&nbsp;&nbsp; <br />
       <br />
       <input type="submit" name="Submit" value="GRAVAR ENTRADA" />
       &nbsp;&nbsp;&nbsp;</div>
      </label>
      <div align="right">
        <?php
		print "
		<input name='id_cadastro' type='hidden' id='id_cadastro' value='22'>
        <input type='hidden' name='regiao' value='$regiao'>";
		?>
        <br />
      </div>
    </form>
    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top"><div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/entradas.gif" alt="entrdas" width="25" height="25" align="absmiddle" /></span><span class="style3">&nbsp;<span class="style14">CADASTRAR TIPOS ENTRADA</span></span></font></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#CCFFCC">
    <div align="right">
    <form action="cadastro2.php" method="post" name="form2">
    <span class="style2"><strong> <br />
      &nbsp;&nbsp;&nbsp;NOME:</strong></span>
      <input name="nome" type="text" size="40" id="nome" />
&nbsp;&nbsp;&nbsp;<span class="style16">DESCRI&Ccedil;&Atilde;O:</span>
<input name="descricao" type="text" size="40" id="descricao" />
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
<input type="submit" name="Submit2" value="GRAVAR TIPO DE ENTRADA" />
&nbsp;&nbsp;&nbsp;<br />
<?php
		print "
		<input name='id_cadastro' type='hidden' id='id_cadastro' value='23'>
        <input type='hidden' name='tipo' value='1'> 
		<input type='hidden' name='regiao' value='$regiao'>";

?>

&nbsp;&nbsp;<br />
<br />
<div align="center"><a href="<?php print "financeiro.php?regiao=$regiao"; ?>" class="style12">Voltar</a></div>

<br />
</form>
    </div></td>
  </tr>
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#5C7E59"><img src="layout/baixo.gif" width="750" height="38" />
        <div align="center" class="style6">
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?><br />
      </div></td>
  </tr>
</table>
</body>
</html>
<?php 
}
?>