<?php

if(empty($_REQUEST['certo'])){
	echo "<html><title>Em Manutenção</tile><br><br><div align='center' style='font-size: 22px'>Desculpe, página em <b>Manutenção</b>!</div></html>";
	exit;
}


if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$data_hoje = date('d/m/Y');
?>
<html><head><title>Intranet</title>
<style>
h1 { page-break-after: always }
</style>
<link href="../net1.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr> 
    <td width="21%"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5">
    <br>
    <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' /><br />
        </span></strong><span style="font-size:10px"><?=$row_master['razao']?></span></p></td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'><?php print "$row_pro[nome] <br> $row_pro[regiao] "; ?></span></b></p></td>
    <td width="21%">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3"><div align="center">
        <p><strong><br>
          RELAT&Oacute;RIO DE VALE TRANSPORTE<br>
          <br>
          <?php
//$result_bolsista = mysql_query("SELECT * FROM bolsista$projeto where status = '1' and id_bolsista IN (SELECT id_bolsista FROM abolsista$projeto where transporte = '1' and status_reg = '1') ORDER BY nome");

$result_vale = mysql_query("SELECT * FROM vale where status_vale = '1' and tipo_vale = '1' and 
id_projeto = '$projeto' ORDER BY nome");

//PEGANDO VALE TRANSPORTE EM CARTÃO
   print "
   <BR>
   Cartão
   <hr width=150>
   ";

  print "
  <table width=95% border=0>
  <tr height=25>
  <td background='../layout/fundo_tab_azul.gif' align=center>Cód</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Nome</td>  
  <td background='../layout/fundo_tab_azul.gif' align=center>Número Cartão</td>  
  <td background='../layout/fundo_tab_azul.gif' align=center>Valor Total</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Tipo Cartão 1</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Número Cartão 2</td>  
  <td background='../layout/fundo_tab_azul.gif' align=center>Valor Total 2</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Tipo Cartão 2</td>
  </tr>";


$cont = "0";
while($row_vale = mysql_fetch_array($result_vale)){

$result_bolsista = mysql_query("SELECT * FROM autonomo where id_bolsista = '$row_vale[id_bolsista]'");
$row_bolsista = mysql_fetch_array($result_bolsista);


if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

print "
<TR bgcolor=$color>
<TD>$row_bolsista[campo3]</TD>
<TD>$row_bolsista[nome]</TD>
<TD>$row_vale[numero_cartao]</TD>
<TD>$row_vale[valor_cartao]</TD>
<TD>$row_vale[tipo_cartao_1]</TD>
<TD>$row_vale[numero_cartao2]</TD>
<TD>$row_vale[valor_cartao2]</TD>
<TD>$row_vale[tipo_cartao_2]</TD>
</TR>";

$cont ++;	  
}
print "</TABLE><Br>";

	 ?> 
        </strong></p>
        <p><strong>
          <?php
//$result_bolsista2 = mysql_query("SELECT * FROM bolsista$projeto where status = '1' and id_bolsista IN (SELECT id_bolsista FROM abolsista$projeto where transporte = '1' and status_reg = '1') ORDER BY nome");


$result_vale2 = mysql_query("SELECT * FROM vale where status_vale = '1' and tipo_vale = '2' and 
id_projeto = '$projeto' ORDER BY nome");


//PEGANDO VALE TRANSPORTE EM CARTAO
   print "
   <BR>
   Vale Transporte
   <hr width=150>
   ";

  print "
  <table width=95% border=0>
  <tr height=25>
  <td background='../layout/fundo_tab_azul.gif' align=center>C&oacute;d</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Nome</td>  
  <td background='../layout/fundo_tab_azul.gif' align=center>Passagem 1</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Passagem 2</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Passagem 3</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Passagem 4</td>
  <td background='../layout/fundo_tab_azul.gif' align=center>Valor Total</td>
  </tr>";


$cont = "0";
while($row_vale2 = mysql_fetch_array($result_vale2)){
 
$result_bolsista2 = mysql_query("SELECT * FROM atonomo where id_bolsista = '$row_vale2[id_bolsista]'");
$row_bolsista2 = mysql_fetch_array($result_bolsista2);

if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

print "
<TR bgcolor=$color>
<TD>$row_bolsista2[campo3]</TD>
<TD>$row_bolsista2[nome]</TD>
<TD>$row_vale2[qnt1] de R$ $row_vale2[valor1]</TD>
<TD>$row_vale2[qnt2] de R$ $row_vale2[valor2]</TD>
<TD>$row_vale2[qnt3] de R$ $row_vale2[valor3]</TD>
<TD>$row_vale2[qnt4] de R$ $row_vale2[valor4]</TD>
<TD>R$ $row_vale2[valor_cartao]</TD>
</TR>";

$cont ++;	  
}
print "</TABLE><Br>";
/*
$result_valor1 = mysql_query("SELECT Distinct valor1,valor2,valor3,valor4 FROM vale where valor1 != '' and tipo_vale = '2' and id_projeto = '$projeto' ORDER BY valor1");

  print "
  <table width=95% border=0>
  <tr height=25>
  <td background='layout/fundo_tab_azul.gif' align=center>Valor</td>
  <td background='layout/fundo_tab_azul.gif' align=center>Quantidade</td>  
  </tr>";

$cont3 = "0";
while($row_total_valor1 = mysql_fetch_array($result_valor1)){

print "
<TR bgcolor=$color>
<TD>$row_total_valor1[1]</TD>
<TD>--</TD>
</TR>";

$cont3 ++;	  
}

print "</TABLE><Br>";
*/

$num_bolsista = mysql_num_rows($result_bolsista);
$num_bolsista2 = mysql_num_rows($result_bolsista2);

$total = "$num_bolsista" + "$num_bolsista2";
print "
Total Cartão: $num_bolsista<br>
Total Papel: $num_bolsista2<br>
Total Geral: $total";
	 ?>
          <br>
          <br>
          </strong>        </p>
    </div></td>
  </tr>
</table>
</body>
</html>
<?php
/* Liberando o resultado */
mysql_free_result($result_vale);
mysql_free_result($result_bolsista);

mysql_free_result($result_vale2);
mysql_free_result($result_bolsista2);

/* Fechando a conexão */
mysql_close($conn);

}
?>