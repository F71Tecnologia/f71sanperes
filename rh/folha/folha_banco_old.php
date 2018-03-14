<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');

$Regi = new regiao();

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = str_replace('--', '+', $_REQUEST['enc']);
list($regiao,$folha,$banco) = explode('&', decrypt($enc));
//

if(!empty($_REQUEST['agencia'])){

	$ag  		= $_REQUEST['agencia'];
	$cc  		= $_REQUEST['conta'];
	$clt 		= $_REQUEST['clt'];
	$tipo_conta = $_REQUEST['radio_tipo_conta'];
	
	$RE_clt = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha_proc = '$clt' and status = 3 and tipo_pg = '0'") or die (mysql_error());
	$RowCLT = mysql_fetch_array($RE_clt);
	
	mysql_query("UPDATE rh_clt SET agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_clt = '$RowCLT[id_clt]'") or die (mysql_error());
	mysql_query("UPDATE rh_folha_proc SET agencia='$ag', conta='$cc' WHERE id_folha_proc = '$clt'") or die (mysql_error());

}

$dataPagamento = $_REQUEST['data'];

list($d,$m,$a) 		 = explode('/', $dataPagamento);
list($dia,$mes,$ano) = explode('/', date('d/m/Y'));

$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user 	 = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'"); // 4 FAHJEL                 /          $row_user[id_master]
$row_master    = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT * , date_format(data_proc, '%d/%m/%Y') AS data_proc2, date_format(data_inicio, '%d/%m/%Y') AS data_inicio, date_format(data_fim, '%d/%m/%Y') AS data_fim FROM rh_folha WHERE id_folha = '$folha'");
$row_folha 	  = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto 	= mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
$row_banco 	  = mysql_fetch_array($result_banco);

$meses        = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_inteiro  = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_inteiro];

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$data_menor14 = date('Y-m-d', mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date('Y-m-d', mktime(0,0,0, $mes,$dia,$ano - 21));
?>
<html>
<head>
<script type="text/javascript" src="../../js/prototype.js"></script>
<script type="text/javascript" src="../../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../../js/lightbox.js"></script>
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
</head>
<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><div style="font-size:9px; text-align:left; color:#E2E2E2;"><b>ID:
      <?php
    echo $folha.", regi&atilde;o: ";
	$Regi -> MostraRegiao($row_folha['regiao']);
	echo $Regi -> regiao;
	echo " CLT  - Banco: $banco";
	?>
    </b></div>

      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="93" align="center" class="show">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></td>
            <td width="62%">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </td>
            <td width="22%">
            Data de Processamento: <?=$row_folha['data_proc2']?>
            <br>
            Data para Pagamento: <?=$d.'/'.$m.'/'.$a?>
            </td>
            </tr>
        </table>
        </td>
      </tr>
    </table>
      <br>
      <table width="325" border="0">
        <tr>
          <td width="52"><img src="../../imagens/bancos/<?=$row_banco['id_nacional']?>.jpg" width="50" height="50"></td>
          <td width="257"><div style="font-size:16px">&nbsp;<?=$row_banco['nome']?></div></td>
        </tr>
      </table>
	  <br />
      <span class="title">Folha de Pagamento - <?=$mes_da_folha?> / <?=$ano?></span>
      <br /><br />
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="5%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="18%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">CPF</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Agência</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Conta</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Imp. Renda</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Fam. </td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Tipo Conta</td>          
        </tr>
        
       <?php // VERIFICA OS TIPOS DE PAGAMENTOS DA REGIÃO E PROJETO ATUAL
			$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' AND campo1 = '1' AND id_projeto = '$row_projeto[0]'");
			$rowTipoPg = mysql_fetch_array($tiposDePagamentos);
			
	   		if($row_banco['id_nacional'] == '237') {
				
			// Nome do Arquivo Texto
			$CONSTANTE = 'FP';
			$DD = date('d');
			$MM = date('m');
			$NUM_ARQUIVO01 = '2';
			$NUM_ARQUIVO02 = '1';
			$TIPO = 'TST';
				
			// VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
			$resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco' AND tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido != '0.00'");				
			while ($rowContas = mysql_fetch_array($resultContas)) {
										
				$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
				
				if(($rowTiposDeConta['tipo_conta'] == 'corrente') and ($rowTiposDeConta['tipo_conta'] != '')) {
					
					$contaCorrente = 'corrente';
				
				} elseif(($rowTiposDeConta['tipo_conta'] == 'salario') and ($rowTiposDeConta['tipo_conta'] != '')) {
					
					$contaSalario = 'salario';
					
				}
			}		
				
			//EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
			if ($contaCorrente != ''){
				$NUM_ARQUIVO = $NUM_ARQUIVO01;
				include "BANCOS/BRADESCO/header_bradesco_corrente.php";
			}
			
			if ($contaSalario != ''){
						$NUM_ARQUIVO = $NUM_ARQUIVO02;
						include "BANCOS/BRADESCO/header_bradesco_salario.php";					
			}
					  
			} else if($row_banco['id_nacional'] == '356'){
					
					$CONSTANTE = 'FP_BANCO_REAL_'.$regiao.'_'.$folha;
					$DD = date('d');
					$MM = date('m');
					$ANO= date('Y');
					$NUM_ARQUIVO01 = '1';
					$NUM_ARQUIVO02 = '2';
					$NUM_ARQUIVO03 = '3';
					
					//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
					$resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$banco' and tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido != '0.00'");				
					while ($rowContas = mysql_fetch_array($resultContas)){					
							$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
		  					$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		
				
				if ($contaCorrente != ''){
							include "BANCOS/REAL/header_arquivo_real_corrente.php";
							include "BANCOS/REAL/header_lote_real_corrente.php";					
				}
				
				if ($contaSalario != ''){
							include "BANCOS/REAL/header_arquivo_real_salario.php";
							include "BANCOS/REAL/header_lote_real_salario.php";					
				}			  			
		  			
															
		}else if ($row_banco['id_nacional'] == '341'){
				$CONSTANTE = 'FP_BANCO_ITAU_'.$regiao.'_'.$folha;
				$DD = date('d');
				$MM = date('m');
				$ANO= date('Y');
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
				$resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$banco' and tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido != '0.00'");				
				while ($rowContas = mysql_fetch_array($resultContas)){					
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
		  				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		
				
				//EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
					include "BANCOS/ITAU/header_itau_corrente.php";				
				}
				if ($contaSalario != ''){
							include "BANCOS/ITAU/header_itau_salario.php";					
				}			  			
		}else if ($row_banco['id_nacional'] == '001'){

				$CONSTANTE = 'FP_BANCO_BRASIL_'.$regiao.'_'.$folha;
				$DD	= date('d');
				$MM	= date('m');
				$ANO= date('Y');
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARIÁVEL ESPECÍFICA COM O TIPO DE CONTRA ENCONTRADO
				$resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco' AND tipo_pg = '$rowTipoPg[id_tipopg]' AND salliquido != '0.00'");				
				while ($rowContas = mysql_fetch_array($resultContas)){					
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
		  				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
						
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		

				// EXECUTA OS CABEÇALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
					include "BANCOS/BRASIL/header_brasil_corrente.php";				
				}
				if ($contaSalario != ''){
					include "BANCOS/BRASIL/header_brasil_salario.php";			
				}			  			
		}
       
       
          
		  
		  
		  
		  
		  
		  
		  
		  $cont = "0";
		  
		  $resultClt = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco' ORDER BY nome ASC");
		  while($row_clt = mysql_fetch_array($resultClt)) {
		  
		  $REtabCLT  = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
		  $RowTabCLT = mysql_fetch_array($REtabCLT);

		  // Verificando Tipo de Pagamento
		  $qr_tipo_pg = mysql_query("SELECT tipopg FROM tipopg WHERE id_tipopg = '$row_clt[tipo_pg]'");
		 @$tipo_pg    = mysql_result($qr_tipo_pg,0);
		 
		  if(strstr($tipo_pg,'Conta') or strstr($tipo_pg,'conta')) {
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  $nome  = str_split($row_clt['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  //-----------------		  		  
		  
		  //----FORMATANDO OS VALORES FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00) ---------
		  $salario_brutoF = number_format($row_clt['salbase'],2,",",".");
		  $total_rendiF = number_format($row_clt['rend'],2,",",".");
		  $total_debitoF = number_format($row_clt['desco'],2,",",".");
		  $valor_inssF = number_format($row_clt['a5020'],2,",",".");
		  $valor_IRF = number_format($row_clt['a5021'],2,",",".");
		  $valor_familiaF = number_format($row_clt['a5022'],2,",",".");

		  $valor_final_individualF = number_format($row_clt['salliquido'],2,",",".");			  	  
		  //-------------------
		
		  $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
		  $rowTipoConta = mysql_fetch_array($resultTipoConta);
		  
		  switch($rowTipoConta['tipo_conta']) {
			  case 'salario': $tipoConta = 'Conta Salário';
			  break;
			  case 'corrente': $tipoConta = 'Conta Corrente';
			  break;
			  default: $tipoConta = '&nbsp;';
		  }
			
		  $tipoR = $RowTabCLT['tipo_conta'];
			
		  if ($tipoR == 'salario') {
			  $checkedSalario = 'checked';	
		  } elseif ($tipoR == 'corrente') {
			  $checkedCorrente = 'checked';
		  }
			
			
			$alink = "<a href='#' onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', 
			wrapperClassName: 'draggable-header',headingText: '$nomeT' } )\" class='highslide'>";
			
			$divTT = "<div class='highslide-maincontent'>
			<form action='' method='post' name='form'>
			<table width='526' border='0' cellspacing='0' cellpadding='0'>
			  <tr>			  
			    <td align='right'>Agencia</td>
			    <td>&nbsp;<input name='agencia' type='text' size='15' maxlength='10' id='agencia' value='$row_clt[agencia]'/>&nbsp;</td>
			    <td align='right'>Conta</td>
			    <td>&nbsp;<input name='conta' type='text' size='15' maxlength='10' id='conta' value='$row_clt[conta]'/></td>
			    <td><input type='submit' value='Enviar' /></td>
			  </tr>
			  <tr>
			    <td align='right'>Tipo de Conta</td>
			    <td colspan='3'>&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Salário </label>
				&nbsp;&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label></td>
			  </tr>			  
			</table>
			<input type='hidden' name='enc' value='$enc'>
			<input type='hidden' name='clt' value='$row_clt[0]'>
			</form>
			</div>";
			
			$bgclass = ($cont % 2) ? "corfundo_um" : "corfundo_dois";
			
		  print"
		  <tr class='novalinha $bgclass'>
          <td align='center' valign='middle' $bord style='font-size:10px'>$row_clt[cod] </td>
          <td align='lefth' valign='middle' $bord style='font-size:10px'>$alink $nomeT</a> $divTT</td>
		  <td align='right' valign='middle' $bord style='font-size:10px' >$row_clt[cpf]</td>
		  <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[agencia]</td>
		  <td align='right' valign='middle' $bord style='font-size:10px'>$row_clt[conta]</td>		  
          <td align='right' valign='middle' $bord style='font-size:10px'>$salario_brutoF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$total_rendiF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$total_debitoF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$valor_inssF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$valor_IRF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$valor_familiaF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'>$valor_final_individualF</td>
          <td align='right' valign='middle' $bord style='font-size:10px'> $tipoConta </td>		  
		  </tr>";
		  
		  unset($checkedSalario);
		  unset($checkedCorrente);
		  
		  if ($row_banco['id_nacional'] == '237'){
		  		$tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
		  		if ($tipoContaCorrente01 == 'corrente'){			  
						$NUM_ARQUIVO = $NUM_ARQUIVO01;			  
						$statusContaCorrente = 'corrente';			
						include "BANCOS/BRADESCO/detalhes_bradesco_corrente.php";
		  		}else if($tipoContaCorrente01 =='salario'){
							$NUM_ARQUIVO = $NUM_ARQUIVO02;
							$statusContaSalario = 'salario';
							 include "BANCOS/BRADESCO/detalhes_bradesco_salario.php";
		  		}
		  }
		  
		  if ($row_banco['id_nacional'] == '001'){
		  		$tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
		  		if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '001')){			  		  
						$statusContaCorrente = 'corrente';	
						include "BANCOS/BRASIL/detalhes_brasil_corrente.php";
		  		}else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '001')){
							$statusContaSalario = 'salario';
							 include "BANCOS/BRASIL/detalhes_brasil_salario.php";
		  		}
		  }
		  
		  
		  if ($row_banco['id_nacional'] == '356'){
			  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
			  if ($tipoContaCorrente01 =='corrente'){			  			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
					$VALOR = $row_clt['salliquido'];
					//$arrayValorTotalCorrente[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);			
					$statusContaCorrente = 'corrente';			
			  		include "BANCOS/REAL/detalhes_real_corrente.php";
			 }else if($tipoContaCorrente01 =='salario'){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SALÁRIO	  
					$VALOR = $row_clt['salliquido'];
					//$arrayValorTotalSalario[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);										
			  		$statusContaSalario = 'salario';
					include "BANCOS/REAL/detalhes_real_salario.php";
			 }			  
		  }else if($row_banco['id_nacional'] == '341'){
			  
		  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];		  
		  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '341')){			  			  
			//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
			$VALOR = $row_clt['salliquido'];
			//$arrayValorTotalCorrente[] = $VALOR;
			$remover = array(".", "-", "/",",");
			$VALOR = str_replace($remover, "", $VALOR);
			$VALOR = sprintf("%013d",$VALOR);
			
			$statusContaCorrente = 'corrente';
			
			include "BANCOS/ITAU/detalhes_itau_corrente.php";
		  }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '341')){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SALÁRIO	  
					$VALOR = $row_clt['salliquido'];
				//	$arrayValorTotalSalario[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);										
			  		$statusContaSalario = 'salario';
			 		 include "BANCOS/ITAU/detalhes_itau_salario.php";
		  }
		  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '001')){			  			  			
			$statusContaCorrente = 'corrente';			
			include "BANCOS/BRASIL/detalhes_brasil_corrente.php";
		  }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '001')){			  									
			  		$statusContaSalario = 'salario';
			 		 include "BANCOS/BRASIL/detalhes_brasil_salario.php";
		  }			  
		  }
		  
		  unset($tipoContaCorrente01);		  
		// AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO		  
		// SOMANDO VARIAVIES PARA CHEGAR AO VALOR FINAL
		$salario_brutoFinal = $salario_brutoFinal + $row_clt['salbase'];
		$total_rendiFinal = $total_rendiFinal + $row_clt['rend'];
		$total_debitoFinal = $total_debitoFinal + $row_clt['desco'];
		$valor_inssFinal = $valor_inssFinal + $row_clt['a5020'];
		$valor_IRFinal = $valor_IRFinal + $row_clt['a5021'];
		$valor_familiaFinal = $valor_familiaFinal + $row_clt['a5022'];
		$valor_liquiFinal = $valor_liquiFinal + $row_clt['salliquido'];

		$cont ++;		
		}
		  }

		// FORMATANDO OS DADOS FINAIS - FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00)
		$salario_brutoFinalF = number_format($salario_brutoFinal,2,",",".");
		$total_rendiFinalF = number_format($total_rendiFinal,2,",",".");
		$total_debitoFinalF = number_format($total_debitoFinal,2,",",".");
		$valor_inssFinalF = number_format($valor_inssFinal,2,",",".");
		$valor_IRFinalF = number_format($valor_IRFinal,2,",",".");
		$valor_familiaFinalF = number_format($valor_familiaFinal,2,",",".");
		$valor_liquiFinalF = number_format($valor_liquiFinal,2,",",".");
		?>
        
         <tr>
          <td height="20" align="center" valign="middle" >&nbsp;</td>
          <td height="20" align="right" valign="bottom" >TOTAIS:</td>
          <td align="right" valign="bottom" ><?=$salario_brutoFinalF?></td>
          <td align="right" valign="bottom" ><?=$total_rendiFinalF?></td>
          <td align="right" valign="bottom" ><?=$total_debitoFinalF?></td>
          <td align="right" valign="bottom" ><?=$valor_inssFinalF?></td>
          <td align="right" valign="bottom" ><?=$valor_IRFinalF?></td>
          <td align="right" valign="bottom" ><?=$valor_familiaFinalF?></td>
          <td align="right" valign="bottom" ><?=$valor_liquiFinalF?></td>
          <td align="right" valign="bottom" >&nbsp;</td>
        </tr>        
      </table>
      <br>
      <br>
      <?=$cont." Participantes<br/>"?>
      
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$folha"); 
$linkvolt = str_replace("+","--",$linkvolt);

$linkselect = encrypt("$regiao&$folha&$row_banco[0]&$dataPagamento");
$linkselect = str_replace("+","--",$linkselect);

// -----------------------------
?>
</td>
</table>

<?
if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '237')){
		$NUM_ARQUIVO = $NUM_ARQUIVO01;
		include "BANCOS/BRADESCO/trailler_bradesco_corrente.php";
}
if(($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '237')){
		$NUM_ARQUIVO = $NUM_ARQUIVO02;
		include "BANCOS/BRADESCO/trailler_bradesco_salario.php";
}
if (($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '356')){
		$valor_liquiFinalF = array_sum($arrayValorTotalSalario);
		$VALOR_TOTAL = $valor_liquiFinalF;
		$remover = array(".", "-", "/",",");
		$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
		$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
		include "BANCOS/REAL/trailler_lote_real_salario.php";	
		include "BANCOS/REAL/trailler_arquivo_real_salario.php";

}
if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '356')){
			$valor_liquiFinalF = array_sum($arrayValorTotalCorrente);
			$VALOR_TOTAL = $valor_liquiFinalF;
			$remover = array(".", "-", "/",",");
			$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
			$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
			include "BANCOS/REAL/trailler_lote_real_corrente.php";	
			include "BANCOS/REAL/trailler_arquivo_real_corrente.php";
				
}else if($row_banco['id_nacional'] == '341'){
			if($statusContaSalario != ''){	
					include "BANCOS/ITAU/trailler_itau_salario.php";
			}
			if($statusContaCorrente != ''){
					include "BANCOS/ITAU/trailler_itau_corrente.php";
			}
}
if($row_banco['id_nacional'] == '001'){
			if($statusContaSalario != ''){	
					include "BANCOS/BRASIL/trailler_brasil_salario.php";
			}
			if($statusContaCorrente != ''){
					include "BANCOS/BRASIL/trailler_brasil_corrente.php";
			}
}

?>
    <?
	if ($row_banco['id_nacional'] == '341'){
			//$arquivo = 'BANCOS/ITAU/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
	}else if($row_banco['id_nacional'] == '356'){
			$arquivo = 'BANCOS/REAL/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
	}else if($row_banco['id_nacional'] == '237'){
			$arquivo = 'BANCOS/BRADESCO/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt";	
	}else if($row_banco['id_nacional'] == '001'){
			$arquivo = 'BANCOS/BRASIL/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
	}
	?>
    
     <?
	$id_projeto = $row_projeto[0];	
    $id_banco = $row_banco[0];
	//$id_user;
	$nome = 'teste FOLHA DE PAGAMENTO';
	$especifica = 'teste FOLHA DE PAGAMENTO - CREDITO EM '.$DD."-".$MM."-".$ANO;
	$tipo = '30';
	//$valorTotalLiquido = number_format($valor_liquiFinalF, 2, ".",",");
	
	$valor = str_replace(".", "", $valor_liquiFinalF); 
	$valor = str_replace(",", ".", $valor); 
	
	$data_proc = date('Y-m-d H:i:s');
	$data_vencimento = $ANO."-".$MM."-".$DD;
	$status = '1';
	$linkfin = encrypt("$regiao&$folha&$row_banco[0]&$id_projeto&$id_user&$nome&$especifica&$tipo&$valor&$data_proc&$data_vencimento&$status");
	$linkfin = str_replace("+","--",$linkfin);	
	?>
<table width="95%" border="0" align="center">
    <tr>
    <td align="center" valign="middle" bgcolor="#999999">
    <a href='#' style="text-decoration:none; color:#000" onClick="Confirm(<?=$regiao?>,<?=$folha?>)" class="botao">FINALIZAR</a>
    
    &nbsp;&nbsp;&nbsp;
    
    <a href='ver_folha.php?<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000" class="botao">VOLTAR</a>
    
    &nbsp;&nbsp;&nbsp;&nbsp;
    
    <a href='folha_banco_a.php?<?="enc=".$linkselect."&sel=1"?>' style="text-decoration:none; color:#000" class="botao">
    SELECIONAR FUNCION&Aacute;RIOS A SEREM PAGOS</a>
    
    </td> 
  </tr>
    <tr>
    </table>
    <br/>
    <br/>
<script language="javascript">
    
function Confirm(a,b){
	var Regiao = a;
	var Folha = b;	
	
	input_box=confirm("Deseja realmente FINALIZAR?\n\nLembrando que após a confirmação, não podera gerar novamente o ARQUIVO TEXTO do banco!");
	
	if (input_box==true){ 
		// Output when OK is clicked
		// alert (\"You clicked OK\"); 
		location.href="finalizando.php?enc=<?=$linkfin?>";
		}else{
		// Output when Cancel is clicked
		// alert (\"You clicked cancel\");
	}

}
    
</script>
    <table bgcolor="#FFFFFF" width="40%" align="center">
    <tr>
    	<td align="center" valign="middle" bgcolor="#666666"><span class="igreja"> <strong>DOWNLOAD DO ARQUIVO TEXTO</strong> </span></td>
    </tr>
    <tr>
    <td align='center'>&nbsp;</td>
    <tr>
    <td></td>
    </tr>
    </tr>
    <?php
	if($statusContaSalario != ''){ //ESSA LINHA É EXECUTADA CASO A EXISTA "CONTA SALÁRIO" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."SALARIO"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_SALARIO";
			$arquivo = 'BANCOS/ITAU/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
			$arquivo = 'BANCOS/REAL/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";		
		}else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO02."_".$TIPO.".txt";
			$dirContaSalario = "BANCOS/BRADESCO/CONTA_SALARIO";
			$arquivo = 'BANCOS/BRADESCO/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '001'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/BRASIL/CONTA_SALARIO";
			$arquivo = 'BANCOS/BRASIL/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}
	}
	?>
	</tr>
    <?php
	if($statusContaCorrente != ''){ //ESSA LINHA É EXECUTADA CASO A EXISTA "CONTA CORRENTE" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."CORRENTE"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_CORRENTE";
			$arquivo = 'BANCOS/ITAU/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
			$arquivo = 'BANCOS/REAL/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
		}else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SERÁ ENVIADO A FOLHA QUE ESTÁ SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO01."_".$TIPO.".txt";
			$dirContaSalario = "BANCOS/BRADESCO/CONTA_CORRENTE";
			$arquivo = 'BANCOS/BRADESCO/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '001'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/BRASIL/CONTA_CORRENTE";
			$arquivo = 'BANCOS/BRASIL/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}
	}
	?>	
  </td>
    </tr>
    </table>
<p>&nbsp;</p>
</body>
</html>