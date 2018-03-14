<?php

class FormataDadosClass {
    
    static function campoTexto($str, $length, $completa = " ", $direcao = STR_PAD_RIGHT) {

        $arr = array(
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
            'o.' => '/&ordm;/',
            ' ' => "/(  +)/i",
            '' => "/[^a-zA-Z0-9  +]+/i"
        );
        return str_pad(substr(preg_replace($arr, array_keys($arr), htmlentities($str, ENT_NOQUOTES, "iso-8859-1")), 0, $length), $length, $completa, $direcao);
    }

    static function campoNumerico($str, $length, $completa = 0, $direcao = STR_PAD_LEFT) {
        
        $arr = array(
            '' => "/(  +)/i",
            '' => "/[^0-9  +]+/i"
        );

        $str = preg_replace($arr, array_keys($arr), htmlentities($str, ENT_NOQUOTES, "iso-8859-1"));
        return substr(str_pad($str, $length, $completa, $direcao), 0, $length);
    }
}
