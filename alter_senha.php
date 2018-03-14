<?php

include "conn.php";

$login = $_REQUEST['login'];
$senha = $_REQUEST['nova_senha'];

$result = mysql_query("SELECT * FROM funcionario where login = '$login'") or die ("Não foi possível efetuar pesquisa.");
$row = mysql_fetch_array($result);

$senha_antiga = $row['senha'];

if($senha == $senha_antiga){
	print "
	<script>
	alert(\"Favor não colocar a mesma senha!\");
	location.href=\"login.php\"
	</script>";

}else{

$sql_2 = "UPDATE funcionario SET senha = '$senha', alt_senha = '0' where id_funcionario = '$row[0]'";
mysql_query($sql_2, $conn);

$iduser = $row[0];
setcookie ("logado", $iduser);
header("Location: index.php");

}


?>