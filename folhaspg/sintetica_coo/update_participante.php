<?php // Criando Update do Participante

if($row_cooperado['tipo_inss'] == 1){ // Valor Definido
    $t_inss = '-';
}else{ // valor percentual
    $t_inss = $row_cooperado['inss'];
}

$update_participantes .= "UPDATE folha_cooperado SET salario = '".formato_banco($salario_base)."', salario_liq = '".formato_banco($liquido)."', meses_trab = '".$meses_trabalhados."', h_trab = '".$horas_trabalhadas."', h_mes = '".$row_curso['hora_mes']."', valor_hora = '".formato_banco($valor_hora)."', inss = '".formato_banco($inss)."', t_inss = '".$t_inss."' , irrf = '".formato_banco($gravar_irrf)."', t_irrf = '".$taxa_irrf."', d_irrf = '".formato_banco($valor_deducao_ir)."', quota = '".formato_banco($valor_quota)."', p_quota = '".formato_banco($parcela_quota)."', base_imposto = '".formato_banco($base_inss)."', base_irrf = '".formato_banco($base_irrf)."', taxa_ope = '".formato_banco($taxa_operacional)."', t_ope = '".formato_banco($taxa_cooperativa)."', nota = '".formato_banco($nota_fiscal)."', faltas = '".$horas_trabalhadas."', status = '3' WHERE id_folha_pro = '".$row_participante[0]."' LIMIT 1;\r\n";
?>