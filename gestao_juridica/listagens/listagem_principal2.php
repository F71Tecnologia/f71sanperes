<?php 
include("include/restricoes.php");
?>
<script type="text/javascript">
$(function(){


$('.mostrar_regiao').click(function(){

var display = $(this).next().css('display');
var elemento = $(this).next();

	if(display == 'none'){
		
		$('.tabela2').hide();
		
		elemento.fadeIn();
		
	} else {
	
		elemento.css('display','none');
	}
	
});


})

</script> 
<style>
a.mostrar_regiao{
	
text-decoration:none;
width:100%;
heigth:35px;
padding: 10px 0 10px 0 ;
border:1px solid    #CFCFCF;
/*background-color: #F8F8F8;*/
display:block;	
text-align:center;
margin-top:3px;
font-size:14px;
color:  #616161;
font-weight:300;
margin-left:2px;
margin-bottom: 5px;


}
a.mostrar_regiao:hover{
	
	background-color:#C9C9C9;
}
.selecionado{
	
	background-color:#C9C9C9;
	
}
</style>
 
 
 <?php
 

		$qr_regiao = mysql_query("SELECT * FROM regioes
									INNER JOIN  funcionario_regiao_assoc
									ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
									WHERE funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' AND regioes.id_master = '$id_master'") or die(mysql_error());
		
	
			
		while($row_regiao = mysql_fetch_assoc($qr_regiao)):
		
		
		///verifica existencia de processos na região	
		  $qr_processo = mysql_query("SELECT * FROM processos_juridicos 
		  							  INNER JOIN proc_trab_andamento 
									  ON processos_juridicos.proc_id = proc_trab_andamento.proc_id
									  WHERE processos_juridicos.id_regiao = '$row_regiao[id_regiao]' AND processos_juridicos.status = 1 AND   proc_trab_andamento.andamento_status = 1 AND (proc_trab_andamento.proc_status_id <=	 9 or  proc_trab_andamento.proc_status_id IN(23, 24)) ") or die(mysql_error());
		 //////////  
		  if(mysql_num_rows($qr_processo) != 0) {
			
			 
			if($row_regiao['id_regiao'] != $regiao_anterior) {    echo '<a href="#" class="mostrar_regiao">'.$row_regiao['regiao'].'</a>';  }
		 ?>
		 
		<table width="100%" class="table table-striped table-hover text-sm valign-middle" style="display:none;margin-bottom:10px;" > 
		 
		 <?php
		$qr_status = mysql_query("SELECT * FROM processo_status WHERE  proc_status_id <= 9  or  proc_status_id IN(23, 24) ORDER by ordem ASC");
			 while ($row_status = mysql_fetch_assoc($qr_status)):
		
		
			  $mostrar = 0;
			  
			  $qr_processo = mysql_query("SELECT *,
                            (SELECT nome FROM processos_juridicos_nomes WHERE processos_juridicos_nomes.proc_id = processos_juridicos.proc_id ORDER BY nome DESC LIMIT 1) proc_nome2
                            FROM processos_juridicos WHERE id_regiao = '$row_regiao[id_regiao]' AND status = 1") or die(mysql_error());
			
			 	while($row_processo = mysql_fetch_assoc($qr_processo)):		
			 		
						
				  $qr_andamento = mysql_query("SELECT MAX(proc_status_id) as total,andamento_data_movi FROM proc_trab_andamento WHERE  proc_id= '$row_processo[proc_id]' AND andamento_status = 1") or die(mysql_error());
				 $row_andamento = mysql_fetch_assoc($qr_andamento);
				 
				 
				 if($row_andamento['total'] != $row_status['proc_status_id']) continue;
				 
				 if(($row_status['proc_status_id'] != $status_id_anterior) and $row_status['proc_status_id'] != 2) {	
						   
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
								<td width="180">N<code>&deg;</code> DO PROCESSO</td>
							   <td width="100">DATA DA DISTRIBUI&Ccedil;&Atilde;O</td> 
							   <td width="300">NOME</td>
							   <td width="150">PROCESSO</td>
							   <td width="60">EDITAR</td>
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
										<td  align="center">*<?php echo implode('/', array_reverse(explode('-',$row_andamento['andamento_data_movi'])))?></td>
										<td  align="left">
										
										<a href="/processo_trabalhista/dados_trabalhador.php?id_processo=<?php echo $row_processo['proc_id'];?>"> </a>
										<?php
										//CONDIÇÃO PARA VER A FICHA DOS PROCESSOS
										if($row_processo['proc_tipo_id'] == 1 ) {
													
													if($row_processo['id_autonomo'] != 0 ) {
														
													echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_processo['proc_id'].'" class="link_nome">';if(!empty($row_processo['proc_nome'])){ echo $row_processo['proc_nome']; } else { echo $row_processo['proc_nome2'];} echo '
													 <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8">
													 </a>';
													
													} else {
													echo' <a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_processo['proc_id'].'" class="link_nome">';if(!empty($row_processo['proc_nome'])){ echo $row_processo['proc_nome']; } else { echo $row_processo['proc_nome2'];} echo ' <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"></a>';
													}
														
										} else {
									
                                                                echo '<a href="outros_processos/dados_processo/ver_processo.php?id_processo='.$row_processo['proc_id'].'" class="link_nome">';if(!empty($row_processo['proc_nome'])){ echo $row_processo['proc_nome']; } else { echo $row_processo['proc_nome2'];} echo '</a> <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"></a>';  
									 
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
			
		  
			<?php
		   		  endwhile; ///LOOP ROW_STATUS
				 
			?>
		    <tr>
		    <td colspan="6">&nbsp;</td>
		    </tr>
		    </table> 
		    
		    
		    <?php 
			unset($status_id_anterior); 
			$regiao_anterior = $row_regiao['id_regiao'];
		 } 
		endwhile; 
		
?>
		  
           
	 
          
         
	
