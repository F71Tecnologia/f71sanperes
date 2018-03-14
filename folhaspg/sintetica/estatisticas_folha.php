<div id="estatisticas">

       <?php // Totalizadores
		     $totalizadores_nome   = array('BASE', 'RENDIMENTOS', 'DESCONTOS', 'L&Iacute;QUIDO');
		     $totalizadores_valor  = array($salario_total, $rendimentos_total, $descontos_total, $liquido_total);
			 $totalizadores_classe = array('base_total', 'rendimentos_total', 'descontos_total', 'liquido_total'); ?>
        
        <div id="totalizadores">
          <table cellspacing="1">
            <tr>
              <td class="secao_pai" colspan="2">Totalizadores</td>
            </tr>
            <tr class="linha_um">
              <td class="secao">PARTICIPANTES:</td>
              <td class="valor"><?=$total_participantes?></td>
            </tr>
            <?php foreach($totalizadores_valor as $chave => $valor) { ?>
  		    <tr class="linha_<?php if($linha2++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
              <td class="secao"><?=$totalizadores_nome[$chave]?>:</td>
              <td class="valor <?=$totalizadores_classe[$chave]?>"><?=formato_real($valor)?></td>
            </tr>
            <?php } ?>
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