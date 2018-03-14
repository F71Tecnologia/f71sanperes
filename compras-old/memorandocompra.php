<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";
$regiao = $_REQUEST['regiao'];
$id_compra = $_REQUEST['compra'];

$result = mysql_query("SELECT *,date_format(prazo, '%d/%m/%Y')as prazo FROM compra where id_compra = '$id_compra'");
$row = mysql_fetch_array($result);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_for = mysql_query("SELECT * FROM fornecedores where id_fornecedor = '$row[fornecedor_escolhido]'");
$row_for = mysql_fetch_array($result_for);

$descricao = str_replace("#","<br>",$row['descricao_compra']);

$data = date('d/m/Y');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Processo de Compra</title>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
-->
</style></head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
 
  
  <tr>
   
    <td colspan="2"><div align="center">
        <p>
<?php
include "../empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="imagens/certificadosrecebidos.gif" alt="soe" width="120" height="86" align="absmiddle" />--></p>
        <p><strong>MEMORANDO INTERNO DE COMPRAS<br />
        </strong><?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?> </p>
    </div></td>
  
  </tr>
  
  <tr>
    
    <td colspan="2" align="center" valign="top"><p align="right">
    <strong><br />
    Processo n&ordm;.: &nbsp;<?php print "$row[num_processo]";?></strong>&nbsp;&nbsp;&nbsp;&nbsp;</p>
      <p align="left">&nbsp;&nbsp;</p>
      <blockquote>
        <p align="left"><strong><?php print "$row_reg[regiao]";?></strong>, <strong><?php print "$data";?></strong></p>
        <p align="left">&Agrave; </p>
        <p align="left"><br />
          Diretoria para Autoriza&ccedil;&atilde;o</p>
        <p align="left"><br />
        Ref.: <strong>
		<?php print "$row[nome_produto]";?></strong>, a ser adquirido de: <strong>
		<?php print "$row_for[nome]";?></strong>, no valor total de <strong>
        <?php print "R$ $row[preco_final]";?> </strong> com entrega para 
        <?php print "$row[prazo]";?>.</p>
        <p align="left"><br />
        Conforme  publica&ccedil;&atilde;o estou anexando or&ccedil;amentos para a aquisi&ccedil;&atilde;o de <strong><?php print "$row[descricao_produto]";?></strong>&nbsp;dos  funcion&aacute;rios do <?php 
$nomEmp2= new empresa();
$nomEmp2 -> nomeEmpresa(); 
?>-<?php print "$row_reg[sigla]";?>. </p>
        <p align="left"><br />
          E 
        solicitando AUTORIZA&Ccedil;&Atilde;O, para a aquisi&ccedil;&atilde;o dos mesmos conforme descria&ccedil;&atilde;o acima e de acordo com os crit&eacute;rios de escolha.<br />
        <br />
        <strong><?php print "$descricao";?></strong><br />
        </p>
        <p align="left"><br />
            <br />
          <br />
          Sem mais.</p>
      </blockquote>
      <p><br />
        <br />
          <span class="style2">_______________________________________________<br />
   <?php 
$nomEmp2= new empresa();
$nomEmp2 -> nomeEmpresa(); 
?><br />
        </span> </p>
      <blockquote>
        <p align="left"><br />
            <br />
          <br />
          <br />
          CIENTE /  AUTORIZADO:___________________________
          &nbsp;DATA ____/____/_____</p>
      </blockquote>      
      <br />
      <br /></td>
   
  </tr>
  <tr>
  	<td><?php
$rod = new empresa();
$rod -> rodape();
?>
</td>
  </tr>
  
  
  </table>

</body>
</html>
<?php

}

?>