<?php

include_once("../../conn.php");
include_once("../../classes/RescisaoClass.php");

$rescisao = new Rescisao();
$dados = $rescisao->montarArrayRescisao("03", "2014", "3309", false, true);
