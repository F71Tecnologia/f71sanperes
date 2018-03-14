<?php 
// Incluindo Arquivos
require('conn.php');
include('funcoes.php');
include('wfuntions.php');
include('classes/FolhaClass.php');

$objFolha = new Folha();

$movimentos =  $objFolha->getResumoPorMovimento(2226);
echo '<pre>';
print_r($movimentos);



?>