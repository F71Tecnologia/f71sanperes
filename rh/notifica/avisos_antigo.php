<?php
include('../../conn.php');
if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='login.php'>Logar</a>";
exit;
}

$id_regiao = $_REQUEST['regiao'];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GERENCIAMENTO DE NOTIFICA&Ccedil;&Otilde;ES</title>
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../net1.css" rel="stylesheet" type="text/css" />
<link href="../../SpryAssets/SpryAccordion.css" rel="stylesheet" type="text/css">
<script src="../../SpryAssets/SpryAccordion.js" type="text/javascript"></script>
<script type="text/javascript">
function MM_preloadImages() {
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
</script>
</head>
<body>
<table width="80%" border="0" align="center" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0" class="bordaescura1px">
  <tr>
    <td colspan="2" align="right" valign="middle"><?php include('../../reportar_erro.php');?> </td>
  </tr>
  <tr>
    <td width="100%" colspan="2" align="center" valign="middle">
       <p class="linha">
	   <?php include "../../empresa.php";
             $img= new empresa();
             $img -> imagemCNPJ(); ?>
       </p></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle"><br>
      <span class="linha">GERENCIAMENTO DE AVISOS E NOTIFICA&Ccedil;&Otilde;ES</span><br>
      <br>
      <br>
      <div id="Accordion1" class="Accordion" tabindex="0">
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          <?php $qr_aviso_previo = mysql_query("SELECT * FROM rh_recisao WHERE id_regiao = '$id_regiao'");
		        $num_aviso_previo = 0;
			       while($aviso_previo = mysql_fetch_assoc($qr_aviso_previo)) {
					    if(date('Y-m-d') <= $aviso_previo['data_fim_aviso']) {
						       $num_aviso_previo = $num_aviso_previo + 1;
					    }
				   } ?>
				  AVISO PR&Eacute;VIO (<?=$num_aviso_previo?>)
          </div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="4" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>IN&Iacute;CIO DO AVISO</td>
                <td>T&Eacute;RMINO DO AVISO</td>
              </tr>
              <?php $qr_aviso_previo_2 = mysql_query("SELECT * FROM rh_recisao WHERE id_regiao = '$id_regiao'");
			            while($aviso_previo_2 = mysql_fetch_assoc($qr_aviso_previo_2)) {
					         if(date('Y-m-d') <= $aviso_previo_2['data_fim_aviso']) { ?>
              <tr>
                <td><?=$aviso_previo_2['id_clt']?></td>
                <td><?=$aviso_previo_2['nome']?></td>
                <td>
				<?php $inicio_aviso = explode("-", $aviso_previo_2['data_fim_aviso']);
				      $dia_inicio = $inicio_aviso[2];
				      $mes_inicio = $inicio_aviso[1];
					  $ano_inicio = $inicio_aviso[0];
				      $inicio_aviso = date('d/m/Y', mktime(0,0,0,$mes_inicio,$dia_inicio-30,$ano_inicio)); 
					  echo $inicio_aviso; ?>
                </td>
                <td><?php echo date('d/m/Y', mktime(0,0,0,$mes_inicio,$dia_inicio,$ano_inicio)); ?></td>
              </tr>
              <?php } } ?>
            </table>
          </div>
        </div>
        
<!-- Observações individuais -->
<?
// OBSERCAÇÕES INDIVIDUAIS
$resultOBS = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$id_regiao' AND observacao != '' AND status < '60' ORDER BY nome ASC");
$quantidade = mysql_affected_rows();
?>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">OBSERVA&Ccedil;&Otilde;ES INDIVIDUAIS (<?=$quantidade?>)</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0" class="texto">
              <tr>
                <td colspan="3" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>OBSERVA&Ccedil;&Otilde;ES</td>
              </tr>
              <?php
			  while($rowOBS = mysql_fetch_array($resultOBS)) {
              	print "<tr>";
              	print "<td>$rowOBS[id_clt]</td>";
              	print "<td><a href='../alter_clt.php?clt=$rowOBS[id_clt]&pro=$rowOBS[id_projeto]&pagina=clt' class='linkBlack'>$rowOBS[nome]</a></td>";
              	print "<td>$rowOBS[observacao]</td>";
              	print "</tr>";
			  }
			  ?>             
            </table>
          </div>
        </div>
<!-- Final de observações individuais -->
        
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          <?php $qr_acidente = mysql_query("SELECT *, date_format(data_saida, '%d/%m/%Y') AS data_saida FROM rh_clt WHERE status = '70' AND id_regiao = '$id_regiao'");
			    $num_acidente = mysql_num_rows($qr_acidente); ?>
				ACIDENTE DE TABALHO (<?=$num_acidente?>)
          </div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="4" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>DATA SA&Iacute;DA</td>
                <td>DATA RETORNO</td>
              </tr>
              <?php while($acidente = mysql_fetch_assoc($qr_acidente)) {
				       $qr_datas_acidente = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') as data, date_format(data_retorno, '%d/%m/%Y') as data_retorno FROM rh_eventos WHERE id_clt = '$acidente[id_clt]' ORDER BY id_evento DESC");
				       $datas_acidente = mysql_fetch_assoc($qr_datas_acidente); ?>
              <tr>
                <td><?=$acidente['id_clt']?></td>
                <td><?=$acidente['nome']?></td>
                <td><?=$datas_acidente['data']?></td>
                <td><?=$datas_acidente['data_retorno']?></td>
              </tr>
              <?php } ?>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          <?php $qr_licensas = mysql_query("SELECT * FROM rh_clt WHERE id_regiao = '$id_regiao' AND status IN('20','30','50','51','52','90','100','80','110')");
		        $numero_licensas = mysql_num_rows($qr_licensas); ?>
                LICEN&Ccedil;AS (<?=$numero_licensas?>)
          </div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="5" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>TIPO DE LICEN&Ccedil;A</td>
                <td>IN&Iacute;CIO DA LICEN&Ccedil;A</td>
                <td>T&Eacute;RMINO DA LICEN&Ccedil;A</td>
              </tr>
              <?php while ($licensa = mysql_fetch_array($qr_licensas)) { ?>
              <tr>
                <td><?=$licensa['campo3']?></td>
                <td><?=$licensa['nome']?></td>
                <td><?php $qr_nome_licensa = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$licensa[status]'");                          $nome_licensa = mysql_fetch_assoc($qr_nome_licensa); 
				          echo $nome_licensa['especifica']; ?></td>
                    <?php $qr_datas_licensa = mysql_query("SELECT *, date_format(data, '%d/%m/%Y') as data FROM rh_eventos WHERE id_clt = '$licensa[id_clt]'");
				          $datas_licensa = mysql_fetch_assoc($qr_datas_licensa); ?>
				<td><?=$datas_licensa['data']?></td>
                <td <?php if(date('Y-m-d') > $datas_licensa['data_retorno']) { echo "style=color:#C30;"; } ?>>
                    <?php echo implode("/", array_reverse(explode("-", $datas_licensa['data_retorno']))); ?></td>
              </tr>
              <?php } ?>
            </table>
        </div></div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          AQUISI&Ccedil;&Atilde;O DE F&Eacute;RIAS
          </div>
            <div class="AccordionPanelContent">
            <table width="95%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="6" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>SITUA&Ccedil;&Atilde;O ATUAL</td>
                <td>DIAS PRÉ PERIODO</td>
                <td>DATA DE AQUISI&Ccedil;&Atilde;O</td>
                <td>VENCIMENTO DO PER&Iacute;DO</td>
              </tr>
          <?php $qr_aquisicao_clt = mysql_query("SELECT * FROM rh_clt WHERE status < '60' AND id_regiao = '$id_regiao'");
			    while($aquisicao_clt = mysql_fetch_assoc($qr_aquisicao_clt)) {
				     $qr_numero_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$aquisicao_clt[id_clt]'");
				     $numero_ferias = mysql_num_rows($qr_numero_ferias);
				     $data_entrada = explode("-", $aquisicao_clt['data_entrada']);
	                 $data_aquisicao = date('d/m/Y', mktime('0', '0', '0', $data_entrada[1], $data_entrada[2], $data_entrada[0] + $numero_ferias + 1));
				     $data_vencimento = date('d/m/Y', mktime('0', '0', '0', $data_entrada[1], $data_entrada[2] - 1, $data_entrada[0] + $numero_ferias + 2));
					 $data_diferenca = explode("/", $data_vencimento);
                     $dias_diferenca = (int)((mktime(0,0,0,$data_diferenca[1],$data_diferenca[0],$data_diferenca[2]) - time())/86400); if($dias_diferenca <= 30 and $dias_diferenca >= 0) { ?>
              <tr>
                <td><?=$aquisicao_clt['campo3']?></td>
                <td><?=$aquisicao_clt['nome']?></td>
                <td><?php $qr_nome_licensa = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$aquisicao_clt[status]'");                          $nome_licensa = mysql_fetch_assoc($qr_nome_licensa);
				          echo $nome_licensa['especifica']; ?></td>
                <td><?=$dias_diferenca?> dias restando</td>
                <td><?=$data_aquisicao?></td>
                <td><?=$data_vencimento?></td>
              </tr>
           <?php }
		   unset($data_diferenca);
		   unset($dias_diferenca);
		   unset($data_aquisicao);
		   unset($data_vencimento);
		   } ?>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          <?php $qr_ferias = mysql_query("SELECT * FROM rh_clt WHERE status = '40' AND id_regiao = '$id_regiao' ORDER BY id_clt ASC");
		        $numero_ferias = mysql_num_rows($qr_ferias); ?>
				RETORNO DE F&Eacute;RIAS (<?=$numero_ferias?>)
          </div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="2" style="text-align:center; font-size:11px; font-weight:bold;">
              <tr>
                <td colspan="4" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr>
                <td bgcolor="#CCCCCC" style="width:50px;">COD</td>
                <td bgcolor="#CCCCCC" style="width:320px;">NOME</td>
                <td bgcolor="#CCCCCC" style="width:100px;">DATA SAIDA</td>
                <td bgcolor="#CCCCCC" style="width:100px;">DATA RETORNO</td>
              </tr>
         <?php while($ferias = mysql_fetch_assoc($qr_ferias)) { 
			   $qr_ferias2 = mysql_query("SELECT * , date_format(data_fim, '%d/%m/%Y') as data_fim, date_format(data_retorno, '%d/%m/%Y') as data_retorno FROM rh_ferias WHERE id_clt = '$ferias[id_clt]' AND regiao = '$id_regiao' ORDER BY id_ferias DESC"); 
			   $ferias2 = mysql_fetch_assoc($qr_ferias2); ?>
              <tr>
                <td><?php echo $ferias['id_clt']; ?></td>
                <td><?php echo ucwords(strtolower($ferias['nome'])); ?></td>
                <td><?php echo $ferias2['data_fim']; ?></td>
                <td><?php echo $ferias2['data_retorno']; ?></td>
              </tr>
              <?php } ?>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">
          <?php $qr_sindicatos = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$id_regiao' AND status = '1'");
		        $numero_sindicatos = mysql_num_rows($qr_sindicatos); ?>
          CONTRIBUI&Ccedil;&Atilde;O SINDICAL (<?=$numero_sindicatos?>)</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="2" align="center" bgcolor="#FFCCCC">SINDICATOS</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td width="65%">SINDICATO</td>
                <td width="35%">PAGAMENTO (M&Ecirc;S)</td>
              </tr>
              <?php while($sindicatos = mysql_fetch_assoc($qr_sindicatos)) { ?>
              <tr>
                <td><?=$sindicatos['nome']?></td>
                <td align="center" style="font-weight:bold;">
           <?php $qr_clt_sindicatos = mysql_query("SELECT * FROM rh_clt WHERE rh_sindicato = '$sindicatos[id_sindicato]' AND status IN('10','40','50','51','52','30','110')");
                 $numero_clt_sindicatos = mysql_num_rows($qr_clt_sindicatos);
                 $ContribuicaoFinal = 0;
                 while($clt_sindicatos = mysql_fetch_assoc($qr_clt_sindicatos)){

				$GetSalario = mysql_query("SELECT salario FROM curso WHERE id_curso = '$clt_sindicatos[id_curso]'");
				$Salario = mysql_fetch_array($GetSalario);
				$SalarioCalc = $Salario['salario'];
				$SalarioSoma = $SalarioCalc / 30;
				$ContribuicaoFinal = $SalarioSoma + $ContribuicaoFinal;
		
		}
		
		echo "R$ ".number_format($ContribuicaoFinal,2,",","")."";
		$meses = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
		
				      for($i=0; $i<=12; $i++) {
					     if($sindicatos['mes_desconto'] == $i) {
					         echo " ($meses[$i])";
					     }
					  }
         ?>
                </td>
              </tr>
              <?php } ?>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">CONTRATOS EM EXPERI&Ecirc;NCIA</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0" class="texto">
              <tr>
                <td colspan="5" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>COD</td>
                <td>NOME</td>
                <td>CONTRATADO EM</td>
                <td>T&Eacute;RMINO DA EXPERI&Ecirc;NCIA</td>
              </tr>
          <?php $qr_experiencia_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada FROM rh_clt WHERE status < '60' AND id_regiao = '$id_regiao'");
			    while($experiencia_clt = mysql_fetch_assoc($qr_experiencia_clt)) {
					$data_entrada = explode("/", $experiencia_clt['data_entrada']);
                    $dias_diferenca = (int)((time() - mktime(0,0,0,$data_entrada[1],$data_entrada[0],$data_entrada[2]))/86400);
					$termino_experiencia = date('d/m/Y', mktime('0', '0', '0', $data_entrada[1],$data_entrada[0] + 90,$data_entrada[2]));
					if($dias_diferenca <= 90 and $dias_diferenca >= 0) { ?>
              <tr>
                <td><?=$experiencia_clt['campo3']?></td>
                <td><?=$experiencia_clt['nome']?></td>
                <td><?=$experiencia_clt['data_entrada']?></td>
                <td><?=$termino_experiencia?></td>
              </tr>
              <?php } } ?>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">RAIS</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="4" align="center" bgcolor="#FFCCCC">RAIS</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>DATA ENTREGA</td>
                <td>DATA LIMITE</td>
                <td>DIAS ATRASADOS</td>
                <td>ENTREGUE</td>
              </tr>
              <?php $ano_base_rais = date('Y') - 1;
			        $qr_rais = mysql_query("SELECT * FROM rais WHERE ano_base = '$ano_base_rais' AND regiao = '$id_regiao'");
			        $rais = mysql_num_rows($qr_rais);
					$ano_atual_rais = date('Y'); ?>
              <tr>
                <td>01/01/<?=$ano_atual_rais?></td>
                <td>01/03/<?=$ano_atual_rais?></td>
                <td><?php $diferenca2 = (int)((time() - mktime(0,0,0,03,01,$ano_atual_rais))/86400);
                          if($diferenca2 > 0) { echo "$diferenca2 dias"; } else { echo "Dentro do Prazo"; } ?></td>
                <td><?php if(empty($rais)) { echo "Não"; } else { echo "Sim"; } ?></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">DARF - IR</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="6" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td width="25%">Folha</td>
                <td width="15%">Cria&ccedil;&atilde;o</td>
                <td width="13%">Mês</td>
                <td width="30%">Data de Pagamento</td>
                <td width="5%" align="center">CLTs</td>
                <td width="12%" align="center">Gerar IR</td>
              </tr>

 <?php $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
       $regiao = mysql_fetch_assoc($qr_regiao);
       $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND regiao = '$id_regiao' ORDER BY id_folha ASC");
	   $numero_folha = mysql_num_rows($qr_folha);
	   if(!empty($numero_folha)) {
          while($folha = mysql_fetch_assoc($qr_folha)) { 
		     $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
		     $projeto = mysql_fetch_assoc($qr_projeto); ?>
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>" style="text-align:center;">
       <td style="text-align:left; padding-left:5px;"><?php echo $folha['id_folha']." - ".$projeto['nome']; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_proc']))); ?></td>
       <td><?php $meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'); 
	             echo $meses[(int)$folha['mes']]; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_inicio']))); ?> até 
	       <?php echo implode("/", array_reverse(explode("-", $folha['data_fim']))); ?></td>
       <td><?=$folha['clts']?></td>
       <td><a href="../ir/ir.php?regiao=<?=$_GET['regiao']?>&folha=<?=$folha['id_folha']?>&tipo=2" target="_blank" title="Gerar IR | mês: <?=$meses[(int)$folha['mes']]?> | projeto: <?php echo $folha['id_folha']." - ".$projeto['nome']; ?>"><img src="../ir/imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
    </tr>
    <?php } } ?>
    </table>
            
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">DARF - PIS</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="6" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td width="25%">Folha</td>
                <td width="15%">Cria&ccedil;&atilde;o</td>
                <td width="13%">Mês</td>
                <td width="30%">Data de Pagamento</td>
                <td width="5%" align="center">CLTs</td>
                <td width="12%" align="center">Gerar IR</td>
              </tr>

 <?php $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$_GET[regiao]'");
       $regiao = mysql_fetch_assoc($qr_regiao);
       $qr_folha = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND regiao = '$regiao[id_regiao]' ORDER BY id_folha ASC");
       $numero_folha = mysql_num_rows($qr_folha);
	   if(!empty($numero_folha)) {
          while($folha = mysql_fetch_assoc($qr_folha)) { 
		  $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
		  $projeto = mysql_fetch_assoc($qr_projeto); ?>
    <tr class="linha_<? if($alternateColor++%2==0) { ?>um<? } else { ?>dois<? } ?>" style="text-align:center;">
       <td style="text-align:left; padding-left:5px;"><?php echo $folha['id_folha']." - ".$projeto['nome']; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_proc']))); ?></td>
       <td><?php $meses = array('ERRO','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'); echo $meses[(int)$folha['mes']]; ?></td>
       <td><?php echo implode("/", array_reverse(explode("-", $folha['data_inicio']))); ?> até <?php echo implode("/", array_reverse(explode("-", $folha['data_fim']))); ?></td>
       <td><?=$folha['clts']?></td>
       <td><a href="../pis/pis.php?regiao=<?=$_GET['regiao']?>&folha=<?=$folha['id_folha']?>&tipo=2" target="_blank" title="Gerar PIS | mês: <?=$meses[(int)$folha['mes']]?> | projeto: <?php echo $folha['id_folha']." - ".$projeto['nome']; ?>"><img src="../pis/imagens/pdf.jpg" width="25" height="25" alt="pdf"></a></td>
    </tr>
    <?php } } ?>
    </table>
          </div>
        </div>
        <div class="AccordionPanel">
          <div class="AccordionPanelTab">GPS</div>
          <div class="AccordionPanelContent">
            <table width="90%" border="1" cellspacing="0" cellpadding="0">
              <tr>
                <td colspan="5" align="center" bgcolor="#FFCCCC">PROJETO</td>
              </tr>
              <tr style="text-align:center; background-color:#CCC;">
                <td>GPS</td>
                <td>DATA PAGAMENTO</td>
                <td>DATA LIMITE</td>
                <td>MULTA</td>
                <td>DIAS DE ATRASO</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <br>      
      &nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top">&nbsp;</td>
    <td align="right" valign="top">&nbsp;</td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1");
</script>
</body>
</html>
