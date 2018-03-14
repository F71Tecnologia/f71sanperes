<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');

$clt_id = $_REQUEST["clt"];
$reg = $_REQUEST["reg"];
$pro = $_REQUEST["pro"];


$quest01 = $_REQUEST["quest01"];
$quest02 = $_REQUEST["quest02"];
$quest03 = $_REQUEST["quest03"];
$quest04 = $_REQUEST["quest04"];
$quest05 = $_REQUEST["quest05"];
$quest06 = $_REQUEST["quest06"];
$quest07 = $_REQUEST["quest07"];
$quest08 = $_REQUEST["quest08"];
$quest09 = $_REQUEST["quest09"];
$quest10 = $_REQUEST["quest10"];
$quest11 = $_REQUEST["quest11"];
$quest12 = $_REQUEST["quest12"];



$qry_avaliacao = mysql_query("INSERT INTO rh_avaliacao_clt VALUES ('','$clt_id','$quest01','$quest02','$quest03','$quest04','$quest05','$quest06','$quest07','$quest08','$quest09','$quest10','$quest11','$quest12',NOW())") or die (mysql_error());





$link = "ver_clt.php?reg=$reg&clt=$clt_id&ant=0&pro=$pro&pagina=bol"; 
echo "<script>location.href='".$link."';</script>";  

?>