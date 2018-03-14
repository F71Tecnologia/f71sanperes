<?php



////INSERINDO DADOS NO ARQUIVO

$clt_xml = $xml->addChild('clt');


$clt_xml->addChild('id_folha_proc', "$row_participante[id_folha_proc]");
$clt_xml->addChild('id_clt', "$row_participante[id_clt]");
$clt_xml->addChild('id_projeto', "$row_participante[id_projeto]");
$clt_xml->addChild('id_folha', "$row_participante[id_folha]");
$clt_xml->addChild('parte', "$row_participante[parte]");
$clt_xml->addChild('data_proc', "$row_participante[data_proc]");
$clt_xml->addChild('user_proc', "$row_participante[user_proc]");
$clt_xml->addChild('mes', "$row_participante[mes]");
$clt_xml->addChild('ano', "$row_participante[ano]");
$clt_xml->addChild('data_pg', "$row_participante[data_pg]");
$clt_xml->addChild('cod', "$row_clt[campo3]");
$clt_xml->addChild('nome', utf8_encode("$row_clt[nome]"));
$clt_xml->addChild('status_clt', "$row_clt[status_clt]");

$clt_xml->addChild('id_banco', "$row_clt[banco]");
$clt_xml->addChild('agencia', "$row_clt[agencia]");
$clt_xml->addChild('conta', "$row_clt[conta]");
$clt_xml->addChild('cpf', "$row_clt[cpf]");

$clt_xml->addChild('dias_trab', "$dias");
$clt_xml->addChild('meses', "$meses");
$clt_xml->addChild('salbase', formato_banco($base));
$clt_xml->addChild('sallimpo', formato_banco($salario_limpo));

$clt_xml->addChild('sallimpo_real', formato_banco($salario));
$clt_xml->addChild('rend', formato_banco($rendimentos));
$clt_xml->addChild('desco',formato_banco($descontos));
$clt_xml->addChild('inss', formato_banco($inss));
$clt_xml->addChild('t_inss', $faixa_inss);
$clt_xml->addChild('imprenda', formato_banco($irrf));

$clt_xml->addChild('t_imprenda', $faixa_irrf);
$clt_xml->addChild('d_imprenda', $fixo_irrf);
$clt_xml->addChild('fgts', formato_banco($fgts));
$clt_xml->addChild('base_imposto', '');
$clt_xml->addChild('base_irrf', formato_banco($base_irrf));
$clt_xml->addChild('salfamilia', formato_banco($familia));
$clt_xml->addChild('salliquido', formato_banco($liquido));

$clt_xml->addChild('a4001',   $array_update_movimentos_individual[4001]);
$clt_xml->addChild('a4002',   $array_update_movimentos_individual[4002]);
$clt_xml->addChild('a4003',   $array_update_movimentos_individual[4003]);
$clt_xml->addChild('a4004',   $array_update_movimentos_individual[4004]);
$clt_xml->addChild('a4005',   $array_update_movimentos_individual[4005]);
$clt_xml->addChild('a4006',   $array_update_movimentos_individual[4006]);
$clt_xml->addChild('a4007',   $array_update_movimentos_individual[4007]);
$clt_xml->addChild('a5001',   $array_update_movimentos_individual[5001]);
$clt_xml->addChild('a5002',   $array_update_movimentos_individual[5002]);
$clt_xml->addChild('a5003',   $array_update_movimentos_individual[5003]);
$clt_xml->addChild('a5004',   $array_update_movimentos_individual[5004]);
$clt_xml->addChild('a5010',   $array_update_movimentos_individual[5010]);
$clt_xml->addChild('a5011',   $array_update_movimentos_individual[5011]);

$clt_xml->addChild('a5012',   $array_update_movimentos_individual[5012]);
$clt_xml->addChild('a5013',   $array_update_movimentos_individual[5013]);
$clt_xml->addChild('a5014',   $array_update_movimentos_individual[5014]);
$clt_xml->addChild('a5015',   $array_update_movimentos_individual[5015]);
$clt_xml->addChild('a5016',   $array_update_movimentos_individual[5016]);
$clt_xml->addChild('a5017',   $array_update_movimentos_individual[5017]);
$clt_xml->addChild('a5018',   $array_update_movimentos_individual[5018]);

$clt_xml->addChild('a5019',formato_banco($sindicato));
$clt_xml->addChild('a5020', formato_banco($inss));
$clt_xml->addChild('a5021',formato_banco($irrf));
$clt_xml->addChild('a5022', formato_banco($familia));

$clt_xml->addChild('a5023', $array_update_movimentos_individual[5023]);
$clt_xml->addChild('a5024', $array_update_movimentos_individual[5024]);
$clt_xml->addChild('a5025', $array_update_movimentos_individual[5025]);
$clt_xml->addChild('a5026', $array_update_movimentos_individual[5026]);
$clt_xml->addChild('a5027', $array_update_movimentos_individual[5027]);
$clt_xml->addChild('a5028', $array_update_movimentos_individual[5028]);
$clt_xml->addChild('a5029', $array_update_movimentos_individual[5029]);
$clt_xml->addChild('a5030', formato_banco($irrf_dt));
$clt_xml->addChild('a5031', formato_banco($inss_dt));
$clt_xml->addChild('a5032',  $array_update_movimentos_individual[5032]);
$clt_xml->addChild('a5033',  $array_update_movimentos_individual[5033]);
$clt_xml->addChild('a5034',  $array_update_movimentos_individual[5034]);
$clt_xml->addChild('a5035', formato_banco($inss_ferias));
$clt_xml->addChild('a5036', formato_banco($irrf_ferias));
$clt_xml->addChild('a5037', formato_banco($valor_ferias));

$clt_xml->addChild('a5038',  $array_update_movimentos_individual[5038]);
$clt_xml->addChild('a5039',  $array_update_movimentos_individual[5039]);
$clt_xml->addChild('a5040', 	$array_update_movimentos_individual[5040]);
$clt_xml->addChild('a5041',  $array_update_movimentos_individual[5041]);
$clt_xml->addChild('a5042',  $array_update_movimentos_individual[5042]);
$clt_xml->addChild('a5043',  $array_update_movimentos_individual[5043]);
$clt_xml->addChild('a5044', formato_banco($fgts_ferias));
$clt_xml->addChild('a5045', $array_update_movimentos_individual[5045]);


$clt_xml->addChild('a5046', $array_update_movimentos_individual[5046]);
$clt_xml->addChild('a5047', $array_update_movimentos_individual[5047]);
$clt_xml->addChild('a5048', $array_update_movimentos_individual[5048]);
$clt_xml->addChild('a5049', formato_banco($ddir));
$clt_xml->addChild('a6000', $array_update_movimentos_individual[6000]);
$clt_xml->addChild('a6001', $array_update_movimentos_individual[6001]);
$clt_xml->addChild('a6003', $array_update_movimentos_individual[6003]);
$clt_xml->addChild('a6004', $array_update_movimentos_individual[6004]);
$clt_xml->addChild('a6005', formato_banco($salario_maternidade));
$clt_xml->addChild('a6006', $array_update_movimentos_individual[6006]);
$clt_xml->addChild('a7000',  $array_update_movimentos_individual[7000]);
$clt_xml->addChild('a7001', formato_banco($vale_transporte));
$clt_xml->addChild('a7003',  $array_update_movimentos_individual[7003]);
$clt_xml->addChild('a7004',  $array_update_movimentos_individual[7004]);
$clt_xml->addChild('a7009',  $array_update_movimentos_individual[7009]);

$clt_xml->addChild('a8000',  $array_update_movimentos_individual[8000]);
$clt_xml->addChild('a8002',  $array_update_movimentos_individual[8002]);
$clt_xml->addChild('a8003', formato_banco($vale_refeicao));
$clt_xml->addChild('a8004',  $array_update_movimentos_individual[8004]);
$clt_xml->addChild('a8005',  $array_update_movimentos_individual[8005]);
$clt_xml->addChild('a8006',  $array_update_movimentos_individual[8006]);
$clt_xml->addChild('a8080',  $array_update_movimentos_individual[8080]);
$clt_xml->addChild('a9000', $array_update_movimentos_individual[9000]);

$clt_xml->addChild('a9500', $array_update_movimentos_individual[9500]);
$clt_xml->addChild('a9999', $array_update_movimentos_individual[9999]);
$clt_xml->addChild('a50220', $array_update_movimentos_individual[50220]);
$clt_xml->addChild('a50222', $filhos_familia);
$clt_xml->addChild('a50272', $array_update_movimentos_individual[50272]);
$clt_xml->addChild('a50292', $array_update_movimentos_individual[50292]);
$clt_xml->addChild('a50372', $array_update_movimentos_individual[50372]);
$clt_xml->addChild('a50492', $filhos_irrf);


$clt_xml->addChild('a80002', $dias_faltas);
$clt_xml->addChild('a50111', $array_update_movimentos_individual[50111]);

$clt_xml->addChild('ids_movimentos', @implode(',',$ids_movimentos_update_individual));
$clt_xml->addChild('status',3);
$clt_xml->addChild('valor_ferias', formato_banco($valor_ferias));
$clt_xml->addChild('valor_pago_ferias', formato_banco($desconto_ferias));
$clt_xml->addChild('inss_ferias', formato_banco($inss_ferias));

$clt_xml->addChild('ir_ferias', formato_banco($irrf_ferias));
$clt_xml->addChild('fgts_ferias', formato_banco($fgts_ferias));
$clt_xml->addChild('valor_dt', formato_banco($decimo_terceiro_credito));
$clt_xml->addChild('inss_dt', formato_banco($inss_dt));
$clt_xml->addChild('ir_dt', formato_banco($irrf_dt));
$clt_xml->addChild('fgts_dt', '');

$clt_xml->addChild('valor_rescisao', formato_banco($valor_rescisao));
$clt_xml->addChild('valor_pago_rescisao', formato_banco($desconto_rescisao));
$clt_xml->addChild('inss_rescisao', formato_banco($inss_rescisao));
$clt_xml->addChild('ir_rescisao', formato_banco($irrf_rescisao));
$clt_xml->addChild('fgts_rescisao', '');
$clt_xml->addChild('desconto_inss', '');
$clt_xml->addChild('tipo_desconto_inss','');
$clt_xml->addChild('arquivo', '');
$clt_xml->addChild('tipo_pg','');

$clt_xml->addChild('folha_proc_salario_outra_empresa', '');
$clt_xml->addChild('folha_proc_desconto_outra_empresa', '');
$clt_xml->addChild('folha_proc_diferenca_inss','');
$clt_xml->addChild('hora_trabalhada', $horas);
$clt_xml->addChild('hora_extra', '');


$clt_xml->addChild('hora_desconto', '');
$clt_xml->addChild('financeiro', '');
$clt_xml->addChild('hora_noturna', $horas_noturnas);
$clt_xml->addChild('adicional_noturno', formato_banco($valor_adicional_noturno));
$clt_xml->addChild('horas_atraso', $horas_atraso);
$clt_xml->addChild('dsr', formato_banco($DSR));
$clt_xml->addChild('desconto_auxilio_distancia', formato_banco($desconto_aux_distancia));



?>