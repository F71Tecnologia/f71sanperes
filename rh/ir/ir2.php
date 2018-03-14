<?php
include('../../conn.php');
include('../../classes/funcionario.php');

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;

$mes = $_GET['mes'];
$ano = $_GET['ano'];
$tipo_contratacao = $_GET['tipo'];

mysql_query("INSERT INTO ir (folha,regiao,tipo_contratacao,autor,data) VALUES ('$folha','$regiao','$tipo_contratacao','$_COOKIE[logado]',NOW())");

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa    = mysql_fetch_assoc($qr_empresa);

$meses = array('ERRO','01' => 'Janeiro','02' => 'Fevereiro','03' => 'Março','04' => 'Abril','05' => 'Maio','06' => 'Junho','07' => 'Julho','08' => 'Agosto','09' => 'Setembro','10' => 'Outubro','11' => 'Novembro','12' => 'Dezembro');
?>
<html>
<head>
<title>Rela&ccedil;&atilde;o de IRRF</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
<link href="../folha/sintetica/folha.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="corpo">
	<table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" valign="middle" align="center">
            <img src="../../imagens/logomaster<?=$Master?>.gif" width="110" height="79">
        </td>
        <td style="font-size:12px;">
        	<b>Relação de IRRF:</b> <?php echo $meses[$mes].' / '.$ano; ?>
        </td>
      </tr>
    </table>

	<table cellpadding="0" cellspacing="1" id="folha">
     <tr class="secao">
        <td width="4%">COD</td>
        <td width="28%" style="padding-left:5px; text-align:left;">NOME</td>
        <td width="10%">REGIÃO</td>
        <td width="6%">CPF</td>
        <td width="4%">FILHOS</td>
        <td width="10%">INCIDÊNCIA</td>
        <td width="10%">BASE DE IRRF</td>
        <td width="10%">IRRF</td>
     </tr>
     
	<?php 
        
        $sql_empregados = "SELECT * FROM rh_folha_proc  WHERE mes = '$mes' AND ano = '$ano' AND id_regiao != '36' AND status = '3' ORDER BY nome ASC";
        
        $qr_empregados   = mysql_query($sql_empregados);
	      while($empregado = mysql_fetch_assoc($qr_empregados)) {
			  
			$total_empregados++;
			  
			$ir_folha = $empregado['a5021'] + $empregado['ir_dt'];

			$qr_filhos = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$empregado[id_clt]' AND id_regiao = '$empregado[id_regiao]' AND contratacao = '$tipo_contratacao'");
			$filhos    = mysql_fetch_assoc($qr_filhos);
			
			$qr_regiao   = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$empregado[id_regiao]'");
			$nome_regiao = mysql_result($qr_regiao,0);
			
			$numfilhos = 0;
			
			if(!empty($filhos['nome5'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome4'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome3'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome2'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome1'])) {
				$numfilhos += 1;
    		} 
			
			if(!empty($ir_folha)) { ?>

      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td><?php echo $empregado['id_clt']; ?></td>
        <td style="padding-left:5px; text-align:left;"><?php echo $empregado['nome']; ?></td>
        <td style="text-transform:uppercase;"><?php echo $nome_regiao; ?></td>
        <td><?php echo $empregado['cpf']; ?></td>
        <td><?php echo $numfilhos; ?></td>
        <td>Folha</td>
        <td>R$ <?php echo number_format($empregado['base_irrf'], 2, ',', '.'); ?></td>
        <td>R$ <?php echo number_format($ir_folha, 2, ',', '.'); ?></td>
      </tr>
  
	  <?php $somairbase += $empregado['base_irrf'];
	        $somair     += $ir_folha;
	        
			}
			
			if($empregado['ir_rescisao'] != 0.00) { ?>
		
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td><?php echo $empregado['id_clt']; ?></td>
        <td style="padding-left:5px; text-align:left;"><?php echo $empregado['nome']; ?></td>
        <td style="text-transform:uppercase;"><?php echo $nome_regiao; ?></td>
        <td><?php echo $empregado['cpf']; ?></td>
        <td><?php echo $numfilhos; ?></td>
        <td>Rescisão</td>
        <td>&nbsp;</td>
        <td>R$ <?php echo number_format($empregado['ir_rescisao'], 2, ',', '.'); ?></td>
      </tr>
		
  <?php }

 	     $somair += $empregado['ir_rescisao'];
    } 
	
	// Verifica se o empregado terá folha de férias
			
	$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE year(data_ini) = '$ano' AND mes = '$mes'");
	while($row_ferias = mysql_fetch_assoc($qr_ferias)) {
		
		    $total_empregados++;
		
		    $qr_cpf = mysql_query("SELECT cpf FROM rh_clt WHERE id_clt = '$row_ferias[id_clt]'");
		    @$cpf = mysql_result($qr_cpf,0);
		
		    $qr_regiao   = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_ferias[regiao]'");
			@$nome_regiao = mysql_result($qr_regiao,0);
			
			$qr_filhos = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$row_ferias[id_clt]' AND id_regiao = '$row_ferias[regiao]' AND contratacao = '$tipo_contratacao'");
			$filhos    = mysql_fetch_assoc($qr_filhos);
			
			$numfilhos = 0;
			
			if(!empty($filhos['nome5'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome4'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome3'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome2'])) {
				$numfilhos += 1;
			} if(!empty($filhos['nome1'])) {
				$numfilhos += 1;
    		} 
			
			if($row_ferias['ir'] != 0.00) { ?>
		
      <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td><?php echo $row_ferias['id_clt']; ?></td>
        <td style="padding-left:5px; text-align:left;"><?php echo $row_ferias['nome']; ?></td>
        <td style="text-transform:uppercase;"><?php echo $nome_regiao; ?></td>
        <td><?php echo $cpf; ?></td>
        <td><?php echo $numfilhos; ?></td>
        <td>Férias</td>
        <td>R$ <?php echo number_format($row_ferias['total_remuneracoes'] - $row_ferias['inss'], 2, ',', '.'); ?></td>
        <td>R$ <?php echo number_format($row_ferias['ir'], 2, ",", "."); ?></td>
      </tr>
		
  <?php $somairbase += ($row_ferias['total_remuneracoes'] - $row_ferias['inss']);
        $somair     += $row_ferias['ir'];
  
        } 
	} ?>

  <tr class="totais">
    <td colspan="6">
	  <?php if($total_empregados > 10) { ?>
          <a href="#corpo" class="ancora">Subir ao topo</a>
      <?php } ?>
    </td>
    <td>R$ <?php echo number_format($somairbase, 2, ',', '.'); ?></td>
    <td>R$ <?php echo number_format($somair, 2, ',', '.'); ?></td>
  </tr>
</table>
</div>
</body>
</html>