<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/SefipClass.php');

include "../classes/LogClass.php";
$log = new Log();

if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    
    $retorno = array("status" => 0);
    
    if($_REQUEST['method'] == "exclui_sefip"){
        $sefip = new SefipClass();
        $id_folha = $_REQUEST['id'];
        
        if($sefip->delSefip($id_folha)){
            $retorno = array("status" => 1);
        }
        
        echo json_encode($retorno);   
        $log->gravaLog('Relat�rios e Impostos', "Exclus�o de relat�rio: ID{$id_folha}");
        exit();        
    }
    
}



?>