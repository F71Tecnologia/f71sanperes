<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";



$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];





$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "1"){

print "

<script>

alert(\"Você não pode imprimir este MEMORANDO DE COTAÇÃO sem ter impresso a ABERTURA DE PROCESSO\");

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

<title>SOLICITA&Ccedil;&Acirc;O INTERNA</title>

<style type="text/css">

<!--

.style1 {font-family: Arial, Helvetica, sans-serif}

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 30px;

}

.style4 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 14px;

	color: #000000;

}

.style5 {

	font-size: 16px;

	font-weight: bold;

}

.style6 {

	font-size: 20px;

	font-weight: bold;

}

.style8 {color: #000000}

.style7 {font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

</style>

</head>



<body>

<br />

<table width="680" border="3" align="center" cellpadding="10" cellspacing="0" bordercolor="#000000">

  <tr>

    <td><p align="center" class="style1"><br />

        <br />
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?>
      <!--  <img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

    </p>

      <p align="center" class="style2 style8"><span class="style6">SOLICITA&Ccedil;&Atilde;O  INTERNA </span><br />

      </p>

      <p class="style8">&nbsp;</p>

      <p class="style4">S&atilde;o Paulo,<span class="style7"><strong> &nbsp;&nbsp;</strong>_____/_____/________.</span><br />

        <br />

      </p>

      <p class="style4">Senhores,<br />

        <br />

      </p>

      <p class="style4">Ref.: <span class="style5">COTA&Ccedil;&Atilde;O</span><br /><br />

          <strong>

          <?=$row_prestador['c_razao']?>

          </strong><br />

        <br />

        <br />

      </p>

      <p align="justify" class="style4">            Conforme regulamento de  contratações, preconizado na Lei n.º 9790/99 e Decreto n.º 3.100/99. Solicito  

        tomada de 

        pre&ccedil;os (cota&ccedil;&atilde;o) de servi&ccedil;o especializado para presta&ccedil;&atilde;o de servi&ccedil;os:<strong>

        <?php

        

		print "$row_prestador[especificacao]";

		

		

		?>

        </strong> 

        para  

        complementa&ccedil;&atilde;o a(o) 

        <strong>

        <?=$row_projeto['nome']?>

        </strong>, no munic&iacute;pio de<strong>

        <?=$row_prestador['co_municipio']?>

        </strong>.</p>

      <p align="justify" class="style4"><br />

        <br />

        Sem mais.<br />

        <br />

      </p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">__________________________________<br />

        <? 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?><br />

        Gerente do Projeto</p>

      <p align="center" class="style4">&nbsp;</p>

      <p align="center" class="style4">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p>

    <p align="center" class="style1">&nbsp;</p></td>

  </tr>

</table>

</body>

</html>

<?php

if($row_prestador['imprimir'] == "1"){

mysql_query("UPDATE prestadorservico SET imprimir = '2' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>