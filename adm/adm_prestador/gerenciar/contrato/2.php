<?php
//echo '<pre>';
//print_r($prestador);
//echo '</pre>';

if ($anexo == 1) {
    $titulo = 'ANEXO I';
    $texto = 'Conforme determinado no item 2 - Do Prazo de Vig�ncia, e no subitem 2.1, onde determina que o presente contrato entrar� em pleno vigor na data de sua assinatura e respeitando o contrato n.� 031/2013 assinado pelo '.$prestador['contratante'].' atrav�s da PREFEITURA MUNICIPAL DE '.$prestador['municipio'].', dever� o contrato de presta��o de servi�o ter vig�ncia de 12 (doze) meses, podendo ser prorrogado por igual per�odo, de acordo com o subitem 2.2 do presente contrato.';
} elseif ($anexo == 2) {
    $titulo = 'ANEXO II - Dos Servi�os';
    $texto = 'O Contratado dever� prestar servi�os na �rea da Sa�de atendendo as demandas do(a) '.$prestador['municipio'].'. Os servi�os ser�o prestados em regime de emerg�ncia.';
} elseif ($anexo == 3) {
    $titulo = 'ANEXO III - Da Remunera��o';
    $texto = 'Fica convencionado que o Contratante pagar� a Contratada a quantia de R$ (zero) mensais pelos servi�os prestados, respeitando o disposto no anexo II do presente Contrato de Presta��o de Servi�os devendo a Contratada apresentar, mensalmente, nota fiscal referente ao servi�o prestado, assim como relat�rios de servi�os prestados.';
} elseif ($anexo == 4) {
    $titulo = 'ANEXO IV - Das Condi��es Comerciais';
    $texto = 'Al�m das regras contidas no Regulamento de Contrata��o de Obras e Servi�os do '.$prestador['contratante'].' dever�o ser obedecidos os Princ�pios da Boa F� e da Obrigatoriedade dos efeitos contratuais. A Contratada n�o poder� delegar ou transferir a terceiros a presta��o de servi�os ora pactuados.';
}
?>

<h2 class="titulo"><?= $titulo; ?></h2>          
<p><?= $texto; ?></p>
<?php
//echo '<pre>';
//print_r($prestador);
//echo '</pre>';
?>
<p><?= $prestador['estado'] . ', ' . date('d') . ' de ' . $meses[date('m')] . ' de ' . date('Y'); ?></p>
<div class="f-left w-metade">
    <p>____________________________________</p>
    <p>Contratante</p>
</div>
<div class="f-left">
    <p>____________________________________</p>
    <p>Contratante</p>  
</div>
<div class="clear"></div>