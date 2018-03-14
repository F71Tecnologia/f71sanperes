<?php

include('../../conn.php');
include('../../classes/EduEventosClass.php');
include('../../wfunction.php');

$id_evento = $_REQUEST['id_evento'];

$deleta_evento = new EduEventosClass();

$deleta_evento->removeEvento($id_evento);

//echo json_encode(array('return'=>$return));
?>