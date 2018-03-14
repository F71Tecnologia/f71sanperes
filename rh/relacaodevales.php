<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$mes_referencia = $_REQUEST['mes_referencia'];
$id_protocolo = $_REQUEST['id_protocolo'];
$status_pagina = $_REQUEST['status_pagina'];

$result_data = mysql_query("SELECT *, date_format(data_ini, '%d/%m/%Y') AS data_iniF, date_format(data_fim, '%d/%m/%Y') AS data_fimF FROM rh_vale_protocolo JOIN ano_meses WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'");
$row = mysql_fetch_array($result_data);

// Variaveis globais que serão usados para verificar quando cada funcionário entrou na empresa para fins de quantidade de vale transporte a ser distribuido
$GLOBALS["dataInicio"] = $row['data_ini'];
$GLOBALS["dataFim"] = $row['data_fim'];

$result_periodo = mysql_query("SELECT * FROM rh_vale_protocolo JOIN ano_meses WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao'");
$periodo = mysql_fetch_array($result_periodo);

// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA
$result_dias = mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND fds != 1");
$numero_de_dias = mysql_num_rows($result_dias);


// VERIFICA OS FERIADOS FEDERAIS NO PERÍODO
$result_feriadosFEDERAL = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data_federalF FROM rhferiados WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND tipo = 'Federal'");
$numero_de_feriadosFEDERAL = mysql_affected_rows();

// VERIFICA SE EXISTE FERIADOS REGIONAIS NESTE PERÍODO
$result_feriadosREGIONAL = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') AS data_regionalF FROM rhferiados WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND id_regiao = '$regiao' AND tipo = 'Regional'");
$numero_de_feriadosREGIONAL = mysql_affected_rows();

$numero_de_dias = ($numero_de_dias - $numero_de_feriadosFEDERAL) - $numero_de_feriadosREGIONAL;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>RELAT&Oacute;RIO DE VALES TRANPORTE</title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<style type="text/css">
table {
	font-size:9px;
}
h1 { 
	page-break-after:always;
}
</style>
</head>
<body>
<table width="100%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5">
  <tr>
    <td width="100%" align="center" valign="middle">
    	<p class="linha">
		<?php
		include "../../empresa.php";
		$rod = new empresa();
		$rod -> rodape();
		?>
        <br />
        <br />
    <span class="style2">Protocolo de Entrega de Vales - Transporte</span>
        </p>
      <?=$row['nome_mes'].' / '.$row['ano']?>
      <p class="linha">Referente a <span class="style2"><?=$numero_de_dias?></span> dias &uacute;teis entre <span class="style2"><?=$row['data_iniF']?></span> a <span class="style2"><?=$row['data_fimF']?></span></p>
      
<?php $cont01 = 0;
	  if(!empty($numero_de_feriadosFEDERAL) or !empty($numero_de_feriadosREGIONAL)) { ?>
  <table width="40%" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
	<tr>
	    <td bgcolor="#003300" colspan="3" align="center"><strong class="style1">FERIADO NO PERÍODO</strong></td>
	</tr>
	<tr>
	    <td align="center" bgcolor="#CCFFCC" style="width:33%;"><span class="linha">DATA</span></td>
	    <td align="center" bgcolor="#CCFFCC" style="width:33%;"><span class="linha">FERIADO</span></td>
	    <td align="center" bgcolor="#CCFFCC" style="width:34%;"><span class="linha">TIPO</span></td>
	</tr>
    <?php while($rowFederal = mysql_fetch_array($result_feriadosFEDERAL)) { ?>
	<tr bgcolor="<?php if(($cont01 % 2)==0) { echo '#ECF2EC'; } else { echo '#FFFFFF'; } ?>">
		<td align="center"><?=$rowFederal['data_federalF']?></td>
		<td align="center"><?=$rowFederal['nome']?></td>
		<td align="center"><?=$rowFederal['tipo']?></td>
    </tr>
    <?php $cont01 = $cont01 + 1;
		  }
		  while($rowFederal = mysql_fetch_array($result_feriadosREGIONAL)) { ?>
	<tr bgcolor="<?php if(($cont01 % 2)==0) { echo '#ECF2EC'; } else { echo '#FFFFFF'; } ?>">
		<td align="center"><?=$rowFederal['data_regionalF']?></td>
		<td align="center"><?=$rowFederal['nome']?></td>
		<td align="center"><?=$rowFederal['tipo']?></td>
	</tr>
	<?php $cont01 = $cont01 + 1;
		  }
	?>
</table>
<?php } ?>

<br>

      <table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
        <tr>
          <td colspan="10" align="center" valign="middle" bgcolor="#666666" width="100%"><strong class="style1">RELA&Ccedil;&Atilde;O DE FUNCION&Aacute;RIO BENEFICIADOS</strong></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#999999"><span class="linha">ID</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">NOME</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">TIPO</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">CÓDIGO</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">INTINERÁRIO</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">DIAS</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">VALOR</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">VALOR PARCIAL</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">VALOR TOTAL</span></td>
          <td align="center" bgcolor="#999999"><span class="linha">ASSINATURA DO FUNCION&Aacute;RIO</span></td>
        </tr>
        
<?php $result_vale = mysql_query("SELECT * FROM rh_vale JOIN rh_clt WHERE rh_vale.id_regiao = '$regiao' AND rh_vale.id_clt = rh_clt.id_clt AND rh_clt.status != '62' AND rh_vale.status_reg != NULL LIMIT 100");

$valor = "0";
$quant_vales = 0;
$cont = 0;

// Inicia a gravação da tabela rh_vale_r_relatorio para depois ser atualizado várias vezes em pontos específicos.

while($row = mysql_fetch_array($result_vale)){
	
	if(($cont % 2)==0) { 
		echo '<tr bgcolor=#ECF2EC>';
	} else { 
		echo '<tr bgcolor=#FFFFFF>'; 
	}

	//ID do funcionário
	$ID_FUNC = $row['id_clt'];

///////Confere a data de contratação do funcionário////////////////////////////////////////////////////
    /*
	$resultEntrada = mysql_query("SELECT data_entrada, data_saida, nome FROM rh_clt r where id_clt = '$ID_FUNC'");
	$rowEntrada = mysql_fetch_array($resultEntrada);
	
	$dataContrato = $rowEntrada['data_entrada'];
	$dataSaida = $rowEntrada['data_saida'];
	$dataIniPeriodo = $GLOBALS["dataInicio"];
	$dataFimPeriodo = $GLOBALS["dataFim"];
	
$numDiasEntra = 0;
if (($dataContrato != '0000-00-00')and($dataContrato>=$dataIniPeriodo)){	

/////////////////////////////////////////
///CONTAGENS DA ENTRADA DO FUNCIOÁRIO///
///////////////////////////////////////


		// CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a ENTRADA do funcionário e o início do periodo do vale transporte
		$result_diasEntrada=mysql_query("SELECT data, fds FROM ano WHERE data >= '$dataContrato' AND data <= '$dataContrato' AND fds = 1");
		$numero_de_diasEntrada=mysql_affected_rows();
		
		// VERIFICA OS FERIADOS FEDERAIS NO PERÍODO em que o funcionário entrou e o início do período do vale transporte
		$result_feriadosFEDERALEntrada=mysql_query("SELECT data, tipo FROM rhferiados WHERE data >='$dataIniPeriodo' and data <='$dataContrato' and tipo = 'Federal'");
		$numero_de_feriadosFEDERALEntrada=mysql_affected_rows();
		

		// VERIFICA SE EXISTE FERIADOS REGIONAIS no período entre a entrada do funcionário e o iniício do período do vale transporte
		$result_feriadosREGIONALEntrada=mysql_query("SELECT data, id_regiao, tipo FROM rhferiados WHERE data >='$dataIniPeriodo' and data <='$dataContrato' and id_regiao='$regiao' and tipo='Regional'");
		$numero_de_feriadosREGIONALEntrada=mysql_affected_rows();
		
		// DIFERENÇA TOTAL ENTRE A ENTRADA E O INÍCIO DO PERÍODO DO VALE TRANSPORTE
		$resultEntrada = mysql_query("SELECT DATEDIFF('$dataContrato','$dataIniPeriodo') AS teste");
		$diasAserDescontadoDaEntrada = mysql_fetch_array($resultEntrada);
				
		$numDiasEntra = $diasAserDescontadoDaEntrada['teste'] - ($numero_de_feriadosREGIONALEntrada+$numero_de_feriadosFEDERALEntrada+$numero_de_diasEntrada);
		
		// ESTE IF DEVE ESXISTIR PARA SITUAÇÕES COMO A SEGUINTE
		// SE O FUNCIONÁRIO FOR CONTRATADO NO MESMO DIA DO INÍCIO DO PERÍODO DO VALE, E EXISTIR UM FERIADO, NÃO RETORNARÁ -1 E SIM 0;
		if ($numDiasEntra < 0){
			$numDiasEntra = 0;
		}
		
		$avisoEntrada[] = $ID_FUNC;
		
		$avisoEntrada[] = $rowEntrada['nome'];
		$avisoEntrada[] = $dataContrato;
		$avisoEntrada[] = $numDiasEntra;
}

$totalDiasDescontoSaida = 0;

/////////////////////////////////////// 
///CONTAGENS DA SAÍDA DO FUNCIONÁRIO///
/////////////////////////////////////

if(($dataSaida != '0000-00-00')and($dataSaida<=$dataFimPeriodo)){
		//CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a saída do funcionário e o fim do periodo do vale transporte
		$result_diasSaida=mysql_query("SELECT * FROM ano WHERE data >= '$dataSaida' AND data <= 	'$dataFimPeriodo' AND fds = 1");
		$numero_de_diasSaida=mysql_affected_rows();

		//VERIFICA OS FERIADOS FEDERAIS NO PERÍODO em que o funcionário saíu e o fim do período do vale transporte
		$result_feriadosFEDERALSaida=mysql_query("SELECT data, tipo FROM rhferiados WHERE data >='$dataSaida' and data <='$dataFimPeriodo' and tipo = 'Federal'");
		$numero_de_feriadosFEDERALSaida=mysql_affected_rows();

		//VERIFICA SE EXISTE FERIADOS REGIONAIS no período entre a saída do funcionário e o fim do período do vale transporte
		$result_feriadosREGIONALSaida=mysql_query("SELECT data, id_regiao, tipo FROM rhferiados WHERE data >='$dataSaida' and data <='$dataFimPeriodo' and id_regiao='$regiao' and tipo='Regional'");
		$numero_de_feriadosREGIONALSaida=mysql_affected_rows();

		//DIFERENÇA TOTAL ENTRE A SAIDA E O FIM DO PERÍODO DO VALE TRANSPORTE
		$resultSaida = mysql_query("SELECT DATEDIFF('$dataFimPeriodo','$dataSaida') AS teste2");
		$diasAserDescontadoDaSaida = mysql_fetch_array($resultSaida);
		
		$totalDiasDescontoSaida = $diasAserDescontadoDaSaida['teste2'] -($numero_de_feriadosREGIONALSaida + $numero_de_feriadosFEDERALSaida	+ $numero_de_diasSaida);

		if ($totalDiasDescontoSaida < 0){
			$totalDiasDescontoSaida = 0;
		}
		
		$avisoSaida[] = $ID_FUNC;
		$avisoSaida[] = $rowEntrada['nome'];
		$avisoSaida[] = $dataSaida;
		$avisoSaida[] = $totalDiasDescontoSaida;
}
*/

	//Grava todos os nome impressos no Array 
	if (empty($row['id_clt'])) {
		break;
	}
	
	for($i=1; $i<=6; $i++) {
		$array_ID_FUNC[$cont][] = $ID_FUNC;
	}
	
	//Nome do funcionário
	$NOME = $row['nome'];
	if(empty($row['nome'])) {
		break;
	}
	
	for($i=1; $i<=6; $i++) {
		$array_NOME[$cont][] = $NOME;
	} ?>
	
	
	
	<td align="left" valign="middle"><?=$ID_FUNC?></td>

	<?php if(empty($row['id_clt'])) {
				break;
			} ?>
	
    <td align="left" valign="middle"><?=$NOME?></td>
    
	<?php if(empty($row['nome'])) {
				break;
		    } ?>
	
	<td align="left" valign="middle">
    
    <?php //Tipo
			for($i=1; $i<=6; $i++){
				$tarifa=$row['id_tarifa'.$i];
				$result_tarifas=mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
				$row_tarifas=mysql_fetch_array($result_tarifas);
				$TIPOS=$row_tarifas['tipo'];
				echo $TIPOS;
								
				if ($row_tarifas['tipo'] == ''){break;}
				$array_TIPOS[$cont][] = $TIPOS;
			} ?>
	
    </td>
    <td align="right" valign="middle">
    
    <?php //xx
			for($i=1; $i<=6; $i++){
				$tarifa=$row['id_tarifa'.$i];
				$result_tarifas=mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
				$row_tarifas=mysql_fetch_array($result_tarifas);
				$CODIGO = $row_tarifas['id_tarifas'];
				
				echo $CODIGO;
							
				if ($row_tarifas['id_tarifas'] == ''){break;}
				$array_CODIGO[$cont][] = $CODIGO;
			} ?>
            
	</td>
    <td align="left" valign="middle"  >
	
	<?php //Intenerário
	for($i=1; $i<=6; $i++){
		$tarifa=$row['id_tarifa'.$i];
		$result_tarifas=mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas=mysql_fetch_array($result_tarifas);
		$ITINERARIOS = $row_tarifas['itinerario'];	
		echo $ITINERARIOS;
		
		$numero_de_diasParcial = $numero_de_dias - ($numDiasEntra + $totalDiasDescontoSaida);
		//$arrayDiasParcial[$cont][] = $numero_de_diasParcial ;
		if ($row_tarifas['itinerario'] == ''){break;}
		$array_ITINERARIOS[$cont][] = $ITINERARIOS;
	} ?>
	
    </td>
    <td align="center" valign="middle">
    
    <?php //Quantidade	
	$numero_de_diasParcia2 = $numero_de_dias - ($numDiasEntra + $totalDiasDescontoSaida);

	///////////////////////////////////////////////////////////////////	
	//	IDENTIFICAR SE O FUNCIONÁRIO TRABALHA NOS FINAIS DE SEMANA  //
	/////////////////////////////////////////////////////////////////

	//IDENTIFICAR SE O FUNCIOÁRIO TRABALHA NO SÁBADO, DOMINGO OU AMBOS
	if ($row['id_clt'] == ''){break;}
	
	//SELECIONA O VALOR EM rh_clt.rh_horario POR FUNCIOÁRIO EM CADA VOLTA DO LAÇO DE REPETIÇÃO
	$resultHorarios = mysql_query("SELECT rh_horario FROM rh_clt WHERE id_clt = '$ID_FUNC'");
	$rowHorario = mysql_fetch_array($resultHorarios);
	
	//ANALIZA rh_horarios.folgas BASEADO NO VALOR DE $rowHorario[rh_horario]
	$resultFolgas = mysql_query("SELECT folga FROM rh_horarios where id_horario = '$rowHorario[rh_horario]'");
	$rowFolgas = mysql_fetch_array($resultFolgas);	
	$folgas = $rowFolgas['folga'];

	//CONTA O NUMERO DE DIAS DO FINAL DE SEMANA NO PERÍODO DE VIGENCIA DO VALE TRANSPORTE, OU SEJA, QUANTOS SABADOS E DOMINGOS
	$resultDiasFinalSemana=mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND fds = 1");
	$numerDiasFinsSemana=mysql_affected_rows();
	
	//CONTA A QUANIDADE DE SABADOS QUE O FUNCIOÁRIO IRÁ TRABALHAR NO PERIODO
	$resultSabadosSemana=mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND nome = 'Sábado'");
	$numeroSabadosSemana=mysql_affected_rows();

	//CONTA A QUANTIDADE DE DOMINGOS QUE O FUNCIOÁRIO IRÁ TRABALHAR NO PERIODO
	$resultDomingosSemana=mysql_query("SELECT * FROM ano WHERE data >= '$periodo[data_ini]' AND data <= '$periodo[data_fim]' AND nome = 'Domingo'");
	$numeroDomingosSemana=mysql_affected_rows();

	if($folgas == '5') {
		
		$resultPeriodo = mysql_query("SELECT data_fim, data_ini FROM rh_vale_protocolo WHERE id_protocolo = $id_protocolo AND id_reg='$regiao'")or die(mysql_error());
		$periodo = mysql_fetch_array($resultPeriodo);
		$inicialPeriodoVale = $periodo['data_ini'];
		$finalPeriodoVale = $periodo['data_fim'];
		
//PARA FINS DE CALCULO DO INTERVALO ENTRE O INÍCIO DO PERÍODO DO VALE E O FIM, É USADO ESTE SELECT PARA SOMAR UM DIA NA DATA FINAL
//VISTO QUE CASO ISSO NÃO SEJA FEITO O RESULTADO DA FUNÇÃO DATEDIFF RETORNA A DIFERENÇA COM 1 DIA A MENOS
		$resultFimperiodo=mysql_query("SELECT DATE_ADD(data_fim, INTERVAL 1 DAY) AS final FROM rh_vale_protocolo WHERE id_protocolo = $id_protocolo AND id_reg='$regiao'");
		$rowFimperiodo = mysql_fetch_array($resultFimperiodo);		
		$final = $rowFimperiodo['final'];
		
		//CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a ENTRADA do funcionário e o início do periodo do vale transporte
		$result_diasEntrada=mysql_query("SELECT DATEDIFF('$dataContrato','$inicialPeriodoVale') AS periodo");
		$rowDiasEntrada = mysql_fetch_array($result_diasEntrada);
		$entrada = $rowDiasEntrada['periodo'] + 1; //DEVE SOMAR 1 PARA COMPENSAR A FALHA DA FUNÇÃO DATADIFF, POIS ELA NÃO CONTA O PRIMEIRO DIA
		print $entrada;


		//CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a Saída do funcionário e o final do periodo do vale transporte
		$result_diasSaida=mysql_query("SELECT DATEDIFF('$finalPeriodoVale','$dataSaida') AS periodo");
		$rowDiasEntrada = mysql_fetch_array($result_diasSaida);
		$Saida = $rowDiasSaida['periodo'] + 1; //DEVE SOMAR 1 PARA COMPENSAR A FALHA DA FUNÇÃO DATADIFF, POIS ELA NÃO CONTA O PRIMEIRO DIA
		print $Saida;

		print '('.$numero_de_diasEntrada02.')';
		
		//CONTA O INTERVALO DE DIAS SEM OS FINAIS DE SEMANA entre a saída do funcionário e o fim do periodo do vale transporte
		$result_diasSaida=mysql_query("SELECT * FROM ano WHERE data >= '$dataSaida' AND data <= 	'$dataFimPeriodo' AND fds = 1");
		$numero_de_diasSaida02=mysql_affected_rows();		
				
		$resultDiasPeriodoVale = mysql_query("SELECT DATEDIFF('2009-03-31','2009-03-01') AS periodo");
		$rowPeriodo = mysql_fetch_array($resultDiasPeriodoVale);
		
		$periodo = $rowPeriodo['periodo'];
		$periodo = $periodo - ($numero_de_diasEntrada02);
		$periodo = $periodo/2;
		$numero_de_diasParcia2 = ceil($periodo);

		$extra = '(PlAN)';
		echo $numero_de_diasParcia2.' '.$extra.'<br>';
		
	}	
	if ($folgas == '3'){
		echo $numero_de_diasParcia2.'<br>';
	}
	if ($folgas == '2'){
		$numero_de_diasParcia2 = $numeroDomingosSemana+1;
		$extra = '(DOM)';
		echo $numero_de_diasParcia2.' '.$extra.'<br>';
	}
	if ($folgas == '1'){
		$numero_de_diasParcia2 = $numero_de_diasParcia2+$numeroSabadosSemana;
		$extra = '(SAB)';
		echo $numero_de_diasParcia2.' '.$extra.'<br>';

	}

	if ($folgas == '0'){

		$numero_de_diasParcia2 = $numero_de_diasParcia2+$numerDiasFinsSemana;
		$extra = '(SAB e DOM)';
		echo $numero_de_diasParcia2.' '.$extra.'<br>';
		
	}
	
	$array_extra[$cont][] = $extra;
	$arrayDiasParcial[$cont][] = $numero_de_diasParcia2;
	//if ($numero_de_diasParcial == ''){break;} ?>
    
	</td>
	<td align="center">
    
	<?php //Valor
	    for($i=1; $i<=6; $i++){
		$tarifa=$row['id_tarifa'.$i];
		
		$result_tarifas=mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas=mysql_fetch_array($result_tarifas);
		if($row_tarifas['valor']!=0){
			$TARIFAS=$row_tarifas['valor'];
			
			echo $TARIFAS.'<br>';
									
			if ($row_tarifas['valor'] == ''){break;}
			
		}
		$valor_parcial = $row_tarifas['valor'];			
		//Muda de valor com virgula para valor com ponto para fins de calculos
		$valor2 = str_replace(".","",$valor_parcial);
		$valor2 = str_replace(",",".",$valor2);
		
		//Valor com a tarifa
		$array_TARIFAS[$cont][] = $valor2;
		$totaParcialDia = (($valor2*$numero_de_diasParcia2)*2);
		
		$valor = $valor+$totaParcialDia;
		$total = $valor;
		
		$array_valor_parcial[$i] = $valor;		

	}
	unset($array_valor_parcial);
    unset($valor); ?>

	</td>
    <td align="center">
			
    <?php // Valor Parcial
	for($i=1; $i<=6; $i++) {
		
		$tarifa = $row['id_tarifa'.$i];
		$result_tarifas = mysql_query("SELECT * FROM rh_tarifas WHERE id_tarifas = '$tarifa'");
		$row_tarifas = mysql_fetch_array($result_tarifas);
		$valor_parcial = $row_tarifas['valor'];
		$valor_parcial = str_replace(".","",$valor_parcial);
		$valor_parcial = str_replace(",",".",$valor_parcial);
		$totalParcialDia = $valor_parcial;
		
		if(!empty($totalParcialDia)) {
			
			$array_total_parcial[$cont][] = $totalParcialDia;
			$totalParcialDia = number_format($totalParcialDia,2,",",".");			
			echo $totalParcialDia.'<br>';

			if(empty($totalParcialDia)) {
				break;
			}
						
		}

	}
	
	unset($array_valor_parcial);
    unset($valor);
    ?>
    
	</td>
    <td align="center" valign="middle">

	<?php //Valor Total
	if(empty($total)) {
		break;
	}
	
	$array_total[$cont][] = $total;
	
	$total = number_format($total,2,",",".");
	echo $total; ?>
	
    </td>
	<td align="center" valign="middle">__________________________</td>	
  </tr>
  
  <?php $arrayDiasParcial[$cont][] = $numero_de_diasParcia2;
		$cont = $cont+1;
		} ?>
      </table>
      
      <h1><!---Aqui a página é quebrada--></h1>
      
      <table width="100%" border="1" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
        <tr>
          <td colspan="8" align="center" valign="middle" bgcolor="#666666" class="style1">
            <strong>RESUMO DE VALES - TRANSPORTE - ENTREGUES</strong>
          </td>
        </tr>
        <tr>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">CÓDIGO</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">ITINER&Aacute;RIO</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">TIPO</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">QUANTIDADE</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">VALES</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">VALOR DIÁRIO</span></td>
          <td align="center" valign="middle" bgcolor="#999999"><span class="linha">VALOR /ITINERÁRIO</span></td>
        </tr>
        
<?php    
//Gravando registros na tabela rh_vale_r_relatorio, caso $ACAO sejas igual a gravar.
$ACAO = $_REQUEST['acao'];

//Somente 6, pois cada funcionário só possui 6 tipos de vales diferentes.
$cont2 = 6;

for($l=0; $l<$cont; $l++){
	for($c=0; $c<$cont2; $c++){
		if ($array_CODIGO[$l][$c] != 0){
			$ID_FUNC_r = $array_ID_FUNC[$l][$c];
			$NOME_r = $array_NOME[$l][$c];
			$CODIGO_r = $array_CODIGO[$l][$c];
			$TIPO_r = $array_TIPOS[$l][$c];
			$ITINERARIO_r = $array_ITINERARIOS[$l][$c];
			$TARIFAS_r = $array_TARIFAS[$l][$c];
			$VALOR_PARCIAL_r = $array_total_parcial[$l][$c];
			$VALOR_TOTAL_FUNC = $array_total [$l][$c];
			$NUM_DIAS_PARCIAL_r = $arrayDiasParcial[$l][$c].'<br>';
			$EXTRA = $array_extra[$l][$c];
			if(($ACAO=='gravar')and($status_pagina!='voltar')){	
				mysql_query("INSERT rh_vale_r_relatorio SET id_protocolo='$id_protocolo', mes='$mes_referencia', id_func='$ID_FUNC_r', nome='$NOME_r', id_reg='$regiao', codigo='$CODIGO_r', tipo='$TIPO_r', itinerario='$ITINERARIO_r', valor='$TARIFAS_r', quantidade='$NUM_DIAS_PARCIAL_r', extra = '$EXTRA', valor_parcial='$VALOR_PARCIAL_r', valor_total_func = '$VALOR_TOTAL_FUNC'")or die(mysql_error());
							
			}
		}
	}
}

$resultTotais01 = mysql_query("SELECT id_protocolo,mes,id_reg,codigo FROM rh_vale_r_relatorio WHERE id_protocolo = $id_protocolo AND mes = $mes_referencia AND id_reg = $regiao ORDER BY codigo");

while($row01 = mysql_fetch_array($resultTotais01)){
	$codigo[]=$row01['codigo'];
}

//DEFINE QUANTOS ELEMENTOS O ARRAY POSSUY
$contCodigo = @count($codigo);
//RETIRA OS ELEMENTOS REPETIDOS DO ARRAY
$codigoAUX = @array_unique($codigo);
//CONTA QUANTOS ELEMENTOS RESTARAM NO ARRAY
$quantCodigoAUX = @count($codigoAUX);
//ORGANIZA OS ELEMENTOS DO ARRAY
for($i=0; $i<$quantCodigoAUX; $i++){
	$codigoAUX2[$i] = @current($codigoAUX);
	@next($codigoAUX);
}
$cont = 0;
$quantidade= 0;
for($i=0; $i<$quantCodigoAUX; $i++) {
	
	$resultTotais = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = $id_protocolo AND mes = $mes_referencia AND id_reg = $regiao AND codigo='$codigoAUX2[$i]' ORDER BY codigo");
    $row = mysql_fetch_array($resultTotais);
	
	//DEFINE A QUANTIDADE DE VALES DE UM DETERMINADO ITINERÁRIO
	$r = mysql_query("SELECT quantidade, id_protocolo,mes,id_reg,codigo  FROM rh_vale_r_relatorio WHERE id_protocolo = $id_protocolo AND mes = $mes_referencia AND id_reg = $regiao AND codigo='$codigoAUX2[$i]' ORDER BY codigo");
	
	while($rowQuant = mysql_fetch_array($r)){
		$quantidade = $quantidade+$rowQuant['quantidade'];
	}



	//MOSTRA O VALOR TOTAL DOS VALES DO TIPO CARTÃO
	$resultCartoes = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = $id_protocolo AND mes = $mes_referencia AND id_reg = $regiao AND codigo='$codigoAUX2[$i]' and tipo = 'CARTÃO' ORDER BY codigo");
	while ($rowCartoes = mysql_fetch_array($resultCartoes)){
		$arrayCartao[] = $rowCartoes['valor_parcial'];	
	}
   
    
	
	//MOSTRA O VALOR TOTAL DOS VALES DO TIPO PAPEL
	$resultPapel = mysql_query("SELECT * FROM rh_vale_r_relatorio WHERE id_protocolo = $id_protocolo AND mes = $mes_referencia AND id_reg = $regiao AND codigo='$codigoAUX2[$i]' and tipo = 'PAPEL' ORDER BY codigo");
	while ($rowPapel = mysql_fetch_array($resultPapel)){
		$arrayPapel[] = $rowPapel['valor_parcial'];	
	}



	//DEFINE QUANTAS VEZES UM DETERMINADO ITINERÁRIO REPETE
	for ($j=0; $j<=$contCodigo; $j++){
		if ($codigo[$j] == $codigoAUX2[$i]){
			$cont = $cont + 1;
		}
	}
	
	echo '<tr>';
	echo '<td>'.$row['codigo'].'</td>';
	echo '<td>'.$row['itinerario'].'</td>';
	echo '<td>'.$row['tipo'].'</td>';
	echo '<td>'.$cont.'</td>';
	
	$quantidade = $quantidade*2;
	echo '<td>'.$quantidade.'</td>';
	
	$valor = $row['valor'];
	$valor=number_format($valor,2,",",".");
	echo '<td>'.$valor.'</td>';
	
	$valorParcial = $row['valor']*$quantidade;
	$valorTotal[] = $valorParcial;
	$valorParcial = number_format($valorParcial,2,",",".");
	
	echo '<td>'. $valorParcial.'</td>';
	$cont = 0;
	$quantidade = 0;
	echo '</tr>';
}
	$total = @array_sum($valorTotal);
	$cartao = @array_sum($arrayCartao);
	$papel = @array_sum($arrayPapel);
	
	//MOSTRA O TOTAL DE VALES DO TIPO PAPEL
	echo '<tr>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td bordercolor="#FFFFFF" align="right">TOTAL DO TIPO PAPEL:</td>';
		  $papel=number_format($papel,2,",",".");
	echo '<td><font color="#FF0000" size="3px">'.$papel.'</font></td>';	
	echo '</tr>';
	//MOSTRA O TOTAL DE VALES DO TIPO CARTÃO
	echo '<tr>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td bordercolor="#FFFFFF" align="right">TOTAL DO TIPO CARTÃO:</td>';
		  $cartao = number_format($cartao,2,",",".");
	echo '<td><font color="#FF0000" size="3px">'.$cartao.'</font></td>
		  </tr>';
	//MOTRA O VALOR TOTAL
	echo '<tr>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td bordercolor="#FFFFFF" align="right"><strong>VALOR TOTAL:</strong></td>';
		  $total=number_format($total,2,",",".");
	echo '<td><font color="#FF0000" size="3px"><strong>'.$total.'</strong></font></td>
		  </tr>';

//Marca na tabela rh_vale_protocolo no campo status que o protocolo foi impresso
mysql_query("UPDATE rh_vale_protocolo SET status = 'IMPRESSO' WHERE id_protocolo='$id_protocolo' AND id_reg = '$regiao'");

//Gravando na tabela rh_vale_relatorio os dados gerais, os detalhes erão gravados na tabela rh_vale_r_relatorio
$ANO = date('Y');
$result2 = mysql_query("SELECT * FROM rh_vale_relatorio WHERE id_protocolo = '$id_protocolo' AND id_reg = '$regiao' AND mes = '$mes_referencia' AND ano = '$ANO'");
$num_row_verifica2 = mysql_num_rows($result2);

if(empty($num_row_verifica2)) {
	if($ACAO == 'gravar') {
		mysql_query("INSERT rh_vale_relatorio SET id_protocolo = '$id_protocolo', id_reg = '$regiao', mes = '$mes_referencia', ano='$ANO', dias = '$numero_de_dias', valor_total = '$valorTotal', user = '$id_user', data = CURDATE(), status = 'GRAVADO'");
	}
}
?>
      </table>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>