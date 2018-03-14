<?php
class txtCaged extends txt{
	function formataNome($str, $enc = "iso-8859-1"){
            return $this->nome($str);
//            return $this->nome(preg_replace("/(  +)/i", " ",$str));
	}
        function nome($str, $enc = "iso-8859-1"){
            
		$limpo = str_replace('.','',$str);
		$limpo = str_replace('-','',$limpo);
		$limpo = str_replace(')','',$limpo);
		$limpo = str_replace('(','',$limpo);
		$limpo = str_replace(',','',$limpo);
		$limpo = preg_replace("/(  +)/i", " ",$limpo);
		$limpo = str_replace('/','',$limpo);
		$limpo = str_replace('\\','',$limpo);
                
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
   		return preg_replace($acentos, array_keys($acentos), htmlentities($limpo, ENT_NOQUOTES, $enc));
	}
}