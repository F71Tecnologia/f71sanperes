<?php

include('../../conn.php');
include('../../wfunction.php');
include('../../funcoes.php');
include('../../classes/abreviacao.php');
include('../../classes/ICalculosDatasClass.php');
include('../../classes/CalculosDatasClass.php');
include('../../classes/EventoClass.php');
include('../../classes/EventoViewClass.php');
include "../../classes_permissoes/acoes.class.php";

// verifica se existe CLT com retorno de evento
$query = "SELECT id_clt FROM rh_eventos WHERE data_retorno = DATE_ADD(CURDATE(), INTERVAL -1 DAY) AND status = 1 AND id_regiao = 36";
echo "$query<br>";
$result = mysql_query($query);
while ($row_clt = mysql_fetch_array($result)) {
    // verifica se os mesmos clts possuem outro evento
    $query_teste = "SELECT id_clt,cod_status FROM rh_eventos WHERE (data > DATE_ADD(CURDATE(), INTERVAL -1 DAY) OR data_retorno > DATE_ADD(CURDATE(), INTERVAL -1 DAY)) AND id_clt = '{$row_clt['id_clt']}' AND status = 1 AND id_regiao = 36 ORDER BY data_retorno";
//    echo "$query_teste<br>";
    $result_teste = mysql_query($query_teste);
    // se não tiver evento agendado, coloca status 10
    if (mysql_num_rows($result_teste) == 0) {
        $query_update = "UPDATE rh_clt SET status=10 WHERE id_clt = '{$row_clt['id_clt']}'";
//        echo "$query_update<br>";
    } else {
        // se tiver evento agendado, coloca status do evento
        $row_teste = mysql_fetch_assoc($result_teste);
        $query_update = "UPDATE rh_clt SET status={$row_teste['cod_status']} WHERE id_clt = '{$row_teste['id_clt']}'";
//        echo "$query_update<br>";
    }
    $result_update = mysql_query($query_update);
//    var_dump($result_update);
}



