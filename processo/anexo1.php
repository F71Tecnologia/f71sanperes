<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "../conn.php";



$regiao = $_REQUEST['regiao'];
$id_prestador = $_REQUEST['prestador'];


$pega_master = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_master = mysql_fetch_assoc($pega_master);

$pega_parceiro = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$regiao'");
$row_parceiro = mysql_fetch_assoc($pega_parceiro);

$valor_master = mysql_query("SELECT * FROM master WHERE id_master= $row_master[id_master]");
$row_vmaster = mysql_fetch_assoc($valor_master);


$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);



if($row_prestador['imprimir'] < "2"){

print "

<script>

alert(\"Voc� n�o pode imprimir o ANEXO 1 sem ter GERADO O CONTRATO!\");

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

<title>CONTRATO DE PROCESSO</title>

<style type="text/css">

<!--

.style2 {

	font-family: Arial, Helvetica, sans-serif;

	font-size: 30px;

}

.style6 {

	font-size: 17px;

	font-weight: bold;

	color: #000000;

}

.style10 {

	font-size: 12px;

	font-family: Arial, Helvetica, sans-serif;

	font-weight: bold;

	color: #000000;

}

.style12 {

	font-size: 12px;

	font-weight: bold;

}

.style14 {font-size: 12px; font-family: Arial, Helvetica, sans-serif; }

.style15 {

	font-size: 12px;

	color: #000000;

}

.style17 {font-family: Arial, Helvetica, sans-serif; font-size: 30px; color: #000000; }

.style18 {font-size: 12px; font-family: Arial, Helvetica, sans-serif; color: #000000; }

-->

ul{
list-style:none;	
}

</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>



<body>

<table width="700" border="0" align="center" cellpadding="10" cellspacing="0" >

  <tr>

    <td bgcolor="#FFFFFF"><center>

<?php

include "../empresa.php";
$img= new empresa();
$img -> imagem();

$nomEmp= new empresa();
?>
     <!-- <img src="../imagens/certificadosrecebidos.gif" width="120" height="86" alt="logo" />--><br />

        </center>

    </div>

     <h3 align="center"><font size="6"> ANEXO I</font></h3>
     <p align="center">&nbsp;</p>
     <p align="center">&nbsp;</p>

      <p align="right" class="style17">&nbsp;</p>



<p style="text-align:justify"><font size="4"> Conforme determinado no item 2 - Do Prazo de Vig�ncia, e no subitem 2.1, onde determina que �o presente contrato entrar� em pleno vigor na data de sua assinatura� e respeitando o contrato n.� <?php echo $row_prestador['numero']; ?> assinado pelo <?php echo $row_vmaster['razao']?> atrav�s da <?php echo $row_parceiro['parceiro_nome']; ?>, dever� o contrato de presta��o de servi�o ter vig�ncia de 12 (doze) meses, podendo ser prorrogado por igual per�odo, de acordo com o subitem 2.2 do presente contrato. </font></p>



<h3>&nbsp;</h3>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

	<?php
	
	
		$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_prestador[id_regiao]'");
		$row_regiao = mysql_fetch_assoc($qr_regiao);
		
		
		
		$qr_master = mysql_query("SELECT * FROM master WHERE id_master= ".$nomEmp ->id_user);
		$row_master = mysql_fetch_assoc($qr_master);
		
$meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Mar�o", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");
	
 $hoje = getdate();
 $dia = $hoje["mday"];
 $mes = $hoje["mon"];
 $nomemes = $meses[$mes];
 $ano = $hoje["year"];
		
		?> 

<p align="center"><font size="4"><?php 	echo  $row_master['municipio'].', '.date('d').' de '.$nomemes.' de '.date('Y'); ?></font></p>
<p>&nbsp;</p>
<table width="100%" style="margin-top:70px;">
  <tr>
		<td align="center">____________________________________</td>
		<td align="center">____________________________________</td>
	</tr>
	<tr>
		<td align="center">Contratante</td>
		<td align="center">Contratada</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>



 <p align="right" style="font-weight:bold;margin-top:100px;">&nbsp;</p> 
 </td>
  </tr>
 </table> 
</body>

</html>

<?php

if($row_prestador['imprimir'] == "2"){

$data_b = date("Y-m-d");

$id_user = $_COOKIE['logado'];



mysql_query("UPDATE prestadorservico SET imprimir = '3', contratado_por = '$id_user', contratado_em = '$data_b', acompanhamento = '3' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}

}

}



?>

