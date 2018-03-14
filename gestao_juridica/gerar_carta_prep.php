<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$prep_id     = mysql_real_escape_string($_GET['id']);
$id_processo = mysql_real_escape_string($_GET['processo']);

$qr_processo = mysql_query("SELECT * FROM processos_juridicos WHERE proc_id = '$id_processo'") or die(mysql_error());
$row_processo = mysql_fetch_assoc($qr_processo);


$qr_n_processo  = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$id_processo' ");


$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_id = '$prep_id'") or die(mysql_error());
$row_prep    = mysql_fetch_assoc($qr_preposto);

$qr_empresa  = mysql_query("SELECT * FROM master  WHERE id_master = 1");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$dia = date('d');
$mes = date('m');
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


<html xmlns="undefined">
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>CARTA DE REFER&Ecirc;NCIA</title>
<style>
<!--
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{font-size:8.0pt;
	font-family:"Tahoma","sans-serif";}
body {
	margin-left: 5px;
	margin-top: 0px;
	margin-right: 5px;
	margin-bottom: 0px;
}
.style9 {font-family: Arial, Helvetica, sans-serif}
.style12 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 10px;
}
-->
</style>
<link href="../net1.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#FFFFFF" lang=PT-BR>
<table width="700" align="center" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr>
    <td><table width="650" align="center">
      <tr>
        <td align="center" valign="middle"><div align="center"><span class="style9"><strong><?  
		include '../empresa.php';
		$img = new empresa();
		$img -> imagem();
		?>INSTITUTO SORRINDO PARA A VIDA</strong></span></div></td>
        </tr>
      <tr>
        <td><p align="center" class="style9"><br>
          </p>
          <p align="center"><strong><br>
            <br>
            </strong></p>
          <p align="center"><strong><br>
            <span class="style9">CARTA DE PREPOSIÇÃO</span></strong></p>
          <p align="justify" class="style9"><br>
            <br>
            </p>
          <p align="justify" class="style9">&nbsp;</p>
          <p align="justify" class="style9"><br>
           <strong><?php echo $row_empresa['razao']; ?></strong>, sociedade devidamente inscrita no CNPJ/MF sob nº ><?php echo $row_empresa['cnpj'];?>, com sede na <?php echo $row_empresa['endereco'];?>, por seu presidente, Sr. <?php echo $row_empresa['responsavel']?>, casado, administrador de empresas, portador da cédula de identidade RG nº  <?php echo $row_empresa['rg']?> e inscrito no Cadastro de Pessoas Físicas do Ministério da Fazenda, sob nº <?php echo $row_empresa['cpf']?>, pelo presente instrumento de <strong>Carta de Preposição</strong>, nomeia e constitui seu(ua) preposto (a) ou (a) <strong> SR(A).  <?php echo $row_prep['prep_nome']?></strong>, portador (a) da Cédula de Identidade RG nº <?php echo $row_prep['prep_rg']?>,
 inscrito (a) no CPF/MF sob o nº  <?php echo $row_prep['prep_cpf']?>, a fim de representá-lo perante a Justiça Especializada do Trabalho,
  nos autos do processo nº 
  <?php 
while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):

 echo $row_n_processo['n_processo_numero'].',';

endwhile;
?> que tramita perante a <?php echo $row_processo['proc_numero_vara'];?>ª <?php echo $row_processo['proc_local'];?> (recte.________________________). 
</p>



  <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">Itaboraí, <?php echo $dia?> de <?php echo $mes?> de <?php echo $ano;?>.</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9"><br>
          </p>
        </td>
        </tr>
      <tr>
        <td>
 
          </p></td>
        </tr>
    </table>      </td>
  </tr>
</table>
</body>
</html>