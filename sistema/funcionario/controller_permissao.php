<?php

include_once("../../conn.php");
include_once("../../classes/PermissoesClass.php");
$permissao = new Permissoes();

if(isset($_REQUEST['method']) && !empty($_REQUEST['method'])){
    
    /**
     * CADASTRA TODOS OS MASTERS
     */
    if($_REQUEST['method'] == "cadastra_master"){
        $retorno = array("status" => false);
        if($permissao->cadastraPermissaoMaster($_REQUEST['id_user'], $_REQUEST['master'])){
            $retorno = array("status" => true);
        }
        
        echo json_encode($retorno);
        exit();
    }
    
    if($_REQUEST['method'] == "carregaRegiao"){
        $html = "";
        print_r($_REQUEST['master']);
        exit();
        $regioes = GlobalClass::carregaRegioes($_REQUEST['master'], array(), false);
        print_r($regioes);
        
        echo utf8_encode($html); 
    }
    
}


