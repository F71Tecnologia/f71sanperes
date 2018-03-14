<div id="estatisticas">

	 <?php // Resumo por Movimento
		 $movimentos_codigo = array('0001',
		   							'5029',
									'5037', '5037',
									'4007', '4007',
									'5020', '5031', '5035','4007',
									'5021', '5030', '5036', '4007',
									'5022', '6005', '5019',
									'7001', '8003');
		   $movimentos_nome = array('SAL&Aacute;RIO',
			 						'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
									'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
									'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
									'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE F&Eacute;RIAS', 'INSS SOBRE RESCIS&Atilde;O',
									'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
									'SAL&Aacute;RIO FAMILIA', 'SAL&Aacute;RIO MATERNIDADE', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
									'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O');
		   $movimentos_tipo = array('CREDITO',
			 						'CREDITO',
									'CREDITO', 'DEBITO',
									'CREDITO', 'DEBITO',
									'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
									'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
									'CREDITO', 'CREDITO', 'DEBITO',
									'DEBITO', 'DEBITO');
		  $movimentos_valor = array($salario_total,
									$decimo_terceiro_total,
									$ferias_total, $ferias_desconto_total,
									$rescisao_total, $rescisao_desconto_total,
									$inss_total, $inss_dt_total, $inss_ferias_total, $inss_rescisao_total,
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



			   $qr_movimentos  = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                         AND id_mov NOT IN(62) AND status = 1
											 GROUP BY id_mov") or die(mysql_error());
                           
//                           if($_COOKIE['logado'] == 179){
//                               print_r("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
//			   								 FROM `rh_movimentos_clt`
//											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
//                                                                                         AND id_mov NOT IN(62) AND status = 1
//											 GROUP BY id_mov");
//                           }
                           
			   while($movimento = mysql_fetch_array($qr_movimentos)) {
                                    
                                $chave++;
                                $movimentos_listados[] = $movimento['id_mov'];
                                $movimentos_codigo[]   = $movimento['cod_movimento'];
                                $movimentos_nome[]     = $movimento['nome_movimento'];
                                $movimentos_tipo[]     = $movimento['tipo_movimento'];  			  
                                $movimentos_valor[$chave]  = $movimento['total'];
				   
			   }
			   
			   unset($chave);
			
			   
			   // Organizado as Arrays pelo CÃ³digo
			   array_multisort($movimentos_codigo, $movimentos_nome, $movimentos_tipo, $movimentos_valor);

		   } ?>
           
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
          <?php foreach($movimentos_valor as $chave => $valor) { ?>
			  
                <?php if(!empty($valor)) { ?>
                    <tr class="linha_<?php if($linha3++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                    <td><?=$movimentos_codigo[$chave]?></td>
                    <td class="movimento"><?=$movimentos_nome[$chave]?></td>
                        <?php if($movimentos_tipo[$chave] == 'CREDITO') { 
                            if(!in_array($movimentos_codigo[$chave],$movimentos_informativos)) { $movimentos_credito += $valor;  } ?>
                            <td<?php if(in_array($movimentos_codigo[$chave],$movimentos_informativos)) { ?> style="color:#999;"<?php } ?>><?=formato_real($valor)?></td>
                            <td>&nbsp;</td>   
                        <?php } elseif($movimentos_tipo[$chave] == 'DEBITO' or $movimentos_tipo[$chave] == 'DESCONTO') {
                            if(!in_array($movimentos_codigo[$chave],$movimentos_informativos)) { $movimentos_debito += $valor; } ?>
                            <td>&nbsp;</td>
                            <td<?php if(in_array($movimentos_codigo[$chave],$movimentos_informativos)) { ?> style="color:#999;"<?php } ?>><?=formato_real($valor)?></td>       
                        <?php } ?>

                    </tr>
                <?php } ?>
          <?php } ?>
                
          <tr class="totais">
              <td colspan="2" align="right">TOTAIS:</td>
              <td><?=formato_real($movimentos_credito)?></td>
              <td><?=formato_real($movimentos_debito)?></td>
          </tr>
          <tr class="totais">
              <td colspan="2" align="right">L&Iacute;QUIDO:</td>
              <td><?=formato_real($movimentos_credito-$movimentos_debito)?></td>
              <td>&nbsp;</td>
          </tr>
        </table>
        
        <div id="botoes">
            <?php //if($_COOKIE['logado'] == 179){ ?>
                <!--<a href="javascript:;" data-link="<?=$link_finaliza?>" class="finaliza valida_saldo_devedor"></a>-->     
            <?php //} ?>  
                
            <a href="<?=$link_add_remove?>" class="add_remove"></a>
            
            <?php  if($_REQUEST['btn'] !=1) { ?>
                <a href="sintetica_analitica.php?enc=<?= $_REQUEST['enc'].'&btn=1'?>" class="folha_analitica"></a>
            <?php }  ?>
            
            <?php
            if($qtd_cltFaltando == 0){
                if(in_array("86", $permissoesFolha)){ ?>
                    <a href="javascript:;" data-link="<?=$link_finaliza?>" class="finaliza valida_saldo_devedor"></a>
                <?php }
            }
            ?>
            
        </div>                        
        </div>        
            
            <?php
            if($qtd_cltFaltando == 1){
                $msgTrv = "Existe {$qtd_cltFaltando} CLT cadastrado após a abertura da folha, que não entrou";
            }elseif($qtd_cltFaltando > 1){
                $msgTrv = "Existem {$qtd_cltFaltando} CLTS cadastrados após a abertura da folha, que não entraram";
            } ?>
        
       <?php 
	   
	   ////'INSS N&Atilde;O DESCONTADO',
	   ///$total_base_inss_nao_descontado,
	   
	   // Totalizadores
		     $totalizadores_nome  = array('L&Iacute;QUIDO', 'BASE DE INSS(FUNCION&Aacute;RIOS SEM DESCONTO)', 'BASE DE INSS',  'INSS', 'INSS DE F&Eacute;RIAS', 'TOTAL INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)'/*, 'INSS (RECOLHER)'*/, 'BASE DE IRRF', 'IRRF', 'DDIR', 'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS', 'BASE DE FGTS TOTAL', 'FGTS' /*, 'FGTS'*/);
		     $totalizadores_valor = array($liquido_total, $total_base_inss_nao_descontado, $base_inss_total, $inss_completo_total, $inss_ferias_total, ($inss_completo_total + $inss_ferias_total), $base_inss_empresa, $base_inss_rat, $base_inss_terceiros, /*(($base_inss_empresa + $base_inss_rat + $base_inss_terceiros + $inss_completo_total) - $familia_total),*/ $base_irrf_total, $irrf_completo_total, $ddir_total, $base_fgts_total, $base_fgts_ferias_total, ($base_fgts_total + $base_fgts_ferias_total),
                          ($base_fgts_total + $base_fgts_ferias_total) *0.08/*, $fgts_completo_total*/); ?>
        
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
              <td class="valor"><?=formato_real($valor)?></td>
            </tr>
            <?php } ?>
          </table>
        
        </div>

</div>

       <?php
       if(sizeof($ids_movimentos_estatisticas) >0){
            $qr_movimentos_faltas  = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   								 FROM `rh_movimentos_clt`                                                                                         
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                          AND id_mov  = 62
											 GROUP BY id_mov") or die(mysql_error());
        if(mysql_num_rows($qr_movimentos_faltas) != 0){
            ?>
           <table cellspacing="1" width="300">
              <tr>
                <td colspan="2" class="secao_pai">Total de faltas</td>
              </tr>
              <tr class="secao">
                <td width="9%">COD</td>
                <td width="9%">TOTAL</td>
              </tr>
                  
            <?php
            while($row_mov2 = mysql_fetch_assoc($qr_movimentos_faltas)):
              ?>  
                  <tr class="linha_dois">
                  <td  align="right"  style="font-size:12px;"><?php echo $row_mov2['cod_movimento'];?></td>
                  <td  align="right" style="font-size:12px;" > <?php echo number_format($row_mov2['total'],2,',','.');?></td>                
              </tr>
              
           <?php     
            endwhile;
           ?>
           </table>
          <p style="font-style:italic; text-align: left; font-size: 10px; color: #ff6666; margin-left: 70px;">*As faltas são abatidas no salário base.</p>
          
          
          <?php } 
       }?>
          

<div class="clear"></div>

<!--MENSAGEM PARA CLTS CADASTRADOS APÓS FOLHA ABERTA-->
<?php if($qtd_cltFaltando > 0){ ?>
<div id='msg_red'>
    <p><?php echo $msgTrv; ?></p>
    <ul>
        <?php while($res_cltAusente = mysql_fetch_assoc($sql_cltFin)){ ?>
        <li><?php echo $res_cltAusente['id_clt'] . " - " . $res_cltAusente['nome']; ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>