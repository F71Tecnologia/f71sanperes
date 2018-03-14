<?php
/**
    * Funรงรฃo para fazer upload de arquivos
    * @author Rafael Wendel Pinheiro
    * @param File $arquivo Arquivo a ser salvo no servidor
    * @param String $pasta Local onde o arquivo serรก salvo
    * @param Array  $blacklist Extens๕es nใo permitidas para o arquivo
    * @param String $nome Nome do arquivo. Null para manter o nome original
    * @return array
*/
function uniqueAlfa($length = 20) {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($salt);
        $pass = '';
        mt_srand(10000000 * (double) microtime());
        for ($i = 0; $i < $length; $i++) {
            $pass .= $salt[mt_rand(0, $len - 1)];
        }
        return $pass;
}

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
            $retorno["erro"] = "Tipo nใo permitido";

        }else{
	    if(file_exists($pasta.$nomeOriginal))
	    {
		$nomes = explode('.', $nomeOriginal);
		$tot = count($nomes);
		$nomeOriginal = "";
		$cont = 0;
		foreach($nomes as $nome)
		{
		    $cont++;
		    if($cont == 2)
		    {
			$nomeOriginal .= uniqueAlfa(4). '.' .$nome . '.';
		    }else
		    {
			$nomeOriginal .= $nome . '.';
		    }
		}
		$nomeOriginal = substr($nomeOriginal, 0, strlen($nomeOriginal) -1);
		//$nomeOriginal = uniqueAlfa(4).$nomeOriginal;
	    }
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
