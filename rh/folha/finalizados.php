<?
include "../../conn.php";
include "../../funcoes.php";

$regiao = $_GET['regiao']; 
$folha = $_GET['folha'];
$projeto = $_GET['projeto'];
$banco = $_GET['banco'];
?>

<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesFolhaINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$MesFolhaINT];

$result_folha_pro = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3'");

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../../net1.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" bgcolor="#666666"><span class="style1"><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
            <td width="62%" bgcolor="#666666"><span class="style1">
              <?=$row_master['razao']?><br>
              CNPJ : <?=$row_master['cnpj']?>
              <br>
            </span></td>
            <td width="22%" bgcolor="#666666">
            <span class="style1">
            Data de Processamento: <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Pagamento Finalizada - 
      <?=$mes_da_folha?> / <?=$ano?></span><br />
      <span class="title"><br />
    </span>

<body>

<?
$resultFinalizados = mysql_query("SELECT * FROM rh_folha_proc WHERE status = '4' and id_regiao = '$regiao' and id_projeto = '$projeto' and id_folha = '$folha' and id_banco=$banco") or die(mysql_error());
$quantRetornado = mysql_affected_rows();
?>
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
<tr height="20px">  
   <?
     if ($quantRetornado != 0 ){
			  print '<td width="7%" height="25" align="center" valign="middle" bgcolor="#CCFFCC" class="style23">C&oacute;digo</td>';
			  print '<td width="28%" align="center" valign="middle" bgcolor="#CCFFCC" class="style23">Nome </td>';
			  print '<td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. Base</td>';
			  print '<td width="7%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Rendim.</td>';
			  print '<td width="9%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Descontos </td>';
			  print '<td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">INSS</td>';
			  print '<td width="11%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Imp. Renda</td>';
			  print '<td width="11%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. Fam. </td>';
			  print '<td width="11%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Sal. L&iacute;q.</td>';
			  //print '<td width="8%" align="right" valign="middle" bgcolor="#CCFFCC" class="style23">Tipo de Conta</td>';          
			  print '</tr>';
			 $cont = 0;
			 while ($row_clt = mysql_fetch_array($resultFinalizados)){
					/*$sal_baseF = number_format($row['salario'],2,",",".");
					$rendiF = number_format($row['adicional'],2,",",".");
					$descoF = number_format($row['desconto'],2,",",".");
					$sal_liqF = number_format($row['salliquido'],2,",",".");  */
					
				 /* $salario_brutoF = number_format($row_clt['salbase'],2,",",".");
				  $total_rendiF = number_format($row_clt['rend'],2,",",".");
				  $total_debitoF = number_format($row_clt['desco'],2,",",".");
				  $valor_inssF = number_format($row_clt['inss'],2,",",".");
				  $valor_IRF = number_format($row_clt['imprenda'],2,",",".");
				  $valor_familiaF = number_format($row_clt['salfamilia'],2,",",".");
				  $valor_final_individualF = number_format($row_clt['salliquido'],2,",",".");	*/
				  
				  $salario_brutoF = number_format($row_clt['salbase'],2,",",".");
				  $total_rendiF = number_format($row_clt['rend'],2,",",".");
				  $total_debitoF = number_format($row_clt['desco'],2,",",".");
				  $valor_inssF = number_format($row_clt['a5020'],2,",",".");
				  $valor_IRF = number_format($row_clt['a5021'],2,",",".");
				  $valor_familiaF = number_format($row_clt['a5022'],2,",",".");
		  
				  $valor_final_individualF = number_format($row_clt['salliquido'],2,",",".");				  			  
				  
				  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
				  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
				  $nome = str_split($row_clt['nome'], 30);
				  $nomeT = sprintf("% -30s", $nome[0]);
				  $bord = "style='border-bottom:#000 solid 1px;'";
				  //-----------------	
					print"
					<tr>
					  <tr bgcolor=$color height='20' class='style28'>
					  <td align='center' valign='middle' $bord> $row_clt[cod] </td>
					  <td align='lefth' valign='middle' $bord>$alink $nomeT</a> $divTT</td>
					  <td align='right' valign='middle' $bord>$salario_brutoF</td>
					  <td align='right' valign='middle' $bord>$total_rendiF</td>
					  <td align='right' valign='middle' $bord>$total_debitoF</td>
					  <td align='right' valign='middle' $bord>$valor_inssF</td>
					  <td align='right' valign='middle' $bord>$valor_IRF</td>
					  <td align='right' valign='middle' $bord>$valor_familiaF</td>
					  <td align='right' valign='middle' $bord>$valor_final_individualF</td>	  
					</tr>";
					// AQUI TERMINA O LAÇO ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM ÚNICO FUNCIONARIO		  
					// SOMANDO VARIAVIES PARA CHEGAR AO VALOR FINAL
					$salario_brutoFinal01[] = $row_clt['salbase'];
					$total_rendiFinal01[] = $row_clt['rend'];
					$total_debitoFinal01[] = $row_clt['desco'];
					$valor_inssFinal01[] = $row_clt['a5020'];
					$valor_IRFinal01[] = $row_clt['a5021'];
					$valor_familiaFinal01[] = $row_clt['a5022'];
					$valor_liquiFinal01[] = $row_clt['salliquido'];
					$cont++;
		  
		   }	
		   $salario_brutoFinal = array_sum($salario_brutoFinal01);
		   $total_rendiFinal = array_sum($total_rendiFinal01);
		   $total_debitoFinal = array_sum($total_debitoFinal01);
		   $valor_inssFinal = array_sum($valor_inssFinal01);
		   $valor_IRFinal = array_sum($valor_IRFinal01);
		   $valor_familiaFinal = array_sum($valor_familiaFinal01);
		   $valor_liquiFinal = array_sum($valor_liquiFinal01);	
		   
			// FORMATANDO OS DADOS FINAIS - FORMATO BRASILEIRO PARA VISUALIZAÇÃO (5.100,00)
			$salario_brutoFinalF = number_format($salario_brutoFinal,2,",",".");
			$total_rendiFinalF = number_format($total_rendiFinal,2,",",".");
			$total_debitoFinalF = number_format($total_debitoFinal,2,",",".");
			$valor_inssFinalF = number_format($valor_inssFinal,2,",",".");
			$valor_IRFinalF = number_format($valor_IRFinal,2,",",".");
			$valor_familiaFinalF = number_format($valor_familiaFinal,2,",",".");
			$valor_liquiFinalF = number_format($valor_liquiFinal,2,",",".");		
		
			print '<tr>';
			print '<td height="20" align="center" valign="middle" class="style23">&nbsp;</td>';
			print '<td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>';
			print '<td align="right" valign="bottom" class="style23">'.$salario_brutoFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$total_rendiFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$total_debitoFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$valor_inssFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$valor_IRFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$valor_familiaFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">'.$valor_liquiFinalF.'</td>';
			print '<td align="right" valign="bottom" class="style23">&nbsp;</td>';
			print '</tr>';

	 }else{
	 		print '<tr align=center><td><span style="color:red"> <b>NÃO HÁ FUNCIONÁRIO FINALIZADO</b> </span></td></tr>';
	 }
    ?>
      </table>
	<?php
    
    //-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkvolt = encrypt("$regiao&$regiao"); 
    $linkvolt = str_replace("+","--",$linkvolt);
    // -----------------------------
    
    ?>
	
    </table>
    <br/>
    <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
  		<tr>
    		<td align="center" valign="middle" bgcolor="#CCCCCC"><b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b></td>
  		</tr>
	</table>

</body>
</html>