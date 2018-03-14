<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../classes/formato_data.php');
include('../classes/formato_valor.php');

$id_user 	   = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_assoc($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_assoc($result_master);
$Master        = $row_master['id_master'];

$tela             = $_REQUEST['tela'];
$tipo_contratacao = $_REQUEST['tipo_contratacao'];
$ano_base         = $_REQUEST['ano'];
$meses 	          = array(1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
$tipos            = array(1 => 'AUTÔNOMO', 2  => 'CLT', 3 => 'COLABORADOR', 4 => 'AUTÔNOMO PJ');

switch($tipo_contratacao) {
	
	// Folha de Autônomo
	case 1:
	
		$totalizadores_nome  = array('PARTICIPANTES', 'BASE', 'RENDIMENTOS', 'DESCONTOS', 'L&Iacute;QUIDO');
		
		// Rodando os 12 meses
		foreach($meses as $mes => $nome_mes) {
			
			$tubarao = sprintf('%02d', $mes);

			$qr_folha  = mysql_query("SELECT SUM(participantes) AS total_participantes, SUM(total_liqui) AS total_liquido, SUM(total_bruto) AS total_bruto, SUM(rendimentos) AS rendimentos, SUM(descontos) AS descontos FROM folhas WHERE mes = '$tubarao' AND regiao != '36' AND year(data_inicio) = '$ano_base' AND contratacao = '1' AND terceiro != '1' AND status = '3'");
			$row_folha = mysql_fetch_assoc($qr_folha);
			
			$totalizadores_valor[$mes] = array($row_folha['total_participantes'], $row_folha['total_bruto'], $row_folha['rendimentos'], $row_folha['descontos'], $row_folha['total_liquido']);
			
		} // Fim do Loop de 12 meses
		
	break;



   
   
    // Folha de CLT
	case 2:
	
		$totalizadores_nome = array('PARTICIPANTES', 'L&Iacute;QUIDO', 'BASE DE INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)', 'INSS (RECOLHER)', 'BASE DE IRRF', 'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS', 'BASE DE FGTS TOTAL', 'INSS', 'IRRF', 'DDIR', 'FGTS');
		
		// Rodando os 12 meses
		foreach($meses as $mes => $nome_mes) {
			
			$tubarao = sprintf('%02d', $mes);

			$qr_folha  = mysql_query("SELECT SUM(clts) AS total_participantes, SUM(total_liqui) AS total_liqui, SUM(total_irrf) AS total_irrf, SUM(base_inss) AS base_inss, SUM(total_inss) AS total_inss, SUM(inss_dt) AS inss_dt, SUM(inss_rescisao) AS inss_rescisao, SUM(inss_ferias) AS inss_ferias, SUM(total_familia) AS total_familia, SUM(base_irrf) AS base_irrf, SUM(base_fgts) AS base_fgts, SUM(base_fgts_ferias) AS base_fgts_ferias, SUM(ir_dt) AS ir_dt, SUM(ir_rescisao) AS ir_rescisao, SUM(ir_ferias) AS ir_ferias, SUM(total_fgts) AS total_fgts, SUM(fgts_dt) AS fgts_dt, SUM(fgts_rescisao) AS fgts_rescisao, SUM(fgts_ferias) AS fgts_ferias FROM rh_folha WHERE mes = '$tubarao' AND regiao != '36' AND year(data_inicio) = '$ano_base' AND terceiro != '1' AND status = '3'");
			$row_folha = mysql_fetch_assoc($qr_folha);
			
			$qr_folha_participante  = mysql_query("SELECT SUM(a5049) AS ddir FROM rh_folha_proc WHERE status IN(3,4) AND mes = '$tubarao' AND id_regiao != '36' AND ano = '$ano_base'");
			$row_folha_participante = mysql_fetch_assoc($qr_folha_participante);
			
			// Percentual RAT
			if($ano >= 2011) {
				$percentual_rat = '0.01';
			} else {
				$percentual_rat = '0.03';
			}

			if($_COOKIE['logado'] == 87) { }

			$totalizadores_valor[$mes] = array($row_folha['total_participantes'],
											   $row_folha['total_liqui'], 
											   $row_folha['base_inss'],
											   ($row_folha['base_inss'] * 0.2),
											   ($row_folha['base_inss'] * $percentual_rat),
											   ($row_folha['base_inss'] * 0.058),
											   ((($row_folha['base_inss'] * 0.2) + ($row_folha['base_inss'] * $percentual_rat) + ($row_folha['base_inss'] * 0.058) + ($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - $row_folha['total_familia']),
											   $row_folha['base_irrf'],
											   $row_folha['base_fgts'],
											   $row_folha['base_fgts_ferias'],
											   $row_folha['base_fgts'] + $row_folha['base_fgts_ferias'],
											   $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'],
											   $row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias'],
											   $row_folha_participante['ddir'],
											   $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias']);
										 
		} // Fim do Loop de 12 meses
					 
	break;
	
	
	
	
	// Folha de Cooperado
	case 3:
	
		$totalizadores_nome = array('PARTICIPANTES', 'BASE', 'RENDIMENTOS', 'DESCONTOS', 'INSS', 'IRRF', 'QUOTA', 'AJUDA DE CUSTO', 'L&Iacute;QUIDO');
		
		// Rodando os 12 meses
		foreach($meses as $mes => $nome_mes) {
			
			$tubarao = sprintf('%02d', $mes);

			$qr_folha  = mysql_query("SELECT SUM(participantes) AS total_participantes, SUM(total_liqui) AS total_liquido, SUM(total_bruto) AS total_bruto, SUM(rendimentos) AS rendimentos, SUM(descontos) AS descontos FROM folhas WHERE mes = '$tubarao' AND regiao != '36' AND year(data_inicio) = '$ano_base' AND contratacao = '3' AND terceiro != '1' AND status = '3'");
			$row_folha = mysql_fetch_assoc($qr_folha);
			
			$qr_folha_participante  = mysql_query("SELECT SUM(inss) AS inss, SUM(irrf) AS irrf, SUM(quota) AS quota, SUM(ajuda_custo) AS ajuda_custo FROM folha_cooperado WHERE status IN(3,4) AND mes = '$tubarao' AND regiao != '36' AND ano = '$ano_base' AND terceiro != '1'");
			$row_folha_participante = mysql_fetch_assoc($qr_folha_participante); 
			
			$totalizadores_valor[$mes] = array($row_folha['total_participantes'], $row_folha['total_bruto'], $row_folha['rendimentos'], $row_folha['descontos'], $row_folha_participante['inss'], $row_folha_participante['irrf'], $row_folha_participante['quota'], $row_folha_participante['ajuda_custo'], $row_folha['total_liquido']);
			
		} // Fim do Loop de 12 meses

	break;
	
	
	
	
	// Folha de Autônomo PJ
	case 4:
	
		$totalizadores_nome = array('PARTICIPANTES', 'BASE', 'RENDIMENTOS', 'DESCONTOS', 'INSS', 'IRRF', 'QUOTA', 'AJUDA DE CUSTO', 'L&Iacute;QUIDO');
		
		// Rodando os 12 meses
		foreach($meses as $mes => $nome_mes) {
			
			$tubarao = sprintf('%02d', $mes);

			$qr_folha  = mysql_query("SELECT SUM(participantes) AS total_participantes, SUM(total_liqui) AS total_liquido, SUM(total_bruto) AS total_bruto, SUM(rendimentos) AS rendimentos, SUM(descontos) AS descontos FROM folhas WHERE mes = '$tubarao' AND regiao != '36' AND year(data_inicio) = '$ano_base' AND contratacao = '4' AND terceiro != '1' AND status = '3'");
			$row_folha = mysql_fetch_assoc($qr_folha);
			
			$qr_folha_participante  = mysql_query("SELECT SUM(inss) AS inss, SUM(irrf) AS irrf, SUM(quota) AS quota, SUM(ajuda_custo) AS ajuda_custo FROM folha_cooperado WHERE status IN(3,4) AND mes = '$tubarao' AND regiao != '36' AND ano = '$ano_base' AND terceiro != '1'");
			$row_folha_participante = mysql_fetch_assoc($qr_folha_participante);
			
			$totalizadores_valor[$mes] = array($row_folha['total_participantes'], $row_folha['total_bruto'], $row_folha['rendimentos'], $row_folha['descontos'], $row_folha_participante['inss'], $row_folha_participante['irrf'], $row_folha_participante['quota'], $row_folha_participante['ajuda_custo'], $row_folha['total_liquido']);
			
		} // Fim do Loop de 12 meses
		
	break;
}
?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>Consolida&ccedil;&atilde;o de Folha</title>
<link href="css/estrutura.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="corpo" <?php if($tela == 3) { echo 'style="width:1500px;'; } ?>>

<?php switch($tela) {
	  case 1:
?>
		 <div style="float:right;">
         	<?php include('../reportar_erro.php'); ?>
         
          </div>
         <div style="clear:right;">
         </div>
         <div id="topo">
              <?php include('include/topo.php'); ?>
         </div>
         <div id="conteudo">
              <h1 style="margin:70px;"><span>RELATÓRIOS</span> CONSOLIDA&Ccedil;&Atilde;O DE FOLHA</h1>
              <form style="margin-bottom:190px;">
                  Selecione um Tipo de Contratação:
                  <select onChange="location.href=this.value;" name="tipo_contratacao">
                      <option disabled="disabled" selected>Selecione um tipo</option>
                      <option value="<?=$_SERVER['PHP_SELF']?>?tipo_contratacao=1&tela=2">Autônomo</option>
                      <option value="<?=$_SERVER['PHP_SELF']?>?tipo_contratacao=2&tela=2">CLT</option>
                      <option value="<?=$_SERVER['PHP_SELF']?>?tipo_contratacao=3&tela=2">Colaborador</option>
                      <option value="<?=$_SERVER['PHP_SELF']?>?tipo_contratacao=4&tela=2">Autônomo / PJ</option>
                  </select>
              </form>
		 </div>
         <div id="rodape">
         	  <?php include('include/rodape.php'); ?>
         </div>
     
<?php break;
	  case 2:
?>

         <div id="topo">
              <?php include('include/topo.php'); ?>
         </div>
         <div id="conteudo">
              <h1 style="margin:70px;"><span>RELATÓRIOS</span> CONSOLIDA&Ccedil;&Atilde;O DE FOLHA</h1>
              <form style="margin-bottom:190px;">
                  Selecione o Ano Base:
                  <select onChange="location.href=this.value;" name="ano">
                      <option disabled="disabled" selected>Selecione um ano</option>
                  	  <?php for($ano=2009; $ano<=date('Y'); $ano++) { ?>
                      <option value="<?=$_SERVER['PHP_SELF']?>?tipo_contratacao=<?=$tipo_contratacao?>&ano=<?=$ano?>&tela=3"><?php echo $ano; ?></option>
                      <?php } ?>
                  </select>
              </form>
		 </div>
         <div id="rodape">
         <?php include('include/rodape.php'); ?>
         </div>

<?php break;
      case 3:
?>

<table cellspacing="0" cellpadding="0" class="relacao" style="width:100%; border:0; page-break-after:always;">
  <tr> 
    <td width="20%" align="center">
        <img src='../imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' />
    </td>
    <td width="80%" align="center" colspan="2">
        <strong>CONSOLIDA&Ccedil;&Atilde;O DE FOLHA DE PAGAMENTO DE <?php echo $tipos[$tipo_contratacao].' '.$ano_base?></strong>
    </td>
  </tr>
  <tr> 
    <td colspan="3">
    
    <table cellspacing="2" cellpadding="3" class="relacao" style="font-weight:normal; line-height:22px; margin-top:30px; border:0; width:100%;">
      <tr class="secao">       
        <td width="13%">Descri&ccedil;&atilde;o</td>
        <td width="87%" colspan="13">
          
          <table cellpadding="0" cellspacing="0" width="100%" class="relacao" style="border:0px;">
            <tr>
              <?php foreach($meses as $mes => $nome_mes) { ?>
                <td style="width:7%" align="center"><?php echo substr($nome_mes,0,3); ?></td>
              <?php } ?>
              <td style="width:16%" align="center">Total</td>
            </tr>
          </table>
          
        </td>
      </tr>
      
      <?php foreach($totalizadores_nome as $chave => $descricao) { ?>
						  
              <tr class="<?php if($alternateColor++%2==0) { echo 'linha_um'; } else { echo 'linha_dois'; } ?>">   
                 <td width="13%"><?php echo $descricao;?></td>
                 <td width="87%" colspan="13">
                  
                        <table cellpadding="0" cellspacing="0" style="border:0; width:100%; font-size:13px;">
                            <tr>
                                <?php foreach($meses as $mes => $nome_mes) {
									      $valor = $totalizadores_valor[$mes][$chave];
										  $totalizador_descricao += $valor;
										  if($chave != 0) { 
											  $totalizador_mes[$mes] += $valor;
										  } ?>
                                    <td style="width:7%" align="center"><?php if($chave == 0) { echo (int)$valor; } else { echo formato_real($valor); } ?></td>
                                <?php } ?>
                                    <td style="width:16%" align="center"><?php if($chave == 0) { echo (int)$totalizador_descricao; } else { echo formato_real($totalizador_descricao); } unset($totalizador_descricao); ?></td>
                             </tr>
                        </table>
                        
                 </td>         
              </tr>
						  
		<?php } /* ?>
                    
		<tr>
          <td width="13%" style="font-weight:bold; text-align:right;">Totais:</td>
           <td width="87%" colspan="13">
          
                <table cellpadding="0" cellspacing="0" style="border:0; width:100%; font-size:13px;">
                    <tr>
                        <?php foreach($totalizador_mes as $total_mes) { ?>
                            <td style="width:7%" align="center"><?php  echo formato_real($total_mes); ?></td>
                        <?php } ?>
                            <td style="width:16%" align="center"><?php echo formato_real(array_sum($totalizador_mes)); ?></td>
                     </tr>
                </table>
                
         </td>         
        </tr>
		*/ ?>
        
     </table>
 
	</td>
  </tr>
</table>
    
<?php break; } ?>

</div>
</body>
</html>