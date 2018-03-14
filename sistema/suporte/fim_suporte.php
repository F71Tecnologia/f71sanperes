<?php
include('../../conn.php');
include('../../classes/SuporteClass.php');
include('../../wfunction.php');


if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "fechar_chamado"){
        $objSuporte = new SuporteClass();
        $id_suporte = $_REQUEST['id'];
        if($objSuporte->delSuporte($id_suporte)){
            $retorno = array("status" => 1);
        }
        
        echo json_encode($retorno);
        exit();
        
    }
    
}



?>