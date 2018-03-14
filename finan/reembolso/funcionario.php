<?php
include('../../conn.php');
include('../../classes/ReembolsoClass.php');
include('../../wfunction.php');

$reembolso = new Reembolso();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "trazer_funcionario"){
        $id_fun = $_REQUEST['id'];
        $row = $reembolso->getCltId($id_fun);
        
        if($reembolso->getCltId($id_fun)){
            $retorno = array("status" => 1, "nome" => $row['nome'], "cpf" => $row['cpf'], "banco" => $row['banco'], "agencia" => $row['agencia'], "conta" => $row['conta']);
        }
        
        echo json_encode($retorno);
        exit();
    }
}
?>