<?php function abreviacao($nome, $numero_palavras) {
	
	if(!empty($numero_palavras)) {
		
	   $palavras = explode(' ', $nome);
		
	   $conexoes_nome = array('das','Das','DAS','da','Da','DA','de','De','DE','dos','Dos','DOS','do','Do','DO','e','E');
					
	   if(in_array($palavras[$numero_palavras-1], $conexoes_nome)) {
		   $numero_palavras++;
	   }
		
	   for($a=0; $a<$numero_palavras; $a++) {
		   $nome_abreviado[] = $palavras[$a];
	   }
		
	   $nome_abreviado = implode(' ', $nome_abreviado);

	} else {
		
		$nome_abreviado = $nome;
		
	}
	
	return $nome_abreviado;

} ?>