<?php
include('../../conn.php');
include('../../classes/SaidaClass.php');
include('../../wfunction.php');

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){    
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == "rel_despesas"){
        $id = $_REQUEST['id'];
        $where = $_REQUEST['where'];
        $ex = explode(".", $id);
        
        if(count($ex) == 3){
            $cod = "cod = '{$id}'";
        }elseif(count($ex) == 2){
            $cod = "B.id_subgrupo = '{$id}";
        }else{
            $cod = "A.id_grupo = '".str_replace("0","",$id)."0'";
        }
        
        $qr = "SELECT D.*,
            CAST( REPLACE(D.valor, ',', '.') as decimal(13,2)) as cvalor,
            DATE_FORMAT(data_vencimento, \"%d/%m/%Y\") AS dataBr,D.tipo
            FROM entradaesaida_grupo AS A
            LEFT JOIN entradaesaida_subgrupo AS B ON (A.id_grupo=B.entradaesaida_grupo)
            LEFT JOIN entradaesaida AS C ON (LEFT(C.cod,5)=B.id_subgrupo)
            INNER JOIN (SELECT * FROM saida WHERE {$_REQUEST['where']}) AS D ON (D.tipo=C.id_entradasaida)
            WHERE C.id_entradasaida >= 154 AND {$cod}
            ORDER BY C.cod";
        
        $result = mysql_query($qr);
        $row = mysql_fetch_assoc($result);
        
        if($result){
            $retorno = array("status" => 1, "id_despesa" => $id);
        }
        
        echo json_encode($retorno);
        exit();
    }
}
?>