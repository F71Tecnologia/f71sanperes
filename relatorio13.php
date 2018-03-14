<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

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

$result_bolsista = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo where status = '0'  AND tipo_contratacao ='1' AND id_projeto ='$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_row = mysql_num_rows($result_bolsista);

$result_clt = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where status = '0'  AND tipo_contratacao ='2' AND id_projeto ='$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_clt = mysql_num_rows($result_clt);

$result_cooperado = mysql_query("SELECT *,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo where status = '0'  AND tipo_contratacao ='3' AND id_projeto ='$projeto' ORDER BY year(data_saida),month(data_saida),day(data_saida) DESC");
$num_cooperado = mysql_num_rows($result_cooperado);

?>
<html><head><title>Intranet</title>
<style>
h1 { page-break-after: always }
</style>
<link href=\"net2.css\" rel=\"stylesheet\" type=\"text/css\">
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  <tr> 
    <td width="21%"><p class="MsoHeader" align="center" style='text-align:center'><span class="MsoHeader" style="text-align:center"><strong><span class="style5"><img src='imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' /><br />
    </span></strong><span style="font-size:10px">
    <?=$row_master['razao']?>
    </span></span></p></td>
    <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'><?php print "$row_pro[nome] <br> $row_pro[regiao] "; ?></span></b></p></td>
    <td width="21%">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3"><div align="center">
        <p><strong><br>
          <br>
          <font face="Arial, Helvetica, sans-serif" size="-1">
          RELAT&Oacute;RIO DE PARTICIPANTES DESLIGADOS DO PROJETO EM ORDEM ALFAB&Eacute;TICA<br>
          <br>
          TOTAL DE PARTICIPANTES:  <?php echo $num_row; ?>
          <br>
          <?php
	  
  print "
  <table width=95% cellpadding='0' cellspacing='0' border=1 bordercolor='#999999'>
  <tr height=25>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Nome</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Atividade</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Salário</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>CPF</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>RG</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Banco</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Agência</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>C.C.</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Data Saida</font></td>
  </tr>";


$cont = "0";
while($row_bolsista = mysql_fetch_array($result_bolsista)){

$result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_bolsista[id_curso]'");
$row_atividade = mysql_fetch_array($result_atividade);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row_bolsista[banco]'");
$row_banco = mysql_fetch_array($result_banco);


if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

if($row_bolsista['tipo_contratacao'] == "1"){
$contratacao = "Participante";
}elseif($row_bolsista['tipo_contratacao'] == "2"){
$contratacao = "CLT";
}else{
$contratacao = "Colaborador";
}

print "

<TR bgcolor=$color>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade[valor]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[cpf]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[rg]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_banco[nome]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[agencia]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[conta]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_bolsista[data_saida]</font></td>
</TR></font>";

$cont ++;	  
}
print "</TABLE><br><BR><br><Br>";

?> 
          </font></strong></p>
    </div></td>
  </tr>
</table>
</body>
</html>

<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
      </tr>
  <tr> 
    <td colspan="3"><div align="center">
        <p><strong><br>
          <br><font face="Arial, Helvetica, sans-serif" size="-1">
          RELAT&Oacute;RIO DE PARTICIPANTES DESLIGADOS DO PROJETO EM ORDEM ALFAB&Eacute;TICA<br>
          <br>
          TOTAL DE CLTs: <?php echo $num_clt; ?></font>
          <br>
          <?php
	  
  print "
  <table width=95% cellpadding='0' cellspacing='0' border=1 bordercolor='#999999'>
  <tr height=25>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Nome</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Atividade</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Salário</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>CPF</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>RG</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Banco</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Agência</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>C.C.</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Data Saída</font></td>
  </tr>";


$cont2 = "0";
while($row_clt = mysql_fetch_array($result_clt)){

$result_atividade2 = mysql_query("SELECT * FROM curso where id_curso = '$row_clt[id_curso]'");
$row_atividade2 = mysql_fetch_array($result_atividade2);

$result_banco2 = mysql_query("SELECT * FROM bancos where id_banco = '$row_clt[banco]'");
$row_banco2 = mysql_fetch_array($result_banco2);


if($cont2 % 2){ $color2="#f0f0f0"; }else{ $color2="#dddddd"; }

if($row_clt['tipo_contratacao'] == "1"){
$contratacao2 = "Participante";
}elseif($row_clt['tipo_contratacao'] == "2"){
$contratacao2 = "CLT";
}else{
$contratacao2 = "Colaborador";
}

print "
<TR bgcolor=$color2>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade2[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade2[valor]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[cpf]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[rg]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_banco2[nome]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[agencia]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[conta]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_clt[data_saida]</font></td>
</TR>";

$cont2 ++;	  
}
print "</TABLE><br><BR><br><Br>";

?> 


<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr> 
      </tr>
  <tr> 
    <td colspan="3"><div align="center">
        <p><strong><br>
          <br><font face="Arial, Helvetica, sans-serif" size="-1">
          RELAT&Oacute;RIO DE PARTICIPANTES DESLIGADOS DO PROJETO EM ORDEM ALFAB&Eacute;TICA<br>
          <br>
          TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></font>
          <br>
          <?php
	  
  print "
  <table width=95% cellpadding='0' cellspacing='0' border=1 bordercolor='#999999'>
  <tr height=25>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Nome</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Atividade</font></td>  
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Salário</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>CPF</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>RG</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Banco</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Agência</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>C.C.</font></td>
  <td background='layout/fundo_tab_azul.gif' align=center><font size=1 face=Arial>Data Saída</font></td>
  </tr>";


$cont3 = "0";
while($row_cooperado = mysql_fetch_array($result_cooperado)){

$result_atividade3 = mysql_query("SELECT * FROM curso where id_curso = '$row_cooperado[id_curso]'");
$row_atividade3 = mysql_fetch_array($result_atividade3);

$result_banco3 = mysql_query("SELECT * FROM bancos where id_banco = '$row_cooperado[banco]'");
$row_banco3 = mysql_fetch_array($result_banco3);


if($cont3 % 2){ $color3="#f0f0f0"; }else{ $color3="#dddddd"; }

if($row_cooperado['tipo_contratacao'] == "1"){
$contratacao3 = "Participante";
}elseif($row_cooperado['tipo_contratacao'] == "2"){
$contratacao3 = "CLT";
}else{
$contratacao3 = "Colaborador";
}

print "
<TR bgcolor=$color3>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade3[nome]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_atividade3[valor]</font></TD>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[cpf]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[rg]</font></td>
<TD><font face='Arial, Helvetica, sans-serif' size='-2'>$row_banco3[nome]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[agencia]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[conta]</font></td>
<td><font face='Arial, Helvetica, sans-serif' size='-2'>$row_cooperado[data_saida]</font></td>
</TR>";

$cont3 ++;	  
}
print "</TABLE><br><BR><br><Br>";

?> 

<?php


/* Liberando o resultado */

mysql_free_result($result_bolsista);
mysql_free_result($result_pro);

/* Fechando a conexão */
mysql_close($conn);

}
?>