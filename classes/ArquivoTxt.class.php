<?php

class ArquivoTxt {

    public $arquivo;

    function dados($dados) {
        $this->arquivo .= $dados;
    }

    function filler($N) {
        for ($i = 0; $i < $N; $i++) {
            $this->arquivo .= " ";
        }
    }

    function fechalinha($str = '') {
        $this->arquivo .= $str . "\r\n";
    }

    function limpar($str) {
        $limpo = str_replace('.', '', $str);
        $limpo = str_replace('-', '', $limpo);
        $limpo = str_replace(')', '', $limpo);
        $limpo = str_replace('(', '', $limpo);
        $limpo = str_replace(',', '', $limpo);
        $limpo = str_replace('/', '', $limpo);
        $limpo = preg_replace("/( +)/i", " ", $limpo);
        $limpo = str_replace('\\', '', $limpo);
        return trim($limpo);
    }

    function nome($str, $enc = "iso-8859-1") {
        $acentos = array(
            'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'C' => '/&Ccedil;/',
            'c' => '/&ccedil;/',
            'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
            'N' => '/&Ntilde;/',
            'n' => '/&ntilde;/',
            'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
            'Y' => '/&Yacute;/',
            'y' => '/&yacute;|&yuml;/',
            'a.' => '/&ordf;/',
            'o.' => '/&ordm;/'
        );
        return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
    }

    function completa($str, $n, $caractere = " ", $lado = 'depois') {
        if (strlen($str) > $n) {
            return substr($str, 0, $n);
        }
        $quant = strlen($str);
        $total = $n - $quant;
        $str_final = $str;
        for ($i = 0; $i < $total; $i++) {

            $complementos .= $caractere;
        }
        if ($lado == 'depois') {
            $str_final .= $complementos;
        } else {
            $str_final = $complementos . $str_final;
        }
        return $str_final;
    }

}
