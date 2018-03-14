<?php 
// Totalizadores
$salario_total     += $salario;
$rendimentos_total += $rendimentos;
$descontos_total   += $descontos;
$liquido_total     += $liquido;

// Resetando Valores
unset($dias,
	  $meses,
	  $faltas,
	  $salario,
	  $rendimentos,
	  $descontos,
	  $liquido);
?>