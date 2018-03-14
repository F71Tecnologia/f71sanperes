<?php
// Criando Update da Folha
$update_folha = "UPDATE folhas SET participantes = '".$total_participantes."', rendimentos = '".formato_banco($rendimentos_total)."', descontos = '".formato_banco($descontos_total)."', total_bruto = '".formato_banco($salario_base_total)."', total_liqui = '".formato_banco($liquido_total)."', status = '3' WHERE id_folha = '".$folha."' LIMIT 1; \r\n";

// Juntando os Updates
$conteudo = $update_participantes.$update_folha;

//echo $conteudo;

// Criando Nome do Arquivo
$nome_arquivo    = 'cooperado_'.$folha.'.txt';
$caminho_arquivo = '../arquivos/folhacooperado/'.$nome_arquivo;

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