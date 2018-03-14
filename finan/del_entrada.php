<?php
include('../conn.php');
include('../classes/EntradaClass.php');
include('../wfunction.php');

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "del_entrada"){
        $entrada = new Entrada();
        $id = $_REQUEST['id'];
        
        if($entrada->delEntrada($id)){
            $retorno = array("status" => 1);
        }
        
        echo json_encode($retorno);
        exit();        
    }
    
}
?>