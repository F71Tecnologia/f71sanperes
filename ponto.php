<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

include "conn.php";
include "classes/funcionario.php";

$id = $_REQUEST['id'];

switch ($id){
case 1:												//1ª VISUALIZAÇÃO - ESCOLHER AS DATAS DA CONSULTA

$user = $_REQUEST['user'];
$id_reg = $_REQUEST['id_reg'];

$clasuser = new funcionario();
$clasuser -> MostraUser($user);

//DECLARANDO AS VARIAVEIS
$nome = $clasuser -> nome;

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
<!--
body{
	background-color:#CCD9DD;
	font-family:Arial, Helvetica, sans-serif;
	
}
-->
</style>
<script language="javascript" src="js/ramon.js"></script>
</head>
<body bgcolor='#FFFFFF'>
<form action='ponto.php' method='post' name='form1'>
  <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor="#CCCCCC" align='center' style="border:#666 solid 1px;">
    <tr>
      <td height="31" colspan='2' align='center' bgcolor="#666666"><div style="color:#FFF; font-weight:bold;">Funcionário</div></td>
    </tr>
    <tr>
      <td height="31" colspan='2' align='center'><font size=3> <?=$nome?> </font></td>
    </tr>
    <tr>
      <td colspan='2' align='center'><div style="font-size:12px;">Escolha as datas de referência para o relatório de ponto</div></td>
    </tr>
    <tr>
      <td width='136'>&nbsp;</td>
      <td width='318'>&nbsp;</td>
    </tr>
    <tr>
      <td height="37" colspan="2" align='center' valign="middle">
      <div style="font-size:12px;">
      Inicio:&nbsp;
<input name='ini' type='text' class='campotexto' id='ini' 
style='background:#FFFFFF;'
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'"
onKeyUp="mascara_data(this); pula(10,this.id,fim.id)" size='11' maxlength='10'>
</div>
</td>
    </tr>
    <tr>
      <td height="38" colspan="2" align='center' valign="middle">
      <div style="font-size:12px;">
      Fim:&nbsp;&nbsp;&nbsp;
      <input name='fim' type='text' class='campotexto' id='fim' 
style='background:#FFFFFF;'
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'"
onKeyUp="mascara_data(this); pula(10,this.id,fim.id)" size='11' maxlength='10'>
</div>
</td>
    </tr>
    <tr>
      <td height="48" colspan="2" align='center' bgcolor="#666666">&nbsp;
        <input type='hidden' name='id' value='2'>
        <input type='hidden' name='regiao' value='<?=$id_reg?>'>
        <input type='hidden' name='user' value='<?=$user?>'>
      <input type='submit' name='Submit' value='Ver Ponto'></td>
    </tr>
  </table>
</form>
<?php

break;

case 2:

$user = $_REQUEST['user'];
$id_reg = $_REQUEST['regiao'];

$dataINI = $_REQUEST['ini'];
$dataFIM = $_REQUEST['fim'];

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/
function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$data_ini = ConverteData($dataINI);
$data_fim = ConverteData($dataFIM);

$clasuser = new funcionario();
$clasuser -> MostraUser($user);

//DECLARANDO AS VARIAVEIS
$nome = $clasuser -> nome;
$regiao = $clasuser -> regiao;
$funcao = $clasuser -> funcao;

?>
<div class=Section1>
  <div align=center>
    <table border=1 cellspacing=0 cellpadding=0 bgcolor="#FFFFFF">
      <tr>
        <td colspan=10 valign=top><p align=center style='
  text-align:center;line-height:normal'><b><i><span style='font-size:13.0pt'>FOLHA
            DE PONTO</span></i></b></p></td>
      </tr>
      <tr>
        <td colspan=5 valign=top><p style='line-height:normal'>NOME:
            <?=$nome?>
          </p></td>
        <td colspan=5 valign=top><p style='line-height: normal'>LOCAL:
            <?=$regiao?>
          </p></td>
      </tr>
      <tr>
        <td colspan=5 valign=top><p style='line-height:
  normal'>CARGO: <span style="line-height:
  normal">
            <?=$funcao?>
            </span></p></td>
        <td colspan=5 valign=top><p style='line-height:
  normal'>MÊS DE REFERÊNCIA:
            <?=$dataINI." - ".$dataFIM?>
          </p></td>
      </tr>
      <tr>
        <td width=106 valign=top>&nbsp;</td>
        <td width=94 align="center" valign="middle" bgcolor="#F2F2F2"><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>ENTRADA</span></b></p></td>
        <td width=94><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>JUSTIFICATIVA</span></b></p></td>
        <td width=94 bgcolor="#F2F2F2"><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>SAÍDA
            ALMOÇO</span></b></p></td>
        <td width=94><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>JUSTIFICATIVA</span></b></p></td>
        <td width=166 bgcolor="#F2F2F2"><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>RETORNO
            ALMOÇO</span></b></p></td>
        <td width=132><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>JUSTIFICATIVA</span></b></p></td>
        <td width=94 bgcolor="#F2F2F2"><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>SAÍDA</span></b></p></td>
        <td width=94><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>JUSTIFICATIVA</span></b></p></td>
        <td width=94 bgcolor="#F2DBDB"><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>TOTAL
            HORAS</span></b></p></td>
      </tr>
      <?php
$RE =  mysql_query("SELECT *,date_format(data, '%d/%m/%y')as data2 FROM ano WHERE data >= '$data_ini' and data <= '$data_fim' order by data");

while($row = mysql_fetch_array($RE)){

$result_ponto = mysql_query("SELECT *,date_format(data, '%d/%m/%y')as nova_data FROM ponto where 
id_funcionario = '$user' and id_regiao = '$id_reg' and data = '$row[data]'");

$row_ponto = mysql_fetch_array($result_ponto);
$Numponto = mysql_num_rows($result_ponto);

if($Numponto == 0){
	
	if($row['nome'] == "Sábado" or $row['nome'] == "Domingo"){
		$corDia = "";
	}else{
		$corDia = "#CC3300";
	}
	
	$entrada1 = "-";
	$justifica1 = "-";
	$saida1 = "-";
	$justifica2 = "-";
	$entrada2 = "-";
	$justifica3 = "-";
	$saida2 = "-";
	$justifica4 = "-";
}else{
	$corDia = "";
	$entrada1 = $row_ponto['entrada1'];
	$justifica1 = $row_ponto['justifica1']."&nbsp;";
	
	$saida1 = $row_ponto['saida1'];
	$justifica2 = $row_ponto['justifica2']."&nbsp;";
	
	$entrada2 = $row_ponto['entrada2'];
	$justifica3 = $row_ponto['justifica3']."&nbsp;";
	
	$saida2 = $row_ponto['saida2'];
	$justifica4 = $row_ponto['justifica4']."&nbsp;";
}

if($row['nome'] == "Sábado" or $row['nome'] == "Domingo"){ $cor = "#F2F2F2"; } else { $cor = ""; };

print"
 <tr bgcolor='$cor'>
  <td width=106 valign=top align='center' valign='middle' bgcolor='$corDia'>";
  
  echo $row['data2']."<br>".$row['nome'];
  
  print "</td>
  <td width=94 valign=top  bgcolor='$corDia' align='center' valign='middle'>";
  
  echo $entrada1;
  
  print "</td>
  <td width=94 valign=top align='center' bgcolor='$corDia' valign='middle'>";
  
  echo $justifica1;
  
  print "</td>
  <td width=94 valign=top bgcolor='$corDia' align='center' valign='middle'>";
  
  echo $saida1;
  
  print "</td>
  <td width=94 valign=top align='center' valign='middle' bgcolor='$corDia'>";
  
  echo $justifica2;
  
  print "</td>
  <td width=166 valign=top bgcolor='$corDia' align='center' valign='middle'>";
  
  echo $entrada2;
  
  print " </td >
  <td width=132 valign=top align='center' valign='middle' bgcolor='$corDia'>";
  
  echo $justifica3;
  
  print "</td>
  <td width=94 valign=top bgcolor='$corDia' align='center' valign='middle'>";
  
  echo $saida2;
  
  print "</td>
  <td width=94 valign=top align='center' valign='middle' bgcolor='$corDia'>";
  
  echo $justifica4;
  
  print "</td>
  <td width=94 valign=top bgcolor='#F2DBDB' align='center' valign='middle'>&nbsp;</td>
 </tr>";



	unset($entrada1);
	unset($justifica1);
	unset($saida1);
	unset($justifica2);
	unset($entrada2);
	unset($justifica3);
	unset($saida2);
	unset($justifica4);
 }

?>
      <tr>
        <td colspan=8 valign=top><p style='line-height:
  normal'><span style='font-size:10.0pt'><br>
                           _______________________________________________<br>
                                                                       ASSINATURA</span></p></td>
        <td width=94 valign=top><p align=center style='
  text-align:center;line-height:normal'><b><span style='font-size:10.0pt'>TOTAL
            DE HORAS TRABALHADAS</span></b></p></td>
        <td width=94 valign=top bgcolor="#F2DBDB">&nbsp;</td>
      </tr>
    </table>
  </div>
</div>
</body>
</html>
<?php
break;

}

?>
