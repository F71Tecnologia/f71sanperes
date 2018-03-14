 <?php


include "../../conn.php";
include "../../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 
$decript = explode("&",$link);
$regiao = $decript[0];
$id_folha =  $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$id_folha'");
$row_folha = mysql_fetch_assoc($qr_folha);



$xml = new DOMDocument();//
$xml->load("xml_horista/$id_folha.xml") or die("Error");

$root = $xml->getElementsByTagName('clt');////Pegando o elemento pai
$total_participantes = $root->length;




for($i=0; $i<$total_participantes; $i++){


			  
			  ///Pegando 
			  $id_folha_proc 		= $xml->getElementsByTagName('id_folha_proc')->item($i)->nodeValue; 
			  $id_clt 			    = $xml->getElementsByTagName('id_clt')->item($i)->nodeValue; 
			  $cod 					= $xml->getElementsByTagName('cod')->item($i)->nodeValue; 
			  $nome 				= $xml->getElementsByTagName('nome')->item($i)->nodeValue; 
			  $status_clt 			= $xml->getElementsByTagName('status_clt')->item($i)->nodeValue;
			  $id_banco 			= $xml->getElementsByTagName('id_banco')->item($i)->nodeValue;
			  $agencia 				= $xml->getElementsByTagName('agencia')->item($i)->nodeValue;
			  $conta 				= $xml->getElementsByTagName('conta')->item($i)->nodeValue;			  
			  $cpf 					= $xml->getElementsByTagName('cpf')->item($i)->nodeValue;
			  $dias_trab 			= $xml->getElementsByTagName('dias_trab')->item($i)->nodeValue;
			  $meses 				= $xml->getElementsByTagName('meses')->item($i)->nodeValue;			  
			  $salbase 				= $xml->getElementsByTagName('salbase')->item($i)->nodeValue;
			  $sallimpo 			= $xml->getElementsByTagName('sallimpo')->item($i)->nodeValue;
			  $sallimpo_real 		=  $xml->getElementsByTagName('sallimpo_real')->item($i)->nodeValue;
			  $rend 				= $xml->getElementsByTagName('rend')->item($i)->nodeValue;
			  $desco 				= $xml->getElementsByTagName('desco')->item($i)->nodeValue;
			  $inss 				= $xml->getElementsByTagName('inss')->item($i)->nodeValue;
			  $t_inss 				= $xml->getElementsByTagName('t_inss')->item($i)->nodeValue;
			  $imprenda 			= $xml->getElementsByTagName('imprenda')->item($i)->nodeValue;
			  $t_imprenda 			= $xml->getElementsByTagName('t_imprenda')->item($i)->nodeValue;			  
			  $d_imprenda 			= $xml->getElementsByTagName('d_imprenda')->item($i)->nodeValue;
			  $fgts 				= $xml->getElementsByTagName('fgts')->item($i)->nodeValue;			  
			  $base_irrf 			= $xml->getElementsByTagName('base_irrf')->item($i)->nodeValue;
			  $salfamilia 			= $xml->getElementsByTagName('salfamilia')->item($i)->nodeValue;
			  $salliquido 			= $xml->getElementsByTagName('salliquido')->item($i)->nodeValue;
			 
			  		
			$array_movimentos = array('a4001' => $xml->getElementsByTagName('a4001')->item($i)->nodeValue,
			                          'a4001' => $xml->getElementsByTagName('a4001')->item($i)->nodeValue,
									  'a4003' => $xml->getElementsByTagName('a4003')->item($i)->nodeValue,
									  'a4004' => $xml->getElementsByTagName('a4004')->item($i)->nodeValue,
									  'a4005' => $xml->getElementsByTagName('a4005')->item($i)->nodeValue,
									  'a4006' => $xml->getElementsByTagName('a4006')->item($i)->nodeValue,
									  'a4007' => $xml->getElementsByTagName('a4007')->item($i)->nodeValue,
									  'a5001' => $xml->getElementsByTagName('a5001')->item($i)->nodeValue,
									  'a5002' => $xml->getElementsByTagName('a5002')->item($i)->nodeValue, 
									  'a5003' => $xml->getElementsByTagName('a5003')->item($i)->nodeValue,
									  'a5004' => $xml->getElementsByTagName('a5004')->item($i)->nodeValue,
									  'a5010' => $xml->getElementsByTagName('a5010')->item($i)->nodeValue,
									  'a5011' => $xml->getElementsByTagName('a5011')->item($i)->nodeValue,
									  'a5012' => $xml->getElementsByTagName('a5012')->item($i)->nodeValue,
									  'a5013' => $xml->getElementsByTagName('a5013')->item($i)->nodeValue,
									  'a5014' => $xml->getElementsByTagName('a5014')->item($i)->nodeValue,
									  'a5015' => $xml->getElementsByTagName('a5015')->item($i)->nodeValue,
									  'a5016' => $xml->getElementsByTagName('a5016')->item($i)->nodeValue,
									  'a5017' => $xml->getElementsByTagName('a5017')->item($i)->nodeValue,
									  'a5018' => $xml->getElementsByTagName('a5018')->item($i)->nodeValue,
									  'a5019' => $xml->getElementsByTagName('a5019')->item($i)->nodeValue ,
									  'a5020' => $xml->getElementsByTagName('a5020')->item($i)->nodeValue,
									  'a5021' => $xml->getElementsByTagName('a5021')->item($i)->nodeValue,
									  'a5022' => $xml->getElementsByTagName('a5022')->item($i)->nodeValue,
									  'a5023' => $xml->getElementsByTagName('a5023')->item($i)->nodeValue,
									  'a5024' => $xml->getElementsByTagName('a5024')->item($i)->nodeValue,
									  'a5025' => $xml->getElementsByTagName('a5025')->item($i)->nodeValue,
									  'a5026' => $xml->getElementsByTagName('a5026')->item($i)->nodeValue,
									  'a5027' => $xml->getElementsByTagName('a5027')->item($i)->nodeValue,
									  'a5028' => $xml->getElementsByTagName('a5028')->item($i)->nodeValue,
									  'a5029' => $xml->getElementsByTagName('a5029')->item($i)->nodeValue,
									  'a5030' => $xml->getElementsByTagName('a5030')->item($i)->nodeValue,
									  'a5031' => $xml->getElementsByTagName('a5031')->item($i)->nodeValue,
									  'a5032' => $xml->getElementsByTagName('a5032')->item($i)->nodeValue,
									  'a5033' => $xml->getElementsByTagName('a5033')->item($i)->nodeValue,
									  'a5034' => $xml->getElementsByTagName('a5034')->item($i)->nodeValue,
									  'a5035' =>  $xml->getElementsByTagName('a5035')->item($i)->nodeValue,
									  'a5036' => $xml->getElementsByTagName('a5036')->item($i)->nodeValue,
									  'a5037' => $xml->getElementsByTagName('a5037')->item($i)->nodeValue,
									  'a5038' => $xml->getElementsByTagName('a5038')->item($i)->nodeValue,
									  'a5039' => $xml->getElementsByTagName('a5039')->item($i)->nodeValue,
									  'a5040' => $xml->getElementsByTagName('a5040')->item($i)->nodeValue,
									  'a5040' => $xml->getElementsByTagName('a5040')->item($i)->nodeValue,
									  'a5042' => $xml->getElementsByTagName('a5042')->item($i)->nodeValue,
									  'a5043' => $xml->getElementsByTagName('a5043')->item($i)->nodeValue,
									  'a5044' => $xml->getElementsByTagName('a5044')->item($i)->nodeValue,
									  'a5045' => $xml->getElementsByTagName('a5045')->item($i)->nodeValue,
									  'a5046' => $xml->getElementsByTagName('a5046')->item($i)->nodeValue,
									  'a5047' => $xml->getElementsByTagName('a5047')->item($i)->nodeValue,
									  'a5048' => $xml->getElementsByTagName('a5048')->item($i)->nodeValue,
									  'a5049' => $xml->getElementsByTagName('a5049')->item($i)->nodeValue,
									  'a6000'=> $xml->getElementsByTagName('a5049')->item($i)->nodeValue,
									  'a6001'=> $xml->getElementsByTagName('a6001')->item($i)->nodeValue,
									  'a6003'=> $xml->getElementsByTagName('a6003')->item($i)->nodeValue,
									  'a6004'=> $xml->getElementsByTagName('a6004')->item($i)->nodeValue,
									  'a6005' => $xml->getElementsByTagName('a6005')->item($i)->nodeValue,
									  'a6006' => $xml->getElementsByTagName('a6006')->item($i)->nodeValue,
									  'a6007' => $xml->getElementsByTagName('a6007')->item($i)->nodeValue,
									  'a7000' => $xml->getElementsByTagName('a7000')->item($i)->nodeValue,
									  'a7001' => $xml->getElementsByTagName('a7001')->item($i)->nodeValue,
									  'a7003' => $xml->getElementsByTagName('a7003')->item($i)->nodeValue,
									  'a7004' => $xml->getElementsByTagName('a7004')->item($i)->nodeValue,
									  'a7009' => $xml->getElementsByTagName('a7009')->item($i)->nodeValue,
									  'a8000' => $xml->getElementsByTagName('a8000')->item($i)->nodeValue,
									  'a8002' => $xml->getElementsByTagName('a8002')->item($i)->nodeValue,
									  'a8003' => $xml->getElementsByTagName('a8003')->item($i)->nodeValue,
									  'a8004' => $xml->getElementsByTagName('a8004')->item($i)->nodeValue,
									  'a8005' => $xml->getElementsByTagName('a8005')->item($i)->nodeValue,
									  'a8006' => $xml->getElementsByTagName('a8006')->item($i)->nodeValue,
									  'a8080' => $xml->getElementsByTagName('a8080')->item($i)->nodeValue,
									  'a9000' => $xml->getElementsByTagName('a9000')->item($i)->nodeValue,
									  'a9500' => $xml->getElementsByTagName('a9500')->item($i)->nodeValue,
									  'a9999' => $xml->getElementsByTagName('a9999')->item($i)->nodeValue,
									  'a50220' => $xml->getElementsByTagName('a50220')->item($i)->nodeValue,
									  'a50222' => $xml->getElementsByTagName('a50222')->item($i)->nodeValue,
									  'a50272' => $xml->getElementsByTagName('a50272')->item($i)->nodeValue,
									  'a50292' => $xml->getElementsByTagName('a50292')->item($i)->nodeValue,
									  'a50372' => $xml->getElementsByTagName('a50372')->item($i)->nodeValue,
									  'a50492' =>  $xml->getElementsByTagName('a50492')->item($i)->nodeValue,
									  'a80002' => $xml->getElementsByTagName('a80002')->item($i)->nodeValue,
									  'a50111'  => $xml->getElementsByTagName('a50111')->item($i)->nodeValue);	
									  	
				
  					
  			  foreach($array_movimentos  as $campo => $valor){  $update_movimentos_individual[] = "$campo =  '$valor'"; }  			
  		      $update_movimentos_individual = implode(', ',$update_movimentos_individual);
			  			  
			  
			  $ids_movimentos 		= $xml->getElementsByTagName('ids_movimentos')->item($i)->nodeValue;			  
			  $valor_ferias 		= $xml->getElementsByTagName('valor_ferias')->item($i)->nodeValue;
			  $valor_pago_ferias 	= $xml->getElementsByTagName('valor_pago_ferias')->item($i)->nodeValue; 
			  $inss_ferias 			= $xml->getElementsByTagName('inss_ferias')->item($i)->nodeValue;			  
			  $ir_ferias 			= $xml->getElementsByTagName('ir_ferias')->item($i)->nodeValue;			  
			  $fgts_ferias 			= $xml->getElementsByTagName('fgts_ferias')->item($i)->nodeValue;  
			  $valor_dt 			= $xml->getElementsByTagName('valor_dt')->item($i)->nodeValue;
			  $inss_dt 				= $xml->getElementsByTagName('inss_dt')->item($i)->nodeValue;
			  $ir_dt 				= $xml->getElementsByTagName('ir_dt')->item($i)->nodeValue;				  
			  $valor_rescisao 		= $xml->getElementsByTagName('valor_rescisao')->item($i)->nodeValue;
			  $valor_pago_rescisao 	= $xml->getElementsByTagName('valor_pago_rescisao')->item($i)->nodeValue;			  
			  $inss_rescisao 		= $xml->getElementsByTagName('inss_rescisao')->item($i)->nodeValue;			  
			  $ir_rescisao 			= $xml->getElementsByTagName('ir_rescisao')->item($i)->nodeValue;			
			  $hora_trabalhada 		= $xml->getElementsByTagName('hora_trabalhada')->item($i)->nodeValue;			
			  $hora_noturna			= $xml->getElementsByTagName('hora_noturna')->item($i)->nodeValue;
			  $adicional_noturno	= $xml->getElementsByTagName('adicional_noturno')->item($i)->nodeValue; 
 			  $horas_atraso 		= $xml->getElementsByTagName('horas_atraso')->item($i)->nodeValue;
			  $DSR 					= $xml->getElementsByTagName('dsr')->item($i)->nodeValue;
			  $desconto_auxilio_distancia = $xml->getElementsByTagName('desconto_auxilio_distancia')->item($i)->nodeValue;			  
			  
			
			  /////ATUALIZA O DESCONTO AUXILIO DISTÂNCIA
			  mysql_query("UPDATE rh_movimentos_clt SET id_folha = '$id_folha', valor_movimento = '$desconto_auxilio_distancia' 
			  			   WHERE id_mov = 195 AND id_clt = '$id_clt' AND ((mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]') OR lancamento = 2) LIMIT 1");
			  
			  //////////////////////INSERINDO O DSR E O ADICIONAL NOTURNO
			  if($DSR != '0.00' or $adicional_noturno != '0.00'){
				  
				 $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov IN(61,66)") ;
				 while($row_mov = mysql_fetch_assoc($qr_mov)):
				 
				 
				 /////DSR
				  if($row_mov['id_mov'] == 61 and $DSR != '0.00'){ 
					
					mysql_query("DELETE  FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '61' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]' AND id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]'");
					
					
					mysql_query("INSERT INTO rh_movimentos_clt (id_clt, id_regiao, id_projeto, id_folha, mes_mov,ano_mov, id_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento,user_cad, valor_movimento, percent_movimento, lancamento, incidencia, status,  status_reg)
					VALUES ('$id_clt', '$row_folha[regiao]', '$row_folha[projeto]', '$id_folha', '$row_folha[mes]', '$row_folha[ano]', '$row_mov[id_mov]', '$row_mov[cod]', '$row_mov[categoria]','$row_mov[descicao]', 'NOW()', '$_COOKIE[logado]', '$DSR', '', 1, '0001', 5 , 1)") or die(mysql_error());
				
					$id_movimento_dsr = mysql_insert_id();					
					$ids_movimentos   = $ids_movimentos.','.$id_movimento_dsr;
					  
				  }
				  
				   ////Adicional Noturno
				  if($row_mov['id_mov'] == 66 and $adicional_noturno != '0.00'){ 
					
						mysql_query("DELETE  FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '66' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]' AND id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]'");
						
					mysql_query("INSERT INTO rh_movimentos_clt (id_clt, id_regiao, id_projeto, id_folha, mes_mov,ano_mov, id_mov, cod_movimento, tipo_movimento, nome_movimento, data_movimento,user_cad, valor_movimento, percent_movimento, lancamento, incidencia, status,  status_reg)
					VALUES ('$id_clt', '$row_folha[regiao]', '$row_folha[projeto]', '$id_folha', '$row_folha[mes]', '$row_folha[ano]', '$row_mov[id_mov]', '$row_mov[cod]', '$row_mov[categoria]','$row_mov[descicao]', 'NOW()', '$_COOKIE[logado]', '$adicional_noturno', '', 1, '5020,5021,5023', 5 , 1)") or die(mysql_error());
					
					$id_movimento_adicional = mysql_insert_id();					
					$ids_movimentos   = $ids_movimentos.','.$id_movimento_adicional;
					
				  }				 
				 endwhile;
				  
			  }
              //////////////////////////////////////////////


// Criando Update do Participante
$update_participantes = "UPDATE rh_folha_proc SET cod = '".$cod."', status_clt = '".$status_clt."', dias_trab = '".$dias_trab."', meses = '".$meses."', id_banco = '".$id_banco."', agencia = '".$agencia."', conta = '".$conta."', cpf = '".$cpf."', salbase = '".$salbase."', sallimpo = '".$sallimpo."', sallimpo_real = '".$sallimpo_real."', rend = '".$rend."', desco = '".$desco."', inss = '".$inss."', t_inss = '".$t_inss."', imprenda = '".$imprenda."', t_imprenda = '".$t_imprenda."', d_imprenda = '".$d_imprenda."', fgts = '".$fgts."', base_irrf = '".$base_irrf."', salfamilia = '".$salfamilia."', salliquido = '".$salliquido."', valor_ferias = '".$valor_ferias."', valor_pago_ferias = '".$valor_pago_ferias."', inss_ferias = '".$inss_ferias."', ir_ferias = '".$ir_ferias."', fgts_ferias = '".$fgts_ferias."', valor_dt = '".$valor_dt."', inss_dt = '".$inss_dt."', ir_dt = '".$ir_dt."', valor_rescisao = '".$valor_rescisao."', valor_pago_rescisao = '".$valor_pago_rescisao."', inss_rescisao = '".$inss_rescisao."', ir_rescisao = '".$ir_rescisao."', ".$update_movimentos_individual.", ids_movimentos = '".$ids_movimentos."', status = '3', hora_trabalhada = '".$hora_trabalhada."' , hora_noturna = '$hora_noturna', adicional_noturno = '$adicional_noturno'  WHERE id_folha_proc = '$id_folha_proc'  AND horas_atraso = '$horas_atraso' AND dsr ='$DSR' LIMIT 1;\r\n";


mysql_query($update_participantes) or die(mysql_error());
unset($update_movimentos_individual, $update_movimentos_individual);
}


// Criando Update dos Movimentos
$xml2 = new DOMDocument();//
$xml2->load("xml_horista/$id_folha.xml") or die("Error");

$root = $xml2->getElementsByTagName('rh_folha');////Pegando o elemento pai



$array_campos = array('clts' 		    	=> $xml2->getElementsByTagName('clts')->item(0)->nodeValue,
					  'rendi_indivi'  		=>  $xml2->getElementsByTagName('rendi_indivi')->item(0)->nodeValue,
					  'rendi_final' 		=>  $xml2->getElementsByTagName('rendi_final')->item(0)->nodeValue,
					  'descon_indivi' 		=>  $xml2->getElementsByTagName('descon_indivi')->item(0)->nodeValue,
					  'descon_final'		=>  $xml2->getElementsByTagName('descon_final')->item(0)->nodeValue,
					  'total_limpo'			=>  $xml2->getElementsByTagName('total_limpo')->item(0)->nodeValue,
					  'total_salarios'		=> $xml2->getElementsByTagName('total_salarios')->item(0)->nodeValue,
					  'total_liqui'			=> $xml2->getElementsByTagName('total_liqui')->item(0)->nodeValue,
					  'total_familia'		=> $xml2->getElementsByTagName('total_familia')->item(0)->nodeValue,
					  'total_sindical'		=> $xml2->getElementsByTagName('total_sindical')->item(0)->nodeValue,
					  'total_vt'			=> $xml2->getElementsByTagName('total_vt')->item(0)->nodeValue,
					  'total_vr' 			=>  $xml2->getElementsByTagName('total_vr')->item(0)->nodeValue,
					  'base_inss' 			=>  $xml2->getElementsByTagName('base_inss')->item(0)->nodeValue,
					  'total_inss' 			=> $xml2->getElementsByTagName('total_inss')->item(0)->nodeValue,
					  'base_irrf' 			=>  $xml2->getElementsByTagName('base_irrf')->item(0)->nodeValue,			  
					  'total_irrf'			=>  $xml2->getElementsByTagName('total_irrf')->item(0)->nodeValue,
					  'base_fgts' 			=>  $xml2->getElementsByTagName('base_fgts')->item(0)->nodeValue,
					  'total_fgts' 			=>  $xml2->getElementsByTagName('total_fgts')->item(0)->nodeValue,
					  'base_fgts_ferias'	=> $xml2->getElementsByTagName('base_fgts_ferias')->item(0)->nodeValue,
					  'base_fgts_sefip' 	=> $xml2->getElementsByTagName('base_fgts_sefip')->item(0)->nodeValue,
					  'total_fgts_sefip'	=> $xml2->getElementsByTagName('total_fgts_sefip')->item(0)->nodeValue,
					 'multa_fgts'			=> $xml2->getElementsByTagName('multa_fgts')->item(0)->nodeValue,
					 'valor_dt'			    => $xml2->getElementsByTagName('valor_dt')->item(0)->nodeValue,
					 'inss_dt'				=> $xml2->getElementsByTagName('inss_dt')->item(0)->nodeValue,
					 'ir_dt'				=> $xml2->getElementsByTagName('ir_dt')->item(0)->nodeValue,
					 'valor_ferias'			=> $xml2->getElementsByTagName('valor_ferias')->item(0)->nodeValue,					
					  'valor_pago_ferias'	=> $xml2->getElementsByTagName('valor_pago_ferias')->item(0)->nodeValue,
					 'inss_ferias'			=> $xml2->getElementsByTagName('inss_ferias')->item(0)->nodeValue,
					 'ir_ferias'			=> $xml2->getElementsByTagName('ir_ferias')->item(0)->nodeValue,
					 'fgts_ferias'			=> $xml2->getElementsByTagName('fgts_ferias')->item(0)->nodeValue,
					 'valor_rescisao'		=> $xml2->getElementsByTagName('valor_rescisao')->item(0)->nodeValue,
					'valor_pago_rescisao'	=> $xml2->getElementsByTagName('valor_pago_rescisao')->item(0)->nodeValue,
					'inss_rescisao'			=> $xml2->getElementsByTagName('inss_rescisao')->item(0)->nodeValue,
					'ir_rescisao'			=> $xml2->getElementsByTagName('ir_rescisao')->item(0)->nodeValue,
					'ids_movimentos_update'	=> $xml2->getElementsByTagName('ids_movimentos_update')->item(0)->nodeValue,
					'ids_movimentos_estatisticas'	=> $xml2->getElementsByTagName('ids_movimentos_estatisticas')->item(0)->nodeValue,
					'status' => 3,
					'total_auxilio_distancia'	=> $xml2->getElementsByTagName('total_auxilio_distancia')->item(0)->nodeValue
					  
					  );


$total_movimentos = explode(',',$array_campos['ids_movimentos_estatisticas']);
$total_movimentos =  count($total_movimentos);

if(!empty($array_campos['ids_movimentos_estatisticas'])) {
	$update_movimentos = "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$id_folha' WHERE id_movimento IN(".$array_campos['ids_movimentos_estatisticas'].") LIMIT $total_movimentos;\r\n";
	
mysql_query($update_movimentos) or die(mysql_error() ) ;

	
	
}




////ALTERANDO OS VALORES
foreach($array_campos as $node => $valor){
	
	$update_rh_folha[] =  "$node = '$valor'";
				
}

$update = "UPDATE rh_folha SET ".implode(',',$update_rh_folha)."  WHERE id_folha = '".$id_folha."' LIMIT 1;";
mysql_query($update);

//echo $update;
exit;
?>