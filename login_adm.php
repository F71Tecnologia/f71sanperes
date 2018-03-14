<?php
if(empty($_COOKIE['logado'])){
	print "<center>Efetue o Login<br><a href='login.php'>Logar</a></center>";
	exit;
}

include "conn.php";
include "classes/funcionario.php";
include "funcoes.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;


//EFETUANDO A PESQUISA PARA LOGAR
//$url = ($_COOKIE['logado'] == 75) ? "novoFinanceiro/index.php?regiao=$regiao" : "financeiro/novofinanceiro.php?regiao=$regiao";
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);

$url = "novoFinanceiro/index.php?enc=$linkEnc";

include "classes/logar.php";

$logando = new logar();
$logando -> LoginFinanceiro($senha,1,$url);


?>