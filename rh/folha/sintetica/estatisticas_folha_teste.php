<div id="estatisticas">

	 <?php 
	 

	 // Resumo por Movimento
		 $movimentos_codigo = array('0001',
		   							'5029',
									'5037', '5037',
									'4007', '4007',
									'5020', '5031', '4007',
									'5021', '5030', '5036', '4007',
									'5022', '6005', '5019',
									'7001', '8003', );
		   $movimentos_nome = array('SAL&Aacute;RIO',
			 						'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
									'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
									'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
									'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE RESCIS&Atilde;O',
									'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
									'SAL&Aacute;RIO FAMILIA', 'SAL&Aacute;RIO MATERNIDADE', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
									'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O');
									
									
									
			$movimentos_class   =	array(
									'SAL&Aacute;RIO'						=> 'total_base',
			 						'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO' => 'total_decimo_terceiro',
									'F&Eacute;RIAS' 						=> 'total_ferias',
								    'VALOR PAGO NAS F&Eacute;RIAS' 			=> 'total_desconto_ferias',
									'RESCIS&Atilde;O'						=> 'total_rescisao',
									'VALOR PAGO NA RESCIS&Atilde;O' 		=> 'total_desconto_rescisao',
									'INSS' 									=> 'total_inss_completo',
									'INSS SOBRE D&Eacute;CIMO TERCEIRO'  	=> 'total_inss_dt', 
									'INSS SOBRE RESCIS&Atilde;O'  			=> 'total_inss_rescisao',
									'IRRF' 									=> 'total_irrf_completo',
									'IRRF SOBRE D&Eacute;CIMO TERCEIRO' 	=> 'total_irrf_dt',
									'IRRF SOBRE F&Eacute;RIAS' 				=> 'total_irrf_ferias',
									'IRRF SOBRE RESCIS&Atilde;O'			=> 'total_irrf_rescisao',
									'SAL&Aacute;RIO FAMILIA'    			=> 'total_familia',
									'SAL&Aacute;RIO MATERNIDADE'			=> 'total_salario_maternidade',
									'CONTRIBUI&Ccedil;&Atilde;O SINDICAL' 	=> 'total_sindicato',
									'DESCONTO VALE TRANSPORTE'  			=> 'total_vale_transporte',
									'DESCONTO VALE REFEI&Ccedil;&Atilde;O'  => 'total_vale_refeicao',
																			  
									);					
									
						
							
		   $movimentos_tipo = array('CREDITO',
			 						'CREDITO',
									'CREDITO', 'DEBITO',
									'CREDITO', 'DEBITO',
									'DEBITO', 'DEBITO', 'DEBITO',
									'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
									'CREDITO', 'CREDITO', 'DEBITO',
									'DEBITO', 'DEBITO');
									
		  $movimentos_valor = array($salario_total,
									$decimo_terceiro_total,
									$ferias_total, $ferias_desconto_total,
									$rescisao_total, $rescisao_desconto_total,
									$inss_total, $inss_dt_total, $inss_rescisao_total,
									$irrf_total, $irrf_dt_total, $irrf_ferias_total, $irrf_rescisao_total,
									$familia_total, $maternidade_total, $sindicato_total,
									$vale_transporte_total, $vale_refeicao_total);
		   
		   
		   
		  
		   // Criando Array de Movimentos Informativos
		   $movimentos_informativos = array('8000');
			   
			  
		   // Adicionando Mais Movimentos
		   if(!empty($ids_movimentos_estatisticas)) {
				
			   $chave = '18';
			   $ids_movimentos_estatisticas = implode(',', $ids_movimentos_estatisticas);
			   settype($movimentos_listados, 'array');

			   $qr_movimentos  = mysql_query("SELECT * FROM rh_movimentos_clt 
											  WHERE id_movimento IN($ids_movimentos_estatisticas) 
											  ORDER BY cod_movimento ASC");
			   while($movimento = mysql_fetch_array($qr_movimentos)) {
					
						
				   if(!in_array($movimento['id_mov'], $movimentos_listados)) {
					   $chave++;
					   $movimentos_listados[] = $movimento['id_mov'];
					   $movimentos_codigo[]   = $movimento['cod_movimento'];
					   $movimentos_nome[]     = $movimento['nome_movimento'];
					   $movimentos_tipo[]     = $movimento['tipo_movimento'];
				   }
				   
				   
				   $ids_movimentos_estatistica_xml[$movimento['cod_movimento']] =  $movimento['id_movimento'];
				   
				   $movimentos_valor[$chave] += $movimento['valor_movimento'];
				   
			   }
			   
			   
			
			   unset($chave);
			   
			   // Organizado as Arrays pelo Código
			   array_multisort($movimentos_codigo, $movimentos_nome, $movimentos_tipo, $movimentos_valor);

		   } 
		   
		   
		   
		   ?>
           
        <div id="resumo">
        
        <table cellspacing="1">
          <tr>
            <td colspan="4" class="secao_pai">Resumo por Movimento</td>
          </tr>
          <tr class="secao">
            <td width="9%">COD</td>
            <td width="53%" class="movimento">MOVIMENTO</td>
            <td width="19%">RENDIMENTO</td>
            <td width="19%">DESCONTO</td>
          </tr>
          <?php foreach($movimentos_valor as $chave => $valor) {
			    	if(!empty($valor)) { ?>
          
          <tr class="linha_<?php if($linha3++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            <td><?=$movimentos_codigo[$chave]?></td>
            <td class="movimento"><?=$movimentos_nome[$chave]?></td>
            
            <?php if($movimentos_tipo[$chave] == 'CREDITO') { 
			      if(!in_array($movimentos_codigo[$chave],$movimentos_informativos)) {
			         $movimentos_credito += $valor;
				  } ?>
                <td<?php if(in_array($movimentos_codigo[$chave],$movimentos_informativos)) { ?> style="color:#999;"<?php } ?> class="<?php echo $movimentos_class[$movimentos_nome[$chave]]?>"><?=formato_real($valor)?></td>
                <td></td>   
            <?php 
			
			} elseif($movimentos_tipo[$chave] == 'DEBITO') {
					if(!in_array($movimentos_codigo[$chave],$movimentos_informativos)) {
			           $movimentos_debito += $valor;
					} ?>
                <td></td>
                <td<?php if(in_array($movimentos_codigo[$chave],$movimentos_informativos)) { ?> style="color:#999;"<?php } ?> class="<?php echo $movimentos_class[$movimentos_nome[$chave]]?>" ><?=formato_real($valor)?></td>       
            <?php } ?>
            
          </tr>
          
          <?php } } ?>
          <tr class="totais">
          	  <td colspan="2" align="right">TOTAIS:</td>
              <td class="total_rendimentos_mov"><?=formato_real($movimentos_credito)?></td>
              <td class="total_desconto_mov"><?=formato_real($movimentos_debito)?></td>
          </tr>
          <tr class="totais">
              <td colspan="2" align="right">L&Iacute;QUIDO:</td>
              <td class="total_liquido_mov"><?=formato_real($movimentos_credito-$movimentos_debito)?></td>
              <td>&nbsp;</td>
          </tr>
        </table>
        
        <div id="botoes">
            <a href="<?=$link_add_remove?>" class="add_remove"></a>
            
            <?php  if($_REQUEST['btn'] !=1) {
				?>
            
            	<a href="sintetica_analitica.php?enc=<?= $_REQUEST['enc'].'&btn=1'?>" class="folha_analitica"></a>
            
            <?php }  ?>
            <?php if(in_array($_COOKIE['logado'], $acesso_finalizacao)) { ?>
            	<a href="finaliza_folha_horista.php?enc=<?php echo str_replace('+', '--', encrypt("$regiao&$folha"))?>" class="finaliza"></a>
            <?php } ?>
        </div>

      </div>
        
       <?php // Totalizadores
		     $totalizadores_nome  = array('L&Iacute;QUIDO', 'BASE DE INSS', 'INSS', 'INSS DE F&Eacute;RIAS', 'TOTAL INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)'/*, 'INSS (RECOLHER)'*/, 'BASE DE IRRF', 'IRRF', 'DDIR', 'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS', 'BASE DE FGTS TOTAL'/*, 'FGTS'*/);
			 
			 $totalizadores_class = array('total_liquido', 'total_base_inss','total_inss_completo', 'total_inss_ferias', 'total_inss', 'total_base_inss_empresa', 'total_base_inss_rat', 'total_base_inss_terceiros', 'total_base_irrf','total_irrf_completo', 'total_ddir', 'total_base_fgts', 'total_base_fgts_ferias', 'total_base_fgts_completo' );
			 
		     $totalizadores_valor = array($liquido_total, $base_inss_total, $inss_completo_total, $inss_ferias_total, ($inss_completo_total + $inss_ferias_total), $base_inss_empresa, $base_inss_rat, $base_inss_terceiros, /*(($base_inss_empresa + $base_inss_rat + $base_inss_terceiros + $inss_completo_total) - $familia_total),*/ $base_irrf_total, $irrf_completo_total, $ddir_total, $base_fgts_total, $base_fgts_ferias_total, ($base_fgts_total + $base_fgts_ferias_total)/*, $fgts_completo_total*/); ?>
        
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
              <td class="valor"><span class="<?php echo $totalizadores_class[$chave];?>"><?=formato_real($valor)?></span></td>
            </tr>
            <?php } ?>
          </table>
        
        </div>

</div>
<div class="clear"></div>