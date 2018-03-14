<?php
include('include/restricoes.php');
include "../conn.php";
include "../classes/funcionario.php";

$user = new funcionario();
$user -> MostraUser(0);
$nome = $user -> nome1;
$regiao = $user -> id_regiao;

if($_POST['pronto'] == "login") {
	$qr_login = mysql_query("SELECT * FROM senhas WHERE id_senha = '5' AND senha = '$_POST[senha]'");
	$login = mysql_num_rows($qr_login);
	if(!empty($login)) {
		if(!isset($_SESSION['adm'])) {
		    session_start();
		    $_SESSION['adm'] = $_POST['usuario'];
		}
	    header("Location: index.php");
	} else {
		header("Location: login.php?senha=errada");
    }
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'>
<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	margin:0px;
}
</style>
</head>
<body>
<br><br><br>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center" valign="top">
<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1">
<br>
<div class="style3">PLANO DE CONTAS B&Aacute;SICO</div>
<table width="520" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tbody>
    <tr>
      <td width="72" valign="top"><p><strong>1</strong></p></td>
      <td width="448" valign="top"><p><strong>ATIVO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1</strong></p></td>
      <td width="448"><p><strong>ATIVO CIRCULANTE</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.1</strong></p></td>
      <td width="448"><p><strong>DISPON&Iacute;VEL</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.1.1</strong></p></td>
      <td width="448"><p><strong>CAIXA</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.1.1.01</p></td>
      <td width="448"><p>Caixa Geral</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.1.2</strong></p></td>
      <td width="448"><p><strong>BANCOS CONTA MOVIMENTO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.1.2.01</p></td>
      <td width="448"><p>Banco A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.1.3</strong></p></td>
      <td width="448" valign="top"><p><strong>APLICA&Ccedil;&Otilde;ES FINANCEIRAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.1.3.01</p></td>
      <td width="448" valign="top"><p>Banco A</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.2</strong></p></td>
      <td width="448"><p><strong>CONTAS A RECEBER</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.2.1</strong></p></td>
      <td width="448"><p><strong>CLIENTES</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.2.1.01</p></td>
      <td width="448"><p>Cliente A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.1.2.2&nbsp;<br>
      </strong></p></td>
      <td width="448" valign="top"><p><strong>(-) DUPLICATAS DESCONTADAS&nbsp;<br>
      </strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td>1.1.2.2.01</td>
      <td>Banco A</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.2.9</strong></p></td>
      <td width="448"><p><strong>OUTRAS CONTAS A RECEBER</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.2.9.01</p></td>
      <td width="448"><p>Conta A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.3</strong></p></td>
      <td width="448"><p><strong>ESTOQUES</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.1.3.1</strong></p></td>
      <td width="448" valign="top"><p><strong>MERCADORIAS PARA REVENDA</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.1.01</p></td>
      <td width="448"><p>Estoque Inicial</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.02</p></td>
      <td width="448" valign="top"><p>Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.03</p></td>
      <td width="448" valign="top"><p>Fretes e Carretos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.1.04</p></td>
      <td width="448"><p>ICMS &ndash; Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.05</p></td>
      <td width="448" valign="top"><p>ICMS &ndash; Antecipado</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.10</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Devolu&ccedil;&otilde;es de Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.11</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) ICMS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.12</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) COFINS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.13</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) PIS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.1.19</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Custo das Mercadorias Vendidas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.1.3.2</strong></p></td>
      <td width="448" valign="top"><p><strong>PRODUTOS ACABADOS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.2.01</p></td>
      <td width="448"><p>Estoque Inicial</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.2.02</p></td>
      <td width="448" valign="top"><p>Produ&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.2.09</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Custo dos Produtos Vendidos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td><strong>1.1.3.3</strong></td>
      <td><strong>MAT&Eacute;RIAS-PRIMAS</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.3.01</p></td>
      <td width="448"><p>Estoque Inicial</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.02</p></td>
      <td width="448" valign="top"><p>Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.03</p></td>
      <td width="448" valign="top"><p>Fretes e Carretos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.3.04</p></td>
      <td width="448"><p>ICMS &ndash; Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.05</p></td>
      <td width="448" valign="top"><p>ICMS &ndash; Antecipado</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.10</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Devolu&ccedil;&otilde;es de Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.11</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) ICMS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.12</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) COFINS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.13</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) PIS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.3.19</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Transfer&ecirc;ncia para Consumo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.1.3.4</strong></p></td>
      <td width="448" valign="top"><p><strong>MATERIAIS DE EMBALAGEM</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.4.01</p></td>
      <td width="448"><p>Estoque Inicial</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.02</p></td>
      <td width="448" valign="top"><p>Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.03</p></td>
      <td width="448" valign="top"><p>Fretes e Carretos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.4.04</p></td>
      <td width="448"><p>ICMS &ndash; Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.05</p></td>
      <td width="448" valign="top"><p>ICMS &ndash; Antecipado</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.10</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Devolu&ccedil;&otilde;es de Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.11</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) ICMS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.12</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) COFINS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.13</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) PIS sobre Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.4.19</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Transfer&ecirc;ncia para Consumo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.1.3.9</strong></p></td>
      <td width="448" valign="top"><p><strong>MATERIAIS DE CONSUMO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.9.01</p></td>
      <td width="448"><p>Estoque Inicial</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.9.02</p></td>
      <td width="448" valign="top"><p>Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.9.03</p></td>
      <td width="448" valign="top"><p>Fretes e Carretos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.3.9.04</p></td>
      <td width="448"><p>ICMS &ndash; Antecipado</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.9.10</p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Devolu&ccedil;&otilde;es de Compras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.3.9.19<br>
      </p></td>
      <td width="448" valign="top"><p>&nbsp;(-) Transfer&ecirc;ncia para Consumo&nbsp;<br>
      </p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td><strong>1.1.4</strong></td>
      <td><strong>OUTROS CR&Eacute;DITOS</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><strong>1.1.4.1</strong></td>
      <td width="448"><p><strong>IMPOSTOS A RECUPERAR</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.01</p></td>
      <td width="448"><p>IPI</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.02</p></td>
      <td width="448"><p>ICMS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.03</p></td>
      <td width="448"><p>ICMS Antecipado</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.4.1.04</p></td>
      <td width="448" valign="top"><p>COFINS</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.05</p></td>
      <td width="448"><p>PIS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.06</p></td>
      <td width="448"><p>IRPJ</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.07</p></td>
      <td width="448"><p>CSLL</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.4.1.08</p></td>
      <td width="448"><p>IRF</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>1.1.4.1.09</p></td>
      <td width="448" valign="bottom"><p>ISSF</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom"><strong>1.1.9</strong></td>
      <td width="448" valign="top"><p><strong>DESPESAS DO EXERC&Iacute;CIO SEGUINTE</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.1.9.1</strong></p></td>
      <td width="448"><p><strong>DESPESAS ANTECIPADAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.1.9.1.01</p></td>
      <td width="448"><p>Seguros a Apropriar</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.9.1.02</p></td>
      <td width="448" valign="top"><p>Encargos a Apropriar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.1.9.1.03&nbsp;<br>
      </p></td>
      <td width="448" valign="top"><p>IPTU a Apropriar&nbsp;<br>
      </p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td><strong>1.2</strong></td>
      <td valign="top"><strong>ATIVO REALIZ&Aacute;VEL A LONGO PRAZO</strong></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.2.1&nbsp;<br>
      </strong></p></td>
      <td width="448" valign="top"><p><strong>APLICA&Ccedil;&Otilde;ES FINANCEIRAS DE LONGO PRAZO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td><strong>1.2.1.1</strong></td>
      <td><strong>APLICA&Ccedil;&Otilde;ES FINANCEIRAS</strong></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.2.1.1.01</p></td>
      <td width="448"><p>Banco A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.2.2</strong></p></td>
      <td width="448"><p><strong>CONTAS A RECEBER</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.2.2.1</strong></p></td>
      <td width="448"><p><strong>CLIENTES</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.2.2.1.01</p></td>
      <td width="448"><p>Cliente A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3</strong></p></td>
      <td width="448"><p><strong>ATIVO PERMANENTE</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.1</strong></p></td>
      <td width="448"><p><strong>INVESTIMENTOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>PARTICIPA&Ccedil;&Otilde;ES SOCIET&Aacute;RIAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.1.1.01</p></td>
      <td width="448" valign="top"><p>Cons&oacute;rcio Simples A</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.1.1.02</p></td>
      <td width="448" valign="top"><p>Cooperativa de Cr&eacute;dito A</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.2</strong></p></td>
      <td width="448"><p><strong>IMOBILIZADO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.2.1</strong></p></td>
      <td width="448" valign="top"><p><strong>BENS EM OPERA&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.1.01</p></td>
      <td width="448" valign="top"><p>Terrenos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.1.02</p></td>
      <td width="448"><p>Constru&ccedil;&otilde;es e Benfeitorias</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.1.03</p></td>
      <td width="448" valign="top"><p>M&aacute;quinas, Aparelhos e Equipamentos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.1.04</p></td>
      <td width="448" valign="top"><p>Ferramentas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.1.05</p></td>
      <td width="448"><p>Matrizes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.1.06</p></td>
      <td width="448"><p>M&oacute;veis &amp; Utens&iacute;lios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.1.07</p></td>
      <td width="448"><p>Equipamentos de Inform&aacute;tica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.1.08</p></td>
      <td width="448" valign="top"><p>Instala&ccedil;&otilde;es Comerciais</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.1.09</p></td>
      <td width="448"><p>Ve&iacute;culos e Acess&oacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.2.9&nbsp;<br>
      </strong></p></td>
      <td width="448" valign="top"><p><strong>(-) DEPRECIA&Ccedil;&Otilde;ES ACUMULADAS&nbsp;<br>
      </strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">1.3.2.9.01</td>
      <td valign="top">Constru&ccedil;&otilde;es e Benfeitorias</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.9.02</p></td>
      <td width="448" valign="top"><p>M&aacute;quinas, Aparelhos e Equipamentos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.9.03</p></td>
      <td width="448" valign="top"><p>Ferramentas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.9.04</p></td>
      <td width="448"><p>Matrizes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.9.05</p></td>
      <td width="448"><p>M&oacute;veis &amp; Utens&iacute;lios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.9.06</p></td>
      <td width="448"><p>Equipamentos de Inform&aacute;tica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.2.9.07</p></td>
      <td width="448" valign="top"><p>Instala&ccedil;&otilde;es Comerciais</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.2.9.08</p></td>
      <td width="448"><p>Ve&iacute;culos e Acess&oacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>1.3.3</strong></p></td>
      <td width="448" valign="bottom"><p><strong>INTANG&Iacute;VEL</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.3.1</strong></p></td>
      <td width="448" valign="top"><p><strong>BENS INCORP&Oacute;REOS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>1.3.3.1.01</p></td>
      <td width="448"><p>Marcas e Patentes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.3.1.02</p></td>
      <td width="448" valign="top"><p>Sistemas Aplicativos (<em>softwares</em>)</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>1.3.3.9</strong></p></td>
      <td width="448" valign="top"><p><strong>(-) AMORTIZA&Ccedil;&Otilde;ES ACUMULADAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.3.9.01</p></td>
      <td width="448" valign="top"><p>Marcas e Patentes</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.3.9.02</p></td>
      <td width="448" valign="top"><p>Sistemas Aplicativos (<em>softwares</em>)</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.4</strong></p></td>
      <td width="448"><p><strong>DIFERIDO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>1.3.4.1</strong></p></td>
      <td width="448"><p><strong>GASTOS PR&Eacute;-OPERACIONAIS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>1.3.4.1.01</p></td>
      <td width="448" valign="top"><p>Gastos de Organiza&ccedil;&atilde;o e Administra&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>1.3.4.1.02</p></td>
      <td width="448" valign="bottom"><p>Projetos e Desenvolvimento de Novos Produtos</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>1.3.4.9</strong></p></td>
      <td width="448" valign="bottom"><p><strong>(-) AMORTIZA&Ccedil;&Otilde;ES ACUMULADAS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>1.3.4.9.01</p></td>
      <td width="448" valign="bottom"><p>Gastos de Organiza&ccedil;&atilde;o e Administra&ccedil;&atilde;o</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>1.3.4.9.02</p></td>
      <td width="448" valign="bottom"><p>Projetos e Desenvolvimento de Novos Produtos</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>2</strong></p></td>
      <td width="448" valign="bottom"><p><strong>PASSIVO</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.1</strong></p></td>
      <td width="448"><p><strong>CIRCULANTE</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.1.1</strong></p></td>
      <td width="448"><p><strong>CONTAS A PAGAR</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.1.1.1</strong></p></td>
      <td width="448"><p><strong>SAL&Aacute;RIOS A PAGAR</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.1.01</p></td>
      <td width="448"><p>Sal&aacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.1.02</p></td>
      <td width="448"><p>F&eacute;rias a Pagar</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.1.03</p></td>
      <td width="448" valign="top"><p>13&ordm; Sal&aacute;rio a Pagar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.1.1.2</strong></p></td>
      <td width="448" valign="top"><p><strong>OBRIGA&Ccedil;&Otilde;ES TRABALHISTAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.2.01</p></td>
      <td width="448" valign="top"><p>INSS a Recolher</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.2.02</p></td>
      <td width="448"><p>FGTS a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.2.03</p></td>
      <td width="448" valign="top"><p>Contribui&ccedil;&atilde;o Sindical a Recolher</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.1.1.3</strong></p></td>
      <td width="448" valign="top"><p><strong>OBRIGA&Ccedil;&Otilde;ES TRIBUT&Aacute;RIAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.3.01</p></td>
      <td width="448" valign="top"><p>Simples Nacional a Recolher</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.02</p></td>
      <td width="448"><p>IPI a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.03</p></td>
      <td width="448"><p>ICMS a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.04</p></td>
      <td width="448"><p>COFINS a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.05</p></td>
      <td width="448"><p>PIS a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.06</p></td>
      <td width="448"><p>IRPJ a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.07</p></td>
      <td width="448"><p>CSLL a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.08</p></td>
      <td width="448"><p>ISS a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.09</p></td>
      <td width="448"><p>IRF a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.10</p></td>
      <td width="448"><p>ISSF a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.3.11</p></td>
      <td width="448"><p>ICMS Substitui&ccedil;&atilde;o Tribut&aacute;ria a Recolher</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>2.1.1.4</strong></p></td>
      <td width="448" valign="bottom"><p><strong>FORNECEDORES</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.4.01</p></td>
      <td width="448" valign="top"><p>Fornecedor A</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.1.1.5</strong></p></td>
      <td width="448"><p><strong>EMPR&Eacute;STIMOS BANC&Aacute;RIOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.5.01</p></td>
      <td width="448"><p>Banco A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.1.1.6</strong></p></td>
      <td width="448" valign="top"><p><strong>(-) ENCARGOS FINANCEIROS A TRANSCORRER</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.6.01</p></td>
      <td width="448"><p>Juros Passivos</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.1.1.9</strong></p></td>
      <td width="448"><p><strong>OUTRAS CONTAS A PAGAR</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.1.1.9.01</p></td>
      <td width="448"><p>Alugu&eacute;is a Pagar</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.9.02</p></td>
      <td width="448" valign="top"><p>Energia El&eacute;trica a Pagar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.9.03</p></td>
      <td width="448" valign="top"><p>Telefone a Pagar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.9.04</p></td>
      <td width="448" valign="top"><p>&Aacute;gua e Esgotos a Pagar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.1.1.9.05</p></td>
      <td width="448" valign="top"><p>Pr&oacute;-labore a Pagar</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.2</strong></p></td>
      <td width="448"><p><strong>EXIG&Iacute;VEL A LONGO PRAZO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.2.1</strong></p></td>
      <td width="448"><p><strong>CONTAS A PAGAR</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.2.1.1</strong></p></td>
      <td width="448"><p><strong>FINANCIAMENTOS BANC&Aacute;RIOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.2.1.1.01</p></td>
      <td width="448"><p>Banco A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.3</strong></p></td>
      <td width="448"><p><strong>RESULTADOS DE EXERC&Iacute;CIOS FUTUROS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.3.1</strong></p></td>
      <td width="448"><p><strong>RESULTADOS DIFERIDOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.3.1.1</strong></p></td>
      <td width="448"><p><strong>RECEITAS DIFERIDAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.3.1.1.01</p></td>
      <td width="448"><p>Receitas de Obras em Andamento</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.3.1.2</strong></p></td>
      <td width="448" valign="top"><p><strong>(-) CUSTOS DIFERIDOS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.3.1.2.01</p></td>
      <td width="448" valign="top"><p>Custos de Obras em Andamento</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.3.1.3</strong></p></td>
      <td width="448" valign="top"><p><strong>(-) DESPESAS DIFERIDAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.3.1.3.01</p></td>
      <td width="448" valign="top"><p>Despesas de Obras em Andamento</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.4</strong></p></td>
      <td width="448" valign="top"><p><strong>PATRIM&Ocirc;NIO L&Iacute;QUIDO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.4.1</strong></p></td>
      <td width="448" valign="top"><p><strong>CAPITAL SOCIAL REALIZADO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.1.1</strong></p></td>
      <td width="448"><p><strong>CAPITAL SOCIAL SUBSCRITO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.4.1.1.01</p></td>
      <td width="448"><p>Capital Nacional</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.4.1.2</strong></p></td>
      <td width="448" valign="top"><p><strong>(-) CAPITAL SOCIAL A REALIZAR</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.4.1.2.01</p></td>
      <td width="448"><p>S&oacute;cio A</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.2</strong></p></td>
      <td width="448"><p><strong>RESERVAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.2.1</strong></p></td>
      <td width="448"><p><strong>RESERVAS DE CAPITAL</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.4.2.1.01</p></td>
      <td width="448"><p>Reserva de Incentivos Fiscais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.2.2</strong></p></td>
      <td width="448" valign="top"><p><strong>AJUSTES DE AVALIA&Ccedil;&Atilde;O PATRIMONIAL</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.4.2.2.01</p></td>
      <td width="448" valign="top"><p>Varia&ccedil;&otilde;es de Elementos Ativos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.4.2.2.02</p></td>
      <td width="448" valign="top"><p>Varia&ccedil;&otilde;es de Elementos Passivos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.2.3</strong></p></td>
      <td width="448"><p><strong>RESERVAS DE LUCROS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.4.2.3.01</p></td>
      <td width="448"><p>Reten&ccedil;&otilde;es de Lucros</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>2.4.2.3.02</p></td>
      <td width="448" valign="bottom"><p>Lucros a Realizar</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.4.3</strong></p></td>
      <td width="448" valign="top"><p><strong>QUOTAS EM TESOURARIA</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>2.4.3.1</strong></p></td>
      <td width="448" valign="top"><p><strong>QUOTAS EM TESOURARIA</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.4.3.1.01</p></td>
      <td width="448" valign="top"><p>Quotas de Capital Realizado</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.4</strong></p></td>
      <td width="448"><p><strong>LUCROS OU PREJUIZOS ACUMULADOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>2.4.4.1</strong></p></td>
      <td width="448"><p><strong>LUCROS OU PREJUIZOS ACUMULADOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>2.4.4.1.01</p></td>
      <td width="448"><p>Lucros Acumulados</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>2.4.4.1.02</p></td>
      <td width="448" valign="top"><p>Preju&iacute;zos Acumulados</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>3</strong></p></td>
      <td width="448" valign="bottom"><p><strong>CUSTOS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1</strong></p></td>
      <td width="448" valign="top"><p><strong>CUSTOS DE PRODU&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>3.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>CUSTOS INDUSTRIAIS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.1.1</strong></p></td>
      <td width="448"><p><strong>INSUMOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.1.01</p></td>
      <td width="448" valign="top"><p>Mat&eacute;rias-primas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.1.02</p></td>
      <td width="448"><p>Materiais de embalagem</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.1.2</strong></p></td>
      <td width="448"><p><strong>M&Atilde;O-DE-OBRA DIRETA</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.2.01</p></td>
      <td width="448"><p>Sal&aacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.2.02</p></td>
      <td width="448"><p>Encargos Sociais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.2.03</p></td>
      <td width="448" valign="top"><p>Vale Transporte</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.2.04</p></td>
      <td width="448"><p>Refei&ccedil;&otilde;es</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.2.05</p></td>
      <td width="448"><p>Uniformes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.2.06</p></td>
      <td width="448"><p>Assist&ecirc;ncia M&eacute;dica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.1.3</strong></p></td>
      <td width="448"><p><strong>OUTROS CUSTOS DIRETOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.3.01</p></td>
      <td width="448"><p>Materiais de consumo</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.1.9</strong></p></td>
      <td width="448" valign="top"><p><strong>CUSTOS INDIRETOS DE FABRICA&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.01</p></td>
      <td width="448" valign="top"><p>Sal&aacute;rios</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.9.02</p></td>
      <td width="448"><p>Encargos Sociais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.03</p></td>
      <td width="448" valign="top"><p>Vale Transporte</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.9.04</p></td>
      <td width="448"><p>Refei&ccedil;&otilde;es</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.9.05</p></td>
      <td width="448"><p>Uniformes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.1.9.06</p></td>
      <td width="448"><p>Assist&ecirc;ncia M&eacute;dica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.10</p></td>
      <td width="448" valign="top"><p>Energia el&eacute;trica</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.11</p></td>
      <td width="448" valign="top"><p>Manuten&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.12</p></td>
      <td width="448" valign="top"><p>Aluguel de bens im&oacute;veis</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.13</p></td>
      <td width="448" valign="top"><p>Loca&ccedil;&atilde;o de bens m&oacute;veis</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.14</p></td>
      <td width="448" valign="top"><p>&Aacute;gua e Esgoto</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.15</p></td>
      <td width="448" valign="top"><p>Materiais de consumo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.90</p></td>
      <td width="448" valign="top"><p>Pr&ecirc;mios de Seguro</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.1.9.91</p></td>
      <td width="448" valign="top"><p>Deprecia&ccedil;&atilde;o e Amortiza&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>3.1.2</strong></p></td>
      <td width="448" valign="top"><p><strong>CUSTOS DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>3.1.2.1</strong></p></td>
      <td width="448" valign="top"><p><strong>CONSUMO DE MATERIAIS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.1.01</p></td>
      <td width="448" valign="top"><p>Materiais Aplicados</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.2.2</strong></p></td>
      <td width="448"><p><strong>M&Atilde;O-DE-OBRA DIRETA</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.2.01</p></td>
      <td width="448"><p>Sal&aacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.2.02</p></td>
      <td width="448" valign="top"><p>Encargos Sociais</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.2.03</p></td>
      <td width="448"><p>Vale Transporte</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.2.04</p></td>
      <td width="448"><p>Refei&ccedil;&otilde;es</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.2.05</p></td>
      <td width="448"><p>Uniformes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.2.06</p></td>
      <td width="448"><p>Assist&ecirc;ncia M&eacute;dica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.2.3</strong></p></td>
      <td width="448"><p><strong>OUTROS CUSTOS DIRETOS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.3.01</p></td>
      <td width="448"><p>Materiais de consumo</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>3.1.2.9</strong></p></td>
      <td width="448" valign="top"><p><strong>CUSTOS INDIRETOS DE PRESTA&Ccedil;&Atilde;O DE SERVI&Ccedil;OS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.9.01</p></td>
      <td width="448" valign="top"><p>Sal&aacute;rios</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.9.02</p></td>
      <td width="448" valign="top"><p>Encargos Sociais</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.03</p></td>
      <td width="448"><p>Vale Transporte</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.04</p></td>
      <td width="448"><p>Refei&ccedil;&otilde;es</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.05</p></td>
      <td width="448"><p>Uniformes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.06</p></td>
      <td width="448"><p>Assist&ecirc;ncia M&eacute;dica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.10</p></td>
      <td width="448"><p>Energia el&eacute;trica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.9.11</p></td>
      <td width="448" valign="top"><p>Manuten&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.9.12</p></td>
      <td width="448" valign="top"><p>Aluguel de bens im&oacute;veis</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.13</p></td>
      <td width="448"><p>Loca&ccedil;&atilde;o de bens m&oacute;veis</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.14</p></td>
      <td width="448"><p>&Aacute;gua e Esgoto</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.15</p></td>
      <td width="448"><p>Materiais de consumo</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>3.1.2.9.16</p></td>
      <td width="448"><p>Ferramentas</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>3.1.2.9.90</p></td>
      <td width="448" valign="top"><p>Pr&ecirc;mios de Seguro</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>3.1.2.9.91</p></td>
      <td width="448" valign="top"><p>Deprecia&ccedil;&atilde;o e Amortiza&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>4</strong></p></td>
      <td width="448" valign="top"><p><strong>PRODU&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>4.1</strong></p></td>
      <td width="448" valign="top"><p><strong>PRODU&Ccedil;&Acirc;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>4.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>PRODU&Ccedil;&Acirc;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>4.1.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>PRODU&Ccedil;&Acirc;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>4.1.1.1.01</p></td>
      <td width="448" valign="top"><p>De Bens</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>4.1.1.1.02</p></td>
      <td width="448" valign="top"><p>De Servi&ccedil;os</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>5</strong></p></td>
      <td width="448" valign="bottom"><p><strong>DESPESAS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>5.1</strong></p></td>
      <td width="448" valign="bottom"><p><strong>DESPESAS DIVERSAS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>5.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>DESPESAS OPERACIONAIS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.1</strong></p></td>
      <td width="448"><p><strong>CUSTO DAS VENDAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.1.01</p></td>
      <td width="448"><p>Custo das Mercadorias Vendidas</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.1.02</p></td>
      <td width="448"><p>Custo dos Produtos Vendidos</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.1.03</p></td>
      <td width="448"><p>Custo dos Servi&ccedil;os Prestados</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.2</strong></p></td>
      <td width="448"><p><strong>DESPESAS COM PESSOAL</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.2.01</p></td>
      <td width="448"><p>Sal&aacute;rios</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.2.02</p></td>
      <td width="448"><p>Encargos Sociais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.2.03</p></td>
      <td width="448" valign="top"><p>Vale Transporte</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.2.04</p></td>
      <td width="448" valign="top"><p>Refei&ccedil;&otilde;es</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.2.05</p></td>
      <td width="448"><p>Uniformes</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.2.06</p></td>
      <td width="448"><p>Assist&ecirc;ncia M&eacute;dica</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.3</strong></p></td>
      <td width="448"><p><strong>DESPESAS ADMINISTRATIVAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.01</p></td>
      <td width="448"><p>Pr&oacute;-labore</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.02</p></td>
      <td width="448"><p>Aluguel de Im&oacute;veis</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.3.03</p></td>
      <td width="448" valign="top"><p>Loca&ccedil;&atilde;o de Bens</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.3.04</p></td>
      <td width="448" valign="top"><p>Energia El&eacute;trica</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.05</p></td>
      <td width="448"><p>Telefone e Internet</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.06</p></td>
      <td width="448" valign="top"><p>&Aacute;gua e Esgoto</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.3.07</p></td>
      <td width="448" valign="top"><p>Tarifas Banc&aacute;rias</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.08</p></td>
      <td width="448"><p>Material de Consumo</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.3.09</p></td>
      <td width="448"><p>Material de Expediente</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.3.10</p></td>
      <td width="448" valign="top"><p>Correios</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.4</strong></p></td>
      <td width="448" valign="top"><p><strong>DESPESAS DE COMERCIALIZA&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.4.01</p></td>
      <td width="448" valign="top"><p>Fretes e Carretos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.4.02</p></td>
      <td width="448"><p>Comiss&otilde;es e Corretagens</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.4.03</p></td>
      <td width="448" valign="top"><p>Despesas de Viagens e Estadas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.5</strong></p></td>
      <td width="448"><p><strong>DESPESAS TRIBUT&Aacute;RIAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.5.01</p></td>
      <td width="448"><p>IPTU</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.5.02</p></td>
      <td width="448"><p>IPVA</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.5.03</p></td>
      <td width="448"><p>IOF</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.5.04</p></td>
      <td width="448"><p>Multas Fiscais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.5.05</p></td>
      <td width="448"><p>COFINS s/Outras Receitas</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.5.06</p></td>
      <td width="448" valign="top"><p>PIS s/Outras Receitas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.5.07</p></td>
      <td width="448" valign="top"><p>IRPJ s/Aplica&ccedil;&otilde;es Financeiras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.5.08</p></td>
      <td width="448" valign="top"><p>Impostos e Taxas Diversas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>5.1.1.6</strong></p></td>
      <td width="448" valign="top"><p><strong>DESPESAS FINANCEIRAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>5.1.1.6.01</p></td>
      <td width="448" valign="bottom"><p>Juros Passivos</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.6.02</p></td>
      <td width="448" valign="top"><p>Juros de Mora</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.6.03</p></td>
      <td width="448"><p>Descontos Concedidos</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.6.04</p></td>
      <td width="448"><p>Varia&ccedil;&otilde;es Monet&aacute;rias Passivas</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.6.05</p></td>
      <td width="448" valign="top"><p>Varia&ccedil;&otilde;es Cambiais Passivas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>5.1.1.7</strong></p></td>
      <td width="448" valign="top"><p><strong>DEPRECIA&Ccedil;&Atilde;O E AMORTIZA&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.7.01</p></td>
      <td width="448" valign="top"><p>Deprecia&ccedil;&atilde;o</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.1.7.02</p></td>
      <td width="448"><p>Amortiza&ccedil;&atilde;o</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.1.8</strong></p></td>
      <td width="448"><p><strong>PERDAS DIVERSAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.1.8.01</p></td>
      <td width="448" valign="top"><p>Perdas por Insolv&ecirc;ncia</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.2</strong></p></td>
      <td width="448"><p><strong>DESPESAS N&Atilde;O OPERACIONAIS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>5.1.2.1</strong></p></td>
      <td width="448"><p><strong>DESPESAS DIVERSAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.2.1.01</p></td>
      <td width="448"><p>Multas de Tr&acirc;nsito</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>5.1.2.1.02</p></td>
      <td width="448"><p>Multas Fiscais</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>5.1.2.1.03</p></td>
      <td width="448" valign="top"><p>Gastos com Festividades</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>6</strong></p></td>
      <td width="448" valign="bottom"><p><strong>RECEITAS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1</strong></p></td>
      <td width="448"><p><strong>RECEITAS DIVERSAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1.1</strong></p></td>
      <td width="448"><p><strong>RECEITAS OPERACIONAIS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1.1.1</strong></p></td>
      <td width="448"><p><strong>RECEITA BRUTA DE VENDAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.1.01</p></td>
      <td width="448"><p>Vendas de Mercadorias</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.1.02</p></td>
      <td width="448"><p>Vendas de Mercadorias com Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.03</p></td>
      <td width="448" valign="top"><p>Vendas de Mercadorias para o Exterior</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.10</p></td>
      <td width="448" valign="top"><p>Vendas de Produtos de Fabrica&ccedil;&atilde;o Pr&oacute;pria</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.11</p></td>
      <td width="448" valign="top"><p>Vendas de Produtos de Fabrica&ccedil;&atilde;o Pr&oacute;pria com Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.12</p></td>
      <td width="448" valign="top"><p>Vendas de Produtos de Fabrica&ccedil;&atilde;o Pr&oacute;pria para o Exterior</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.20</p></td>
      <td width="448" valign="top"><p>Vendas de Servi&ccedil;os Prestados</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.21</p></td>
      <td width="448" valign="top"><p>Vendas de Servi&ccedil;os Prestados com Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.1.22</p></td>
      <td width="448" valign="top"><p>Vendas de Servi&ccedil;os Prestados para o Exterior</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1.1.2</strong></p></td>
      <td width="448"><p><strong>(-) DEDU&Ccedil;&Otilde;ES DA RECEITA BRUTA</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.01</p></td>
      <td width="448"><p>Simples Nacional</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.02</p></td>
      <td width="448"><p>ISS Substitui&ccedil;&atilde;o Tribut&aacute;ria</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.03</p></td>
      <td width="448"><p>ICMS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.04</p></td>
      <td width="448"><p>ISS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.05</p></td>
      <td width="448"><p>COFINS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.06</p></td>
      <td width="448"><p>PIS</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>6.1.1.2.09</p></td>
      <td width="448"><p>Devolu&ccedil;&otilde;es de Vendas</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>6.1.1.3</strong></p></td>
      <td width="448" valign="bottom"><p><strong>RECEITAS FINANCEIRAS</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.3.01</p></td>
      <td width="448" valign="top"><p>Juros Ativos</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.3.02</p></td>
      <td width="448" valign="top"><p>Rendimentos de Aplica&ccedil;&otilde;es Financeiras</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>6.1.1.4</strong></p></td>
      <td width="448" valign="top"><p><strong>RECEITAS DIVERSAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.1.4.01</p></td>
      <td width="448" valign="top"><p>Recupera&ccedil;&atilde;o de Despesas</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1.2</strong></p></td>
      <td width="448"><p><strong>RECEITAS N&Atilde;O OPERACIONAIS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>6.1.2.1</strong></p></td>
      <td width="448"><p><strong>RECEITAS DIVERSAS</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>6.1.2.1.01</p></td>
      <td width="448" valign="top"><p>Ganhos de Capital</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>6.1.2.1.02</p></td>
      <td width="448" valign="bottom"><p>Outras Receitas</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
      <td valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p><strong>7</strong></p></td>
      <td width="448" valign="bottom"><p><strong>CONTAS DE APURA&Ccedil;&Atilde;O</strong></p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>7.1</strong></p></td>
      <td width="448" valign="top"><p><strong>CONTAS DIVERSAS</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>7.1.1</strong></p></td>
      <td width="448"><p><strong>BALAN&Ccedil;O</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>7.1.1.1</strong></p></td>
      <td width="448" valign="top"><p><strong>BALAN&Ccedil;O DE ABERTURA</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>7.1.1.1.01</p></td>
      <td width="448" valign="top"><p>Ativo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>7.1.1.1.02</p></td>
      <td width="448" valign="top"><p>(-) Passivo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p><strong>7.1.1.2</strong></p></td>
      <td width="448" valign="top"><p><strong>BALAN&Ccedil;O DE ENCERRAMENTO</strong></p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>7.1.1.2.01</p></td>
      <td width="448" valign="top"><p>Ativo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="top"><p>7.1.1.2.01</p></td>
      <td width="448" valign="top"><p>(-) Passivo</p></td>
      <td width="448" valign="top">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p><strong>7.1.2</strong></p></td>
      <td width="448"><p><strong>RESULTADO</strong></p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72"><p>7.1.2.1</p></td>
      <td width="448"><p>RESULTADO DO EXERC&Iacute;CIO</p></td>
      <td width="448">&nbsp;</td>
    </tr>
    <tr>
      <td width="72" valign="bottom"><p>7.1.2.1.01</p></td>
      <td width="448" valign="bottom"><p>Resultado Final de Exerc&iacute;cio</p></td>
      <td width="448" valign="bottom">&nbsp;</td>
    </tr>
  </tbody>
</table>
<p>&nbsp;</p>
</form>
</td>
</tr>
</table>
</body>
</html>