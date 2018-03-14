<?php

include_once("conn.php");
$query = "SELECT id_clt,id_regiao,id_projeto,gerado_por,data FROM rh_eventos A where A.cod_status = 991";
$sql = mysql_query($query) or die("Erro ao selecionar evento");
while($linha = mysql_fetch_assoc($sql)){
    $datas = $linha["data"] . ' 00:00:00';
    $query_insert = "INSERT INTO rescisao_log (id_clt,id_regiao,id_projeto,processado_por,processado_em,acao,tipo) VALUES ('{$linha["id_clt"]}','{$linha["id_regiao"]}','{$linha["id_projeto"]}','{$linha["gerado_por"]}','{$datas}','1','3')";
    echo $query_insert . "<br/>";
    $sql_insert = mysql_query($query_insert) or die("Erro ao inserir dados");
}

exit();