<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

if(empty($_REQUEST['volta'])){

include "conn.php";

$id_bol = $_REQUEST['bol'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '21' and id_clt = '$id_bol'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('21','$id_bol','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bol' and tipo = '21'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

if(empty($_REQUEST['tipo'])){
	$id_clt = "0";
	$tipo = "1";
	
	$result_bol = mysql_query("SELECT * FROM autonomo where id_autonomo = '$id_bol'");
	$row = mysql_fetch_array($result_bol);
	
	$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]");
	$row_curso = mysql_fetch_array($result_curso);

}else{
	$id_clt = $_REQUEST['clt'];
	$tipo = $_REQUEST['tipo'];
	
	$result_bol = mysql_query("SELECT * FROM rh_clt where id_clt = '$id_clt'");
	$row = mysql_fetch_array($result_bol);
	
	$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]");
	$row_curso = mysql_fetch_array($result_curso);

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];
mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('8','$row[0]','$data_cad', '$user_cad')");
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

}



print "<body><center>
<form action='declararenda.php' method='post' name='form1'>
<table class='bordaescura1px'>
<tr><td colspan=2 align=center>Deseja alterar o salario do bolsista?</td></tr>
<tr><td><input type='radio' name='volta' value='1'> Sim</td><td><input type='radio' name='volta' value='2' checked> Não</td></tr>
<tr><td>Salario Atual: </td><td>R$ $row_curso[valor]</td></tr>
<tr><td>Salario p/ declaração: </td><td><input type=text name=salario size=5></td></tr>
<tr><td colspan=2 align=center><input type=submit value=Enviar></td></tr>
</table>
<input type='hidden' name='id_bol' value='$id_bol'>
<input type='hidden' name='pro' value='$pro'>
<input type='hidden' name='id_reg' value='$id_reg'>
<input type='hidden' name='tipo' value='$tipo'>
<input type='hidden' name='id_clt' value='$id_clt'>
</form>
</body>
";
}else{
include "conn.php";

$id_bol = $_REQUEST['id_bol'];
$tab = $_REQUEST['tab'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];
$volta = $_REQUEST['volta'];

$tipo = $_REQUEST['tipo'];
$id_clt = $_REQUEST['id_clt'];

//---------------
if($tipo == "1"){

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM autonomo where id_autonomo = '$id_bol'");
$row = mysql_fetch_array($result_bol);

$result_reg = mysql_query("Select * from regioes where id_regiao = $row[id_regiao]", $conn);
$row_reg = mysql_fetch_array($result_reg);

}else{

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM rh_clt where id_clt = '$id_clt'", $conn);
$row = mysql_fetch_array($result_bol);

$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[id_regiao]", $conn);
$row_reg = mysql_fetch_array($result_reg);

}
$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("Select * from  projeto where id_projeto = $pro", $conn);
$row_pro = mysql_fetch_array($result_pro);

$valor_salario = number_format($row_curso['salario'],2,",",".");

$dia = date('d');
$mes = date('n');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$MesInt = (int)$mes;
$NomeMES = $meses[$MesInt];

if($volta == "1"){
$salario = $_REQUEST['salario'];
$valor_salario = "$salario";
}else{
}


include "empresa.php";
$img= new empresa();
$nomEmp= new empresa();
$cnpjEmp= new empresa();
$nomEmp2= new empresa();
$end = new empresa();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Declara&ccedil;&atilde;o de Renda</title>
<style type="text/css">
<!--
div.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
li.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
p.MsoHeader {font-size:12.0pt;
	font-family:"Arial","sans-serif";}
.style1 {color: #003300}
.style4 {font-family: Arial, Helvetica, sans-serif}
.style5 {color: red}
.style9 {font-size: 14}
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table style="width:650px; margin:0px auto;" border="0" align="center" cellpadding="10" cellspacing="0" bgcolor="#FFFFFF" class='bordaescura1px'>
  <tr>
    <td width="21%"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
<?php
$img -> imagem();
?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' />--><br />
    </span></strong></p>    </td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'><?php print "$row_pro[nome] / $row_pro[regiao] <br><br> $row[locacao]"; ?></span></b></p>    </td>
    <td width="21%">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><p align="center" style='text-align:center'>&nbsp;</p>
      <p align="center" style='text-align:center'><b><span
style='font-size:16.0pt;font-family:&quot;Univers 45 Light&quot;,&quot;sans-serif&quot;'>DECLARA&Ccedil;&Atilde;O</span></b></p>
      <font face="Arial, Helvetica, sans-serif" size="-2"><p align="center" style='text-align:center'>&nbsp;</p>
      <style type="text/css">
	  .texto {
		  text-align:justify; 
		  line-height:150%; 
		  font-size:14pt; 
		  font-family:Univers 45 Light, sans-serif; 
		  color:#030
	  }
	  .nota {
		  color:red;
	  }
	  </style>
      <p class="texto">&nbsp;&nbsp;O 
<span class="nota"><?php 
$nomEmp -> nomeEmpresa2(); 
?></span>
  sem fins lucrativos, registrada no CNPJ &ndash; MF, sob o n&ordm;. 
<span class="nota"><?php 
$cnpjEmp -> cnpjEmpresa2(); 
?></span>
, DECLARA para os devidos fins que o Sr.(a) <span class="nota"><?php print "$row[nome]"; ?></span>, CPF <span class="nota"><?php print "$row[cpf]"; ?></span>, RG <span class="nota"><?php print "$row[rg]"; ?></span>, integra, como educando do sistema andrag&oacute;gico de  capacita&ccedil;&atilde;o no curso de <span class="nota"><?php print "$row_curso[nome]";?></span>, o Projeto em parceria com a <span class="nota"><?php print "$row[locacao]";?></span>, na condi&ccedil;&atilde;o de bolsista, desde <span class="nota"><?php print "$row[data_entrada]"; ?></span> recebendo a quantia de R$ <span class="nota"><?php print "$valor_salario"; ?></span> nos termos do programa de Trabalho estabelecido  entre o Instituto declarante e a Prefeitura.</p>
      <p>&nbsp;</p>
      <p class="texto"><?php print "$row_reg[regiao], $dia de $NomeMES de $ano"; ?></p>
      <p>&nbsp;</p>
      <p style="display:block; width:400px; margin:0px auto;">                                ______________________________________________________________</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>    </font></td>
  </tr>
  <tr>
    <td colspan="7"><div align="center">
      <p>
<?php
$end -> endereco('black','13px');
?><br />
      </p>
</div>    </tr>
</table>
</body>
</html>
<?php
}           // FECHANDO IF DO SALARIO
?>