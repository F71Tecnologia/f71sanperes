
<?php
include ("include/restricoes.php");
include('../conn.php');


if(isset($_GET['noti']) or isset($_GET['andamentos'])){
	
$andamentos_ids   = mysql_real_escape_string($_GET['andamentos']);
$notificacoes_ids = mysql_real_escape_string($_GET['noti']);



/////PROCESSOS	
if(!empty($andamentos_ids) ){
		$qr_andamentos = mysql_query("SELECT * FROM `processos_juridicos`
										INNER JOIN proc_trab_andamento ON
										`processos_juridicos`.proc_id   =  proc_trab_andamento.proc_id
										INNER JOIN processo_status 
										ON processo_status.proc_status_id =  proc_trab_andamento.proc_status_id
										AND andamento_realizado = 0
										WHERE proc_trab_andamento.andamento_id IN($andamentos_ids)" );
		if(mysql_num_rows($qr_andamentos) !=0 ){
		
		echo '<h3 class="titulo_aviso">PROCESSOS</h3>';
		echo '<table class="tabela2" width="100%" border="0">';
		echo '<tr class="secao_nova"  height="25">
				<td>Nº DO PROCESSO</td>
				<td>REGIÃO</td>
				<td>NOME</td>
				<td width="30%">STATUS</td>				
				<td>DATA</td>
				<td width="5%">HORÁRIO</td>
				<td></td>
				
			</tr>';	
		
										
		while($row_andamento = mysql_fetch_assoc($qr_andamentos)):
			
			$qr_n_processo = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$row_andamento[proc_id]' ORDER BY n_processo_ordem ASC");
			while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
			
				$n_processos[] = $row_n_processo['n_processo_numero'];
			
			endwhile;
				
			$nome_regiao = @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_andamento[id_regiao]'"),0);
			$nome_status = mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]'"),0);
			
		
			if(($i++ % 2) == 0){ $linha_class = 'linha_um'; } else {  $linha_class = 'linha_dois'; };	
			
			echo '	<tr class="'.$linha_class.'">
					<td>'.@implode(', ',$n_processos).'</td>
					<td>'.htmlentities($nome_regiao).'</td>';
					
					if($row_andamento['proc_tipo_id'] ==  1) {
						echo    '<td><a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_andamento['proc_id'].'" title="Ver processo" class="link_nome"> '.htmlentities($row_andamento['proc_nome']).'&nbsp;&nbsp; <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"/></a></td>';
					} else {
						echo	'<td><a href="outros_processos/dados_processo/ver_processo.php?id_processo='.$row_andamento['proc_id'].'" title="Ver processo" class="link_nome">'.htmlentities($row_andamento['proc_nome']).'</a>&nbsp;&nbsp;<img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"/></td>';
					}
					
					
				
					echo '<td>'.htmlentities($nome_status).'</td>
					<td>'.implode('/',array_reverse(explode('-',$row_andamento['andamento_data_movi']))).'</td>
					<td>'.substr($row_andamento['andamento_horario'], 0,5).'</td>
					<td>';
					
					
					?>
					 <a href="action.realizado.php?id=<?php echo $row_andamento['andamento_id']; ?>"  onclick="	if(!confirm('Definir como realizado?')){ return false; }"class="realizado"> REALIZADO </a>
					<?php
                   
					  echo '  </td>
				</tr>';	
				
		unset($n_processos);
		endwhile;
		}
		
		echo '</table>
		<br>';
		}
		
}		
	



		
//////////////NOTIFICAÇÕES			
if(!empty($notificacoes_ids) ){
		$qr_notificacoes = mysql_query("SELECT * FROM notificacoes WHERE notificacao_id IN($notificacoes_ids)  AND notificacao_entregue = 0");
		if(mysql_num_rows($qr_notificacoes) !=0) {
	  		
 		echo '<h3  class="titulo_aviso">NOTIFICAÇÕES</h3>';
	  		echo '<table class="tabela2"  width="100%" border="0">
	  			<tr class="secao_nova">
	  				<td>TIPO</td>
	  				<td>NÚMERO</td>
	  				<td>REGIÃO</td>
	  				<td>PROJETO</td>
	  				<td>RESPONSÁVEL</td>
					<td></td>
	  			</tr>';
	  		
	  		
	  		while($row_notificacao = mysql_fetch_assoc($qr_notificacoes)):
	  		
	  		$tipo  = mysql_result(mysql_query("SELECT * FROM tipos_notificacoes WHERE tipos_notificacoes_id = '$row_notificacao[tipos_notificacoes_id]'"),0);
	  		$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_notificacao[id_regiao]'"),0);
	  		
	  		if($row_notificacao['id_projeto'] != 'todos') {
	  			$nome_projeto  =  mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$row_notificacao[id_projeto]'"),0);
	  		} else {
	  			$nome_projeto = 'todos';
	  		}
	  		
	  		 $qr_respon = mysql_query("SELECT * FROM notific_responsavel_assoc WHERE notificacoes_id = '$row_notificacao[notificacao_id]'");
	  		 while($row_respon = mysql_fetch_assoc($qr_respon)):
	  		 
	  			$nome_responsavel[] = mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$row_respon[funcionario_id]'"),0); 
	  		
	  		 endwhile;
	  		
	  		
	  		if(($i++ % 2) == 0){ $linha_class = 'linha_um'; } else {  $linha_class = 'linha_dois'; };
	  		echo '
	  			<tr class="'.$linha_class.'">
	  				<td>'.$tipo.'</td>
	  				<td>'.$row_notificacao['notificacao_numero'].'</td>
					<td>'.htmlentities($nome_regiao).'</td>
	  				<td><a href="notificacao/editar.php?id_noti='.$row_notificacao['notificacao_id'].'"> '.htmlentities($nome_projeto).'
					&nbsp;&nbsp;<img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"/><a/></td>	  				
	  				<td>'.@implode(', ',$nome_responsavel).'</td>
					<td><a href="action.confirma.php?id_noti='.$row_notificacao['notificacao_id'].'" class="botao"> CONFIRMAR</a></td>
	  			</tr>';
	  		
	  		
	  		endwhile;
	  		echo '</table> 
			<br>';
		}
		
		


		
}												

			

	
if(!empty($andamentos_ids) ) {	

		$qr_andamentos = mysql_query("SELECT * FROM `processos_juridicos`
										INNER JOIN proc_trab_andamento ON
										`processos_juridicos`.proc_id   =  proc_trab_andamento.proc_id
										INNER JOIN processo_status 
										ON processo_status.proc_status_id =  proc_trab_andamento.proc_status_id
										AND andamento_realizado = 1
										WHERE proc_trab_andamento.andamento_id IN($andamentos_ids)" ) or die(mysql_error());

		if(mysql_num_rows($qr_andamentos) !=0 ){
		
		echo '<h3 class="titulo_aviso">REALIZADOS</h3>';
		echo '<table class="tabela2" width="100%" border="0">';
		echo '<tr class="secao_nova">
				<td>Nº DO PROCESSO</td>
				<td>REGIÃO</td>
				<td>NOME</td>
				<td>STATUS</td>				
				<td>DATA</td>
				<td>HORÁRIO</td>
			</tr>';	
		
										
		while($row_andamento = mysql_fetch_assoc($qr_andamentos)):
			
			$qr_n_processo = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$row_andamento[proc_id]' ORDER BY n_processo_ordem ASC");
			while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
			
				$n_processos[] = $row_n_processo['n_processo_numero'];
			
			endwhile;
				
			$nome_regiao = @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_andamento[id_regiao]'"),0);
			$nome_status = mysql_result(mysql_query("SELECT proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]'"),0);
			
		
			if(($i++ % 2) == 0){ $linha_class = 'linha_um'; } else {  $linha_class = 'linha_dois'; };	
			
			echo '	<tr class="'.$linha_class.'">
					<td>'.@implode(', ',$n_processos).'</td>
					<td>'.htmlentities($nome_regiao).'</td>';
					
					if($row_andamento['proc_tipo_id'] ==  1) {
						echo    '<td><a href="processo_trabalhista/dados_trabalhador/ver_trabalhador.php?id_processo='.$row_andamento['proc_id'].'" title="Ver processo" class="link_nome"> '.htmlentities($row_andamento['proc_nome']).'&nbsp;&nbsp; <img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"/></a></td>';
					} else {
						echo	'<td><a href="outros_processos/dados_processo/ver_processo.php?id_processo='.$row_andamento['proc_id'].'" title="Ver processo" class="link_nome">'.htmlentities($row_andamento['proc_nome']).'</a>&nbsp;&nbsp;<img src="../rh/folha/sintetica/seta_um.gif" width="8" height="8"/></td>';
					}
					
					
				
					echo '<td>'.htmlentities($nome_status).'</td>
					<td>'.implode('/',array_reverse(explode('-',$row_andamento['andamento_data_movi']))).'</td>
					<td>'.substr($row_andamento['andamento_horario'], 0,5).'</td>
					
				</tr>';	
				
		unset($n_processos);
		endwhile;
		}
		
		echo '</table>';
		}
	


?>