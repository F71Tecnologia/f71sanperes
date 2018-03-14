<?php
if(empty($_COOKIE['logado'])) {
	print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
	exit;
}

include "../conn.php";
include "../funcoes.php";

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao,$folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

// MASTER
$id_user       = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

// FOLHA
$qr_folha  = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_array($qr_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto    = mysql_fetch_array($result_projeto);

$meses        = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT       = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>
<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" class="show">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="17%" height="100" align="center"><span class="texto10"><img src="../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="58%"><span class="texto10">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="25%">
            <span class="texto10">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table>
        </td>
      </tr>
    </table>
    <br/>
    <br/>
      <span class="title">Lista de Participantes - <?=$mes_da_folha?> / <?=$row_folha['ano']?></span>
    <br/>
    <br/>
      <table align="center" cellpadding="5" cellspacing="1" width="90%">
        <tr style="background-color:#ccc; font-weight:bold;">
          <td width="50%">NOME</td>
          <td width="25%">PIS</td>
          <td width="25%" align="center">INSS PAGO</td>
        </tr>
        <?php $qr_cooperados = mysql_query("SELECT folha.nome, cadastro.pis, folha.inss FROM folha_cooperado folha INNER JOIN autonomo cadastro ON folha.id_autonomo = cadastro.id_autonomo WHERE folha.id_folha = '$folha' AND folha.status IN('3','4') ORDER BY folha.nome ASC");
			  while($cooperado = mysql_fetch_array($qr_cooperados)) {
				  $total += $cooperado[2]; ?>
        <tr style="background-color:<?php if($linha++%2==0) { echo '#eee'; } else { echo '#f1f1f1'; } ?>;">
          <td><?=$cooperado[0]?></td>
          <td><?=$cooperado[1]?></td>
          <td align="center"><?=number_format($cooperado[2], 2, ',', '.')?></td>
        </tr>
        <?php } ?>
      </table>
      <br>
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="36" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="show">TOTALIZADORES</td>
        </tr>
        <tr>
          <td width="46%" height="30" align="right" valign="middle" bgcolor="#f0f0f0" class="secao"><span class="linha">L&iacute;quido:</span></td>
          <td width="54%" height="30" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;<span class="style23">
            <?=number_format($total, 2, ',', '.')?>
          </span></td>
        </tr>
        <tr>
          <td height="30" align="right" valign="middle" bgcolor="#f0f0f0" class="secao"><span class="linha">Funcion&aacute;rios Listados:</span></td>
          <td height="30" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=mysql_num_rows($qr_cooperados)?></td>
        </tr>
      </table>
      <br>            
<?php $linkvolt = str_replace('+', '--', encrypt("$regiao&$folha")); ?>
</td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href="ver_folhacoop.php?enc=<?=$linkvolt?>" class="botao">VOLTAR</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>