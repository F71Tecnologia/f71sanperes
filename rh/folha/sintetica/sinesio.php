<?php


$caminho = '../../../arquivos/folhaclt/arquivo.txt';
$text = 'Ol�. Eu sou Pikachu';

if($file = fopen($caminho, 'a+')){
    fwrite($file, $text);
    echo "Arquivo alterado com sucesso";
}else{
    echo "Erro ao criar arquivo";
}

fclose($file);

?>