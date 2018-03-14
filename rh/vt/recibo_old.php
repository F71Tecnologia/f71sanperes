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

$result_vale = mysql_query("Select * from rh_vale where id_clt = '$row[0]'and id_projeto = $id_pro ");
$row_vale = mysql_fetch_array($result_vale);

$nome_para_arquivo = $row['1'];
	
if($row['foto'] == "1"){
	
	if($nome_para_arquivo == "0"){
		$nome_imagem = $id_reg."_".$id_pro."_".$row['0'].".gif";
	}else{
		$nome_imagem = $id_reg."_".$id_pro."_".$nome_para_arquivo.".gif";
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
<?php
include "../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?>
<br />
          <span class="style2">Recibo de Entrega de Vale - Transporte</span></p></td>
        <td width="16%">&nbsp;</td>
      </tr>
    </table>
      <br />
      <table width="100" height="130" border="1" cellpadding="4" cellspacing="0" bordercolor="#FFFFFF">
        <tr>
          <td width="100" align="center" valign="middle" bgcolor="#CCFFCC" class="niver"><strong class="linha">
          <img src='../../fotos/<?=$nome_imagem?>' border=0 width='100' height='130'>
          </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td width="19%" bgcolor="#CCCCCC"><span class="linha">Nome do Funcion&aacute;rio:</span></td>
    <td width="81%" class="linha"><?=$row['nome']?></td>
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
    <td bgcolor="#CCCCCC"><span class="linha">Empresa:</span></td>
    <td class="linha"><?=$row_empresa['nome']?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><br />
      <table width="100%" border="1" bordercolor="#FFFFFF" cellpadding="5" cellspacing="0">
        <tr>
          <td colspan="6" align="center" bgcolor="#666666"><strong class="style1"><strong>TRANSPORTES UTILIZADOS</strong></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#F0F0F0" class="linha">MEIO DE TRANSPORTE UTILIZADO</td>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">INTINER&Aacute;RIO</span></td>
          <td align="center" bgcolor="#F0F0F0"><span class="linha">PRE&Ccedil;O DA PASSAGEM</span></td>
          <td align="center" bgcolor="#F0F0F0" class="linha"><span class="linha">QUANTIDADE</span></td>
        </tr>
<? 

		$valor = "0";
		$quant_vales = 0;
		
		//For de 6 voltas, pois os campos na tabela hr_tarifas passui 6 colunas
		for($i=1; $i<=6; $i++){
			
			//Campos da tabela
			echo "<tr>";
			
			$tarifa=$row_vale['id_tarifa'.$i];	
			$result_tarifa = mysql_query("Select * from rh_tarifas where id_tarifas = '$tarifa'");
			$row_tarifa = mysql_fetch_array($result_tarifa);
			
			//Nome do Funcionário
			echo "<td bgcolor='#CCFFCC'>".$row_tarifa['tipo'].'</td>';
			
			//Itinerário
			echo '<td bgcolor="#CCFFCC">'.$row_tarifa['itinerario'].'</td>';
			
			$valor_parcial = $row_tarifa['valor'];			
			
			//Muda de valor com virgula para valor com ponto para fins de calculos
			$valor2 = str_replace(".","",$row_tarifa['valor']);
			$valor2 = str_replace(",",".",$valor2);
				
			$valor = $valor+$valor2;

			$array_valor_parcial[$i] = $valor_parcial;
			
			//Valor parcial
			echo '<td bgcolor="#CCFFCC">'.$valor_parcial.'</td>';
			
			$quant_vales = $quant_vales + $row_vale['qnt'.$i];					

			$array_quantidade_parcial[$i] = $row_vale['qnt'.$i];
			
			//Quantidade
			echo '<td bgcolor="#CCFFCC">'.$row_vale['qnt'.$i].'</td>';
			echo "</tr>";
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
	$valor_total = $valor*$quant_vales; 
	
	$valorTotal=0.0;
	for($i=1;$i<=6;$i++){
	  //Variável array com o valor parcial dos vales transporte
	  $valorArrayParcial = $array_valor_parcial[$i];
	  //Esta parte do script está trocando a virgula por ponto para fins de calculo.
	  $valorArrayParcial = str_replace(",",".",$valorArrayParcial);
	  //Esta parte do script está multiplicando o quantidade parcial pelo valor unitário de cada vale transporte
	  $TotalParcial = $valorArrayParcial*$array_quantidade_parcial[$i];
		  
   	  $valorTotal = $valorTotal+$TotalParcial;
	  }

	  //Formata o valor R$ novamente.	  
	  echo $valorTotal_f = number_format($valorTotal,2,",",".")
	  
	?></span> para utiliza&ccedil;&atilde;o<br />
        durante o per&iacute;odo de <span class="style2">
        <label>
          <select name="select" id="select">
            <option><? echo date('d'); ?></option>
            <option>01</option>
            <option>02</option>
            <option>03</option>
            <option>04</option>
            <option>05</option>
            <option>06</option>
            <option>07</option>
            <option>08</option>
            <option>09</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
            <option>13</option>
            <option>14</option>
            <option>15</option>
            <option>16</option>
            <option>17</option>
            <option>18</option>
            <option>19</option>
            <option>20</option>
            <option>21</option>
            <option>22</option>
            <option>23</option>
            <option>24</option>
            <option>25</option>
            <option>26</option>
            <option>27</option>
            <option>28</option>
            <option>29</option>
            <option>30</option>
            <option>31</option>
          </select>
        </label>
        </span> de 
        <select name="select2" id="select2">
          <option><? echo $mes; ?></option>
          <option>Janeiro</option>
          <option>Fevereiro</option>
          <option>Março</option>
          <option>Abril</option>
          <option>Maio</option>
          <option>Junho</option>
          <option>Julho</option>
          <option>Agosto</option>
          <option>Setenbro</option>
          <option>Outubro</option>
          <option>Novenbro</option>
          <option>Dezembro</option>
        </select>
        de
        <? echo date('Y'); ?> &nbsp;a &nbsp;<span class="style2">
        <label>
  <select name="select3" id="select3">
  
  "<script>EndOfAMonth(2009, 1)</script>"; 

    <option><? echo date('t'); ?></option>
    <option>01</option>
    <option>02</option>
    <option>03</option>
    <option>04</option>
    <option>05</option>
    <option>06</option>
    <option>07</option>
    <option>08</option>
    <option>09</option>
    <option>10</option>
    <option>11</option>
    <option>12</option>
    <option>13</option>
    <option>14</option>
    <option>15</option>
    <option>16</option>
    <option>17</option>
    <option>18</option>
    <option>19</option>
    <option>20</option>
    <option>21</option>
    <option>22</option>
    <option>23</option>
    <option>24</option>
    <option>25</option>
    <option>26</option>
    <option>27</option>
    <option>28</option>
    <option>29</option>
    <option>30</option>
    <option>31</option>
  </select>
</label>
</span> de
<select name="select3" id="select4">
  <option><? echo $mes; ?></option>
  <option>Janeiro</option>
  <option>Fevereiro</option>
  <option>Mar&ccedil;o</option>
  <option>Abril</option>
  <option>Maio</option>
  <option>Junho</option>
  <option>Julho</option>
  <option>Agosto</option>
  <option>Setenbro</option>
  <option>Outubro</option>
  <option>Novenbro</option>
  <option>Dezembro</option>
</select>
de
<? echo date('Y'); ?>.<br />
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
