<table class="table table-striped table-hover text-sm valign-middle">

<tr class="titulo" >
	<td colspan="9"><h5>ALERTAS ADMINISTRATIVOS E JURÍDICOS</h5></td>
</tr>
<tr class="titulo" >
    <td>TIPO</td>
    <td>Nº</td>
    <td></td>
    <td>REGIÃO</td>
    <td>PROJETO</td>
    <td>RESPONSÁVEL</td>
    <td>DATA</td>
    <td colspan="2"></td>
</tr> 
<?php
  
  	$qr_notificacoes = mysql_query("SELECT * , DATEDIFF( notificacao_data_limite, CURDATE( )) as diferenca_dias FROM notificacoes WHERE notificacao_status = '1' AND notificacao_entregue = 0 ");
	while($row_notificacoes =  mysql_fetch_assoc($qr_notificacoes)):
	
	$diferenca     = $row_notificacoes['diferenca_dias'];
	$prazo_entrega = 10; 
	
	///verifica se algum está expirando, expirado ou expira hoje
	if($diferenca<=  $prazo_entrega ) {
				
				
						//nome do tipo
						$nome_tipo = mysql_result(mysql_query("SELECT tipos_notificacoes_nome FROM tipos_notificacoes WHERE tipos_notificacoes_id ='$row_notificacoes[tipos_notificacoes_id]'"),0);
				
						//nome regiao
						if($row_notificacoes['id_regiao'] == 'todos') {
							$nome_regiao =  $row_notificacoes['id_regiao'];
						} else {
							$nome_regiao  = @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_notificacoes[id_regiao]' "),0);
							
						}
						
						//nome projeto	
						if($row_notificacoes['id_projeto'] == 'todos') {
							$nome_projeto =  $row_notificacoes['id_projeto'];
						} else {
							$nome_projeto = @mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_notificacoes[id_projeto]' "),0);	
							
						}
						
						//NOme dos responsaveis
						 $qr_responsavel_assoc = mysql_query("SELECT * FROM notific_responsavel_assoc WHERE notificacoes_id = '$row_notificacoes[notificacao_id]'");
							while($row_responsavel = mysql_fetch_assoc($qr_responsavel_assoc)):
							
								$nome_responsavel[] = @mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_responsavel[funcionario_id]'"),0);
							
							endwhile;
						$class = (($i++ % 2) == 0)?'class="linha_um"': 'class="linha_dois"';
						
						?>	
						 <tr <?php echo $class;?>>
								<td><?php echo $nome_tipo; ?></td>                               
								<td><?php echo $row_notificacoes['notificacao_numero']; ?></td>
                                 <td><a href="notificacao/action.ver_anexos.php?id_noti=<?php echo $row_notificacoes['notificacao_id']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )"> <img src="../imagens/ver_anexo.gif" width="20" height="20"/> </a>
                                </td>
								<td><?php echo $nome_regiao; ?></td>
								<td><?php echo $nome_projeto; ?></td>
								<td> 
								  <?php
									if(sizeof($nome_responsavel) == 1){
										
										echo $nome_responsavel[0];
									} else {
										echo @implode(', ',$nome_responsavel);
									}
									?> 
								</td>
								<td><?php echo implode('/', array_reverse(explode('-', $row_notificacoes['notificacao_data_limite']))); ?></td>
	<?php	
	}
	 
	  if( $diferenca <0) {
		 
		 ?>	
         <td><span  style="color:#F00;font-weight:bold;"> Expirado </span></td>   
         <td><a href="action.confirma.php?id_noti=<?php echo $row_notificacoes['notificacao_id'];?>" class="botao"> Confirmar</a></td>       
        </tr>      
		 
	<?php 
		 
	 }else 
	 if( $diferenca ==0) {
	 ?>	
    
        <td><span style="color:#F60;font-weight:bold;">Expira hoje!</span></td>      
        <td><a href="action.confirma.php?id_noti=<?php echo $row_notificacoes['notificacao_id'];?>" class="botao" > Confirmar</a></td>       
    </tr>
	<?php 
		 
	 }else if(($diferenca > 0) and ($diferenca <=$prazo_entrega)) {
	 ?>	
  
        <td><span style="color:#09C;font-weight:bold;">Expira em  <?php echo $diferenca; ?> dias!</span></td>
        <td><a href="action.confirma.php?id_noti=<?php echo $row_notificacoes['notificacao_id'];?>" class="botao">
         Confirmar</a></td>              
    </tr>
	<?php 
		 
	 }
	 
	 unset($diferenca, $nome_responsavel);
	endwhile; //fim notificações

?> 
<tr><td colspan="8">&nbsp;</td></tr>
 </table>         
