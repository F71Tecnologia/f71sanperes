<?php
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
}

include "../../conn.php";
include "../../empresa.php";

$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];

$mes_referencia = $_REQUEST['mes_referencia'];
$id_protocolo = $_REQUEST['id_protocolo'];
$status_pagina = $_REQUEST['status_pagina'];

$qr_data = mysql_query("SELECT *, date_format(data_ini, '%d/%m/%Y') AS data_iniF, date_format(data_fim, '%d/%m/%Y') AS data_fimF FROM rh_vale_protocolo JOIN ano_meses WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'");
$data = mysql_fetch_array($qr_data);

// Variaveis globais que serão usados para verificar quando cada funcionário entrou na empresa para fins de quantidade de vale transporte a ser distribuido
$GLOBALS["dataInicio"] = $data['data_ini'];
$GLOBALS["dataFim"] = $data['data_fim'];

$qr_periodo = mysql_query("SELECT * FROM rh_vale_protocolo JOIN ano_meses WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'");
$periodo = mysql_fetch_array($qr_periodo);

// Conta o Intervalo de Dias sem os Finais de Semana
$qr_dias = mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND fds != 1");
$numero_dias = mysql_num_rows($qr_dias);

// Verifica os Feriados Federais neste Período
$qr_feriados_federal = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data_federalF FROM rhferiados WHERE data >='$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND tipo = 'Federal'");
$numero_feriados_federal = mysql_num_rows($qr_feriados_federal);

// Verifica os Feriados Regionais neste Período
$qr_feriados_regional = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data_regionalF FROM rhferiados WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND id_regiao = '$regiao' AND tipo = 'Regional'");
$numero_feriados_regional = mysql_num_rows($qr_feriados_regional);

// Verifica os Feriados Federais neste Período nos Dias Uteis
$numero_feriados_federal_dias = NULL;

$qr_feriados = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data_federalF FROM rhferiados WHERE data >='$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND tipo = 'Federal'");
while($feriado = mysql_fetch_assoc($qr_feriados)) {
	
	$qr_dias1 = mysql_query("SELECT * FROM ano WHERE data >= '$feriado[data]' AND data <= '$feriado[data]' AND fds != 1");
	$dias1 = mysql_num_rows($qr_dias1);
	if(!empty($dias1)) {
		$numero_feriados_federal_dias++;
	}
	unset($dias1);
	
}

// Verifica os Feriados Regionais neste Período nos Dias Uteis
$numero_feriados_regional_dias = NULL;

$qr_feriados = mysql_query("SELECT * FROM rhferiados WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND id_regiao = '$regiao' AND tipo = 'Regional'");
while($feriado = mysql_fetch_assoc($qr_feriados)) {
	
	$qr_dias2 = mysql_query("SELECT * FROM ano WHERE data >= '$feriado[data]' AND data <= '$feriado[data]' AND fds != 1");
	$dias2 = mysql_num_rows($qr_dias2);
	if(!empty($dias2)) {
		$numero_feriados_regional_dias++;
	}
	unset($dias2);
	
}

$feriados = $numero_feriados_federal + $numero_feriados_regional;
$feriados_dias = $numero_feriados_federal_dias + $numero_feriados_regional_dias;
$numero_dias -= $feriados_dias;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RELAT&Oacute;RIO DE VALES TRANPORTE</title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body {
	margin:0px;
	text-align:center;
}
h1 { 
	page-break-after:always;
}
.destaque {
	font-weight:bold;
	color:#C30;
}
.secao_master {
	text-align:center; 
	color:#C30; 
	font-weight:bold;
}
.secao_um {
	background-color:#DDD;
	font-weight:bold;
}
.secao_dois {
	background-color:#ECF2EC;
	font-weight:bold;
}
.linha_um {
	background-color:#FAFAFA;
}
.linha_dois {
	background-color:#F3F3F3;
}
</style>
</head>
<body>
<div style="background:#FFF; width:90%; margin:0px auto; padding:20px;">

    	<?php $imgCNPJ = new empresa();
		      $imgCNPJ -> imagemCNPJ(); ?>
        
   <table cellspacing="0" cellpadding="0" style="margin:30px auto;">
      <tr>
        <td class="secao_master">Protocolo de Entrega de Vale-Transporte</td>
      </tr>
      <tr>
        <td>Referente a <span class="destaque"><?=$numero_dias?></span> dias &uacute;teis entre <span class="destaque"><?=$data['data_iniF']?></span> a <span class="destaque"><?=$data['data_fimF']?></span></td>
      </tr>
    </table>
      
<?php if(!empty($feriados)) { ?>

	<table width="40%" cellpadding="4" cellspacing="1" style="margin:0px auto; text-align:center;">
	  <tr>
	    <td colspan="3" class="secao_um">
          FERIADOS NO PERÍODO
        </td>
	  </tr>
	  <tr>
	    <td width="33%" class="secao_um">DATA</td>
	    <td width="33%" class="secao_um">FERIADO</td>
	    <td width="33%" class="secao_um">TIPO</td>
	  </tr>
	
<?php while($feriado_federal = mysql_fetch_array($qr_feriados_federal)) { ?>
	
     <tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		<td class="secao_dois"><?=$feriado_federal['data_federalF']?></td>
		<td class="secao_dois"><?=$feriado_federal['nome']?></td>
		<td class="secao_dois"><?=$feriado_federal['tipo']?></td>
	 </tr>

<?php } while ($feriado_regional = mysql_fetch_array($qr_feriados_regional)){ ?>

	 <tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		<td class="secao_dois"><?=$feriado_regional['data_federalF']?></td>
		<td class="secao_dois"><?=$feriado_regional['nome']?></td>
		<td class="secao_dois"><?=$feriado_regional['tipo']?></td>
	 </tr>
	
<?php } ?>
  </table>
<?php } ?>

      <table cellpadding="2" cellspacing="0" style="margin:50px auto; line-height:23px; text-align:center; width:95%;">
        <tr>
          <td colspan="13" class="secao_um">RELA&Ccedil;&Atilde;O DE FUNCION&Aacute;RIOS BENEFICIADOS</td>
        </tr>
        <tr class="secao_um">
          <td width="22%" style="text-align:left;">NOME</td>
          <td width="6%">BANCO</td>
          <td width="6%">AG&Ecirc;NCIA<br>CONTA</td>
          <td width="5%">TIPO</td>
          <td width="3%">COD</td>
          <td width="22%" style="text-align:left;">ITINERÁRIO</td>
          <td width="3%">DIAS</td>
          <td width="6%">VALOR</td>
          <td width="6%">PARCIAL</td>
          <td width="5%"> TOTAL</td>
          <td width="16%">ASSINATURA</td>
        </tr>
        
<?php $result_vale = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' ORDER BY nome ASC");

$valor = 0;
$quant_vales = 0;
$cont = 0;

// Inicia a gravação da tabela rh_vale_r_relatorio para depois ser atualizado várias vezes em pontos específicos.
while($row = mysql_fetch_array($result_vale)) {
	$ids_funcionarios[] = $row['id_func'];
}

// RETIRA OS ELEMENTOS REPETIDOS DO ARRAY
$ids_funcionarios = array_unique($ids_funcionarios);

// CONTA QUANTOS ELEMENTOS RESTARAM NO ARRAY
$quantidade_ids = count($ids_funcionarios);

// ORGANIZA OS ELEMENTOS DO ARRAY
for($i=0; $i<$quantidade_ids; $i++){
	$id_funcionarioAUX[$i] = current($ids_funcionarios);
	next($ids_funcionarios);
}

$quantCodigo = count($id_funcionarioAUX);

for($j=0; $j<$quantCodigo; $j++) {
	
	$qr_participante = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC");
	$participante = mysql_fetch_array($qr_participante);
	
	$qr_dados_bancarios = mysql_query("SELECT b.nome, a.agencia, a.conta FROM rh_clt a INNER JOIN bancos b ON a.banco = b.id_banco WHERE id_clt = '$id_funcionarioAUX[$j]'");
	$dados_bancarios	= mysql_fetch_array($qr_dados_bancarios); ?>
	
<tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>" style="font-size:11px;">
	  <td style="text-align:left;"><?=$participante['id_func'].' - '.$participante['nome']?></td>
      <td><?=$dados_bancarios[0]?></td>
      <td><?=$dados_bancarios[1]?><br><?=$dados_bancarios[2]?></td>

<?php // TIPO DO VALE TRANSPORTE
	  $qr_tipo = mysql_query("SELECT tipo FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC"); ?>
      <td><?php while($tipo = mysql_fetch_array($qr_tipo)) {
					print $tipo['tipo'].'<br>';
				} ?></td>
	
<?php // CÓDIGO DO VALE TRANSPORTE
	  $qr_codigo = mysql_query("SELECT codigo FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC"); ?>
      <td><?php while($codigo = mysql_fetch_array($qr_codigo)) {
		            print $codigo['codigo']."<br>";
	            } ?></td>

<?php // ITINERÁRIOS
	  $qr_itinerarios = mysql_query("SELECT itinerario FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC"); ?>
      <td style="text-align:left;">
	  <?php while($itinerarios = mysql_fetch_array($qr_itinerarios)) {
				print $itinerarios['itinerario']."<br>";
	        } ?></td>
	
<?php // NÚMERO DE DIAS
	  if(!empty($participante['extra'])) {
		  $extra = '*';
	  } ?>
	  <td><?=$participante['quantidade']?>
          <span class="destaque"><?=$extra?></span><br>
		  <?php unset($extra); ?></td>

<?php // VALOR
	  $qr_valor = mysql_query("SELECT valor FROM rh_vale_r_relatorio WHERE id_reg='$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC"); ?>
	  <td><?php while($valor = mysql_fetch_array($qr_valor)) {
					echo number_format($valor['valor'],2,",",".").'<br>';
	            } ?></td>

<?php // VALOR PARCIAL
	 $qr_valor_parcial = mysql_query("SELECT valor_parcial FROM rh_vale_r_relatorio WHERE id_reg = '$regiao' AND id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_func = '$id_funcionarioAUX[$j]' ORDER BY id_r_relatorio ASC"); ?>
	  <td><?php while($valor_parcial = mysql_fetch_array($qr_valor_parcial)) {
		            echo number_format($valor_parcial['valor_parcial'],2,",",".").'<br>';
	            } ?></td>
	
<?php // VALOR TOTAL
  	  $total = number_format($participante['valor_total_func'],2,",",".");
		if($total == '0,00') { 
			unset($total);
		} ?>
	  <td><?=$total.'<br>'?></td>
      
<?php // ASSINATURA ?>
	  <td>_________________________</td>
	</tr>
  <?php } ?>
    <tr>
      <td colspan="10" align="right" class="destaque">* Plantonista</td>
    </tr>
</table>
     
      
  <table cellpadding="4" cellspacing="0" style="margin:50px auto; text-align:center; width:95%;">
        <tr>
          <td colspan="8" class="secao_um">RESUMO DE VALE- TRANSPORTE  ENTREGUES</td>
        </tr>
        <tr class="secao_um">
          <td width="6%">COD</td>
          <td width="35%" style="text-align:left;">ITINER&Aacute;RIO</td>
          <td width="15%">TIPO</td>
          <td width="7%">QUANTIDADE</td>
          <td width="7%">VALES</td>
          <td width="15%">VALOR DIÁRIO</td>
          <td width="15%">VALOR / ITINERÁRIO</td>
        </tr>
     
<?php $resultTotais01 = mysql_query("SELECT codigo FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' ORDER BY codigo");
	  while($row01 = mysql_fetch_array($resultTotais01)){
	  	  $codigo[] = $row01['codigo'];
      }

// DEFINE QUANTOS ELEMENTOS O ARRAY POSSUY
$contCodigo = count($codigo);
$codigoAUX = array_unique($codigo);
$quantCodigoAUX = count($codigoAUX);

//ORGANIZA OS ELEMENTOS DO ARRAY
for($i=0; $i<$quantCodigoAUX; $i++){
	$codigoAUX2[$i] = current($codigoAUX);
	next($codigoAUX);
}

$cont = NULL;
$quantidade = NULL;

for($i=0; $i<$quantCodigoAUX; $i++) {
	
	
	
	$qr_totais = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND codigo = '$codigoAUX2[$i]' ORDER BY codigo ASC");
	$row = mysql_fetch_array($qr_totais);
    
	// DEFINE A QUANTIDADE DE VALES DE UM DETERMINADO ITINERÁRIO
	$qr_quantidade = mysql_query("SELECT quantidade FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND codigo = '$codigoAUX2[$i]'");
	while($quantidade3 = mysql_fetch_array($qr_quantidade)) {
		$quantidade += $quantidade3['quantidade'] * 2;
	}

	// MOSTRA O VALOR TOTAL DOS VALES DO TIPO CARTÃO
	$qr_cartoes = mysql_query("SELECT valor_parcial FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND codigo = '$codigoAUX2[$i]' AND tipo = 'CARTÃO'");
	while($cartoes = mysql_fetch_array($qr_cartoes)) {
		$arrayCartao[] = $cartoes['valor_parcial'];	
	}

	// MOSTRA O VALOR TOTAL DOS VALES DO TIPO PAPEL
	$qr_papel = mysql_query("SELECT valor_parcial FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' AND codigo = '$codigoAUX2[$i]' AND tipo = 'PAPEL'");
	while($papel = mysql_fetch_array($qr_papel)) {
		$arrayPapel[] = $papel['valor_parcial'];
	}
	
	$valor = number_format($row['valor'],2,",",".");
	$valorParcial = number_format($row['valor'] * $quantidade,2,",",".");

	for($j=0; $j<=$contCodigo; $j++) {
		if($codigo[$j] == $codigoAUX2[$i]) {
			$cont += 1;
		}
	} ?>
	
   		<tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
          <td><?=$row['codigo']?></td>
          <td style="text-align:left;"><?=$row['itinerario']?></td>
          <td><?=$row['tipo']?></td>
          <td><?=$quantidade_ids;?></td>
	      <td><?=$quantidade?></td>
	      <td><?='R$ '.$valor?></td>
	      <td><?='R$ '.$valorParcial?></td>
    	</tr>
	  
      <?php unset($cont, $quantidade);
	}
    
	if(!empty($arrayCartao)) {
		$cartao = array_sum($arrayCartao);
	}
	if(!empty($arrayPapel)) {
		$papel = array_sum($arrayPapel);
	}
	$total = $cartao + $papel;
	$cartao = number_format($cartao,2,",",".");
	$papel = number_format($papel,2,",",".");
	$total = number_format($total,2,",","."); ?>
    
    <tr>
      <td colspan="7">&nbsp;</td>
    </tr>
	<tr>
	  <td colspan="4">&nbsp;</td>
	  <td align="right" class="secao_dois" colspan="2">TOTAL DO TIPO PAPEL:</td>
	  <td align="left" class="destaque"><?='R$ '.$papel?></td>	
	</tr>
	<tr>
	  <td colspan="4">&nbsp;</td>
	  <td align="right" class="secao_dois" colspan="2">TOTAL DO TIPO CARTÃO:</td>
	  <td align="left" class="destaque"><?='R$ '.$cartao?></td>	
	</tr>
	<tr>
	  <td colspan="4">&nbsp;</td>
	  <td align="right" class="secao_dois" colspan="2">VALOR TOTAL:</td>
	  <td align="left" class="destaque"><?='R$ '.$total?></td>	
	</tr>
  </table>
</div>
</body>
</html>