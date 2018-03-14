<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script language="javascript">
$('.ano').parent().next().hide();
	$(".ano").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
	$('a.recisao').click(function(){
		$(this).next().toggle();
	});
</script>
</head>

<body>
        	<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////-->
        
<?php 
$regiao = $_GET['regiao'];
$permissao = array('5','27','9','75','77','64','68');
if(in_array($id_user,$permissao)){ ?>
<div id="provisoes">
	<div class="apDiv1">
	<table width="100%"   border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
	  <tr>
	    <td  height="25" colspan="5" align="left" valign="middle" bgcolor="#999999" ><span style="color:#FFF;"><strong>PROVIS&Otilde;ES AUTONOMOS</strong></span></td>
	    </tr>
	  <tr>
	    <td colspan="5">
        <?php 
		$query_provisao = mysql_query("SELECT 
									p.id_provisao,		 	 	 	
									p.id_projeto,		 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 	 
									p.ano_provisao		 	 	 	 	 	 	 
									FROM provisao AS p  LEFT JOIN projeto AS pr 
									ON pr.id_projeto = p.id_projeto 
									WHERE p.status_provisao = '1' AND pr.id_regiao = '$regiao' 
									ORDER BY p.id_provisao ASC;
										");
		$provisao_autonomo = array();
		while($row_provisao = mysql_fetch_assoc($query_provisao)){
			$projeto	= $row_provisao['id_projeto'];
			$ano 		= $row_provisao['ano_provisao'];
			$provisao_autonomo[$projeto][$ano][] = $row_provisao['id_provisao'];
			ksort($provisao_autonomo[$projeto]);
		}
		/*
		print_r($provisao_autonomo);
		*/				
		?>
        <table width="100%" border="0"  cellspacing='1' cellpadding='3'>
              <?php foreach($provisao_autonomo as $projeto => $anos){?>
                <tr bgcolor="#FBFBFB" class="linha_um" >
                  <td colspan="5" align="center" bgcolor="#666666" >
                  <span style="font-weight:bold; font-size:16px; color:#FFF;">
                  	<?php 
					if($projeto_anterior != $projeto){
							$qr_projeto = mysql_query("SELECT nome,id_projeto FROM projeto WHERE id_projeto = '$projeto'");
							print mysql_result($qr_projeto,0);
						}
					?>
                  </span>
                  </td>
               	</tr>
                  <?php foreach($anos as $ano => $provisoes){ ?>
                  <tr bgcolor="#FBFBFB">
                  	<td  class="dataautonomos" colspan="5" align="center"  style="cursor:pointer;">
                    <span style="padding:10; margin-bottom:10px; font-size:14px; font-weight:bold">
                  		<?php
							if($ano_anterior != $ano){
								echo $ano;
							}
						?>
                    </span>
                  	</td>
                  </tr>
                  <tr>
                  	<td colspan="5"  >
             		<table width="100%" class="autonomos"  cellspacing='1' cellpadding='3'>
                      <tr class="linha_dois">
                        <td width="20%" bgcolor="#CCCCCC"><b>Provisão</b></td>
                        <td width="20%" bgcolor="#CCCCCC"><b>Mês</b></td>
                        <td bgcolor="#CCCCCC">&nbsp;</td>
                        <td width="20%" bgcolor="#CCCCCC"><b>Valor</b></td>
                        <td bgcolor="#CCCCCC">&nbsp;</td>
                      </tr>
                      <?php 
                        foreach($provisoes as $provisao){
                    ?>
                      <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
                          <?php 
                            $qr_provisao = mysql_query("SELECT * FROM provisao WHERE id_provisao = '$provisao'");
                            $rw_provisao = mysql_fetch_assoc($qr_provisao);
                          ?>
                      <td>
                           <?=$rw_provisao['id_provisao']?>
                      </td>
                     <td>
                            <?php
                               $qr_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_provisao[mes_provisao]'"); 
                                echo mysql_result($qr_mes,0);
                            ?>
                      </td>
                      <td >
                      </td>
                      <td width="20%"><?php echo "R$ ".number_format($rw_provisao['valor_provisao'], 2, ',', ' '); $valor_total += $rw_provisao['valor_provisao']; ?></td>
                      <td width="10%" align="right">
                        <a href="javascript:abrir('cadastro.provisao.php?ID=<?=$rw_provisao['id_provisao']?>&regiao=<?=$regiao?>','700','500','Cadastro de provisão')" ><img  src="../imagensmenu2/Edit.png"  width="20px" height="20px" border="0" /></a>
                        <a onclick="confirmacao('actions/cadastro.provisao.php?regiao=<?=$regiao?>&log=3&id=<?=$rw_provisao['id_provisao']?>','Tem certeza que deseja deletar esta provisao?')" href="#"><img src="../imagensmenu2/Symbol-Delete.png" width="20px" height="20px" border="0" /></a>
                      </td>
                      </tr>
                      <?php } ?>	   
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><b>Total: </b></td>
                        <td><b><?php echo " R$ ".number_format ($valor_total, 2, ',', ' '); $valor_total = 0;?></b></td>
                     </tr>
                      </table>
                    </td>
                  </tr>
                  <?php
				  	$ano_anterior = $ano;	
				   	}?>
              	<?php 
					$ano_anterior = NULL;
					$projeto_anterior = $projeto;
				} ?>
              </table>
          </td>
	    </tr>
	  </table>
      </div>
    <br />
    <!-- ///////////////////// CLT ///////////////////////-->
    <?php
		$projeto = array();
		$sql_folha = "SELECT f.rendi_final, p.nome, f.mes, f.projeto, f.id_folha, f.ano, f.terceiro, f.tipo_terceiro
						FROM rh_folha f
						LEFT JOIN projeto p ON p.id_projeto = f.projeto
						WHERE p.id_regiao =  '$regiao'
						AND f.status =  '3' 
						AND p.status_reg != 0 
						ORDER BY f.mes ASC;";
		$query_folha = mysql_query($sql_folha);
		while($row_folha = mysql_fetch_assoc($query_folha)){
			$chave_projeto = $row_folha['projeto'];
			$ano_folha = $row_folha['ano'];
			$projetos[$chave_projeto][$ano_folha][] =  $row_folha['id_folha'];
			ksort($projetos[$chave_projeto][$ano_folha]);
		}
	?>
   <div class="apDiv1">
<table width="100%"   border="0" bordercolor="#FFFFFF" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
  <tr>
    <td  height="25" align="left" valign="middle" bgcolor="#999999"  >
        <span style="color:#FFF;"><strong>PROVIS&Otilde;ES CLT</strong></span>
    </td>
  </tr>
  <tr>
      <?php 
	  if(empty($projetos)) echo "<h1>TA VAZIO</h1>"; exit;
	  	if(!empty($projetos)){
			foreach($projetos as $projeto => $anos) {
				foreach($anos as $ano => $folhas) {
					if($projeto != $ultimo_projeto) { ?>
						<tr>
						  <td align="center" bgcolor="#CCCCCC" ><span style="font-weight:bold; font-size:16px; color:#333; ">
						  <?php 
						  		$query_projetos = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
						  		print mysql_result($query_projetos,0);
							?>
                            </span>
                           </td>
						</tr>
              <?php } if($ano != $ultimo_ano) { ?>
              		<tr style="cursor:pointer;" >
                      <td height="20" class="ano" align="center" bgcolor="<? if($alternateColorAno++%2==0) { ?>#EEEEEE<? } else { ?>#FFFFFF<? } ?>" >
                          <span style="padding:10; margin-bottom:10px; font-size:14px; font-weight:bold">
							<?=$ano?>
                          </span>
                      </td>
                    </tr>
     		<?php }?>
     			<tr>
        	 		<td >
                    	<table style="clear:both;" width="100%" align="center"  cellspacing='1' cellpadding='3'>
                        	<tr>
                            	<td><b>Folha</b></td>
                                <td><b>Mês</b></td>
                                <td><b>Valor total da folha</b></td>
                                <td colspan="2"><b>Total gasto</b></td>
                                <td colspan="2"><b>Valor da provis&atilde;o</b></td>
                                <td><b>Valor a pagar</b></td>
                            </tr>
                    <?php foreach($folhas as $folha) { 
							$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha';");
							$rw_folha = mysql_fetch_assoc($qr_folha);
					?>
                            <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
                              <td><?=$rw_folha['id_folha']?></td>
                              <td>
							<?php
                                $query_meses = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$rw_folha[mes]';");
                                $print_mes = mysql_result($query_meses,0);
                                if($rw_folha['terceiro'] == '1'){
                                    if($rw_folha['tipo_terceiro'] == 3){
                                        $print_mes .= "13ª integral";
                                    }else{
                                        $print_mes .=" 13ª ($rw_folha[tipo_terceiro]ª) Parcela";
                                    }
                                }						  			
                                echo $print_mes;
                                $qr_recisao = mysql_query("SELECT * FROM rh_recisao WHERE MONTH(data_demi) = '$rw_folha[mes]' AND YEAR(data_demi) = '$rw_folha[ano]' AND id_regiao = '$rw_folha[regiao]' AND id_projeto = '$rw_folha[projeto]'");
								$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE MONTH(data_ini) = '$rw_folha[mes]' AND YEAR(data_ini) = '$rw_folha[ano]' AND regiao = '$rw_folha[regiao]' AND projeto = '$rw_folha[projeto]'");
								$num_recisao = mysql_num_rows($qr_recisao);
								$num_ferias = mysql_num_rows($qr_ferias);
								?>
                                <?php if(!empty($num_recisao) or !empty($num_ferias)):?>
                                <a class="recisao"><img src="../rh/folha/sintetica/seta_um.gif" width="9" height="9" style="cursor:pointer"/></a>
                                <div style="display:none">
                                <table>
                                	<tr>
                                    	<td>ID</td>
                                    	<td>Nome</td>
                                        <td>Total</td>
                                    </tr>
									<?php 
									if(!empty($num_recisao)):
									while($row_recisao = mysql_fetch_assoc($qr_recisao)): ?>
                                    <tr>
                                    	<td>
                                    		<?=$row_recisao['id_clt']?>
                                    	</td>
                                        <td>
                                    		<?=$row_recisao['nome']?>
                                    	</td>
                                        <td>
                                    		<?="R$ " .number_format ($row_recisao['total_liquido'], 2, ',', ' ')?>
                                    	</td>
                                    </tr>
                                   <?php 
								   		$Total_recisao += $row_recisao['total_liquido'];
								   endwhile;?>
                                   <tr>
                                        <td>&nbsp;</td>
                                        <td align="right">Total recisão:</td>
                                        <td>
                                            <?="R$ " .number_format ($Total_recisao, 2, ',', ' ')?>
                                        </td>
                                    </tr>
                                   <?php endif;?>
                                   <?php 
								   if(!empty($num_ferias)):
								   while($row_ferias = mysql_fetch_assoc($qr_ferias)):?>
                                   <tr>
                                    	<td>
                                    		<?=$row_ferias['id_clt']?>
                                    	</td>
                                        <td>
                                    		<?php
												$qr = mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '$row_ferias[id_clt]'");
												$nome = @mysql_result($qr,0);
											 	echo $nome;
											?>
                                    	</td>
                                        <td>
                                    		<?="R$ " .number_format ($row_ferias['total_liquido'], 2, ',', ' ')?>
                                    	</td>
                                    </tr>
                                   <?php
								   	$Total_ferias += $row_ferias['total_liquido'];
								    endwhile;?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td align="right">Total Ferias:</td>
                                        <td>
                                            <?php 
											echo"R$ " .number_format ($Total_ferias, 2, ',', ' ');
											?>
                                        </td>
                                    </tr>
                                   <tr>
                                   <?php endif;?>
                                   	<td>&nbsp;</td>
                                    <td align="right"><b>Total: </b></td>
                                   	<td><b><?php 
											$Total_gasto = $Total_recisao+$Total_ferias;
											 
											$total_final += $Total_gasto;
											echo "R$ " .number_format ($Total_gasto, 2, ',', ' ');
											$Total_recisao = NULL;
											$Total_ferias = NULL;
										?>
                                        </b>
                                    </td>
                                   </tr>
                                </table>
                                </div>
                                <?php endif;?>
                              </td>
                              <td>
							  	<?php 
									// se for folha de 13° salario pegar o valor da valor_dt
							  		if($rw_folha['terceiro'] == '1'){
										$total = $rw_folha['valor_dt'];
									}else{
										$total = $rw_folha['rendi_final'];
									}
									echo "R$ " . number_format ($total, 2, ',', ' ');
									?>
                              </td>
								<td colspan="2">
									<?php
									echo "R$ " . number_format ($Total_gasto, 2, ',', ' ');
									?></td>
                              <td colspan="2">
							  	<?php
                              		$totalF=($total* 33.93)/100;
									$somatorio += $totalF;
									echo "R$ ".number_format ($totalF, 2, ',', ' ');
								?>
                                </td>
                                <td>
								<?php 
									$total_a_pagar = $totalF - $Total_gasto;
									$total_a_pagar_final += $total_a_pagar;
									echo "R$ ".number_format ($total_a_pagar, 2, ',', ' ');
									unset($total_a_pagar);
								?></td>
                            </tr>
                      <?php unset($Total_gasto); } ?>
                      		<tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right">Total:</td>
                                <td align="left"><?="R$ ".number_format ($total_final, 2, ',', ' ')?></td>
                                <td align="right">Total:</td>
                                <td><?="R$ ".number_format ($somatorio, 2, ',', ' ')?></td>
                                <td align="right">Total:</td>
								<td>
								<?php 
									echo "R$ ".number_format ($total_a_pagar_final, 2, ',', ' ');
									unset($total_a_pagar_final);
								?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td align="right"><b>Total final:</b></td>
                                <td colspan="2"><b>
                                	<?php 
										$Calc_final = $somatorio - $total_final;
										echo "R$ ".number_format ($Calc_final, 2, ',', ' ');
									?>
                                    </b>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                      	</table>
          			</td>
        		</tr>
<?php
	 		$ultimo_projeto = $projeto;
			$ultimo_ano		= $ano;
			unset($Total_gasto,$somatorio,$total_final);
				}
			unset($ultimo_ano);
			}
		}
	}// fim do if da permisao
?>
</table>
</div>
<!-- ///////////////////////// CLT //////////////////// -->  
</div>
	<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////-->

</body>
</html>