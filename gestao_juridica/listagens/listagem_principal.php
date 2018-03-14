<?php 
include("include/restricoes.php");
?>
<table width="100%" class="tabela2">
 <?php
 
$qr_status = mysql_query("SELECT * FROM processo_status WHERE  1 ORDER by ordem ASC");
	 while ($row_status = mysql_fetch_assoc($qr_status)):


	  $mostrar = 0;
	  
	  
	  $qr_processo = mysql_query("SELECT *,
                            (SELECT nome FROM processos_juridicos_nomes WHERE processos_juridicos_nomes.proc_id = processos_juridicos.proc_id ORDER BY proc_id,id_proc_jur_nome LIMIT 1) AS proc_nome
                            FROM processos_juridicos WHERE 1 ") or die(mysql_error());
	
	 	while($row_processo = mysql_fetch_assoc($qr_processo)):		
	 		
				
		 $qr_andamento = mysql_query("SELECT MAX(proc_status_id) as total,andamento_data_movi FROM proc_trab_andamento WHERE  proc_id= '$row_processo[proc_id]' AND andamento_status = 1") or die(mysql_error());
		 $row_andamento = mysql_fetch_assoc($qr_andamento);
		 
		 
		 if($row_andamento['total'] != $row_status['proc_status_id']) continue;
		 
		 if($row_status['proc_status_id'] != $status_id_anterior ) {	
				   
					 $mostrar=1;
				
					?> 
					<tr>
					  <td class="titulo_tabela" colspan="5">
				   		<span style="float:left;"> <img src="../imagens/status_<?php echo $row_status['proc_status_id'];?>.png"  width="10" height="10"/></span>
                        
					  <div class="sombra1" style="float:left; margin-left:4px;"> <?php echo $row_status['proc_status_nome'];?>                                                
							  <div class="texto">  <?php echo $row_status['proc_status_nome'];?></div>             
							 </div>
					  </td>
					</tr>
					
					  <tr class="secao">
						<td width="130">N<code>&deg;</code> DO PROCESSO</td>
					   <td width="100">DATA DA DISTRIBUIÇÃO</td> 
					   <td>NOME</td>
					   <td >PROCESSO</td>
					   <td>EDITAR</td>
					 </tr>
				  
				  <?php  
				  } 
				
	
			  
			  ?>
			  
			  <tr class="<?php if($i % 2 == 0) { echo 'linha_um'; } else { echo 'linha_dois'; }?>">
							
								<td align="left">
								<?php 
								$qr_n_processo = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$row_processo[proc_id]' ORDER BY n_processo_ordem" ) or die(mysql_error());
								while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
								
								  echo '('.$row_n_processo['n_processo_ordem'].') '.$row_n_processo['n_processo_numero'].'<br>';
								
								
								endwhile;
								?>
                                
                                </td>
								<td  align="center"><?php echo implode('/', array_reverse(explode('-',$row_andamento['andamento_data_movi'])))?></td>
								<td  align="left">
								
								<a href="/processo_trabalhista/dados_trabalhador.php?id_processo=<?php echo $row_processo['proc_id'];?>"> </a>
								<?php
								//CONDIÇÃO PARA VER A FICHA DOS PROCESSOS
								if($row_processo['proc_tipo_id'] == 1 ) {
											
											if($row_processo['id_autonomo'] != 0 ) {
												
											echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_processo['proc_id'].'" class="link_nome">'.$row_processo['proc_nome'].'
											 <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8">
											 </a>';
											
											} else {
											echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_processo['proc_id'].'" class="link_nome"> '.$row_processo['proc_nome'].' <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"></a>';
											}
												
								} else {
							
						echo '<a href="outros_processos/dados_processo/ver_processo.php?id_processo='.$row_processo['proc_id'].'" class="link_nome">'.$row_processo['proc_nome'].'</a> <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"></a>';  
							
						}		
											
											
											
								?>
								
								</td>
								<td align="center">
                                	<?php
										$qr_tipo = mysql_query("SELECT proc_tipo_nome FROM processo_tipo WHERE proc_tipo_id = '$row_processo[proc_tipo_id]'");
										$row_tipo = mysql_fetch_assoc($qr_tipo); 
										
										echo $row_tipo['proc_tipo_nome'];
	;								?>
                                
                                
                                </td>     
								<td align="center">
								<?php 
								
								//CONDIÇÃO PARA VER A EDIÇÃO DOS PROCESSOS
								if($row_processo['proc_tipo_id'] == 1 ) {
									
													if($row_processo['proc_tipo_contratacao'] != 2 ) {
													
														 echo ' <a href="processo_trabalhista/edit_processo.php?id_processo='.$row_processo['proc_id'].'">  <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a>';          
												
													}else {
														 echo ' <a href="processo_trabalhista/edit_processo.php?id_processo='.$row_processo['proc_id'].'">  <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a>';          
													
													}
										
								} else {
							
										echo '<a href="outros_processos/edit_processo.php?id_processo='.$row_processo['proc_id'].'"> <img src="../imagens/editar_projeto.png"  title="Editar" width="25" height="25"/></a>';  
							
						}
								?>
								</td>
			 </tr> 
         
 
   <?php
   					 $status_id_anterior = $row_status['proc_status_id'];	 
   				 endwhile; ///LOOP ROW_PROCESSO 
				 
			 ?>
	
    <tr>
           	  <td>&nbsp; </td>
       	 </tr>		 
	<?php
   		  endwhile; ///LOOP ROW_STATUS
		  ?>
		  
           
	 
          
         
	</table> 
