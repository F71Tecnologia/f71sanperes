<?php

include('../../conn.php');
include('../../classes/FeriadosClass.php');
include('../../wfunction.php');

$id_feriado = $_REQUEST['id'];
$usuario = carregaUsuario();
$objFeriado = new FeriadosClass();
$return = $objFeriado->alteraStatusFeriado($id_feriado,$usuario);

echo json_encode(array('return'=>$return));
?>