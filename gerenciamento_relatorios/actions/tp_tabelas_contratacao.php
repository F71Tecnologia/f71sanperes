<?php
switch($tipo_contratacao){
					case 1: 	$tabela_trab 		   = 'autonomo'; 
								$id_trab     		   = 'id_autonomo';
								$folhas				   = 'folhas';
								$folha_proc			   = 'folha_autonomo';
								$nome_campo_dados      =  $array_nome_campos_autonomo;
								$nome_campo_folhas     = $array_folhas;
								$nome_campo_folha_proc = $array_folha_autonomo;
						break;
					case 2:		$tabela_trab 		   = 'rh_clt'; 
								$id_trab    		   = 'id_clt';
								$folhas				   = 'rh_folha';
								$folha_proc	 		   = 'rh_folha_proc';
								$nome_campo_dados      =  $array_nome_campos_clt;
								$nome_campo_folhas     = $array_rh_folha;
								$nome_campo_folha_proc = $array_folha_proc;
						break;
					case 3:		$tabela_trab 		   = 'autonomo'; 
								$id_trab     		   = 'id_autonomo';
								$folhas		 		   = 'folhas';
								$folha_proc	 		   = 'folha_cooperado';
								$nome_campo_dados      =  $array_nome_campos_autonomo;
								$nome_campo_folhas     = $array_folhas;
								$nome_campo_folha_proc = $array_folha_cooperado;
						break;
					case 4: 	$tabela_trab 	       = 'autonomo'; 
								$id_trab     	       = 'id_autonomo';
								$folhas		 		   = 'folhas';
								$folha_proc	 		   = 'folha_cooperado';
								$nome_campo_dados  	   =  $array_nome_campos_autonomo;
								$nome_campo_folhas     =  $array_folhas;
								$nome_campo_folha_proc = $array_folha_cooperado;
						break;
					}
					
					$nome_tp_contrato = mysql_result(mysql_query("SELECT tipo_contratacao_nome  
											  FROM tipo_contratacao 
											  WHERE tipo_contratacao_id ='$tipo_contratacao' "),0);		
?>