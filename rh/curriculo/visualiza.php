<?php

include('../../wfunction.php');
include('../../classes/global.php');

$path = 'arquivos/';
$tipo_contrato = validatePost('tipo');
$id_fun = validatePost('id');
$return = array();

$tipoContra = normalizaNometoFile(GlobalClass::getContratacao($tipo_contrato));
$fileName = "curriculo_".$tipoContra."_".$id_fun;

$file = glob($path.$fileName.".*");
$return['teste'] = $file;
$tipo = explode(".", $file[0]);
$tipo = end($tipo);
if(count($file)>0){
    $return['status'] = 1;
    $return['doc'] = $file['0'];
    $tp = ($tipo=="docx") ? "doc":$tipo;
    $return['type'] = $tp;
}else{
    $return['status'] = 0;
    $return['msg'] = "Currículo não encontrado";
}

echo json_encode($return);
