<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";



$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];

$id_user = $_COOKIE['logado'];



$result_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");

$row_func = mysql_fetch_array($result_func);



$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "2"){

print "

<script>

alert(\"Você não pode imprimir este MEMORANDO INTERNO sem ter impresso o MEMORANDO DE COTAÇÃO\");

window.close();

</script>";

}else{



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");

$row_projeto = mysql_fetch_array($result_projeto);





$data = date("d/m/Y");



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>ABERTURA DE PROCESSO</title>

<style type="text/css">

<!--

.style1 {font-family: Arial, Helvetica, sans-serif}

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 30px;

	color: #000000;

}

.style4 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 14px;

}

.style6 {

	font-size: 20px;

	font-weight: bold;

}

.style8 {

	font-size: 14px;

	font-family: Arial, Helvetica, sans-serif;

}

.style10 {

	font-size: 16px;

	font-weight: bold;

}

.style11 {color: #000000}

.style12 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #000000; }

.style7 {font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

</style>

</head>



<body>

<br />

<table width="680" border="3" align="center" cellpadding="10" cellspacing="0" bordercolor="#000000">

  <tr>

    <td><p align="center" class="style1"><br />

        <span class="style11">
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

        </span></p>

      <p align="center" class="style2"><span class="style6">MEMORANDO INTERNO </span><br />

      </p>

      <p class="style11">&nbsp;</p>

      <p class="style12">S&atilde;o Paulo,<span class="style7"><strong> &nbsp;&nbsp;</strong>_____/_____/________.</span><br />

        <br />

      </p>

      <p class="style12">Senhores,</p>

      <p class="style12">Ref.:<span class="style10"> COTA&Ccedil;&Atilde;O</span><br />

        <br />

      </p>

      <p align="justify" class="style12">Estamos apresentando o resultado da cota&ccedil;&atilde;o, conforme  solicita&ccedil;&atilde;o anterior. 

        Tendo em vista o regulamento de contrata&ccedil;&otilde;es,  conforme o preconizado na Lei n.&ordm; 9790/99 e Decreto n.&ordm; 3.100/99. Propomos a  contrata&ccedil;&atilde;o e/ou aquisi&ccedil;&atilde;o pela Empresa: 

        <strong>

        <?=$row_prestador['c_razao']?></strong>, 

        nas 

        condi&ccedil;&otilde;es estabelecidas na  planilha resumo.<br />

        <br />

        <br />

        Sem mais.</p>

      <p align="justify" class="style12">&nbsp;</p>

      <p align="center" class="style12">&nbsp;</p>

      <p align="center" class="style12">__________________________________<br />

      <? 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?><br />

      <?=$row_func['nome']?>

      </p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">CIENTE / AUTORIZADO:______________________________</p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">DATA ______/______/_______</p>

      <p class="style8">&nbsp;</p>

      <p align="center" class="style8">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p></td>

  </tr>

</table>

</body>

</html>

<?php

if($row_prestador['imprimir'] == "2"){

mysql_query("UPDATE prestadorservico SET imprimir = '3', acompanhamento = '2' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>

