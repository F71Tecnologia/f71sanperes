<?php
$loc = $_REQUEST['locacao'];
$locacao = explode("//", $_REQUEST['locacao']);
$locacao_nome = $locacao[0];
$locacao_id = $locacao[1];

echo "LOCA��O ARRAY: {$locacao}<br />";
echo "LOCA��O S/ ARRAY: {$loc}<br />";
echo "LOCA��O NOME: {$locacao_nome}<br />";
echo "LOCA��O ID: {$locacao_id}<br />";
?>
