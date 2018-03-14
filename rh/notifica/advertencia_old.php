<?
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
//PEGANDO O ID DO CADASTRO
include "../../conn.php";

$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['id_reg'];

$result = mysql_query(" SELECT * FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_regiao' ", $conn);
$row_regiao = mysql_fetch_array($result_regiao);
$regiao = $row_regiao['regiao'];

echo $row_regiao['id_master'];
$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


$result_curso = mysql_query(" SELECT * FROM curso where id_curso = '$row[id_curso]' ");
$row_curso = mysql_fetch_array($result_curso);

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


	
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '10' and id_clt = '$id_clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('10','$id_clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = 'id_clt' and tipo = '10'");
	echo "<script>alert('O funcionírio $row[nome], CPF: $row[cpf], já possui uma advertência emitida.');</script>";
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS


?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>ADVERT&Ecirc;NCIA</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="700" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
  <tr>
    <td width="680" colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto">
<img src="../../imagens/logomaster<?php echo $row_master['id_master'];?>.gif"/> </td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
    </strong></span><span class="title"><strong>Advert&ecirc;ncia</strong></span><span class="campotexto"><strong><br />
    <br />
    </strong></span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF">      <blockquote>
        <p class="linha">&nbsp;</p>
        <p class="style2"><?php	print "$regiao, $dia de $mes de $ano";?></p>
<p class="linha">A(o) Sr.(a) <span class="style2"><?=$row['nome']?></span></p>
        <p class="linha">Pela presente fica  V.S&ordf;. advertido,em raz&atilde;o da(s) irregularidade(s) abaixo discriminada(s):<br />
        </p>
<p align="justify" class="linha">Esclarecemos que a  reitera&ccedil;&atilde;o no cometimento de irregularidades autorizam a rescis&atilde;o do 
          contrato de trabalho  por justa causa, raz&atilde;o pela qual esperamos que V.S&ordf;. procure evitar 
          a reincid&ecirc;ncia em  procedimentos an&aacute;logos, para que n&atilde;o tenhamos, no futuro, de tomar as 
        en&eacute;rgicas medidas  que nos s&atilde;o facultadas por lei.</p>
<p>&nbsp;</p>
<p class="linha">Ciente  ____/____/______</p>
<p>&nbsp;</p>
<p class="linha"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br />
  <span class="style2"><?=$row['nome']?><br />
          <?=$row['ctps']?><br />
        <?=$row_curso['nome']?></span></p>
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