<?php
function uploadFile($arquivo, $pasta, $extensao, $nome = null){
    
    if (isset($arquivo)) {
        
        $nomeOriginal = is_null($nome)? utf8_decode($arquivo['name']): $nome;
        $tipoArquivo = preg_match('/\.([a-z0-9]){3,4}$/', $nomeOriginal, $_)? $_[0]: null;                        
        $tipoPermitido = false;
//        foreach($blacklist as $tipo){
//            if(strtolower($tipoArquivo) == strtolower($tipo)){
//                $tipoPermitido = false;
//            }
//        }
        if(strtolower($tipoArquivo) == strtolower($extensao)){
                $tipoPermitido = true;
        }
           
        if (!$tipoPermitido) {
            $retorno["erro"] = "Tipo não permitido ou Arquivo não setado";
        }else{
            if(move_uploaded_file($arquivo['tmp_name'], $pasta.$nomeOriginal)){
                $retorno["caminho"] = $pasta.$nomeOriginal;
            }
            else{
                $retorno["erro"] = "Erro ao fazer upload";
            }
        }
    }else{
        $retorno["erro"] = "Arquivo nao setado";
    }
    return $retorno;
}
