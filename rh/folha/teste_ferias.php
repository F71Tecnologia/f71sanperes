<?php

include_once("../../conn.php");
include_once("../../classes/FeriasClass.php");

$ferias = new Ferias();
$linha = $ferias->getPeriodoFerias("6227", "05", "2014");
while($d = mysql_fetch_assoc($linha)){
    $ferias->getDebug($d);
}