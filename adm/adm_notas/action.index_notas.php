<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');


header("Content-Type: text/html; charset=ISO-8859-1",true);


$regiao = mysql_real_escape_string($_GET['regiao']);
$status =  mysql_real_escape_string($_GET['status']);
$acesso_exclusao = array(9,5);




  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT *
							       FROM projeto
							      WHERE id_regiao = '$regiao' AND status_reg = '$status' ORDER BY id_projeto ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	  
	  
	$qr_notas = mysql_query("SELECT * FROM notas 
										WHERE id_projeto = '$row_projeto[id_projeto]' 
										AND status = '1' 
										AND id_notas NOT IN(
											SELECT id_notas FROM notas INNER JOIN notas_assoc USING(id_notas)
												WHERE notas_assoc.id_entrada IN(
												SELECT id_entrada 
												FROM notas_assoc
												GROUP BY id_entrada
												HAVING COUNT(*) > 1
											)
											AND notas.id_projeto = '$row_projeto[id_projeto]' 
											AND notas.status = '1'
											)
											
										ORDER BY data_emissao DESC") or die(mysql_error());
	
	

	
	
	?>
	<table style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao"> 
				<tr>
					<td align="center" colspan="7" style="font-style: italic;text-align:center;"><?=$row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?><td>
			   </tr>
			   
				<?php 
					// LOOP DE NOTAS
					
					
					while($row_notas = mysql_fetch_assoc($qr_notas)):
						
						$ano=substr($row_notas['data_emissao'], 0,4);
						
						
						// parceiro
						$qr_parceiro = mysql_query("SELECT 	parceiro_id, parceiro_nome FROM parceiros WHERE parceiro_id = '$row_notas[id_parceiro]'");
						$row_parceiro = mysql_fetch_assoc($qr_parceiro);
						$nome_parceiro = $row_parceiro['parceiro_id'] . ' - ' . $row_parceiro['parceiro_nome'];  
						
						
						//totalizadores por ano
						
						$total_notas_anos = mysql_num_rows(mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' AND YEAR(data_emissao) = '$ano' "));
						$total_ano += str_replace(',','.',$row_notas['valor']);
						$a++;
						
							if($ano != $ano_anterior) {
							
								
								echo '<tr><td colspan="11">&nbsp;</td></tr>';
								echo '<tr class="novo2"><td colspan="11"><span style=" font-size:14px;">'.$ano.'</span></td></tr>';
								echo '<tr class="secao">
												   <td>Cod.</td>
												   <td width="18%">Parceiro</td>
												   <td>N&deg; </td>
												   <td>Data de Emiss&atilde;o</td>
												   <td>Valor NF / Carta Medi&ccedil;&atilde;o</td>
												   <td>Repasse<br>Parceiro</td>
												   <td>Diferenca</td>
												   <td>Status</td>
												   <td>Editar</td>
												   ';
							if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
												  
												  echo' <td>Excluir</td>';
							}
												   
												   echo'<td>&Uacute;ltima edi&ccedil;&atilde;o</td>
										</tr>';
								
							}
							
					
					$qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano'  AND entrada.status = '2';
								");
							$total_entrada_anos = (float) @mysql_result($qr_total_anos,0); 
							$totalizador_repasse_anos += $total_entrada_anos;
							
							
				$diferenca_anos = (float) $total_entrada_anos - str_replace(',','.',$row_notas['valor']) ;
						$total_diferenca_anos += $diferenca_anos; 
						
							
													
						$ano_anterior = $ano;
						
					
				?>
				<tr class="linha_<?php  if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
					<?php 
					
					
						$qr_entrada = mysql_query("SELECT * FROM entrada  WHERE id_entrada = '$row_notas[id_entrada]';");
						$row_entrada = mysql_fetch_assoc($qr_entrada);
						
					
						switch ($row_entrada['status']){
							case 0 :
								$status_entrada = "Entrada apagada.";
								break;
							case 1 :
								$status_entrada = "Entrada não confirmada.";
								break;
							case 2 :
								$status_entrada = "Entrada confirmada.";
								break;
						}
					?>
					<td><?= $row_notas['id_notas'] ?></td>
					<td><?= $nome_parceiro ?></td>
					<td><?= $row_notas['numero']; ?></td>
					<td><?=implode('/',array_reverse(explode('-',$row_notas['data_emissao']))); ?></td>
					<td>R$ <?php 
							$totalizador_valor += str_replace(',','.',$row_notas['valor']);
							echo number_format($row_notas['valor'],2,',','.'); 
							
							?>
					</td>
					<td>
						<a href="view_entrada.php?nota=<?php echo $row_notas['id_notas'];  ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" style="text-decoration:none;">
						R$ 
						<?php 
	
							$qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND entrada.status = '2';
								");
							$total_entrada = (float) @mysql_result($qr_total,0); 
							$totalizador_repasse += $total_entrada;
							echo number_format($total_entrada,2,',','.');
						?>
							<img src="../../novoFinanceiro/image/seta.gif" border="0"/>
						</a>
					</td>
					<td <?php 
					$vn = (float) str_replace(',','.',$row_notas['valor']);
						if($vn > $total_entrada){
							echo 'class="vermelho"';
						}else if($vn < $total_entrada){
							echo 'class="azul"';
							
						}
					?>>R$
						<?php 
						$diferenca = (float) $total_entrada - str_replace(',','.',$row_notas['valor']) ;
						$totalizador_diferenca += $diferenca; 
						echo number_format($diferenca,2,',','.');
						?>
					</td>
					
					<td>
					<?php 
					/*$qr_imagem	=	mysql_query("SELECT notas.id_notas, notas_files.id_file, notas_files.tipo, notas_files.status
										FROM notas
										INNER JOIN notas_files ON notas_files.id_notas = '$row_notas[id_notas]'");
					$row_imagem	=	mysql_fetch_assoc($qr_imagem);*/
					?>
					<a href="visializa_files.php?id_nota=<?php echo $row_notas['id_notas']; ?>" target="_blank" >                     
					<img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
					</a>
										 
					</td>
					<td>
						<a href="edicao.php?m=<?php echo $_GET['m'];?>&id=<?php echo $row_notas['id_notas'];?>">
				<img src="../../imagens/editar_projeto.png" alt="Editar nota">
				 </a> 
						
					</td>
					
					<?php 
					if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
						?>
					<td>
						<a href="exclusao.php?m=<?php echo $_GET['m'] ;?>&id=<?php  echo $row_notas['id_notas'];?>">
				<img src="../../imagens/lixo.gif" alt="Excluir nota"/></a>
					</td>
					
					<?php }?>
					
					<td>
					<?php
					
					  if($row_notas['ultima_edicao']!='0000-00-00 00:00:00'){
						  $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[editado_por]'");
					 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
					 $nome_func=explode(' ', $row_funcionario['nome']);
					 
					 $data=date('d/m/Y',strtotime($row_notas['ultima_edicao']));
					 echo 'Editado por:<br> '.$nome_func[0].' <br>em '.$data;
					 
					  } else {
						   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[id_funcionario]'");
					 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
					 $nome_func=explode(' ', $row_funcionario['nome']);
					 $data=date('d/m/Y',strtotime($row_notas['nota_data']));
					 
					 echo 'Cadastrado por '.$nome_func[0].' em '.$data;
					 
					  }
					
					?>
					
					
					</td>
				</tr>
				
			<?php	
			
				if($a==$total_notas_anos){
					?>
					
					
					<?php 
					
								
									echo '<tr class="secao">
												<td>Total:</td>
												<td></td>
												<td></td>
												<td></td>
												<td>'.'R$ '.number_format($total_ano,2,',','.').'</td>
												<td>'.'R$ '.number_format($totalizador_repasse_anos,2,',','.').'</td>
												<td>'.'R$ '.number_format($total_diferenca_anos,2,',','.').'</td>
												<td></td>
												<td></td>
												<td></td>';
												
							
				  if(in_array($_COOKIE['logado'],$acesso_exclusao)) { 
					
						echo '<td></td>';
					}
					echo '</tr>';
												
									unset($ano_anterior,$total_ano,$a,$totalizador_repasse_anos,$diferenca_anos,$total_diferenca_anos );
								}
			
			endwhile;// Notas
			
			?>
			<?php 
					# GATO
					$controle = 0;
					$qr_notas_dupli = mysql_query("SELECT * FROM notas INNER JOIN notas_assoc USING(id_notas)
												WHERE notas_assoc.id_entrada IN(
												SELECT id_entrada 
												FROM notas_assoc
												GROUP BY id_entrada
												HAVING COUNT(*) > 1
											)
											AND notas.id_projeto = '$row_projeto[id_projeto]' 
											AND notas.status = '1'
											ORDER BY notas_assoc.id_entrada
											");
					// Criando grupo
					$num_notas_dupli = mysql_num_rows($qr_notas_dupli);
					$array_grupo = array();
					while($rw_notas_dupli = mysql_fetch_assoc($qr_notas_dupli)):
						$array_grupo[$rw_notas_dupli['id_entrada']][] = $rw_notas_dupli;
					endwhile;
					?>
					<?php 
					foreach($array_grupo as $grupo_entrada => $grupo_notas ):
						foreach($grupo_notas as $row_notas):
						
					
					?>
					<?php 
					
					
					$ano=substr($row_notas['data_emissao'], 0,4);
						
						
						// parceiro
						$qr_parceiro = mysql_query("SELECT 	parceiro_id, parceiro_nome FROM parceiros WHERE parceiro_id = '$row_notas[id_parceiro]'");
						$row_parceiro = mysql_fetch_assoc($qr_parceiro);
						$nome_parceiro = $row_parceiro['parceiro_id'] . ' - ' . $row_parceiro['parceiro_nome'];  
						
						
						//totalizadores por ano
						
						$total_notas_anos = mysql_num_rows(mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' AND YEAR(data_emissao) = '$ano' "));
						$total_ano += str_replace(',','.',$row_notas['valor']);
						$a++;
						
							if($ano != $ano_anterior) {
							
								
								echo '<tr><td colspan="11">&nbsp;</td></tr>';
								echo '<tr class="novo2"><td colspan="11"><span style=" font-size:14px;">'.$ano.'</span></td></tr>';
								echo '<tr class="secao">
												   <td>Cod.</td>
												   <td width="18%">Parceiro</td>
												   <td>Nº</td>
												   <td>Data de Emissão</td>
												   <td>Valor NF / Carta Medição</td>
												   <td>Repasse<br>Parceiro</td>
												   <td>Diferenca</td>
												   <td>Status</td>
												   <td>Editar</td>
												   ';
							if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
												  
												  echo' <td>Excluir</td>';
							}
												   
												   echo'<td>Última edição</td>
										</tr>';
								
							}
							
					
					$qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano'  AND entrada.status = '2';
								");
							$total_entrada_anos = (float) @mysql_result($qr_total_anos,0); 
							$totalizador_repasse_anos += $total_entrada_anos;
							
							
				$diferenca_anos = (float) $total_entrada_anos - str_replace(',','.',$row_notas['valor']) ;
						$total_diferenca_anos += $diferenca_anos; 
						
							
													
						$ano_anterior = $ano;
						
					
				?>
			   
				<tr class="linha_<?php  if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
					<?php 
					
					
						$qr_entrada = mysql_query("SELECT * FROM entrada  WHERE id_entrada = '$row_notas[id_entrada]';");
						$row_entrada = mysql_fetch_assoc($qr_entrada);
						
					
						switch ($row_entrada['status']){
							case 0 :
								$status_entrada = "Entrada apagada.";
								break;
							case 1 :
								$status_entrada = "Entrada não confirmada.";
								break;
							case 2 :
								$status_entrada = "Entrada confirmada.";
								break;
						}
					?>
					<td><?= $row_notas['id_notas'] ?></td>
					<td><?= $nome_parceiro ?></td>
					<td><?= $row_notas['numero']; ?></td>
					<td><?=implode('/',array_reverse(explode('-',$row_notas['data_emissao']))); ?></td>
					<td>R$ <?php 
							$totalizador_valor += str_replace(',','.',$row_notas['valor']);
							echo number_format($row_notas['valor'],2,',','.'); 
							
							?>
					</td>
					<td>
	
						
						<?php 
	
							/*$qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND entrada.status = '2';
								");
							$total_entrada = (float) @mysql_result($qr_total,0); 
							$totalizador_repasse += $total_entrada;
							echo number_format($total_entrada,2,',','.');*/
						?>
							<img src="imagens/arow.png" border="0" width="18" height="18"/>
					   
					</td>
					<td <?php 
					$vn = (float) str_replace(',','.',$row_notas['valor']);
						if($vn > $total_entrada){
							echo 'class="vermelho"';
						}else if($vn < $total_entrada){
							echo 'class="azul"';
							
						}
					?>>R$
						<?php 
						$diferenca = (float) $total_entrada - str_replace(',','.',$row_notas['valor']) ;
						$totalizador_diferenca += $diferenca; 
						echo number_format($diferenca,2,',','.');
						?>
					</td>
					
					<td>
					<?php 
					/*$qr_imagem	=	mysql_query("SELECT notas.id_notas, notas_files.id_file, notas_files.tipo, notas_files.status
										FROM notas
										INNER JOIN notas_files ON notas_files.id_notas = '$row_notas[id_notas]'");
					$row_imagem	=	mysql_fetch_assoc($qr_imagem);*/
					?>
					<a href="visializa_files.php?id_nota=<?php echo $row_notas['id_notas']; ?>" target="_blank" >                     
					<img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
					</a>
							 
					</td>
					<td>
						<a href="edicao.php?m=<?php echo $_GET['m'];?>&id=<?php echo $row_notas['id_notas'];?>">
				<img src="../../imagens/editar_projeto.png" alt="Editar nota">
				 </a> 
						
					</td>
					
					<?php 
					if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
						?>
					<td>
						<a href="exclusao.php?m=<?php echo $_GET['m'] ;?>&id=<?php  echo $row_notas['id_notas'];?>">
				<img src="../../imagens/arow.png.gif" alt="Excluir nota"/></a>
					</td>
					
					<?php }?>
					
					<td>
					<?php
					
					  if($row_notas['ultima_edicao']!='0000-00-00 00:00:00'){
						  $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[editado_por]'");
					 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
					 $nome_func=explode(' ', $row_funcionario['nome']);
					 
					 $data=date('d/m/Y',strtotime($row_notas['ultima_edicao']));
					 echo 'Editado por:<br> '.$nome_func[0].' <br>em '.$data;
					 
					  } else {
						   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[id_funcionario]'");
					 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
					 $nome_func=explode(' ', $row_funcionario['nome']);
					 $data=date('d/m/Y',strtotime($row_notas['nota_data']));
					 
					 echo 'Cadastrado por '.$nome_func[0].' em '.$data;
					 
					  }
					
					?>
					
					
					</td>
				</tr>
	
			   <?php
					endforeach; ?>
					<tr>	
							<td colspan="5" align="right">Valor da entrada : </td>
							<td>R$
								<?php 
								$qr_total = mysql_query("SELECT REPLACE(valor,',','.') FROM 
								entrada 
								WHERE id_entrada = '{$row_notas['id_entrada']}';
								");
							$total_entrada = (float) @mysql_result($qr_total,0); 
							$totalizador_repasse += $total_entrada;
							echo number_format($total_entrada,2,',','.');
								//echo $row_notas['id_entrada']; ?>
							</td>
							<td colspan="3" align="center"></td>
						</tr>
					<?php 
					endforeach; #FIM GATO ?>
			<tr >
					<td width="10%" ></td>
					<td width="5%"></td>
					<td width="5%"></td>
					<td width="8%">Total do projeto:</td>
					<td width="15%"><?php echo 'R$ '.number_format($totalizador_valor,2,',','.');  ?></td>
					<td width="13%"><?php echo 'R$ '.number_format($totalizador_repasse,2,',','.'); ?></td>
					<td  width="15%"><?php echo 'R$ '.number_format($totalizador_diferenca,2,',','.'); ?></td>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					
					<td></td>
				  
				</tr>	
			</table>
		
		   
			</td>
		  </tr>
		</table>

	 <?php 
	 
	 endwhile;
		  $regiao_anterior = $row_projeto['id_regiao'];
		  $status_anterior = $row_projeto['status_reg'];
		 
		unset($totalizador_valor,$totalizador_repasse,$totalizador_diferenca);
			
		