<?php
// Contratado depois do Início da Folha
if($row_clt['data_entrada'] > $data_inicio and $row_clt['data_entrada'] < $data_fim) {
	
	$inicio = $row_clt['data_entrada'];
	$fim    = $data_fim;
	
	// Calculando Dias da Entrada
	$dias_entrada = abs(30 - (int)floor((strtotime($fim) - strtotime($inicio)) / 86400) - 1);
	
}
?>