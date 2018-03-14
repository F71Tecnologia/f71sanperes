<?php include('sintetica/cabecalho_folha.php'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de Aut&ocirc;nomo (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../favicon.ico" rel="shortcut icon">
<link href="../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.allowSizeReduction = false;
</script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/formatavalor.js"></script>
<script type="text/javascript" src="sintetica/scripts.js"></script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
</style>
</head>
<body>
<div id="corpo">
    <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../imagens/logomaster<?=mysql_result($qr_projeto, 0, 2)?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.htmlentities($mes_folha, ENT_COMPAT, 'utf-8').')'?></b>
        </td>
        <td></td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
        <td width="20%"> <?php include('../reportar_erro.php'); ?></td>
          <td></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
    
    <table cellpadding="0" cellspacing="1" class="folha">
        <tr>
          <td colspan="2"><a href="<?=$link_voltar?>" class="voltar">Voltar</a></td>
          <td colspan="8" style="color:#C30; font-style:italic; font-size:12px; text-align:right; font-weight:bold;">Para editar FALTAS, RENDIMENTOS ou DESCONTOS clique sobre o valor</td>
        </tr>
        <tr>
          <td colspan="10">
                
                <table cellspacing="0" cellpadding="0" width="100%">
                 <tr class="secao">
                  <td width="4%">COD</td>
                  <td width="24%" align="left" style="padding-left:5px;">NOME</td>
                  <td width="8%">SALÁRIO</td>
                  <td width="8%">DIÁRIA</td>
                  <td width="8%"><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></td>
                  <td width="8%">FALTAS</td>
                  <td width="10%">BASE</td>
                  <td width="10%">RENDIMENTOS</td>
                  <td width="10%">DESCONTOS</td>
                  <td width="10%">L&Iacute;QUIDO</td>
                 </tr>
                </table>
                
          </td>
        </tr>
        
   
<?php // Início do Loop dos Participantes da Folha
	  while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  // Id do Participante
		  $autonomo     = $row_participante['id_autonomo'];
		  $ids_update[] = $row_participante['id_folha_pro'];
		  
		  // Link para Relatório
		  $relatorio = str_replace('+', '--', encrypt("$autonomo&$folha"));
		  
		  // Calculando a Folha
		  include('sintetica/calculos_folha.php'); ?>
		 
		 <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
			<td width="4%"><?php echo $autonomo; ?></td>
			<td width="24%" align="left"><a href="sintetica/relatorio.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Ver relatório de <?=$row_participante['nome']?>"><?php echo abreviacao($row_participante['nome'], 4, 1); ?> <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif"></a></td>
            <td width="8%"><?php echo formato_real($salario_limpo); ?></td>
            <td width="8%"><?php echo formato_real($diaria); ?></td>
			<td width="8%"><?php if(!empty($decimo_terceiro)) { echo $meses; } else { echo $dias; } ?></td>
            <td width="8%" title="Clique aqui para editar o número de faltas"><input type="text" name="faltas" value="<?=(int)$faltas?>" class="valor_update"></td>
			<td width="10%" class="base_ajax"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
			<td width="10%" class="rendimentos_ajax" title="Clique aqui para editar o valor de rendimento"><input type="text" name="rendimentos" value="<?=formato_real($rendimentos)?>" class="valor_update" onKeyDown="FormataValor(this,event,17,2)"></td>
			<td width="10%" class="descontos_ajax" title="Clique aqui para editar o valor de desconto"><input type="text" name="descontos" value="<?=formato_real($descontos)?>" class="valor_update" onKeyDown="FormataValor(this,event,17,2)"></td>
			<td width="10%" class="liquido_ajax"><?=formato_real(abs($liquido))?></td>
            <input type="hidden" name="id_folha_participante" class="id_folha_participante" value="<?=$row_participante['id_folha_pro']?>">
		 </tr>
	
		<?php include('sintetica/totalizadores_resets.php');
	
			// Fim do Loop de Participantes
			} ?>
			
		<tr class="totais">
		  <td colspan="5"><?php if($total_participantes > 10) { ?><a href="#corpo" class="ancora">Subir ao topo</a><?php } ?></td>
		  <td>TOTAIS:</td>
		  <td class="base_total"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_total); } else { echo formato_real($salario_total); } ?></td>
		  <td class="rendimentos_total"><?=formato_real($rendimentos_total)?></td>
		  <td class="descontos_total"><?=formato_real($descontos_total)?></td>
		  <td class="liquido_total"><?=formato_real($liquido_total)?></td>
		</tr>
	</table> 
<?php include('sintetica/estatisticas_folha.php'); ?>
</div>
</body>
</html>