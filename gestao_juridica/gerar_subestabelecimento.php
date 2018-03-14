<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$adv_id     = mysql_real_escape_string($_GET['id']);
$id_processo = mysql_real_escape_string($_GET['processo']);

$qr_processo = mysql_query("SELECT * FROM processos_juridicos WHERE proc_id = '$id_processo'");
$row_processo = mysql_fetch_assoc($qr_processo);


$qr_n_processo  = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$id_processo' ");


$qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_id = '$adv_id'") or die(mysql_error());
$row_adv     = mysql_fetch_assoc($qr_advogado);

$qr_empresa  = mysql_query("SELECT * FROM master  WHERE id_master = 1");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$qr_uf = mysql_query("Select * from uf WHERE uf_id = '$row_processo[uf_id]'");
$row_uf = mysql_fetch_assoc($qr_uf);

/////pegando dados do trabalhador
if($row_processo['id_clt'] != 0) {

	$qr_trabalhador = mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$row_processo[id_clt]'");	
} else {
	$qr_trabalhador = mysql_query("SELECT nome FROM autonomo WHERE id_autonomo = '$row_processo[id_autonomo]'");
}
$row_trabalhador = mysql_fetch_assoc($qr_trabalhador);  
/////

/////////////UF oab 
$qr_uf_oab = mysql_query("SELECT * FROM uf WHERE uf_id = '$row_adv[adv_uf_oab]'");
$row_uf_oab = mysql_fetch_assoc($qr_uf_oab);
/////////

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
<title>SUBESTABELECIMENTO</title>
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
.style8 {font-family: Arial, Helvetica, sans-serif; font-size:12px;}
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
            <span class="style9">SUBESTABELECIMENTO</span></strong></p>
          <p align="justify" class="style9"><br>
            <br>
            </p>
          <p align="justify" class="style9">&nbsp;</p>
          <p align="justify" class="style9"><br>
          
          Substabeleço, com reserva de iguais, a  <?php echo $row_adv['adv_nome']; ?>, brasileiro(a), casado(a), advogado(a), inscrito na OAB/<?php echo $row_uf_oab['uf_sigla']; ?> sob o nº <?php echo $row_adv['adv_oab']; ?>, os poderes que me foram conferidos por  <?php echo $row_empresa['razao']; ?> <?php echo $row_empresa['cnpj'];?>. nos autos do processo nº   <?php 
while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):

 echo '<b>'.$row_n_processo['n_processo_numero'].'</b>, ';

endwhile;
?>
 em curso perante o <?php echo $row_processo['proc_numero_vara'];?>ª,  no Estado  <?php  echo $row_uf['uf_id'];?>, movido contra a empresa por <?php echo $row_trabalhador['nome'];?>.
          
         
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
          
         <p align="center"><span class="style9">
         <?php
		 $q_adv = mysql_query("SELECT * FROM advogados WHERE adv_id = $row_processo[adv_id_principal]");
		 $row_adv = mysql_fetch_assoc($q_adv);
		 
		 echo $row_adv['adv_nome'];
		 
		 
		 ?>
         <br></span></p>
              <strong> 
                <p align="center" class="style9"><?php echo $row_adv['adv_oab']; ?> <?php echo $row_uf_oab['uf_sigla']; ?> </p>
              
 <p align="center"><span class="style9">        
              
<?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa2(); 
?></strong></span><br>
              <br>
              <br>
              <br>
            </p>
          <p align="center">&nbsp;</p>
          <p align="center">&nbsp;</p>
          <p align="center">&nbsp;</p>
          <p align="center"><br>
            <br>
          </p></td>
        </tr>
     
    </table>      </td>
  </tr>
</table>
</body>
</html>