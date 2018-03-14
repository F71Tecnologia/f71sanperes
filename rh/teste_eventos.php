<?php

include_once("../conn.php");
include_once("../classes/EventoClass.php");
$evento = new Eventos();
$evento->validaEventoForFolha(4802, "2014-07", "2014-07-01", "2014-07-31");

