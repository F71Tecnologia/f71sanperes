<?php

//TRABALHANDO COM CRIPTOGRAFIA

define('SALT', "whateveryouwant\n");
//define('SALT', "whateveryouwant\n"); Com esse define é possível fazer o encrypt local

function encrypt($text) {
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

function decrypt($text) {
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

function registrar_log($local, $menssagem, $regiao = 0, $projeto = 0) {

    $ip = $_SERVER['REMOTE_ADDR'];
    mysql_query("INSERT INTO log (id_user, id_regiao, local, ip, acao, horario, status)
							VALUES
							('$_COOKIE[logado]', '$regiao',  '$local', '$ip', '$menssagem', NOW(),  '1')") or die(mysql_error());
}

function formato_matricula($matricula) {

    return sprintf('%05s', $matricula);
}

function formato_num_processo($processo) {

    return sprintf('%05s', $processo);
}

function formato_cep($cep) {
    $cep = str_replace('-', '', str_replace('.', '', $cep));
    $cep1 = substr($cep, 0, 6);
    $cep2 = substr($cep, 6, 3);

    return $cep1 . '-' . $cep2;
}

function formatCPFCNPJ($string) {
    if ($string != "") {
        $string = str_replace(array(".", "-", "/"," ",","), "", $string);
        $output = preg_replace("[' '-./ t]", '', $string);
        $size = (strlen($output) - 2);

        if ($size < 9) {
            $output = str_pad($output, 11, "0", STR_PAD_LEFT);
        } elseif ($size > 9 && $size < 14) {
            $output = str_pad($output, 14, "0", STR_PAD_LEFT);
        }

        $mask = (strlen($output) == 11) ? '###.###.###-##' : '##.###.###/####-##';
        $index = -1;
        for ($i = 0; $i < strlen($mask); $i++):
            if ($mask[$i] == '#')
                $mask[$i] = $output[++$index];
        endfor;
        return $mask;
    }
}

//
?>