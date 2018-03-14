<?php
$xml = simplexml_load_file("xml_horista/$folha.xml");
$folha_xml= $xml->addChild('rh_folha');


$folha_xml->addChild('clts',$total_participantes );


$folha_xml->addChild('id_folha_rh_folha',$folha );
$folha_xml->addChild('rendi_indivi',formato_banco($rendimentos_total) );
$folha_xml->addChild('rendi_final',formato_banco($movimentos_credito) );
$folha_xml->addChild('descon_indivi',formato_banco($descontos_total) );
$folha_xml->addChild('descon_final',formato_banco($movimentos_debito) );
$folha_xml->addChild('total_limpo',formato_banco($salario_total) );

$folha_xml->addChild('total_salarios',formato_banco($base_total) );
$folha_xml->addChild('total_liqui',formato_banco($liquido_total) );

$folha_xml->addChild('total_familia', formato_banco($familia_total) );
$folha_xml->addChild('total_sindical', formato_banco($sindicato_total) );
$folha_xml->addChild('total_vt',formato_banco($vale_transporte_total) );
$folha_xml->addChild('total_vr',formato_banco($vale_refeicao_total) );
$folha_xml->addChild('base_inss',formato_banco($base_inss_total) );


$folha_xml->addChild('total_inss',formato_banco($inss_total) );
$folha_xml->addChild('base_irrf', formato_banco($base_irrf_total) );
$folha_xml->addChild('total_irrf',formato_banco($irrf_total) );
$folha_xml->addChild('base_fgts',formato_banco($base_fgts_total) );

$folha_xml->addChild('total_fgts',formato_banco($fgts_total) );



$folha_xml->addChild('base_fgts_ferias',formato_banco($base_fgts_ferias_total) );
$folha_xml->addChild('base_fgts_sefip',formato_banco($base_fgts_total) );
$folha_xml->addChild('total_fgts_sefip',formato_banco($fgts_total) );

$folha_xml->addChild('multa_fgts',formato_banco('0.00') );

$folha_xml->addChild('valor_dt',formato_banco($decimo_terceiro_total) );
$folha_xml->addChild('inss_dt',formato_banco($inss_dt_total) );

$folha_xml->addChild('ir_dt',formato_banco($irrf_dt_total) );
$folha_xml->addChild('valor_ferias',formato_banco(formato_banco($ferias_total)) );
$folha_xml->addChild('valor_pago_ferias',formato_banco($ferias_desconto_total) );

$folha_xml->addChild('inss_ferias',formato_banco($inss_ferias_total) );
$folha_xml->addChild('ir_ferias',formato_banco($irrf_ferias_total) );
$folha_xml->addChild('fgts_ferias',formato_banco($fgts_ferias_total) );
$folha_xml->addChild('valor_rescisao',formato_banco($rescisao_total) );
$folha_xml->addChild('valor_pago_rescisao',formato_banco($rescisao_desconto_total) );
$folha_xml->addChild('inss_rescisao',formato_banco($inss_rescisao_total) );


$folha_xml->addChild('ir_rescisao',formato_banco($irrf_rescisao_total) );


$folha_xml->addChild('ids_movimentos_update', implode(',',$ids_movimentos_update_geral) );
$folha_xml->addChild('ids_movimentos_estatisticas',$ids_movimentos_estatisticas );
$folha_xml->addChild('status',3 );
$folha_xml->addChild('total_auxilio_distancia',formato_banco($total_aux_distancia) );






file_put_contents ("xml_horista/$folha.xml", $xml->asXML());
?>