<?php include('sintetica/cabecalho_folha.php'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
</script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
	
	
		
	.rendimentos{
		background-color:  #033;	
		}
		
	.tabela tr{
			font-size:10px;
		
		
		}	
		#folha .sem_borda td {
			
			border:0;
			}
</style>

<style type="text/css" media="print">
    
    body{
        font-size: 8px;
    }
    table{
        border-collapse: collapse;
       border: 1px solid   #ccccff;
        font-size: 8px;
    }
   table tr{
        
       border: 1px solid   #ccccff; font-size: 8px;
       
    }  
    
     table td{
        
       border: 1px solid   #ccccff; font-size: 8px;
    }  
    
    table.sem_borda{
        border: 0;
    }
table.sem_borda tr{
        border: 0;
    }
table.sem_borda td{
        border: 0;
        text-align: left;
    }

    
    
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
        <td><b>Gerado por:</b> <?=@abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
     
    	<table cellpadding="0" cellspacing="1" id="folha" class="tabela">
            <tr>
              <td colspan="2">
                <a href="<?=$link_voltar?>" class="voltar">Voltar</a>
              </td>
              <td colspan="8">
              <?php if(empty($decimo_terceiro)) { ?>
                <div style="float:right;">
                    <div class="legenda"><div class="nota entrada"></div>Admissão</div>
                    <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                    <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                    <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                    <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                </div>
              <?php } ?>
              </td>
            </tr>
            <tr>
              <td colspan="10">
              		
                    <table cellspacing="1" cellpadding="1" width="100%">
                     <tr class="secao">
                      <td width="3%">COD</td>
                      <td width="18%" align="left" style="padding-left:5px;">NOME</td>
                   
                      <td width="5%">BASE</td>
                      
                      <td width="6%" class="salario">SAL&Aacute;RIO CONTRATUAL</td>
                      <td  width="8%" class="salario">VALOR/DIA</td>
                      
                     
                      
                      <td  width="25%">RENDIMENTOS</td>
                      <td width="25%">DESCONTOS</td>
                      
                    <!--  <td width="8%">INSS</td>
                      <td width="8%">IRRF</td>
                      <td width="8%">FAM&Iacute;LIA</td>-->
                      <td width="10%">L&Iacute;QUIDO</td>
                     </tr>
                    
            
       
<?php // Início do Loop dos Participantes da Folha
	  while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  // Id do Participante
		  $clt = $row_participante['id_clt'];
		  
		  // Link para Relatório
		  $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));

		  // Calculando a Folha
		  include('sintetica/calculos_folha.php'); 
		  
			// Rendimentos e Descontos
			settype($rendimentos_listados, 'array');
			settype($rendimentos_nome, 'array');
			settype($rendimentos_valor, 'array');
			settype($descontos_listados, 'array');
			settype($descontos_nome, 'array');
			settype($descontos_valor, 'array');
			
			if(!empty($ids_movimentos_parcial)) {				
				
				if(sizeof($ids_movimentos_parcial) >1){
				$ids_movimentos = implode(',', $ids_movimentos_parcial);
				}else {
					$ids_movimentos = $ids_movimentos_parcial[0];
				}
				
				$qr_movimentos  = mysql_query("SELECT * FROM rh_movimentos_clt 
											   WHERE id_movimento IN($ids_movimentos)
											   ORDER BY cod_movimento ASC") or die(mysql_error());	
                                
                                if($_COOKIE['logado'] == 179){
                                    //echo "SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos) ORDER BY cod_movimento ASC";
                                }
                                
				
                                while($movimento = mysql_fetch_array($qr_movimentos)) {
					
                                 
					if($movimento['tipo_movimento'] == 'CREDITO') {
						
						if(!in_array($movimento['id_mov'], $rendimentos_listados)) {
							$rendimentos_listados[] = $movimento['id_mov'];
							$rendimentos_nome[]     = $movimento['nome_movimento'];
							$rendimentos_valor[]    = $movimento['valor_movimento'];
						}
						
					} elseif($movimento['tipo_movimento'] == 'DEBITO' or $movimento['tipo_movimento'] == 'DESCONTO') {
						
						if(!in_array($movimento['id_mov'], $descontos_listados)) {
							$descontos_listados[] = $movimento['id_mov'];
							$descontos_nome[]     = $movimento['nome_movimento'];
							$descontos_valor[]    = $movimento['valor_movimento'];
						}				
					}				
				}			
			}
		  ?>
         
		 <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
            <td ><?=$clt?></td>
		    <td align="left">
				<a href="sintetica/relatorio<?php if(!empty($decimo_terceiro)) { echo '_dt'; } ?>.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Ver relatório de <?=$row_clt['nome']?>">
                	<span class="
                    <?php 		if(isset($dias_entrada))    { echo 'entrada';
                          } elseif(isset($sinaliza_evento)) { echo 'evento';
                          } elseif(isset($dias_ferias))     { echo 'ferias';
                          } elseif(!empty($ferias))   { echo 'rescisao';
                          } elseif(isset($dias_faltas))     { echo 'faltas';
                          } else                            { echo 'normal';
                          } ?>
                          "><?php echo abreviacao($row_clt['nome'], 4, 1);?></span>
                    <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                </a>
            </td>
           
			<td ><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
            
            <td>R$ <?php echo formato_real($salario_limpo);
			
					$total_salario_contratual += $salario_limpo;
			?>        
            </td>
            <td> R$ <?=formato_real($valor_dia)?> x <?=$dias?> dias</td>
           
           
            
            <td>
            
             <!------------------------ MOSTRAR OS RENDIMENTOS --------------------->
            	<table width="100%" class="sem_borda">
					  <?php $rendimentos += $familia;
                            if(!empty($rendimentos)) { ?>
                            
                      
                      <?php if(!empty($familia)) { ?>
                      <tr>
                        <td>SAL&Aacute;RIO FAMILIA</td>
                        <td>R$ <?=formato_real($familia)?></td>
                        <td>R$ <?=formato_real($fixo_familia)?> x <?=$filhos_familia?> filhos</td>    
                      </tr>
                      <?php } if(!empty($salario_maternidade)) { ?>
                      <tr>
                        <td>SAL&Aacute;RIO MATERNIDADE</td>
                        <td>R$ <?=formato_real($salario_maternidade)?></td>
                        <td class="descricao"></td>    
                      </tr>
                      <?php } if(!empty($valor_ferias)) { ?>
                      <tr>
                        <td>F&Eacute;RIAS</td>
                        <td>R$ <?=formato_real($valor_ferias)?></td>
                        <td class="descricao"></td>    
                      </tr>
                      <?php } if(!empty($valor_rescisao)) { ?>
                      <tr>
                        <td>RESCIS&Atilde;O</td>
                        <td>R$ <?=formato_real($valor_rescisao)?></td>
                        <td class="descricao"></td>    
                      </tr>
                      <?php }  
                
                        foreach($rendimentos_valor as $chave => $valor) {								 
                               if(!empty($valor)) { ?>								  
                                 <tr>
                                       <td><?=$rendimentos_nome[$chave]?></td>
                                       <td>R$ <?php echo formato_real($valor);
                                       $total_rendimentos += $valor;									
                                       ?>
                                       </td>
                                       <td class="descricao"></td>   
                                 </tr>

                         <?php } 
                         } 					  
                        } else {?>						  
                        <tr>
                        <td align="center" colspan="3">R$ 0,00</td>
                        </tr>
						  
                     <?php  }?>
                </table>                
            </td> 
            <td>
            
           <!------------------------ MOSTRAR OS DESCONTOS --------------------->
              <table width="100%"  class="sem_borda">    
                <?php 
                    $descontos += $inss_completo + $irrf_completo;
                    if(!empty($descontos) or !empty($row_clt['desconto_inss'])) { ?>                             
				  <?php if(!empty($sindicato)) { ?>
                                  <tr>
                                    <td>CONTRIBUI&Ccedil;&Atilde;O SINDICAL</td>
                                    <td>R$ <?php echo formato_real($sindicato);
                                            $total_descontos += $sindicato;
                                            ?>
                                   </td> 
                                   <td></td>                                   
                                  </tr>
                           <?php }
                            if(!empty($valorPensaoExibirPopUp)) { ?>
                      <tr>
                        <td>PENS&Atilde;O</td>
                        <td>R$ <?=formato_real($valorPensaoExibirPopUp)?></td>
                        <td class="descricao"></td>    
                      </tr>
                      <?php }if((!empty($inss) and $inss != '0.00') or !empty($row_clt['desconto_inss'])) { ?>
                                      <tr>
                                        <td>INSS</td>
                                        <td>R$ <?php echo formato_real($inss);
                                                $total_descontos += $inss;
                                                ?>
                                         </td>
                                        <td>
                                        <?php if($row_clt['tipo_desconto_inss'] == 'isento') { ?>
                                            INSS recolhido em outra empresa
                                        <?php } elseif($row_clt['tipo_desconto_inss'] == 'parcial') { ?>
                                            INSS parcialmente recolhido em outra empresa
                                        <?php } else { ?>
                                            R$ <?=formato_real($base_inss)?> x <?=$percentual_inss?>%
                                        <?php } ?> 
                                        </td>   
                                      </tr>
                           <?php } if(!empty($irrf)) { ?>
                          <tr>
                            <td>IMPOSTO DE RENDA</td>
                            <td>R$ <?php echo formato_real($irrf);
					$total_descontos += $irrf;?>
                            </td>
                            <td>
                         (<?php if(!empty($deducao_irrf)) { ?>(R$ <?=formato_real($base_irrf)?> - R$ <?=formato_real($deducao_irrf)?>)<?php } else { ?>R$ <?=formato_real($base_irrf)?><?php } ?> x <?=$percentual_irrf?>%) - R$ <?=formato_real($fixo_irrf)?>
                            </td>
                          </tr>
                          <?php } if(!empty($vale_transporte) and $vale_transporte != '0.00') { ?>
                          <tr>
                            <td>DESCONTO VALE TRANSPORTE</td>
                            <td>R$ <?php echo formato_real($vale_transporte);
					$total_descontos += $vale_transporte;?>
                              </td>
                            <td>R$ <?=formato_real($salario_limpo)?> x 6%</td>    
                          </tr>
                           <?php } if(!empty($vale_refeicao)) { ?>
                          <tr>
                            <td>DESCONTO VALE REFEI&Ccedil;&Atilde;O</td>
                            <td>R$  <?php echo formato_real($vale_refeicao);
					$total_descontos += $vale_refeicao;?>
                               </td>
                            <td>R$ <?=formato_real($base_refeicao)?> x 20%</td>    
                          </tr>
                           <?php } if(!empty($desconto_ferias)) { ?>
                          <tr>
                            <td>VALOR PAGO NAS F&Eacute;RIAS</td>
                            <td>R$ <?php echo formato_real($desconto_ferias);
					$total_descontos += $desconto_ferias;?>
                              </td>
                            <td></td>
                             
                          </tr>
                          <?php } if(!empty($inss_ferias)) { ?>
                          <tr>
                            <td>INSS SOBRE F&Eacute;RIAS</td>
                            <td>R$ <?php echo formato_real($inss_ferias);
                                          $total_descontos += $inss_ferias;
                                    ?>
                                    </td>
                            <td></td>    
                          </tr>
                          <?php } if(!empty($irrf_ferias) and $irrf_ferias != '0.00') { ?>
                          <tr>
                            <td>IRFF SOBRE F&Eacute;RIAS</td>
                            <td>R$ <?php echo formato_real($irrf_ferias);
                                         $total_descontos += $irrf_ferias;
                                    ?>
                                    </td>
                            <td></td>
                          </tr>
                          <?php } if(!empty($desconto_rescisao)) { ?>
                          <tr>
                            <td>VALOR PAGO NA RESCIS&Atilde;O</td>
                            <td>R$ <?php echo formato_real($desconto_rescisao);
                                        $total_descontos += $desconto_rescisao;
                                    ?>
                            </td>
                             <td></td> 
                          </tr>
                          <?php } if(!empty($inss_rescisao)) { ?>
                          <tr>
                            <td>INSS SOBRE RESCIS&Atilde;O</td>
                            <td>R$ <?php echo formato_real($inss_rescisao);
                                        $total_descontos += $inss_rescisao;
                                    ?>
                            </td>
                             <td></td>   
                          </tr>
                          <?php } if(!empty($irrf_rescisao)) { ?>
                          <tr>
                            <td>IRRF SOBRE RESCIS&Atilde;O</td>
                            <td>R$ <?php echo formato_real($irrf_rescisao);
                                        $total_descontos += $irrf_rescisao;
                                    ?>
                            </td>
                             <td></td>
                          </tr>
                          <?php }
                           
                            foreach($descontos_valor as $chave => $valor) {
                                if(!empty($valor)) { ?>
                                    <tr>
                                          <td><?=$descontos_nome[$chave]?></td>
                                          <td>R$ <?php echo formato_real($valor);
                                                  $total_descontos += $valor;
                                          ?></td>
                                          <td></td>
                                    </tr>

                                    <?php } 
                                    } 

                            } else { ?>

                            <tr>
                            <td colspan="4" align="center">R$ 0,00</td>
                            </tr>

                            <?php } ?>
                    
                    </table>
            </td>
        <!--    <td ><?=formato_real($inss_completo)?></td>
            <td><?=formato_real($irrf_completo)?></td>
            <td ><?=formato_real($familia)?></td>-->
			<td ><?=formato_real(abs($liquido))?></td>
		 </tr>

		<?php include('sintetica/update_participante.php');
			  include('sintetica/totalizadores_resets.php');

		 	// Fim do Loop de Participantes
			unset($valorParaPensaoFinal);
			unset($rendimentos_listados,$rendimentos_nome,$rendimentos_valor,$descontos_listados,$descontos_nome,$descontos_valor, $rendimentos_listados, $rendimentos_nome, $rendimentos_valor,$descontos_listados,$descontos_nome,$descontos_valor, $ids_movimentos_parcial);
		
			
	  	    } ?>
            
      	<tr class="totais">
         <!-- <td colspan="2">
		      <?php if($total_participantes > 10) { ?>
          	      <a href="#corpo" class="ancora">Subir ao topo</a>
              <?php } ?></td>-->
              <td></td>
          <td>TOTAIS:</td>
          <td><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_total); } else { echo formato_real($salario_total); } ?></td>
          <td><?=formato_real($total_salario_contratual)?></td>
          
          <td></td>    
		  <td>R$ <?=formato_real($total_rendimentos)?></td>
          
                         
		  <td><?=formato_real($total_descontos)?></td>
         <!-- <td><?=formato_real($inss_completo_total)?></td>
          <td><?=formato_real($irrf_completo_total)?></td>
          <td><?=formato_real($familia_total)?></td>-->
		  <td><?=formato_real($liquido_total)?></td>
        </tr>
    </table> 
   </td>
   </tr>
  </table>
    
    <?php include('sintetica/estatisticas_folha.php'); ?>
</div>
<?php include('sintetica/updates.php'); ?>
</body>
</html>