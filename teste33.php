<?php
$cores = array('Mamão' => 'Laranja', 'Manga' => 'Amarelo');

foreach($cores as $chave => $valor) {
	$nova_array[] = $chave.' = '.$valor;
}

echo implode(' , ', $nova_array);
?>