<?php

$id_projeto = $_REQUEST["id_projeto"];

//echo $nome;

include_once '../../conn.php';



$sql = "SELECT IF(MAX(numero) > 0,MAX(numero)+1,1) AS numero
        FROM patrimonio
        WHERE id_projeto = '".$id_projeto."'";

$resultado_numero = mysql_query($sql);

    if(mysql_num_rows($resultado_numero) > 0){
        while($option = mysql_fetch_array($resultado_numero)){
            echo "{$option['numero']}";
        }
    }

?>