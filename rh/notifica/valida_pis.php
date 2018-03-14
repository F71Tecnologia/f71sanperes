<?php

    //declara uma fun��o, informando um param�tro
    function isPIS($pis){
    //remove todos os caracteres deixando apenas valores num�ricos
    $pis = preg_replace('/[^0-9]+/', '', $pis);

    //se a quantidade de caracteres num�ricos  for diferente de 11 � inv�lido
    if(strlen($pis) <> 11) return false;

    //inicia uma vari�vel que ser� respons�vel por armazenar o c�lculo da somat�ria dos n�meros individuais
    $digito = 0;
    /**
    * Criamos o for
    * $i = 0 ser� o �ndice para retorna os n�meros individuais
    * $x =3 ser� o valor multiplicador dos n�meros
    * $i<10 a condi��o do loop
    * $i++ incrementa , e $x-- decrementa
    */
    for($i = 0, $x=3; $i<10; $i++, $x--){
    //Verifica se $x for menor que 2, seu valor ser� 9, sen�o ser� $x
    $x = ($x < 2) ? 9 : $x;
    //Realiza a soma dos n�meros individuais vezes o fator multiplicador
    $digito += $pis[$i]*$x;
    }

    /**
    * Verificamos se o m�dulo do resultado por 11 � menor que 2, se for o valor ser� 0
    * Caso n�o for, pegar o valor 11 e diminuir com o m�dulo do resultado da somat�ria.
    */

    $calculo = (($digito%11) < 2) ? 0 : 11-($digito%11);
    //Se o valor da variavel c�lculo for diferente do �ltimo digito, ele ser� inv�lido, sen�o verdadeiro
    return ($calculo <> $pis[10]) ? false :true;
    }

?>

<?php
//inclui o arquivo com a valida��o
include('validacao.php');

//O valor do PIS
$pis = '1325344856701';

//faz a verifica��o com fun��o que criamos
if(isPIS($pis)){
echo 'PIS v�lido';
}else{
echo 'Informe um PIS v�lido';
}
?>
