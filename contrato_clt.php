<?php

if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "conn.php";



$id_bol = $_REQUEST['bol'];

$tab = $_REQUEST['tab'];

$pro = $_REQUEST['pro'];

$id_reg = $_REQUEST['id_reg'];



$result_bol = mysql_query("SELECT * FROM $tab where id_bolsista = '$id_bol'", $conn);

$row = mysql_fetch_array($result_bol);



$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[regiao]", $conn);

$row_reg = mysql_fetch_array($result_reg);



$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);

$row_curso = mysql_fetch_array($result_curso);





$dia = date('d');

$mes = date('n');

$ano = date('Y');

switch ($mes) {

case 1:

$mes = "Janeiro";

break;

case 2:

$mes = "Fevereiro";

break;

case 3:

$mes = "Março";

break;

case 4:

$mes = "Abril";

break;

case 5:

$mes = "Maio";

break;

case 6:

$mes = "Junho";

break;

case 7:

$mes = "Julho";

break;

case 8:

$mes = "Agosto";

break;

case 9:

$mes = "Setembro";

break;

case 10:

$mes = "Outubro";

break;

case 11:

$mes = "Novembro";

break;

case 12:

$mes = "Dezembro";

break;

}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>EXAME ADMISSIONAL</title>

<style type="text/css">

<!--

.style1 {color: #FF0000}

.style2 {

	font-size: 9px;

	font-weight: bold;

}

-->

</style>

</head>



<body>

<table width="674" height="100" cellpadding="0" cellspacing="0">

  <tr>

    <td width="1%">&nbsp;</td>

    <td width="99%"><table border="0" cellspacing="0" cellpadding="0" width="616">

      <tr>

        <td width="168"><p align="center">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img width="120" height="86" src="imagens/certificadosrecebidos.gif" />--></p></td>

        <td width="448"><p align="center" class="style1"><strong><?php print "$row[locacao]"; ?></strong></p>

          <p align="center" class="style1"><strong><?php print "$row_reg[1]"; ?></strong></p></td>

      </tr>

    </table></td>

    <td width="0%">&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td><p align="center">&nbsp;</p>

      <p align="center">&nbsp;</p>

      <p align="center">&nbsp;</p>

      <p align="center"><u>EXAME  ADMISSIONAL</u></p>

      <p align="center">&nbsp;</p>

      <p>&nbsp;</p>

      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Apresentamos o (a) Senhor (a) <strong><?php print "$row[nome]"; ?> </strong>portador(a) do RG<strong> <?php print "$row[rg]"; ?> </strong>para ser submetido (a) a exame m&eacute;dico pr&eacute;-admissional,  visando a ocupa&ccedil;&atilde;o do cargo de<strong> <?php print "$row_curso[campo2]"; ?></strong>.</p>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

      <p class="style1"><?php print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

      <p>&nbsp;</p>

      <p align="center">_____________________________________________________________</p>

    <p align="center"><strong><?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?></strong></p>

    <p align="center">&nbsp;</p>

    <p align="center">&nbsp;</p>

    <p align="center">&nbsp;</p>

    <p align="center">&nbsp;</p></td>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

  </tr>

</table>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<p>&nbsp;</p>

<table width="650" height="100" cellpadding="0" cellspacing="0">

  <tr>

    <td width="13%">&nbsp;</td>

    <td width="74%"><table border="0" cellspacing="0" cellpadding="0" width="645">

        <tr>

          <td width="139"><p align="center">
<?php
$img= new empresa();
$img -> imagem();
?>
          <!--<img width="120" height="86" src="imagens/certificadosrecebidos.gif" />--></p></td>

          <td width="473"><p align="center" class="style1"><strong><?php print "$row[locacao]"; ?></strong></p>

              <p align="center" class="style1"><strong><?php print "$row_reg[1]"; ?></strong></p></td>

        </tr>

    </table></td>

    <td width="13%">&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td><p align="center">&nbsp;</p>

      <p align="center"><strong>Exame Admissional &ndash; parecer do  m&eacute;dico</strong></p>

      <p>Ao  Instituto Sorrindo para a Vida</p>

      <p>&nbsp;</p>

      <p>Comunicamos que o (a) Senhor (a)<strong> <?php print "$row[nome]"; ?></strong><strong> </strong>portador(a) do RG<strong> <?php print "$row[rg]"; ?></strong>&nbsp;&nbsp;foi considerado (a):</p>

      <p>(&nbsp;&nbsp;&nbsp;&nbsp; ) Apto (a) para a  atividade indicada.<br />

        (&nbsp;&nbsp;&nbsp;&nbsp; ) Inapto.</p>

      <p>Fundamento  do parecer:<br />

        _________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p>_________________________________________________________</p>

      <p align="left">________________________________________________________</p>

      <p align="left">&nbsp;</p>

      <p align="left">&nbsp;</p>

      <div>

        <p>M&eacute;dico respons&aacute;vel: </p>

      </div>      <p align="center">&nbsp;</p>

      <p align="center">&nbsp;</p>

      <p align="center">_________________________________________________________________________</p></td>

    <td>&nbsp;</td>

  </tr>

  <tr>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

    <td>&nbsp;</td>

  </tr>

</table>

<p>&nbsp;</p>

</body>

</html>

<?php



}



?>