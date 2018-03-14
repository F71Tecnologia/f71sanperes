<?php
include('../../conn.php');
include('../../classes/ViagemClass.php');
include('../../wfunction.php');

$reembolso = new ViagemClass();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    $retorno = array("status" => 0);
    if($_REQUEST['method'] == "trazer_funcionario"){
        $id_fun = explode('///', $_REQUEST['id']);
        
        $id_fun = $id_fun[0];
        $row = $reembolso->getCltId($id_fun);
        
        if($row){
            $retorno = array("status" => 1, "nome" => utf8_encode($row['nome']), "cpf" => $row['cpf'], "banco" => utf8_encode($row['nome_banco']), "agencia" => $row['agencia'], "conta" => $row['conta']);
        }
        echo json_encode($retorno);
        exit();
    }
}
?>