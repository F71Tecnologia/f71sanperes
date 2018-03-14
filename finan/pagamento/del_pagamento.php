<?php
include('../../conn.php');
include('../../classes/PagamentoClass.php');
include('../../wfunction.php');

$pagamento = new Pagamento();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "excluir_pagamento"){
        $id_pagamento = $_REQUEST['id'];
        
        if($pagamento->delPagamento($id_pagamento)){
            $retorno = array("status" => 1);
        }
        
        echo json_encode($retorno);
        exit();        
    }    
}
?>