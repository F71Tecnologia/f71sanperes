<style>
.aviso_box
{
	position:relative;
	float:left;
	 width:450px;
	  height:auto;

}

</style>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr><td colspan="6" align="center"><h1><span>Administração Geral</span></h1><p>&nbsp;</p></td></tr>
  <tr style="font-size:11px; font-family:arial; padding-top:20px;">
  
  <!--
    <td align="center">
     <a href="#"><img src="imagens/calc.jpg" width="70" height="70"></a><br />
 <br>
          ADMINISTRA&Ccedil;&Atilde;O FINANCEIRA
    </td>
    
    -->
    
    
    <td width="20%" align="center">
     <a href="adm_parceiros/index.php?m=<?=$link_master?>"><img src="imagens/parceiros.jpg" width="61" height="70"></a><br />
<br>
          ADMINISTRA&Ccedil;&Atilde;O DE PARCEIROS
    </td>
    
    <td width="21%" align="center">
     <a href="adm_projeto/index.php?m=<?=$link_master?>"><img src="imagens/cahier.jpg" width="70" height="70"></a><br />
<br>
          ADMINISTRA&Ccedil;&Atilde;O DE PROJETOS
    </td>
    <td width="23%" align="center">
     <a href="adm_notas/index.php?m=<?=$link_master?>"><img src="imagens/nota_fiscal.jpg" width="70" height="70"></a><br />
<br>
          ADMINISTRA&Ccedil;&Atilde;O DE NOTAS FISCAIS
    </td>
    
    <!--
    <td align="center">
 	 <a href="adm_curso/index.php?m=<?=$link_master?>"><img src="imagens/folder2.jpg" width="87" height="70"></a><br />
<br>      
     	  ADMINISTRA&Ccedil;&Atilde;O DE CURSOS
    </td>
    -->
    
    <td width="19%" align="center">
 	 <a href="adm_contratos/index.php?m=<?=$link_master?>"><img src="imagens/classeurs.jpg" width="70" height="70"></a><br />
<br>
      	  ADMINISTRA&Ccedil;&Atilde;O DE OBRIGA&Ccedil;&Otilde;ES
    </td>
    
      <td width="17%" align="center">
 	 <a href="prestador.php?m=<?=$link_master?>" onclick="window.open('prestador.php?m=<?=$link_master?>','width=400,height=300'"><img target="_blank" src="imagens/prestador.jpg" width="70" height="70"></a><br />
<br>
      	 PRESTAÇÃO DE SERVIÇOS
    </td>
         <td width="17%" align="center">
 	 <a href="documentos/index.php?m=<?=$link_master?>" onclick="window.open('documentos.php?m=<?=$link_master?>','width=400,height=300'"><img target="_blank" src="imagens/download.jpg" width="70" height="70"></a><br />
<br>
      	 MODELOS DE DOCUMENTOS
    </td>
  </tr>
</table>



<?php 
for($i=0; $i<=1; $i++):

	
		
		if($i == 1) {
			
		/*<!--------------------------------------ENVIAR OS AVISOS POR EMAIL A CADA 10 DIAS---------------------------------------------------------------------------------------------------------->*/
					
					
					
					
							$pega_master = $Master;
							
							ob_start(); //começa a armazenar ocódigo no buffer
									
							
							 echo  '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
							<title>Quadro de avisos</title> 
							<style>
							table.relacao {
								width:95%;
								
								margin:0px auto;
								font-size:12px;
								text-align:center;
							}
							
							table.relacao h1 {
								background-color:#C99;
								font-size:13px;
								color:#FFF;
								padding:4px 8px;
								width:150px;
							}
							
							tr.secao {
								text-align:center; 
								background-color:#777; 
								color:#FFF; 
								font-weight:bold; 
								line-height:24px; 
								font-size:11px;
								text-transform:uppercase;
							}
							
							td.secao {
								text-align:right;
								padding-right:8px;
								font-style:italic;
								font-weight:bold;
								width:200px;
							}
							
							
							tr.novo {
								font-size:12px;
								padding:4px; 
								font-weight:normal;
								background-color:#Eff1e9;
							}
							tr.novo2 {
								font-size:10px;
								padding:10px; 
								font-weight:normal;
								background-color:#DBEAE8;
								text-align:center;
								margin-left:30px;
							}
							
							.linha_um {
								background-color:#FAFAFA;
							}
							
							.titulo{
								background-color:#C99;
								font-size:13px;
								color:#FFF;
								padding:4px 8px;
								width:180px;
								margin:20px auto;
								
								}
								#conteudo {
								width:100%;
								text-align:center;
							}
							
							</style>
							</head>
							<body>
							<div id="conteudo">
							<div>'; 
					
					$date = date('d/m/Y');
					
					?>
					
					<div style="margin:0 auto; font-size:16px; color:#09F; font-weight:bold;">
					
					<?php if($Master='1')
							{ 
							
							echo 'Instituto Sorrindo para a Vida<br>';
							} 
							else if($Master='2')
							{ 
							echo 'SOE<br>';
							}
							else if($Master='4')
							{ 
								 echo 'FAHJEL<br>';
							}
											
							echo '</div>';
				
		} //fim if
		?>
		
		<!-----------------------------------QUADRO DE AVISOS---------------------------------------------------------->
		
		
		 <!--<div style="background-image:url(../imagens/fundo_quadro.jpg); width:auto; height:auto;">-->
		<h1 class="aviso" style="font-size:16px;;margin-top:50px;">Quadro de Avisos</h1>
		<!---<img style="margin-top:30px;"  width="100" height="100"src="../imagens/quadro_aviso.jpg"/>-->
		
		<div>
		
		  <table class="relacao">
			<tr class="titulo">
				<td colspan="3" align="center">PROJETO</td>
			</tr>
			</table>
			
			<table class="relacao">
			<tr class="secao">
                    <td>Nome</td>
                    <td>Local</td>
                    <td colspan="2">Status</td>
		    </tr>
		
		<?php
		$qr_regiao = mysql_query("SELECT * FROM regioes WHERE status_reg = '1' AND status = '1' AND id_master='$Master' ORDER BY regiao");
		while($row_regiao = mysql_fetch_assoc($qr_regiao)):
				
						if($row_regiao['id_regiao']=='15' or $row_regiao['id_regiao']=='36' or $row_regiao['id_regiao']=='37') continue;  	
					
						$qr_projeto = mysql_query("SELECT * FROM projeto WHERE status_reg = '1' AND id_regiao = '$row_regiao[id_regiao]';");
						while($row_projeto = mysql_fetch_assoc($qr_projeto)):
						
									 //verifica expiracao dos projetos
									  list($ano,$mes,$dia) = explode('-',$row_projeto['termino']);
									 $data_termino = mktime(0,0,0,$mes,$dia,$ano);
									 $data_hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
									 $prazo_renovacao = mktime(0,0,0,$mes,$dia-45,$ano); //45 dias
									 
									
										$qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
										(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
										INNER JOIN entrada 
										ON notas_assoc.id_entrada = entrada.id_entrada
										WHERE entrada.data_vencimento >='$row_projeto[inicio]' AND  entrada.data_vencimento <= '$row_projeto[termino]' AND notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_subprojeto] ");
									   
										$total_entrada = (float) @mysql_result($qr_valor,0);
										$valor_alcancado = $total_entrada;
										
										$totalizador += $valor_alcancado;
										
										$totalizador_verbadestinada+= $row_projeto['verba_destinada'];
										
										
																	 
									 $qr_subprojeto=mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
												$verifica = mysql_num_rows($qr_subprojeto);
												
											if(!empty($verifica)){
													
												 while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):
													
													  $qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
													   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													   INNER JOIN entrada 
													   ON notas_assoc.id_entrada = entrada.id_entrada
													   WHERE entrada.data_vencimento >='$row_subprojeto[inicio]' AND  entrada.data_vencimento <= '$row_subprojeto[termino]' AND notas.id_projeto = '$row_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_subprojeto[id_subprojeto]  ")or die(mysql_error());
													   
														 
													
															$total_entrada_sub = (float) @mysql_result($qr_valor,0);
															$valor_alcancado_sub=$total_entrada_sub;
															$totalizador += $valor_alcancado_sub;
															
														//totalizador	
															$verba_destinada = str_replace(',','.',(str_replace('.','',$row_subprojeto['verba_destinada'])));
															$totalizador_verbadestinada += $verba_destinada;
																										
												endwhile;
												}
												
												if($totalizador > $totalizador_verbadestinada){
										
                                              ?>      <tr class="linha_um">
                                                            <td align="left"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?> </td>
                                                            <td align="left"><?php echo $row_regiao['regiao'];?> </td>
                                                            <td>
																<?php
                                                                    echo '<br> <span  style="color:#F00;font-weight:bold;"> Verba do projeto ultrapassou o valor estimado!</span>';
                                                                   
                                                                ?>
                                                            
                                                            </td>
												
													</tr>
                                   <?php
												   }
												   
												   
												    unset ($totalizador_verbadestinada);								
            															unset($totalizador);  
                                                                
								 if(($prazo_renovacao<=$data_hoje) and($data_termino>$data_hoje))
									 { 
										$dif=($data_termino-$data_hoje)/86400; //projetos expirando
										
									?>
                                     <tr class="linha_um">
                                        <td align="left"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?> </td>
                                        <td align="left"><?php echo $row_regiao['regiao'];?> </td>
                                        <td><?php echo '<span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span>';?> </td>
                                        <td>
                                        
                                     </tr>
																	  
									<?php
									}
									
									 elseif($data_hoje>$data_termino) //projetos expirado
									
									{	 
												//verifica se exite subprojetos  exbibe caso exista e estja expirando ou expirado
												$verifica_ok=0;		 
												$qr_subprojeto=mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
												$verifica = mysql_num_rows($qr_subprojeto);
												
												if(!empty($verifica)){
													
												 while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):
												 
												 
												
												 
													 list($ano,$mes,$dia) = explode('-',$row_subprojeto['termino']);
													$data_termino = mktime(0,0,0,$mes,$dia,$ano);
													 $data_hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
													$prazo_renovacao = mktime(0,0,0,$mes,$dia-45,$ano); //45 dias
													
													if($data_hoje<$data_termino)
													{														
														$verifica_ok++;
																			
													}
									 
											 if(($prazo_renovacao<=$data_hoje) and($data_termino>$data_hoje)) //projetos expirando
											 { 
												$dif=($data_termino-$data_hoje)/86400;
												
															?>
														
                                                            <tr class="linha_um">
                                                                <td align="left"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?> </td>
                                                                <td align="left"><?php echo $row_regiao['regiao'];?> </td>
                                                                <td><?php echo '<span style="color:#09C;font-weight:bold;">Renovação expira em '.$dif.' dias!</span>';?> </td>
                                                            </tr>
                                                
														<?php
											 }  
											 elseif($data_hoje>$data_termino  and $verifica_ok== 0) //SUBprojetos expirado
											 {
												 ?>
                                                <tr class="linha_um">
                                                    <td align="left"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?> </td>
                                                    <td align="left"><?php echo $row_regiao['regiao'];?> </td>
                                                    <td>
                                                    
                                                    <?php 
                                                    if($row_subprojeto['termino']=='0000-00-00' or $row_subprojeto['inicio']=='0000-00-00')
                                                        {
                                                            echo '<span  style="color:#F90;font-weight:bold;">Faltam informações sobre as datas</span>';	
                                                        }
                                                         else 
                                                        {
                                                            echo '<span  style="color:#F00;font-weight:bold;"> Renovação expirada</span>';
                                                            
                                                        }
                                                            ?> 
                                                    
                                                    </td>
                                         	</tr> 
												 <?php
                                                 $verifica_ok=1;
                                                  }
																		
													endwhile; //subprojeto	
												
												} else { 
														?>
														
														
														<tr class="linha_um">
															<td align="left"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?> </td>
															<td align="left"><?php echo $row_regiao['regiao'];?> </td>
															<td>
															<?php  
															if($row_projeto['termino']=='0000-00-00' or $row_projeto['inicio']=='0000-00-00')
															{
																echo '<span  style="color:#F90;font-weight:bold;">Faltam informações sobre as datas</span>';	
															}else{
																echo '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
															}
															
															?> 
															
															
															</td>
														</tr>
																	
													<?php					 
														}
															
						}
					
					endwhile;//projeto
					
					
		 endwhile;//regiao
			?>
			</table> 
		
		</div>
		
		
		
		<div class="aviso_box">   
		
		 <!----------------------------------NOTAS FISCAIS-------------------------->
		 
			<table class="relacao">
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		  
			<tr class="titulo">
				<td colspan="3" align="center">NOTAS FISCAIS </td>
			</tr>
			
			 </table> 
			 
			<table class="relacao">
				<tr class="secao">			
					<td>Região</td>
					<td>Valor NF/ Carta Medição</td>
					<td>Repasse</td>
					<td>Diferença</td>
					<td>Ano</td>
				 </tr>
				
			 
			<?php
		
		$qr_regiao = mysql_query("SELECT * FROM regioes WHERE status_reg = '1' AND status = '1' AND id_master='$Master' ORDER BY regiao");
		
				while($row_regiao = mysql_fetch_assoc($qr_regiao)):
					
						if($row_regiao['id_regiao']=='15' or $row_regiao['id_regiao']=='36' or $row_regiao['id_regiao']=='37') continue; // 	
					
					$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '1'");
						while($row_projeto = mysql_fetch_assoc($qr_projeto)) :	
						
							for($ano=2010; $ano<=date('Y'); $ano++) :
						
								$qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1'   AND YEAR(data_emissao) = '$ano' ORDER BY data_emissao DESC");
								$num_notas = mysql_num_rows($qr_notas);
								
								if (empty($num_notas)) continue;
								
								while($row_notas = mysql_fetch_assoc($qr_notas)):
		
									// totalizadores por ano						
									$total_ano += $row_notas['valor'];
								
									$qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
										(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
										INNER JOIN entrada 
										ON notas_assoc.id_entrada = entrada.id_entrada
										WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano' AND entrada.status = 2 ;
										");
									
									$totalizador_repasse_anos += @str_replace(',','.',mysql_result($qr_total_anos,0));
									$totalizador_valor        += $row_notas['valor'];
									
									
								endwhile;
								
								$totalizador_diferenca_anos = ($totalizador_repasse_anos - $totalizador_valor);
		
					if(!empty($totalizador_diferenca_anos)) {
			
						if($totalizador_diferenca_anos<0) {
							echo '<tr style="background-color:#FEC0C1">';
						} else {
							echo '<tr class="linha_um">';
						}
								
						echo '<td align="left">'.$row_regiao['id_regiao'].' - '.$row_regiao['regiao'].'</td>
							  <td align="center" width="24%">'.'R$ '.number_format($totalizador_valor,2,',','.').'</td>
							  <td align="center" width="26%">'.'R$ '.number_format($totalizador_repasse_anos,2,',','.').'</td>
							  <td align="center" width="26%" >'.'R$ '.number_format($totalizador_diferenca_anos,2,',','.').'</td>
							  <td width="13%" align="center">'.$ano.'</td>
						</tr> ';
						
					}
					
					unset($totalizador_repasse_anos,$totalizador_diferenca_anos,$totalizador_valor);
									
							endfor;//ANOS
						endwhile;//projeto
				endwhile;//regiao
		
		
		?>
			  </table>  
		</div>      
			  
			  
		<div class="aviso_box">  
		
			
		<!---------OBRIGAÇÔES OSCIP--------------->
		
		<table class="relacao">
		  <tr>
			<td colspan="3">&nbsp;</td>
			</tr>
		  
			<tr class="titulo">
				<td colspan="3" align="center">OBRIGAÇÕES DA OSCIP</td>
			</tr>
			
			 </table> 
			<table class="relacao">
			
									
		<tr class="secao">			
			<td>Documento</td>
			<td>Descrição</td>
			<td>Status</td>
			
		</tr>
		 
			   <?php
		
			   $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$Master' AND status = 1 ORDER BY tipo_oscip ASC" ) or die (mysql_error());
			   while($row_oscip = mysql_fetch_assoc($qr_oscip)):
			  
				 
				$periodo = $row_oscip['periodo'];
				$data = $row_oscip['data_publicacao'];
				$n_periodo = $row_oscip['numero_periodo'];
				$data_termino_oscip = $row_oscip['oscip_data_termino'];
				
				
				 if( $periodo =='Indeterminado') continue;
		   
							list($ano,$mes,$dia) = explode('-',$data);
							
								
								if( $periodo=='Dias') 
								{
								//descobre a data de vencimento	
								$data_vencimento=mktime(0,0,0,$mes,$dia+$n_periodo,$ano);
								
								@$prazo_renovacao=$data_vencimento-5184000;
								
								$data_atual=mktime(0,0,0,date('m'),date('d'),date('Y'));
								
								
								if($prazo_renovacao<$data_atual and $data_vencimento>$data_atual)
								{		//quantidade de dias para a expiração
									$dif=($data_vencimento-$data_atual)/86400;
					
						echo '<tr class="novo">
								<td width="20%">'.$row_oscip['tipo_oscip'].'</td>
								<td width="50%">';
								
								if(empty($row_oscip['descricao']))
								
								{ echo '---------' ;} 
								else 
								{ echo $row_oscip['descricao'];}
								
								echo '</td>
								<td width="200%"><span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span></td>
								</tr>';	
									}
								}
								
								if( $periodo=='Meses') 
								{
								$data_vencimento=mktime(0,0,0,$mes+$n_periodo,$dia,$ano);
													@$prazo_renovacao=$data_vencimento-5184000;
											$data_atual=mktime(0,0,0,date('m'),date('d'),date('Y'));
													if($prazo_renovacao<$data_atual and $data_vencimento>=$data_atual)
													{		//quantidade de dias para a expiração
														$dif=($data_vencimento-$data_atual)/86400;
										
											echo '<tr class="novo">
											<td width="20%">'.$row_oscip['tipo_oscip'].'</td>
											<td width="50%">'.$row_oscip['descricao'].'</td>';
											
											if($dif==0)
																	{
																		echo '<td width="100%"><span style="color:#F60;font-weight:bold;">Expira hoje!</span></td>
													</tr>';
																	}else{
																		
																	echo '<td width="200%"><span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span></td>
													</tr>';
																	}	
														}
								}
								if( $periodo=='Anos') 
								{
									
								$data_vencimento=mktime(0,0,0,$mes,$dia,$ano+$n_periodo);
								@$prazo_renovacao=$data_vencimento-5184000;
													$data_atual=mktime(0,0,0,date('m'),date('d'),date('Y'));
													
													
													if($prazo_renovacao<$data_atual and $data_vencimento>$data_atual)
													{		//quantidade de dias para a expiração
														$dif=($data_vencimento-$data_atual)/86400;
										
											echo '<tr class="novo">
											<td width="20%">'.$row_oscip['tipo_oscip'].'</td>
											<td width="50%">'.$row_oscip['descricao'].'</td>';
																	if($dif==0)
																	{
																		echo '<td width="200%"><span style="color:#F60;font-weight:bold;">Expira hoje!</span></td>
													</tr>';
																	}else{
																		
																	echo '<td width="200%"><span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span></td>
													</tr>';
																	}	
											
								}
													///Expirados
														elseif($data_atual>$data_vencimento) {
														$cont = 0;
														
															
														//verifica se existe algum que esteja expirando			
														 $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$Master' AND id_oscip = '$row_oscip[tipo_oscip]' AND status = 1 ORDER BY tipo_oscip ASC" ) or die (mysql_error());
														  while($row_oscip_2= mysql_fetch_assoc($qr_oscip)):
														  
														  
														  	$periodo = $row_oscip_2['periodo'];
															$data = $row_oscip_2['data_publicacao'];
															$n_periodo = $row_oscip_2['numero_periodo'];
															$data_termino_oscip = $row_oscip_2['oscip_data_termino'];
																	
															$data_vencimento=mktime(0,0,0,$mes,$dia,$ano+$n_periodo);
															@$prazo_renovacao=$data_vencimento-5184000;
															$data_atual=mktime(0,0,0,date('m'),date('d'),date('Y'));
														
														  
														  
														  if($prazo_renovacao<$data_atual and $data_vencimento>$data_atual)
																{
																	$cont++;
																	
																}
																
														   endwhile;
															 
															 
															if($cont == 0) {
													
																	echo '<tr class="novo">
																	<td width="20%">'.$row_oscip['tipo_oscip'].'</td>
																	<td width="50%">';
																	
																	if(empty($row_oscip['descricao']))
																	
																	{ echo '---------' ;} 
																	else 
																	{ echo $row_oscip['descricao'];}
																	echo '<td><span  style="color:#F00;font-weight:bold;"> Expirado</span></td></tr>';
															}
															
															unset($cont);
								}
								
								
								
								
								
								}
								
					if( $periodo=='Período') 
								{
								
								  list($ano_2,$mes_2,$dia_2)=explode('-',$data_termino_oscip);
								 
								 
								$data_vencimento=mktime(0,0,0,$mes_2,$dia_2,$ano_2);
									 @$prazo_renovacao=$data_vencimento-5184000;
								$data_atual=mktime(0,0,0,date('m'),date('d'),date('Y'));
						
													if($prazo_renovacao<$data_atual and $data_vencimento>$data_atual)
													{		//quantidade de dias para a expiração
														$dif=($data_vencimento-$data_atual)/86400;
										
												echo '<tr class="novo">
												<td width="20%">'.$row_oscip['tipo_oscip'].'</td>
												<td width="50%">'.$row_oscip['descricao'].'</td>';
												
												if($dif==0)
																	{
																		echo '<td width="100%"><span style="color:#F60;font-weight:bold;">Expira hoje!</span></td>
													</tr>';
																	}else{
																		
																	echo '<td width="200%"><span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span></td>
													</tr>';
																	}	
											
														}
								}
					
		endwhile; //oscip
		  
		  ?>
		 
		 </table>
		 
		</div> 
        
        
		<!------------------------------PROJETOS SEM PUBLICAÇÃO---------------------->  
		  <?php
		
		///verifica publicação para cada projeto
		
		
		unset ($qr_projeto,$row_projeto);
		
		$a=0;
		$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master' AND status_reg = '1' ORDER BY regiao");
			while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
			
			if($row_projeto['id_regiao']=='15' or $row_projeto['id_regiao']=='36' or $row_projeto['id_regiao']=='37') continue; // 	
					
				$ano_pub = substr($row_projeto['data_assinatura'],0,4)+1;
				
				$qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$row_projeto[id_projeto]'  AND status_reg = 1 ORDER BY termino ASC");
				while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)) :
				
					$ano_fim = substr($row_subprojeto['termino'],0,4);
					
				endwhile;
				if(mysql_num_rows($qr_subprojeto)==0){
				
					$ano_fim = substr($row_projeto['termino'],0,4);
				}
				
				//a publicação do ano 2010, é publicada em 2011 e assim por diante
				for ($ano_oscip = $ano_pub;$ano_oscip <= date('Y') ;$ano_oscip++){
						
					if($ano_fim >= $ano_oscip)	{
						$qr_verifica = mysql_query("SELECT * FROM  obrigacoes_oscip WHERE id_projeto = '$row_projeto[id_projeto]' AND tipo_oscip = 'Publicação Anexo 1 em Jornal' AND  status = 1 AND YEAR(data_publicacao) = '$ano_oscip' ");
						$verifica_oscip = mysql_num_rows($qr_verifica);
						
						
						if($verifica_oscip == 0)
						{
										if($a==0)
										{
											echo '	<table class="relacao">
													<tr>
														<td colspan="4">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="4" class="titulo" align="center"> PROJETOS SEM PUBLICAÇÃO</td> 
													</tr>
													<tr  class="secao">
														<td>Projeto</td>';
														
										     if($i!=1){ echo '<td>Cadastrar </td>'; }
														echo '<td>Região</td>
															  <td>Ano</td>
													</tr>';
													  $a=1;
										}
								 
								 
								  echo '</tr>
								  
										<tr class="linha_um">
											<td align="left">'.$row_projeto['nome'].'</td>';
											
											 if($i!=1){
												 	 echo '<td><a href="adm_contratos/cadastro_oscip.php?m='.$link_master.'&id='.$row_projeto['id_projeto'].'&tp=Publicação Anexo 1 em Jornal" target="_blanck"><img src="../imagens/cadastro_oscip.jpg" width="20" heigth="20"/></a></td>';
											 }
											 
											echo '<td align="left">'.$row_projeto['regiao'].'</td>					
											<td align="center">'.($ano_oscip-1).'</td>
										</tr>';
								}
						
			}
		}
			endwhile;//projeto
		
			  
		  ?>     
					
		  </table>  
</div>
		
        
        
		<?php 
		//FIM DA CRIAÇÃO DO EMAIL
		
			if($i == 1){ ?>           
						  </table>  
						
						</div>
						</div>
						</body>
						</html>
								
						<?php	
						// Recebe o valor do buffer na variável $resultado
						$resultado = ob_get_contents();
						
						 
						//  encerra o buffer e limpa tudo que há nele
						ob_end_clean();
						 ob_clean();
						 
						$headers = "Content-type: text/html; charset=iso-8859-1";
						
						$qr_aviso = mysql_query("SELECT * FROM avisos WHERE id_master = '$Master' ORDER BY data_ultimo_aviso DESC");
						$row_aviso = mysql_fetch_assoc($qr_aviso);
						$verifica_aviso = mysql_num_rows($qr_aviso);
						
								
						
						
							list($ano,$mes,$dia) = explode('-',$row_aviso['data_ultimo_aviso']);
							
							$data_aviso = mktime(0,0,0,$mes,$dia+5,$ano);
							$data_hoje = mktime(0,0,0,date('m'),date('d'),date('Y'));
							
							
								
		
		}//fim if

endfor;

?>
