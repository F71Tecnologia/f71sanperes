<?php include('sintetica_coo/cabecalho_folha.php'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de <?php if($row_folha['contratacao'] == '3') { echo 'Cooperado'; } elseif($row_folha['contratacao'] == '4') { echo 'Aut&ocirc;nomo PJ'; } ?> (<?=$folha?>)</title>
<link href="sintetica_coo/folha.css" rel="stylesheet" type="text/css">
<link href="../favicon.ico" rel="shortcut icon">
<link href="../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/formatavalor.js"></script>
<script type="text/javascript" src="sintetica_coo/scripts.js"></script>
<script type="text/javascript">
	hs.graphicsDir = '../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.allowSizeReduction = false;
</script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
</style>
</head>
<body>
<div id="corpo">
	<table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" align="center">
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
         <td><?php include('../reportar_erro.php');?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
        <td></td>
      </tr>
    </table>
    
    <table cellpadding="0" cellspacing="1" class="folha">
        <tr>
          <td valign="bottom"><a href="<?=$link_voltar?>" class="voltar">Voltar</a></td>
          <td colspan="12" style="color:#C30; font-style:italic; font-size:11px; text-align:right; font-weight:bold; line-height:28px;">Para editar HORAS TRABALHADAS, RENDIMENTOS, DESCONTOS, INSS ou AJUDA DE CUSTO clique sobre o valor do mesmo; para ver QUOTAS clique sobre o mesmo.</td>
        </tr>
        </table>
      
                
           <table cellpadding="0" cellspacing="1" class="folha">     
                <tr class="secao">
                  <td width="5%">COD</td>
                  <td width="18%" align="left" style="padding-left:5px;">NOME</td>
                  <td width="6%" class="pequeno">VALOR/HORA</td>
                  <td width="6%">HORAS</td>
                  <td width="6%">BASE</td> 
                  <?php if($row_folha['terceiro'] >0) {?> <td width="10%"> Meses Trabalhados</td><?php   }?>
                 
                  <td width="8%" class="pequeno">RENDIMENTOS</td>
                  <td width="8%" class="pequeno">DESCONTOS</td>
                  <td width="6%">INSS</td>
                  <td width="6%">IRRF</td>
                  <td width="8%">QUOTA</td>
          		  <td width="8%" class="pequeno">AJUDA CUSTO</td>
                  <td width="12%">L&Iacute;QUIDO</td>
                  <td width="6%" class="pequeno">NOTA FISCAL</td>
                </tr>
              
        

<?php // Início do Loop dos Participantes da Folha
	  while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  // Id do Participante
		  $cooperado = $row_participante['id_autonomo'];
		  
		  // Link para Relatório
		  $relatorio = str_replace('+', '--', encrypt("$cooperado&folha&$row_participante[id_folha_pro]"));

		  // Calculando a Folha
		  include('sintetica_coo/calculos_folha.php'); ?>
          
		<tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
          <td ><?php echo $cooperado; ?></td>
          <td  align="left" style="padding-left:5px;"><?php echo abreviacao($row_cooperado['nome'],4,1); ?></td>
          <td ><?php echo formato_real($valor_hora); ?></td>
          <td ><input name="horas_trabalhadas" title="Editar Horas Trabalhadas" type="text" class="valor_update" value="<?php echo (int)$horas_trabalhadas; ?>"/></td>
          <td ><?php echo formato_real($salario_base); ?></td>
         
         
          <?php if($row_folha['terceiro'] >0) { ?> 
          <td>
          
          <?php
		  
		  list($ano_admissao,$mes_admissao,$dias_admissao) = explode('-', $row_cooperado['data_entrada']);
		  $data_admissao_segundos = mktime(0,0,0,$mes_admissao, $dia_admissao, $ano_admissao);
		  
		  $data_fim_folha = explode('/',$row_folha['data_fim_br']);
		  
		  $data_hoje_segundos = mktime(0,0,0,$data_fim_folha[1],$data_fim_folha[0],$data_fim_folha[2]);
		  
		  if($ano_admissao < $data_fim_folha[2] ){
			  
			  $inicio_ano_segundos = mktime(0,0,0,1,1,$data_fim_folha[2]);	 
			  
			  $meses_trabalhados = ( $data_hoje_segundos - $inicio_ano_segundos) / 2592000;
			  $meses_trabalhados = (int)  $meses_trabalhados ;
			  
			  $valor = ($salario_base/12)* $meses_trabalhados;
			  $totalizador_meses_trabalhados += $valor;
			  echo $meses_trabalhados.' meses - R$ '.formato_real($valor);
			  
		  
		  
		  } else {
			  
		  $meses_trabalhados = ( $data_hoje_segundos - $data_admissao_segundos) / 2592000;
		  $meses_trabalhados = (int) $meses_trabalhados ;	
			  
			  $valor = ($salario_base/12)* $meses_trabalhados;
			  $totalizador_meses_trabalhados += $valor;	  
			   echo $meses_trabalhados.' meses - R$ '.formato_real($valor);
			   
		  
		  }
		  
          ?>          
          </td>
          
          <?php }?>
          
		  <td><input name="rendimentos" title="Editar Rendimentos" type="text" class="valor_update" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo formato_real($rendimentos); ?>"/></td>
          <td><input name="descontos" title="Editar Descontos" type="text" class="valor_update" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo formato_real($descontos); ?>"/></td>
          <td><a href="sintetica_coo/edicao_inss.php?enc=<?php echo $relatorio; ?>" title="Editar INSS" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><?php echo formato_real($inss); ?></a></td>
		  <td><?php echo formato_real($irrf); ?></td>
		  <td><a href="relacao_quotas.php?id=<?php echo $cooperado; ?>" title="Visualizar Relatório de Quotas" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><?php echo formato_real($valor_quota); ?></a></td>
		  <td><input name="ajuda_custo" type="text" class="valor_update" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo formato_real($ajuda_custo); ?>" <?php if($row_cooperado['tipo_inss'] == 1) { echo 'disabled="disabled" style="color:#CCC;" title="Bloqueado pois o participante tem o INSS fixo"'; } else { echo 'title="Editar Ajuda de Custo" '; }?>/></td>
	      <td><?php echo formato_real($liquido); ?></td>
		  <td><?php echo formato_real($nota_fiscal); ?></td>
          <input type="hidden" name="id_folha_participante" class="id_folha_participante" value="<?=$row_participante['id_folha_pro']?>">
		</tr>
        
        <?php include('sintetica_coo/update_participante.php');
			  include('sintetica_coo/totalizadores_resets.php');
			  
			  // Fim do Loop de Participantes
	  	    } ?>
        
        <tr class="totais">
           <td colspan="4">
		       <?php if($total_participantes > 10) { ?>
          	      <a href="#corpo" class="ancora">Subir ao topo</a>
               <?php } ?>
               <div class="right">TOTAIS:</div>
           </td>
           <td><?php echo formato_real($salario_base_total); ?></td>
           <?php if($row_folha['terceiro'] >0) {?>  <td><?php echo formato_real($totalizador_meses_trabalhados);?>  </td> <?php } ?>
           <td><?php echo formato_real($rendimentos_total); ?></td>
           <td><?php echo formato_real($descontos_total); ?></td>
           <td><?php echo formato_real($inss_total); ?></td>
           <td><?php echo formato_real($irrf_total); ?></td>
           <td><?php echo formato_real($valor_quota_total); ?></td>
           <td><?php echo formato_real($ajuda_custo_total); ?></td>
           <td><?php echo formato_real($liquido_total); ?></td>
           <td><?php echo formato_real($nota_fiscal_total); ?></td>
        </tr>
      </table>
	  <?php include('sintetica_coo/estatisticas_folha.php'); ?>
</div>
<?php include('sintetica_coo/updates.php'); ?>
</body>
</html>