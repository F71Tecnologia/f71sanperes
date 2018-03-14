<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";

$dataPagamento = $_REQUEST['data'];
$dataPag = explode("/",$dataPagamento);
$a=$dataPag[2];
$m=$dataPag[1];
$d=$dataPag[0];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$banco = $decript[1];
$folha = $decript[2];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

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

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mes_da_folha = $meses[$row_folha['mes']];

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

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

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%"><span class="style1"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%"><span class="style1">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="22%">
            <span class="style1">
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
          <td width="8%" height="25" align="center" valign="middle" bgcolor="#CCFFCC" class="style23">C&oacute;digo</td>
          <td width="25%" align="center" valign="middle" bgcolor="#CCFFCC" class="style23">Nome </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. Base</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Rendim.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Descontos </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">INSS</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Imp. Renda</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. Fam. </td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. L&iacute;q.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Tipo de Conta</td>          
        </tr>
       <?php 
	   
	   		if ($banco == '1'){
				$numeroSequencial = 0;
			//ARQUIVO TEXTO DO BANCO BRADESCO
			
			//NOME DO ARQUIVO TEXTO
			$CONSTANTE = 'FP';
			$DD = date('d');
			$MM = date('m');
			$NUM_ARQUIVO = '0';
			$TIPO = 'TST';
			
			//REGISTRO DE HEADER
			$COD_REGISTRO = '0';	//VALOR CONSTANTE. 
			$COD_REMESSA = '1'; 	//VALOR CONSTANTE
			$LITERAL1 = "REMESSA";	//VALOR CONSTANTE
			$COD_SERVICO = '03';	//VALOR CONSTANTE
			
			$LITERAL2 = "CREDITO C/C";	
			$LITERAL2 = sprintf("% 15s",$LITERAL2);
			
			$IDENTIFICA = $row_banco['agencia'];
			$IDENTIFICA = sprintf("%05d",$IDENTIFICA);
			
			$RAZAO = $row_banco['num_razao'];
			$RAZAO = sprintf("%0-5s",$RAZAO);
			
			$EMPRESA = $row_banco['conta'];
			$EMPRESA = sprintf("%07d",$EMPRESA);
			
			$str = $row_banco['conta'];
			$ultimoDigitoConta = $str{strlen($str)-1};
			$NOBANCO = $ultimoDigitoConta;
			
			$ID = ' ';
			$RESERVA = ' ';
			
			$COD_EMPRESA = $row_banco['cod_empresa'];	//INSERIR CÓDIGO DO CLIENTE NO BANCO. PARA CADA CONTA, DEVE HAVER UM CÓDIGO
			$COD_EMPRESA = sprintf("%05d",$COD_EMPRESA);
			
			
			$NOME_EMPR = $row_master['razao'];
			$NOME_EMPR = sprintf("% 25s",$NOME_EMPR);
			
			$COD_BANCO = '237';	//VALOR CONSTANTE			
			
			$NOME_BCO = 'BRADESCO';	//VALOR CONSTANTE
			$NOME_BCO = sprintf("% 15s",$NOME_BCO);
			
			$DT_GRAVACAO = date('dmY');
			$DENSIDADE = '01600';
			$DENSIDADE = sprintf("%05s",$DENSIDADE);
			$LITERAL3 = 'BPI';	//VALOR CONSTANTE
			$DT_DEBITO = $d.$m.$a; //DATA DO DÉBITO, ENVIADO PELO FORMÁRIO
			$ID_MOEDA = ' ';
			$ID_SECULO = 'N';					
			
			$numeroSequencial = $numeroSequencial + 1;
			$NUMERO_SEQ = sprintf("%06d", $numeroSequencial);
			
			$handle = fopen('BANCOS/BRADESCO/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt", "a");
			fwrite($handle, $COD_REGISTRO, 1);
			fwrite($handle, $COD_REMESSA, 1);
			fwrite($handle, $LITERAL1, 7);
			fwrite($handle, $COD_SERVICO, 2);
			fwrite($handle, $LITERAL2, 15);
			fwrite($handle, $IDENTIFICA, 5);
			fwrite($handle, $RAZAO, 5);
			fwrite($handle, $EMPRESA, 7);
			fwrite($handle, $NOBANCO, 1);
			fwrite($handle, $ID, 1);
			fwrite($handle, $RESERVA, 1);
			fwrite($handle, $COD_EMPRESA, 5);
			fwrite($handle, $NOME_EMPR, 25);
			fwrite($handle, $COD_BANCO, 3);
			fwrite($handle, $NOME_BCO, 15);
			fwrite($handle, $DT_GRAVACAO, 8);
			fwrite($handle, $DENSIDADE, 5);
			fwrite($handle, $LITERAL3, 3);
			fwrite($handle, $DT_DEBITO, 8);
			fwrite($handle, $ID_MOEDA, 1);
			fwrite($handle, $ID_SECULO, 1);
			$RESERVA = ' ';
			$RESERVA = sprintf("% 74s" ,$RESERVA);
			fwrite($handle, $RESERVA, 74);
			fwrite($handle, $NUMERO_SEQ, 6);								
			fwrite($handle, "\r\n"); 
		}

       
       ?>
        <?php
          $cont = "0";
		  
		  $resultClt = mysql_query("SELECT * FROM rh_folha_proc where id_folha = '$folha' and status = '3' and id_banco = '$banco'");
		  while($row_clt = mysql_fetch_array($resultClt)){
		  
		  
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row_clt['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------
		  
		  
		  
		  
		  
		  //----FORMATANDO OS VALORES FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00) ---------
		  $salario_brutoF = number_format($row_clt['salbase'],2,",",".");
		  $total_rendiF = number_format($row_clt['rend'],2,",",".");
		  $total_debitoF = number_format($row_clt['desco'],2,",",".");
		  $valor_inssF = number_format($row_clt['inss'],2,",",".");
		  $valor_IRF = number_format($row_clt['imprenda'],2,",",".");
		  $valor_familiaF = number_format($row_clt['salfamilia'],2,",",".");

		  $valor_final_individualF = number_format($row_clt['salliquido'],2,",",".");


		  //$valor_desconto_sindicatoF = number_format($valor_desconto_sindicato,2,",",".");
		  //$valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
		  
		  //-------------------
		
		  $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$row_clt[id_clt]'");
		  $rowTipoConta = mysql_fetch_array($resultTipoConta);
		  
		  switch($rowTipoConta['tipo_conta']){
			  case 'salario': $tipoConta = 'Conta Salário';
			  break;
			  case 'corrente': $tipoConta = 'Conta Corrente';
			  break;
			  case 'poupanca': $tipoConta = 'Conta Poupança';
			  break;
			  default: $tipoConta = '&nbsp;';
		  }
		  
		  print"
		  <tr bgcolor=$color height='20' class='style28'>
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
		  
		  $tipoContaCorrente01 = $rowTipoConta['tipo_conta'];
		  if (($tipoContaCorrente01 =='corrente') and ($banco == '1')){
			//VALOR TOTAL DO ARQUIVO TXT PARA CONTAS CORRENTE  
			$VALOR = $row_clt['salliquido'];
			$arrayValorTotalCorrente[] = $arrayValorTotalCorrente+$VALOR;
			$remover = array(".", "-", "/",",");
			$VALOR = str_replace($remover, "", $VALOR);
			$VALOR = sprintf("%013d",$VALOR);

			$statusContaCorrente = 'corrente';
			
			include "BANCOS/BRADESCO/detalhes_bradesco_corrente.php";
		  }else if(($tipoContaCorrente01 =='poupanca') and ($banco == '1')){
			
					$VALOR = $row_clt['salliquido'];
					$arrayValorTotalPoupanca[] = $arrayValorTotalPoupanca+$VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);
			  
			  		$statusContaPoupanca = 'poupanca';
			  		include "BANCOS/BRADESCO/detalhes_bradesco_poupanca.php";
		  }else if(($tipoContaCorrente01 =='salario') and ($banco == '1')){
			  
					$VALOR = $row_clt['salliquido'];
					$arrayValorTotalSalario[] = $arrayValorTotalSalario+$VALOR;
					$remover = array(".", "-", "/",",");
					$VALOR = str_replace($remover, "", $VALOR);
					$VALOR = sprintf("%013d",$VALOR);
			
			  		$statusContaSalario = 'salario';
			 		 include "BANCOS/BRADESCO/detalhes_bradesco_salario.php";
		  }
		  unset($tipoContaCorrente01);

		  
		// AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO		  
		// SOMANDO VARIAVIES PARA CHEGAR AO VALOR FINAL
		$salario_brutoFinal = $salario_brutoFinal + $row_clt['salbase'];
		$total_rendiFinal = $total_rendiFinal + $row_clt['rend'];
		$total_debitoFinal = $total_debitoFinal + $row_clt['desco'];
		$valor_inssFinal = $valor_inssFinal + $row_clt['inss'];
		$valor_IRFinal = $valor_IRFinal + $row_clt['imprenda'];
		$valor_familiaFinal = $valor_familiaFinal + $row_clt['salfamilia'];
		$valor_liquiFinal = $valor_liquiFinal + $row_clt['salliquido'];

		$cont ++;
		
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
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$salario_brutoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_rendiFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$total_debitoFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_inssFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_IRFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_familiaFinalF?></td>
          <td align="right" valign="bottom" class="style23"><?=$valor_liquiFinalF?></td>
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
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='ver_folha.php?<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    </td>
  </tr>
</table>

<?

if (($statusContaCorrente =='corrente') AND ($banco == '1')){
	$valor_liquiFinalF = array_sum($arrayValorTotalCorrente);
	$VALOR_TOTAL = $valor_liquiFinalF;
	$remover = array(".", "-", "/",",");
	$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
	$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);	
	include "BANCOS/BRADESCO/trailler_bradesco_corrente.php";
}else if(($statusContaPoupanca =='poupanca') AND ($banco == '1')){
	include "BANCOS/BRADESCO/trailler_bradesco_poupanca.php";
}else if(($statusContaSalario =='salario') AND ($banco == '1')){
	include "BANCOS/BRADESCO/trailler_bradesco_salario.php";
}
?>
<p>&nbsp;</p>
</body>
</html>