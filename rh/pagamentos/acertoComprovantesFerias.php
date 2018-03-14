<?php
error_reporting(E_ERROR);

session_cache_expire(1440); //24 horas
session_start();

function copy_comprovante($source,$target){
    
    if(file_exists($source)){
        
        if(!file_exists($target)){
            
            echo ", Arquivo enexistente no destino ";
            
            if(!copy($source, $target)) {
                
                echo ", Falha ao copiar o arquivo de {$source}";
                
                return 0;
                
            }
            else {
                
                echo ", Arquivo source copiado para {$target}";
                
                return 1;
                
            }
            
        }
        else {
            
            echo ", Source e Target existentes";
            
            return 1;
            
        }
        

    }
    else {

        echo ", não existe o PDF na origem: {$source}";
        
        return 0;

    }
    
}

include('../../classes/MySqlClass.php');

$db = new MySqlClass();

$db->setQuery(SELECT,'f.id_clt,
                      CONCAT("ferias_",f.id_clt,"_",f.id_ferias,".pdf") comprovante_ferias, 
                      CONCAT(sf.id_saida_file,".",s.id_saida,".pdf") comprovante_pagto');

$db->setQuery(FROM,'saida s INNER JOIN rh_ferias f ON s.id_clt = f.id_clt
                            INNER JOIN saida_files sf ON s.id_saida = sf.id_saida');
        
$db->setQuery(WHERE,'s.id_clt IN (SELECT id_clt FROM rh_ferias_itens)');

$db->setQuery(ORDER,'s.data_proc DESC');

if(!$db->setRs()) echo 'Houve um erro na query de consulta';

while ($db->setRow()) {

    $source[0] = '/home/ispv/public_html/intranet/rh/arquivos/ferias/'.$db->getRow('comprovante_ferias');

    $source[1] = '/home/ispv/public_html/intranet/rh_novaintra/ferias/arquivos/'.$db->getRow('comprovante_ferias');

    $target = '/home/ispv/public_html/intranet/comprovantes/'.$db->getRow('comprovante_pagto');
    
    $id_clt = $db->getRow('id_clt');
    
    echo "Processando Clt {$id_clt}";

    if(!copy_comprovante($source[0],$target)) {
        
        echo "<br>Tentando copiar arquivo do diretório novo de férias";
        
        copy_comprovante($source[1],$target);
    
    }

    echo '<br>';

}

