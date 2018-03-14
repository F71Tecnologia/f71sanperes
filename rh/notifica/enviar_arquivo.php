<?php
session_start();
require_once('funcao_upload.php');

$attachment = $_FILES['attachment'];
$id_clt = $_REQUEST['id_clt'];
$id_regiao = $_REQUEST['id_regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$nome_doc = $_REQUEST['nome_doc'];

// tipos arquivos não permitidos
//$blacklist = array('bat', 'com', 'exe');
$extensao = '.pdf';

$attachment = reArrayFiles($attachment);

for($i=0; $i < count($attachment); $i++)
{
    $normalizaNome = explode('.', $attachment[$i]['name']);
    $normalizaNome[0] = $nome_doc;
    $attachment[$i]['name'] = $normalizaNome[0].'.'.$normalizaNome[1];
    $enviar = uploadFile($attachment[$i], 'arquivos_advertencia/', $extensao);
    $data[$i]['sucesso'] = false;
//    print_r($enviar);
//    exit;
    if($enviar['erro']){
        $data[$i]['filepath'] = $enviar['erro'];
        $msg = $enviar['erro'];
    }
    else{
        $data[$i]['sucesso'] = true;
        /* Caminho do arquivo */
        $data[$i]['filepath'] = $enviar['caminho'];
        $msg = 'Upload feito com sucesso';
    }
}

header("Location: advertencia.php?clt=$id_clt&pro=$id_projeto&id_reg=$id_regiao");
$_SESSION['MESSAGE'] = $msg;

//echo json_encode($data);

function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            if(!empty($file_post[$key][$i])){
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
    }

    return $file_ary;
}

?>