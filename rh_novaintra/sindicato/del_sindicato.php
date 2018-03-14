<?php
include('../../conn.php');
include('../../classes/SindicatoClass.php');
include('../../wfunction.php');

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "excluir_sindicato"){
        $id_sindicato = $_REQUEST['id'];
        if(delSindicato($id_sindicato)){
            $retorno = array("status" => 1);
        }
        
        json_encode($retorno);
        exit();        
    }    
}
?>