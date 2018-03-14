<?php
include ("include/restricoes.php");
include "../conn.php";
include "../classes/funcionario.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;


//EFETUANDO A PESQUISA PARA LOGAR

$senha = trim($_REQUEST['senha']);
$url = "index.php?regiao=$regiao&id=1";

include "../classes/logar.php";

$logando = new logar();
$logando -> LoginJuridico($senha,6,$url);
?>