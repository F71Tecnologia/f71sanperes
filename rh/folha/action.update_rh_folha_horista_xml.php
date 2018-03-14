<?php

include('../../adm/include/restricoes.php');
include('../../conn.php');
include('../../classes/calculos.php');
include('../../classes/valor_proporcional.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');

ini_set('display_errors', 1);




if(isset($_POST['ajax'])){


extract($_POST);





$xml = new DOMDocument();
$xml->load("xml_horista/$id_folha.xml") or die("Error");

$root = $xml->getElementsByTagName('rh_folha');////Pegando o elemento pai	
			 

$array_campos = array('clts' 		    	=> $total_participantes,
					  'rendi_indivi'  		=> formato_banco( $total_rendimentos),
					  'rendi_final' 		=> formato_banco( $total_rendimento_mov),
					  'descon_indivi' 		=> formato_banco( $total_descontos),
					  'descon_final'		=> formato_banco( $total_desconto_mov),
					  'total_limpo'			=> formato_banco( $total_base),
					  'total_salarios'		=> formato_banco($total_xml_base),
					  'total_liqui'			=> formato_banco($total_liquido),
					  'total_familia'		=> formato_banco($total_familia),
					  'total_sindical'		=> formato_banco($total_sindicato),
					  'total_vt'			=> formato_banco($total_vale_transporte),
					  'total_vr' 			=>  formato_banco($total_vale_refeicao),
					  'base_inss' 			=>  formato_banco($total_base_inss),
					  'total_inss' 			=> formato_banco($total_inss_completo),
					  'base_irrf' 			=>  formato_banco($total_base_irrf),					  
					  'total_irrf'			=> formato_banco($total_irrf_completo),
					  'base_fgts' 			=> formato_banco($total_base_fgts),
					  'total_fgts' 			=> formato_banco($total_fgts),
					  'base_fgts_ferias'	=> formato_banco($total_base_fgts_ferias),
					  'base_fgts_sefip' 	=> formato_banco($total_base_fgts),
					  'total_fgts_sefip'	=> formato_banco($total_fgts),
					   
					 'multa_fgts'			=> formato_banco('0.00'),
					 'valor_dt'			    => formato_banco($total_decimo_terceiro),
					 'inss_dt'				=> formato_banco($total_inss_dt),
					 'ir_dt'				=> formato_banco($total_irrf_dt),
					 'valor_ferias'			=> formato_banco($total_ferias),					 
					
					  'valor_pago_ferias'	=> formato_banco($total_desconto_ferias),
					  
					 'inss_ferias'			=> formato_banco($total_inss_ferias),
					 'ir_ferias'			=> formato_banco($total_irrf_ferias),
					 'fgts_ferias'			=> formato_banco($total_fgts_ferias),
					 'valor_rescisao'			=> formato_banco($total_rescisao),
					  
					'valor_pago_rescisao'			=> formato_banco($total_desconto_rescisao),
					'inss_rescisao'			=> formato_banco($total_inss_rescisao),
					 'ir_rescisao'			=> formato_banco($total_irrf_rescisao),
					 
					 
					  
					  );

////ALTERANDO OS VALORES
foreach($array_campos as $node => $valor){
	
			$node_antigo 			= $xml->getElementsByTagName($node)->item(0);
			$new_node		= $xml->createElement($node, $valor); 
			$node_antigo->parentNode->replaceChild($new_node, $node_antigo); 
				
}
			
							
///SALVANDO O ARQUIVO	
file_put_contents ("xml_horista/$id_folha.xml", $xml->saveXML());			  





echo json_encode($JSON);
	
}

?>