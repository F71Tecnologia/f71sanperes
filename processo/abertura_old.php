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

.style5 {	font-family: Arial, Helvetica, sans-serif;

	font-size: 14px;

}

.style6 {color: #000000}

.style7 {font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

</style>

</head>



<body>

<br />

<table width="80%" border="3" align="center" cellpadding="10" cellspacing="0" bordercolor="#000000">

  <tr>

    <td><p align="center">&nbsp;</p>

      <p align="center">&nbsp;</p>

      <p align="center" class="style1 style6">
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--></p>

      <p align="center" class="style2">&nbsp;</p>

      <p align="center" class="style2">ABERTURA DE  PROCESSO</p>

      <p align="center" class="style7"><br />

      </p>

      <p align="center" class="style7">&nbsp;</p>

      <p class="style7"><strong>ASSUNTO:</strong>&nbsp;&nbsp;&nbsp;<strong>

        <?=$row_prestador['assunto']?>

      </strong></p>

      <p class="style7">&nbsp;</p>

      <p class="style7"><strong>DATA: &nbsp;&nbsp;</strong>_____/_____/________.</p>

      <p class="style7">&nbsp;</p>

      <p class="style7"><strong>PROCESSO N&ordm;: 

        <?=$row_prestador['numero']?>

         </strong></p>

      <p class="style7">&nbsp;</p>

      <p align="center" class="style7">&nbsp;</p>

      <p align="center" class="style7">&nbsp;</p>

      <p align="center" class="style7"><br />

        __________________________________<br />

 <? 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?><br />

      <span class="style5">

      <?=$row_func['nome']?>

      </span></p>

    <p align="center" class="style7">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p></td>

  </tr>

</table>

</body>

</html>

<?php

if($row_prestador['imprimir'] == "0"){

mysql_query("UPDATE prestadorservico SET imprimir = '1' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}



?>