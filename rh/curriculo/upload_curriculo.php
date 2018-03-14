<?php
include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
if($_COOKIE[logado] == 257){
    
}
$return = array();

$dirname = dirname(__FILE__);

$tipo_contrato = validatePost('tipo_contrato');
$id_fun = validatePost('id_fun');
$tabela = ($tipo_contrato == 2) ? 'rh_clt' : 'autonomo';
$key = ($tipo_contrato == 2) ? 'id_clt' : 'id_autonomo';
$condicao = $key." = ".$id_fun;


$tipoContra = normalizaNometoFile(GlobalClass::getContratacao($tipo_contrato));
$fileName = "curriculo_".$tipoContra."_".$id_fun;
$path = 'arquivos/';

if(isset($_REQUEST['method']) && $_REQUEST['method']=="deletaCurriculo"){
    
    if(unlink($dirname."/".$_REQUEST['doc'])){
        sqlUpdate($tabela, array("curriculo" => 0), $condicao);
        $return['status'] = 1;
        $return['msg'] = "";
    }else{
        $return['status'] = 0;
        $return['msg'] = "Erro ao excluir ($dirname/$file)";
    }
    
    echo json_encode($return);
    exit();
}


if(!empty($_FILES)){
    $tipos = array('jpg', 'png', 'docx', 'pdf', 'doc');
    $rs = montaQueryFirst($tabela, "{$key},nome", $condicao);
    if(!empty($rs)){
        
        $enviar = GlobalClass::uploadFile($_FILES['file_curriculo'], $path, $tipos, $fileName);
        
        if($enviar['erro']){    
            $return['status'] = 0;
            $return['msg'] = $enviar['erro'];
        } else {
            sqlUpdate($tabela, array("curriculo" => 1), $condicao);
            $return['status'] = 1;
            $return['msg'] = "";
        }
    }else{
        $return['status'] = 0;
        $return['msg'] = "Funcionário não encontrado";
    }
}

echo json_encode($return);
exit();
