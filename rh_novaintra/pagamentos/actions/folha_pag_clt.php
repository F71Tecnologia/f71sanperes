<?php 
require("../../../conn.php");

$ano = $_POST['ano'];
$mes = 12;
 ?>
       <div class="ano">
            <div class="titulo">FOLHAS DE PAGAMENTO <?=$ano?> <span class="destaque">CLT</span></div>
                  <table cellpadding="4" cellspacing="0" class="relacao">
                      <tr class="secao">
                        <td colspan="3">Mês Referente</td>
                      </tr>
                        <?php 			
                       $qr_folha = mysql_query("SELECT f.id_folha, f.projeto, p.id_regiao, f.terceiro, f.tipo_terceiro, p.regiao, p.nome
                                                FROM rh_folha f INNER JOIN projeto p ON f.projeto = p.id_projeto
                                                 WHERE p.id_master = '1' 
                                                 AND (f.status = '3' OR f.status = '2')
                                                 AND f.mes = '$mes' 
                                                 AND f.ano = '$ano'
                                                 AND p.id_regiao != '36'");

                      $total_folha = mysql_num_rows($qr_folha);
                       if(!empty($total_folha)) { 
                        while($row_folha = mysql_fetch_assoc($qr_folha)){
                           ?>
                               
                <tr class="linha_<?php if($cor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                   <td colspan="2">
                      <div>
                            	<span class="folha_mes"><?=$nome_mes?>&nbsp;<img src="../../folha/sintetica/seta_dois.gif"/></span>
                                <div >
									<table width="100%" bgcolor="#FFFFFF"  cellspacing="1" cellpadding="5">
                                    	<tr bgcolor="#CCCCCC">
                                            <td><span class="cabecalho">ID folha</span></td>
                                            <td ><span class="cabecalho">Regiao</span></td>
                                            <td><span class="cabecalho">Projeto</span></td>
                                            <td><span class="cabecalho">GPS</span></td>
                                            <td><span class="cabecalho">FGTS</span></td>
                                            <td><span class="cabecalho">PIS</span></td>
                                            <td><span class="cabecalho">IR</span></td>
                                        </tr>
                                        <?php 
                                          while($row_folha = mysql_fetch_assoc($qr_folha)){
                                        ?>
                                
                                        <tr class="linha_<?php if($cor2++%2==0) { ?>um<? } else { ?>dois<? } ?>">
                                        	<td><span class="dados"><?=$row_folha['id_folha']?></span></td>
                                        	<td><span class="dados">
											<?=
												$row_folha['id_regiao'] . " - " . $row_folha['regiao'];
											?></span>
                                            </td>
                                        	<td><span class="dados">
                                            	<?php 
												// verificação de 13ª salario.
												if($row_folha['terceiro'] == '1'){
													if($row_folha['tipo_terceiro'] == 3){
														$decimo3 = " - 13ª integral";
													}else{
														$decimo3 =" - 13ª ($row_folha[tipo_terceiro]ª) Parcela";
													}
												}	
												?>
												<?=$row_folha['nome'].$decimo3?>
                                                <?php unset($decimo3);?>
                                             	</span>
                                             </td>
                                            <?php 
											$sql = "
												SELECT saida.status,pagamentos.id_pg,pagamentos.tipo_pg,saida.valor,saida.id_saida,date_format(saida.data_proc, '%d/%m/%Y') AS DATA, 
												saida.id_user, date_format(saida.data_pg, '%d/%m/%Y') AS DATAPG, saida.id_userpg 
												FROM saida 
												INNER JOIN pagamentos
												ON saida.id_saida = pagamentos.id_saida
												WHERE pagamentos.mes_pg = '$mes'
												AND pagamentos.ano_pg = '$ano'
												AND saida.id_regiao != '36'
												AND pagamentos.id_folha = '$row_folha[id_folha]'
												
												";
												
												
												
												$query_controle = mysql_query($sql);
												$num_controle = mysql_num_rows($query_controle);
												while($row_controle = mysql_fetch_assoc($query_controle)){
													$link_encryptado = encrypt('ID='.$row_controle['id_saida'].'&tipo=0');
													$link_encryptado_pg = encrypt('ID='.$row_controle['id_saida'].'&tipo=1');
													$tipo = $row_controle['tipo_pg'];
												
													switch($row_controle['status']){
														case 1:
															$color[$tipo] = "bgColor='#FF473E'";
															break;
														case 2:
															$color[$tipo] = "bgColor='#9BD248'";
															break;
														case 0: 
															$color[$tipo] = "bgColor='#8BDCF3'";
															break;
														default: $color[$tipo] = '';
													}	
													
													//  COLOCANDO AS MENSAGENS DE STATUS
													switch($row_controle['status']){
														case 1: 
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_user]'");
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].' - '.@mysql_result($qr_fun,0).'<br>';
															
															
															$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_controle[id_saida]'");
															$quanti = mysql_num_rows($qr_quant);
															$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_controle[id_saida]'");
															$quanti2 = mysql_num_rows($qr_quant2);
															
															
																$title[$tipo] .= '<b>ID saida :</b> '.$row_controle['id_saida'].'<br>';
															
															
															if(!empty($quanti)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'\'>Comprovante</a></b><br>';
															}
															if(!empty($quanti2)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado_pg.'\'>Comprovante PG</a></b><br>';
															}
															

															//$title[$tipo] .= '<b>Id saida :</b> '.$row_controle['id_saida'].'<br>';
															$title[$tipo] .= '<b>Valor :</b> R$ '.$row_controle['valor'].'"';
															break;
														case 2:
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_user]'");
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].' - '.@mysql_result($qr_fun,0).'<br>';
															$title[$tipo] .= '<b>Pago em :</b> '.$row_controle['DATAPG'].'<br>';
															$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_controle[id_saida]'");
															$quanti = mysql_num_rows($qr_quant);
															$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_controle[id_saida]'");
															$quanti2 = mysql_num_rows($qr_quant2);
															
															if(!empty($quanti)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'\'>Comprovante</a></b><br>';
															}
															if(!empty($quanti2)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado_pg.'\'>Comprovante PG</a></b><br>';
															}
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_userpg]'");
															
															
																$title[$tipo] .= '<b>ID saida :</b> '.$row_controle['id_saida'].'<br>';
															
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_userpg'].' - '.@mysql_result($qr_fun,0).'<br>';
															$title[$tipo] .= '<b>Valor :</b> R$ '.$row_controle['valor'].'"';
															break;
														case 0:
															$qr_fun = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario  = '$row_controle[id_user]'");
															$title[$tipo] = 'title="<b>Gerado em :</b> '.$row_controle['DATA'].'<br>';
															$title[$tipo] .= '<b>Por :</b> '.$row_controle['id_user'].' - '.@mysql_result($qr_fun,0).'<br>';
															
															$qr_quant = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_controle[id_saida]'");
															$quanti = mysql_num_rows($qr_quant);
															$qr_quant2 = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$row_controle[id_saida]'");
															$quanti2 = mysql_num_rows($qr_quant2);
															
															if(!empty($quanti)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado.'\'>Comprovante</a></b><br>';
															}
															if(!empty($quanti2)){
																$title[$tipo] .= '<b><a target=\'_blank\' href=\'../../novoFinanceiro/view/comprovantes.php?'.$link_encryptado_pg.'\'>Comprovante PG</a></b><br>';
															}
															//$title[$tipo] .= '<b>Id saida :</b> '.$row_controle['id_saida'].'<br>';
															$title[$tipo] .= '<b>Valor :</b> R$ '.$row_controle['valor'].'"';
															break;
																													
													}
												}
												if(empty($num_controle)){
													$color[1] = '';
													$color[2] = '';
													$color[3] = '';
													$color[4] = '';
													$color[5] = '';
													$title[1] = '';
													$title[2] = '';
													$title[3] = '';
													$title[4] = '';
													$title[5] = '';
												}
												
												
										
										
												
											?>
                                            <td <?=$title[1]?> <?=$color[1]?> align="center">  
                                             <?php if($row_controle['status'] == 0 ):?>
                                            <span class="dados">
                                            <a href="cadastro.php?gps&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400', width: '400' } )">
                                            	<img src="../imagensrh/gps.jpg" />
                                            </a>
                                            </span>
                                             <?php else: ?>
                                            <span class="dados"><img src="../../imagensrh/gps.jpg" /></span>
                                            <?php endif;?>
                                            </td>
                                            <td <?=$title[2]?> <?=$color[2]?> align="center">
                                            <?php //if ($_COOKIE['logado'] == '75') echo $sql;?>
                                            <?php if($row_controle['status'] == 0):?>
                                            <span class="dados"><a href="cadastro.php?fgts&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400', width: '400'  } )" ><img src="../imagensrh/log_fgts.jpg"/></a></span>
                                            <?php else: ?>
                                            <span class="dados"><img src="../../imagensrh/log_fgts.jpg"/></span>
                                            <?php endif;?>
                                            </td>
                                            <td <?=$title[3]?> <?=$color[3]?> align="center">
                                              <?php if(empty($color[3])):?>
                                            <span class="dados"><a href="cadastro.php?pis&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400', width: '400'  } )"><img src="../imagensrh/pis.jpg"/></a></span>
                                             <?php else: ?>
                                            <span class="dados"><img src="../../imagensrh/pis.jpg"/></span>
                                            <?php endif;?>
                                            </td>
                                            <td <?=$title[4]?> <?=$color[4]?> align="center">
                                             <?php if(empty($color[4])):?>
                                            <span class="dados"><a href="cadastro.php?ir&tipo=CLT&mes=<?=$mes?>&ano=<?=$ano?>&projeto=<?=urlencode($row_folha['nome'])?>&folha=<?=$row_folha['id_folha']?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe', height: '400', width: '400'  } )"><img src="../imagensrh/ir.jpg"/></a></span>
                                            <?php else: ?>
                                            <span class="dados"><img src="../../imagensrh/ir.jpg"/></span>
                                            <?php endif;?>
                                            </td>
                                        </tr>
                               <?php unset($color,$title);?>
                                <?php } ?>
                                 
                                </table>
                                </div>
                      </div>
                   </td>
                   <td align="center">
                        		<?=$total_participantes?>
                   </td>
                </tr>  
                                  
                  <?php unset($total_participantes); 
                  
                       }
                }
               ?>
</table>
</div> 
         
        
    