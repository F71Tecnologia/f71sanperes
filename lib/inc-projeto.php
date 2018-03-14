<?php
/**
 * Include para carga de arquivo de configuração de acordo com o projeto. 
 * Caso não seja encontrado o arquivo de configuração do projeto então utiliza-se
 * o arquivo de configuração default (setup.ini)
 */

$setup = array(
                    0 => 'setup.ini',
                    1 => 'lagos.ini',
                    2 => 'idr.ini',
                    3 => 'iabassp.ini'
                    );

if(strpos($_SERVER['HTTP_HOST'], 'lagos') && file_exists(ROOT_DIR.$setup[1])){

    $i=1;

}
elseif(strpos($_SERVER['HTTP_HOST'], 'idr') && file_exists(ROOT_DIR.$setup[2])) {

    $i=2;
    
}
elseif(strpos($_SERVER['HTTP_HOST'], 'iabassp') && file_exists(ROOT_DIR.$setup[3])){

    $i=3;

}
else {
    
    $i = 0;
    
    if(!file_exists(ROOT_DIR.$setup[$i]))  $this->error->set(_("# Não foi possível definir o arquivo de configuração [{$setup[$i]}] para [{$_SERVER['HTTP_HOST']}]"),E_FRAMEWORK_ERROR);

}

$file_setup = $setup[$i];


