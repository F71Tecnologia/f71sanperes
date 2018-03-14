<?php
if(empty($_COOKIE['logado'])){
	print "<center>Efetue o Login<br><a href='login.php'>Logar</a></center>";
	exit;
}

include "conn.php";
include "classes/funcionario.php";

$user = new funcionario();
$user -> MostraUser(0);

$nome = $user -> nome1;
$regiao = $user -> id_regiao;


//EFETUANDO A PESQUISA PARA LOGAR

$senha = trim($_REQUEST['senha']);
$url = "principalrh.php?regiao=$regiao&id=1";

include "classes/logar.php";

$logando = new logar();
$logando -> LoginRH($senha,3,$url);



?>