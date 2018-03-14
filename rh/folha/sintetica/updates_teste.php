<?php
// Criando Update dos Movimentos
if(!empty($ids_movimentos_update_geral)) {
	$total_movimentos            = count($ids_movimentos_update_geral);
	$ids_movimentos_update_geral = implode(',', $ids_movimentos_update_geral);
	$update_movimentos = "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$folha' WHERE id_movimento IN($ids_movimentos_update_geral) LIMIT $total_movimentos;\r\n";
}

// Criando Update da Folha
$update_folha = "UPDATE rh_folha SET clts = '".$total_participantes."', rendi_indivi = '".formato_banco($rendimentos_total)."', rendi_final = '".formato_banco($movimentos_credito)."', descon_indivi = '".formato_banco($descontos_total)."', descon_final = '".formato_banco($movimentos_debito)."', total_limpo = '".formato_banco($salario_total)."', total_salarios = '".formato_banco($base_total)."', total_liqui = '".formato_banco($liquido_total)."', total_familia = '".formato_banco($familia_total)."', total_sindical = '".formato_banco($sindicato_total)."', total_vt = '".formato_banco($vale_transporte_total)."', total_vr = '".formato_banco($vale_refeicao_total)."', base_inss = '".formato_banco($base_inss_total)."', total_inss = '".formato_banco($inss_total)."', base_irrf = '".formato_banco($base_irrf_total)."', total_irrf = '".formato_banco($irrf_total)."', base_fgts = '".formato_banco($base_fgts_total)."', total_fgts = '".formato_banco($fgts_total)."', base_fgts_ferias = '".formato_banco($base_fgts_ferias_total)."', base_fgts_sefip = '".formato_banco($base_fgts_total)."', total_fgts_sefip = '".formato_banco($fgts_total)."', multa_fgts = '0.00', valor_dt = '".formato_banco($decimo_terceiro_total)."', inss_dt = '".formato_banco($inss_dt_total)."', ir_dt = '".formato_banco($irrf_dt_total)."', valor_ferias = '".formato_banco($ferias_total)."', valor_pago_ferias = '".formato_banco($ferias_desconto_total)."', inss_ferias = '".formato_banco($inss_ferias_total)."', ir_ferias = '".formato_banco($irrf_ferias_total)."', fgts_ferias = '".formato_banco($fgts_ferias_total)."', valor_rescisao = '".formato_banco($rescisao_total)."', valor_pago_rescisao = '".formato_banco($rescisao_desconto_total)."', inss_rescisao = '".formato_banco($inss_rescisao_total)."', ir_rescisao = '".formato_banco($irrf_rescisao_total)."', ids_movimentos_update = '".$ids_movimentos_update_geral."', ids_movimentos_estatisticas = '".$ids_movimentos_estatisticas."', status = '3' WHERE id_folha = '".$folha."' LIMIT 1;";



// Juntando os Updates
$conteudo = $update_participantes.$update_movimentos.$update_folha;

//echo $conteudo;
/*
// Criando Nome do Arquivo
$nome_arquivo    = 'idfolha_'.$folha.'.txt';
$caminho_arquivo = '../../arquivos/folhaclt/'.$nome_arquivo;

// Abre o Arquivo TXT
if(!$arquivo = fopen($caminho_arquivo, 'wa+')) {
    echo "Erro abrindo arquivo ($caminho_arquivo)";
	exit;
}

// Escreve no Arquivo TXT
if(!fwrite($arquivo, $conteudo)) {
	echo "Erro escrevendo no arquivo ($caminho_arquivo)";
	exit;
}

// Fecha o Arquivo TXT
fclose($arquivo);

?>

<!--<a href="../../arquivos/folhaclt/<?=$nome_arquivo?>" target="_blank">Abrir Arquivo TXT</a>-->
*/