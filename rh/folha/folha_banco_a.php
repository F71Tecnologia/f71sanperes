<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/curso.php";
include "../../classes/regiao.php";

$Regi		= new regiao();

$Atividade = new tabcurso();

if(!empty($_REQUEST['enc'])){
	//RECEBENDO A VARIAVEL CRIPTOGRAFADA
	$enc = $_REQUEST['enc'];
	$enc = str_replace("--","+",$enc);
	$link = decrypt($enc); 
	
	$decript = explode("&",$link);
	
	$regiao = $decript[0];
	$banco = $decript[2];
	$folha = $decript[1];
	$dataenc = $decript[3];
	//RECEBENDO A VARIAVEL CRIPTOGRAFADA
}else{
	$regiao =  $_REQUEST['regiao'];
	$banco =  $_REQUEST['banco'];
	$folha =  $_REQUEST['folha'];
	$dataenc =  $_REQUEST['dataenc'];
}

$id_user = $_COOKIE['logado'];


$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'");
$row_banco = mysql_fetch_array($result_banco);

$meses = array('Erro','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesFolhaINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$MesFolhaINT];

$titulo = "Folha Sint�tica: Projeto $row_projeto[nome] m�s de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$data_menor14 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));

$result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos order by cod");

while($row_codigos = mysql_fetch_array($result_codigos)){
	$ar_codigos[] = $row_codigos['0'];
}


//EXECUTANDO SELECIONADOS
if(!empty($_REQUEST['vai'])){

	$clt = $_REQUEST['id_clt'];
	$maximo = count($clt);
	for($i=0; $i < $maximo; $i ++){
		mysql_query("UPDATE rh_folha_proc SET arquivo = '1' WHERE id_folha = '".$folha."' and id_clt = '$clt[$i]' LIMIT 1");
		$Ids .= $clt[$i].",";
	}
		
		//-- ENCRIPTOGRAFANDO A VARIAVEL
		$linkvolt = encrypt("$regiao&$folha&$banco&$dataenc"); 
		$linkvolt = str_replace("+","--",$linkvolt);
		
		
		//----- INI -- GRAVANDO AS INFORMA��ES DO LOGIN NA TABELA LOG
		$id_user = $_COOKIE['logado'];
		$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
		$row_user = mysql_fetch_array($result_user);
		
		$ip = $_SERVER['REMOTE_ADDR'];  //PEGANDO O IP
		$local = "GERANDO ARQUIVO TXT INDIVIDUAL";
		$horario = date('Y-m-d H:i:s');
		$acao = "GERANDO ARQUIVO TXT INDIVIDUAL ($folha- $Ids )";
		
		mysql_query("INSERT INTO log (id_user,id_regiao,tipo_user,grupo_user,local,horario,ip,acao) 
		VALUES ('$id_user','$row_user[id_regiao]','$row_user[tipo_usuario]',
		'$row_user[grupo_usuario]','$local','$horario','$ip','$acao')") or die ("Erro Inesperado<br><br>".mysql_error());
		
		//----- FIM -- GRAVANDO AS INFORMA��ES DO LOGIN NA TABELA LOG
				
				
				
		print '
		<script>
		location.href= "folha_banco_a.php?enc='.$linkvolt.'";
		</script>
		';
	
	exit;
	
}



//SELECIONANDO O PESSOAL
if(!empty($_REQUEST['sel'])){
	
	print "<br><center>SELECIONE APENAS QUEM VAI SER PAGO!</center><br><br><form action=\"folha_banco_a.php\" method=\"post\" name=\"Form\" id=\"Form\">";
	
	$resultClt = mysql_query("SELECT * FROM rh_folha_proc where (id_folha = '$folha' and status = '3' and id_banco = '$banco') ORDER BY nome");
	$NumRows = mysql_num_rows($resultClt);
	
	print "<table border='0' bgcolor='#E2E2E2' align='center' width='90%' cellpadding='0' cellspacing='0'>
	<tr bgcolor='#CCCCCC'>
	<th><input type='checkbox' name='CheckTodos' onClick='selecionar_tudo();' checked ></th>
	<th>COD</th>
	<th>NOME</th>
	<th>SALARIO</th>
	</tr>
	";
	
	$cont = 0;
	
	while($row = mysql_fetch_array($resultClt)){
		
		//---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------	
		
		mysql_query("UPDATE rh_folha_proc SET arquivo = '0' WHERE id_folha = '".$folha."' and id_clt = '$row[id_clt]' LIMIT 1");
		
		print "
		<tr bgcolor='$color'>
		<td $bord align='center'><input name='id_clt[]' id='id_clt' type='checkbox' value='$row[id_clt]' checked></td>
		<td $bord align='center'>$row[0]</td>
		<td $bord>$nomeT</td>
		<td $bord>$row[salliquido]</td>
		</tr>";
		
		$cont ++;
	}
	
	echo '
	<input type="hidden" name="folha" value="'.$folha.'">
	<input type="hidden" name="regiao" value="'.$regiao.'">
	<input type="hidden" name="banco" value="'.$banco.'">
	<input type="hidden" name="dataenc" value="'.$dataenc.'">
	<input type="hidden" name="vai" value="1">
	
	';
	
	print "</table><center><input type='submit' name='enviar' value='Avan�ar'></center></form>";
	
	
	print '
	<script language="javascript" type="text/javascript">

function selecionar_tudo(){
	var contaForm = document.Form.elements.length;
	contaForm = contaForm - 3;
    var campo = document.Form;  
    var i; 

	for (i=0 ; i<contaForm ; i++){
		if (campo.elements[i].id == "id_clt") {
			campo.elements[i].checked = campo.CheckTodos.checked;
		}
	}
	
	
}

</script>';
	
	exit;
}

$dataPagamento = $dataenc;
$dataPag = explode("/",$dataPagamento);
$a=$dataPag[2];
$m=$dataPag[1];
$d=$dataPag[0];

$DataparaPG_F = $d."/".$m."/".$a;
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
        <td width="100%" height="81" align="center" class="show">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%"><span class="style1"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%" >
              <span class="Texto10">
              <?=$row_master['razao']?>
              <br>
              CNPJ : <?=$row_master['cnpj']?>
              </span><span class="style1"><br>
              </span></td>
            <td width="22%" class="Texto10">
            <span class="Texto10">
            Data de Processamento: <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
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
      <span class="title">Folha de Pagamento - 
      <?=$mes_da_folha?> / <?=$ano?></span><br />
      <span class="title"><br />
    </span>
      <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="8%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="25%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Base</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Imp. Renda</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Sal. Fam. </td>
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
				$resultContas = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco' and tipo_pg = '$rowTipoPg[id_tipopg]'");				
					while ($rowContas = mysql_fetch_array($resultContas)){					
							$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
		  					$rowTiposDeConta = mysql_fetch_array($resultTiposDeConta);
					if ($rowTiposDeConta['tipo_conta'] == 'corrente'){
						$contaCorrente = 'corrente';
					}else if ($rowTiposDeConta['tipo_conta'] == 'salario'){
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
					$ANO= date('Y');
					$NUM_ARQUIVO01 = '1';
					$NUM_ARQUIVO02 = '2';
					$NUM_ARQUIVO03 = '3';
					
					//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
					$resultContas = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco' AND tipo_pg = '$rowTipoPg[id_tipopg]'");				
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
		  			
					}else if($row_banco['id_nacional'] == '033'){
					
					$CONSTANTE = 'FP_BANCO_SANTANDER_'.$regiao.'_'.$folha;
					$DD = date('d');
					$MM = date('m');
					$ANO= date('Y');
					$NUM_ARQUIVO01 = '1';
					$NUM_ARQUIVO02 = '2';
					$NUM_ARQUIVO03 = '3';
					
					//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
					$resultContas = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco' AND tipo_pg = '$rowTipoPg[id_tipopg]'");				
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
							include "BANCOS/SANTANDER/header_arquivo_santander_corrente.php";
							include "BANCOS/SANTANDER/header_lote_santander_corrente.php";					
				}
				
				if ($contaSalario != ''){
							include "BANCOS/SANTANDER/header_arquivo_santander_salario.php";
							include "BANCOS/SANTANDER/header_lote_santander_salario.php";					
				}			  			
		  			
											
															
		}else if ($row_banco['id_nacional'] == '341'){
				$CONSTANTE = 'FP_BANCO_ITAU_'.$regiao.'_'.$folha;
				$DD = date('d');
				$MM = date('m');
				$ANO= date('Y');
				//VERIFICA QUAIS OS TIPOS DE CONTAS QUE O FECHAMENTO POSSUI E PREENCHE UMA VARI�VEL ESPEC�FICA COM O TIPO DE CONTRA ENCONTRADO
				$resultContas = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco' and AND tipo_pg = '$rowTipoPg[id_tipopg]'");				
				while ($rowContas = mysql_fetch_array($resultContas)){					
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
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
				$resultContas = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3' AND id_banco = '$banco' AND tipo_pg = '$rowTipoPg[id_tipopg]'");				
				while ($rowContas = mysql_fetch_array($resultContas)){					
						$resultTiposDeConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$rowContas[id_clt]'");
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
		  
		  $resultClt = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and 
		  id_banco = '$banco' and arquivo = '1' ORDER BY nome");
		  
		  while($row_clt = mysql_fetch_array($resultClt)){
		  
		  $REtabCLT = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
		  $RowTabCLT = mysql_fetch_array($REtabCLT);
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row_clt['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------		  		  
		  
		  //----FORMATANDO OS VALORES FORMATO BRASILEIRO PARA VISUALIZA��O (5.100,00) ---------
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
			
			$bgclass = ($cont % 2) ? "corfundo_um" : "corfundo_dois";
					
		  print"
		  <tr class='novalinha $bgclass'>
          <td align='center' valign='middle' $bord>$row_clt[cod] </td>
          <td align='lefth' valign='middle' $bord>$nomeT</td>
          <td align='right' valign='middle' $bord>$salario_brutoF</td>
          <td align='right' valign='middle' $bord>$total_rendiF</td>
          <td align='right' valign='middle' $bord>$total_debitoF</td>
          <td align='right' valign='middle' $bord>$valor_inssF</td>
          <td align='right' valign='middle' $bord>$valor_IRF</td>
          <td align='right' valign='middle' $bord>$valor_familiaF</td>
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
					//$arrayValorTotalCorrente[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);			
					$statusContaCorrente = 'corrente';			
			  		include "BANCOS/REAL/detalhes_real_corrente.php";
			 }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '356')){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SAL�RIO	  
					$VALOR = $row_clt['salliquido'];
					//$arrayValorTotalSalario[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);										
			  		$statusContaSalario = 'salario';
					include "BANCOS/REAL/detalhes_real_salario.php";
			 }
			 
			  }
		  
		  if ($row_banco['id_nacional'] == '033'){
			  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
			  if (($tipoContaCorrente01 =='corrente') and ($row_banco['id_nacional'] == '033')){			  			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
					$VALOR = $row_clt['salliquido'];
					//$arrayValorTotalCorrente[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);			
					$statusContaCorrente = 'corrente';			
			  		include "BANCOS/SANTANDER/detalhes_santander_corrente.php";
			 }else if(($tipoContaCorrente01 =='salario') and ($row_banco['id_nacional'] == '033')){			  
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SAL�RIO	  
					$VALOR = $row_clt['salliquido'];
					//$arrayValorTotalSalario[] = $VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);										
			  		$statusContaSalario = 'salario';
					include "BANCOS/SANTANDER/detalhes_santander_salario.php";
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
					//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS SAL�RIO	  
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
		// AQUI TERMINA O LA�O ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM �NICO FUNCIONARIO		  
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

		// FORMATANDO OS DADOS FINAIS - FORMATO BRASILEIRO PARA VISUALIZA��O (5.100,00)
		$salario_brutoFinalF = number_format($salario_brutoFinal,2,",",".");
		$total_rendiFinalF = number_format($total_rendiFinal,2,",",".");
		$total_debitoFinalF = number_format($total_debitoFinal,2,",",".");
		$valor_inssFinalF = number_format($valor_inssFinal,2,",",".");
		$valor_IRFinalF = number_format($valor_IRFinal,2,",",".");
		$valor_familiaFinalF = number_format($valor_familiaFinal,2,",",".");
		$valor_liquiFinalF = number_format($valor_liquiFinal,2,",",".");
		?>
        
         <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$salario_brutoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_rendiFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_debitoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_inssFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_IRFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_familiaFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_liquiFinalF?></td>
          <td align="right" valign="bottom" class="style23">&nbsp;</td>
        </tr>        
      </table>
      <br />
      <br>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$folha"); 
$linkvolt = str_replace("+","--",$linkvolt);
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
			
}
if (($statusContaSalario =='salario') AND ($row_banco['id_nacional'] == '033')){
		$valor_liquiFinalF = array_sum($arrayValorTotalSalario);
		$VALOR_TOTAL = $valor_liquiFinalF;
		$remover = array(".", "-", "/",",");
		$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
		$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
		include "BANCOS/SANTANDER/trailler_lote_santander_salario.php";	
		include "BANCOS/SANTANDER/trailler_arquivo_santander_salario.php";

}
if (($statusContaCorrente =='corrente') AND ($row_banco['id_nacional'] == '033')){
			$valor_liquiFinalF = array_sum($arrayValorTotalCorrente);
			$VALOR_TOTAL = $valor_liquiFinalF;
			$remover = array(".", "-", "/",",");
			$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
			$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);		
			include "BANCOS/SANTANDER/trailler_lote_santander_corrente.php";	
			include "BANCOS/SANTANDER/trailler_arquivo_santander_corrente.php";
				
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
			}else if($row_banco['id_nacional'] == '033'){
			$arquivo = 'BANCOS/SANTANDER/'.$CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";	
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
	/*
	print 'id_projeto: '.$id_projeto.'<br>';
	print 'id_banco: '.$id_banco.'<br>';
	print 'id_user: '.$id_user.'<br>';
	print 'nome: '.$nome.'<br>';
	print 'valor: '.$valor.'<br>';
	print 'data_proc: '.$data_proc.'<br>';
	print 'data_pg: '.$data_vencimento.'<br>';
	*/
	$linkfin = encrypt("$regiao&$folha&$row_banco[0]&$id_projeto&$id_user&$nome&$especifica&$tipo&$valor&$data_proc&$data_vencimento&$status");
	$linkfin = str_replace("+","--",$linkfin);	
	?>
<table width="95%" border="0" align="center">
    <tr>
    <td align="center" valign="middle" bgcolor="#999999" height="35">
    <!--
    <a href='#' style="text-decoration:none; color:#000" onClick="Confirm(<?=$regiao?>,<?=$folha?>)">FINALIZAR</a>
    
    &nbsp;&nbsp;&nbsp;
    -->
    <a href='ver_folha.php?<?="enc=".$linkvolt."&tela=1"?>' class="botao">VOLTAR</a></td> 
  </tr>
    <tr>
    </table>
    <br/>
    <br/>
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
	if($statusContaSalario != ''){ //ESSA LINHA � EXECUTADA CASO A EXISTA "CONTA SAL�RIO" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."SALARIO"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_SALARIO";
			$arquivo = 'BANCOS/ITAU/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
			$arquivo = 'BANCOS/REAL/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";		
			}else if ($row_banco['id_nacional'] == '033'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/SANTANDER/CONTA_SALARIO";
			$arquivo = 'BANCOS/SANTANDER/CONTA_SALARIO/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";		
	
		
		}else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
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
	if($statusContaCorrente != ''){ //ESSA LINHA � EXECUTADA CASO A EXISTA "CONTA CORRENTE" NO FACHAMENTO DA FOLHA
		if ($row_banco['id_nacional'] == '341'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
			$nomeArquivo = $CONSTANTE."_"."CORRENTE"."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/ITAU/CONTA_CORRENTE";
			$arquivo = 'BANCOS/ITAU/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '356'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/REAL/CONTA_SALARIO";
			$arquivo = 'BANCOS/REAL/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		}else if ($row_banco['id_nacional'] == '033'){
			$nomeArquivo = $CONSTANTE."_".$DD."_".$MM."_".$ANO.".txt";
			$dirContaSalario = "BANCOS/SANTANDER/CONTA_SALARIO";
			$arquivo = 'BANCOS/SANTANDER/CONTA_CORRENTE/'.$nomeArquivo;
			print "<tr><td align = 'center'> <a href='download.php?file=".$arquivo."' style='text-decoration:none; color:#000' border='0'>".$nomeArquivo."</a></td></tr>";
		
		}else if ($row_banco['id_nacional'] == '237'){ //ANALIZA QUAL QUAL BANCO SER� ENVIADO A FOLHA QUE EST� SENDO GERADA
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