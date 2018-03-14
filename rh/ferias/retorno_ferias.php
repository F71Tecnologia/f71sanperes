<?php

include "../../conn.php";
date_default_timezone_set("America/Sao_Paulo"); // define c time zone como o do Brasil
$hoje = date("Y-m-d");

$query_ferias = "SELECT A.id_ferias,A.id_clt,A.nome,A.data_retorno FROM rh_ferias AS A
WHERE DATE_FORMAT(A.data_retorno,'%Y-%m-%d') = '{$hoje}' AND A.status=1";

$rslt_ferias = mysql_query($query_ferias);

if (mysql_num_rows($rslt_ferias) > 0) {
    $count_sucess = 0;
    $count_error = 0;
    while ($row = mysql_fetch_assoc($rslt_ferias)) {
        $query_clt = "UPDATE rh_clt SET status = 10 WHERE id_clt = {$row['id_clt']} AND status = 40";
        $rslt_clt = mysql_query($query_clt);
        if($rslt_clt){
            $count_sucess++;
            $clts_sucess[]=$row['id_clt'];
        }else{
            $count_error++;
            $clts_error[]=$row['id_clt'];
        }
    }
    $hora = date("Y-m-d H:i:s");
    $local = "Retorno Férias";
    $ip = $_SERVER['REMOTE_ADDR'];
    $ids_error = implode(",", $clts_error);
    $ids_sucess = implode(",", $clts_sucess);
    $acao = "$count_sucess clts voltaram para atividade normal (IDs: $ids_sucess). Houve falha em $count_error clts. (IDs: $ids_error)";
    $query_log = "INSERT INTO log (local,horario,ip,acao) VALUES ('$local','$hora','$ip','$acao')";
    mysql_query($query_log);
    
    
}