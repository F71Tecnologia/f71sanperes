<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../empresa.php";

$pro = '1';
$id_reg = '4';

//$id_clt = $_REQUEST['clt'];

//$result_bol = mysql_query("SELECT * FROM rh_clt where id_clt = '3585'");
//$row = mysql_fetch_array($result_bol);
/*
$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[id_regiao]", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);
*/
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
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
/*$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];
mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('13','$row[0]','$data_cad', '$user_cad')"); */
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EXAME DEMISSIONAL</title>
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {
	font-size: 9px;
	font-weight: bold;
}
P.quebra-aqui {page-break-before: always}
-->
</style>
</head>

<body>
<?
$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_regiao = '$id_reg' AND status = '10' AND id_projeto = '$pro' ORDER BY nome", $conn);
	  
	 while ($row = mysql_fetch_array($result_bol)){	
	 
	 	$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[id_regiao]", $conn);
	 	$row_reg = mysql_fetch_array($result_reg);

		$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);
		$row_curso = mysql_fetch_array($result_curso);

	 
		/*print "<table width='650' height='100' cellpadding='0' cellspacing='0'>
  		<tr>
    	<td width='10%'>&nbsp;</td>
    	<td width='75%'>
    
    	<table border='0' cellspacing='0' cellpadding='0' width='684'>
      	<tr>
        <td width='168'><p align='center'>";*/

		print "<br>";
		$img= new empresa();
		$img -> imagem();
		
		print "
		</p></td>
     	<td width='611'><p align='center' class='style1'><strong>$row[locacao]</strong></p>
            <p align='center' class='style1'><strong>$row_reg[regiao]</strong></p></td>
      	</tr>
    	
    
    </td>
    <td width='15%'>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><p align='center'>&nbsp;</p>
      <p align='center'>&nbsp;</p>
      <p align='center'>&nbsp;</p>
      <p align='center'><u>EXAME DEMISSIONAL  </u></p>
      <p align='center'>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Apresentamos o (a) Senhor (a) <strong>$row[nome]</strong>portador(a) do RG<strong> $row[rg] </strong>para ser submetido (a) a exame m&eacute;dico demissional,  ao qual ocupavao cargo de<strong> $row_curso[campo2]</strong>.</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p class='style1'>$row_reg[regiao], $dia de $mes de $ano.</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p align='center'>_____________________________________________________________</p>
    <p align='center'><strong>";

$nomEmp= new empresa();
$nomEmp -> nomeEmpresa2(); 

//print "<p class='quebra-aqui'><!-- Quebra de página --></p>";

print "</strong></p>
    <p align='center'>&nbsp;</p>
    <p align='center'>&nbsp;</p>
    <p align='center'>&nbsp;</p>
    <p align='center'>&nbsp;</p></td>
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
<table width='650' height='100' cellpadding='0' cellspacing='0'>
  <tr>
    <td width='13%'>&nbsp;</td>
    <td width='74%'><table border='0' cellspacing='0' cellpadding='0' width='686'>
        <tr>
          <td width='139'><p align='center'>";

$img2= new empresa();
$img2 -> imagem();
print "</p></td>
          <td width='473'><p align='center' class='style1'><strong>$row[locacao]</strong></p>
              <p align='center' class='style1'><strong>$row_reg[regiao]</strong></p></td>
        </tr>
    </table></td>
    <td width='13%'>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><p align='left'><span class='style1'><br />
      $row_reg[regiao], $dia de $mes de $ano.</span></p>
      <p align='center'><strong>Exame Demissional &ndash; parecer do  m&eacute;dico</strong></p>
      <p>Ao  Instituto Sorrindo para a Vida</p>
      <p>&nbsp;</p>
      <p>Comunicamos que o (a) Senhor (a)<strong> $row[nome]</strong><strong> </strong>portador(a) do RG<strong> $row[rg]</strong>&nbsp;&nbsp;foi considerado (a):</p>
      <p>(&nbsp;&nbsp;&nbsp;&nbsp; ) Apto (a) para a  dispensa imediata.<br />
        (&nbsp;&nbsp;&nbsp;&nbsp; ) Inapto (a) para dispensa imediata.</p>
      <p>Fundamento  do parecer:<br />
        _________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p>_________________________________________________________</p>
      <p align='left'>________________________________________________________</p>
      <p align='left'>&nbsp;</p>
      <p align='left'>&nbsp;</p>
      <div>
        <p>M&eacute;dico respons&aacute;vel: </p>
      </div>      
      <p align='center'>&nbsp;</p>
      <p align='center'>&nbsp;</p>
      <p align='center'>_________________________________________________________________________</p></td>
    <td>&nbsp;</td>
  </tr>";
  
	print "<br>";
	print "<p class='quebra-aqui'><!-- Quebra de página --></p>";
	print "<br>";

	print "</table>";

		
	 }
?>
<p>&nbsp;</p>
</body>
</html>