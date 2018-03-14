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
/*
// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE 
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
*/
$numero_dias = 1;
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
          <td colspan="10" class="secao_um">RELA&Ccedil;&Atilde;O DE FUNCION&Aacute;RIOS BENEFICIADOS</td>
        </tr>
        <tr class="secao_um">
          <td width="26%" style="text-align:left;">NOME</td>
          <td width="5%">TIPO</td>
          <td width="3%">COD</td>
          <td width="24%" style="text-align:left;">ITINERÁRIO</td>
          <td width="3%">DIAS</td>
          <td width="7%">VALOR</td>
          <td width="7%">PARCIAL</td>
          <td width="5%">TOTAL</td>
          <td width="20%">ASSINATURA</td>
        </tr>
        
          <?php
			$valor = "0";
			$quant_vales = 0;
			$cont = 0;

			$qr_vale = mysql_query("SELECT * FROM rh_vale WHERE id_regiao = '$regiao' AND status_reg != '0'");
			while($vale = mysql_fetch_array($qr_vale)) { ?>
            
	<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>" style="font-size:11px;">
    
    <?php $qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$regiao' AND id_clt = '$vale[id_clt]' and status = '10' AND transporte = '1'");
		  $row = mysql_fetch_array($qr_clt);
		  
	      if($row['status'] == '10') {

			// ID do funcionário
			$dataEntrada = $row['data_entrada'];
			$dataSaida = $row['data_saida'];
			$dataIniPeriodo = $GLOBALS["dataInicio"];
			$dataFimPeriodo = $GLOBALS["dataFim"];



///////////////////////////////////////////
/// CONTAGENS DA ENTRADA DO FUNCIONÁRIO ///
///////////////////////////////////////////

if($dataEntrada != '0000-00-00' and $dataEntrada >= $dataIniPeriodo and $dataEntrada <= $dataFimPeriodo) {	

$numDiasEntra = 0;
		
// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a ENTRADA do funcionário e o Início do periodo do vale transporte
$qr_dias_entrada = mysql_query("SELECT data, fds FROM ano WHERE data BETWEEN '$dataEntrada' AND '$dataFimPeriodo' AND fds != '1'");
$numero_dias_entrada = mysql_num_rows($qr_dias_entrada);
	
/*
// VERIFICA OS FERIADOS NO PERÍODO em que o funcionário entrou e o início do período do vale transporte
$qr_feriados_entrada = mysql_query("SELECT data, tipo FROM rhferiados WHERE data BETWEEN '$dataEntrada' AND '$dataFimPeriodo' AND (tipo = 'Federal' OR tipo = 'Regional' AND id_regiao = '$regiao')");
$numero_feriados_entrada = mysql_num_rows($qr_feriados_entrada);

$numDiasEntra = $numero_dias_entrada - $numero_feriados_entrada;

if($numDiasEntra < 0) {
	$numDiasEntra = 0;
}*/
		
$avisoEntrada[] = $row['id_clt'];	
$avisoEntrada[] = $rowEntrada['nome'];
$avisoEntrada[] = $dataEntrada;
$avisoEntrada[] = $numDiasEntra;



/////////////////////////////////////////
/// CONTAGENS DA SAÍDA DO FUNCIONÁRIO ///
/////////////////////////////////////////
	
} elseif($dataSaida != '0000-00-00' and $dataSaida <= $dataFimPeriodo and $dataSaida >= $dataIniPeriodo) {

$totalDiasDescontoSaida = 0;
/*			
// CONTA O INTERVALO DE DIAS TRABALHADOS SEM OS FIM DE SEMANA
$qr_dias_saida = mysql_query("SELECT data, fds FROM ano WHERE data BETWEEN '$dataIniPeriodo' AND '$dataSaida' AND fds != '1'");
$numero_dias_saida = mysql_num_rows($qr_dias_saida);
	
// VERIFICA OS FERIADOS NO PERÍODO EM QUE O FUNCIONÁRIO TRABALHOU
$qr_feriados_saida = mysql_query("SELECT data, tipo FROM rhferiados WHERE data BETWEEN '$dataIniPeriodo' AND '$dataSaida' AND (tipo = 'Federal' OR tipo = 'Regional' AND id_regiao = '$regiao')");
$numero_feriados_saida = mysql_num_rows($qr_feriados_saida);

$totalDiasDescontoSaida = $numero_dias_saida - $numero_feriados_saida;
*/
if($totalDiasDescontoSaida < 0) {
	$totalDiasDescontoSaida = 0;
}
		
$avisoSaida[] = $row['id_clt'];
$avisoSaida[] = $rowEntrada['nome'];
$avisoSaida[] = $dataSaida;
$avisoSaida[] = $totalDiasDescontoSaida;

} 
?>



<?php // ID e NOME
	if(!empty($row['id_clt'])) {
		for($i=1; $i<=6; $i++) {		
			$array_ids[$cont][] = $row['id_clt'];
		}
	}

	if(!empty($row['nome'])) {
		for($i=1; $i<=6; $i++) {			
			$array_nomes[$cont][] = $row['nome'];
		}
	} ?>
    
	<td style="text-align:left;"><?=$row['id_clt'].' - '.$row['nome']?></td>
	<td>
    
	<?php // TIPO
	for($i=1; $i<=6; $i++) {
		
		$tarifa = $vale['id_tarifa'.$i];
		$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas = mysql_fetch_array($result_tarifas);
		
		if(!empty($row_tarifas['tipo'])) {
			echo $row_tarifas['tipo'].'<br>';	
			$array_tipos[$cont][] = $row_tarifas['tipo'];
		}
	} 
	?>
    
    </td>
	<td>
    
	<?php // CÓDIGO
	for($i=1; $i<=6; $i++) {
		
		$tarifa = $vale['id_tarifa'.$i];
		$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas = mysql_fetch_array($result_tarifas);
		
		if(!empty($row_tarifas['id_tarifas'])) {
			echo $row_tarifas['id_tarifas'].'<br>';
			$array_codigos[$cont][] = $row_tarifas['id_tarifas'];
		}
	} 
	?>
    
    </td>
    <td style="text-align:left;">
    
	<?php // ITINERÁRIO
	for($i=1; $i<=6; $i++) {
		
		$tarifa = $vale['id_tarifa'.$i];
		$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas = mysql_fetch_array($result_tarifas);

		if(!empty($row_tarifas['itinerario'])) {
			echo $row_tarifas['itinerario'].'<br>';	
			$array_itinerarios[$cont][] = $row_tarifas['itinerario'];
		}
	}
	?>
    
    </td>
    <td>
	
	<?php //Quantidade	
	$numero_de_dias_parcial = $numero_dias - ($numDiasEntra + $totalDiasDescontoSaida);
	
	// IDENTIFICA SE O FUNCIONÁRIO TRABALHA NO SÁBADO, DOMINGO OU AMBOS
	$resultHorarios = mysql_query("SELECT rh_horario FROM rh_clt WHERE id_clt = '$row[id_clt]'");
	$rowHorario = mysql_fetch_array($resultHorarios);
	
	// ANALISA FOLGAS
	$resultFolgas = mysql_query("SELECT folga FROM rh_horarios where id_horario = '$rowHorario[rh_horario]'");
	$rowFolgas = mysql_fetch_array($resultFolgas);	
	$folgas = $rowFolgas['folga'];

	// CONTA O NUMERO DE DIAS DE SABADOS E DOMINGOS NO PERÍODO DE VIGÊNCIA DO VALE TRANSPORTE
	$resultDiasFinalSemana = mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND fds = 1");
	$numerDiasFinsSemana = mysql_num_rows($resultDiasFinalSemana);
	
	// CONTA O NUMERO DE DIAS DE SABADOS NO PERÍODO DE VIGÊNCIA DO VALE TRANSPORTE
	$resultSabadosSemana = mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND nome = 'S&aacute;bado'");
	$numeroSabadosSemana = mysql_num_rows($resultSabadosSemana);

	// CONTA O NUMERO DE DIAS DE DOMINGOS NO PERÍODO DE VIGÊNCIA DO VALE TRANSPORTE
	$resultDomingosSemana = mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND nome = 'Domingo'");
	$numeroDomingosSemana = mysql_num_rows($resultDomingosSemana);

	if($folgas == '5') {
		
		$resultPeriodo = mysql_query("SELECT data_fim, data_ini FROM rh_vale_protocolo WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'")or die(mysql_error());
		$periodo = mysql_fetch_array($resultPeriodo);
		$inicialPeriodoVale = $periodo['data_ini'];
		$finalPeriodoVale = $periodo['data_fim'];
		
        // PARA FINS DE CALCULO DO INTERVALO ENTRE O INÍCIO DO PERÍODO DO VALE E O FIM, É USADO ESTE SELECT PARA SOMAR UM DIA NA DATA FINAL, VISTO QUE CASO ISSO NÃO SEJA FEITO O RESULTADO DA FUNÇÃO DATEDIFF RETORNA A DIFERENÇA COM 1 DIA A MENOS
		$resultFimperiodo = mysql_query("SELECT DATE_ADD(data_fim, INTERVAL 1 DAY) AS final FROM rh_vale_protocolo WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'");
		$rowFimperiodo = mysql_fetch_array($resultFimperiodo);		
		$final = $rowFimperiodo['final'];
		
		// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a ENTRADA do funcionário e o início do periodo do vale transporte
		$result_diasEntrada=mysql_query("SELECT DATEDIFF('$dataContrato','$inicialPeriodoVale') AS periodo");
		$rowDiasEntrada = mysql_fetch_array($result_diasEntrada);
		$entrada = $rowDiasEntrada['periodo'] + 1;

		// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a Saída do funcionário e o final do periodo do vale transporte
		$result_diasSaida=mysql_query("SELECT DATEDIFF('$finalPeriodoVale','$dataSaida') AS periodo");
		$rowDiasEntrada = mysql_fetch_array($result_diasSaida);
		$Saida = $rowDiasSaida['periodo'] + 1;
		
		// Conta o intervalo de Dias sem os Finais de Semana entre a Saída do Funcionário e o Fim do Periodo do Vale Transporte
		$result_diasSaida = mysql_query("SELECT * FROM ano WHERE data >= '$dataSaida' AND data <= '$dataFimPeriodo' AND fds = 1");
		$numero_de_diasSaida02 = mysql_num_rows($result_diasSaida);		
				
		$resultDiasPeriodoVale = mysql_query("SELECT DATEDIFF('2009-03-31','2009-03-01') AS periodo");
		$rowPeriodo = mysql_fetch_array($resultDiasPeriodoVale);
		
		$periodo = $rowPeriodo['periodo'];
		$periodo = $periodo - ($numero_de_diasEntrada02);
		$periodo = $periodo/2;
                
                
		//$numero_de_dias_parcial = ceil($periodo);
                
                ///ALTERADO PARA A LAGOS
                $numero_de_dias_parcial = 1;

		$extra = '<span class=destaque>*</span>';
		echo $numero_de_dias_parcial.$extra.'<br>';
	}	
	
	if ($folgas == '3') {
		echo $numero_de_dias_parcial.$extra.'<br>';
	}
	
	if ($folgas == '2') {
		$numero_de_dias_parcial = $numeroDomingosSemana+1;
		$extra = '(DOM)';
		echo $numero_de_dias_parcial.$extra.'<br>';
	}
	
	if ($folgas == '1') {
		$numero_de_dias_parcial = $numero_de_dias_parcial+$numeroSabadosSemana;
		$extra = '(SAB)';
		echo $numero_de_dias_parcial.$extra.'<br>';
	}

	if ($folgas == '0') {
		$numero_de_dias_parcial = $numero_de_dias_parcial+$numerDiasFinsSemana;
		$extra = '(SAB e DOM)';
		echo $numero_de_dias_parcial.$extra.'<br>';
	}
	
	$array_extra[$cont][] = $extra;
	$arrayDiasParcial[$cont][] = $numero_de_dias_parcial;
	?>
    
    </td>
	<td>
    
    <?php // Valor
	for($i=1; $i<=6; $i++) {
		
		$tarifa = $vale['id_tarifa'.$i];
		$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas = mysql_fetch_array($result_tarifas);
		
		if(!empty($row_tarifas['valor'])) {
			echo $row_tarifas['valor'].'<br>';
		}
		
			$valor_parcial = $row_tarifas['valor'];	
			
			// Muda de Valor com virgula para Valor com ponto para fins de calculos
			$valor2 = str_replace(".", "", $valor_parcial);
			$valor2 = str_replace(",", ".", $valor2);
		
			// Valor com a tarifa
			$array_tarifas[$cont][] = $valor2;
			$TotalParcialDia = (($valor2 * $numero_de_dias_parcial)*1); // estava 2 alterado para 1 SJR 29-03-2010 - 17:11
			$valor += $TotalParcialDia;
			$total = $valor;	
			$array_valor_parcial[$i] = $valor;		

		}

		unset($array_valor_parcial);
    	unset($valor); 
		?>
        
        </td>
		<td>
        
        <?php //Valor Parcial
		for($i=1; $i<=6; $i++) {
			
			$tarifa = $vale['id_tarifa'.$i];
			$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
			$row_tarifas = mysql_fetch_array($result_tarifas);		
			$valor_parcial = $row_tarifas['valor'];
			
			//Muda de valor com virgula para valor com ponto para fins de calculos
			$valor2 = str_replace(".", "", $valor_parcial);
			$valor2 = str_replace(",", ".", $valor2);
			
			$totaParcialDia = ($valor2 * $numero_de_dias_parcial);
			$valor2 = $totaParcialDia;

			$totalParcial = $valor2;
		
			if(!empty($totalParcial)) {	
			
				$array_dias_parcial[$cont][] = $numero_de_dias_parcial;		
				$array_total_parcial[$cont][] = $totalParcial;
				$totalParcial = number_format($totalParcial,2,",",".");			
				echo $totalParcial.'<br>';
				
			}

		}
		
		unset($array_valor_parcial);
    	unset($valor); ?>
        
        </td>
	    <td>
        
        <?php //Valor Total
		if(!empty($total)) {
			$array_total[$cont][] = $total;
			$total = number_format($total,2,",",".");
			echo $total;
			$cont += 1;
		} ?>
			
        </td>
        <td align="center">__________________________</td>	
     </tr>
     
     <?php 
	} unset($extra);
}
?>
    </tr>
    <tr>
      <td colspan="10" align="right" class="destaque"> Plantonista</td>
    </tr>
  </table>
     
<?php
for($l=0; $l<$cont; $l++) {
		for($c=0; $c<6; $c++) {
			if(!empty($array_codigos[$l][$c])) {
				
				$id = $array_ids[$l][$c];
				$nome = $array_nomes[$l][$c];
				$codigo2 = $array_codigos[$l][$c];
				$tipo2 = $array_tipos[$l][$c];
				$itinerario = $array_itinerarios[$l][$c];
				$tarifa2 = $array_tarifas[$l][$c];
				$valor_parcial2 = $array_total_parcial[$l][$c];
				$valor_total = $array_total[$l][$c];
				$num_dias_parcial = $array_dias_parcial[$l][$c];
				$extra2 = $array_extra[$l][$c];
				
				// Gravando registros na tabela rh_vale_r_relatorio, caso ação seja igual a gravar
				$acao = $_REQUEST['acao'];
				
				if(($acao == 'gravar') and ($status_pagina != 'voltar')) {	
					mysql_query("INSERT INTO rh_vale_r_relatorio SET id_protocolo = '$id_protocolo', id_reg = '$regiao', mes = '$mes_referencia', id_func = '$id', nome = '$nome', codigo = '$codigo2', tipo = '$tipo2', itinerario = '$itinerario', valor = '$tarifa2', valor_parcial = '$valor_parcial2', valor_total_func = '$valor_total', quantidade = '$num_dias_parcial', extra = '$extra2'")or die(mysql_error());
			}
		}
	}
}
?>
     
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
        
<?php $resultTotais01 = mysql_query("SELECT codigo FROM rh_vale_r_relatorio WHERE id_protocolo = '$id_protocolo' AND mes = '$mes_referencia' AND id_reg = '$regiao' ORDER BY codigo ASC");
	  while($row01 = mysql_fetch_array($resultTotais01)) {
		  $codigo[] = $row01['codigo'];
	  }

// DEFINE QUANTOS ELEMENTOS O ARRAY POSSUY
$contCodigo = count($codigo);
$codigoAUX = array_unique($codigo);
$quantCodigoAUX = count($codigoAUX);

// ORGANIZA OS ELEMENTOS DA ARRAY
for($i=0; $i<$quantCodigoAUX; $i++) {
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
	//$valorParcial = number_format($row['valor'] * $quantidade,2,",",".");
	$valorParcial = number_format($row['valor'] ,2,",",".");

	for($j=0; $j<=$contCodigo; $j++) {
		if($codigo[$j] == $codigoAUX2[$i]) {
			$cont += 1;
		}
	} ?>
	
    <tr class="linha_<?php if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
       <td><?=$row['codigo']?></td>
       <td style="text-align:left;"><?=$row['itinerario']?></td>
       <td><?=$row['tipo']?></td>
       <td><?=$cont?></td>
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

<?php
// Marca na tabela rh_vale_protocolo no campo status que o protocolo foi impresso


$ano = date('Y');
if(mysql_query("UPDATE rh_vale_protocolo SET status = 'IMPRESSO' WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'")){

    echo 'atualizou';
}




//Gravando na tabela rh_vale_relatorio os dados gerais, os detalhes serão gravados na tabela rh_vale_r_relatorio
$result2 = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao' AND mes = '$mes_referencia' AND ano = '$ano'");
$num_row_verifica2 = mysql_num_rows($result2);

if(empty($num_row_verifica2)) {
	if($acao == 'gravar') {
		mysql_query("INSERT rh_vale_relatorio SET id_protocolo = '$id_protocolo', id_reg = '$regiao', mes = '$mes_referencia', ano = '$ano', dias = '$numero_dias', valor_total = '$total', user = '$id_user', data = CURDATE(), status = 'GRAVADO'");
	}
}
?>
</div>
</body>
</html>