<?php
//echo '<pre>';
//print_r($prestador);
//echo '</pre>';

if ($anexo == 1) {
    $titulo = 'ANEXO I';
    $texto = 'Conforme determinado no item 2 - Do Prazo de Vigência, e no subitem 2.1, onde determina que o presente contrato entrará em pleno vigor na data de sua assinatura e respeitando o contrato n.º 031/2013 assinado pelo '.$prestador['contratante'].' através da PREFEITURA MUNICIPAL DE '.$prestador['municipio'].', deverá o contrato de prestação de serviço ter vigência de 12 (doze) meses, podendo ser prorrogado por igual período, de acordo com o subitem 2.2 do presente contrato.';
} elseif ($anexo == 2) {
    $titulo = 'ANEXO II - Dos Serviços';
    $texto = 'O Contratado deverá prestar serviços na área da Saúde atendendo as demandas do(a) '.$prestador['municipio'].'. Os serviços serão prestados em regime de emergência.';
} elseif ($anexo == 3) {
    $titulo = 'ANEXO III - Da Remuneração';
    $texto = 'Fica convencionado que o Contratante pagará a Contratada a quantia de R$ (zero) mensais pelos serviços prestados, respeitando o disposto no anexo II do presente Contrato de Prestação de Serviços devendo a Contratada apresentar, mensalmente, nota fiscal referente ao serviço prestado, assim como relatórios de serviços prestados.';
} elseif ($anexo == 4) {
    $titulo = 'ANEXO IV - Das Condições Comerciais';
    $texto = 'Além das regras contidas no Regulamento de Contratação de Obras e Serviços do '.$prestador['contratante'].' deverão ser obedecidos os Princípios da Boa Fé e da Obrigatoriedade dos efeitos contratuais. A Contratada não poderá delegar ou transferir a terceiros a prestação de serviços ora pactuados.';
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