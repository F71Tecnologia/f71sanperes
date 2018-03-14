<?
$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

include "../../conn.php";

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_reg' ", $conn);
$row_regiao = mysql_fetch_array($result_regiao);
$regiao = $row_regiao['regiao'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '15' and id_clt = '$id_clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('15','$id_clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '15'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

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
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pedido para Dispensa de Vale - Transporte</title>
<link href="../net.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
body {
	background-color: #CCC;
}
-->
</style>
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5">
  <tr>
    <td colspan="2" align="center" valign="middle"><p class="linha"><?php
include "../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?><br />
    <span class="style2">Pedido para Dispensa de Vale - Transporte</span></p></td>
  </tr>
  <tr>
    <td width="18%" bgcolor="#CCCCCC"><span class="linha">Nome do Funcion&aacute;rio:</span></td>
    <td width="82%" class="linha"><?=$row['nome']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Endere&ccedil;o Residencial:</span></td>
    <td class="linha"><?=$row['endereco']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Municipio:</span></td>
    <td class="linha"><?=$row['cidade']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Bairro:</span></td>
    <td class="linha"><?=$row['bairro']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">CEP:</span></td>
    <td class="linha"><?=$row['cep']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Pai:</span></td>
    <td class="linha"><?=$row['pai']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">M&atilde;e:</span></td>
    <td class="linha"><?=$row['mae']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">RG:</span></td>
    <td class="linha"><?=$row['rg']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">CPF:</span></td>
    <td class="linha"><?=$row['cpf']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Empresa:</span></td>
    <td class="linha"><?=$row_empresa['nome']?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><p class="linha">&nbsp;</p>
      <p align="justify" class="campotexto">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Venho por meio desta , declarar que n&atilde;o &eacute; de meu interesse receber vales -   transporte para os meus
    deslocamentos resid&ecirc;ncia - trabalho - resid&ecirc;ncia,   devido ao(s) motivo(s) relacionado(s) abaixo:<br />
      </p></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><p class="linha"><span class="campotexto">_______________________________________________________________________</span><br />
      </p></td>
  </tr>
  <tr>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><p>&nbsp;</p>
      <table width="100%" border="1" bordercolor="#000000" cellpadding="5" cellspacing="0">
        <tr>
          <td colspan="4" align="center" bgcolor="#003300"><strong class="style1">TRANSPORTES UTILIZADOS</strong></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">MEIO DE TRANSPORTE UTILIZADO</span></td>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">INTINER&Aacute;RIO</span></td>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">PRE&Ccedil;O DA PASSAGEM</span></td>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">QUANTIDADE</span></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
          <td bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
      </table>
    <p>&nbsp;</p></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><span class="linha">
      <?php 

	print "$regiao, $dia de $mes de $ano";	
	
	?>
    </span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><p>&nbsp;</p>
    <p><span class="linha">_____________________________________________________<br />
    Assinatura</span></p></td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
</body>
</html>
