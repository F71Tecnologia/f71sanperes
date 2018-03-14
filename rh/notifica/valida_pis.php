<?php

    //declara uma função, informando um paramêtro
    function isPIS($pis){
    //remove todos os caracteres deixando apenas valores numéricos
    $pis = preg_replace('/[^0-9]+/', '', $pis);

    //se a quantidade de caracteres numéricos  for diferente de 11 é inválido
    if(strlen($pis) <> 11) return false;

    //inicia uma variável que será responsável por armazenar o cálculo da somatória dos números individuais
    $digito = 0;
    /**
    * Criamos o for
    * $i = 0 será o índice para retorna os números individuais
    * $x =3 será o valor multiplicador dos números
    * $i<10 a condição do loop
    * $i++ incrementa , e $x-- decrementa
    */
    for($i = 0, $x=3; $i<10; $i++, $x--){
    //Verifica se $x for menor que 2, seu valor será 9, senão será $x
    $x = ($x < 2) ? 9 : $x;
    //Realiza a soma dos números individuais vezes o fator multiplicador
    $digito += $pis[$i]*$x;
    }

    /**
    * Verificamos se o módulo do resultado por 11 é menor que 2, se for o valor será 0
    * Caso não for, pegar o valor 11 e diminuir com o módulo do resultado da somatória.
    */

    $calculo = (($digito%11) < 2) ? 0 : 11-($digito%11);
    //Se o valor da variavel cálculo for diferente do último digito, ele será inválido, senão verdadeiro
    return ($calculo <> $pis[10]) ? false :true;
    }

?>

<?php
//inclui o arquivo com a validação
include('validacao.php');

//O valor do PIS
$pis = '1325344856701';

//faz a verificação com função que criamos
if(isPIS($pis)){
echo 'PIS válido';
}else{
echo 'Informe um PIS válido';
}
?>
