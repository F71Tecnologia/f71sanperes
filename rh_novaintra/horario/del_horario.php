<?php

    include "../../classes/LogClass.php";
    include('../../conn.php');
    include('../../classes/FuncoesClass.php');
    include('../../wfunction.php');

    $usuario = carregaUsuario();
    $id_horario = $_REQUEST['id_horario'];

    // DESATIVA HORÁRIO
    $rhHorariosQuery = sqlUpdate("rh_horarios", array("status_reg" => 0), "id_horario = $id_horario", false);
    
    // INÍCIO LOG
    $log = new Log();
    $log->log(2, "Horário ID $id_horario deletado", $cursoTable);
    // FIM LOG

?>
