<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}
include "../conn.php";
include "../funcoes.php";

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = str_replace('--', '+', $_REQUEST['enc']);
list($regiao,$folha) = explode('&', decrypt($enc));
//

if(!empty($_REQUEST['agencia'])){
	include "../conn.php";
	$nome = $_REQUEST['nome'];
	$cpf = $_REQUEST['cpf'];
	$ag = $_REQUEST['agencia'];
	$cc = $_REQUEST['conta'];
	$enc = $_REQUEST['enc'];
	$clt = $_REQUEST['clt'];
	$id = $_REQUEST['id'];

	$tipo_conta = $_REQUEST['radio_tipo_conta'];
 	$RE_clt = mysql_query("SELECT * FROM folha_cooperado where id_autonomo = '$id' and status = 3") or die (mysql_error());
	
	$RowCLT = mysql_fetch_array($RE_clt);
	mysql_query("UPDATE autonomo SET nome='$nome', cpf='$cpf', agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_autonomo = '$RowCLT[id_autonomo]'") or die (mysql_error());
	
	mysql_query("UPDATE folha_cooperado SET nome='$nome', cpf='$cpf', agencia='$ag', conta='$cc' WHERE id_autonomo = '$id'") or die (mysql_error());
	/*
	print "<div style='backgroud:red'>";
	echo "ID: ".$id."<BR>";
	echo "AG:".$ag."<BR>";
	echo "CC:".$cc."<BR>";
	echo "CLT:".$clt."<BR>";
	echo "TIPO:".$tipo_conta."<BR>";
	echo "<a href='folha_banco.php?enc=$enc'>Continuar</a>";
	print "</div>";
	
	exit;
	*/
}



$dataPagamento = $_REQUEST['data'];
$dataPag = explode("/",$dataPagamento);
$a=$dataPag[2];
$m=$dataPag[1];
$d=$dataPag[0];



$banco              = $_REQUEST['banco'];
$banco_participante = $_REQUEST['banco_participante'];
$dataPagamento      = $_REQUEST['data'];

list($d,$m,$a) 		 = explode('/', $dataPagamento);
list($dia,$mes,$ano) = explode('/', date('d/m/Y'));


$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'") or die(mysql_error());
$row_user = mysql_fetch_array($result_user);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'")or die(mysql_error());
$row_banco = mysql_fetch_array($result_banco);

$result_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_banco[id_regiao]'");
$row_regiao    = mysql_fetch_array($result_regiao);

$result_master = mysql_query("SELECT * FROM master where id_master = '$row_regiao[id_master]'")or die(mysql_error());
$row_master = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'")or die(mysql_error());
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'")or die(mysql_error());
$row_projeto = mysql_fetch_array($result_projeto);


$meses        = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_inteiro  = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_inteiro];

$titulo = "Lista Banco: Projeto $row_projeto[nome] m�s de $mes_da_folha";


$data = date("d/m/Y");

$data_menor14 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));

$result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos order by cod");

while($row_codigos = mysql_fetch_array($result_codigos)){
	$ar_codigos[] = $row_codigos['0'];
}

//DADOS DA COOPERATIVA GERAL DA FOLHA
include "../classes/cooperativa.php";
$CoopGeral = new cooperativa();
$CoopGeral -> MostraCoop($row_folha['coop']);

$Gid_coop	 	= $CoopGeral -> id_coop;
$Gnome	 		= $CoopGeral -> nome;
$Gfantasia		= $CoopGeral -> fantasia;
$Gcnpj			= $CoopGeral -> cnpj;
$Gfoto			= $CoopGeral -> foto;

if($Gfoto == "0"){
	$LogoCoop = "";
}else{
	$LogoCoop = "<img src='../cooperativas/logos/coop_".$Gid_coop.$Gfoto."' alt='' width='110' height='79' align='absmiddle' >";
}
//--------------------------------------------

?>
<html>
<head>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
</head>

<body>
<table width="95%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center" class="bordaescura1px">
      <tr>
        <td width="100%" height="81" align="center" valign="middle" bgcolor="#666666" class="title">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" height="84" align="center" valign="middle" bgcolor="#E2E2E2"><span class="texto10">
                <?=$LogoCoop?>
              </span></td>
            <td width="58%" bgcolor="#E2E2E2"><span class="texto10">
              <?=$Gnome?>
              <br>
CNPJ :
<?=$Gcnpj?>
<br>
            </span></td>
            <td width="26%" bgcolor="#E2E2E2">
            <span class="Texto10">
            Data de Processamento: <?=$row_folha['data_proc2']?>
            <br>
            Data para Pagamento: <?=$d."/".$m."/".$a?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br>
      <table width="325" border="0" class="bordaescura1px">
        <tr>
          <td width="52"><img src="../imagens/bancos/<?=$row_banco['id_nacional']?>.jpg" width="50" height="50"></td>
          <td width="257"><div style="font-size:16px">&nbsp;<?=$row_banco['nome']?></div></td>
        </tr>
      </table>
<br />
      <span class="title">Folha de Pagamento - 
      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <span class="title"><br />
    </span>
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
        <tr>
          <td width="8%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="25%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Horas</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. L&iacute;q.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Tipo de Conta</td>          
        </tr>
       <?php 
	   		//VERIFICA OS TIPOS DE PAGAMENTOS DA REGI�O E PROJETO ATUAL
			$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '1' and id_projeto = '$row_projeto[0]'");
			$rowTipoPg = mysql_fetch_array($tiposDePagamentos);
			
	   		if ($row_banco['id_nacional'] == '237'){
				//NOME DO ARQUIVO TEXTO
				$CONSTANTE = 'FP';
				$DD = date('d');
				$MM = date('m');
				$NUM_ARQUIVO01 = '2';
				$NUM_ARQUIVO02 = '1';
				$TIPO = 'TST';
				
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
				$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' and tipo_pg='$rowTipoPg[id_tipopg]'");				
				while ($rowContas = mysql_fetch_array($resultContas)){					
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$rowContas[id_autonomo]'");
		  				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if (($rowTiposDeConta['tipo_conta'] == 'corrente') and ($rowTiposDeConta['tipo_conta'] != '')){
						$contaCorrente = 'corrente';
					}else if (($rowTiposDeConta['tipo_conta'] == 'salario') and ($rowTiposDeConta['tipo_conta'] != '')){
								$contaSalario = 'salario';
					}
				}		
				
				//EXECUTA OS CABE�ALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
					$NUM_ARQUIVO = $NUM_ARQUIVO01;
					include "BANCOS/BRADESCO/header_bradesco_corrente.php";
				}
				if ($contaSalario != ''){
							$NUM_ARQUIVO = $NUM_ARQUIVO02;
							include "BANCOS/BRADESCO/header_bradesco_salario.php";					
				}			  
		}else if($row_banco['id_nacional'] == '356'){

					$CONSTANTE = 'FP_BANCO_REAL_'.$regiao.'_'.$folha;
					$DD = date('d');
					$MM = date('m');
					$ANO = date('Y');
					$NUM_ARQUIVO01 = '1';
					$NUM_ARQUIVO02 = '2';
					
					//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
					$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' and tipo_pg='$rowTipoPg[id_tipopg]'");				
					while ($rowContas = mysql_fetch_array($resultContas)){					
							$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$rowContas[id_autonomo]'");
		  					$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		

				//EXECUTA OS CABE�ALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
							include "BANCOS/REAL/header_arquivo_real_corrente.php";
							//include "BANCOS/REAL/header_lote_real_corrente.php";					
				}
				
				if ($contaSalario != ''){
							include "BANCOS/REAL/header_arquivo_real_salario.php";
							//include "BANCOS/REAL/header_lote_real_salario.php";					
				}			
				
				// FIM SANTANDER-------------------------------------------------------------------------------
				
					}else if($row_banco['id_nacional'] == '033'){

					$CONSTANTE = 'FP_BANCO_SANTANDER_'.$regiao.'_'.$folha;
					$DD = date('d');
					$MM = date('m');
					$ANO = date('Y');
					$NUM_ARQUIVO01 = '1';
					$NUM_ARQUIVO02 = '2';
					
					//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
					$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' and tipo_pg='$rowTipoPg[id_tipopg]'");				
					while ($rowContas = mysql_fetch_array($resultContas)){					
							$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$rowContas[id_autonomo]'");
		  					$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		

				//EXECUTA OS CABE�ALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
							include "BANCOS/SANTANDER/header_arquivo_santander_corrente.php";
							//include "BANCOS/SANTANDER/header_lote_santander_corrente.php";					
				}
				
				if ($contaSalario != ''){
							include "BANCOS/SANTANDER/header_arquivo_santander_salario.php";
							//include "BANCOS/SANTANDER/header_lote_santander_salario.php";					
				}	  			
															
// FIM SANTANDER--------------------------------------------------------------------------------
				
		}else if ($row_banco['id_nacional'] == '341'){	
				$CONSTANTE = 'FP_BANCO_ITAU_'.$regiao.'_'.$folha;
				$DD = date('d');
				$MM = date('m');
				$ANO = date('Y');
				
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
				$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' and tipo_pg='$rowTipoPg[id_tipopg]'") or die(mysql_error());				
				while ($rowContas = mysql_fetch_array($resultContas)){	
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$rowContas[id_autonomo]'");
		  				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		

				//EXECUTA OS CABE�ALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
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
				
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO				
				$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' and tipo_pg='$rowTipoPg[id_tipopg]'") or die(mysql_error());				
				//$resultContas = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco'") or die(mysql_error());	
				while ($rowContas = mysql_fetch_array($resultContas)){
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$rowContas[id_autonomo]'");
		  				$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
								$contaSalario = 'salario';
					}
				}		
				
				//EXECUTA OS CABE�ALHOS PARA OS TIPOS DE ARQUIVOS ENCONTRADOS
				if ($contaCorrente != ''){
					include "BANCOS/BRASIL/header_brasil_corrente.php";				
				}
				if ($contaSalario != ''){
							include "BANCOS/BRASIL/header_brasil_salario.php";					
				}			  			
		}
       
       ?>
       
       <?php
          $cont = "0";
		  
		  $resultClt = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco_participante' 
		  and tipo_pg='$rowTipoPg[id_tipopg]'  order by nome")or die("Colocar CAMPO1 = 1 no tipo de PG");
		  
		  //$resultClt = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$banco'");
		  while($row_clt = mysql_fetch_array($resultClt)){			  	
		  
		  $resultAutonomo = mysql_query("SELECT campo3, nome FROM autonomo WHERE id_autonomo = '$row_clt[id_autonomo]'");
		  $rowAutonomo = mysql_fetch_array($resultAutonomo);
		  
     	  $REtabCLT = mysql_query("SELECT tipo_conta, id_autonomo FROM autonomo WHERE id_autonomo = '$row_clt[id_autonomo]'");
		  $RowTabCLT = mysql_fetch_array($REtabCLT);
		  
		  // Verificando Tipo de Pagamento
		  $qr_tipo_pg = mysql_query("SELECT tipopg FROM tipopg WHERE id_tipopg = '$row_clt[tipo_pg]'");
		 @$tipo_pg    = mysql_result($qr_tipo_pg,0);
		 
		  if(strstr($tipo_pg,'Conta')) {
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row_clt['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------		  		  
		  
		  //----FORMATANDO OS VALORES FORMATO BRASILEIRO PARA VISUALIZA��O (5.100,00) ---------
		  $salario_brutoF = number_format($row_clt['salario'],2,",","."); //ok
		  $total_rendiF = number_format($row_clt['adicional'],2,",",".");
		  $total_debitoF = number_format($row_clt['desconto'],2,",","."); //ok
		  $faltasF = number_format($row_clt['faltas'],2,",",".");
		  //$valor_IRF = number_format($row_clt['imprenda'],2,",",".");
		  //$valor_familiaF = number_format($row_clt['salfamilia'],2,",",".");

		  $valor_final_individualF = number_format($row_clt['salario_liq'],2,",",".");

		  //$valor_desconto_sindicatoF = number_format($valor_desconto_sindicato,2,",",".");
		  //$valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
		  
		  //-------------------
		
		  $resultTipoConta = mysql_query("SELECT tipo_conta FROM autonomo WHERE id_autonomo = '$row_clt[id_autonomo]'");
		  $rowTipoConta = mysql_fetch_array($resultTipoConta);
		  
		  switch($rowTipoConta['tipo_conta']){
			  case 'salario': $tipoConta = 'Conta Sal�rio';
			  break;
			  case 'corrente': $tipoConta = 'Conta Corrente';
			  break;
			  default: $tipoConta = '&nbsp;';
		  }

		  /*
		   	HTML content	</a> 
	<div class="highslide-maincontent"> 
		This example uses the <code>htmlExpand</code> method to display full HTML content in the expander.
		The width of the expanding <code>div</code> is set to <code>300px</code>, while the height is omitted
		to allow Highslide to decide the best fit.<br/><br/> 
		In the expander you can put all kinds of content, for instance form elements.
	</div>
		  */
			
			$tipoR = $RowTabCLT['tipo_conta'];
				if ($tipoR == 'salario'){
					$checkedSalario = 'checked';	
				}else if ($tipoR == 'corrente'){
					$checkedCorrente = 'checked';
				}
			
			
			$alink = "<a href='#' onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', 
			wrapperClassName: 'draggable-header',headingText: '$nomeT' } )\" class='highslide'>";
			
			$divTT = "<div class='highslide-maincontent'>
			<form action='' method='post' name='form'>
			<table width='526' border='0' cellspacing='0' cellpadding='0'>
			   <tr>
			    <td align='right'>Nome</td>
			    <td>&nbsp;<input name='nome' type='text' size='25' id='nome' value='$row_clt[nome]'/>&nbsp;</td>
			    <td align='right'>CPF</td>
			    <td>&nbsp;<input name='cpf' type='text' size='15' maxlength='14' id='cpf' value='$row_clt[cpf]'/></td>
			  </tr>
			  
			  <tr>
			    <td align='right'>Agencia</td>
			    <td>&nbsp;<input name='agencia' type='text' size='15' maxlength='10' id='agencia' value='$row_clt[agencia]'/>&nbsp;</td>
			    <td align='right'>Conta</td>
			    <td>&nbsp;<input name='conta' type='text' size='15' maxlength='10' id='conta' value='$row_clt[conta]'/></td>
			  </tr>
			  
			  <tr>
			    <td align='right'>Tipo de Conta</td>
			    <td colspan='2'>&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Sal�rio </label>
				&nbsp;&nbsp;
				<label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label></td>
			  </tr>
			  <tr>
			    <td colspan='3' align='center'><input type='submit' value='Enviar' /></td>
			  </tr>
			  
			</table>
			<input type='hidden' name='enc' value='$enc'>
			<input type='hidden' name='clt' value='$row_clt[0]'>
			<input type='hidden' name='id' value='$row_clt[id_autonomo]'>
			</form>
			</div>";

		  print"
		  <tr bgcolor=$color height='20' class='texto10'>
          <td align='center' valign='middle' $bord> $rowAutonomo[campo3] </td>
          <td align='lefth' valign='middle' $bord>$alink $nomeT</a> $divTT</td>
          <td align='right' valign='middle' $bord>$salario_brutoF</td>
          <td align='right' valign='middle' $bord>$total_rendiF</td>
          <td align='right' valign='middle' $bord>$total_debitoF</td>
          <td align='right' valign='middle' $bord>$faltasF</td>
          <td align='right' valign='middle' $bord>$valor_final_individualF</td>
          <td align='right' valign='middle' $bord> $tipoConta </td>		  
		  </tr>";
		  unset($checkedSalario);
		  unset($checkedCorrente);
		  if ($row_banco['id_nacional'] == '237'){
		  		$tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
		  		if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '237')){			  
						$NUM_ARQUIVO = $NUM_ARQUIVO01;			  
						$statusContaCorrente = 'corrente';			
						include "BANCOS/BRADESCO/detalhes_bradesco_corrente.php";
		  		}else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '237')){
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
			  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '356')){			  			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
					$VALOR = $row_clt['salliquido'];
					$statusContaCorrente = 'corrente';			
			  		include "BANCOS/REAL/detalhes_real_corrente.php";
			 }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '356')){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SAL�RIO	  
					$VALOR = $row_clt['salliquido'];								
			  		$statusContaSalario = 'salario';
					include "BANCOS/REAL/detalhes_real_salario.php";
			 }			  
			  
			    }

		  if ($row_banco['id_nacional'] == '033'){			  
			  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
			  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '033')){			  			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
					$VALOR = $row_clt['salliquido'];
					$statusContaCorrente = 'corrente';			
			  		include "BANCOS/SANTANDER/detalhes_santander_corrente.php";
			 }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '033')){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SAL�RIO	  
					$VALOR = $row_clt['salliquido'];								
			  		$statusContaSalario = 'salario';
					include "BANCOS/SANTANDER/detalhes_santander_salario.php";
			 }			  
			  
			  
			  
		  }else if($row_banco['id_nacional'] == '341'){
		  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];		  
		  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '341')){			  			
		  	$statusContaCorrente = 'corrente';
			include "BANCOS/ITAU/detalhes_itau_corrente.php";
		  }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '341')){									
			  		$statusContaSalario = 'salario';
			 		 include "BANCOS/ITAU/detalhes_itau_salario.php";
		  }	
		  }
		  
		  unset($tipoContaCorrente01);

		  
		// AQUI TERMINA O LA�O ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM �NICO FUNCIONARIO		  
		// SOMANDO VARIAVIES PARA CHEGAR AO VALOR FINAL

		$salario_brutoFinal = $salario_brutoFinal + $row_clt['salario'];
		$total_rendiFinal = $total_rendiFinal + $row_clt['rendimentos'];
		$total_debitoFinal = $total_debitoFinal + $row_clt['desconto'];
		$valor_inssFinal = $valor_inssFinal + $row_clt['faltas'];
		$valor_liquiFinal = $valor_liquiFinal + $row_clt['salario_liq'];		

		$cont ++;
		
		}
		
		  }

		// FORMATANDO OS DADOS FINAIS - FORMATO BRASILEIRO PARA VISUALIZA��O (5.100,00)
		$salario_brutoFinalF = number_format($salario_brutoFinal,2,",",".");
		$total_rendiFinalF = number_format($total_rendiFinal,2,",",".");
		$total_debitoFinalF = number_format($total_debitoFinal,2,",",".");
		$valor_inssFinalF = number_format($valor_inssFinal,2,",",".");
		//$valor_IRFinalF = number_format($valor_IRFinal,2,",",".");
		//$valor_familiaFinalF = number_format($valor_familiaFinal,2,",",".");
		$valor_liquiFinalF = number_format($valor_liquiFinal,2,",",".");
		?>
        
         <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$salario_brutoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_rendiFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_debitoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_inssFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_liquiFinalF?></td>
        </tr>        
      </table>
      <br>
      <br>
      <?=$cont." Participantes<br/>"?>
<!-- Criptografia -->
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
	//include "BANCOS/REAL/trailler_lote_real_salario.php";	
	include "BANCOS/REAL/trailler_arquivo_real_salario.php";

}
if (($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '033')){
	//include "BANCOS/SANTANDER/trailler_lote_santander_salario.php";	
	include "BANCOS/SANTANDER/trailler_arquivo_santander_salario.php";

}
if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '356')){
			//include "BANCOS/REAL/trailler_lote_real_corrente.php";	
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
<table width="95%" border="0" align="center" class="bordaescura1px">
    <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    &nbsp;&nbsp;&nbsp;
    <?
	$id_projeto = $row_projeto[0];	
    $id_banco = $row_banco[0];
	//$id_user;
	$arquivoFinanceiro = "../".$arquivo;
	$nome = 'FOLHA DE PAGAMENTO';
	$especifica = "FOLHA DE PAGAMENTO - CREDITO EM ".$DD."-".$MM."-".$ANO."<a href=download.php?file=".$arquivoFinanceiro.">Arquivo</a>";
	$tipo = '30';
	//$valorTotalLiquido = number_format($valor_liquiFinalF, 2, ".",",");
	
	$valor = str_replace(".", "", $valor_liquiFinalF); 
	$valor = str_replace(",", ".", $valor); 
	
	$data_proc = date('Y-m-d H:i:s');
	$data_vencimento = $ANO."-".$MM."-".$DD;
	$status = '1';
	/*
	print 'id_projeto: '.$id_projeto.'<br>';
	print 'id_banco: '.$id_banco.'<br>';
	print 'id_user: '.$id_user.'<br>';
	print 'nome: '.$nome.'<br>';
	print 'valor: '.$valor.'<br>';
	print 'data_proc: '.$data_proc.'<br>';
	print 'data_pg: '.$data_vencimento.'<br>';
	*/
	?>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$folha"); 
$linkvolt = str_replace("+","--",$linkvolt);

//$linkfin = encrypt("$regiao&$folha&$row_banco[0]"); 
$linkfin = encrypt("$regiao&$folha&$row_banco[0]&$id_projeto&$id_user&$nome&$especifica&$tipo&$valor&$data_proc&$data_vencimento&$status");
$linkfin = str_replace("+","--",$linkfin);

$linkselect = encrypt("$regiao&$folha&$row_banco[0]&$dataPagamento");
$linkselect = str_replace("+","--",$linkselect);


// -----------------------------
?>
    
    <table width="80%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><a href='#' onClick="Confirm(<?=$regiao?>,<?=$folha?>)" class="botao">FINALIZAR</a></td>
        <td><a href='ver_folhacoop.php?<?="enc=".$linkvolt."&tela=1"?>' class="botao">VOLTAR</a></td>
        <td><a href='folha_bancocoo_a.php?<?="enc=".$linkselect."&sel=1"?>' class="botao"> SELECIONAR FUNC. A SEREM PAGOS</a></td>
      </tr>
    </table></td> 
    </tr>
    <tr>
    </table>
<script language="javascript">
    
function Confirm(a,b){
	var Regiao = a;
	var Folha = b;	
	
	input_box=confirm("Deseja realmente FINALIZAR?\n\nLembrando que ap�s a confirma��o, n�o podera gerar novamente o ARQUIVO TEXTO do banco!");
	
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
    <br/>
    <br/>
    <table bgcolor="#FFFFFF" width="40%" align="center" class="bordaescura1px">
    <tr>
    	<td align="center" valign="middle" bgcolor="#CCCCCC"><span class="igreja"> <strong>DOWNLOAD DO ARQUIVO TEXTO</strong> </span></td>
    </tr>
    <tr>
    <td align='center'>&nbsp;</td>
    <tr>
    <td></td>
    </tr>
    </tr>
    <?php
	if($statusContaSalario != ''){ //ESSA LINHA � EXECUTADA CASO A EXISTA "CONTA SAL�RIO" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."SALARIO"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_SALARIO";
			$arquivo = 'BANCOS/ITAU/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
			$arquivo = 'BANCOS/REAL/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
					}else if ($row_banco['id_nacional'] == '033'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/SANTANDER/CONTA_SALARIO";
			$arquivo = 'BANCOS/SANTANDER/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";

		}else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO02."_".$TIPO.".txt";
			$dirContaSalario = "BANCOS/BRADESCO/CONTA_SALARIO";
			$arquivo = 'BANCOS/BRADESCO/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '001'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/BRASIL/CONTA_SALARIO";
			$arquivo = 'BANCOS/BRASIL/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}
	}
		?>
	</tr>
    <?php
	if($statusContaCorrente != ''){ //ESSA LINHA � EXECUTADA CASO A EXISTA "CONTA SAL�RIO" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."CORRENTE"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_CORRENTE";
			$arquivo = 'BANCOS/ITAU/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){ 
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_CORRENTE";
			$arquivo = 'BANCOS/REAL/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
			
			}else if ($row_banco['id_nacional'] == '033'){ 
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/SANTANDER/CONTA_CORRENTE";
			$arquivo = 'BANCOS/SANTANDER/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
				
		}else if ($row_banco['id_nacional'] == '237'){ 
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