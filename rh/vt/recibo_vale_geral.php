<?php
include "../../conn.php";
include "../../empresa.php";
$id_user = $_COOKIE['logado'];
$id_reg = $_REQUEST['regiao'];
$id_protocolo = $_REQUEST['id_protocolo'];
$mes_referencia = $_REQUEST['mes_referencia'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RECIBO DE VT</title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
body {
	margin:0px;
	text-align:center;
}
.secao_master {
	text-align:center; 
	color:#C30; 
	font-weight:bold;
}
.secao {
	text-align:right;
	font-weight:bold;
}
.secao_um {
	background-color:#ECF2EC;
	text-align:right;
	font-weight:bold;
}
.secao_dois {
	background-color:#DDD;
	text-align:center;
	font-weight:bold;
}
.linha_um {
	background-color:#FAFAFA;
}
.linha_dois {
	background-color:#F3F3F3;
}
</style>
<body>
<div style="background:#FFF; width:80%; margin:0px auto; padding:30px;">

	<?php $imgCNPJ = new empresa();
		  $imgCNPJ -> imagemCNPJ(); ?>
    
    <?php // Início da Recuperação Coletiva do Banco
	$qr_protocolo = mysql_query("SELECT *, date_format(data_ini, '%d/%m/%Y') AS data_iniF, date_format(data_fim, '%d/%m/%Y') AS data_fimF FROM rh_vale_protocolo WHERE id_protocolo = '$id_protocolo' AND id_reg = '$id_reg'");
	$protocolo = mysql_fetch_array($qr_protocolo);
	$vinculo_protocolo = $protocolo['id_protocolo'];

	$qr_relatorio = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo = '$vinculo_protocolo' AND id_reg = '$id_reg'");
	$relatorio = mysql_fetch_array($qr_relatorio); ?>
	
	<table cellspacing="0" cellpadding="0" style="margin:30px auto; margin-bottom:-120px;">
      <tr>
        <td colspan="2" class="secao_master">Recibo de  Vale - Transporte</td>
      </tr>
      <tr>
        <td class="secao">Mês referência:</td>
        <td><?=$protocolo['mes'].' / '.$protocolo['ano']?></td>
      </tr>
      <tr>
        <td class="secao">Período:</td>
        <td><?=$relatorio['dias']?> dias</td>
      </tr>
      <tr>
        <td class="secao">Data referência:</td>
        <td><?=$protocolo['data_iniF'].' à '.$protocolo['data_fimF']?></td>
      </tr>
      <tr>
        <td class="secao">Valor:</td>
        <td><?=$relatorio['valor_total']?></td>
      </tr>
    </table>

  <p class="quebra-aqui"></p>
    
<?php $qr_r_relatorio = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = '$vinculo_protocolo' AND id_reg = '$id_reg'")or die(mysql_error());
	  while($r_relatorio = mysql_fetch_array($qr_r_relatorio)) {
	
		// Seleciona os dados de cadastro do funcionário
		$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$r_relatorio[id_func]'");
		$clt = mysql_fetch_array($qr_clt);
	
		$qr_empresa= mysql_query("SELECT nome FROM rhempresa WHERE id_empresa = '$clt[rh_vinculo]'");
		$empresa = mysql_fetch_array($qr_empresa);
			
		$ID_FUN[] = $r_relatorio['id_func'];
		
	  }

	// Retira Ids Repetidos
	$result = array_unique($ID_FUN);
	
	// Conta os Restantes
	$quant = count($result);

	for($i=0; $i<$quant; $i++){
		$ID[$i] = current($result);
		next($result);
	}

//-----------------------------------------------------------------

$inicial = $_REQUEST['inicialrhvale'];
$final = $_REQUEST['finalrhvale'];

if($final >= $quant) {
	$final = $quant;	
}

$inicial = $_REQUEST['inicial'];

// Variável enviada pela página anterior deve estar zerada.
if(empty($_REQUEST['finalrhvale'])) { 
	$final = $_REQUEST['final'];
}

//Mostra os dados dos funcionários
for($i2=0; $i2<$quant; $i2++) {

	$qr_r_relatorio2 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = '$vinculo_protocolo' AND id_func = ".$ID[$i2]." AND id_reg = '$id_reg'")or die(mysql_error());
	$r_relatorio2 = mysql_fetch_array($qr_r_relatorio2);
 
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt =".$ID[$i2]."");
	$row_clt = mysql_fetch_array($qr_clt);
	
	$id_pro = $row_clt['id_projeto'];
	$id_reg = $row_clt['id_regiao'];
	
	if($row_clt['foto'] == "1") {
		
		if($row_clt['1'] == "0") {
			$nome_imagem = $id_reg."_".$id_pro."_".$ID[$i2].".gif";
		} else {
			$nome_imagem = $id_reg."_".$id_pro."_".$row_clt['1'].".gif";
		}
		
	} else {
		$nome_imagem = "semimagem.gif";
	} ?>

	<table width="100%" cellpadding="4" cellspacing="0" style="margin-top:150px;">
      <tr>
        <td width="10%" rowspan="5"><img src="../../fotosclt/<?=$nome_imagem?>" border="1" width="85" height="110"></td>
        <td width="10%" class="secao_um">Nome:</td>
        <td width="80%"><?=$row_clt['nome']?></td>
      </tr>
      <tr>
        <td class="secao_um">Endereço:</td>
        <td><?=$row_clt['endereco']?></td>
      </tr>
      <tr>
        <td class="secao_um">Município:</td>
        <td><?=$row_clt['cidade']?></td>
      </tr>
      <tr>
        <td class="secao_um">Bairro:</td>
        <td><?=$row_clt['bairro']?></td>
      </tr>
      <tr>
        <td class="secao_um">CEP:</td>
        <td><?=$row_clt['cep']?></td>
      </tr>
    </table>
    
	<table width="100%" style="text-align:center; line-height:22px;">
  	  <tr>
        <td colspan="4" class="secao_dois">VALE TRANSPORTE UTILIZADOS</td>
  	  </tr>
  	  <tr>
        <td width="25%" class="secao_dois">TIPO</td>
        <td width="30%" class="secao_dois">INTINERÁRIO</td>
        <td width="20%" class="secao_dois">VALOR</td>
        <td width="20%" class="secao_dois">QUANTIDADE</td>
  	  </tr>

	<?php $qr_r_relatorio3 = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_func = '$ID[$i2]' AND id_reg = '$id_reg' AND mes = '$mes_referencia'");
	      while($r_relatorio3 = mysql_fetch_array($qr_r_relatorio3)) {
				
				if(!empty($r_relatorio3['valor_total_func'])) {
					$valor_por_funcionario = number_format($r_relatorio3['valor_total_func'],2,",","."); 
				} ?>
				  <tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				    <td><?=$r_relatorio3['tipo']?></td>
				    <td><?=$r_relatorio3['itinerario']?></td>
				    <td><?=number_format($r_relatorio3['valor'],2,',','.')?></td>
				    <td><?=$r_relatorio3['quantidade']?></td>
		          </tr>
	<?php } ?>
  </table>
    
	<div class="campotexto" style="margin-top:30px; text-align:justify; line-height:22px;">
        &nbsp;&nbsp;&nbsp;&nbsp;Comprometo-me a utilizar o vale-transporte exclusivamente para os deslocamentos Residência - Trabalho - Residência, bem como manter atualizadas as informações acima prestadas.
        <br />
        &nbsp;&nbsp;&nbsp;&nbsp;Declaro, ainda, que as informações supra são a expressão da verdade, ciente de que o erro nas mesmas, ou o uso indevido do vale-transporte, constituirá falta grave, ensejando punição, nos termos da legislação específica.        <br />
        &nbsp;&nbsp;&nbsp;&nbsp;Recebi de <span class='style2'><?=$empresa['nome']?></span>, <span class='style2'><?=$r_relatorio3['quantidade']?></span> vales transporte no valor total de R$ <span class='style2'><?=$valor_por_funcionario?></span> para utilização durante o período de <span class='style2'><?=$protocolo['data_iniF']?></span> a <span class='style2'><?=$protocolo['data_fimF']?></span>.
	</div>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
<div align="center">_____________________________________________________</div>
<div align='center'>
	<span class='linha'>Assinatura</span>
</div>
<p class="quebra-aqui"></p>
<?php } ?>
</div>
</body>
</html>