<?php
$servidor = 'localhost';
$usuario  = 'ispv_consulta';
$senha    = 'Z2~3LXZ~Zrn5';
$banco    = 'ispv_netsorrindo';

// Criando Conexão
$conn = mysql_connect($servidor, $usuario, $senha) or die ('Não pude conectar ao banco de dados');

// Selecionando o Banco de Dados
mysql_select_db("$banco") or die('Não pude selecionar o banco de dados');
///////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////


$mes = date('m');
$ano = date('Y');
$dias_aviso = 3; ///Dias antecedentes a data do processo(AVISO POR e-mail)

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario   = mysql_fetch_array($query_funcionario);


$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);$meses = array(  1 => 'Janeiro',
				 2 => 'Fevereiro',
				 3 => 'Mar&ccedil;o',
				 4 => 'Abril', 
				 5 => 'Maio', 
				 6 => 'Junho', 
				 7 => 'Julho', 
				 8 => 'Agosto', 
				 9 => 'Setembro', 
				 10 => 'Outubro', 
				 11 => 'Novembro',
				 12 => 'Dezembro');

////VERIFICANDO ANDAMENTO NO MêS
$qr_processo 		= mysql_query("SELECT * FROM processos_juridicos 
									WHERE status = 1 ");
									
									
while($row_processo = mysql_fetch_assoc($qr_processo)):

	$qr_andamento = @mysql_query("SELECT andamento_id, andamento_data_movi, proc_id, proc_status_id, MONTH( andamento_data_movi ) AS mes, YEAR( andamento_data_movi ) AS ano, 	andamento_realizado
								FROM proc_trab_andamento
								WHERE proc_id = '$row_processo[proc_id]'
								AND andamento_status = '1'
								AND proc_status_id NOT IN(8,9,10,11,22)
								GROUP BY proc_status_id DESC LIMIT 1"	);
	if(mysql_num_rows($qr_andamento) != 0) {	
	
	
		$row_andamento    = mysql_fetch_assoc($qr_andamento);
	
		$dt_hoje_segundos 	   = mktime(0,0,0,date('m'), date('d'), date('Y'));	
		$dt_andamento_segundos = explode('-',$row_andamento['andamento_data_movi']);
		
		$dt_vencimento 		   = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2],$dt_andamento_segundos[0]);
		$dt_andamento_segundos = mktime(0,0,0,$dt_andamento_segundos[1],$dt_andamento_segundos[2]-$dias_aviso,$dt_andamento_segundos[0]);
			
		
		///EXPIRADOS
		if($dt_hoje_segundos > $dt_vencimento and  $row_andamento['proc_status_id'] != 1 ) {
			
			$andamento_aviso_email['proc_id_expirados'][]    = $row_processo['proc_id'];	
			$andamento_aviso_email['andamentos_expirados'][] = $row_andamento['andamento_id'];		  	
		}
		
		
		
		
		if(($row_andamento['mes'] == (int)$mes) and ($row_andamento['ano'] == $ano)) {
			
				if($dt_hoje_segundos >= $dt_andamento_segundos and $dt_vencimento >= $dt_hoje_segundos and  $row_andamento['proc_status_id'] != 1 ){
					
					$andamento_aviso_email['proc_id'][]   = $row_processo['proc_id'];
					$andamento_aviso_email['andamento'][] = $row_andamento['andamento_id'];
				} 
				
				$andamento[$row_andamento['andamento_data_movi']][] = $row_andamento['andamento_id'];
				$realizado[$row_andamento['andamento_data_movi']][]   = $row_andamento['andamento_realizado'];
		}
		
		
		if($row_andamento['proc_status_id'] == 1 ) {
			
				$andamento_aviso_email['processos_abertos_proc_id'][] 	   = $row_processo['proc_id'];	
				$andamento_aviso_email['processos_abertos_andamento_id'][] = $row_andamento['andamento_id'];
		}
		
		
		
	}

endwhile;


///Usuários que recebem o e-mail				
$array_funcionario = array('5'    => 'fabio.souza@sorrindo.org',
							'65' => 'fabricio@sorrindo.org',
							'88' => 'fernanda.souza@sorrindo.org',
							'104' => 'alexandre@sorrindo.org',
							'80' => 'catia@sorrindo.org',
							'77' => 'vitorio@sorrindo.org',
							'24'  => 'regiane@sorrindo.org',
							'106' => 'renata.bologinini@sorrindo.org',
							'87'  => 'anderson@sorrindo.org');
					
				
foreach($array_funcionario as $usuario => $email) :
				
	ob_start();		
	
	?>

<html>
<head>
<style>

.titulo1{
	background-color:#FF8484;
}
.titulo2{
	background-color: #C1EBFF;
}

.titulo3{
	background-color:   #FFC5A8;
}

.mes_titulo{
text-align:left;	
width: 300px;
height:30px;;
border-top: 1px solid #BCBCBC;
border-right: 1px solid #BCBCBC; 
border-bottom: 3px solid #BCBCBC;
border-left: 3px solid #BCBCBC;
padding-left:3px;
text-transform:uppercase;
}

.ano_titulo{
text-align:center;
background-color:#F2F2F2;
color:#000;	
margin-top:20px;
}

table{
width:98%;

}
tr.titulo{
background-color: #B0B0B0;
color: #FFF;
text-align:center;
}

tr.linha_um{
background-color:   #EFEFEF;
font-size:10px;
}

tr.linha_dois{
    background-color: #E2E2E2;
    font-size:10px;
}
tr h3{
font-weight:400;
text-align:center;
background-color:#DDD;

margin:25px 0px 0px 0px;
}
</style>


</head>
		<body>    
        
        
          <div style="text-align:center;width:100%;heigth:auto;">   
                        	<img src="<?php echo  'http://'.$_SERVER['HTTP_HOST']?>/intranet/imagens/logomaster<?=$id_master?>.gif" width="110" height="79">
           </div>
           <h3 style="text-align:center; background-color:#FFF; fon">Avisos do jur&iacute;dico <br> <span style="font-size:12px; font-weight:300;">( Pr&oacute;ximos eventos ) </span> </h3>          
<?php
				$proc_ids 				    		= @implode(',',$andamento_aviso_email['proc_id']);
				$andamentos_ids 		   			= @implode(',',$andamento_aviso_email['andamento']);
				$proc_ids_expirados 	   			= @implode(',',$andamento_aviso_email['proc_id_expirados']);
				$andamentos_ids_expirados  		    = @implode(',',$andamento_aviso_email['andamentos_expirados']);
				$processos_abertos_proc_id 			= @implode(',',$andamento_aviso_email['processos_abertos_proc_id']);
				$processos_abertos_andamento_id 	= @implode(',',$andamento_aviso_email['processos_abertos_andamento_id']);	
					
				$hoje 					  = date('Y-m-d');
				$avisos 				  = array( 2 => 'PR&Oacute;XIMOS PROCESSOS',1 => 'EXPIRADOS', 3 => 'PROCESSOS EM ABERTO');
	
    		
				
			foreach($avisos as $tipo_aviso => $nome_aviso):
								
						$proximo_dia = date('Y-m-d',mktime(0,0,0,date('m'),date('d')+1, date('Y')));
						
					
						switch($tipo_aviso)  {
						case 1:
						  		
								$qr_processo2  = mysql_query("SELECT * FROM processos_juridicos 
															INNER JOIN proc_trab_andamento
															ON proc_trab_andamento.proc_id = processos_juridicos.proc_id
															INNER JOIN funcionario_regiao_assoc
															ON funcionario_regiao_assoc.id_regiao = processos_juridicos.id_regiao
															 WHERE processos_juridicos.proc_id IN ($proc_ids_expirados) 							 
															AND  proc_trab_andamento.andamento_id IN  ($andamentos_ids_expirados) 
															AND id_funcionario = '$usuario'
															AND andamento_realizado = 0
															ORDER BY `proc_trab_andamento`.`andamento_data_movi`  ASC") ;
															
							  $titulo = 'EXPIRADO';
						
							 
						break;
						
						case 2:
								
								$qr_processo2  = mysql_query("SELECT * FROM processos_juridicos 
															INNER JOIN proc_trab_andamento
															ON proc_trab_andamento.proc_id = processos_juridicos.proc_id
															INNER JOIN funcionario_regiao_assoc
															ON funcionario_regiao_assoc.id_regiao = processos_juridicos.id_regiao
															 WHERE processos_juridicos.proc_id IN ($proc_ids) 							 
															AND  proc_trab_andamento.andamento_id IN  ($andamentos_ids) 
															AND id_funcionario = '$usuario'
															ORDER BY `proc_trab_andamento`.`andamento_data_movi`  ASC") ;
								
							 $titulo = 'PRÓXIMOS PROCESSO';
							
						break;	
						
						case 3:
						
								$qr_processo2  = mysql_query("SELECT * FROM processos_juridicos 
															INNER JOIN proc_trab_andamento
															ON proc_trab_andamento.proc_id = processos_juridicos.proc_id
															INNER JOIN funcionario_regiao_assoc
															ON funcionario_regiao_assoc.id_regiao = processos_juridicos.id_regiao
															WHERE processos_juridicos.proc_id IN ($processos_abertos_proc_id) 							 
															AND  proc_trab_andamento.andamento_id IN  ($processos_abertos_andamento_id) 
															AND id_funcionario = '$usuario'
															ORDER BY `proc_trab_andamento`.`andamento_data_movi`  ASC");
							 $titulo = 'PROCESSOS ABERTOS';
						 
							 
						break;	
						
						
						}
									
								$total_email += @mysql_num_rows($qr_processo2);
								if(@mysql_num_rows($qr_processo2) != 0) {	
					?>				
									
									
					
					
					<table style="margin-top:15px;">
											  							
					<?php						  while($row_processo2 = @mysql_fetch_assoc($qr_processo2)):
												  						
												
												
																		
															$mes_andamento = (int)substr($row_processo2['andamento_data_movi'], 5,2);	
															$ano_andamento = (int)substr($row_processo2['andamento_data_movi'], 0,4);	
											
																	
												  						 $qr_n_processo 	   = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$row_processo2[proc_id]' ORDER BY n_processo_ordem") or die(mysql_error());
												  						 while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
												  						 	$n_processos[] = $row_n_processo['n_processo_numero'];
												  						endwhile;
												  						
												  						 $tipo_processo   = mysql_result(mysql_query("SELECT proc_tipo_nome FROM processo_tipo WHERE proc_tipo_id = '$row_processo2[proc_tipo_id]'"),0) or die(mysql_error());	
																		 $qr_regiao   	  = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_processo2[id_regiao]'");	
												  						 $row_regiao 	  = mysql_fetch_assoc($qr_regiao);
																		 $nome_regiao 	  = $row_regiao['regiao'];
												  						 $processo_status = mysql_result(mysql_query("SELECT 	proc_status_nome FROM processo_status WHERE proc_status_id = '$row_processo2[proc_status_id]'"),0);
												  						
												  						
												  						if($tipo_aviso != $tipo_anterior) {															
												  							
																			$data_movi =  implode('/',array_reverse(explode('-',$row_processo2['andamento_data_movi'])));																			
																			echo '<tr class="titulo'.$tipo_aviso.'"> <td colspan="7" align="center">'.$nome_aviso.'</td></tr>';																		
																		
												  						}
																		
																	
																						
																		if($mes_andamento != $mes_anterior){
																		echo '<tr><td colspan="7">&nbsp;</td></tr>';	
																		echo '<tr><td colspan="7" class="mes_titulo">  '.$meses[$mes_andamento].' /'.$ano_andamento.'</td></tr>';	
																		
																	  echo '<tr class="titulo">
																			<td>N&ordm; DO PROCESSO</td>
																			<td>NOME</td>
																			<td>REGI&Atilde;O</td>
																			<td>STATUS</td>
																			<td>TIPO PROCESSO</td>
																			<td>DATA</td>
																			<td>HOR&Aacute;RIO</td>
																		</tr>';
																			
																		}
																																				
												  						$class = (($i++ % 2) == 0 )?'class="linha_um"':'class="linha_dois"';
																		
												  						?>
												  						<tr <?php echo $class; ?>>
												  							<td align="center"><?php echo @implode(', ',$n_processos);?></td>
												  							<td align="center"><?php echo  $row_processo2['proc_nome']?></td>
												  							<td align="center"><?php echo $nome_regiao?></td>
												  							<td align="center"><?php echo  $processo_status?></td>
												                            <td align="center"><?php echo  $tipo_processo; ?></td>
                                                                            <td align="center"><?php echo implode('/',array_reverse(explode('-',$row_processo2['andamento_data_movi']))); ?></td>  
												  							<td align="center"><?php  echo substr( $row_processo2['andamento_horario'],0,5)?> </td>
												  						</tr>											  						
												  						
												  						<?php
												  $tipo_anterior = $tipo_aviso;				
												  unset($n_processos);
												  
																			  	
													$mes_anterior = $mes_andamento;
													$ano_anterior = $ano_andamento;
						
												  	
												  endwhile;
												  
												  
												  
						echo '</table>'; 		}
				     
				      
					   unset($titulo, $mes_anterior, $ano_anterior,$tipo_anterior);
					  	
					
				     endforeach;
					 
				echo '</body>
				</html>';
					     $resultado = ob_get_contents();
						 ob_end_clean();				
					
				
					 $headers = 'Content-type: text/html; charset=iso-8859-1';	
					mail($email,'Avisos do Juridico',$resultado, $headers);
					
					echo $resultado;
						////////////////////////////////////
			
			
			unset($j,$total_email, $enviado, $resultado);
endforeach;		
?>
