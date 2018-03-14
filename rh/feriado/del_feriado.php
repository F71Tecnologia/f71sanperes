<?php
include('../../conn.php');
include('../../classes/FeriadoClass.php');
include('../../wfunction.php');

$id_feriado = $_GET['id'];

alteraStatusFeriado($id_feriado);
?>