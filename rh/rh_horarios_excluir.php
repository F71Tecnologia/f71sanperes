<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include "../conn.php";

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$horario  = $_REQUEST['horario'];

$qry_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario='$horario'");
$dados_horario =  mysql_fetch_assoc($qry_horario);



$qry_curso = mysql_query("SELECT * from curso WHERE id_curso = '$dados_horario[funcao]'");
$dados_curso = mysql_fetch_assoc($qry_curso);



$qry_clt = mysql_query("SELECT * FROM rh_clt WHERE id_curso = '$dados_curso[id_curso]'");
while($dados_clt = mysql_fetch_assoc($qry_clt))
{
$qry_up = mysql_query("UPDATE rh_clt set rh_horario='0' WHERE id_clt = '$dados_clt[id_clt]'");	
	
	

echo $dados_clt['nome']."<br>";	
	
	
}

mysql_query("DELETE FROM hr_horario WHERE id_horario='$horario'");


$link = "rh_horarios.php?regiao=$regiao"; 
echo "<script>location.href='".$link."';</script>";




mysql_close($conn);

}

?>
