<?

$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];
$ID_PROTOCOLO = $_REQUEST['id_protocolo'];

include "../../conn.php";

$result = mysql_query(" SELECT *, date_format(data_entrada, '%d/%m/%Y')as nova_data FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_reg' ", $conn);
$row_regiao = mysql_fetch_array($result_regiao);
$regiao = $row_regiao['regiao'];




$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);




$nome_para_arquivo = $row['1'];
	
if($row['foto'] == "1"){
	
	if($nome_para_arquivo == "0"){
		$nome_imagem = $id_reg."_".$row['id_projeto']."_".$row['0'].".gif";
	}else{
		$nome_imagem = $id_reg."_".$row['id_projeto']."_".$nome_para_arquivo.".gif";
	}
}else{
$nome_imagem = "semimagem.gif";
}

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
<title>RECIBO DE VT</title>
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
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5" class="bordaescura1px">
  <tr>
    <td colspan="2" align="center" valign="middle"><table width="100%" height="123" border="0" align="center" cellpadding="4">
      <tr>
        <td width="18%" height="117">&nbsp;</td>
        <td width="66%" align="center"><p class="linha">
<img src="../../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/><br />

          <span class="style2">Recibo de Entrega de Vale - Transporte</span></p></td>
        <td width="16%">&nbsp;</td>
      </tr>
    </table>
      <br />
      <table width="100" height="130" border="1" cellpadding="4" cellspacing="0" bordercolor="#FFFFFF">
        <tr>
          <td width="100" align="center" valign="middle" bgcolor="#CCFFCC" class="niver"><strong class="linha"><img src='../../fotos/<?=$nome_imagem?>' width='100' height='130'></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td width="19%" bgcolor="#CCCCCC"><span class="linha">Nome:</span></td>
    <td width="81%" class="linha"><?=$row['nome']?></td>
  </tr>
  <tr>
    <td bgcolor="#CCCCCC"><span class="linha">Endere&ccedil;o:</span></td>
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
    <td bgcolor="#CCCCCC"><span class="linha">Empresa:</span></td>
    <td class="linha"><?=$row_empresa['nome']?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><br />
      <table width="100%" border="1" bordercolor="#FFFFFF" cellpadding="5" cellspacing="0">
        <tr>
          <td colspan="6" align="center" bgcolor="#999999"><strong>VALE TRANSPORTE UTILIZADOS</strong></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#CCCCCC" class="linha">TIPO</td>
          <td align="center" bgcolor="#CCCCCC"><span class="linha">INTINER&Aacute;RIO</span></td>
          <td align="center" bgcolor="#CCCCCC"><span class="linha">VALOR</span></td>
          <td align="center" bgcolor="#CCCCCC" class="linha"><span class="linha">QUANTIDADE</span></td>
        </tr>
<? 
$RESULT_R_RELATORIO = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo='$ID_PROTOCOLO' AND id_func=".$id_clt."")or die(mysql_error());

$resultProtocolo = mysql_query("SELECT *, date_format(data_ini, '%d/%m/%Y')AS data_iniF, date_format(data_fim, '%d/%m/%Y')AS data_fimF FROM rh_vale_protocolo WHERE id_protocolo = '$ID_PROTOCOLO'")or die(mysql_error());
$rowProtocolo = mysql_fetch_array($resultProtocolo);

while($row = mysql_fetch_array($RESULT_R_RELATORIO)){
		
	//Tipo
	echo "<td bgcolor='#CCFFCC'>".$row['tipo'].'</td>';
			
	//Itinerário
	echo '<td bgcolor="#CCFFCC">'.$row['itinerario'].'</td>';
				
	//Valor parcial
	$valor = $row['valor'];
	
	$valor = number_format($valor,2,",",".");
	echo '<td bgcolor="#CCFFCC">'.$valor.'</td>';
		
			
	//Quantidade
	$quantidade = $row['quantidade'];
	$quantidade = $quantidade*2;
	echo '<td bgcolor="#CCFFCC">'.$quantidade.'</td>';
	echo "</tr>";
	$valorCalc = $row['valor'];
	$arrayValor[] = $valorCalc * $quantidade;
}
		
		?>      </table>
    <br /></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" class="campotexto"><div align="justify">
      <p>&nbsp;&nbsp;&nbsp;&nbsp;Comprometo-me a utilizar o vale-transporte   exclusivamente para os deslocamentos Resid&ecirc;ncia -
        Trabalho - Resid&ecirc;ncia, bem   como manter atualizadas as informa&ccedil;&otilde;es acima prestadas. <br />
        <br />
&nbsp;&nbsp;&nbsp;        Declaro,
        ainda, que   as informa&ccedil;&otilde;es supra s&atilde;o a express&atilde;o da verdade, ciente de que o erro nas   mesmas, ou o uso<br />
        indevido do vale-transporte, constituir&aacute; falta grave,   ensejando puni&ccedil;&atilde;o, nos termos da legisla&ccedil;&atilde;o espec&iacute;fica.</p>
      <p>Recebi de <span class="style2"><?=$row_empresa['nome']?></span>, <span class="style2"><? echo $quant_vales; ?></span> vales   transporte no valor total de R$<span class="style2">
	<? 	
	  $valorTotalFuncionario =  array_sum($arrayValor);	  
	  echo $valorTotalFuncionario = number_format($valorTotalFuncionario,2,",",".")
	  
	?></span> para utiliza&ccedil;&atilde;o<br />
        durante o per&iacute;odo de <span class="style2"><? echo $rowProtocolo['data_iniF']; ?></span> &nbsp;a &nbsp;<span class="style2"><? echo $rowProtocolo['data_fimF']; ?></span>.<br />
      <br />
    </p>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle">
</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><span class="linha">
      <?php 

	print "$regiao, $dia de $mes de $ano";	
	echo '<br><br>';
	 ?>
     
    </span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><p><span class="linha">_____________________________________________________<br />
    Assinatura</span></p></td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
</body>
</html>
