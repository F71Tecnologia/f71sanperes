<?php
require_once('funcao_upload.php');

$attachment = $_FILES['attachment'];

// tipos arquivos não permitidos
$blacklist = array('bat', 'com', 'exe');

$attachment = reArrayFiles($attachment);

for($i=0; $i < count($attachment); $i++)
{
    $enviar = uploadFile($attachment[$i], 'uploads/', $blacklist);
    $data[$i]['sucesso'] = false;

    if($enviar['erro']){
        $data[$i]['filepath'] = $enviar['erro'];
    }
    else{
        $data[$i]['sucesso'] = true;

        /* Caminho do arquivo */
        $data[$i]['filepath'] = $enviar['caminho'];
    }
}

echo json_encode($data);

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}