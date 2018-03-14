<?php

require("../../conn.php");
require("../../wfunction.php");

$charset = mysql_set_charset('utf8');

$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);

//RAMON INCLUÍ 20-12-2013
if(isset($_REQUEST['grupo']) && !empty($_REQUEST['grupo'])){
    $json = array();
    $rs = montaQuery("entradaesaida_subgrupo", "*", "entradaesaida_grupo = {$_REQUEST['grupo']}");
    
    foreach($rs as $value){
        $json[$value['id_subgrupo']] = $value['id_subgrupo']. " - ".$value['nome'];
    }
    echo json_encode($json);
    exit;
}

if ($_REQUEST['grupo_id'] <= 4) {
    $query = mysql_query("
    SELECT id_entradasaida, cod, nome 
    FROM  entradaesaida
    WHERE grupo = '$_REQUEST[grupo_id]'
    ");
}

if (!empty($_REQUEST['subgrupo'])) {
    $qr_subgrupo = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE id = '$_REQUEST[subgrupo]'");
    $row_subgrupo = mysqL_fetch_assoc($qr_subgrupo);


    $query = mysql_query("
    SELECT id_entradasaida, cod, nome 
    FROM  entradaesaida
    WHERE cod LIKE '$row_subgrupo[id_subgrupo]%'
    ");
}


$json = array();
while ($row = mysql_fetch_assoc($query)) {

    /*if (($row_func['id_master'] >= 6 and ($row['id_entradasaida'] == 265) or $row['id_entradasaida'] == 156 or $row['id_entradasaida'] == 170 or $row['id_entradasaida'] == 171 or $row['id_entradasaida'] == 169 or $row['id_entradasaida'] == 167
            )) {
        continue;
    }*/
    $json[] = $row;
}
echo json_encode($json);
?>