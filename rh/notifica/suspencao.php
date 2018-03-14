<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../../conn.php";

$clt = $_REQUEST['clt'];
$pro = $_REQUEST['pro'];
$reg = $_REQUEST['id_reg'];
$data = $_REQUEST['data'];
$dia = $_REQUEST['dia'];
$obs = $_REQUEST['obs'];

$result = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
$row = mysql_fetch_array($result);

$result_emp = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$row[rh_vinculo]'");
$row_emp = mysql_fetch_array($result_emp);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$reg' ", $conn);
$row_regiao = mysql_fetch_array($result_regiao);
$regiao = $row_regiao['regiao'];


$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);




$result_suspencao = mysql_query("SELECT *, 
date_format(data_obs, '%d/%m/%Y') AS data, 
DATE_ADD(data, INTERVAL '$dia' DAY) AS retorno
FROM rh_doc_status WHERE tipo = 9 AND id_clt = '$clt' ");
$row_suspencao =  mysql_fetch_array($result_suspencao);

//Formatando data do retorno ao trabalho.
$retorno = $row_suspencao['retorno'];
$retorno = explode("-", $retorno);
	$a=$retorno[0];
	$m=$retorno[1];
	$d=$retorno[2];
	
	$retorno = date("d/m/Y", mktime(0, 0, 0, $m, $d, $a));

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

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SUSPEN&Ccedil;&Atilde;O</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="700" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
  <tr>
    <td width="680" colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto"><img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
    </strong></span><span class="title"><strong>Suspen&ccedil;&atilde;o</strong></span><span class="campotexto"><strong><br />
    <br />
    </strong></span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF">      <blockquote>
        <p class="linha"><span class="style2"><?php	print "$regiao, $dia de $mes de $ano";?></span><br />
          <br />
        </p>
<p class="linha">A(o) Sr.(a) <span class="style2"><?=$row['nome']?></span></p>
        <p align="justify" class="linha">Pela presente o  notificamos que a partir desta data esta suspenso do exerc&iacute;cio de suas 
          fun&ccedil;&otilde;es, pelo prazo  <? echo $row_suspencao['obs2']; ?> dias, em raz&atilde;o da(s) irregularidade(s) abaixo 
          discriminada(s) :</p>
        <p align="justify" class="style2"><? echo $row_suspencao['obs']; ?> - <? echo $row_suspencao['data']; ?></p>
        <p align="justify" class="linha">Devendo, portanto,  apresentar-se novamente ao servi&ccedil;o no hor&aacute;rio usual, no dia 
          <strong class="style2"><? echo $retorno; ?></strong>.</p>
        <p align="justify" class="linha">Esclarecemos que a  reitera&ccedil;&atilde;o no cometimento de irregularidades autorizam a rescis&atilde;o do 
          contrato de trabalho  por justa causa, raz&atilde;o pela qual esperamos que V.S&ordf;. procure evitar
          a reincid&ecirc;ncia em  procedimentos an&aacute;logos, para que n&atilde;o tenhamos,no futuro, de tomar as
          en&eacute;rgicas medidas  que nos s&atilde;o facultadas por lei.</p>
  <p class="linha">&nbsp;</p>
<p class="linha">Ciente  ____/____/______</p>
  <p>&nbsp;</p>
  <p class="linha" align="center">________________________________________________<br />
    <span class="style2"><?=$row['nome']?><br />
      <?=$row['campo1']." ".$row['serie_ctps']." ".$row['uf_ctps']?><br />
      <?=$row['id_curso']?></span></p>
  <p>&nbsp;</p>
  <p class="linha">____________________________________________<br />
    Assinatura do Empregador </p>
        <p class="linha"><span class="linha"><br />
        </span> </p>
    </blockquote>    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>
</body>
</html>