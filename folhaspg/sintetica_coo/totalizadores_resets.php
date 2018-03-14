<?php 
// Totalizadores
$valor_hora_total	+=  number_format($valor_hora,'2','.','');
$salario_base_total +=  number_format($salario_base,'2','.','');
$rendimentos_total  +=  number_format($rendimentos,'2','.','');
$descontos_total	+=  number_format($descontos,'2','.','');
$inss_total	        +=  number_format($inss,'2','.',''); 
$irrf_total			+=  number_format($irrf,'2','.','');
$valor_quota_total  +=  number_format($valor_quota,'2','.','');
$ajuda_custo_total  +=  number_format($ajuda_custo,'2','.','');
$liquido_total		+=  number_format($liquido,'2','.','');
$nota_fiscal_total  +=  number_format($nota_fiscal,'2','.','');


// Resetando Valores
unset($ano_entrada,
      $mes_entrada,
	  $dia_entrada,
	  $valor_hora,
	  $horas_trabalhadas,
	  $meses_trabalhados,
	  $faltas,
	  $valor_quota_paga,
	  $valor_quota_limite,
	  $numero_parcelas,
	  $valor_parcela,
      $valor_quota,
	  $parcela_quota,
	  $salario_base,
	  $rendimentos,
	  $descontos,
	  $liquido,
	  $ajuda_custo,
	  $base_inss,
	  $inss,
	  $taxa_inss,
	  $base_irrf,
	  $irrf,
	  $taxa_irrf,
	  $valor_vestigio,
	  $taxa_operacional,
	  $nota_fiscal);
?>