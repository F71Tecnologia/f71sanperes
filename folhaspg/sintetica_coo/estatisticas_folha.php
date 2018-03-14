<div id="estatisticas">

       <?php
	  
	    // Totalizadores
		     $totalizadores_nome   = array('BASE', 'RENDIMENTOS', 'DESCONTOS', 'INSS', 'IRRF', 'QUOTA', 'AJUDA DE CUSTO', 'L&Iacute;QUIDO', 'NOTA FISCAL');
		     $totalizadores_valor  = array($salario_base_total, $rendimentos_total, $descontos_total, $inss_total, $irrf_total, $valor_quota_total, $ajuda_custo_total, $liquido_total, $nota_fiscal_total); 		     $totalizadores_classe = array('totalizador_base','totalizador_rendimentos','totalizador_descontos','totalizador_inss','totalizador_irrf','totalizador_quota','totalizador_ajuda_custo','totalizador_liquido','totalizador_nota_fiscal')
			 
			 ?>
        
        <div id="totalizadores">
          <table cellspacing="1">
            <tr>
              <td class="secao_pai" colspan="2">Totalizadores</td>
            </tr>
            <tr class="linha_um">
              <td class="secao">PARTICIPANTES:</td>
              <td class="valor"><?=$total_participantes?></td>
            </tr>
            <?php foreach($totalizadores_valor as $chave => $valor) { 
			
			if($totalizadores_nome[$chave] == 'NOTA FISCAL' and !$ACOES->verifica_permissoes(71)) continue;
			?>
  		    <tr class="linha_<?php if($linha2++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
              <td class="secao"><?=$totalizadores_nome[$chave]?>:</td>
              <td class="valor"><span class="<?php echo $totalizadores_classe[$chave]?>"><?=formato_real($valor)?></span></td>
            </tr>
            <?php }  ?>
          </table>
          
          <div id="botoes">
            <a href="<?=$link_add_remove?>" class="add_remove"></a>
            <?php if(in_array($_COOKIE['logado'], $acesso_finalizacao)) { ?>
            	<a href="<?=$link_finaliza?>" class="finaliza"></a>
            <?php } ?>
          </div>
        
        </div>

</div>
<div class="clear"></div>