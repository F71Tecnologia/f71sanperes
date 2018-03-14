<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{
include "conn.php";
if(empty($_REQUEST['editar'])){

$hostname = $_SERVER['REMOTE_ADDR'];


$regiao = $_REQUEST['regiao'];
$id_saida = $_REQUEST['idsaida'];
$tabela = $_REQUEST['tabela'];

$id_user = $_COOKIE['logado'];

$result = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento FROM $tabela where id_$tabela = '$id_saida'");
$row = mysql_fetch_array($result);

$result2 = mysql_query("SELECT * FROM compra where id_compra = '$row[id_compra]'");
$row2 = mysql_fetch_array($result2);

$result_nome = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_user_pedido]'");
$row_nome = mysql_fetch_array($result_nome);

$result_nome2 = mysql_query("SELECT * FROM funcionario where id_funcionario = '$row2[id_user_aprovacao]'");
$row_nome2 = mysql_fetch_array($result_nome2);

$nome_1 = $row_nome['nome1'];
$nome_2 = $row_nome2['nome1'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Financeiro - Sa&iacute;das</title>
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
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style16 {font-size: 12px; font-weight: bold; }
.style17 {font-size: 10px; font-weight: bold; }
-->
</style>
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
    <td width="21" rowspan="4" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center" class="style6">
      <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">&nbsp;<img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" /><span class="style9">EDITAR SA&Iacute;DA</span></span></font></div>
    </div></td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="4" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="16" colspan="2" align="center" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style13">DIGITE OS DADOS RELATIVOS A SA&Iacute;DA </span></span></div></td>
  </tr>
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFCCCC">

<form action="" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right">
        <p><span class="style2"><br />
            <strong> <strong>PEDIDO POR:</strong> <?=$nome_1?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>AUTORIZADO POR:</strong>
<?=$nome_2?>
&nbsp;&nbsp;&nbsp;</strong></span></p>
        <p><span class="style2"><strong>PROJETO:</strong></span> 
          <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
print "<select name='projeto'>";
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[nome]</option>";
}

print "</select>";

?>
  &nbsp;&nbsp;&nbsp;<br>
          <br>
          <span class="style16">CONTA PARA D&Eacute;BITO :</span> 
          <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao'");
print "<select name='banco'>";
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[nome] - $row_banco[agencia] / $row_banco[conta]</option>";
}

print "</select>";

?>
  &nbsp;&nbsp;&nbsp;&nbsp;<br />
          <span class="style2"><br />
            <strong>NOME:</strong></span> 
          <input name="nome" type="text" id="nome" size="80" value="<?php print "$row[nome]"; ?>"/>
  &nbsp;&nbsp;&nbsp;&nbsp;<br> 
                </p>
      </div>
      <div align="right">      </div>
      <div align="right"><span class="style16">ESPECIFICA&Ccedil;&Atilde;O:</span>
          <input name="especifica" type="text" id="especifica" size="80" value="<?php print "$row[especifica]"; ?>"/>
&nbsp;&nbsp;&nbsp;&nbsp;<br />
<br />
          <span class="style16">TIPO:</span>
          <input name="tipo" type="text" id="tipo" size="30" value="COMPRA OU SERVIÇO" disabled/>
          <span class="style16">&nbsp;&nbsp;CUSTO ADICIONAL: R$ </span> 
<input name="adicional" type="text" id="adicional" size="15" value='0,00' disabled/>
<span class="style17">(SE NECESS&Aacute;RIO)</span>&nbsp;&nbsp;&nbsp;&nbsp;<br />
      <br />
      <span class="style16">VALOR: R$ </span>
          <input name="valor" type="text" id="valor" size="20" value="<?php print "$row[valor]"; ?>" disabled/>
      &nbsp;<span class="style16">(ex 15000,00) </span>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;<span class="style16">DATA PARA CREDITO:</span>
      <input name="data_credito" type="text" id="data_credito" size="10" 
      OnKeyUp="mascara_data(this)" maxlength="10" value="<?php print "$row[data_vencimento]"; ?>" disabled>
      &nbsp;&nbsp;&nbsp;&nbsp; <br /><br>
          <span class="style16">ANEXAR COMPROVANTE:</span>&nbsp; 
          <input name="comprovante" type="checkbox" id="comprovante" onClick="document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;" value="1"/>
          &nbsp;&nbsp;&nbsp; <br />
          <span class="style16">
          <br>
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="display:none" id="tablearquivo">
            <tr>
              <td width="27%" align="right"><span class="style16">SELECIONE O 
                COMPROVANTE:</span></td>
              <td width="73%" align="right"><span class="style16"> 
                <input name="arquivo" type="file" id="arquivo" size="60" />
                <span class="style16"> &nbsp;&nbsp;&nbsp;&nbsp;</span> </span></td>
            </tr>
          </table>
          <br />
       <input type="submit" name="Submit" value="GRAVAR ALTERA&Ccedil;&Atilde;O" />
       &nbsp;&nbsp;&nbsp;</div>
        <div align="right"></div>  
		<?php
		print "
		<input type='hidden' name='id_saida' value='$id_saida'>
        <input type='hidden' name='regiao' value='$regiao'>
		<input type='hidden' name='editar' value='1'>
		";
		
		print "
<script>function validaForm(){
           d = document.form1;

           if (d.nome.value == \"\"){
                     alert(\"O campo Nome deve ser preenchido!\");
                     d.nome.focus();
                     return false;
          }

           if (d.valor.value == \"\"){
                     alert(\"O campo Valor deve ser preenchido!\");
                     d.valor.focus();
                     return false;
          }
		  
           if (d.data_credito.value == \"\"){
                     alert(\"O campo Data deve ser preenchido!\");
                     d.data_credito.focus();
                     return false;
          }


		return true;   }
</script> ";

		?>
	</form>    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
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
<?php

/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
mysql_free_result($result);

/* Fechando a conexão */
mysql_close($conn);

}else{

$id_saida = $_REQUEST['id_saida'];
$banco = $_REQUEST['banco'];
$projeto = $_REQUEST['projeto'];
$comprovante = $_REQUEST['comprovante'];
$regiao = $_REQUEST['regiao'];
$nome = $_REQUEST['nome'];


$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

mysql_query("UPDATE saida SET id_projeto = '$projeto', id_banco = '$banco', comprovante = '$comprovante' 
where id_saida = '$id_saida'") or die ("Erro! ".mysql_error());

if($comprovante == "1"){

if(!$arquivo)
{
    $mensagem = "Não acesse esse arquivo diretamente!";
}
// Imagem foi enviada, então a move para o diretório desejado
else
{
    $nome_arq = str_replace(" ", "_", $nome);
	$nome_arq = str_replace("ç", "c", $nome_arq);	
	$nome_arq = str_replace("á", "a", $nome_arq);
	$nome_arq = str_replace("é", "e", $nome_arq);
	$nome_arq = str_replace("í", "i", $nome_arq);
	$nome_arq = str_replace("ó", "o", $nome_arq);
	$nome_arq = str_replace("ú", "u", $nome_arq);
	$nome_arq = str_replace("ã", "a", $nome_arq);
	$nome_arq = str_replace("õ", "o", $nome_arq);
    $tipo_arquivo = "gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "comprovantes/";
	$nome_tmp = $nome_arq."_saida_$id_saida.$tipo_arquivo";
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}   }

print "
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Alterações cadastradas com sucesso!<br><br>
</font>
<br><br>
<a href='financeiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";

}
}
?>

</body>
</html>
