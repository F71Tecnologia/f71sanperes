<?php

//PARAMETRO
$enc = $_REQUEST['enc'];

//INCLUDE
include_once("../../conn.php");
include_once("../../classes/CalculoFolhaClass.php");

//ODJETO
$folha = new Calculo_Folha();
//$movimentos = $folha->getMediaPorClt(1707);
$media = $folha->getMediaMovimentos(4747,11, 2014, 10);
//echo "<pre>";
//    print_r($media);
//echo "</pre>";

header("Location: sintetica.php?enc={$enc}");

?>
