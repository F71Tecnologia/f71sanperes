<?php

//header('Content-Type: text/html; charset=iso-8859-1');
// MÉTODO PARA RETORNAR JSON COM OS ITENS DA TABELA nfeprodutos
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'carregaItem') {
    $query = "SELECT xProd, id_prod FROM nfe_produtos WHERE status = 1";
//    echo $query;
    $result = mysql_query($query);
    echo mysql_num_rows($result);
    while ($row = mysql_fetch_assoc($result)) {
        $array['itens'][] = $row['itens'];

        echo 'oi';
    }
    print_r($array);
    echo json_encode($array);
    exit();
}  