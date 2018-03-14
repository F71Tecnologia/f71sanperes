<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../../../../conn.php";



$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];

$id_user = $_COOKIE['logado'];



$result_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");

$row_func = mysql_fetch_array($result_func);



$result_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "1"){

print "

<script>

alert(\"Você não pode imprimir este FECHAMENTO DE PROCESSO sem ter impresso a ABERTURA DE PROCESSO\");

window.close();

</script>";

}else{



$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");

$row_projeto = mysql_fetch_array($result_projeto);





$data = date("d/m/Y");



if($row_prestador['acompanhamento'] == "2" or $row_prestador['acompanhamento'] == "5"){

$mensagem = "NÃO APROVADO";

}

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>FECHAMENTO DE PROCESSO</title>

<style type="text/css">

<!--

.style1 {font-family: Arial, Helvetica, sans-serif}

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 30px;

}

.style3 {

	font-size: 14px;

	color: #000000;

}

.style11 {color: #000000}

.style13 {font-size: 16px}

.style14 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #000000; }

.style15 {font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

</style>

</head>



<body>

<br />

<table width="680" border="3" align="center" cellpadding="10" cellspacing="0" bordercolor="#000000">

  <tr>

    <td><p align="center">&nbsp;</p>

      <p align="center">&nbsp;</p>

      <p align="center" class="style1">
<?php
include "../../../../empresa.php";
$img= new empresa();
$img -> imagem();
?>
      <!--<img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--></p>

      <p align="center" class="style2">&nbsp;</p>

      <p align="center" class="style2">TERMO DE ENCERR<span class="style11">AMENTO DE  PROCESSO</span></p>

      <p align="center" class="style15"><br />

        Processo N&uacute;mero: <strong>

        <?=$row_prestador['numero']?>

        </strong><br />

        <br />

        <b><span class="style13">

        <?=$mensagem?>

        </span></b>

        <br />

        </p>

      <p class="style14"><strong>ASSUNTO:</strong> <strong>

        <?=$row_prestador['assunto']?>

      </strong><br />

        <br />

      </p>

      <p class="style14"><br />

        <strong>CONTRATADA: <span class="style13"><?=$row_prestador['c_fantasia']?></span></strong><span class="style13"> / <strong><?=$row_prestador['c_cnpj']?></strong></span><br />

        <br />

        <br />

        <br />

        <strong> ENCERRAMENTO: &nbsp;</strong>AP&Oacute;S  ASSINATURA DO CONTRATO E REGISTRO DO MESMO SER&Aacute; ABERTO O PROCESSO FINANCEIRO  PODENDO O PRESENTE SER ARQUIVADO<br />

        <br />

        <br />

        <br />

        <br />

        <strong>DATA: 

        <?=$data?>

        </strong></p>

      <p align="center" class="style14"><br />

        <br />

        <br />

      </p>

      <p align="center" class="style14">__________________________________<br />

        <?php
$nomEmp= new empresa();
$nomEmp->nomeEmpresa();
?><br />

        <?=$row_func['nome']?>

        <br />

        <br />

        <br />

      </p>      

    <p class="style1 style3">&nbsp;</p>    </td>

  </tr>

</table>

</body>

</html>

<?php

if($row_prestador['imprimir'] != "5"){//AKI ELE CANCELA O PROCESSO SEM TER CONCLUÍDO OS PASSOS

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];



mysql_query("UPDATE prestadorservico SET imprimir = '0', encerrado_por = '$id_user', encerrado_em = '$data_b', acompanhamento = '5' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}else{ // AKI O PROCESSO É TERMINADO DE FORMA CORRETA, ONDE TODOS OS PASSOS FORAM SEGUIDOS

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];

mysql_query("UPDATE prestadorservico SET imprimir = '6', encerrado_por = '$id_user', encerrado_em = '$data_b', acompanhamento = '4' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>