<?php

if (isset($_REQUEST) && !empty($_REQUEST)) {

    //ATUALIZANDO HORARIO MENSAL
    if ($_REQUEST['method'] == "atualiza_horario_mensal") {

        $return = array("status" => false);
        $query = "UPDATE rpa_autonomo SET hora_mes = '{$_REQUEST['horas_mes']}' WHERE id_rpa = {$_REQUEST['id_rpa']}";
        echo $query;
        exit();
        
    }
}