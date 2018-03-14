<?php 
include('../include/restricoes.php');
include('../../../conn.php');
include('../../../funcoes.php');
extract($_POST);

if(isset($_POST['finalizar'])){


$projeto 			= $_POST['projeto_id'];
$regiao 			= $_POST['regiao_id'];


$master 			= $_POST['master'];
$ano_competencia	= $_POST['entregue_ano_competencia'];
$obrigacao_data 	= $_POST['entregue_dataproc'];
$entregue_obrigacao	= $_POST['entregue_obrigacao'];
$data_referencia    = $_POST['entregue_datareferencia'];
$projeto_inicio 	= $_POST['projeto_inicio'];
$projeto_termino 	= $_POST['projeto_termino'];
extract($_POST);



	

$insert_obrig_entregue = mysql_query("INSERT INTO obrigacoes_entregues (entregue_obrigacao,
											   entregue_dataproc,
											   entregue_datareferencia,
											   entregue_autor,
											   entregue_data,
											   entregue_ano_competencia
											  ) 
											   VALUES
											   ('$entregue_obrigacao',
											    '$obrigacao_data',
												'$data_referencia',
												'$_COOKIE[logado]',
												NOW(),
												'$ano_competencia'
												)												
												") or die(mysql_error());

$obg_entregue_id = mysql_insert_id();





if($insert_obrig_entregue){

if($tipo == 'anexo_1') {

	$insert_anexo_1 = mysql_query("INSERT INTO obrigacoes_anexo_1 (entregue_id,
																	ano_competencia,
																	prefeitura,
																 	 custo_projeto,
																	 local_projeto,
																	 data_assinatura,
																	 inicio_projeto,
																	 termino_projeto,
																	 obj_termo,
																	 nome_oscip,
																	 endereco_oscip,
																	 cidade_oscip,
																	 uf_oscip,
																	 cep_oscip,
																	 tel_oscip,
																	 fax_oscip,
																	 email_oscip,
																	 responsavel_projeto,
																	 cargo_responsavel,
																	 data_cad,
																	 user_cad,
																	 status)
																	 
																	 VALUES
																	 
																	 ('$obg_entregue_id',
																	 '$ano_competencia',
																	 '$prefeitura',
																	 '$custo_projeto',
																	  '$local_projeto',
																	  '$data_assinatura',
																	  '$data_inicio',
																	  '$data_termino',
																	  '$obj_parceria',
																	  '$nome_oscip',
																	  '$endereco_oscip',
																	  '$cidade_oscip',
																	  '$uf_oscip',
																	  '$cep_oscip',
																	  '$tel_oscip',
																	  '$fax_oscip',
																	  '$email_oscip',
																	  '$responsavel_oscip',
																	  '$cargo_responsavel',
																	  NOW(),
																	  '$_COOKIE[logado]',
																	  '1'	)") or die(mysql_error());
}



if($tipo == 'anexo_2'){


$cat_desp_previsto = str_replace(',','.',str_replace('.','',$cat_desp_previsto));
$cat_desp_diferenca = str_replace(',','.',str_replace('.','',$cat_desp_diferenca));
$total_previsto = str_replace(',','.',str_replace('.','',$total_previsto));
$total_diferenca = str_replace(',','.',str_replace('.','',$total_diferenca));
$cat_desp_realizado = str_replace(',','.',str_replace('.','',$cat_desp_realizado));
$total_realizado = str_replace(',','.',str_replace('.','',$total_realizado));
$resultados = trim($resultados);


$insert_anexo_2 = mysql_query("INSERT INTO obrigacoes_anexo_2 (entregue_id,
																ano_competencia,
																prefeitura,
																custo_projeto,
																local_projeto,
																data_assinatura,
																inicio_projeto,
																termino_projeto,
																obj_projeto,
																resultados,
																cat_desp_previsto,
																cat_desp_realizado ,
																cat_desp_diferenca,
																total_previsto,
																total_realizado,
																total_diferenca,
																nome_oscip,
																endereco_oscip,
																cidade_oscip,
																uf_oscip,
																cep_oscip,
																tel_oscip,
																fax_oscip,
																email_oscip,
																nome_responsavel,
																cargo_responsavel,
																user_cad,
																data_cad,
																status,
																id_projeto,
																id_regiao																
																)
																
																VALUES
																('$obg_entregue_id',
																'$ano_competencia',
																'$prefeitura',																
																'$custo_projeto',
																'$local_projeto',
																'$data_assinatura',
																'$data_inicio',
																'$data_termino',
																'$obj_projeto',
																'$resultados',
																'$cat_desp_previsto',
																'$total_realizado',
																'$cat_desp_diferenca',																
																'$total_previsto',
																'$total_realizado',
																'$total_diferenca',
																'$nome_oscip',
																'$endereco_oscip',
																'$cidade_oscip',
																'$uf_oscip',
																'$cep_oscip',
																'$tel_oscip',
																'$fax_oscip',
																'$email_oscip',
																'$nome_responsavel',
																'$cargo_responsavel',
																'$_COOKIE[logado]',
																NOW(),
																1,
																'$projeto',
																'$regiao') ") or die(mysql_error());	
}




if($tipo == 'conciliacao_bancaria'){
	
$total_repasse 		= str_replace(',','.',str_replace('.','',$total_repasse));
$total_rendimento 	= str_replace(',','.',str_replace('.','',$total_rendimento));
$total_folha 		= str_replace(',','.',str_replace('.','',$total_folha));
$total_fgts 		= str_replace(',','.',str_replace('.','',$total_repasse));
$total_irrf 		= str_replace(',','.',str_replace('.','',$total_irrf));
$total_pis 			= str_replace(',','.',str_replace('.','',$total_pis));
$total_provisao 	= str_replace(',','.',str_replace('.','',$total_provisao));
$total_tarifa 		= str_replace(',','.',str_replace('.','',$total_tarifa));
$total_taxa_adm 	= str_replace(',','.',str_replace('.','',$total_taxa_adm));
$total_prestador 	= str_replace(',','.',str_replace('.','',$total_prestador));
$saldo_atual 		= str_replace(',','.',str_replace('.','',$saldo_atual));
$periodo_inicio 	= implode('-',array_reverse(explode('/',$periodo_inicio)));
$periodo_fim 		= implode('-',array_reverse(explode('/',$periodo_fim)));
$data_evento 		= implode('-',array_reverse(explode('/',$data_evento)));

$termo_aditivo = implode(',',$termo_aditivo);






$insert_conc_bancaria = mysql_query("INSERT INTO obrigacoes_conc_bancaria (	entregue_id,
																			ano_competencia,
																			razao,
																			projeto,
																			projeto_numero,
																			termos_aditivos,
																			nome_banco,
																			agencia_banco,
																			conta_banco,
																			periodo_inicio,
																			periodo_fim,
																			data_evento,
																			total_repasse,
																			total_rendimento,
																			total_folha,
																			total_fgts,
																			total_irrf,
																			total_pis,
																			total_provisao,
																			total_tarifa,
																			total_taxa_adm,
																			total_prestador,
																			saldo_atual,
																			responsavel_convenente,
																			responsavel_prest_contas,
																			cargo_convenente,
																			cargo_prest_contas,
																			user_cad,
																			data_cad,
																			status)
																			VALUES 
																			
																			('$obg_entregue_id',
																			'$ano_competencia',
																			'$razao',
																			'$projeto',
																			'$projeto_numero',
																			'$termo_aditivo',
																			'$nome_banco',
																			'$agencia_banco',
																			'$conta_banco',
																			'$periodo_inicio',
																			'$periodo_fim',
																			'$data_evento',
																			'$total_repasse',
																			'$total_rendimento',
																			'$total_folha',
																			'$total_fgts',
																			'$total_irrf',
																			'$total_pis',
																			'$total_provisao',
																			'$total_tarifa',
																			'$total_taxa_adm',
																			'$total_prestador',
																			'$saldo_atual',
																			'$responsavel_convenente',
																			'$responsavel_prest_contas',
																			'$cargo_convenente',
																			'$cargo_prest_contas',
																			'$_COOKIE[logado]',
																			NOW(),
																			1)") or die( mysql_error());




}



if($tipo == 'anexo_xv'){
	
	
		$insert = mysql_query("INSERT INTO obrigacoes_anexo_xv (entregue_id,
		  														prefeitura,
																entidade_parceira,
																cnpj,
																endereco,
																cidade,
																cep,
																responsavel_entidade,
																obj_termo, 
																exercicio,
																tipo_projeto,
																numero_contrato,
																data_assinatura,
																vigencia,
																valor,
																total_receita,
																nome_oscip,
																membro_conselho1,
																membro_conselho2,
																status
																) 
																VALUES
																('$obg_entregue_id',
																'$prefeitura',
																'$entidade_parceira',
																'$cnpj',
																'$endereco',
																'$cidade',
																'$cep',
																'$responsavel_entidade',
																'$obj_termo',
																'$exercicio',
																'$tipo_projeto',
																'$numero_contrato',
																'$data_assinatura',
																'$vigencia',
																'$valor',
																'$total_receita',
																'$nome_oscip',
																'$membro_conselho1',
																'$membro_conselho2',
																
																'1')") or die(mysql_error());	
		$obrigacoes_anexo_xv_id = mysql_insert_id();   
	
	
	
	
	
	
	
			if($insert) {			
					
					
								///RECEITAS
								$array_valores_previstos 	    = $valores_previstos;	
								$array_valores_previstos_meses  = $valores_previstos_meses;
								$array_doc_credito 	   			= $doc_credito;
								$array_data_credito    			= $data_credito;
								$array_valor_repassado 			= $valor_repassado;
								
								$array_categoria_despesa 		= $categoria_despesa;
								$array_periodo_inicio 			= $periodo_inicio;
								$array_periodo_inicio 			= $periodo_inicio;
								$array_periodo_fim 				= $periodo_fim;
								$array_valor_aplicado 			= $valor_aplicado;
									
								
								foreach($array_valores_previstos as $chave => $valor_previsto ){	
									
										$insert_valor_previsto = mysql_query("INSERT INTO valor_prev_anexo_xv (obrigacoes_anexo_xv_id, valor_previsto, mes)
																									VALUES 
																									('$obrigacoes_anexo_xv_id',$valor_previsto, $array_valores_previstos_meses[$chave])");
									  $valor_previsto_id 	   = mysql_insert_id();
									
									
									   foreach($array_doc_credito[$chave] as $chave2 => $doc_credito){
													
													$insert_repasse = mysql_query("INSERT INTO repasse_anexo_xv (valor_previsto_id, doc_credito, data, repassado)
																												VALUES 
																												('$valor_previsto_id', '$doc_credito',
																												 '".$array_data_credito[$chave][$chave2]."','".$array_valor_repassado[$chave][$chave2]."')") or die(mysql_error());
										
										}
									
								}
								
						
					
							foreach($array_categoria_despesa as $chave3 => $categoria){
							
							mysql_query("INSERT INTO despesas_realizadas_anexo_xv (	obrigacoes_anexo_xv_id, categoria_despesa, periodo_inicio, periodo_fim, valor_aplicado)
																				VALUES
																				( '$obrigacoes_anexo_xv_id', '$categoria', '$array_periodo_inicio[$chave3]', '$array_periodo_fim[$chave3]', '$array_valor_aplicado[$chave3]')") or die(mysql_error());	
							
								
							}
					
					
																				 
			}


}



//Encriptografando a variável
$link_master = encrypt("$master&12");
$link_master = str_replace("+","--",$link_master);


header("Location: ../index.php?m=$link_master");


}




	
}


?>