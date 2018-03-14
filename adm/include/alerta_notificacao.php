<?php 


?>
    
    
    <table class="relacao">
    
    <tr class="titulo" >
    	<td colspan="7">ALERTAS ADMINISTRATIVOS E JURÍDICOS</td>
    </tr>
        <tr class="secao_nova" >
            <td>TIPO</td>
            <td>Nº</td>
            <td>REGIÃO</td>
            <td>PROJETO</td>
            <td>RESPONSÁVEL</td>
            <td>DATA</td>
            <td></td>
        </tr> 
        
<?php

$qr_tipo = mysql_query("SELECT * FROM tipos_notificacoes WHERE 1");
while($row_tipos  = mysql_fetch_assoc($qr_tipo)):
  
  	$qr_notificacoes = mysql_query("SELECT * FROM notificacoes WHERE tipos_notificacoes_id = '$row_tipos[tipos_notificacoes_id]' AND notificacao_entregue = 0 AND notificacao_status = 1");
	while($row_notificacoes =  mysql_fetch_assoc($qr_notificacoes)):
	
	 $data_limite			 = explode('-', $row_notificacoes['notificacao_data_limite']);
	 $data_limite_segundos 	 = mktime(0,0,0,$data_limite[1], $data_limite[2], $data_limite[0]);
	 
     $data_limite_10 		 =  mktime(0,0,0,$data_limite[1], $data_limite[2]-10, $data_limite[0]);	 
	 $data_atual 			 =  mktime(0,0,0,date('m'), date('d'), date('Y'));
	 $diferenca 			 = ($data_limite_segundos - $data_atual ) / 86400;
	 
	
	///verifica se algum está expirando, expirado ou expira hoje
	if($diferenca<=10 ) {
				
				
						//nome regiao
						if($row_notificacoes['id_regiao'] == 'todos') {
							$nome_regiao =  $row_notificacoes['id_regiao'];
						} else {
							$nome_regiao  = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_notificacoes[id_regiao]' "),0);
							
						}
						
						//nome projeto	
						if($row_notificacoes['id_projeto'] == 'todos') {
							$nome_projeto =  $row_notificacoes['id_projeto'];
						} else {
							$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_notificacoes[id_projeto]' "),0);	
							
						}
						
						//NOme dos responsaveis
						 $qr_responsavel_assoc = mysql_query("SELECT * FROM notific_responsavel_assoc WHERE notificacoes_id = '$row_notificacoes[notificacao_id]'");
						while($row_responsavel = mysql_fetch_assoc($qr_responsavel_assoc)):
						
							$nome_responsavel[] = @mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_responsavel[funcionario_id]'"),0);
						
						endwhile;
						$class = (($i++ % 2) == 0)?'class="linha_um"': 'class="linha_dois"';
						
						?>	
						 <tr <?php echo $class;?>>
								<td><?php echo $row_tipos['tipos_notificacoes_nome']; ?></td>
								<td><?php echo $row_notificacoes['notificacao_numero']; ?></td>
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
	 
	  if( $data_atual > $data_limite_segundos) {
		 
		 ?>	
         <td><span  style="color:#F00;font-weight:bold;"> Expirado </span></td>        
        </tr>      
		 
	<?php 
		 
	 }else 
	 if( $data_limite_segundos == $data_atual) {
	 ?>	
    
        <td><span style="color:#F60;font-weight:bold;">Expira hoje!</span></td>        
    </tr>
	<?php 
		 
	 }else if($data_atual >= $data_limite_10  and $data_limite > $data_atual) {
	 ?>	
  
        <td><span style="color:#09C;font-weight:bold;">Expira em  <?php echo $diferenca; ?> dias!</span></td>        
    </tr>
	<?php 
		 
	 }
	 
	 unset($diferenca, $nome_responsavel);
	endwhile; //fim notificações

endwhile; //fim tipos
?> 
 </table>         
