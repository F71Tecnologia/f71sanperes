<?php
include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";
include "../classes/funcionario.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;

//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);

//EFETUANDO A PESQUISA PARA LOGAR

$url = "../novoFinanceiro/relatorios.php?enc=$linkEnc";

include "../classes/logar.php";

$logando = new logar();
$logando -> LoginRelatorio($senha,2,$url);



?>