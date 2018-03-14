<?php 
function formato_real($valor) {
	
	$valor_formatado = number_format($valor, 2, ',', '.');
	return $valor_formatado;

}
	  
function formato_banco($valor) {
	
	$valor_formatado = number_format($valor, 2, '.', '');
	return $valor_formatado;

}
?>