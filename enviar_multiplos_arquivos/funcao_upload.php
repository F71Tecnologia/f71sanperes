<?php
/**
    * FunÃ§Ã£o para fazer upload de arquivos
    * @author Rafael Wendel Pinheiro
    * @param File $arquivo Arquivo a ser salvo no servidor
    * @param String $pasta Local onde o arquivo serÃ¡ salvo
    * @param Array  $blacklist Extensões não permitidas para o arquivo
    * @param String $nome Nome do arquivo. Null para manter o nome original
    * @return array
*/
function uploadFile($arquivo, $pasta, $blacklist, $nome = null){
    if (isset($arquivo)) {
        $nomeOriginal = is_null($nome)? $arquivo['name']: $nome;
        $tipoArquivo = preg_match('/\.([a-z0-9]){3,4}$/', $nomeOriginal, $_)? $_[0]: null;

       

        $tipoPermitido = true;
        foreach($blacklist as $tipo){
            if(strtolower($tipoArquivo) == strtolower($tipo)){
                $tipoPermitido = false;
            }
        }
        if (!$tipoPermitido) {
            $retorno["erro"] = "Tipo não permitido";

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
