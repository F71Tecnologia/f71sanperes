<?php
// Verificando se o usuário está logado
if(empty($_COOKIE['logado'])) {
print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
exit;
}

// Aumentando Tempo Limite de Resposta do Servidor
set_time_limit(120);

// Incluindo Arquivos
require('../../conn.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/regiao.php');
include('../../funcoes.php');

// Id da Folha
$enc   = str_replace('--', '+', $_REQUEST['enc']);
$enc   = explode('&', decrypt($enc));
$folha = $enc[1];

// Consulta da Folha
$qr_folha    = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
									  date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
									  date_format(data_proc, '%d/%m/%Y') AS data_proc_br
							     FROM rh_folha WHERE id_folha = '$folha' AND status = '3'");
$row_folha   = mysql_fetch_array($qr_folha);
$data_inicio = $row_folha['data_inicio'];
$data_fim    = $row_folha['data_fim'];
$ano         = $row_folha['ano'];
$mes         = $row_folha['mes'];
$mes_int     = (int)$mes;

// Consulta do Usuário que gerou a Folha
$qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

// Redefinindo Variáveis de Décimo Terceiro
if($row_folha['terceiro'] != 1) {
	$decimo_terceiro = NULL;
} else {
	$decimo_terceiro = 1;
	$tipo_terceiro   = $row_folha['tipo_terceiro'];
}

// Consulta da Região
$qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao    = mysql_result($qr_regiao, 0, 0);

// Consulta do Projeto
$qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$projeto    = mysql_result($qr_projeto, 0, 0);

// Consulta dos Participantes da Folha
$qr_participantes    = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status IN('2','3','4') ORDER BY nome ASC");
$total_participantes = mysql_num_rows($qr_participantes);

// Definindo Mês da Folha
$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

if(!empty($decimo_terceiro)) {
	switch($tipo_terceiro) {
		case 1:
		$mes_folha = '13º Primeira parcela';
		break;
		case 2:
		$mes_folha = '13º Segunda parcela';
		break;
		case 3:
		$mes_folha = '13º Integral';
		break;
	}
} else {
	$mes_folha = "$meses[$mes_int] / $ano";
}









if(!empty($_REQUEST['agencia'])) {
	
	$ag  = $_REQUEST['agencia'];
	$cc  = $_REQUEST['conta'];
	$enc = $_REQUEST['enc'];
	$enc = str_replace('+', '--', $enc);
	
	$clt = $_REQUEST['clt'];
	$tipo_conta = $_REQUEST['radio_tipo_conta'];
	
	$qr_clt = mysql_query("SELECT * FROM rh_folha_proc where id_folha_proc = '$clt' and status = 3 and tipo_pg = '0'") or die(mysql_error());
	$row = mysql_fetch_array($qr_clt);
	
	mysql_query("UPDATE rh_clt SET agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_clt = '$RowCLT[id_clt]'") or die(mysql_error());
	mysql_query("UPDATE rh_folha_proc SET agencia='$ag', conta='$cc' WHERE id_folha_proc = '$clt'") or die(mysql_error());
}



$result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE cod != '0001' ORDER BY cod");

while($row_codigos = mysql_fetch_array($result_codigos)) {
	$ar_codigos[] = $row_codigos['0'];
}

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Finalizada</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
	function tubarao() {
	    $('a.relatorio').click(function(){
		    return hs.htmlExpand(this, { objectType: 'iframe'} );
		});
	}
	$().ready(function(){
        $('a.participante').tooltip({
		    tip: '.tooltip',
			position: 'center right',
			onShow: tubarao
		});
	});
</script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
</style>
</head>
<body>
<div id="corpo">
	<table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../../imagens/logomaster<?=mysql_result($qr_projeto, 0, 2)?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.$mes_folha.')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>

   <table cellpadding="0" cellspacing="1" id="folha">
            <tr>
              <td colspan="8">
                <a href="<?=$link_voltar?>" class="voltar">Voltar</a>
              </td>
            </tr>
            <tr>
              <td colspan="9">
              		
                    <table cellspacing="0" cellpadding="0" width="100%">
                     <tr class="secao">
                      <td width="32%" align="left" style="padding-left:5px;">NOME</td>
                      <td width="6%">DIAS</td>
                      <td width="8%">BASE</td>
                      <td width="10%">RENDIMENTOS</td>
                      <td width="10%">DESCONTOS</td>
                      <td width="8%">INSS</td>
                      <td width="8%">IRRF</td>
                      <td width="8%">FAM&Iacute;LIA</td>
                      <td width="10%">L&Iacute;QUIDO</td>
                     </tr>
                    </table>
                    
              </td>
            </tr>
            
		  <?php // Início do Loop dos Participantes da Folha
          		while($row_participante = mysql_fetch_array($qr_participantes)) {
              
				  // Id do Participante
				  $clt = $row_participante['id_clt'];
				  
				  // Consulta de Dados do Participante
				  $qr_clt  = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
				  $row_clt = mysql_fetch_array($qr_clt);
				  
				  // Link para Relatório
				  $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));
				  
				  // Define que o funcionário receba caso não tenha CONTA / AGÊNCIA / TIPO DE CONTA
		  		  $qr_tipo_conta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = '$clt'");
		 		  $tipo_conta    = mysql_fetch_array($qr_tipo_conta);
	
				  $qr_tipo_pagamento = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' AND campo1 = '2' AND id_projeto = '$row_projeto[0]'");
				  $tipo_pagamento    = mysql_fetch_assoc($qr_tipo_pagamento);
				  $pagamento_cheque  = $tipo_pagamento[0];
		  
				  if(empty($row_clt['conta'])) {
						mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pagamento_cheque' WHERE id_folha = '$folha' AND id_clt = '$clt'");
				  } if(empty($row_clt['agencia'])) {
						mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pagamento_cheque' WHERE id_folha = '$folha' AND id_clt = '$clt'");
				  } if(empty($tipo_pagamento['tipo_conta'])) {
						mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pagamento_cheque' WHERE id_folha = '$folha' AND id_clt = '$clt'");
				  }	?>     
            
        <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		    <td width="32%" align="left">
				<a class="participante" title="<?=$row_clt['nome']?><br><i><span class=
				<?php if(isset($dias_entrada)) { 
                          echo "entrada>FOI CONTRATADO EM ".formato_brasileiro($row_clt['data_entrada'])."";
                      } elseif(isset($dias_evento)) { 
                          echo "evento>FICOU $dias_evento DE LICENSA";
                      } elseif(isset($dias_ferias)) {
                          echo "ferias>TEVE $dias_ferias DIAS DE FÉRIAS";
                      } elseif(isset($dias_rescisao)) { 
                          echo "rescisao>FOI RESCINDIDO EM ".formato_brasileiro($row_clt['data_saida'])."";
                      } elseif(isset($dias_faltas)) { 
                          echo "faltas>FALTOU $dias_faltas DIAS";
                      } else { 
                          echo "normal>";
                      } ?></span></i>
                          <p>&nbsp;</p>
                          <a href='sintetica/relatorio.php?enc=<?=$relatorio?>' onClick='return hs.htmlExpand(this, { objectType: 'iframe' } )' class='relatorio'>Ver Relatório <img src='sintetica/seta_um.gif'></a>">
                    <span class="
                    <?php       if(isset($dias_entrada))  { echo 'entrada';
                          } elseif(isset($dias_evento))   { echo 'evento';
                          } elseif(isset($dias_ferias))   { echo 'ferias';
                          } elseif(isset($dias_rescisao)) { echo 'rescisao';
                          } elseif(isset($dias_faltas))   { echo 'faltas';
                          } else                          { echo 'normal';
                          } ?>">
                        <?=$clt.' - '.abreviacao($row_clt['nome'], 4, 1)?>
                    </span>
                </a>
            </td>
            <td width="6%"><?=$dias?></td>
			<td width="8%"><?=formato_real($salario)?></td>
			<td width="10%"><?=formato_real($rendimentos)?></td>
			<td width="10%"><?=formato_real($descontos)?></td>
            <td width="8%"><?=formato_real($inss_completo)?></td>
            <td width="8%"><?=formato_real($irrf_completo)?></td>
            <td width="8%"><?=formato_real($familia)?></td>
			<td width="10%"><?=formato_real($liquido)?></td>
		 </tr>
            
             
       
        <?php }
		
		//---- FORMATANDO OS TOTAIS GERAIS DA FOLHA -----------
		$salario_base_finalF = number_format($row_folha['total_salarios'],2,",",".");
		$rendi_indiviF = number_format($row_folha['rendi_indivi'],2,",",".");
		$rendi_finalF = number_format($row_folha['rendi_final'],2,",",".");
		$final_indiviF = number_format($row_folha['descon_indivi'],2,",",".");
		$final_INSSF = number_format($row_folha['total_inss'],2,",",".");
		$final_IRF = number_format($row_folha['total_irrf'],2,",",".");
		$final_familiaF =  number_format($row_folha['total_familia'],2,",",".");;
		$valor_finalF = number_format($row_folha['total_liqui'],2,",",".");
		$totalDeFGTS = number_format($row_folha['total_fgts'],2,",",".");
		
		$base_INSS_TO = number_format($row_folha['valor_dt']+$row_folha['base_inss'],2,",",".");
		$base_IRRFF = number_format($row_folha['base_irrf'],2,",",".");
		//-----------------------
		
				
		//VERIFICANDO SE VAI MOSTRAR OU NÃO OS DESCONTOS FIXOS (EX VALE, INSS, IR, FAMILIA)------------
		$movimentos_fixos = array(0001,7001,5020,5021,5022,5019,5047);
		$valores_movimentos_fixos = array($salario_base_finalF,$vale_transporte_finalF,$final_INSSF,$final_IRF,$final_familiaF,$final_sindicatoF,$final_deducaoIRF);
		
		// colocando o valor livre de redimento (feito por jr 05-02-2010 as 14:49)
		// (feito por jr 06-05-2010 as 16:16) $salariototal = number_format($row_folha['total_salarios'] - $row_folha['rendi_indivi'],2,",",".");
		$salariototal = number_format($row_folha['total_salarios'],2,",",".");
		$salario_finalF = number_format($salario_final,2,",",".");
		?>
        
         <tr class="totais">
          <td><a href="#corpo" class="ancora">Subir ao topo</a></td>
          <td>TOTAIS:</td>
          <td><?=formato_real($salario_total)?></td>
		  <td><?=formato_real($rendimentos_total)?></td>
		  <td><?=formato_real($descontos_total)?></td>
          <td><?=formato_real($inss_completo_total)?></td>
          <td><?=formato_real($irrf_completo_total)?></td>
          <td><?=formato_real($familia_total)?></td>
		  <td><?=formato_real($liquido_total)?></td>
        </tr>
    </table>
     
      <table width="97%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="39%" align="center" valign="top" bgcolor="#F8F8F8" style="border-right:solid 2px #FFF"><br>
            <table width="90%" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td height="24" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
              </tr>
              <tr class="novalinha corfundo_um">
                <td width="53%" align="right">Sal&aacute;rios L&iacute;quidos:</td>
                <td width="47%" align="left"> &nbsp;&nbsp;<?=$valor_finalF?></td>
              </tr>
              <tr class="novalinha corfundo_dois">
                <td align="right">Base de INSS:</td>
                <td align="left"> &nbsp;&nbsp;<?=$base_INSS_TO?></td>
              </tr>
              <tr class="novalinha corfundo_um">
                <td align="right">Base de IRRF:</td>
                <td align="left">&nbsp;&nbsp;<?=$base_IRRFF?></td>
              </tr>
              <tr class="novalinha corfundo_dois">
                <td align="right">Base de FGTS:</td>
                <td align="left">&nbsp;&nbsp;<?=$base_INSS_TO?></td>
              </tr>
              <tr class="novalinha corfundo_um">
                <td align="right">Total de FGTS:</td>
                <td align="left" valign="middle">&nbsp;&nbsp;<?=$totalDeFGTS?></td>
              </tr>
              <tr class="novalinha corfundo_dois">
                <td align="right">Base de FGTS (Sefip):</td>
                <td align="left" valign="middle">&nbsp;&nbsp;<?=$base_INSS_TO?></td>
              </tr>
              <tr class="novalinha corfundo_um">
                <td align="right">FGTS a Recolher (Sefip):</td>
                <td align="left" valign="middle">&nbsp;&nbsp;<?=$totalDeFGTS?></td>
              </tr>
              <tr class="novalinha corfundo_dois">
                <td align="right">Multa do FGTS:</span></td>
                <td align="left">&nbsp;&nbsp; 0,00</td>
              </tr>
              <tr class="novalinha corfundo_um">
                <td align="right">Funcion&aacute;rios Listados:</td>
                <td align="left" valign="middle">&nbsp;&nbsp;<?=$row_folha['clts']?></td>
              </tr>
          </table></td>
          <td width="61%" align="center" valign="top" bgcolor="#F8F8F8" style="border-left:solid 2px #FFF"><br>
          <table width="95%" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" colspan="4" align="center" valign="middle" class="show">Resumo por Evento (R$)</td>
            </tr>
            <tr class="novo_tr_dois">
              <td width="11%" align="center" valign="middle" >Evento</td>
              <td width="45%" align="left" valign="middle" >Descri&ccedil;&atilde;o </td>
              <td width="21%" height="20" align="right" valign="middle" >Rendimentos </td>
              <td width="23%" align="right" valign="middle"  style='margin-right:5;'>Descontos</td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td align="center">0001</td>
              <td align="left" >SALARIO BASE</td>
              <td align="right" ><b><?=$salario_base_finalF?></b></td>
              <td align="right" >&nbsp;</td>
            </tr>
            <?php
		$qntd = count($ar_codigos);
		for($i=0 ; $i < $qntd ; $i ++){
			$result_codNomes = mysql_query("SELECT descicao FROM rh_movimentos WHERE cod='$ar_codigos[$i]'");
			$row_codNome = mysql_fetch_array($result_codNomes);
			$campo = "a".$ar_codigos[$i];
			
			$reult_soma = mysql_query("SELECT SUM($campo) FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3'");
			$row_soma = mysql_fetch_array($reult_soma);
			
			$debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','7001');
			$rendimentos_tab = array('5011','5022','6006','6007','9000','5022');
		
			if (in_array($ar_codigos[$i], $debitos_tab)) { 
				if($ar_codigos[$i] == "5020"){
					$debito = number_format($row_folha['total_inss'],2,",",".");
				}else{
					$debito = number_format($row_soma['0'],2,",",".");
				}
			}else{
				$rendimento = number_format($row_soma['0'],2,",",".");
			}
		
			if($rendimento == "0,00" or $debito == "0,00"){
				$disable = "style='display:none'";
			}else{
				$disable = "style='display:'";
			}
			
			if($campo == "a5049"){					//DDIR
				$disable = "style='display:none'";
			}
			
			print "<tr class=\"novalinha corfundo_um\" $disable>
	          <td height='18' align='center' valign='middle'>$ar_codigos[$i]</td>
	          <td align='left' valign='middle'>$row_codNome[0]</td>
	          <td align='right' valign='middle'><span style='margin-right:1;'><b>".$rendimento."&nbsp;</b></span></td>
	          <td align='right' valign='middle' ><span style='margin-right:5;'><b>".$debito."&nbsp;</b></span></td></tr>";
			
			$debito = "";
			$rendimento = "";
			
		}
		?>
         <?php if($row_folha['terceiro'] == 1){ ?>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5029</td>
              <td align="left" valign="middle">D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle"><b>
                <?=number_format($row_folha['valor_dt'],2,",",".")?>
              </b></td>
              <td align="right" valign="middle">&nbsp;</td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">5030</td>
              <td align="left" valign="middle">IRRF D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b><?=number_format($row_folha['ir_dt'],2,",",".")?></b></span></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5031</td>
              <td align="left" valign="middle">INSS TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b><?=number_format($row_folha['inss_dt'],2,",",".")?></b></span></td>
            </tr>
            <?php
            
			}
				
	
		//FORMATANDO TOTAIS POR EVENTO
		$re_tot_rendimentofimF = number_format($row_folha['rendi_final'],2,",",".");
		$re_tot_descontoF = number_format($row_folha['descon_final'],2,",",".");
		
        ?>
            <tr class="novo_tr_dois">
              <td colspan="2" align="center">TOTAIS</td>
              <td height="20" align="right" ><?=$re_tot_rendimentofimF?></td>
              <td align="right" style="text-align:right"><span style="margin-right:5;">
                <?=$re_tot_descontoF?>
              </span></td>
            </tr>
          </table></td>
        </tr>
      </table>
<br>

      
        <?php // VERIFICANDO QUAIS BANCOS ESTÃO ENVOLVIDOS COM ESSA FOLHA DE PAGAMENTO
	  
	  $qr_bancos = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_banco != '9999' AND id_folha = '$folha' and id_banco != '0' and 
	  (status = '3' or status = '4') GROUP BY id_banco");
	  $num_Bancs = mysql_num_rows($qr_bancos);
	  
	  echo "<table border='0' width='50%' border='0' cellpadding='0' cellspacing='0'>";
	  echo "<tr><td colspan=5 align='center' $bord><div style='font-size: 17px;'><b>Lista de Bancos</b></div></td></tr>";	  
	  $contCol = 0;
	  while($row_Bancs = mysql_fetch_array($RE_Bancs)) {
		  
		  $RE_Bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_Bancs[0]'");
		  $row_Bancos = mysql_fetch_array($RE_Bancos);
		  		  	  
		  //-- ENCRIPTOGRAFANDO A VARIAVEL
		  $linkBanc = encrypt("$regiao&$row_Bancos[0]&$folha");
		  $linkBanc = str_replace("+","--",$linkBanc);
		  // -----------------------------
		  
		  $linkBank = "folha_banco.php?enc=$linkBanc";
  		  $disable_form = "style='display:none'";
		  
		  echo "<tr>";
		  echo "<td align='center' valign='middle' width='30' $bord><div style='font-size: 15px;'>";
		  echo "<img src=../../imagens/bancos/$row_Bancos[id_nacional].jpg  width='25' height='25' 
		  align='absmiddle' border='0'></td>";
          echo "<td valign='middle' $bord>&nbsp;&nbsp;".$row_Bancos['nome']."</div></a></td>";		  
		  
		  $resultBancosFinalizados = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status='4' and id_banco = '$row_Bancs[0]' group by id_banco");
		  $numBancosFinalizados = mysql_affected_rows();
				if ($numBancosFinalizados != 0){						
						$rowBancosFinalizados = mysql_fetch_array($resultBancosFinalizados);
						$resultPartFinalizados = mysql_query("SELECT id_clt FROM rh_folha_proc where id_folha = '$folha' and status = '4' and id_banco = '$rowBancosFinalizados[0]'");
						$numPartFinalizados = mysql_num_rows($resultPartFinalizados);
						print "<td $bord>&nbsp;</td>";
						print "<td  align='right' $bord>";						
						print "&nbsp;&nbsp;<a href=finalizados.php?regiao=$regiao&folha=$folha&projeto=$row_projeto[0]&banco=$rowBancosFinalizados[0]>FINALIZADO</a>";
						print "</td>";
						
						echo "<td align='center' valign='middle' width='10%' $bord>$numPartFinalizados Participantes</td>";						
				}else{
						 $resultPorBanco = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$row_Bancs[0]'");
						 $quant_por_banco = mysql_affected_rows();
						 
						 if ($quant_por_banco != 0){
							  echo "<td valign='center' $bord><form id='form1' name='form1' method='post' action='$linkBank'>&nbsp;
							  <label id='data_pag$contCol' $disable_form> 
							  <input name='data' type='text' id='data[]' size='10' class='campotexto'
							  onKeyUp='mascara_data(this)' maxlength='10' onFocus=\"this.style.background='#CCFFCC'\"
							  onBlur=\"this.style.background='#FFFFFF'\" style='background:#FFFFFF' >
							  <input name='enviar' id='enviar[]' type='submit' value='Gerar'/></label>
							  </td>";
							  echo "</form>";
							  
							  echo "<td align='right' valign='middle' width='15%' $bord><a style='TEXT-DECORATION: none;'>
						  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Banco' onClick=\"document.all.data_pag$contCol.style.display = (document.all.data_pag$contCol.style.display == 'none') ? '' : 'none' ;\"></a></td>";
						  	  echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";
						 }else{	
						 		 echo "<td $bord>&nbsp;</td>";
								 echo "<td $bord align='right'><span style='font-family:verdana, arial; font-size:9px; color:red'><strong>VERIFICAR</strong></span></td>";
								 echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";	
						 }
				}
						  $contCol ++ ;
	  }
	  
	  $RE_ToCheq = mysql_query("SELECT * FROM rh_folha_proc WHERE (id_folha = '$folha' and id_banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3') or (id_folha = '$folha' and tipo_pg = '$rowTipoPg[0]' and status = '3')");
	 /// $num_ToCheq = mysql_num_rows($RE_ToCheq);
	   $num_ToCheq = mysql_affected_rows();
	  
	  //-- ENCRIPTOGRAFANDO A VARIAVEL
		$linkcheque = encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]");
		$linkcheque = str_replace("+","--",$linkcheque);
	  // -----------------------------
	  
		  echo "<tr>";
		  echo "<td align='center' valign='middle' width='30' $bord>";
		  echo "<img src=../../imagens/bancos/cheque.jpg  width='25' height='25' align='absmiddle' border='0'></td>";
          echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;Cheque</div></a></td>";
		  echo "<td valign='center' $bord>&nbsp;</td>";
		  echo "<td align='right' valign='middle' width='10%' $bord><a href='ver_cheque.php?enc=$linkcheque'>
		  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Cheque'></a></td>";
		  
		   echo "<td align='center' valign='middle' width='15%' $bord>$num_ToCheq Participantes</td>";
	  
	  echo "</tr></table>";
	  ?>
       
      
      <br>
<br>
<?php
$linkvolt = encrypt("$regiao&1"); 
$linkvolt = str_replace("+","--",$linkvolt);
$enc2     = str_replace("+","--",$enc);
?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
        <b><a href="folha.php?enc=<?=$linkvolt?>&tela=1" class="botao">VOLTAR</a></b>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <b><a href="ver_lista_banco.php?enc=<?=$enc2?>" class="botao">VER LISTA POR BANCO</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>