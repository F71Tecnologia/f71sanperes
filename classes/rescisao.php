<?php
function getCLTAguardando($regiao, $projeto, $mes, $ano){
    
    if(!empty($ano)){$ano = "AND YEAR(A.data_demi) = {$ano}";}
    $sql = "SELECT A.id_clt, A.nome, B.nome AS nome_curso, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS data_admissao, DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demissao
            FROM rh_clt AS A            
            LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
            WHERE A.status = '200' AND A.id_regiao = '{$regiao}' AND A.id_projeto = {$projeto} AND MONTH(A.data_demi) = {$mes} {$ano} AND B.status = 1
            ORDER BY A.nome ASC";
    $rst = mysql_query($sql) or die(mysql_error());
    return $rst;
}

function getCLTDemitidos($regiao, $projeto, $mes, $ano){
    if(!empty($ano)){$ano = "AND YEAR(A.data_demi) = {$ano}";}
    $sql = "SELECT A.id_clt, B.id_recisao, A.nome, C.nome AS nome_curso, DATE_FORMAT(B.data_adm, '%d/%m/%Y') AS data_admissao, DATE_FORMAT(B.data_demi, '%d/%m/%Y') AS data_demissao, B.total_liquido, B.data_proc,
            (SELECT total_liquido FROM rh_recisao WHERE vinculo_id_rescisao = B.id_recisao AND status = 1) AS valor_complementar,
            (SELECT id_recisao FROM rh_recisao WHERE vinculo_id_rescisao = B.id_recisao AND status = 1) AS id_complementar
            FROM rh_clt AS A
            LEFT JOIN rh_recisao AS B ON (A.id_clt = B.id_clt)
            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
            WHERE A.status IN ('60','61','62','63','64','65','66','80','101') AND A.id_regiao = '{$regiao}' AND A.id_projeto = '{$projeto}' AND MONTH(A.data_demi) = '{$mes}' {$ano} /*AND B.rescisao_complementar IS NULL*/ AND B.status = 1            
            ORDER BY A.nome ASC;";
    $rst = mysql_query($sql) or die(mysql_error());
    return $rst;
}

function getTotalRescisao($regiao, $projeto, $mes, $ano){
    if(!empty($ano)){$ano = "AND YEAR(A.data_demi) = {$ano}";}
    $sql = "SELECT SUM(B.total_liquido) AS total_geral 
            FROM rh_clt AS A
            LEFT JOIN rh_recisao AS B ON (A.id_clt = B.id_clt)            
            WHERE A.status IN ('60','61','62','63','64','65','66','80','101') AND A.id_regiao = {$regiao} AND A.id_projeto = {$projeto} AND MONTH(A.data_demi) = {$mes} {$ano} AND B.status = 1
            ORDER BY A.nome ASC";
    $rst = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($rst);
    return $row;
}
?>