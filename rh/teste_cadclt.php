<?php
$loc = $_REQUEST['locacao'];
$locacao = explode("//", $_REQUEST['locacao']);
$locacao_nome = $locacao[0];
$locacao_id = $locacao[1];

echo "LOCA플O ARRAY: {$locacao}<br />";
echo "LOCA플O S/ ARRAY: {$loc}<br />";
echo "LOCA플O NOME: {$locacao_nome}<br />";
echo "LOCA플O ID: {$locacao_id}<br />";
?>
