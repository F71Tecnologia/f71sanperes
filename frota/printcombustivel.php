<?php
if(empty($_COOKIE['logado'])){
print "<html><head><title>:: Financeiro ::</title>
<link href='../net1.css' rel='stylesheet' type='text/css'>
<body>
<font color=#FFFFFF>
<br><center><h1>Desculpe!</h1><br>Você não tem permissão para ver está página.</conter>
</font></body></html>
";
exit;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re = mysql_fetch_array($result_re);


$idCombustivel = $_REQUEST['com'];


$RE_combustivel = mysql_query("SELECT * FROM fr_combustivel WHERE id_combustivel = '$idCombustivel'");
$Row_comb = mysql_fetch_array($RE_combustivel);

$RE_carros = mysql_query("SELECT * FROM fr_carro WHERE id_carro = '$Row_comb[id_carro]'");
$RowCarros = mysql_fetch_array($RE_carros);


if($Row_comb['funcionario'] == 2){ //FUNCIONARIO EXTERNO ( NÃO ESTA CADASTRADO NA TABELA FUNCIONARIOS )
	$NOME = $Row_comb['nome'];
	$RG = $Row_comb['rg'];
}else{//FUNCIONARIO INTERNO ( SELECIONAMOS O NOME E O CPF DELE CADASTRADO NA BASE DE DADOS )
	$REUser = mysql_query("SELECT nome,rg FROM funcionario where id_funcionario = '$Row_comb[id_user]'");
	$RowUser = mysql_fetch_array($REUser);
	$NOME = $RowUser['nome'];
	$RG = $RowUser['rg'];
}

if($Row_comb['interno'] == 2){
	$PlAca = "$Row_comb[placa]";
	$CaRRo = "$Row_comb[carro]";
}else{
   $PlAca = "$RowCarros[placa]";
   $CaRRo = "$RowCarros[marca] $RowCarros[modelo]";
}

if($Row_comb['valor'] == "0.00"){ //VALOR NÃO DEFINIDO
	$valor = "";
}else{
	$valor = "VALOR: $Row_comb[valor]";
}
	

$dia = date('d');
$mes_h = date('m');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Autoriza&ccedil;&atilde;o</title>
<style type="text/css" media="print"> 
.noprint
{ 
   display: none; 
} 
</style>

<style type="text/css">
<!--
body {
	font-family: Arial, Helvetica, sans-serif;
	background:#EAEAEA;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>

<body>
<body onload="javascript:window.print()">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666">
      <tr>
        <td width="21%" height="139"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5"> <br />
          <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' width='120' height='86' /><br />
          </span></strong><span style='font-size:10px'>
            <?=$row_master['razao']?>
          </span></p></td>
        <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'></span></b><strong>AUTORIZA&Ccedil;&Atilde;O</strong><br />
          <strong>N&uacute;mero: <?=$Row_comb['numero']?></strong></p></td>
        <td width="21%">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:15px" align="justify"><br />
            <br />
            <br />
            <br />
<br />
            <br />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;O<span style="font-size:10px">
            <?=$row_master['razao']?>
            </span>, sediado  na Av. S&atilde;o Luis, 112 &ndash; 18&ordf; andar, conjunto 1802 &ndash; Republica. Inscrito no CNJP  06.888.897/0001-18, Autoriza o SR(a). <strong><?=$NOME?></strong>,  portador do RG n&ordm; <strong><?=$RG?></strong> a abastecer o ve&iacute;iculo detalhado abaixo.<br />
            <strong><br />
            <br />
            <br />
            MODELO:&nbsp;
            <?=$CaRRo?>
            <br />
            PLACA: <?=$PlAca?></strong><br />
            <br />
            <br />
			<strong><?=$valor?></strong>
            <br />
            <br />
            <br />
            Sem mais,<br />
            <br />
            <br />
            <br />
            <br />
<br />
            <br />
            <br />
            <br />
            <div align="center"><?=$row_re['0'].", ".$dia." de ".$mes." de ".$ano?><br />
              <br />
              _________________________________________________<br />
            </div><br />
            <br />
            <br />

            <?php

include('../empresa.php');
$rod = new empresa();
$rod -> endereco("#999","10 px");
?>
            <!--
            <div align="center" style="font-family:Arial, Helvetica, sans-serif; font-size:10px; color:#999">
            Av. S&atilde;o Lu&iacute;s 112 &ndash; Cj. 1802 &ndash; CEP 01046-000  &ndash; S&atilde;o Paulo/SP &ndash; Tel.: 11 3255 6959<br />
            R. Assembl&eacute;ia, 10 &ndash; Cj. 2617 &ndash; CEP  20011-901 &ndash; Rio de Janeiro/RJ &ndash; Tel.: 21 2509 0317<br />
            www.sorrindo.org.br<br />
            </div> -->
            <br />
            </div>
          &nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'><a href="../financeiro/novofinanceiro.php?regiao=<?=$regiao?>" style="text-decoration:none; color:#000">Voltar ao Financeiro</a></td>
  </tr>
</table>
</body>
</html>