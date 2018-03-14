<?php
error_reporting(E_ALL - E_NOTICE);

/*
 * Define constantes para uso do gettext() a fim de utiliza o padrão i18N
 */
if(isset($_REQUEST['locale'])){

    $locale = $_REQUEST['locale'];

}
else {
    
    $locale = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : LOCALES_GETTEXT_DEFAULT;
    
    $locale = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
 
    $locale = (is_array($locale) ? $locale[0] : $locale).'.utf8';
    
}

$locale = strpos(LOCALES_GETTEXT,$locale) ? $locale : LOCALES_GETTEXT_DEFAULT;
$locale = str_replace('-', '_', $locale);

if (defined('LC_MESSAGES')) {
    
    setlocale(LC_MESSAGES, $locale); // Linux
    
} else {
    
    putenv("LC_ALL={$locale}"); // windows
    
}

bindtextdomain("framework", ROOT_LOCALE); 

textdomain("framework");     

