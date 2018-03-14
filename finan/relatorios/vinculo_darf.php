<?php
include('../../conn.php');
include('../../classes/SaidaClass.php');
include('../../wfunction.php');

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){    
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == "vincular"){
        $id_pai = $_REQUEST['id_pai'];
        $tipo_darf = $_REQUEST['tipo_darf'];
        $id_saida = $_REQUEST['id_saida'];   
        
        $result = sqlUpdate("saida",array("id_saida_pai" => $id_pai, "darf" => 1, "tipo_darf" => $tipo_darf), "id_saida = {$id_saida}");            
        
        if($result){
            $retorno = array("status" => 1);
        }
        
        echo json_encode($retorno);
        exit();
    }
}
?>